@extends('layouts.app')

@section('title', 'Создать заметку')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Создать заметку</h1>

    <form action="{{ route('notes.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-8">
        @csrf

        <div class="mb-6">
            <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">Название</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                value="{{ old('title') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                required
            >
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="content" class="block text-sm font-semibold text-gray-900 mb-2">Содержание</label>
            <textarea 
                id="content" 
                name="content" 
                rows="6"
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('content') border-red-500 @enderror"
            >{{ old('content') }}</textarea>
            @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-900 mb-4">Цвет</label>
            <div x-data="colorPicker('#6366f1')" class="flex gap-3">
                @foreach(['#6366f1', '#f43f5e', '#10b981', '#f59e0b', '#3b82f6', '#8b5cf6'] as $color)
                    <button 
                        type="button" 
                        @click="selectColor('{{ $color }}')"
                        :class="selectedColor === '{{ $color }}' ? 'ring-2 ring-offset-2 ring-gray-400' : ''"
                        class="w-10 h-10 rounded-full cursor-pointer transition-all"
                        :style="{ backgroundColor: '{{ $color }}' }"
                    ></button>
                @endforeach
            </div>
            <input type="hidden" id="color" name="color" x-bind:value="selectedColor">
            @error('color')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4">
            <button 
                type="submit" 
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded"
            >
                Сохранить
            </button>
            <a 
                href="{{ route('notes.index') }}" 
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold py-2 px-4 rounded text-center"
            >
                Отмена
            </a>
        </div>
    </form>
</div>

<script>
function colorPicker(initialColor) {
    return {
        selectedColor: initialColor,
        selectColor(color) {
            this.selectedColor = color;
        }
    };
}
</script>
@endsection
