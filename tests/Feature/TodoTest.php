<?php

namespace Tests\Feature;

use App\Todo;
use App\TodoList;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function testCanAddATodoToATodoList()
    {
        $todoList = factory(TodoList::class)->create();
        $name = 'This is a todo';
        $response = $this->json('POST', '/api/todos', [
            'todo_list_id' => $todoList->id,
            'name' => $name,
            'description' => 'This is a todo description.',
            'target_date' => Carbon::now()->format('Y-m-d'),
        ]);
        $response->assertCreated();

        $todoId = $response->json(['id']);
        $createdTodo = Todo::find($todoId);
        $this->assertNotEmpty($createdTodo);
        $this->assertEquals($name, $createdTodo->name);
        $this->assertEquals($todoList->id, $createdTodo->todo_list_id);
    }

    public function testCanListTodosForATodoList()
    {
        $todoList = factory(TodoList::class)->create();
        factory(Todo::class, 4)->create([
            'todo_list_id' => $todoList->id
        ]);

        $anotherTodoList = factory(TodoList::class)->create();
        factory(Todo::class, 4)->create([
            'todo_list_id' => $anotherTodoList->id
        ]);

        $response = $this->json('GET', '/api/todos', [
            'todo_list_id' => $todoList->id,
        ]);
        $response->assertOk();

        $todoCount = $response->json(['total']);
        $this->assertEquals(4, $todoCount);

        $todos = $response->json(['data']);
        foreach ($todos as $todo) {
            $this->assertEquals($todoList->id, $todo['todo_list_id']);
        }
    }

    public function testCanListOnlyCompletedTodosForATodoList()
    {
        $todoList = factory(TodoList::class)->create();
        factory(Todo::class, 4)->create([
            'todo_list_id' => $todoList->id
        ]);

        factory(Todo::class, 4)->create([
            'todo_list_id' => $todoList->id,
            'completed_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        $response = $this->json('GET', '/api/todos', [
            'todo_list_id' => $todoList->id,
            'completed' => true,
        ]);
        $response->assertOk();

        $todoCount = $response->json(['total']);
        $this->assertEquals(4, $todoCount);

        $todos = $response->json(['data']);
        foreach ($todos as $todo) {
            $this->assertNotNull($todo['completed_at']);
        }
    }

    public function canShowATodo()
    {
        $todoList = factory(TodoList::class)->create();
        $todo = factory(Todo::class)->create([
            'todo_list_id' => $todoList->id
        ]);

        $response = $this->json('GET', '/api/todos/' . $todo->id);
        $response->assertOk();

        $retrievedTodoName = $response->json(['name']);
        $this->assertEquals($todo->name, $retrievedTodoName);
    }

    public function canUpdateATodo()
    {
        $todoList = factory(TodoList::class)->create();
        $originalName = 'Original Todo Name';
        $todo = factory(Todo::class)->create([
            'todo_list_id' => $todoList->id,
            'name' => $originalName,
        ]);

        $newName = 'New Todo Name';
        $response = $this->json('PUT', '/api/todos/' . $todo->id, [
            'name' => $newName,
        ]);
        $response->assertOk();

        $updatedTodo = Todo::find($todo->id);
        $this->assertEquals($newName, $updatedTodo->name);
    }

    public function canDeleteATodo()
    {
        $todoList = factory(TodoList::class)->create();
        $todo = factory(Todo::class)->create([
            'todo_list_id' => $todoList->id,
        ]);

        $response = $this->json('DELETE', '/api/todos/' . $todo->id);
        $response->assertOk();

        $deletedTodo = Todo::find($todo->id);
        $this->assertEmpty($deletedTodo);
    }
}
