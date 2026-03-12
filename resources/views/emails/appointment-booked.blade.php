<h2>CuraLink Appointment Confirmation</h2>

<p>Hello {{ $appointment->patient_name }},</p>

<p>Your appointment has been successfully booked.</p>

<p><strong>Doctor:</strong> {{ $appointment->doctor->name }}</p>
<p><strong>Date:</strong> {{ $appointment->appointment_date }}</p>
<p><strong>Time:</strong> {{ $appointment->appointment_time }}</p>

<p>If you need to cancel or reschedule, please contact the clinic.</p>

<p>Thank you,<br>
CuraLink</p>