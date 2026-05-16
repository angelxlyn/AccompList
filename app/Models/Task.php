<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    // ── Valid values — single source of truth used by controller validation ──
    const STATUSES = ['To Do', 'In Progress', 'Completed', 'Submitted'];
    const PRIORITIES = ['High', 'Medium', 'Low'];

    /**
     * The table associated with the model.
     */
    protected $table = 'tasks';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'task_name',
        'priority',
        'deadline',
        'status',
        'description',
        'user_uuid',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Automatically filter all queries by the user's UUID cookie
        static::addGlobalScope('user_privacy', function ($builder) {
            $uuid = request()->cookie('user_uuid');
            
            if ($uuid) {
                $builder->where('user_uuid', $uuid);
            } else {
                // If no cookie is present, show nothing (prevents seeing old NULL-tagged tasks)
                $builder->where('user_uuid', '=', 'no-uuid-set');
            }
        });

        // Automatically assign the user's UUID cookie when creating a new task
        static::creating(function ($task) {
            if (!$task->user_uuid) {
                $task->user_uuid = request()->cookie('user_uuid');
            }
        });
    }

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'deadline' => 'date',
    ];

    /**
     * Scope: filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * CSS class for status badge — matches summary card colors exactly.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'To Do' => 'todo',
            'In Progress' => 'inprogress',
            'Completed' => 'completed',
            'Submitted' => 'submitted',
            default => 'todo',
        };
    }

    /**
     * Description with URLs converted to safe clickable links.
     */
    public function getLinkedDescriptionAttribute(): string
    {
        if (empty($this->description)) {
            return '';
        }

        // Escape the raw text first
        $escaped = e($this->description);

        // Match URLs but stop at whitespace, HTML entities, or HTML brackets.
        return preg_replace(
            '~(https?://[^\s&<>]+)~',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-decoration-none">$1</a>',
            $escaped
        );
    }
}