<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Task::with(['project', 'assignedUser']);

        // Mostrar apenas tarefas de projetos acessíveis
        $query->whereHas('project', function($q) use ($user) {
            $q->where(function($subQuery) use ($user) {
                if (!$user->isAdmin()) {
                    $subQuery->where('owner_id', $user->id)
                            ->orWhereHas('members', function($memberQuery) use ($user) {
                                $memberQuery->where('users.id', $user->id);
                            });
                }
            });
        });

        // Filtros
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('assigned_to') && $request->assigned_to !== '') {
            if ($request->assigned_to === 'me') {
                $query->where('assigned_to', $user->id);
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        if ($request->has('project_id') && $request->project_id !== '') {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'ilike', '%' . $request->search . '%')
                  ->orWhere('description', 'ilike', '%' . $request->search . '%');
            });
        }

        $tasks = $query->orderBy('due_date', 'asc')
                      ->orderBy('priority', 'desc')
                      ->paginate(20);

        // Para filtros
        $projects = Project::accessibleBy($user)->get();
        $users = User::active()->get();

        return view('tasks.index', compact('tasks', 'projects', 'users'));
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        $task->load(['project', 'assignedUser', 'creator', 'comments.user']);
        
        return view('tasks.show', compact('task'));
    }

    public function create(Request $request)
    {
        $project = null;
        if ($request->has('project_id')) {
            $project = Project::findOrFail($request->project_id);
            $this->authorize('view', $project);
        }

        $user = Auth::user();
        $projects = Project::accessibleBy($user)->get();
        $users = User::active()->get();

        return view('tasks.create', compact('projects', 'users', 'project'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['backlog', 'todo', 'in_progress', 'review', 'testing', 'done', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical', 'urgent'])],
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $this->authorize('view', $project);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'project_id' => $validated['project_id'],
            'assigned_to' => $validated['assigned_to'],
            'created_by' => Auth::id(),
            'due_date' => $validated['due_date'],
            'estimated_hours' => $validated['estimated_hours'],
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'Tarefa criada com sucesso!');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        
        $user = Auth::user();
        $projects = Project::accessibleBy($user)->get();
        $users = User::active()->get();
        
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['backlog', 'todo', 'in_progress', 'review', 'testing', 'done', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical', 'urgent'])],
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $this->authorize('view', $project);

        $task->update($validated);

        return redirect()->route('tasks.show', $task)->with('success', 'Tarefa atualizada com sucesso!');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $task->delete();
        
        return redirect()->route('tasks.index')->with('success', 'Tarefa excluída com sucesso!');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['backlog', 'todo', 'in_progress', 'review', 'testing', 'done', 'cancelled'])],
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Status atualizado com sucesso!']);
    }
}
