<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    // Get all students
    public function index()
    {
        return response()->json(Students::all(), 200);
    }

    // Store a new student
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'age' => 'required|integer',
            'course' => 'required|string'
        ]);

        $student = Students::create($request->all());
        return response()->json($student, 201);
    }

    // Get a single student
    public function show($id)
    {
        $student = Students::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        return response()->json($student, 200);
    }

    // Update a student
    public function update(Request $request, $id)
    {
        $student = Students::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:students,email,' . $id,
            'age' => 'integer',
            'course' => 'string'
        ]);

        $student->update($request->all());
        return response()->json($student, 200);
    }

    // Delete a student
    public function destroy($id)
    {
        $student = Students::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $student->delete();
        return response()->json(['message' => 'Student deleted'], 200);
    }
}
