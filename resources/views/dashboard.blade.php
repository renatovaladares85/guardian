@extends('layouts.app')

@section('title', 'Dashboard - Guardian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
    <div class="text-muted">
        {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['total_projects'] }}</h4>
                        <p class="mb-0">Total de Projetos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-project-diagram fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['active_projects'] }}</h4>
                        <p class="mb-0">Projetos Ativos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-play-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['my_tasks'] }}</h4>
                        <p class="mb-0">Minhas Tarefas</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['overdue_tasks'] }}</h4>
                        <p class="mb-0">Tarefas Vencidas</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Projetos Recentes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Projetos Recentes</h5>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                @forelse($recentProjects as $project)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-start priority-{{ $project->priority }}">
                        <div>
                            <strong>{{ $project->name }}</strong>
                            <br><small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $project->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                            <br><small class="text-muted">{{ $project->progress }}%</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Nenhum projeto encontrado.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Minhas Tarefas -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Minhas Tarefas</h5>
                <a href="{{ route('tasks.index', ['assigned_to' => 'me']) }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
            </div>
            <div class="card-body">
                @forelse($myTasks as $task)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-start priority-{{ $task->priority }}">
                        <div>
                            <strong>{{ $task->title }}</strong>
                            <br><small class="text-muted">{{ $task->project->name }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $task->status === 'done' ? 'success' : ($task->isOverdue() ? 'danger' : 'primary') }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                            @if($task->due_date)
                                <br><small class="text-muted">{{ $task->due_date->format('d/m/Y') }}</small>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Nenhuma tarefa atribuída.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if($overdueTasks->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Tarefas Vencidas</h5>
            </div>
            <div class="card-body">
                @foreach($overdueTasks as $task)
                    <div class="alert alert-warning d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $task->title }}</strong>
                            <br><small>{{ $task->project->name }} - Venceu em {{ $task->due_date->format('d/m/Y') }}</small>
                        </div>
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">Ver Tarefa</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div class="row mt-4">
    <div class="col-md-6">
        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-lg w-100">
            <i class="fas fa-plus me-2"></i>Novo Projeto
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('tasks.create') }}" class="btn btn-success btn-lg w-100">
            <i class="fas fa-plus me-2"></i>Nova Tarefa
        </a>
    </div>
</div>
@endsection
