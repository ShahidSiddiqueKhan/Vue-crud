<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'priority',
        'reminder',
        'attachment'
    ];

    protected $casts = [
        'due_date' => 'date',
        'reminder' => 'datetime',
    ];

    public function assignedUsers()
{
    return $this->belongsToMany(User::class, 'task_user')
                ->withPivot('completed_at'); 
}

public function completedUsers()
{
    return $this->belongsToMany(User::class, 'task_user')
                ->whereNotNull('task_user.completed_at') 
                ->withPivot('completed_at');
}

}