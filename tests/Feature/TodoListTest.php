<?php

namespace Tests\Feature;

use App\Todo;
use App\TodoList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    use RefreshDatabase;

    public function testCanCreateTodoList()
    {
        $name = 'New Todo List';
        $response = $this->json('POST', '/api/todo-lists', [
            'name' => $name
        ]);
        $response->assertCreated();

        $todoListId = $response->json(['id']);
        $createdTodoList = TodoList::find($todoListId);
        $this->assertNotEmpty($createdTodoList);
        $this->assertEquals($name, $createdTodoList->name);
    }

    public function testCanListAllTodoLists()
    {
        factory(TodoList::class, 3)->create();

        $response = $this->json('GET', '/api/todo-lists');
        $response->assertOk();

        $todoListCount = $response->json(['total']);
        $this->assertEquals(3, $todoListCount);
    }

    public function canShowTodoList()
    {
        $todoList = factory(TodoList::class)->create();

        $response = $this->json('GET', '/api/todo-lists/' . $todoList->id);
        $response->assertOk();

        $todoListName = $response->json(['name']);

        $this->assertEquals($todoList->name, $todoListName);
    }

    public function testCanIncludeTodosWhenShowingTodoList()
    {
        $todoList = factory(TodoList::class)->create();
        factory(Todo::class, 4)->create([
            'todo_list_id' => $todoList->id,
        ]);

        $response = $this->json('GET', '/api/todo-lists/' . $todoList->id, [
            'includes' => ['todos']
        ]);
        $response->assertOk();

        $todos = $response->json(['todos']);

        $this->assertCount(4, $todos);
    }

    public function testDoesNotIncludeTodosWhenShowingTodoListIfNotAskedFor()
    {
        $todoList = factory(TodoList::class)->create();
        factory(Todo::class, 4)->create([
            'todo_list_id' => $todoList->id,
        ]);

        $response = $this->json('GET', '/api/todo-lists/' . $todoList->id, [
            'includes' => ['todos']
        ]);
        $response->assertOk();

        $response->assertJsonMissing(['todos']);
    }

    public function testCanUpdateTodoList()
    {
        $originalName = 'Original Name';
        $newName = 'New Name';
        $todoList = factory(TodoList::class)->create([
            'name' => $originalName,
        ]);

        $response = $this->json('PUT', '/api/todo-lists/' . $todoList->id, [
            'name' => $newName
        ]);
        $response->assertOk();

        $todoListId = $response->json(['id']);
        $updatedTodoList = TodoList::find($todoListId);

        $this->assertEquals($newName, $updatedTodoList->name);
    }

    public function testCanDeleteTodoList()
    {
        $todoList = factory(TodoList::class)->create();

        $response = $this->json('DELETE', '/api/todo-lists/' . $todoList->id);
        $response->assertOk();

        $deletedTodoList = TodoList::find($todoList->id);
        $this->assertEmpty($deletedTodoList);
    }

    public function testDeletingATodoListRemovesTodosFromDatabase()
    {
        $todoList = factory(TodoList::class)->create();
        factory(Todo::class, 4)->create([
            'todo_list_id' => $todoList->id,
        ]);

        $response = $this->json('DELETE', '/api/todo-lists/' . $todoList->id);
        $response->assertOk();

        $todosCount = Todo::query()
            ->where('todo_list_id', $todoList->id)
            ->count();
        $this->assertEquals(0, $todosCount);
    }
}
