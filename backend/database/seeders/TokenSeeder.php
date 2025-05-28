<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Token;

class TokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Token::create([
            'token' => md5('string'),
            'origin' => 'http://localhost:8000',
        ]);
    }
}
