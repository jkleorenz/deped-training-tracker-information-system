@extends('layouts.app')

@section('title', 'Personnel - ' . config('app.name'))

@push('styles')
<style>
.export-user-list { max-height: 280px; overflow-y: auto; }
.export-user-item { padding: 0.5rem 0.75rem; border-radius: 8px; }
.export-user-item:hover { background: #f8fafc; }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h4 class="page-title mb-1">Personnel</h4>
    <p class="text-muted small mb-0">Click a row to view profile, metadata, and seminars & trainings attended.</p>
</div>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
        <span class="fw-semibold">All personnel</span>
        <button type="button" class="btn btn-deped" data-bs-toggle="modal" data-bs-target="#exportExcelModal"><i class="bi bi-file-earmark-excel me-1"></i> Export to Excel</button>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('personnel.index') }}" class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="search" name="search" class="form-control" placeholder="Search by name, email, employee ID, position..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-deped">Search</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="personnel-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Position</th>
                        <th>School/Office</th>
                    </tr>
                </thead>
                <tbody id="personnel-tbody">
                    @forelse($personnel as $p)
                    <tr class="personnel-row" data-user-id="{{ $p->id }}" style="cursor: pointer;">
                        <td class="fw-medium"><a href="{{ route('personnel.show', $p) }}" class="text-decoration-none text-dark">{{ $p->name }}</a></td>
                        <td>{{ $p->employee_id ?? '—' }}</td>
                        <td>{{ $p->designation ?? '—' }}</td>
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
        @if($personnel->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $personnel->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Export to Excel: select one or multiple users --}}
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-3 shadow-sm">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center gap-2" id="exportExcelModalLabel">
                    <span class="rounded-2 p-2 bg-primary bg-opacity-10 text-primary"><i class="bi bi-file-earmark-excel"></i></span>
                    Export to Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted small mb-3">Select one or more personnel to export their training records. Leave all unchecked to export everyone.</p>
                <div class="d-flex gap-2 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" id="exportSelectAll">Select all</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" id="exportClearAll">Clear</button>
                </div>
                <div class="export-user-list border rounded-3 p-2" id="exportUserList">
                    @foreach($personnel_list ?? [] as $p)
                        <label class="export-user-item d-flex align-items-center gap-2 mb-0 cursor-pointer">
                            <input type="checkbox" class="form-check-input export-user-cb" value="{{ $p->id }}" data-name="{{ e($p->name) }}">
                            <span>{{ $p->name }}</span>
                        </label>
                    @endforeach
                    @if(empty($personnel_list) || count($personnel_list ?? []) === 0)
                        <p class="text-muted small mb-0 py-2">No personnel to list.</p>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 flex-nowrap">
                <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-deped rounded-3 px-4" id="exportExcelGo"><i class="bi bi-download me-1"></i> Export to Excel</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var table = document.getElementById('personnel-table');
    if (table) {
        table.addEventListener('click', function(e) {
            var row = e.target.closest('tr.personnel-row');
            if (!row || e.target.tagName === 'A') return;
            var userId = row.getAttribute('data-user-id');
            if (userId) {
                var link = row.querySelector('a');
                if (link) window.location.href = link.getAttribute('href');
            }
        });
    }

    var baseUrl = "{{ url(route('reports.excel')) }}";
    var goBtn = document.getElementById('exportExcelGo');
    var selectAllBtn = document.getElementById('exportSelectAll');
    var clearAllBtn = document.getElementById('exportClearAll');
    var checkboxes = document.querySelectorAll('.export-user-cb');

    function buildExportUrl() {
        var ids = [];
        checkboxes.forEach(function(cb) {
            if (cb.checked) ids.push(cb.value);
        });
        if (ids.length === 0) return baseUrl;
        return baseUrl + '?' + ids.map(function(id) { return 'user_id[]=' + encodeURIComponent(id); }).join('&');
    }

    if (goBtn) {
        goBtn.addEventListener('click', function(e) {
            e.preventDefault();
            goBtn.href = buildExportUrl();
            window.location.href = goBtn.href;
        });
    }
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(function(cb) { cb.checked = true; });
        });
    }
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            checkboxes.forEach(function(cb) { cb.checked = false; });
        });
    }
})();
</script>
@endpush
@endsection
