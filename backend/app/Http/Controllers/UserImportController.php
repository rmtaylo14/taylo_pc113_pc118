<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Exports\UserExport;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class UserImportController extends Controller
{
   public function import(Request $request)
{
    try {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        $file = $request->file('file');
        $rows = Excel::toArray([], $file)[0];

        $errors = [];

        $allowedRoles = ['user', 'admin', 'manager'];  // Update as needed

        foreach ($rows as $index => $row) {
            if (empty(array_filter($row))) continue;
            if ($index === 0 && $this->isHeaderRow($row)) continue;

            $data = [
                'firstname'    => $row[1] ?? null,
                'lastname'     => $row[2] ?? null,
                'email'        => $row[3] ?? null,
                'address'      => $row[4] ?? null,
                'phone_number' => $row[5] ?? null,
                'role'         => strtolower(trim($row[6] ?? 'user')),
            ];

            $validator = Validator::make($data, [
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'email' => 'required|email',
                'role' => 'required|in:' . implode(',', $allowedRoles),
            ]);

            if ($validator->fails()) {
                $errors[] = "Row $index: " . implode(', ', $validator->errors()->all());
                continue;
            }

            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        if (!empty($errors)) {
            return response()->json(['message' => 'Import completed with errors.', 'errors' => $errors], 422);
        }

        return response()->json(['message' => 'Users imported successfully.']);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Server Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}

}
