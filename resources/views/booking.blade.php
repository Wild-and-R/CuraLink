<!DOCTYPE html>
<html>
<head>
    <title>CuraLink | Book Appointment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2563EB;
            --secondary: #10B981;
        }

        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
            font-size: 24px;
        }

        .hero {
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h1 {
            color: var(--primary);
            font-weight: 700;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .form-control, .form-select {
            border-radius: 12px;
        }

        .btn-primary {
            background-color: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #1e4ed8;
        }

        .slot {
            padding: 8px 14px;
            margin: 5px;
            border-radius: 12px;
            cursor: pointer;
            background-color: #e5e7eb;
        }

        .slot.booked {
            background-color: #ef4444;
            color: white;
            cursor: not-allowed;
        }

        .slot.selected {
            background-color: var(--secondary);
            color: white;
        }

        footer {
            margin-top: 60px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
    </style>
</head>
<body>

<nav class="navbar bg-white shadow-sm px-4">
    <span class="navbar-brand">CuraLink</span>
</nav>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
@if(session('success'))
<div class="toast align-items-center text-bg-success border-0" data-bs-delay="4000">
    <div class="d-flex">
        <div class="toast-body">{{ session('success') }}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
</div>
@endif

@if(session('error'))
<div class="toast align-items-center text-bg-danger border-0" data-bs-delay="4000">
    <div class="d-flex">
        <div class="toast-body">{{ session('error') }}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
</div>
@endif
</div>

<div class="container mt-5">

    <div class="hero">
        <h1>Book Your Doctor Appointment</h1>
        <p class="text-muted">Connecting You to Better Care.</p>
    </div>

    <div class="card p-4">
        <form method="POST" action="/book" id="bookingForm">
            @csrf

            <div class="mb-3">
                <label class="form-label">Doctor</label>
                <select name="doctor_id" class="form-select" id="doctorSelect" required>
                    <option value="">-- Select Doctor --</option>
                    @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->name }} ({{ $doctor->specialization }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="appointment_date" id="appointmentDate" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Time Slots</label>
                <div id="slotsContainer" class="d-flex flex-wrap"></div>
                <input type="hidden" name="appointment_time" id="selectedSlot" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" name="patient_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Complaint</label>
                <textarea name="complaint" class="form-control" rows="3" placeholder="Describe your symptoms (optional)"></textarea>
            </div>

            <button class="btn btn-primary w-100 mt-3">Book Appointment</button>
        </form>
    </div>

    <footer>
        © {{ date('Y') }} CuraLink. Developed by Wild-And-R.
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.map(function (toastEl) {
        new bootstrap.Toast(toastEl).show();
    });
});

const slots = ["09:00","10:00","11:00","13:00","14:00","15:00"];
let currentSelectedSlot = '';

function loadSlots() {
    const doctor_id = $('#doctorSelect').val();
    const date = $('#appointmentDate').val();
    if(!doctor_id || !date) return;

    currentSelectedSlot = $('#selectedSlot').val();

    $.get('/booked-slots', { doctor_id, appointment_date: date }, function(booked){
        $('#slotsContainer').empty();
        slots.forEach(time => {
            const bookedClass = booked.includes(time) ? 'booked' : '';
            const slotDiv = $('<div>').addClass('slot ' + bookedClass).text(time);

            if(!booked.includes(time)){
                slotDiv.click(function(){
                    $('.slot').removeClass('selected');
                    $(this).addClass('selected');
                    $('#selectedSlot').val(time);
                });
            }

            if(currentSelectedSlot === time && !booked.includes(time)){
                slotDiv.addClass('selected');
                $('#selectedSlot').val(time);
            }

            $('#slotsContainer').append(slotDiv);
        });
    });
}

$('#doctorSelect, #appointmentDate').change(loadSlots);
setInterval(loadSlots, 5000); // Poll every 5 seconds to keep slots up-to-date
</script>

</body>
</html>