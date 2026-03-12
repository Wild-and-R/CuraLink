<!DOCTYPE html>
<html>
<head>
    <title>CuraLink | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { --primary: #2563EB; }

        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
            font-size: 24px;
        }

        .card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .btn-primary { background-color: var(--primary); border: none; border-radius: 12px; }
        .btn-danger { border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar bg-white shadow-sm px-4">
    <span class="navbar-brand">CuraLink Admin</span>
</nav>

<div class="toast-container position-fixed top-0 end-0 p-3"></div>

<div class="container mt-5">

@if(!auth()->check())
<div class="card p-4 col-md-4 mx-auto">
    <h4 class="mb-3 text-center" style="color:#2563EB;">Admin Login</h4>
    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
        <button class="btn btn-primary w-100">Login</button>
    </form>
</div>
@else

<div class="d-flex justify-content-between mb-3">
    <h4 style="color:#2563EB;">Appointments</h4>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn btn-secondary">Logout</button>
    </form>
</div>

<div class="card p-3">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>Doctor</th>
                <th>Patient</th>
                <th>Email</th>
                <th>Date</th>
                <th>Time</th>
                <th>Complaint</th>
                <th width="100">Action</th>
            </tr>
        </thead>
        <tbody id="appointmentsBody">
            @foreach($appointments as $a)
            <tr>
                <td>{{ $a->doctor->name }}</td>
                <td>{{ $a->patient_name }}</td>
                <td>{{ $a->email }}</td>
                <td>{{ $a->appointment_date }}</td>
                <td>{{ $a->appointment_time }}</td>
                <td>
                    <button type="button" class="btn btn-info btn-sm view-complaint"
                        data-name="{{ $a->patient_name }}"
                        data-email="{{ $a->email }}"
                        data-complaint="{{ $a->complaint }}">View</button>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm cancel-btn" data-id="{{ $a->id }}">
                        Cancel
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endif

</div>

<!-- Complaint Modal -->
<div class="modal fade" id="complaintModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Appointment Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Patient:</strong> <span id="modalName"></span></p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Complaint:</strong></p>
        <p id="modalComplaint"></p>
      </div>
    </div>
  </div>
</div>

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelConfirmModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Cancellation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to cancel this appointment?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" id="confirmCancelBtn" class="btn btn-danger">Yes, Cancel</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toastContainer = document.querySelector('.toast-container');
    const complaintModal = new bootstrap.Modal(document.getElementById('complaintModal'));
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelConfirmModal'));
    let cancelId = null;

    // Toast notifications
    @if(session('success'))
        const successToast = `<div class="toast align-items-center text-bg-success border-0" data-bs-delay="4000">
            <div class="d-flex"><div class="toast-body">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
        toastContainer.innerHTML = successToast;
        new bootstrap.Toast(toastContainer.firstElementChild).show();
    @endif

    @if(session('error'))
        const errorToast = `<div class="toast align-items-center text-bg-danger border-0" data-bs-delay="4000">
            <div class="d-flex"><div class="toast-body">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
        toastContainer.innerHTML = errorToast;
        new bootstrap.Toast(toastContainer.firstElementChild).show();
    @endif

    // Attach complaint modal
    function attachComplaintModal() {
        document.querySelectorAll('.view-complaint').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modalName').textContent = this.dataset.name;
                document.getElementById('modalEmail').textContent = this.dataset.email;
                document.getElementById('modalComplaint').textContent = this.dataset.complaint || 'No complaint provided.';
                complaintModal.show();
            });
        });
    }

    // Attach cancel buttons
    function attachCancelButtons() {
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // prevent immediate action
                cancelId = this.dataset.id;
                cancelModal.show();
            });
        });
    }

    // Confirm cancellation
    document.getElementById('confirmCancelBtn').addEventListener('click', function() {
        if(!cancelId) return;

        fetch(`/admin/${cancelId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            cancelModal.hide();
            if(data.success){
                refreshAppointments();
                const toastHTML = `<div class="toast align-items-center text-bg-success border-0" data-bs-delay="4000">
                    <div class="d-flex"><div class="toast-body">${data.success}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
                toastContainer.insertAdjacentHTML('beforeend', toastHTML);
                new bootstrap.Toast(toastContainer.lastElementChild).show();
            } else {
                alert(data.error || 'Cancel failed.');
            }
            cancelId = null;
        });
    });

    // Refresh appointments live
    function refreshAppointments() {
        fetch('/admin/appointments-data')
            .then(res => res.text())
            .then(html => {
                document.getElementById('appointmentsBody').innerHTML = html;
                attachComplaintModal();
                attachCancelButtons();
            });
    }

    attachComplaintModal();
    attachCancelButtons();

    // Poll every 5 seconds
    setInterval(refreshAppointments, 5000);
});
</script>

</body>
</html>