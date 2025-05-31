<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class UserExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch the user data for export.
     */
    public function collection()
    {
        return User::all();
    }

    /**
     * Define the headings for the Excel file.
     */
    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Password',
            'Address',
            'Phone Number',
            'Role'
        ];
    }

    /**
     * Map the user data to the correct columns.
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->firstname,
            $user->lastname,
            $user->email,
            $user->password, // Note: Exporting passwords is not recommended for security reasons
            $user->address,
            $user->phone_number,
            $user->role
        ];
    }
}
