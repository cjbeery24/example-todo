<?php

namespace App\Http\Controllers\Api;

use App\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TodoListController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'includes' => [
                'array',
            ],
            'includes.*' => [
                Rule::in(TodoList::getAllowedRelationships())
            ]
        ]);
        $validator->validate();

        $paginationValidator = $this->getPaginationValidator($request);
        $paginationValidator->validate();

        $includes = $request->input('includes', []);
        $listsQuery = TodoList::query()->with($includes);

        return $this->paginate($request, $listsQuery);
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
            'name' => 'required|string',
        ]);
        $validator->validate();

        $todoList = new TodoList($request->all());
        $todoList->save();

        return $todoList;
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return mixed
     */
    public function show(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'includes' => [
                'array',
            ],
            'includes.*' => [
                Rule::in(TodoList::getAllowedRelationships())
            ]
        ]);
        $validator->validate();

        $includes = $request->input('includes', []);
        /** @var TodoList $todoList */
        $todoList = TodoList::query()->with($includes)
            ->where('id', $id)
            ->first();
        if (!$todoList) {
            abort(404);
        }

        return $todoList;
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
            'description' => 'string|nullable',
            'target_date' => 'date',
            'completed_at' => 'date|nullable',
        ]);
        $validator->validate();

        /** @var TodoList $todoList */
        $todoList = TodoList::find($id);
        if (!$todoList) {
            abort(404);
        }

        $todoList->fill($request->all());
        $todoList->save();

        return $todoList;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return mixed
     */
    public function destroy($id)
    {
        /** @var TodoList $todoList */
        $todoList = TodoList::find($id);
        if (!$todoList) {
            abort(404);
        }

        $todoList->delete();

        return $todoList;
    }
}
