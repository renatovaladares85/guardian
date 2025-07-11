<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Project::accessibleBy($user)->with(['owner', 'members']);

        // Filtros
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('description', 'ilike', '%' . $request->search . '%');
            });
        }

        $projects = $query->orderBy('updated_at', 'desc')->paginate(12);

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        
        $project->load(['owner', 'members', 'tasks.assignedUser', 'milestones']);
        
        $taskStats = [
            'total' => $project->tasks()->count(),
            'completed' => $project->completedTasks()->count(),
            'in_progress' => $project->tasks()->whereIn('status', ['in_progress', 'review', 'testing'])->count(),
            'overdue' => $project->tasks()->overdue()->count(),
        ];

        return view('projects.show', compact('project', 'taskStats'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);
        
        $users = User::active()->get();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['planning', 'active', 'on_hold', 'review', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical', 'urgent'])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'members' => 'array',
            'members.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'owner_id' => Auth::id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'budget' => $validated['budget'],
        ]);

        // Adicionar membros
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $userId) {
                $project->members()->attach($userId, [
                    'role' => 'team_member',
                    'joined_at' => now(),
                ]);
            }
        }

        return redirect()->route('projects.show', $project)->with('success', 'Projeto criado com sucesso!');
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        
        $users = User::active()->get();
        $project->load('members');
        
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['planning', 'active', 'on_hold', 'review', 'completed', 'cancelled'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical', 'urgent'])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'members' => 'array',
            'members.*' => 'exists:users,id',
        ]);

        $project->update($validated);

        // Atualizar membros
        $project->members()->detach();
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $userId) {
                $project->members()->attach($userId, [
                    'role' => 'team_member',
                    'joined_at' => now(),
                ]);
            }
        }

        return redirect()->route('projects.show', $project)->with('success', 'Projeto atualizado com sucesso!');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        
        $project->delete();
        
        return redirect()->route('projects.index')->with('success', 'Projeto excluído com sucesso!');
    }
}
