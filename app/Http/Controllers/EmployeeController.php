<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Get all employees
    public function index(Request $request)
    {
        // $query = Employee::query();
        
        // if ($request->has('search')) {
        //     $search = $request->search;
        //     $query->where('name', 'LIKE', "%$search%")
        //     ->orWhere('email', 'LIKE', "%$search%"); 
        // }

        // return response()->json($query->get(), 200);
        $query = Employee::query();
        if ($request->has('name')) {
            $query->where('name', 'like', '%'. $request->name);
        }
        return response($query->get());
    }


    // Store a new employee
    public function store(Request $request)
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
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:employees,email,' . $id,
            'position' => 'string',
            'salary' => 'numeric'
        ]);

        $employee->update($request->all());
        return response()->json($employee, 200);
    }

    // Delete an employee
    public function destroy($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();
        return response()->json(['message' => 'Employee deleted'], 200);
    }
}

