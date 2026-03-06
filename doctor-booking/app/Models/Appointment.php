<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'email',
        'complaint',
        'appointment_date',
        'appointment_time',
        'status'
    ];

    // Relationship: One Appointment belongs to one Doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}