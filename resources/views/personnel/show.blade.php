@extends('layouts.app')

@section('title', $user->name . ' - ' . config('app.name'))

@push('styles')
<style>
.personnel-profile .card-header.card-header-green { background-color: var(--deped-primary) !important; color: #fff; }
.personnel-profile .card-header.card-header-green .mb-0 { color: #fff; }
.personnel-profile .card-body { padding: 1.25rem 1.25rem; }
.personnel-profile .table td { padding: 0.75rem 0.85rem; vertical-align: middle; }
.personnel-profile .table thead th { padding: 0.75rem 0.85rem; white-space: nowrap; }
.personnel-profile .table-responsive { border-radius: 0.25rem; }
.personnel-profile .card-hover { transition: box-shadow 0.2s ease; }
.personnel-profile .card-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.personnel-profile .stat-card-icon { width: 48px; height: 48px; border-radius: 12px; background: var(--deped-accent); color: var(--deped-primary); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; }
.personnel-profile .stat-number { font-size: 1.75rem; font-weight: 700; color: var(--deped-primary); }
@media (min-width: 992px) {
    .personnel-profile .table thead th { position: sticky; top: 0; background: #f8f9fa; z-index: 1; box-shadow: 0 1px 0 0 #dee2e6; }
}
/* Action buttons: icon + text side-by-side, vertically centered */
.personnel-profile .card-body .d-flex.gap-1 .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
/* Pagination: same as Trainings page */
.personnel-profile .trainings-pagination-wrap { flex-wrap: wrap; }
.personnel-profile .trainings-pagination { display: flex; flex-wrap: wrap; gap: 0.25rem; justify-content: flex-end; align-items: center; }
.personnel-profile .trainings-pagination .page-link { padding: 0.35rem 0.6rem; font-size: 0.875rem; color: var(--deped-primary) !important; }
.personnel-profile .trainings-pagination .page-link:hover { color: var(--deped-primary) !important; background-color: var(--deped-accent) !important; }
.personnel-profile .trainings-pagination .page-item.active .page-link { background-color: var(--deped-primary) !important; border-color: var(--deped-primary) !important; color: #fff !important; }
</style>
@endpush

@section('content')
<div class="personnel-profile">
    {{-- Top card: breadcrumb, name, actions --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('personnel.index') }}">Personnel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
                        </ol>
                    </nav>
                    <h4 class="page-title mb-0">{{ $user->name }}</h4>
                </div>
                <div class="d-flex flex-wrap gap-1">
                    <a href="{{ route('reports.pdf', ['user_id' => $user->id]) }}" class="btn btn-deped btn-sm" target="_blank"><i class="bi bi-file-pdf me-1"></i> Print STA</a>
                    <a href="{{ route('reports.pds-excel', ['user_id' => $user->id]) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i> PDS Excel</a>
                    <a href="{{ route('personnel.pds.edit', $user) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil-square me-1"></i> Edit PDS</a>
                    <a href="{{ route('reports.excel', ['user_id' => $user->id]) }}" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-excel me-1"></i> STA Excel</a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isSubAdmin())
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportExcel"><i class="bi bi-upload me-1"></i> Import Excel</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Info cards: Trainings count, Employee ID, School / Office --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="stat-card-icon flex-shrink-0"><i class="bi bi-journal-check"></i></div>
                    <div class="min-w-0">
                        <h6 class="text-muted mb-1">Trainings / Seminars</h6>
                        <p class="mb-0 stat-number">{{ $user->trainings_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="stat-card-icon flex-shrink-0"><i class="bi bi-person-badge"></i></div>
                    <div class="min-w-0">
                        <h6 class="text-muted mb-1">Employee ID</h6>
                        <p class="mb-0 fw-semibold">{{ $user->employee_id ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="stat-card-icon flex-shrink-0"><i class="bi bi-building"></i></div>
                    <div class="min-w-0">
                        <h6 class="text-muted mb-1">School / Office</h6>
                        <p class="mb-0 fw-semibold">{{ $user->school ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Personnel info card (based on Personal Data Sheet + user) --}}
    <div class="card mb-3">
        <div class="card-header card-header-green py-2">
            <h6 class="mb-0">Personnel info</h6>
        </div>
        <div class="card-body">
            @include('partials.personnel-info', ['user' => $user])
        </div>
    </div>

    {{-- Seminars & trainings attended card --}}
    <div class="card">
        <div class="card-header card-header-green py-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="mb-0">Seminars & Trainings Attended</h6>
            @if((auth()->user()->isAdmin() || auth()->user()->isSubAdmin()) && ($user->trainings_count ?? 0) > 0)
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light btn-sm py-1" id="btn-remove-selected" title="Remove selected from this person's record" disabled>
                        <i class="bi bi-trash me-1"></i> Remove selected
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm py-1" id="btn-remove-all" title="Remove all trainings from this person's record">
                        <i class="bi bi-trash me-1"></i> Remove all
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div id="personnel-trainings-loading" class="text-center py-5" role="status" aria-live="polite">
                <div class="spinner-border" style="color: var(--deped-primary);" aria-hidden="true"></div>
                <p class="mt-2 mb-0 text-muted small">Loading…</p>
            </div>
            <div id="personnel-trainings-empty" class="text-center text-muted py-5 d-none">
                <i class="bi bi-journal-check display-6 d-block mb-2 opacity-50"></i>
                <p class="mb-0">No seminars or trainings recorded.</p>
                <p class="small mb-0 mt-1">Contact admin to add attendance records.</p>
            </div>
            <div id="personnel-trainings-wrap" class="d-none">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle" id="personnel-trainings-table">
                        <thead>
                            <tr>
                                @if(auth()->user()->isAdmin() || auth()->user()->isSubAdmin())
                                    <th class="text-center" style="width: 2.5rem;">
                                        <label class="mb-0 d-flex align-items-center justify-content-center">
                                            <input type="checkbox" class="form-check-input" id="training-select-all" aria-label="Select all rows">
                                        </label>
                                    </th>
                                @endif
                                <th>Title</th>
                                <th>Type of L&amp;D</th>
                                <th>Provider</th>
                                <th>Venue</th>
                                <th>Date</th>
                                <th class="text-end">Hrs</th>
                            </tr>
                        </thead>
                        <tbody id="personnel-trainings-tbody"></tbody>
                    </table>
                </div>
                <div class="trainings-pagination-wrap d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
                    <div class="trainings-pagination-info text-muted small" id="personnel-trainings-pagination-info" aria-live="polite"></div>
                    <nav class="trainings-pagination" id="personnel-trainings-pagination" aria-label="Trainings pagination"></nav>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0" for="personnel-trainings-per-page">Per page</label>
                        <select id="personnel-trainings-per-page" class="form-select form-select-sm" style="width: auto;" aria-label="Rows per page">
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

{{-- Confirm Remove all trainings --}}
<div class="modal fade" id="modalRemoveAllTrainings" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove all trainings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Remove all trainings from <strong>{{ $user->name }}</strong>'s record? This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-remove-all"><i class="bi bi-trash me-1"></i> Remove all</button>
            </div>
        </div>
    </div>
</div>

{{-- Import Excel modal (trainings for this personnel only) --}}
<div class="modal fade" id="modalImportExcel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Trainings from Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Import trainings from an Excel file and assign them to <strong>{{ $user->name }}</strong>. Columns: Title, Type, Provider, Venue, Start Date, End Date, Hours, Attended Date.</p>
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
    const baseUrl = '{{ url("/") }}';
    const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
    const userId = {{ $user->id }};
    const detachBulkUrl = baseUrl + '/api/personnel/' + userId + '/trainings/detach-bulk';
    const personnelTrainingsUrl = baseUrl + '/api/personnel/' + userId + '/trainings';
    const hasCheckboxes = !!document.getElementById('training-select-all');

    window._personnelTrainingsPage = 1;
    window._personnelTrainingsPerPage = 10;

    function formatDateDisplay(isoStr) {
        if (!isoStr) return '—';
        var m = String(isoStr).match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (!m) return isoStr;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return parseInt(m[3], 10) + ' ' + months[parseInt(m[2], 10) - 1] + ' ' + m[1];
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

    async function loadPersonnelTrainings() {
        var loading = document.getElementById('personnel-trainings-loading');
        var empty = document.getElementById('personnel-trainings-empty');
        var wrap = document.getElementById('personnel-trainings-wrap');
        var tbody = document.getElementById('personnel-trainings-tbody');
        if (loading) loading.classList.remove('d-none');
        if (empty) empty.classList.add('d-none');
        if (wrap) wrap.classList.add('d-none');
        try {
            var qs = '?page=' + (window._personnelTrainingsPage || 1) + '&per_page=' + (window._personnelTrainingsPerPage || 10);
            var r = await fetch(personnelTrainingsUrl + qs, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            var json = await r.json().catch(function() { return {}; });
            if (loading) loading.classList.add('d-none');
            if (!r.ok) { if (empty) { empty.classList.remove('d-none'); empty.querySelector('p').textContent = 'Error loading trainings.'; } return; }
            var data = json.data || [];
            var meta = json.meta || {};
            if (meta.total === 0 || data.length === 0 && (meta.total === undefined || meta.total === 0)) {
                if (empty) { empty.classList.remove('d-none'); }
                if (tbody) tbody.innerHTML = '';
                if (wrap) wrap.classList.add('d-none');
                return;
            }
            if (empty) empty.classList.add('d-none');
            var rows = [];
            data.forEach(function(t) {
                var title = (t && t.title != null) ? String(t.title) : '—';
                var typeOfLd = typeOfLdLabel(t && t.type_of_ld, t && t.type_of_ld_specify);
                var provider = (t && t.provider) ? String(t.provider) : '—';
                var venue = (t && t.venue) ? String(t.venue) : '—';
                var duration = (t.start_date && t.end_date) ? (formatDateDisplay(t.start_date) + ' – ' + formatDateDisplay(t.end_date)) : (formatDateDisplay(t.start_date) || '—');
                var hrs = (t && t.hours != null) ? t.hours : '—';
                var cbCell = hasCheckboxes ? '<td class="text-center"><input type="checkbox" class="form-check-input training-row-cb" value="' + (t && t.id) + '" data-training-id="' + (t && t.id) + '" aria-label="Select row"></td>' : '';
                rows.push('<tr>' + cbCell + '<td>' + escapeHtml(title) + '</td><td>' + escapeHtml(typeOfLd) + '</td><td>' + escapeHtml(provider) + '</td><td>' + escapeHtml(venue) + '</td><td>' + escapeHtml(duration) + '</td><td class="text-end">' + (typeof hrs === 'number' ? hrs : escapeHtml(hrs)) + '</td></tr>');
            });
            if (tbody) tbody.innerHTML = rows.join('');
            if (wrap) wrap.classList.remove('d-none');
            renderPersonnelTrainingsPagination(meta);
            var selectAll = document.getElementById('training-select-all');
            if (selectAll) selectAll.checked = false;
            updateRemoveSelectedButton();
        } catch (e) {
            if (loading) loading.classList.add('d-none');
            if (empty) { empty.classList.remove('d-none'); var p = empty.querySelector('p'); if (p) p.textContent = 'Error loading trainings.'; }
        }
    }

    function renderPersonnelTrainingsPagination(meta) {
        var total = meta.total || 0, from = meta.from || 0, to = meta.to || 0, current = meta.current_page || 1, last = meta.last_page || 1;
        var info = 'Showing ' + (total ? (from + '–' + to + ' of ' + total) : '0') + ' ';
        var infoEl = document.getElementById('personnel-trainings-pagination-info');
        if (infoEl) infoEl.textContent = info;
        var nav = document.getElementById('personnel-trainings-pagination');
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
            a.addEventListener('click', function(e) { e.preventDefault(); window._personnelTrainingsPage = num; loadPersonnelTrainings(); });
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

    document.getElementById('personnel-trainings-tbody') && document.getElementById('personnel-trainings-tbody').addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('training-row-cb')) updateRemoveSelectedButton();
    });

    document.getElementById('personnel-trainings-per-page') && document.getElementById('personnel-trainings-per-page').addEventListener('change', function() {
        window._personnelTrainingsPerPage = parseInt(this.value, 10) || 10;
        window._personnelTrainingsPage = 1;
        loadPersonnelTrainings();
    });

    function getSelectedTrainingIds() {
        var cbs = document.querySelectorAll('.training-row-cb:checked');
        return Array.prototype.map.call(cbs, function(el) { return parseInt(el.getAttribute('data-training-id'), 10); });
    }

    function updateRemoveSelectedButton() {
        var btn = document.getElementById('btn-remove-selected');
        if (!btn) return;
        btn.disabled = getSelectedTrainingIds().length === 0;
    }

    function runDetachBulk(body) {
        return fetch(detachBulkUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        });
    }

    var selectAll = document.getElementById('training-select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.training-row-cb').forEach(function(cb) { cb.checked = selectAll.checked; });
            updateRemoveSelectedButton();
        });
    }

    var btnRemoveSelected = document.getElementById('btn-remove-selected');
    if (btnRemoveSelected) {
        btnRemoveSelected.addEventListener('click', async function() {
            var ids = getSelectedTrainingIds();
            if (ids.length === 0) return;
            var proceed = true;
            if (typeof Swal !== 'undefined') {
                var res = await Swal.fire({
                    title: 'Remove selected trainings?',
                    text: 'Remove ' + ids.length + ' selected training(s) from this person\'s record? This cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--deped-primary)',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove',
                    cancelButtonText: 'Cancel'
                });
                proceed = !!res.isConfirmed;
            } else {
                proceed = confirm('Remove ' + ids.length + ' selected training(s) from this person\'s record?');
            }
            if (!proceed) return;

            btnRemoveSelected.disabled = true;
            try {
                var r = await runDetachBulk({ training_ids: ids });
                var json = await r.json().catch(function() { return {}; });
                if (r.ok) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed',
                            text: json.message || 'Removed successfully.',
                            confirmButtonColor: 'var(--deped-primary)',
                            confirmButtonText: 'OK',
                            timer: 5000,
                            timerProgressBar: true
                        });
                    } else {
                        alert(json.message || 'Removed.');
                    }
                    window.location.reload();
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: json.message || 'Failed to remove.',
                            confirmButtonColor: 'var(--deped-primary)',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert(json.message || 'Failed to remove.');
                    }
                }
            } catch (e) {
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
                btnRemoveSelected.disabled = false;
            }
        });
    }

    var btnRemoveAll = document.getElementById('btn-remove-all');
    if (btnRemoveAll) {
        btnRemoveAll.addEventListener('click', function() {
            var modal = document.getElementById('modalRemoveAllTrainings');
            if (modal && typeof bootstrap !== 'undefined') bootstrap.Modal.getOrCreateInstance(modal).show();
        });
    }

    document.getElementById('btn-confirm-remove-all') && document.getElementById('btn-confirm-remove-all').addEventListener('click', async function() {
        var btn = this;
        btn.disabled = true;
        try {
            var r = await runDetachBulk({ all: true });
            var json = await r.json().catch(function() { return {}; });
            if (r.ok) {
                if (typeof bootstrap !== 'undefined') bootstrap.Modal.getInstance(document.getElementById('modalRemoveAllTrainings')).hide();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Removed',
                        text: json.message || 'Removed successfully.',
                        confirmButtonColor: 'var(--deped-primary)',
                        confirmButtonText: 'OK',
                        timer: 5000,
                        timerProgressBar: true
                    });
                } else {
                    alert(json.message || 'Removed.');
                }
                window.location.reload();
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: json.message || 'Failed to remove.',
                        confirmButtonColor: 'var(--deped-primary)',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert(json.message || 'Failed to remove.');
                }
            }
        } catch (e) {
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
        }
    });

    document.getElementById('btn-import-excel').addEventListener('click', async function(ev) {
        ev.preventDefault();
        const fileInput = document.getElementById('import-excel-file');
        const btn = document.getElementById('btn-import-excel');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner-border');
        const errEl = document.getElementById('import-excel-errors');

        if (!fileInput.files || !fileInput.files[0]) {
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

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('user_ids[]', userId);
        formData.append('_token', token);

        try {
            const r = await fetch(baseUrl + '/api/trainings/import', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                body: formData
            });
            const json = await r.json().catch(function() { return {}; });
            if (r.ok) {
                bootstrap.Modal.getInstance(document.getElementById('modalImportExcel')).hide();
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
                window.location.reload();
            } else {
                var msg = json.message || 'Import failed.';
                var errList = json.errors ? (Array.isArray(json.errors) ? json.errors.join('\n') : Object.values(json.errors).flat().join('\n')) : '';
                errEl.textContent = errList || msg;
                errEl.classList.remove('d-none');
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

    loadPersonnelTrainings();
})();
</script>
@endpush
@endsection
