<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'project_id',
        'milestone_id',
        'assigned_to',
        'created_by',
        'due_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'tags',
        'dependencies',
        'attachments'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'tags' => 'array',
        'dependencies' => 'array',
        'attachments' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            if (!$task->uuid) {
                $task->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });

        static::updated(function ($task) {
            // Update project progress when task status changes
            if ($task->wasChanged('status') && $task->project) {
                $task->project->updateProgress();
            }
        });
    }

    /**
     * Get the project this task belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the milestone this task belongs to.
     */
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    /**
     * Get the user assigned to this task.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the comments for this task.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * Get the time logs for this task.
     */
    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !in_array($this->status, ['done', 'cancelled']);
    }

    /**
     * Check if task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }

    /**
     * Check if task is in progress.
     */
    public function isInProgress(): bool
    {
        return in_array($this->status, ['in_progress', 'review', 'testing']);
    }

    /**
     * Get total time logged for this task.
     */
    public function getTotalTimeLoggedAttribute(): float
    {
        return $this->timeLogs()->sum('hours');
    }

    /**
     * Get task progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        $statusProgress = [
            'backlog' => 0,
            'todo' => 10,
            'in_progress' => 50,
            'review' => 80,
            'testing' => 90,
            'done' => 100,
            'cancelled' => 0
        ];

        return $statusProgress[$this->status] ?? 0;
    }

    /**
     * Check if user can access task.
     */
    public function canAccess(User $user): bool
    {
        return $this->project->canAccess($user);
    }

    /**
     * Check if user can edit task.
     */
    public function canEdit(User $user): bool
    {
        return $this->created_by === $user->id ||
               $this->assigned_to === $user->id ||
               $this->project->owner_id === $user->id ||
               $user->canManageProjects();
    }

    /**
     * Scope for tasks assigned to user.
     */
    public function scopeAssignedTo($query, User $user)
    {
        return $query->where('assigned_to', $user->id);
    }

    /**
     * Scope for tasks with specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['done', 'cancelled']);
    }

    /**
     * Scope for tasks due today.
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
                    ->whereNotIn('status', ['done', 'cancelled']);
    }

    /**
     * Scope for high priority tasks.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical', 'urgent']);
    }
}
