<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = auth()->user()->notes();
        
        if ($q = $request->get('q')) {
            $query->where('title', 'like', "%{$q}%");
        }
        
        $notes = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        return view('notes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        auth()->user()->notes()->create($validated);

        return redirect()->route('notes.index')->with('success', 'Заметка создана.');
    }

    public function edit(Note $note)
    {
        $this->authorize('view', $note);
        
        return view('notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        $note->update($validated);

        return redirect()->route('notes.index')->with('success', 'Заметка обновлена.');
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Заметка удалена.');
    }

    public function togglePin(Note $note)
    {
        $this->authorize('update', $note);
        
        $note->update(['is_pinned' => !$note->is_pinned]);

        return response()->json(['is_pinned' => $note->is_pinned]);
    }
}
