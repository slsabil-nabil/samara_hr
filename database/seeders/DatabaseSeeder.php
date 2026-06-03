<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'name' => env('HR_DEFAULT_USER_NAME', 'نبيل السنفي'),
            'email' => env('HR_DEFAULT_USER_EMAIL', 'anabeel16@gmail.com'),
            'password' => Hash::make(env('HR_DEFAULT_USER_PASSWORD', 'password')),
        ];

        $user = User::query()->first();

        if ($user) {
            $user->forceFill($data)->save();
        } else {
            User::query()->create($data);
        }
    }
}