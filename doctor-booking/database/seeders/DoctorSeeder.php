<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        Doctor::create([
            'name' => 'Dr. Red Robe',
            'specialization' => 'General Practitioner',
        ]);

        Doctor::create([
            'name' => 'Dr. Jane Doe',
            'specialization' => 'Pediatrician',
        ]);

        Doctor::create([
            'name' => 'Dr. Agnes Tachyon',
            'specialization' => 'Orthopaedist',
        ]);
    }
}