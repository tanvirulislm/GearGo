<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Users Seeder
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('1234'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Customers Seeder - Fixed version
        DB::table('customers')->insert([
            [
                'name' => 'John Doe',
                'phone' => '+1234567890',
                'email' => 'john.doe@example.com',
                'profile_photo' => null, // Added to match structure
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'phone' => '+1987654321',
                'email' => 'jane.smith@example.com',
                'profile_photo' => 'profiles/jane.jpg',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Cars Seeder
        DB::table('cars')->insert([
            [
                'brand' => 'Toyota',
                'model' => 'Camry',
                'year' => 2022,
                'car_type' => 'Sedan',
                'fuel_type' => 'Petrol',
                'transmission' => 'Automatic',
                'mileage' => '15000 km',
                'seats' => 5,
                'color' => 'Silver',
                'registration_number' => 'ABC123',
                'daily_rent_price' => 59.99,
                'status' => 'available',
                'image' => 'cars/camry.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'brand' => 'Honda',
                'model' => 'CR-V',
                'year' => 2021,
                'car_type' => 'SUV',
                'fuel_type' => 'Hybrid',
                'transmission' => 'Automatic',
                'mileage' => '22000 km',
                'seats' => 5,
                'color' => 'Black',
                'registration_number' => 'XYZ789',
                'daily_rent_price' => 79.99,
                'status' => 'available',
                'image' => 'cars/crv.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Rentals Seeder
        DB::table('rentals')->insert([
            [
                'customer_id' => 1,
                'car_id' => 1,
                'start_date' => now()->addDays(1)->format('Y-m-d'),
                'end_date' => now()->addDays(5)->format('Y-m-d'),
                'total_cost' => 239.96,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'customer_id' => 2,
                'car_id' => 2,
                'start_date' => now()->addDays(10)->format('Y-m-d'),
                'end_date' => now()->addDays(15)->format('Y-m-d'),
                'total_cost' => 399.95,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
