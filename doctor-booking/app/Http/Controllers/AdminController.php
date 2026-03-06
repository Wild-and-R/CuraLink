<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return view('admin');
        }

        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $appointments = Appointment::with('doctor')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();

        $stats = Appointment::select(
            DB::raw('DATE(appointment_date) as date'),
            DB::raw('count(*) as total')
        )
        ->where('appointment_date', '>=', Carbon::now()->subDays(6))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $chartLabels = $stats->pluck('date');
        $chartData = $stats->pluck('total');

        return view('admin', compact('appointments','chartLabels','chartData'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {

            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->with('error', 'Unauthorized.');
            }

            $request->session()->regenerate();

            return redirect()->route('admin');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin');
    }

    public function adminData()
{
    $appointments = Appointment::with('doctor')
        ->orderBy('appointment_date', 'asc')
        ->orderBy('appointment_time', 'asc')
        ->get();

    return view('partials.admin-appointments', compact('appointments'));
}

    public function appointmentsData()
{
    $appointments = Appointment::with('doctor')
        ->orderBy('appointment_date', 'asc')
        ->orderBy('appointment_time', 'asc')
        ->get();

    // Return only the HTML for table body
    return view('partials.admin-appointments-table', compact('appointments'));
}

    public function cancel(Request $request, $id)
{
    $appointment = Appointment::findOrFail($id);
    $appointment->delete();

    if ($request->expectsJson()) {
        return response()->json([
            'success' => 'Appointment canceled successfully.'
        ]);
    }

    return back()->with('success', 'Appointment canceled successfully.');
}
}