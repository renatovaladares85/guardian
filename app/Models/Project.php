<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'owner_id',
        'start_date',
        'end_date',
        'budget',
        'spent_budget',
        'progress',
        'technologies',
        'settings',
        'is_archived'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'spent_budget' => 'decimal:2',
        'progress' => 'integer',
        'technologies' => 'array',
        'settings' => 'array',
        'is_archived' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($project) {
            if (!$project->uuid) {
                $project->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (!$project->code) {
                $project->code = strtoupper(substr($project->name, 0, 3)) . '-' . date('Y') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the project owner.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the project members.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users')
                    ->withPivot(['role', 'joined_at'])
                    ->withTimestamps();
    }

    /**
     * Get the project tasks.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the project milestones.
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    /**
     * Get the project files.
     */
    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    /**
     * Get active tasks.
     */
    public function activeTasks()
    {
        return $this->tasks()->whereNotIn('status', ['done', 'cancelled']);
    }

    /**
     * Get completed tasks.
     */
    public function completedTasks()
    {
        return $this->tasks()->where('status', 'done');
    }

    /**
     * Calculate project progress based on tasks.
     */
    public function calculateProgress(): int
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->completedTasks()->count();
        return (int) round(($completedTasks / $totalTasks) * 100);
    }

    /**
     * Update project progress.
     */
    public function updateProgress(): void
    {
        $this->update(['progress' => $this->calculateProgress()]);
    }

    /**
     * Check if project is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->end_date && $this->end_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Check if user is project member.
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Check if user can access project.
     */
    public function canAccess(User $user): bool
    {
        return $this->owner_id === $user->id || 
               $this->hasMember($user) || 
               $user->isAdmin();
    }

    /**
     * Scope for active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope for projects with specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for user accessible projects.
     */
    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('owner_id', $user->id)
              ->orWhereHas('members', function ($memberQuery) use ($user) {
                  $memberQuery->where('users.id', $user->id);
              });
        });
    }
}
