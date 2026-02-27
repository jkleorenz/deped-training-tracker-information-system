@extends('layouts.app')

@section('title', 'Manage Trainings - ' . config('app.name'))

@push('styles')
<style>
/* Admin trainings table: same as personnel dashboard */
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
.trainings-table .col-actions { width: 7rem; }
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
.trainings-pagination-wrap { flex-wrap: wrap; }
.trainings-pagination { display: flex; flex-wrap: wrap; gap: 0.25rem; justify-content: flex-end; align-items: center; }
.trainings-pagination .page-link { padding: 0.35rem 0.6rem; font-size: 0.875rem; color: var(--deped-primary) !important; }
.trainings-pagination .page-link:hover { color: var(--deped-primary) !important; background-color: var(--deped-accent) !important; }
.trainings-pagination .page-item.active .page-link { background-color: var(--deped-primary) !important; border-color: var(--deped-primary) !important; color: #fff !important; }
@media (max-width: 767.98px) {
    .trainings-table-wrap { display: none !important; }
    .trainings-cards-wrap.d-none { display: none !important; }
    .trainings-cards-wrap:not(.d-none) { display: block !important; }
}
@media (min-width: 768px) {
    .trainings-cards-wrap { display: none !important; }
}
.trainings-card-mobile { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
.trainings-card-mobile .card-mobile-title { font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
.trainings-card-mobile .card-mobile-row { display: flex; gap: 0.5rem; font-size: 0.8125rem; margin-bottom: 0.25rem; }
.trainings-card-mobile .card-mobile-label { color: #64748b; min-width: 5rem; }
.trainings-card-mobile .card-mobile-actions { margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #eee; display: flex; gap: 0.5rem; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title mb-1">Manage Trainings & Seminars</h4>
        <p class="text-muted small mb-0">Add, edit, and assign personnel to trainings.</p>
    </div>
    <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalImport"><i class="bi bi-upload me-1"></i> Import from Excel</button>
            <button type="button" class="btn btn-deped" data-bs-toggle="modal" data-bs-target="#modalAddTraining"><i class="bi bi-plus-lg me-1"></i> Add Training</button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
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
            <p class="mb-2">No trainings yet.</p>
            <button type="button" class="btn btn-deped" data-bs-toggle="modal" data-bs-target="#modalAddTraining"><i class="bi bi-plus-lg"></i> Add Training</button>
        </div>

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

<div class="modal fade" id="modalAddTraining" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Training / Seminar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-add-training">
                    <p class="small fw-semibold text-muted mb-2">Basics</p>
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CONDUCTED/ SPONSORED BY (Write in full)</label>
                        <input type="text" name="provider" class="form-control" maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type of L&amp;D</label>
                        <select name="type_of_ld" class="form-select">
                            <option value="">— Select —</option>
                            <option value="Managerial">Managerial</option>
                            <option value="Supervisory">Supervisory</option>
                            <option value="Technical">Technical</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3 type-of-ld-other-wrap d-none">
                        <label class="form-label">If Other, please specify</label>
                        <input type="text" name="type_of_ld_specify" class="form-control" maxlength="100" placeholder="Specify type">
                    </div>
                    <p class="small fw-semibold text-muted mb-2">When & where</p>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Venue</label>
                        <input type="text" name="venue" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hours</label>
                        <input type="number" name="hours" class="form-control" min="0">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-deped" id="btn-save-training">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditTraining" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Training / Seminar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-training">
                    <input type="hidden" name="id" id="edit-training-id">
                    <p class="small fw-semibold text-muted mb-2">Basics</p>
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CONDUCTED/ SPONSORED BY (Write in full)</label>
                        <input type="text" name="provider" id="edit-provider" class="form-control" maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type of L&amp;D</label>
                        <select name="type_of_ld" id="edit-type_of_ld" class="form-select">
                            <option value="">— Select —</option>
                            <option value="Managerial">Managerial</option>
                            <option value="Supervisory">Supervisory</option>
                            <option value="Technical">Technical</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3 type-of-ld-other-wrap-edit d-none">
                        <label class="form-label">If Other, please specify</label>
                        <input type="text" name="type_of_ld_specify" id="edit-type_of_ld_specify" class="form-control" maxlength="100" placeholder="Specify type">
                    </div>
                    <p class="small fw-semibold text-muted mb-2">When & where</p>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="edit-start_date" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="edit-end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Venue</label>
                        <input type="text" name="venue" id="edit-venue" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hours</label>
                        <input type="number" name="hours" id="edit-hours" class="form-control" min="0">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit-description" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-deped" id="btn-update-training">Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Trainings from Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Upload an Excel file with columns: Title, Type of L&D, Provider, Venue, Start Date, End Date, Hours, Attended Date. All rows will be imported and assigned to the selected user(s).</p>
                <div class="mb-3">
                    <label class="form-label">Select user(s) <span class="text-danger">*</span></label>
                    <select id="import-user-ids" class="form-select" multiple size="8">
                    </select>
                    <small class="text-muted">Hold Ctrl (or Cmd on Mac) to select multiple.</small>
                </div>
                <div class="mb-2">
                    <label class="form-label">Excel file (.xlsx, .xls) <span class="text-danger">*</span></label>
                    <input type="file" id="import-file" class="form-control" accept=".xlsx,.xls" required>
                    <div id="import-errors" class="invalid-feedback d-none"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-deped" id="btn-do-import"><span class="btn-text">Import</span><span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAssign" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Personnel to Training</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted" id="assign-training-title"></p>
                <label class="form-label">Select personnel (hold Ctrl for multiple)</label>
                <select id="assign-user-ids" class="form-select" multiple size="8"></select>
                <div class="mt-2">
                    <label class="form-label">Attended Date (optional)</label>
                    <input type="date" id="assign-attended-date" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-deped" id="btn-do-assign">Assign</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeleteTraining" tabindex="-1" aria-labelledby="modalDeleteTrainingTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteTrainingTitle">Delete Training</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Delete "<span id="delete-training-title"></span>"? This will remove the training and all personnel assignments. This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete-training"><i class="bi bi-trash me-1"></i> Delete</button>
            </div>
        </div>
    </div>
</div>

@php
$canDeleteTraining = auth()->user()->isAdmin();
@endphp
@push('scripts')
<script>
window._canDeleteTraining = {!! json_encode($canDeleteTraining) !!};
(function() {
    const baseUrl = '{{ url("/") }}';
    const meta = document.querySelector('meta[name="csrf-token"]');
    const token = meta ? meta.getAttribute('content') : '';
    let assignTrainingId = null;
    let loadAbort = null;

    function headers() {
        return { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' };
    }

    const DATE_RE = /^(\d{4})-(\d{2})-(\d{2})$/;
    function formatDateDisplay(isoStr) {
        if (!isoStr) return '—';
        var m = String(isoStr).match(DATE_RE);
        if (!m) return isoStr;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return (parseInt(m[3], 10)) + ' ' + months[parseInt(m[2], 10) - 1] + ' ' + m[1];
    }

    function escapeHtml(s) {
        if (s == null || s === '') return '—';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
    function escapeAttr(s) {
        if (s == null) return '';
        return String(s).replace(/"/g, '&quot;');
    }
    function typeOfLdLabel(typeOfLd, specify) {
        if (!typeOfLd) return '—';
        var base = String(typeOfLd).charAt(0).toUpperCase() + String(typeOfLd).slice(1);
        if (specify && String(specify).trim()) base += ' (' + String(specify).trim() + ')';
        return base;
    }

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
        var path = window.location.pathname || '';
        var qs = buildQueryString(params);
        if (window.history && window.history.replaceState) window.history.replaceState({}, '', path + (qs || ''));
    }

    function readParamsFromUrl() {
        var path = window.location.pathname || '';
        var search = window.location.search;
        var params = {};
        if (search) {
            search.slice(1).split('&').forEach(function(pair) {
                var i = pair.indexOf('=');
                if (i > 0) { params[decodeURIComponent(pair.slice(0, i))] = decodeURIComponent(pair.slice(i + 1)); }
            });
        }
        if (params.page) window._trainingsPage = Math.max(1, parseInt(params.page, 10));
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
        // Handle per_page: if not in URL, default to 10; only update if not focused
        var perPageValue = params.per_page || '10';
        if (perEl && document.activeElement !== perEl) perEl.value = perPageValue;
        if (perCards && document.activeElement !== perCards) perCards.value = perPageValue;
    }

    async function loadTrainings(skipReadUrl) {
        if (!skipReadUrl) readParamsFromUrl();
        var params = getQueryParams();
        var loading = document.getElementById('trainings-loading');
        var empty = document.getElementById('trainings-empty');
        var wrap = document.getElementById('trainings-wrap');
        var cardsWrap = document.getElementById('trainings-cards-wrap');

        function showLoading() {
            if (loading) { loading.classList.remove('d-none'); var p = loading.querySelector('p'); if (p) p.textContent = 'Loading…'; }
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
            var r = await fetch(baseUrl + '/api/trainings' + qs, {
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
            // Update page number from server response to ensure consistency
            // This is important when the requested page doesn't exist (e.g., last page changed)
            window._trainingsPage = meta.current_page || 1;

            syncUrl(getQueryParams());

            if (data.length === 0 && (meta.total === undefined || meta.total === 0)) {
                if (empty) { empty.classList.remove('d-none'); var ep = empty.querySelector('p'); if (ep) ep.textContent = 'No trainings yet.'; }
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
            var desc = (t && t.description) ? String(t.description) : '';
            rows.push('<tr class="trainings-data-row" data-id="' + (t && t.id) + '" data-index="' + idx + '" tabindex="0" role="button">');
            rows.push('<td class="col-expand"><button type="button" class="btn btn-link btn-sm p-0 text-secondary expand-row" aria-label="Expand row" data-index="' + idx + '"><i class="bi bi-chevron-right"></i></button></td>');
            rows.push('<td class="col-title"><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(title) + '">' + escapeHtml(titleDisplay) + '</span></td>');
            rows.push('<td class="col-type">' + escapeHtml(typeOfLd) + '</td>');
            rows.push('<td class="col-provider"><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(provider) + '">' + escapeHtml(provider) + '</span></td>');
            rows.push('<td class="col-venue"><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(venue) + '">' + escapeHtml(venue) + '</span></td>');
            rows.push('<td class="col-duration">' + escapeHtml(duration) + '</td>');
            rows.push('<td class="col-hrs text-end">' + (typeof hrs === 'number' ? hrs : escapeHtml(hrs)) + '</td>');
            rows.push('<td class="col-actions text-end"><div class="d-flex justify-content-end gap-1">');
            rows.push('<button type="button" class="btn btn-outline-secondary btn-sm btn-action-icon edit-btn" data-id="' + (t && t.id) + '" data-title="' + escapeHtml(title) + '" title="Edit" aria-label="Edit"><i class="bi bi-pencil-square"></i></button>');
            rows.push('<button type="button" class="btn btn-outline-secondary btn-sm btn-action-icon assign-btn" data-id="' + (t && t.id) + '" data-title="' + escapeAttr(title) + '" title="Assign personnel" aria-label="Assign"><i class="bi bi-person-plus"></i></button>');
            if (window._canDeleteTraining) rows.push('<button type="button" class="btn btn-outline-secondary btn-sm btn-action-icon btn-delete delete-btn" data-id="' + (t && t.id) + '" data-title="' + escapeAttr(title) + '" title="Delete training" aria-label="Delete"><i class="bi bi-trash"></i></button>');
            rows.push('</div></td></tr>');
            rows.push('<tr class="row-detail d-none" data-detail-for="' + (t && t.id) + '" aria-hidden="true"><td colspan="8"><div class="detail-grid"><span class="detail-label">Title</span><span>' + escapeHtml(title) + '</span><span class="detail-label">Type of L&D</span><span>' + escapeHtml(typeOfLd) + '</span><span class="detail-label">Provider</span><span>' + escapeHtml(provider) + '</span><span class="detail-label">Venue</span><span>' + escapeHtml(venue) + '</span><span class="detail-label">Start</span><span>' + (t.start_date_display || formatDateDisplay(t.start_date)) + '</span><span class="detail-label">End</span><span>' + (t.end_date_display || formatDateDisplay(t.end_date)) + '</span><span class="detail-label">Hours</span><span>' + (t.hours != null ? t.hours : '—') + '</span>' + (desc ? '<span class="detail-label">Description</span><span>' + escapeHtml(desc) + '</span>' : '') + '</div></td></tr>');
        });
        tbody.innerHTML = rows.join('');
        wrap.classList.remove('d-none');
        if (window.innerWidth < 768) {
            wrap.classList.add('d-none');
            if (cardsWrap) document.getElementById('trainings-cards-wrap').classList.remove('d-none');
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
            return '<div class="trainings-card-mobile" data-id="' + (t && t.id) + '">' +
                '<div class="card-mobile-title">' + escapeHtml(title) + '</div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Type of L&D</span><span>' + escapeHtml(typeOfLd) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Provider</span><span>' + escapeHtml(t && t.provider) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Venue</span><span>' + escapeHtml(t && t.venue) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Duration</span><span>' + escapeHtml(duration) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Hrs</span><span>' + (t.hours != null ? t.hours : '—') + '</span></div>' +
                '<div class="card-mobile-actions">' +
                '<button type="button" class="btn btn-outline-secondary btn-sm edit-btn" data-id="' + (t && t.id) + '" data-title="' + escapeHtml(title) + '" title="Edit"><i class="bi bi-pencil-square me-1"></i>Edit</button>' +
                '<button type="button" class="btn btn-outline-secondary btn-sm assign-btn" data-id="' + (t && t.id) + '" data-title="' + escapeAttr(title) + '" title="Assign personnel"><i class="bi bi-person-plus me-1"></i>Assign</button>' +
                (window._canDeleteTraining ? '<button type="button" class="btn btn-outline-secondary btn-sm btn-delete delete-btn" data-id="' + (t && t.id) + '" data-title="' + escapeAttr(title) + '" title="Delete"><i class="bi bi-trash me-1"></i>Delete</button>' : '') +
                '</div></div>';
        }).join('');
        if (window.innerWidth < 768 && data.length > 0) wrap.classList.remove('d-none');
    }

    function renderPagination(meta) {
        var total = meta.total || 0, from = meta.from || 0, to = meta.to || 0, current = meta.current_page || 1, last = meta.last_page || 1;
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
            function addPage(num) {
                if (num < 1 || num > last) return;
                var li = document.createElement('li');
                li.className = 'page-item' + (num === current ? ' active' : '');
                var a = document.createElement('a');
                a.href = '#';
                a.className = 'page-link';
                a.textContent = num;
                a.setAttribute('aria-label', 'Page ' + num);
                a.addEventListener('click', function(e) { e.preventDefault(); window._trainingsPage = num; loadTrainings(true); });
                li.appendChild(a);
                ul.appendChild(li);
            }
            addPage(1);
            if (current > 3) { var ell = document.createElement('li'); ell.className = 'page-item disabled'; ell.innerHTML = '<span class="page-link">…</span>'; ul.appendChild(ell); }
            if (current > 2) addPage(current - 1);
            if (current > 1 && current < last) addPage(current);
            if (current < last - 1) addPage(current + 1);
            if (current < last - 2) { var ell2 = document.createElement('li'); ell2.className = 'page-item disabled'; ell2.innerHTML = '<span class="page-link">…</span>'; ul.appendChild(ell2); }
            if (last > 1) addPage(last);
            nav.appendChild(ul);
        }
        makePageNav('trainings-pagination');
        makePageNav('trainings-cards-pagination');
    }

    function updateSortIndicators() {
        var sort = window._trainingsSort || 'start_date', dir = window._trainingsDirection || 'desc';
        document.querySelectorAll('.trainings-table .sortable').forEach(function(th) {
            var col = th.getAttribute('data-sort');
            th.setAttribute('aria-sort', col === sort ? (dir === 'asc' ? 'ascending' : 'descending') : 'none');
            var iconAsc = th.querySelector('.sort-icon-asc'); var iconDesc = th.querySelector('.sort-icon-desc');
            if (iconAsc) iconAsc.style.display = (col === sort && dir === 'asc') ? 'inline' : 'none';
            if (iconDesc) iconDesc.style.display = (col === sort && dir === 'desc') ? 'inline' : 'none';
        });
    }

    function bindRowExpand() {
        var tbody = document.getElementById('trainings-tbody');
        if (!tbody) return;
        tbody.addEventListener('click', function(e) {
            var expandBtn = e.target.closest('.expand-row');
            if (expandBtn) {
                e.preventDefault(); e.stopPropagation();
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
            var row = e.target.closest('.trainings-data-row');
            if (row && !e.target.closest('.edit-btn') && !e.target.closest('.assign-btn') && !e.target.closest('.delete-btn')) {
                var id = row.getAttribute('data-id');
                var detail = tbody.querySelector('.row-detail[data-detail-for="' + id + '"]');
                var expandBtnInRow = row.querySelector('.expand-row');
                if (detail) {
                    detail.classList.toggle('d-none');
                    detail.setAttribute('aria-hidden', detail.classList.contains('d-none'));
                    if (expandBtnInRow) { var icon = expandBtnInRow.querySelector('i.bi'); if (icon) icon.className = detail.classList.contains('d-none') ? 'bi bi-chevron-right' : 'bi bi-chevron-down'; }
                }
                document.querySelectorAll('.trainings-table tbody tr.selected').forEach(function(r) { r.classList.remove('selected'); });
                row.classList.add('selected');
            }
        });
    }

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
        loadTrainings();
    });

    var searchTimeout = null;
    document.getElementById('trainings-search') && document.getElementById('trainings-search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { window._trainingsPage = 1; loadTrainings(); }, 350);
    });
    // Dropdown filters use change event
    ['filter-type_of_ld', 'filter-year'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', function() { window._trainingsPage = 1; loadTrainings(); });
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
                loadTrainings();
                return;
            }
            // Debounce for actual number input
            minHoursTimeout = setTimeout(function() { 
                window._trainingsPage = 1; 
                loadTrainings(); 
            }, 400);
        });
        // Also trigger on Enter key
        minHoursEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(minHoursTimeout);
                window._trainingsPage = 1;
                loadTrainings();
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
                loadTrainings();
                return;
            }
            // Debounce for actual number input
            maxHoursTimeout = setTimeout(function() { 
                window._trainingsPage = 1; 
                loadTrainings(); 
            }, 400);
        });
        // Also trigger on Enter key
        maxHoursEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(maxHoursTimeout);
                window._trainingsPage = 1;
                loadTrainings();
            }
        });
    }
    // Clear all filters button
    document.getElementById('btn-clear-filters') && document.getElementById('btn-clear-filters').addEventListener('click', function() {
        var searchEl = document.getElementById('trainings-search');
        var typeOfLdEl = document.getElementById('filter-type_of_ld');
        var yearEl = document.getElementById('filter-year');
        var minEl = document.getElementById('filter-min-hrs');
        var maxEl = document.getElementById('filter-max-hrs');
        if (searchEl) searchEl.value = '';
        if (typeOfLdEl) typeOfLdEl.value = '';
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
        loadTrainings();
    });
    document.getElementById('trainings-per-page') && document.getElementById('trainings-per-page').addEventListener('change', function() {
        window._trainingsPage = 1;
        var per = document.getElementById('trainings-cards-per-page');
        if (per && per.value !== this.value) per.value = this.value;
        loadTrainings(true); // skip reading from URL to preserve user selection
    });
    document.getElementById('trainings-cards-per-page') && document.getElementById('trainings-cards-per-page').addEventListener('change', function() {
        window._trainingsPage = 1;
        var per = document.getElementById('trainings-per-page');
        if (per && per.value !== this.value) per.value = this.value;
        loadTrainings(true); // skip reading from URL to preserve user selection
    });

    document.body.addEventListener('click', async function(e) {
        var editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            e.preventDefault(); e.stopPropagation();
            var id = editBtn.getAttribute('data-id');
            try {
                var r = await fetch(baseUrl + '/api/trainings/' + id, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) throw new Error('Failed');
                var json = await r.json();
                var t = json.data;
                document.getElementById('edit-training-id').value = t.id;
                document.getElementById('edit-title').value = t.title || '';
                document.getElementById('edit-provider').value = t.provider || '';
                var editTypeOfLd = document.getElementById('edit-type_of_ld');
                var editTypeOfLdSpecify = document.getElementById('edit-type_of_ld_specify');
                var editOtherWrap = document.querySelector('.type-of-ld-other-wrap-edit');
                if (editTypeOfLd) {
                    var typeOfLdVal = t.type_of_ld || '';
                    if (['Managerial', 'Supervisory', 'Technical', 'Other'].indexOf(typeOfLdVal) >= 0) {
                        editTypeOfLd.value = typeOfLdVal;
                        if (editTypeOfLdSpecify) editTypeOfLdSpecify.value = t.type_of_ld_specify || '';
                    } else {
                        editTypeOfLd.value = typeOfLdVal ? 'Other' : '';
                        if (editTypeOfLdSpecify) editTypeOfLdSpecify.value = typeOfLdVal || t.type_of_ld_specify || '';
                    }
                    if (editOtherWrap) editOtherWrap.classList.toggle('d-none', editTypeOfLd.value !== 'Other');
                }
                document.getElementById('edit-venue').value = t.venue || '';
                document.getElementById('edit-start_date').value = t.start_date ? String(t.start_date).slice(0, 10) : '';
                document.getElementById('edit-end_date').value = t.end_date ? String(t.end_date).slice(0, 10) : '';
                document.getElementById('edit-hours').value = t.hours != null ? t.hours : '';
                document.getElementById('edit-description').value = t.description || '';
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditTraining')).show();
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error loading training.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            }
            return;
        }
        var assignBtn = e.target.closest('.assign-btn');
        if (assignBtn) {
            e.preventDefault(); e.stopPropagation();
            assignTrainingId = assignBtn.getAttribute('data-id');
            document.getElementById('assign-training-title').textContent = assignBtn.getAttribute('data-title') || '';
            var personnel = await fetch(baseUrl + '/api/personnel', { headers: { 'Accept': 'application/json' } }).then(function(r) { return r.json(); }).then(function(j) { return j.data || []; });
            var sel = document.getElementById('assign-user-ids');
            sel.innerHTML = personnel.map(function(p) { return '<option value="' + p.id + '">' + escapeHtml(p.name) + ' (' + escapeHtml(p.employee_id || p.email) + ')</option>'; }).join('');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAssign')).show();
            return;
        }
        var deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            e.preventDefault(); e.stopPropagation();
            window._deleteTrainingId = deleteBtn.getAttribute('data-id');
            document.getElementById('delete-training-title').textContent = deleteBtn.getAttribute('data-title') || 'this training';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDeleteTraining')).show();
        }
    });

    document.getElementById('btn-confirm-delete-training') && document.getElementById('btn-confirm-delete-training').addEventListener('click', function() {
        var id = window._deleteTrainingId;
        if (!id) return;
        var self = this;
        this.disabled = true;
        fetch(baseUrl + '/api/trainings/' + id, { method: 'DELETE', headers: headers(), credentials: 'same-origin' })
            .then(function(r) { return r.json().catch(function() { return {}; }).then(function(data) { return { res: r, data: data }; }); })
            .then(function(o) {
                if (!o.res.ok) throw new Error(o.data.message || 'Failed to delete');
                bootstrap.Modal.getInstance(document.getElementById('modalDeleteTraining')).hide();
                loadTrainings();
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'Training deleted successfully.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK',
                    timer: 4000,
                    timerProgressBar: true
                });
            })
            .catch(function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Could not delete.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            })
            .finally(function() { self.disabled = false; });
    });

    document.getElementById('btn-do-assign').addEventListener('click', async function() {
        var sel = document.getElementById('assign-user-ids');
        var userIds = Array.from(sel.selectedOptions).map(function(o) { return parseInt(o.value, 10); });
        var attendedDate = document.getElementById('assign-attended-date').value || null;
        if (userIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Personnel Selected',
                text: 'Select at least one personnel.',
                confirmButtonColor: 'var(--deped-primary)',
                confirmButtonText: 'OK'
            });
            return;
        }
        try {
            var r = await fetch(baseUrl + '/api/trainings/' + assignTrainingId + '/attach', { method: 'POST', headers: headers(), body: JSON.stringify({ user_ids: userIds, attended_date: attendedDate }) });
            if (!r.ok) throw new Error('Failed');
            bootstrap.Modal.getInstance(document.getElementById('modalAssign')).hide();
            loadTrainings();
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error assigning personnel.',
                confirmButtonColor: 'var(--deped-primary)',
                confirmButtonText: 'OK'
            });
        }
    });

    document.getElementById('btn-update-training').addEventListener('click', async function() {
        var id = document.getElementById('edit-training-id').value;
        var data = { title: document.getElementById('edit-title').value, type_of_ld: document.getElementById('edit-type_of_ld').value || null, type_of_ld_specify: document.getElementById('edit-type_of_ld_specify').value || null, provider: document.getElementById('edit-provider').value || null, venue: document.getElementById('edit-venue').value || null, start_date: document.getElementById('edit-start_date').value, end_date: document.getElementById('edit-end_date').value, hours: document.getElementById('edit-hours').value ? parseInt(document.getElementById('edit-hours').value, 10) : null, description: document.getElementById('edit-description').value || null };
        try {
            var r = await fetch(baseUrl + '/api/trainings/' + id, { method: 'PUT', headers: headers(), body: JSON.stringify(data) });
            if (!r.ok) throw new Error('Failed');
            bootstrap.Modal.getInstance(document.getElementById('modalEditTraining')).hide();
            loadTrainings();
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error updating training.',
                confirmButtonColor: 'var(--deped-primary)',
                confirmButtonText: 'OK'
            });
        }
    });

    document.getElementById('btn-save-training').addEventListener('click', async function() {
        var form = document.getElementById('form-add-training');
        var data = { title: form.title.value, type_of_ld: form.type_of_ld && form.type_of_ld.value || null, type_of_ld_specify: form.type_of_ld_specify && form.type_of_ld_specify.value || null, provider: form.provider.value || null, venue: form.venue.value || null, start_date: form.start_date.value, end_date: form.end_date.value, hours: form.hours.value ? parseInt(form.hours.value, 10) : null, description: form.description.value || null };
        try {
            var r = await fetch(baseUrl + '/api/trainings', { method: 'POST', headers: headers(), body: JSON.stringify(data) });
            if (!r.ok) throw new Error('Failed');
            bootstrap.Modal.getInstance(document.getElementById('modalAddTraining')).hide();
            form.reset();
            loadTrainings();
            Swal.fire({
                icon: 'success',
                title: 'Training Added',
                text: 'New training has been added successfully.',
                confirmButtonColor: 'var(--deped-primary)',
                confirmButtonText: 'OK',
                timer: 4000,
                timerProgressBar: true
            });
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error saving training.',
                confirmButtonColor: 'var(--deped-primary)',
                confirmButtonText: 'OK'
            });
        }
    });

    var addTypeOfLd = document.querySelector('#form-add-training [name="type_of_ld"]');
    if (addTypeOfLd) addTypeOfLd.addEventListener('change', function() {
        var wrap = document.querySelector('#modalAddTraining .type-of-ld-other-wrap');
        if (wrap) wrap.classList.toggle('d-none', this.value !== 'Other');
    });
    var editTypeOfLd = document.getElementById('edit-type_of_ld');
    if (editTypeOfLd) editTypeOfLd.addEventListener('change', function() {
        var wrap = document.querySelector('.type-of-ld-other-wrap-edit');
        if (wrap) wrap.classList.toggle('d-none', this.value !== 'Other');
    });

    document.getElementById('modalImport').addEventListener('show.bs.modal', async function() {
        var sel = document.getElementById('import-user-ids');
        var personnel = await fetch(baseUrl + '/api/personnel', { headers: { 'Accept': 'application/json' } }).then(function(r) { return r.json(); }).then(function(j) { return j.data || []; });
        sel.innerHTML = personnel.map(function(p) { return '<option value="' + p.id + '">' + escapeHtml(p.name) + ' (' + escapeHtml(p.employee_id || p.email) + ')</option>'; }).join('');
        document.getElementById('import-file').value = '';
        document.getElementById('import-errors').classList.add('d-none');
    });

    document.getElementById('btn-do-import').addEventListener('click', async function(ev) {
        ev.preventDefault(); ev.stopPropagation();
        var fileInput = document.getElementById('import-file');
        var importUserIdsEl = document.getElementById('import-user-ids');
        var userIds = importUserIdsEl ? Array.from(importUserIdsEl.selectedOptions).map(function(o) { return o.value; }) : [];
        var btn = document.getElementById('btn-do-import');
        var btnText = btn ? btn.querySelector('.btn-text') : null;
        var spinner = btn ? btn.querySelector('.spinner-border') : null;
        var errEl = document.getElementById('import-errors');
        if (!userIds.length) {
            Swal.fire({
                icon: 'warning',
                title: 'No Users Selected',
                text: 'Please select at least one user.',
                confirmButtonColor: 'var(--deped-primary)',
                confirmButtonText: 'OK'
            });
            return;
        }
        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            Swal.fire({
                icon: 'warning',
                title: 'No File Selected',
                text: 'Please select an Excel file.',
                confirmButtonColor: 'var(--deped-primary)',
                confirmButtonText: 'OK'
            });
            return;
        }
        if (errEl) errEl.classList.add('d-none');
        btn.disabled = true;
        if (btnText) btnText.classList.add('d-none');
        if (spinner) spinner.classList.remove('d-none');
        var formData = new FormData();
        formData.append('file', fileInput.files[0]);
        userIds.forEach(function(id) { formData.append('user_ids[]', id); });
        formData.append('_token', token);
        try {
            var r = await fetch(baseUrl + '/api/trainings/import', { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token }, body: formData });
            var json = await r.json().catch(function() { return {}; });
            if (r.ok) {
                if (document.getElementById('modalImport') && bootstrap.Modal.getInstance(document.getElementById('modalImport'))) bootstrap.Modal.getInstance(document.getElementById('modalImport')).hide();
                loadTrainings();
                // Show success message with SweetAlert2
                if (json.imported > 0) {
                    let successMessage = `Successfully imported ${json.imported} training record(s)`;
                    if (json.user_count === 1 && json.user_name) {
                        successMessage += ` for ${json.user_name}.`;
                    } else if (json.user_count > 1) {
                        successMessage += ` for ${json.user_count} user(s).`;
                    } else {
                        successMessage += '.';
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Successful',
                        text: successMessage,
                        confirmButtonColor: 'var(--deped-primary)',
                        confirmButtonText: 'OK',
                        timer: 4000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Successful',
                        text: json.message || 'Import completed.',
                        confirmButtonColor: 'var(--deped-primary)',
                        confirmButtonText: 'OK',
                        timer: 4000,
                        timerProgressBar: true
                    });
                }
            } else {
                var msg = json.message || 'Import failed.';
                var errList = json.errors ? (Array.isArray(json.errors) ? json.errors.join('\n') : Object.values(json.errors).flat().join('\n')) : '';
                if (errEl) { errEl.textContent = errList || msg; errEl.classList.remove('d-none'); }
                Swal.fire({
                    icon: 'error',
                    title: 'Import Failed',
                    text: msg + (errList ? '\n\n' + errList : ''),
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            }
        } catch (e) {
            console.error('Import error', e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error: ' + (e.message || 'Please try again.'),
                confirmButtonColor: 'var(--deped-primary)',
                confirmButtonText: 'OK'
            });
        }
        finally { btn.disabled = false; if (btnText) btnText.classList.remove('d-none'); if (spinner) spinner.classList.add('d-none'); }
    });

    loadTrainings();
})();
</script>
@endpush
@endsection
