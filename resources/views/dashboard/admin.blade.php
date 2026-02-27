@extends('layouts.app')

@section('title', 'Admin Dashboard - ' . config('app.name'))

@push('styles')
<style>
.card-hover { transition: box-shadow 0.2s ease; }
.card-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.stat-card-icon { width: 48px; height: 48px; border-radius: 12px; background: var(--deped-accent); color: var(--deped-primary); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; }
.stat-number { font-size: 1.75rem; font-weight: 700; color: var(--deped-primary); }
.quick-actions-title { font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.75rem; }
.quick-action-tile {
    display: block;
    text-decoration: none;
    color: #1e293b;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.25rem;
    text-align: center;
    transition: box-shadow 0.2s ease, border-color 0.2s ease, transform 0.15s ease;
}
.quick-action-tile:hover { color: #1e293b; border-color: var(--deped-primary); box-shadow: 0 4px 12px var(--deped-shadow); transform: translateY(-2px); }
.quick-action-tile .tile-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 0.75rem;
    border-radius: 14px;
    background: var(--deped-accent);
    color: var(--deped-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.quick-action-tile .tile-label { font-weight: 600; font-size: 0.9375rem; }
.export-user-list { max-height: 280px; overflow-y: auto; }
.export-user-item { padding: 0.5rem 0.75rem; border-radius: 8px; }
.export-user-item:hover { background: #f8fafc; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title mb-1">Admin Dashboard</h4>
        <p class="text-muted small mb-0">Overview of personnel and trainings</p>
    </div>
    <div>
        <a href="{{ route('personnel.index') }}" class="btn btn-deped"><i class="bi bi-people me-1"></i> Manage Personnel</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <a href="{{ route('personnel.index') }}" class="text-decoration-none text-dark">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="stat-card-icon flex-shrink-0"><i class="bi bi-people"></i></div>
                    <div class="min-w-0">
                        <h6 class="text-muted mb-1">Personnel</h6>
                        <p class="mb-0 stat-number" id="stat-personnel">{{ $personnel_count ?? 0 }}</p>
                        <span class="small text-muted">View all →</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('trainings.manage') }}" class="text-decoration-none text-dark">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="stat-card-icon flex-shrink-0"><i class="bi bi-journal-check"></i></div>
                    <div class="min-w-0">
                        <h6 class="text-muted mb-1">Trainings / Seminars</h6>
                        <p class="mb-0 stat-number" id="stat-trainings">{{ $trainings_count ?? 0 }}</p>
                        <span class="small text-muted">Manage →</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
<div class="mt-4">
    <h6 class="quick-actions-title"><i class="bi bi-lightning me-1"></i> Quick actions</h6>
    <div class="row g-3">
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('trainings.manage') }}" class="quick-action-tile">
                <div class="tile-icon"><i class="bi bi-journal-plus"></i></div>
                <span class="tile-label">Add training</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('personnel.create') }}" class="quick-action-tile">
                <div class="tile-icon"><i class="bi bi-person-plus"></i></div>
                <span class="tile-label">Add personnel</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <button type="button" class="quick-action-tile w-100" data-bs-toggle="modal" data-bs-target="#exportExcelModal" style="cursor: pointer;">
                <div class="tile-icon"><i class="bi bi-file-earmark-excel"></i></div>
                <span class="tile-label">Export to Excel</span>
            </button>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('trainings.manage') }}" class="quick-action-tile">
                <div class="tile-icon"><i class="bi bi-upload"></i></div>
                <span class="tile-label">Import from Excel</span>
            </a>
        </div>
    </div>
</div>

{{-- Export to Excel: select one or multiple users --}}
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-3 shadow-sm">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center gap-2" id="exportExcelModalLabel">
                    <span class="rounded-2 p-2" style="background: var(--deped-accent); color: var(--deped-primary);"><i class="bi bi-file-earmark-excel"></i></span>
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
{{-- Stats are server-rendered above; no extra API calls on page load --}}
@endpush
@endsection
