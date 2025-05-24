<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Get all employees

    public function getEmployees()
{
    $employees = Employee::all();  // Or a more specific query if needed
    return response()->json(['employeessss' => $employees]);
}

    public function index()
    {
        return response()->json(Employee::all(), 200);
    }

    public function search(Request $request)
    {
        $search = $request->get('search');
        $employee = Employee::where('name', 'like', '%' .$search. '%')
        ->orWhere('position', 'like', '%' .$search. '%')
        ->orWhere('id', 'like', '%' .$search. '%')->get();

        return response()->json([
            'employees' => $employee
        ]);
    }


    // Store a new employee
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'position' => 'required|string',
            'salary' => 'required|numeric'
        ]);

        $employee = Employee::create($request->all());
        return response()->json($employee, 201);
    }

    // Get a single employee
    public function show($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
        return response()->json($employee, 200);
    }

    // Update an employee
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        if (is_null($employee)) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:employees,email,' . $id,
            'position' => 'string',
            'salary' => 'numeric'
        ]);

        $employee->update($validatedData);
        return response()->json([
            'message' => 'Employee updated successfully',
            'employees' => $employee
        ], 200);
    }

    // Delete an employee
    public function delete($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();
        return response()->json(['message' => 'Employee deleted'], 200);
    }
}

