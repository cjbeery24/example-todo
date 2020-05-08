<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoList extends Model implements ApiModel
{
    protected $fillable = [
        'name',
    ];

    public static function getAllowedRelationships(): array
    {
        //TODO: filter the list of allowed relationships based on authenticated user
        return [
            'todos',
        ];
    }

    public function todos()
    {
        return $this->hasMany(Todo::class, 'todo_list_id');
    }
}
