# Заметки — веб-приложение для управления заметками

## Выбор стека: Laravel

### Обоснование

**Laravel** выбран как основной фреймворк потому что:

1. **ORM Eloquent** — встроенная поддержка моделей, relations и query builder с защитой от SQL-injection
2. **Миграции из коробки** — версионирование схемы БД встроено в фреймворк
3. **Authorization Policies** — встроенные Policy классы для проверки прав доступа на каждый CRUD операцию
4. **Blade шаблоны** — простой синтаксис, встроенная защита от XSS ({{ }} экранирует вывод)
5. **CSRF защита** — автоматическая генерация и проверка CSRF токенов
6. **Laravel Breeze** — готовая система аутентификации (register, login, logout)
7. **Встроенная валидация** — простой и читаемый синтаксис валидации Request объектов

**Frontend**: Blade + Tailwind CSS + Alpine.js
- Blade генерирует HTML на сервере, нет SPA сложности
- Tailwind CSS для быстрой стилизации (utility-first подход)
- Alpine.js (3.13) для минимальной интерактивности (toggle pin без перезагрузки)

**БД**: MySQL 8 (или SQLite для локального развития)

---

## Локальный запуск

### Требования
- PHP 8.2+
- Composer
- Node.js + npm
- MySQL 8 (опционально, можно использовать SQLite)

### Инструкция

1. **Клонирование и зависимости**
   ```bash
   git clone <repo>
   cd notes
   cp .env.example .env
   composer install
   npm install
   ```

2. **Ключ приложения**
   ```bash
   php artisan key:generate
   ```

3. **БД (MySQL)**
   ```bash
   # В .env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=notes
   DB_USERNAME=root
   DB_PASSWORD=
   
   # Создать БД
   mysql -u root -e "CREATE DATABASE notes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

4. **БД (SQLite для dev)**
   ```bash
   # В .env
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   
   # Создать файл
   touch database/database.sqlite
   ```

5. **Миграции и сидинг**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Сборка фронтенда**
   ```bash
   npm run build
   # или для development
   npm run dev
   ```

7. **Запуск сервера**
   ```bash
   php artisan serve
   ```
   
   Приложение доступно на `http://localhost:8000`

8. **Учётная запись для тестирования**
   - Email: `test@example.com`
   - Password: `password` (смотри DatabaseSeeder)

---

## Архитектурные решения

### Структура проекта

```
app/
  ├── Http/Controllers/NoteController.php       # CRUD логика
  ├── Models/
  │   ├── User.php                              # Модель пользователя
  │   └── Note.php                              # Модель заметки
  └── Policies/NotePolicy.php                   # Authorization policy
database/
  └── migrations/
      └── 2024_01_01_000000_create_notes_table.php  # Schema
routes/
  ├── web.php                                   # Web маршруты
  └── auth.php                                  # Auth маршруты (от Breeze)
resources/
  ├── views/
  │   ├── layouts/app.blade.php                 # Main layout
  │   └── notes/
  │       ├── index.blade.php                   # List view
  │       ├── create.blade.php                  # Create form
  │       └── edit.blade.php                    # Edit form
  ├── js/app.js                                 # Alpine.js init
  └── css/app.css                               # Tailwind CSS
```

### Маршруты (все защищены middleware `auth`)

```
GET    /notes                  → index (list notes)
GET    /notes/create          → create (show form)
POST   /notes                 → store (save new)
GET    /notes/{id}/edit       → edit (show edit form)
POST   /notes/{id}            → update (save changes)
POST   /notes/{id}/delete     → destroy (delete)
POST   /notes/{id}/toggle-pin → togglePin (AJAX)
```

### Безопасность

1. **SQL Injection** — Eloquent использует prepared statements
2. **XSS** — Blade `{{ }}` автоматически экранирует вывод
3. **CSRF** — встроенная защита через middleware (token в форме и meta тег)
4. **Authorization** — Policy `NotePolicy` проверяет `user_id` на каждый write запрос
5. **Валидация** — Form Request валидация в `NoteController@store` и `update`

### Функциональность

| Требование | Реализация |
|-----------|-----------|
| CRUD | Полный CRUD через NoteController с Policy авторизацией |
| Поиск | GET параметр `q` в index методе (like запрос) |
| Пагинация | Laravel Paginator (20 заметок на странице) |
| Pin/Unpin | AJAX fetch запрос на `/notes/{id}/toggle-pin` без перезагрузки |
| Цвета | 6 предустановок, выбор через Alpine.js |
| Валидация | title (required, max 255), color (regex #rrggbb) |
| UI | CSS Grid 3 колонки, Tailwind стилизация, responsive |
| Аутентификация | Laravel Breeze (register, login, logout) |

### Компромиссы

1. **Нет real-time sync** — для простоты используется fetch, а не WebSocket
2. **No dark mode** — темная тема отключена при установке Breeze
3. **Нет пагинации для ajax** — pin/unpin не подгружает следующую страницу автоматически
4. **SQLite для dev** — MySQL требует отдельной установки

---

## Git процесс

Проект организован в 4 feature ветки для логического разделения:

1. `feature/db-migration` — схема БД, модели, Policy
2. `feature/note-crud` — контроллер, валидация, маршруты
3. `feature/frontend-list` — страница списка, Alpine.js toggle-pin
4. `feature/frontend-form` — формы создания/редактирования

Каждая ветка → отдельный MR в `main`

---

