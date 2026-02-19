@extends('layouts.app')

@section('title', 'Personnel - ' . config('app.name'))

@push('styles')
<style>
.export-user-list { max-height: 280px; overflow-y: auto; }
.export-user-item { padding: 0.5rem 0.75rem; border-radius: 8px; }
.export-user-item:hover { background: #f8fafc; }

/* Personnel table: same rules as Trainings admin */
.personnel-table-wrap .personnel-table-scroll { max-height: min(70vh, 560px); overflow: auto; }
.personnel-table { border-collapse: collapse; }
.personnel-table thead th {
    position: sticky; top: 0; z-index: 2;
    background: #f1f5f9; color: #334155;
    padding: 0.6rem 0.75rem; font-size: 0.8125rem; font-weight: 600;
    white-space: nowrap; border-bottom: 2px solid #e2e8f0;
}
.personnel-table tbody td {
    padding: 0.5rem 0.75rem; vertical-align: middle;
    font-size: 0.875rem; line-height: 1.35;
    height: 48px; max-height: 56px; box-sizing: border-box;
}
.personnel-table tbody tr { transition: background 0.15s ease; }
.personnel-table tbody tr:nth-child(even) { background: #fafbfc; }
.personnel-table tbody tr:nth-child(odd) { background: #fff; }
.personnel-table tbody tr:hover { background: #eef4ff !important; }
.personnel-table tbody tr.selected { background: #e0e7ff !important; outline: 1px solid var(--deped-primary); outline-offset: -1px; }
.personnel-table .col-title { font-weight: 600; color: #1e293b; }
.personnel-table .text-truncate-cell { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.personnel-table-pagination-wrap { flex-wrap: wrap; }
.personnel-table-pagination { display: flex; flex-wrap: wrap; gap: 0.25rem; justify-content: flex-end; align-items: center; }
.personnel-table-pagination .page-link { padding: 0.35rem 0.6rem; font-size: 0.875rem; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title mb-1">Personnel</h4>
        <p class="text-muted small mb-0">Click a row to view profile, metadata, and seminars & trainings attended.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('personnel.create') }}" class="btn btn-deped"><i class="bi bi-person-plus me-1"></i> Add Personnel</a>
        @endif
        <button type="button" class="btn btn-deped" data-bs-toggle="modal" data-bs-target="#exportExcelModal"><i class="bi bi-file-earmark-excel me-1"></i> Export to Excel</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('personnel.index') }}" class="personnel-toolbar mb-3" id="personnel-toolbar-form">
            <div class="row g-2 align-items-end flex-wrap">
                <div class="col-12 col-md-auto flex-grow-1">
                    <label class="form-label visually-hidden" for="personnel-search">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" name="search" id="personnel-search" class="form-control" placeholder="Search by name, email, employee ID, position…" value="{{ request('search') }}" aria-label="Search personnel">
                    </div>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0" for="personnel-per-page">Per page</label>
                    <select name="per_page" id="personnel-per-page" class="form-select form-select-sm" style="width: auto;" aria-label="Rows per page">
                        <option value="10" {{ (int) request('per_page', 10) === 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ (int) request('per_page', 10) === 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ (int) request('per_page', 10) === 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-deped btn-sm">Search</button>
                </div>
            </div>
        </form>
        <div class="personnel-table-wrap">
            <div class="personnel-table-scroll">
                <table class="table personnel-table mb-0 align-middle" id="personnel-table" role="grid" aria-readonly="true">
                    <thead>
                        <tr>
                            <th scope="col" class="col-title">Name</th>
                            <th scope="col">Employee ID</th>
                            <th scope="col">Position</th>
                            <th scope="col">School/Office</th>
                        </tr>
                    </thead>
                    <tbody id="personnel-tbody">
                        @forelse($personnel as $p)
                        <tr class="personnel-row" data-user-id="{{ $p->id }}" tabindex="0" role="button" style="cursor: pointer;">
                            <td class="col-title"><a href="{{ route('personnel.show', $p) }}" class="text-decoration-none text-dark"><span class="text-truncate-cell d-inline-block" title="{{ e($p->name) }}">{{ $p->name }}</span></a></td>
                            <td>{{ $p->employee_id ?? '—' }}</td>
                            <td><span class="text-truncate-cell d-inline-block" title="{{ e($p->designation ?? '') }}">{{ $p->designation ?? '—' }}</span></td>
                            <td><span class="text-truncate-cell d-inline-block" title="{{ e($p->school ?? '') }}">{{ $p->school ?? '—' }}</span></td>
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
            @if($personnel->total() > 0)
                <div class="personnel-table-pagination-wrap d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
                    <div class="text-muted small" aria-live="polite">Showing {{ $personnel->firstItem() }}–{{ $personnel->lastItem() }} of {{ $personnel->total() }}</div>
                    <div class="d-flex align-items-center gap-2">
                        <nav class="personnel-table-pagination" aria-label="Personnel pagination">{{ $personnel->links() }}</nav>
                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-muted mb-0" for="personnel-per-page-footer">Per page</label>
                            <select id="personnel-per-page-footer" class="form-select form-select-sm" style="width: auto;" aria-label="Rows per page">
                                <option value="10" {{ (int) request('per_page', 10) === 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ (int) request('per_page', 10) === 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ (int) request('per_page', 10) === 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif
        </div>
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
    var form = document.getElementById('personnel-toolbar-form');
    var perPageEl = document.getElementById('personnel-per-page');
    var perPageFooter = document.getElementById('personnel-per-page-footer');
    if (form && perPageEl) {
        perPageEl.addEventListener('change', function() { form.submit(); });
    }
    if (form && perPageFooter) {
        perPageFooter.addEventListener('change', function() {
            if (perPageEl) perPageEl.value = perPageFooter.value;
            form.submit();
        });
    }

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
