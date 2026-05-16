<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

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
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'deadline' => 'date',
    ];

    /**
     * Scope a query to only include tasks with a specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include tasks with a specific priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'High'   => 'danger',
            'Medium' => 'warning',
            'Low'    => 'success',
            default  => 'secondary',
        };
    }

    /**
     * Get status badge CSS class.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'To Do'       => 'todo',
            'In Progress' => 'inprogress',
            'Completed'   => 'completed',
            'Submitted'   => 'submitted',
            default       => 'todo',
        };
    }
}
