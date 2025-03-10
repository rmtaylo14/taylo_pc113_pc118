<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Get all students
    public function index()
    {
        return response()->json(Student::all(), 200);
    }

    public function search(Request $request)
    {
        $search = $request->get('searchlist');
        $student = Student::where('name', 'like', '%' .$search. '%')
        ->orWhere('course', 'like', '%' .$search. '%')
        ->orWhere('id', 'like', '%' .$search. '%')->get();

        return response()->json([
            'students' => $student
        ]);
    }

    // Store a new student
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'age' => 'required|integer',
            'course' => 'required|string',
        ]);

        $student = Student::create($validatedData);
        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student
        ], 201);
    }

    // Get a single student
    public function show($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        return response()->json($student, 200);
    }

    // Update a student
    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (is_null($student)) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:students,email',
            'age' => 'integer',
            'course' => 'string',
        ]);

        $student->update($validatedData);
        return response()->json([
            'message' => 'Student updated successfully',
            'students' => $student
        ], 200);
    }

    // Delete a student
    public function delete($id)
    {
        $student = Student::find($id);
        if (is_null($student)) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $student->delete();
        return response()->json([
            'message' => 'Student deleted succesfully'], 200);
    }
}
