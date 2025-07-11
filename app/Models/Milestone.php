<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Milestone extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'due_date',
        'completion_date',
        'status',
        'progress'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'due_date' => 'date',
        'completion_date' => 'date',
        'progress' => 'integer',
    ];

    /**
     * Get the project that owns the milestone.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the tasks for the milestone.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentageAttribute(): int
    {
        if ($this->tasks()->count() === 0) {
            return $this->progress ?? 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        $totalTasks = $this->tasks()->count();

        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    }

    /**
     * Check if the milestone is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->status !== 'completed';
    }

    /**
     * Scope a query to only include active milestones.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'completed');
    }

    /**
     * Scope a query to only include completed milestones.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
