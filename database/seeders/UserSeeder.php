<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'company_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'contact_number' => 9999999999,
            'address' => 'address',
            'password' => app('hash')->make('123@123'),
            'gst_no' => '12',
            'status' => 1,
            'otp_verified_status' => 1,
            'email_verified_at' => date("Y-m-d"),
            'is_admin' => 1,
            
        ]);
    }
}
