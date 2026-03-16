<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentBooked;

class BookingController extends Controller
{
    public function index()
    {
        $doctors = Doctor::all();
        return view('booking', compact('doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required',
            'email' => 'required|email',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required'
        ]);

        // Anti double booking
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This slot is already booked. Please choose another time.');
        }

        // Create appointment
        $appointment = Appointment::create([
            'doctor_id' => $request->doctor_id,
            'patient_name' => $request->patient_name,
            'email' => $request->email,
            'complaint' => $request->complaint,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        // Load doctor relationship for email
        $appointment->load('doctor');

        // Send confirmation email
        //Mail::to($appointment->email)->send(new AppointmentBooked($appointment));

        return back()->with('success', 'Appointment Booked Successfully!');
    }

    public function bookedSlots(Request $request)
    {
        $slots = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->pluck('appointment_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        return response()->json($slots);
    }
}