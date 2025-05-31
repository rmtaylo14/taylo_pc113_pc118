<?php

namespace App\Imports;


use App\Models\User;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Transform each row into a User model.
     */
    public function model(array $row)
    {
        // Optional: Update existing users by email or create new ones
        return User::updateOrCreate(
            [
                'firstname'    => $row['first_name'],
                'lastname'     => $row['last_name'],
                'email'        => $row['email'],
                'password'     => bcrypt($row['password']), // Ensure passwords are hashed
                'address'      => $row['address'],
                'phone_number' => $row['phone_number'],
                'role'         => $row['role'],
            ]
        );
    }

    /**
     * Define validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'address' => ['nullable'],
            'phone_number' => ['nullable'],
            'role' => ['required', Rule::in(['user', 'manager', 'admin'])], // Adjust as needed
        ];
    }

    /**
     * Optional: Custom validation messages.
     */
    public function customValidationMessages()
    {
        return [
            'email.required' => 'The email is required.',
            'email.email' => 'The email must be valid.',
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'role.required' => 'The role is required.',
            'role.in' => 'The role must be one of user, manager, or admin.',
        ];
    }
}
