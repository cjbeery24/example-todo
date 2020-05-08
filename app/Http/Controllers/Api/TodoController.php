<?php

namespace App\Http\Controllers\Api;

use App\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TodoController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'todo_list_id' => 'required|integer|exists:todo_lists,id',
            'includes' => [
                'array',
            ],
            'includes.*' => [
                Rule::in(Todo::getAllowedRelationships())
            ]
        ]);
        $validator->validate();

        $paginationValidator = $this->getPaginationValidator($request);
        $paginationValidator->validate();

        $includes = $request->input('includes', []);
        $todosQuery = Todo::query()->with($includes)
            ->where('todo_list_id', $request->input('todo_list_id'));

        if ($request->has('completed')) {
            $completed = filter_var($request->input('completed'), FILTER_VALIDATE_BOOLEAN);
            if ($completed) {
                $todosQuery->whereNotNull('completed_at');
            } else {
                $todosQuery->whereNull('completed_at');
            }
        }

        return $this->paginate($request, $todosQuery);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'todo_list_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'target_date' => 'required|date',
            'completed_at' => 'date|nullable',
        ]);
        $validator->validate();

        $todo = new Todo($request->all());
        $todo->save();

        return $todo;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return mixed
     */
    public function show($id)
    {
        /** @var Todo $todo */
        $todo = Todo::find($id);
        if (!$todo) {
            abort(404);
        }

        return $todo;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'description' => 'string',
            'target_date' => 'date',
            'completed_at' => 'date|nullable',
        ]);
        $validator->validate();

        /** @var Todo $todo */
        $todo = Todo::find($id);
        if (!$todo) {
            abort(404);
        }

        $todo->fill($request->all());
        $todo->save();

        return $todo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return mixed
     */
    public function destroy($id)
    {
        /** @var Todo $todo */
        $todo = Todo::find($id);
        if (!$todo) {
            abort(404);
        }

        $todo->delete();

        return $todo;
    }
}
