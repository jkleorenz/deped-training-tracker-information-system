@extends('layouts.app')

@section('title', 'My Dashboard - ' . config('app.name'))

@push('styles')
<style>
.dashboard-personnel .card-header.card-header-green { background-color: var(--deped-primary) !important; color: #fff; }
.dashboard-personnel .card-header.card-header-green .mb-0 { color: #fff; }
.dashboard-personnel .card-body { padding: 1.25rem 1.25rem; }
.dashboard-personnel .card-hover { transition: box-shadow 0.2s ease; }
.dashboard-personnel .card-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.dashboard-personnel .stat-card-icon { width: 48px; height: 48px; border-radius: 12px; background: var(--deped-accent); color: var(--deped-primary); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; }
.dashboard-personnel .stat-number { font-size: 1.75rem; font-weight: 700; color: var(--deped-primary); }
.dashboard-personnel .d-flex.justify-content-between.mb-4 .btn { display: inline-flex; align-items: center; justify-content: center; }

/* ——— Trainings table: scanability, density, sticky header ——— */
.trainings-table-wrap .trainings-table-scroll { max-height: min(70vh, 560px); overflow: auto; }
.trainings-table { border-collapse: collapse; }
.trainings-table thead th {
    position: sticky; top: 0; z-index: 2;
    background: #f1f5f9; color: #334155;
    padding: 0.6rem 0.75rem; font-size: 0.8125rem; font-weight: 600;
    white-space: nowrap; border-bottom: 2px solid #e2e8f0;
}
.trainings-table tbody td {
    padding: 0.5rem 0.75rem; vertical-align: middle;
    font-size: 0.875rem; line-height: 1.35;
    height: 48px; max-height: 56px; box-sizing: border-box;
}
.trainings-table tbody tr { transition: background 0.15s ease; }
.trainings-table tbody tr:nth-child(even) { background: #fafbfc; }
.trainings-table tbody tr:nth-child(odd) { background: #fff; }
.trainings-table tbody tr:hover { background: #eef4ff !important; }
.trainings-table tbody tr.selected { background: #e0e7ff !important; outline: 1px solid var(--deped-primary); outline-offset: -1px; }
.trainings-table .col-title { font-weight: 600; color: #1e293b; }
.trainings-table .col-expand { width: 2.25rem; padding-left: 0.5rem !important; }
.trainings-table .col-actions { width: 6rem; }
.trainings-table .text-truncate-cell { max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.trainings-table .sortable { cursor: pointer; user-select: none; }
.trainings-table .sortable:hover { color: var(--deped-primary); }
.trainings-table .sortable .sort-icon { opacity: 0.4; margin-left: 0.25rem; }
.trainings-table .sortable[aria-sort="ascending"] .sort-icon-desc { display: none; }
.trainings-table .sortable[aria-sort="ascending"] .sort-icon-asc { display: inline; }
.trainings-table .sortable[aria-sort="descending"] .sort-icon-asc { display: none; }
.trainings-table .sortable[aria-sort="descending"] .sort-icon-desc { display: inline; }
.trainings-table .sortable[aria-sort="none"] .sort-icon-asc,
.trainings-table .sortable[aria-sort="none"] .sort-icon-desc { display: none; }
.trainings-table .row-detail { background: #f8fafc !important; }
.trainings-table .row-detail td { padding: 0.75rem 1rem; font-size: 0.8125rem; vertical-align: top; border-top: none; }
.trainings-table .row-detail .detail-grid { display: grid; grid-template-columns: auto 1fr; gap: 0.25rem 1rem; }
.trainings-table .row-detail .detail-label { color: #64748b; }
.trainings-table .btn-action-icon { padding: 0.35rem; width: 2rem; height: 2rem; }
.trainings-table .btn-action-icon.btn-delete:hover { color: #dc2626; border-color: #dc2626; }

/* Pagination: no overflow on small screens */
.trainings-pagination-wrap { flex-wrap: wrap; }
.trainings-pagination { display: flex; flex-wrap: wrap; gap: 0.25rem; justify-content: flex-end; align-items: center; }
.trainings-pagination .page-link { padding: 0.35rem 0.6rem; font-size: 0.875rem; color: var(--deped-primary) !important; }
.trainings-pagination .page-link:hover { color: var(--deped-primary) !important; background-color: var(--deped-accent) !important; }
.trainings-pagination .page-item.active .page-link { background-color: var(--deped-primary) !important; border-color: var(--deped-primary) !important; color: #fff !important; }

/* ——— Mobile: stacked cards (no horizontal scroll) ——— */
@media (max-width: 767.98px) {
    .trainings-table-wrap { display: none !important; }
    .trainings-cards-wrap.d-none { display: none !important; }
    .trainings-cards-wrap:not(.d-none) { display: block !important; }
    .trainings-card .card-header .d-flex { width: 100%; justify-content: flex-end; }
}
@media (min-width: 768px) {
    .trainings-cards-wrap { display: none !important; }
}
.trainings-card-mobile {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.trainings-card-mobile .card-mobile-title { font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
.trainings-card-mobile .card-mobile-row { display: flex; gap: 0.5rem; font-size: 0.8125rem; margin-bottom: 0.25rem; }
.trainings-card-mobile .card-mobile-label { color: #64748b; min-width: 5rem; }
.trainings-card-mobile .card-mobile-actions { margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #eee; display: flex; gap: 0.5rem; }
</style>
@endpush

@section('content')
<div class="dashboard-personnel">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">My Dashboard</h4>
            <p class="text-muted small mb-0">Your trainings and personnel info</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#modalImportExcel"><i class="bi bi-upload me-1"></i> Import Excel</button>
            <a href="{{ route('reports.excel') }}" class="btn btn-outline-success" title="Export seminars and trainings attended to Excel"><i class="bi bi-file-earmark-excel me-1"></i> Export STA Excel</a>
        </div>
    </div>

    {{-- Seminars & trainings attended --}}
    <div class="card mt-3 trainings-card">
        <div class="card-header card-header-green py-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0">Seminars & Trainings Attended</h6>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-light btn-sm py-1" data-bs-toggle="modal" data-bs-target="#modalAddMyTraining" title="Add training or seminar to your record" id="btn-add-training-top">
                    <i class="bi bi-plus-lg"></i> Add training / seminar
                </button>
            </div>
        </div>
        <div class="card-body">
            {{-- Search & filters (no reload) --}}
            <div class="trainings-toolbar mb-3">
                <div class="row g-2 align-items-end flex-wrap">
                    <div class="col-12 col-md-auto flex-grow-1">
                        <label class="form-label visually-hidden" for="trainings-search">Search</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                            <input type="search" id="trainings-search" class="form-control" placeholder="Search title, provider, venue…" aria-label="Search trainings">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label class="form-label visually-hidden" for="filter-type_of_ld">Type of L&amp;D</label>
                        <select id="filter-type_of_ld" class="form-select form-select-sm" aria-label="Filter by Type of L&D">
                            <option value="">All types</option>
                            <option value="Managerial">Managerial</option>
                            <option value="Supervisory">Supervisory</option>
                            <option value="Technical">Technical</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label visually-hidden" for="filter-year">Year</label>
                        <select id="filter-year" class="form-select form-select-sm" aria-label="Filter by year">
                            <option value="">All years</option>
                            @foreach(range(now()->year, now()->year - 15) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label visually-hidden" for="filter-min-hrs">Min hours</label>
                        <input type="number" id="filter-min-hrs" class="form-control form-control-sm" min="0" placeholder="Min hrs" aria-label="Minimum hours" style="width: 6rem;">
                    </div>
                    <div class="col-auto">
                        <label class="form-label visually-hidden" for="filter-max-hrs">Max hours</label>
                        <input type="number" id="filter-max-hrs" class="form-control form-control-sm" min="0" placeholder="Max hrs" aria-label="Maximum hours" style="width: 6rem;">
                    </div>
                    <div class="col-auto">
                        <button type="button" id="btn-clear-filters" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                            <i class="bi bi-x-lg" aria-hidden="true"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <div id="trainings-loading" class="text-center py-5 d-none" role="status" aria-live="polite">
                <div class="spinner-border" style="color: var(--deped-primary);" aria-hidden="true"></div>
                <p class="mt-2 mb-0 text-muted small">Loading…</p>
            </div>
            <div id="trainings-empty" class="text-center py-5 text-muted d-none">
                <i class="bi bi-journal-check display-5 d-block mb-2 opacity-50" aria-hidden="true"></i>
                <p class="mb-2">No trainings added yet.</p>
                <button type="button" class="btn btn-deped" data-bs-toggle="modal" data-bs-target="#modalAddMyTraining">
                    <i class="bi bi-plus-lg"></i> Add training / seminar
                </button>
            </div>

            {{-- Desktop: table with sticky header --}}
            <div class="trainings-table-wrap d-none" id="trainings-wrap">
                <div class="trainings-table-scroll">
                    <table class="table trainings-table mb-0 align-middle" role="grid" aria-readonly="true">
                        <thead>
                            <tr>
                                <th scope="col" class="col-expand"></th>
                                <th scope="col" class="col-title sortable" data-sort="title" aria-sort="none"><span class="sort-label">Title</span></th>
                                <th scope="col" class="col-type">Type of L&amp;D</th>
                                <th scope="col" class="col-provider">Provider</th>
                                <th scope="col" class="col-venue">Venue</th>
                                <th scope="col" class="col-duration sortable" data-sort="start_date" aria-sort="descending"><span class="sort-label">Duration</span></th>
                                <th scope="col" class="col-hrs text-end sortable" data-sort="hours" aria-sort="none"><span class="sort-label">Hrs</span></th>
                                <th scope="col" class="col-actions text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="trainings-tbody"></tbody>
                    </table>
                </div>
                <div class="trainings-pagination-wrap d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
                    <div class="trainings-pagination-info text-muted small" id="trainings-pagination-info" aria-live="polite"></div>
                    <nav class="trainings-pagination" id="trainings-pagination" aria-label="Trainings pagination"></nav>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0" for="trainings-per-page">Per page</label>
                        <select id="trainings-per-page" class="form-select form-select-sm" style="width: auto;" aria-label="Rows per page">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Mobile: stacked cards --}}
            <div class="trainings-cards-wrap d-none" id="trainings-cards-wrap">
                <div id="trainings-cards"></div>
                <div class="trainings-pagination-wrap d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                    <div class="trainings-pagination-info text-muted small" id="trainings-cards-pagination-info"></div>
                    <nav class="trainings-pagination" id="trainings-cards-pagination" aria-label="Trainings pagination"></nav>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0" for="trainings-cards-per-page">Per page</label>
                        <select id="trainings-cards-per-page" class="form-select form-select-sm" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Edit attendance (date attended, remarks) --}}
<div class="modal fade" id="modalEditAttendance" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3" id="edit-attendance-training-title"></p>
                <form id="form-edit-attendance">
                    <input type="hidden" name="training_id" id="edit-attendance-training-id">
                    <div class="mb-3">
                        <label class="form-label">Date attended</label>
                        <input type="date" name="attended_date" id="edit-attendance-date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" id="edit-attendance-remarks" class="form-control" maxlength="255" placeholder="Optional">
                    </div>
                    <button type="submit" class="btn btn-deped" id="btn-save-attendance">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Delete training from my record (confirmation) --}}
<div class="modal fade" id="modalDeleteTraining" tabindex="-1" aria-labelledby="modalDeleteTrainingTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteTrainingTitle">Remove from my record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Remove "<span id="delete-training-title"></span>" from your record? This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-training">
                    <i class="bi bi-trash me-1"></i> Remove
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Add training / seminar to my record --}}
<div class="modal fade" id="modalAddMyTraining" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add training / seminar to my record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-existing" type="button">From existing list</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-new" type="button">Add new training / seminar</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-existing">
                        <form id="form-attach-existing">
                            <div class="mb-3">
                                <label class="form-label">Training / Seminar <span class="text-danger">*</span></label>
                                <select name="training_id" class="form-select" required>
                                    <option value="">— Select —</option>
                                </select>
                                <small class="text-muted">Select a training already in the system to add to your record.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date attended</label>
                                    <input type="date" name="attended_date" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" name="remarks" class="form-control" maxlength="255" placeholder="Optional">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-deped" id="btn-attach-existing">Add to my record</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="tab-new">
                        <form id="form-add-new-training">
                            <div class="mb-2">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">CONDUCTED/ SPONSORED BY (Write in full)</label>
                                <input type="text" name="provider" class="form-control" maxlength="255">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Type of L&amp;D</label>
                                <select name="type_of_ld" class="form-select">
                                    <option value="">— Select —</option>
                                    <option value="Managerial">Managerial</option>
                                    <option value="Supervisory">Supervisory</option>
                                    <option value="Technical">Technical</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-2 type-of-ld-other-wrap d-none">
                                <label class="form-label">If Other, please specify</label>
                                <input type="text" name="type_of_ld_specify" class="form-control" maxlength="100" placeholder="Specify type">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Venue</label>
                                <input type="text" name="venue" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Hours</label>
                                    <input type="number" name="hours" class="form-control" min="0">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Date attended</label>
                                    <input type="date" name="attended_date" class="form-control">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control" maxlength="255" placeholder="Optional">
                            </div>
                            <button type="submit" class="btn btn-deped" id="btn-add-new-training">Add to my record</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Import Trainings from Excel --}}
<div class="modal fade" id="modalImportExcel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Trainings from Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Import trainings from an Excel file to your record. Columns: Title, Type of L&D, Provider, Venue, Start Date, End Date, Hours, Attended Date.</p>
                <div class="mb-2">
                    <label class="form-label">Excel file (.xlsx, .xls) <span class="text-danger">*</span></label>
                    <input type="file" id="import-excel-file" class="form-control" accept=".xlsx,.xls">
                    <div id="import-excel-errors" class="invalid-feedback d-none"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-deped" id="btn-import-excel"><span class="btn-text">Import</span><span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span></button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = (meta && meta.getAttribute('content')) || '';

    const DATE_DISPLAY_RE = /^(\d{4})-(\d{2})-(\d{2})$/;
    function formatDateDisplay(isoStr) {
        if (!isoStr) return '—';
        var m = String(isoStr).match(DATE_DISPLAY_RE);
        if (!m) return isoStr;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return (parseInt(m[3], 10)) + ' ' + months[parseInt(m[2], 10) - 1] + ' ' + m[1];
    }

    function escapeHtml(s) {
        if (s == null || s === '') return '—';
        var div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    function typeOfLdLabel(typeOfLd, specify) {
        if (!typeOfLd) return '—';
        var base = String(typeOfLd).charAt(0).toUpperCase() + String(typeOfLd).slice(1);
        if (specify && String(specify).trim()) base += ' (' + String(specify).trim() + ')';
        return base;
    }

    // ——— Query state & URL sync ———
    function getQueryParams() {
        var q = document.getElementById('trainings-search');
        var typeOfLd = document.getElementById('filter-type_of_ld');
        var year = document.getElementById('filter-year');
        var minH = document.getElementById('filter-min-hrs');
        var maxH = document.getElementById('filter-max-hrs');
        var perPageEl = document.getElementById('trainings-per-page');
        var perPageCards = document.getElementById('trainings-cards-per-page');
        var perPage = perPageEl ? parseInt(perPageEl.value, 10) : (perPageCards ? parseInt(perPageCards.value, 10) : 10);
        return {
            q: (q && q.value.trim()) || '',
            type_of_ld: (typeOfLd && typeOfLd.value) || '',
            year: (year && year.value) || '',
            min_hours: (minH && minH.value) ? minH.value : '',
            max_hours: (maxH && maxH.value) ? maxH.value : '',
            sort: window._trainingsSort || 'start_date',
            direction: window._trainingsDirection || 'desc',
            page: window._trainingsPage || 1,
            per_page: perPage
        };
    }

    function buildQueryString(params) {
        var parts = [];
        if (params.q) parts.push('q=' + encodeURIComponent(params.q));
        if (params.type_of_ld) parts.push('type_of_ld=' + encodeURIComponent(params.type_of_ld));
        if (params.year) parts.push('year=' + encodeURIComponent(params.year));
        if (params.min_hours) parts.push('min_hours=' + encodeURIComponent(params.min_hours));
        if (params.max_hours) parts.push('max_hours=' + encodeURIComponent(params.max_hours));
        if (params.sort) parts.push('sort=' + encodeURIComponent(params.sort));
        if (params.direction) parts.push('direction=' + encodeURIComponent(params.direction));
        if (params.page && params.page > 1) parts.push('page=' + params.page);
        if (params.per_page && params.per_page !== 10) parts.push('per_page=' + params.per_page);
        return parts.length ? '?' + parts.join('&') : '';
    }

    function syncUrl(params) {
        var qs = buildQueryString(params);
        var url = window.location.pathname + qs;
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, '', url);
        }
    }

    function readParamsFromUrl(opts) {
        opts = opts || {};
        var search = window.location.search;
        var params = {};
        if (search) {
            search.slice(1).split('&').forEach(function(pair) {
                var i = pair.indexOf('=');
                if (i > 0) {
                    var k = decodeURIComponent(pair.slice(0, i));
                    var v = decodeURIComponent(pair.slice(i + 1));
                    params[k] = v;
                }
            });
        }
        // Only read page from URL on initial load, not on user-initiated actions
        if (!opts.skipPage && params.page) window._trainingsPage = Math.max(1, parseInt(params.page, 10));
        if (params.sort) window._trainingsSort = params.sort;
        if (params.direction) window._trainingsDirection = params.direction;
        var searchEl = document.getElementById('trainings-search');
        var typeOfLdEl = document.getElementById('filter-type_of_ld');
        var yearEl = document.getElementById('filter-year');
        var minEl = document.getElementById('filter-min-hrs');
        var maxEl = document.getElementById('filter-max-hrs');
        var perEl = document.getElementById('trainings-per-page');
        var perCards = document.getElementById('trainings-cards-per-page');
        // Only update fields if they don't have focus (user is not typing)
        if (params.q && searchEl && document.activeElement !== searchEl) searchEl.value = params.q;
        if (params.type_of_ld && typeOfLdEl && document.activeElement !== typeOfLdEl) typeOfLdEl.value = params.type_of_ld;
        if (params.year && yearEl && document.activeElement !== yearEl) yearEl.value = params.year;
        if (params.min_hours && minEl && document.activeElement !== minEl) minEl.value = params.min_hours;
        if (params.max_hours && maxEl && document.activeElement !== maxEl) maxEl.value = params.max_hours;
        if (params.per_page && perEl && document.activeElement !== perEl) perEl.value = params.per_page;
        if (params.per_page && perCards && document.activeElement !== perCards) perCards.value = params.per_page;
    }

    // ——— Fetch & render ———
    var loadAbort = null;
    async function loadMyTrainings(skipReadUrl) {
        readParamsFromUrl({ skipPage: !!skipReadUrl });
        var params = getQueryParams();
        var loading = document.getElementById('trainings-loading');
        var empty = document.getElementById('trainings-empty');
        var wrap = document.getElementById('trainings-wrap');
        var cardsWrap = document.getElementById('trainings-cards-wrap');

        function showLoading() {
            if (loading) {
                loading.classList.remove('d-none');
                var p = loading.querySelector('p');
                if (p) p.textContent = 'Loading…';
            }
            if (wrap) wrap.classList.add('d-none');
            if (cardsWrap) cardsWrap.classList.add('d-none');
            if (empty) empty.classList.add('d-none');
        }
        function showError(msg) {
            if (loading) { loading.classList.remove('d-none'); loading.querySelector('p').textContent = msg || 'Error loading data.'; }
            if (wrap) wrap.classList.add('d-none');
            if (cardsWrap) cardsWrap.classList.add('d-none');
            if (empty) empty.classList.add('d-none');
        }

        if (loadAbort) loadAbort.abort();
        loadAbort = new AbortController();
        showLoading();

        try {
            var qs = buildQueryString(params);
            var r = await fetch('/api/my/record/trainings' + qs, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                signal: loadAbort.signal
            });
            var json = await r.json().catch(function() { return {}; });
            if (loading) loading.classList.add('d-none');

            if (!r.ok) {
                showError(json.message || 'Error loading data.');
                return;
            }

            var data = json.data || [];
            var meta = json.meta || {};
            window._trainingsSort = meta.sort || 'start_date';
            window._trainingsDirection = meta.direction || 'desc';
            window._trainingsPage = meta.current_page || 1;

            syncUrl(getQueryParams());

            if (data.length === 0 && (meta.total === undefined || meta.total === 0)) {
                if (empty) {
                    empty.classList.remove('d-none');
                    var ep = empty.querySelector('p');
                    if (ep) ep.textContent = 'No trainings added yet.';
                }
                if (wrap) { wrap.classList.add('d-none'); var tb = document.getElementById('trainings-tbody'); if (tb) tb.innerHTML = ''; }
                if (cardsWrap) { cardsWrap.classList.add('d-none'); var crd = document.getElementById('trainings-cards'); if (crd) crd.innerHTML = ''; }
                return;
            }

            if (empty) empty.classList.add('d-none');
            renderTable(data, meta);
            renderCards(data, meta);
            renderPagination(meta);
            updateSortIndicators();
            bindRowExpand();
        } catch (e) {
            if (e.name === 'AbortError') return;
            if (loading) loading.classList.add('d-none');
            showError('Error loading data.');
        }
    }

    function renderTable(data, meta) {
        var wrap = document.getElementById('trainings-wrap');
        var tbody = document.getElementById('trainings-tbody');
        if (!wrap || !tbody) return;
        var rows = [];
        data.forEach(function(t, idx) {
            var title = (t && t.title != null) ? String(t.title) : '';
            var titleDisplay = title.length > 50 ? title.slice(0, 50) + '…' : title;
            var typeOfLd = typeOfLdLabel(t && t.type_of_ld, t && t.type_of_ld_specify);
            var provider = (t && t.provider) ? String(t.provider) : '—';
            var venue = (t && t.venue) ? String(t.venue) : '—';
            var duration = (t.start_date_display || t.start_date) && (t.end_date_display || t.end_date)
                ? (t.start_date_display || formatDateDisplay(t.start_date)) + ' – ' + (t.end_date_display || formatDateDisplay(t.end_date))
                : (t.start_date_display || formatDateDisplay(t.start_date)) || '—';
            var hrs = (t && t.hours != null) ? t.hours : '—';
            var remarks = (t && t.remarks != null) ? String(t.remarks) : '';
            var attDisplay = t.attended_date_display || (t.attended_date ? formatDateDisplay(t.attended_date) : null) || '—';
            var fullDetail = 'Provider: ' + (t.provider || '—') + '\nVenue: ' + (t.venue || '—') + '\nStart: ' + (t.start_date_display || '—') + '\nEnd: ' + (t.end_date_display || '—') + '\nAttended: ' + attDisplay + '\nHours: ' + (t.hours != null ? t.hours : '—') + (remarks ? '\nRemarks: ' + remarks : '');
            rows.push('<tr class="trainings-data-row" data-id="' + (t && t.id) + '" data-index="' + idx + '" tabindex="0" role="button">');
            rows.push('<td class="col-expand"><button type="button" class="btn btn-link btn-sm p-0 text-secondary expand-row" aria-label="Expand row" data-index="' + idx + '"><i class="bi bi-chevron-right"></i></button></td>');
            rows.push('<td class="col-title"><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(title) + '">' + escapeHtml(titleDisplay) + '</span></td>');
            rows.push('<td class="col-type">' + escapeHtml(typeOfLd) + '</td>');
            rows.push('<td class="col-provider"><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(provider) + '">' + escapeHtml(provider) + '</span></td>');
            rows.push('<td class="col-venue"><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(venue) + '">' + escapeHtml(venue) + '</span></td>');
            rows.push('<td class="col-duration">' + escapeHtml(duration) + '</td>');
            rows.push('<td class="col-hrs text-end">' + (typeof hrs === 'number' ? hrs : escapeHtml(hrs)) + '</td>');
            rows.push('<td class="col-actions text-end"><div class="d-flex justify-content-end gap-1">');
            rows.push('<button type="button" class="btn btn-outline-secondary btn-sm btn-action-icon edit-attendance" data-id="' + (t && t.id) + '" data-title="' + escapeHtml(title) + '" data-attended="' + (t && t.attended_date ? t.attended_date : '') + '" data-remarks="' + escapeHtml(remarks) + '" title="Edit attendance" aria-label="Edit"><i class="bi bi-pencil-square"></i></button>');
            rows.push('<button type="button" class="btn btn-outline-secondary btn-sm btn-action-icon btn-delete remove-attendance" data-id="' + (t && t.id) + '" data-title="' + escapeHtml(title) + '" title="Remove from my record" aria-label="Remove"><i class="bi bi-trash"></i></button>');
            rows.push('</div></td></tr>');
            rows.push('<tr class="row-detail d-none" data-detail-for="' + (t && t.id) + '" aria-hidden="true"><td colspan="8"><div class="detail-grid"><span class="detail-label">Title</span><span>' + escapeHtml(title) + '</span><span class="detail-label">Provider</span><span>' + escapeHtml(provider) + '</span><span class="detail-label">Venue</span><span>' + escapeHtml(venue) + '</span><span class="detail-label">Start</span><span>' + (t.start_date_display || formatDateDisplay(t.start_date)) + '</span><span class="detail-label">End</span><span>' + (t.end_date_display || formatDateDisplay(t.end_date)) + '</span><span class="detail-label">Attended</span><span>' + attDisplay + '</span><span class="detail-label">Hours</span><span>' + (t.hours != null ? t.hours : '—') + '</span>' + (remarks ? '<span class="detail-label">Remarks</span><span>' + escapeHtml(remarks) + '</span>' : '') + '</div></td></tr>');
        });
        tbody.innerHTML = rows.join('');
        wrap.classList.remove('d-none');
        var cardsWrap = document.getElementById('trainings-cards-wrap');
        if (window.innerWidth < 768) {
            wrap.classList.add('d-none');
            if (cardsWrap) cardsWrap.classList.remove('d-none');
        }
    }

    function renderCards(data, meta) {
        var wrap = document.getElementById('trainings-cards-wrap');
        var container = document.getElementById('trainings-cards');
        if (!wrap || !container) return;
        container.innerHTML = data.map(function(t) {
            var title = (t && t.title != null) ? String(t.title) : '';
            var typeOfLd = typeOfLdLabel(t && t.type_of_ld, t && t.type_of_ld_specify);
            var duration = (t.start_date_display || formatDateDisplay(t.start_date)) + (t.end_date_display || t.end_date ? ' – ' + (t.end_date_display || formatDateDisplay(t.end_date)) : '');
            var remarks = (t && t.remarks != null) ? String(t.remarks) : '';
            return '<div class="trainings-card-mobile" data-id="' + (t && t.id) + '">' +
                '<div class="card-mobile-title">' + escapeHtml(title) + '</div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Type of L&D</span><span>' + escapeHtml(typeOfLd) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Provider</span><span>' + escapeHtml(t && t.provider) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Venue</span><span>' + escapeHtml(t && t.venue) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Duration</span><span>' + escapeHtml(duration) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Hrs</span><span>' + (t.hours != null ? t.hours : '—') + '</span></div>' +
                (remarks ? '<div class="card-mobile-row"><span class="card-mobile-label">Remarks</span><span>' + escapeHtml(remarks) + '</span></div>' : '') +
                '<div class="card-mobile-actions">' +
                '<button type="button" class="btn btn-outline-secondary btn-sm edit-attendance" data-id="' + (t && t.id) + '" data-title="' + escapeHtml(title) + '" data-attended="' + (t && t.attended_date ? t.attended_date : '') + '" data-remarks="' + escapeHtml(remarks) + '" title="Edit attendance"><i class="bi bi-pencil-square me-1"></i>Edit</button>' +
                '<button type="button" class="btn btn-outline-secondary btn-sm btn-delete remove-attendance" data-id="' + (t && t.id) + '" data-title="' + escapeHtml(title) + '" title="Remove from my record"><i class="bi bi-trash me-1"></i>Remove</button>' +
                '</div></div>';
        }).join('');
        if (window.innerWidth < 768 && data.length > 0) {
            wrap.classList.remove('d-none');
        }
    }

    function renderPagination(meta) {
        var total = meta.total || 0;
        var from = meta.from || 0;
        var to = meta.to || 0;
        var current = meta.current_page || 1;
        var last = meta.last_page || 1;
        var perPage = meta.per_page || 10;
        var info = 'Showing ' + (total ? (from + '–' + to + ' of ' + total) : '0') + ' ';
        document.getElementById('trainings-pagination-info').textContent = info;
        var cardsInfo = document.getElementById('trainings-cards-pagination-info');
        if (cardsInfo) cardsInfo.textContent = info;

        function makePageNav(id) {
            var nav = document.getElementById(id);
            if (!nav) return;
            nav.innerHTML = '';
            if (last <= 1) return;
            var ul = document.createElement('ul');
            ul.className = 'pagination pagination-sm mb-0 flex-wrap';
            function addPage(num, label) {
                if (num < 1 || num > last) return;
                var li = document.createElement('li');
                li.className = 'page-item' + (num === current ? ' active' : '');
                var a = document.createElement('a');
                a.href = '#';
                a.className = 'page-link';
                a.textContent = label;
                a.setAttribute('aria-label', 'Page ' + num);
                a.addEventListener('click', function(e) { e.preventDefault(); window._trainingsPage = num; loadMyTrainings(true); });
                li.appendChild(a);
                ul.appendChild(li);
            }
            addPage(1, '1');
            if (current > 3) { var ell = document.createElement('li'); ell.className = 'page-item disabled'; ell.innerHTML = '<span class="page-link">…</span>'; ul.appendChild(ell); }
            if (current > 2) addPage(current - 1, String(current - 1));
            if (current > 1 && current < last) addPage(current, String(current));
            if (current < last - 1) addPage(current + 1, String(current + 1));
            if (current < last - 2) { var ell2 = document.createElement('li'); ell2.className = 'page-item disabled'; ell2.innerHTML = '<span class="page-link">…</span>'; ul.appendChild(ell2); }
            if (last > 1) addPage(last, String(last));
            nav.appendChild(ul);
        }
        makePageNav('trainings-pagination');
        makePageNav('trainings-cards-pagination');
    }

    function updateSortIndicators() {
        var sort = window._trainingsSort || 'start_date';
        var dir = window._trainingsDirection || 'desc';
        document.querySelectorAll('.trainings-table .sortable').forEach(function(th) {
            var col = th.getAttribute('data-sort');
            th.setAttribute('aria-sort', col === sort ? (dir === 'asc' ? 'ascending' : 'descending') : 'none');
            var iconAsc = th.querySelector('.sort-icon-asc');
            var iconDesc = th.querySelector('.sort-icon-desc');
            if (iconAsc) iconAsc.style.display = (col === sort && dir === 'asc') ? 'inline' : 'none';
            if (iconDesc) iconDesc.style.display = (col === sort && dir === 'desc') ? 'inline' : 'none';
        });
    }

    function bindRowExpand() {
        var tbody = document.getElementById('trainings-tbody');
        if (!tbody) return;
        tbody.addEventListener('click', function(e) {
            var row = e.target.closest('.trainings-data-row');
            var expandBtn = e.target.closest('.expand-row');
            if (expandBtn) {
                e.preventDefault();
                e.stopPropagation();
                var idx = expandBtn.getAttribute('data-index');
                var dataRow = tbody.querySelector('.trainings-data-row[data-index="' + idx + '"]');
                var id = dataRow ? dataRow.getAttribute('data-id') : null;
                if (!id) return;
                var detail = tbody.querySelector('.row-detail[data-detail-for="' + id + '"]');
                if (detail) {
                    detail.classList.toggle('d-none');
                    detail.setAttribute('aria-hidden', detail.classList.contains('d-none'));
                    var icon = expandBtn.querySelector('i.bi');
                    if (icon) icon.className = detail.classList.contains('d-none') ? 'bi bi-chevron-right' : 'bi bi-chevron-down';
                }
                return;
            }
            if (row && !e.target.closest('.edit-attendance') && !e.target.closest('.remove-attendance')) {
                var id = row.getAttribute('data-id');
                var detail = tbody.querySelector('.row-detail[data-detail-for="' + id + '"]');
                var expandBtnInRow = row.querySelector('.expand-row');
                if (detail) {
                    detail.classList.toggle('d-none');
                    detail.setAttribute('aria-hidden', detail.classList.contains('d-none'));
                    if (expandBtnInRow) {
                        var icon = expandBtnInRow.querySelector('i.bi');
                        if (icon) icon.className = detail.classList.contains('d-none') ? 'bi bi-chevron-right' : 'bi bi-chevron-down';
                    }
                }
                document.querySelectorAll('.trainings-table tbody tr.selected').forEach(function(r) { r.classList.remove('selected'); });
                row.classList.add('selected');
            }
        });
    }

    // Sortable headers: add sort icons (preserve .sort-label)
    (function initSortHeaders() {
        document.querySelectorAll('.trainings-table .sortable').forEach(function(th) {
            var label = th.querySelector('.sort-label');
            var text = label ? label.textContent : th.textContent.trim();
            th.innerHTML = '<span class="sort-label">' + text + '</span> <span class="sort-icon sort-icon-asc bi bi-chevron-up" aria-hidden="true"></span><span class="sort-icon sort-icon-desc bi bi-chevron-down" aria-hidden="true"></span>';
        });
    })();

    document.querySelector('.trainings-table-wrap') && document.querySelector('.trainings-table-wrap').addEventListener('click', function(e) {
        var th = e.target.closest('th.sortable');
        if (!th) return;
        e.preventDefault();
        var col = th.getAttribute('data-sort');
        if (!col) return;
        window._trainingsSort = col;
        window._trainingsDirection = (window._trainingsDirection === 'asc' && window._trainingsSort === col) ? 'desc' : 'asc';
        window._trainingsPage = 1;
        loadMyTrainings(true);
    });

    // Search, filters, per_page: debounced or on change
    var searchTimeout = null;
    document.getElementById('trainings-search') && document.getElementById('trainings-search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { window._trainingsPage = 1; loadMyTrainings(true); }, 350);
    });
    // Dropdown filters use change event
    ['filter-type_of_ld', 'filter-year'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', function() { window._trainingsPage = 1; loadMyTrainings(true); });
    });
    // Number inputs use input event with debounce for better UX
    var minHoursTimeout = null;
    var maxHoursTimeout = null;
    var minHoursEl = document.getElementById('filter-min-hrs');
    var maxHoursEl = document.getElementById('filter-max-hrs');
    
    if (minHoursEl) {
        minHoursEl.addEventListener('input', function() {
            clearTimeout(minHoursTimeout);
            var val = this.value;
            // Allow empty value to clear filter immediately
            if (val === '') {
                window._trainingsPage = 1;
                loadMyTrainings(true);
                return;
            }
            // Debounce for actual number input
            minHoursTimeout = setTimeout(function() { 
                window._trainingsPage = 1; 
                loadMyTrainings(true); 
            }, 400);
        });
        // Also trigger on Enter key
        minHoursEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(minHoursTimeout);
                window._trainingsPage = 1;
                loadMyTrainings(true);
            }
        });
    }
    
    if (maxHoursEl) {
        maxHoursEl.addEventListener('input', function() {
            clearTimeout(maxHoursTimeout);
            var val = this.value;
            // Allow empty value to clear filter immediately
            if (val === '') {
                window._trainingsPage = 1;
                loadMyTrainings(true);
                return;
            }
            // Debounce for actual number input
            maxHoursTimeout = setTimeout(function() { 
                window._trainingsPage = 1; 
                loadMyTrainings(true); 
            }, 400);
        });
        // Also trigger on Enter key
        maxHoursEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(maxHoursTimeout);
                window._trainingsPage = 1;
                loadMyTrainings(true);
            }
        });
    }
    document.getElementById('trainings-per-page') && document.getElementById('trainings-per-page').addEventListener('change', function() {
        window._trainingsPage = 1;
        var per = document.getElementById('trainings-cards-per-page');
        if (per) per.value = this.value;
        loadMyTrainings(true);
    });
    document.getElementById('trainings-cards-per-page') && document.getElementById('trainings-cards-per-page').addEventListener('change', function() {
        window._trainingsPage = 1;
        var per = document.getElementById('trainings-per-page');
        if (per) per.value = this.value;
        loadMyTrainings(true);
    });

    // Edit & Delete: delegate on document so cards and table both work
    document.body.addEventListener('click', function(e) {
        var editBtn = e.target.closest('.edit-attendance');
        if (editBtn) {
            e.preventDefault();
            e.stopPropagation();
            var id = editBtn.getAttribute('data-id');
            var title = editBtn.getAttribute('data-title') || 'Training';
            var attended = editBtn.getAttribute('data-attended') || '';
            var remarks = editBtn.getAttribute('data-remarks') || '';
            document.getElementById('edit-attendance-training-id').value = id || '';
            document.getElementById('edit-attendance-training-title').textContent = title;
            document.getElementById('edit-attendance-date').value = attended;
            document.getElementById('edit-attendance-remarks').value = remarks;
            var modal = document.getElementById('modalEditAttendance');
            if (modal && typeof bootstrap !== 'undefined') bootstrap.Modal.getOrCreateInstance(modal).show();
            return;
        }
        var removeBtn = e.target.closest('.remove-attendance');
        if (removeBtn) {
            e.preventDefault();
            e.stopPropagation();
            var id = removeBtn.getAttribute('data-id');
            var title = removeBtn.getAttribute('data-title') || 'this training';
            window._deleteTrainingId = id;
            document.getElementById('delete-training-title').textContent = title;
            var modal = document.getElementById('modalDeleteTraining');
            if (modal && typeof bootstrap !== 'undefined') bootstrap.Modal.getOrCreateInstance(modal).show();
        }
    });

    document.getElementById('btn-confirm-delete-training') && document.getElementById('btn-confirm-delete-training').addEventListener('click', function() {
        var id = window._deleteTrainingId;
        if (!id) return;
        this.disabled = true;
        var self = this;
        fetch('/api/my/record/trainings/' + id, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
        .then(function(r) { return r.json().catch(function() { return {}; }).then(function(data) { return { res: r, data: data }; }); })
        .then(function(o) {
            if (!o.res.ok) throw new Error(o.data.message || 'Failed to remove');
            if (typeof bootstrap !== 'undefined') bootstrap.Modal.getInstance(document.getElementById('modalDeleteTraining')).hide();
            loadMyTrainings(true);
            // Show success message with SweetAlert2
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Removed',
                    text: 'Removed from your record.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK',
                    timer: 4000,
                    timerProgressBar: true
                });
            } else {
                alert('Removed from your record.');
            }
        })
        .catch(function(err) {
            // Show error message with SweetAlert2
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Could not remove.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            } else {
                alert(err.message || 'Could not remove.');
            }
        })
        .finally(function() { self.disabled = false; });
    });

    document.getElementById('form-edit-attendance') && document.getElementById('form-edit-attendance').addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('edit-attendance-training-id').value;
        if (!id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Missing training.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Missing training.');
            }
            return;
        }
        var form = e.target;
        var payload = { attended_date: (form.attended_date && form.attended_date.value) || null, remarks: (form.remarks && form.remarks.value ? form.remarks.value.trim() : null) || null };
        var btn = document.getElementById('btn-save-attendance');
        if (btn) btn.disabled = true;
        fetch('/api/my/record/trainings/' + id, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        })
        .then(function(r) { return r.json().catch(function() { return {}; }).then(function(data) { return { res: r, data: data }; }); })
        .then(function(o) {
            if (!o.res.ok) throw new Error(o.data.message || 'Failed to update');
            bootstrap.Modal.getInstance(document.getElementById('modalEditAttendance')).hide();
            loadMyTrainings(true);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Attendance updated.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK',
                    timer: 4000,
                    timerProgressBar: true
                });
            } else {
                alert('Attendance updated.');
            }
        })
        .catch(function(err) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Could not update.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            } else {
                alert(err.message || 'Could not update.');
            }
        })
        .finally(function() { if (btn) btn.disabled = false; });
    });

    // Add training modal: load existing dropdown
    var modalAddMy = document.getElementById('modalAddMyTraining');
    if (modalAddMy) {
        modalAddMy.addEventListener('show.bs.modal', async function() {
            var sel = document.querySelector('#form-attach-existing select[name="training_id"]');
            if (!sel) return;
            sel.innerHTML = '<option value="">— Select —</option>';
            try {
                var r = await fetch('/api/my/trainings', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                var json = await r.json();
                (json.data || []).forEach(function(t) {
                    var opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.title + (t.start_date ? ' (' + formatDateDisplay(t.start_date) + ')' : '');
                    sel.appendChild(opt);
                });
            } catch (err) { console.error(err); }
        });
    }

    document.getElementById('form-attach-existing') && document.getElementById('form-attach-existing').addEventListener('submit', async function(e) {
        e.preventDefault();
        var btn = document.getElementById('btn-attach-existing');
        var form = e.target;
        btn.disabled = true;
        try {
            var res = await fetch('/api/my/trainings/attach', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ training_id: parseInt(form.training_id.value, 10), attended_date: form.attended_date.value || null, remarks: form.remarks.value || null })
            });
            var data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed to add');
            bootstrap.Modal.getInstance(document.getElementById('modalAddMyTraining')).hide();
            form.reset();
            loadMyTrainings(true);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Added',
                    text: 'Training added to your record.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK',
                    timer: 4000,
                    timerProgressBar: true
                });
            } else {
                alert('Training added to your record.');
            }
        } catch (err) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Could not add training.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            } else {
                alert(err.message || 'Could not add training.');
            }
        }
        finally { btn.disabled = false; }
    });

    var personnelTypeOfLd = document.querySelector('#form-add-new-training [name="type_of_ld"]');
    if (personnelTypeOfLd) personnelTypeOfLd.addEventListener('change', function() {
        var wrap = document.querySelector('#modalAddMyTraining .type-of-ld-other-wrap');
        if (wrap) wrap.classList.toggle('d-none', this.value !== 'Other');
    });

    document.getElementById('form-add-new-training') && document.getElementById('form-add-new-training').addEventListener('submit', async function(e) {
        e.preventDefault();
        var btn = document.getElementById('btn-add-new-training');
        var form = e.target;
        btn.disabled = true;
        try {
            var res = await fetch('/api/my/trainings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    title: form.title.value.trim(),
                    type_of_ld: form.type_of_ld && form.type_of_ld.value || null,
                    type_of_ld_specify: form.type_of_ld_specify && form.type_of_ld_specify.value.trim() || null,
                    provider: form.provider.value.trim() || null,
                    venue: form.venue.value.trim() || null,
                    start_date: form.start_date.value,
                    end_date: form.end_date.value,
                    hours: form.hours.value ? parseInt(form.hours.value, 10) : null,
                    description: form.description.value.trim() || null,
                    attended_date: form.attended_date.value || null,
                    remarks: form.remarks.value.trim() || null
                })
            });
            var data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed to add');
            bootstrap.Modal.getInstance(document.getElementById('modalAddMyTraining')).hide();
            form.reset();
            loadMyTrainings(true);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Added',
                    text: 'Training added to your record.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK',
                    timer: 4000,
                    timerProgressBar: true
                });
            } else {
                alert('Training added to your record.');
            }
        } catch (err) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Could not add training.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            } else {
                alert(err.message || 'Could not add training.');
            }
        }
        finally { btn.disabled = false; }
    });

    // Clear all filters button
    document.getElementById('btn-clear-filters') && document.getElementById('btn-clear-filters').addEventListener('click', function() {
        var searchEl = document.getElementById('trainings-search');
        var typeEl = document.getElementById('filter-type_of_ld');
        var yearEl = document.getElementById('filter-year');
        var minEl = document.getElementById('filter-min-hrs');
        var maxEl = document.getElementById('filter-max-hrs');

        if (searchEl) searchEl.value = '';
        if (typeEl) typeEl.value = '';
        if (yearEl) yearEl.value = '';
        if (minEl) minEl.value = '';
        if (maxEl) maxEl.value = '';

        window._trainingsPage = 1;
        window._trainingsSort = 'start_date';
        window._trainingsDirection = 'desc';

        // Clear URL parameters
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, '', window.location.pathname);
        }

        loadMyTrainings(true);
    });

    // Import Excel functionality
    document.getElementById('modalImportExcel') && document.getElementById('modalImportExcel').addEventListener('show.bs.modal', function() {
        document.getElementById('import-excel-file').value = '';
        document.getElementById('import-excel-errors').classList.add('d-none');
    });

    document.getElementById('btn-import-excel') && document.getElementById('btn-import-excel').addEventListener('click', async function() {
        var fileInput = document.getElementById('import-excel-file');
        var btn = document.getElementById('btn-import-excel');
        var btnText = btn.querySelector('.btn-text');
        var spinner = btn.querySelector('.spinner-border');
        var errEl = document.getElementById('import-excel-errors');

        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'No File Selected',
                    text: 'Please select an Excel file.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Please select an Excel file.');
            }
            return;
        }

        errEl.classList.add('d-none');
        btn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');

        var formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('_token', csrfToken);

        try {
            var r = await fetch('/api/my/trainings/import', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
                body: formData
            });
            var json = await r.json().catch(function() { return {}; });
            if (r.ok) {
                bootstrap.Modal.getInstance(document.getElementById('modalImportExcel')).hide();
                loadMyTrainings(true);
                // Show success message with SweetAlert2
                if (typeof Swal !== 'undefined' && json.imported > 0) {
                    const userName = json.user_name || 'User';
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Successful',
                        text: `Successfully imported ${json.imported} training record(s) for ${userName}.`,
                        confirmButtonColor: 'var(--deped-primary)',
                        confirmButtonText: 'OK',
                        timer: 4000,
                        timerProgressBar: true
                    });
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Successful',
                            text: json.message || 'Import completed.',
                            confirmButtonColor: 'var(--deped-primary)',
                            confirmButtonText: 'OK',
                            timer: 4000,
                            timerProgressBar: true
                        });
                    } else {
                        alert(json.message || 'Import completed.');
                    }
                }
            } else {
                var msg = json.message || 'Import failed.';
                var errList = json.errors ? (Array.isArray(json.errors) ? json.errors.join('\n') : Object.values(json.errors).flat().join('\n')) : '';
                errEl.textContent = errList || msg;
                errEl.classList.remove('d-none');
                // Show error message with SweetAlert2
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Failed',
                        text: msg + (errList ? '\n\n' + errList : ''),
                        confirmButtonColor: 'var(--deped-primary)',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert(msg + (errList ? '\n\n' + errList : ''));
                }
            }
        } catch (e) {
            // Show error message with SweetAlert2
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: e.message || 'Please try again.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Error: ' + (e.message || 'Please try again.'));
            }
        } finally {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    });

    window.refreshMyTrainings = loadMyTrainings;
    loadMyTrainings();
})();
</script>
@endpush
@endsection
