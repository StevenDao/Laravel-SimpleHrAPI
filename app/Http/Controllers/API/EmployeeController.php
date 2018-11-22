<?php

namespace App\Http\Controllers\API;

use App\Employee;
use App\Http\Controllers\Controller;
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
        return Employee::get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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
            ]
        );

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors(),
            ];
        }

        $newEmployee = Employee::create($request->all());

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

        return $employee;
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

        $employee->delete();
        return [
            'success' => true,
        ];
    }
}
