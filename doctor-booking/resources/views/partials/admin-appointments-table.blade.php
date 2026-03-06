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
            data-complaint="{{ $a->complaint }}">
            View
        </button>
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm cancel-btn" data-id="{{ $a->id }}">
            Cancel
        </button>
    </td>
</tr>
@endforeach