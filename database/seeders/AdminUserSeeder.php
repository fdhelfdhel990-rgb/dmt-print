<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = config('dmt.admin.email');
        $password = config('dmt.admin.password');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            $this->command?->warn('Admin tidak dibuat: DMT_ADMIN_EMAIL dan DMT_ADMIN_PASSWORD belum diisi.');

            return;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => config('dmt.admin.name', 'Owner Admin'),
                'password' => $password,
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
