<?php

namespace App\Http\Controllers\API;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return [
            'success' => true,
            'data'    => Employee::get(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponseq
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|string',
                'last_name'  => 'required|string',
                'salary'     => 'required|integer',
                'hired_date' => 'required|date',
                'title'      => 'string'
            ]
        );

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors(),
            ];
        }

        $newEmployee = Employee::create($request->except('title'));
        if (!empty($request->input('title'))) {
            $position = new Position();
            $position->title = $request->input('title');
            $newEmployee->position()->save($position);
        }
        return [
            'success' => true,
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if (empty($employee)) {
            return [
                'success' => false,
                'message' => "Cannot find employee with ID: $id",
            ];
        }

        return [
            'success' => true,
            'data'    => $employee,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'string',
                'last_name'  => 'string',
                'salary'     => 'integer',
                'hired_date' => 'date',
            ]
        );

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors(),
            ];
        }

        $employee = Employee::find($id);

        if (empty($employee)) {
            return [
                'success' => false,
                'message' => "Cannot find employee with ID: $id",
            ];
        }

        $employee->fill($request->all());
        $results = $employee->save();

        return [
            'success' => $results,
            'data'    => $employee,
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (empty($employee)) {
            return [
                'success' => false,
                'message' => "Cannot find employee with ID: $id",
            ];
        }

        $position = $employee->position;
        if ($position) {
            $position->delete();
        }
        $employee->delete();
        return [
            'success' => true,
        ];
    }
}
