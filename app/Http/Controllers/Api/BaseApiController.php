<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class BaseApiController extends Controller
{
    protected function paginate(Request $request, $query)
    {
        return $query->paginate(
            $request->input('per_page', 10),
            ['*'],
            'page',
            $request->input('page', 1)
        );
    }

    protected function getPaginationValidator(Request $request)
    {
        return Validator::make($request->all(), [
            'per_page' => 'integer',
            'page' => 'integer',
        ]);
    }
}
