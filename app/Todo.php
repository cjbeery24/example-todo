<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model implements ApiModel
{
    protected $fillable = [
        'todo_list_id',
        'name',
        'description',
        'target_date',
        'completed_at',
    ];

    public static function getAllowedRelationships(): array
    {
        //TODO: filter the list of allowed relationships based on authenticated user
        return [
            'todo_list',
        ];
    }

    public function todo_list()
    {
        return $this->belongsTo(TodoList::class, 'todo_list_id', 'id');
    }
}
