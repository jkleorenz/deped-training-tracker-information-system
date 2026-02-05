@extends('layouts.app')

@section('title', 'Personnel - ' . config('app.name'))

@section('content')
<div class="mb-4">
    <h4 class="page-title mb-1">Personnel</h4>
    <p class="text-muted small mb-0">Click a row to view profile, metadata, and seminars & trainings attended.</p>
</div>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold">All personnel</span>
        <a href="{{ route('reports.excel') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-excel me-1"></i> Export to Excel</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('personnel.index') }}" class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="search" name="search" class="form-control" placeholder="Search by name, email, employee ID, department..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-deped">Search</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="personnel-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Department</th>
                        <th>School</th>
                    </tr>
                </thead>
                <tbody id="personnel-tbody">
                    @forelse($personnel as $p)
                    <tr class="personnel-row" data-user-id="{{ $p->id }}" style="cursor: pointer;">
                        <td class="fw-medium"><a href="{{ route('personnel.show', $p) }}" class="text-decoration-none text-dark">{{ $p->name }}</a></td>
                        <td>{{ $p->employee_id ?? '—' }}</td>
                        <td>{{ $p->department ?? '—' }}</td>
                        <td>{{ $p->school ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-5 d-block mb-2 opacity-50"></i>
                            <p class="mb-0">No personnel found.</p>
                            <p class="small mb-0 mt-1">Try a different search or add personnel via registration.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    document.getElementById('personnel-table').addEventListener('click', function(e) {
        var row = e.target.closest('tr.personnel-row');
        if (!row || e.target.tagName === 'A') return;
        var userId = row.getAttribute('data-user-id');
        if (userId) {
            var link = row.querySelector('a');
            if (link) window.location.href = link.getAttribute('href');
        }
    });
})();
</script>
@endpush
@endsection
