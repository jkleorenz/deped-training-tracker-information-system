<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Training Report - {{ $user->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1E35FF; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; color: #1E35FF; }
        .meta { margin-bottom: 15px; }
        .meta p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 25px; font-size: 9px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Training Tracker</h1>
        <p>Training / Seminar Attendance Report</p>
    </div>
    <div class="meta">
        <p><strong>Personnel:</strong> {{ $user->name }}</p>
        @if($user->employee_id)<p><strong>Employee ID:</strong> {{ $user->employee_id }}</p>@endif
        @if($user->designation)<p><strong>Designation:</strong> {{ $user->designation }}</p>@endif
        @if($user->school)<p><strong>School/Office:</strong> {{ $user->school }}</p>@endif
        <p><strong>Report generated:</strong> {{ now()->format('F j, Y g:i A') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Type of L&amp;D</th>
                <th>Provider</th>
                <th>Venue</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Hours</th>
                <th>Attended</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trainings as $index => $t)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $t->title }}</td>
                <td>{{ $t->type_of_ld ? ucfirst($t->type_of_ld) . ($t->type_of_ld_specify ? ' (' . $t->type_of_ld_specify . ')' : '') : '—' }}</td>
                <td>{{ $t->provider ?? '—' }}</td>
                <td>{{ $t->venue ?? '—' }}</td>
                <td>{{ $t->start_date ? \Carbon\Carbon::parse($t->start_date)->format('Y-m-d') : '—' }}</td>
                <td>{{ $t->end_date ? \Carbon\Carbon::parse($t->end_date)->format('Y-m-d') : '—' }}</td>
                <td>{{ $t->hours ?? '—' }}</td>
                <td>{{ $t->pivot && $t->pivot->attended_date ? \Carbon\Carbon::parse($t->pivot->attended_date)->format('Y-m-d') : '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center">No trainings recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        Training Tracker — Confidential. Generated on {{ now()->format('Y-m-d H:i') }}.
    </div>
</body>
</html>
