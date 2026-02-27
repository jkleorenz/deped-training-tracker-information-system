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
.personnel-table-pagination .page-link { padding: 0.35rem 0.6rem; font-size: 0.875rem; color: var(--deped-primary) !important; }
.personnel-table-pagination .page-link:hover { color: var(--deped-primary) !important; background-color: var(--deped-accent) !important; }
.personnel-table-pagination .page-item.active .page-link { background-color: var(--deped-primary) !important; border-color: var(--deped-primary) !important; color: #fff !important; }

/* Mobile cards */
.personnel-cards-wrap { display: none; }
@media (max-width: 767.98px) {
    .personnel-table-wrap { display: none !important; }
    .personnel-cards-wrap { display: block !important; }
}
.personnel-card-mobile { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
.personnel-card-mobile .card-mobile-title { font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
.personnel-card-mobile .card-mobile-row { display: flex; gap: 0.5rem; font-size: 0.8125rem; margin-bottom: 0.25rem; }
.personnel-card-mobile .card-mobile-label { color: #64748b; min-width: 5rem; }

/* Status badges */
.badge-status { font-size: 0.75rem; padding: 0.25em 0.6em; border-radius: 999px; }
.badge-status-active { background: #dcfce7; color: #166534; }
.badge-status-inactive { background: #fee2e2; color: #991b1b; }

/* Role badges */
.badge-role { font-size: 0.75rem; padding: 0.25em 0.6em; border-radius: 999px; }
.badge-role-admin { background: #dbeafe; color: #1e40af; }
.badge-role-subadmin { background: #e0e7ff; color: #3730a3; }
.badge-role-personnel { background: #f3f4f6; color: #374151; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title mb-1">Personnel</h4>
        <p class="text-muted small mb-0">Click a row to view profile, metadata, and seminars & trainings attended.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        @if(auth()->user()->isAdmin() || auth()->user()->isSubAdmin())
            <a href="{{ route('personnel.create') }}" class="btn btn-deped"><i class="bi bi-person-plus me-1"></i> Add Personnel</a>
        @endif
        <button type="button" class="btn btn-deped" data-bs-toggle="modal" data-bs-target="#exportExcelModal"><i class="bi bi-file-earmark-excel me-1"></i> Export to Excel</button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="personnel-toolbar mb-3" id="personnel-toolbar">
            <div class="row g-2 align-items-end flex-wrap">
                <div class="col-12 col-md-auto flex-grow-1">
                    <label class="form-label visually-hidden" for="personnel-search">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="search" id="personnel-search" class="form-control" placeholder="Search by name, email, employee ID, position…" aria-label="Search personnel">
                    </div>
                </div>


                <div class="col-auto">
                    <label class="form-label visually-hidden" for="filter-school">School</label>
                    <select id="filter-school" class="form-select form-select-sm" aria-label="Filter by school">
                        <option value="">All schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school }}">{{ $school }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label visually-hidden" for="filter-designation">Position</label>
                    <select id="filter-designation" class="form-select form-select-sm" aria-label="Filter by position">
                        <option value="">All positions</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation }}">{{ $designation }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0" for="personnel-per-page">Per page</label>
                    <select id="personnel-per-page" class="form-select form-select-sm" style="width: auto;" aria-label="Rows per page">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="button" id="btn-clear-filters" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                        <i class="bi bi-x-lg" aria-hidden="true"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        <div id="personnel-loading" class="text-center py-5 d-none" role="status" aria-live="polite">
            <div class="spinner-border" style="color: var(--deped-primary);" aria-hidden="true"></div>
            <p class="mt-2 mb-0 text-muted small">Loading…</p>
        </div>
        <div id="personnel-empty" class="text-center py-5 text-muted d-none">
            <i class="bi bi-people display-5 d-block mb-2 opacity-50" aria-hidden="true"></i>
            <p class="mb-2">No personnel found.</p>
            <p class="small mb-0">Try a different search or add personnel via registration.</p>
        </div>
        <div class="personnel-table-wrap d-none" id="personnel-wrap">
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
                    <tbody id="personnel-tbody"></tbody>
                </table>
            </div>
            <div class="personnel-table-pagination-wrap d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
                <div class="text-muted small" id="personnel-pagination-info" aria-live="polite"></div>
                <nav class="personnel-table-pagination" id="personnel-pagination" aria-label="Personnel pagination"></nav>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0" for="personnel-per-page-footer">Per page</label>
                    <select id="personnel-per-page-footer" class="form-select form-select-sm" style="width: auto;" aria-label="Rows per page">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Mobile: stacked cards --}}
        <div class="personnel-cards-wrap d-none" id="personnel-cards-wrap">
            <div id="personnel-cards"></div>
            <div class="personnel-table-pagination-wrap d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 pt-2 border-top">
                <div class="text-muted small" id="personnel-cards-pagination-info"></div>
                <nav class="personnel-table-pagination" id="personnel-cards-pagination" aria-label="Personnel pagination"></nav>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0" for="personnel-cards-per-page">Per page</label>
                    <select id="personnel-cards-per-page" class="form-select form-select-sm" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
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
    var baseUrl = '{{ url("/") }}';
    var isAdmin = {{ $isAdmin ? 'true' : 'false' }};
    var loadAbort = null;

    // Global state
    window._personnelPage = 1;
    window._personnelPerPage = 10;

    function getQueryParams() {
        var q = document.getElementById('personnel-search');
        var school = document.getElementById('filter-school');
        var designation = document.getElementById('filter-designation');
        var perPageEl = document.getElementById('personnel-per-page');
        var perPageCards = document.getElementById('personnel-cards-per-page');
        var perPage = perPageEl ? parseInt(perPageEl.value, 10) : (perPageCards ? parseInt(perPageCards.value, 10) : 10);
        return {
            search: (q && q.value.trim()) || '',
            school: (school && school.value) || '',
            designation: (designation && designation.value) || '',
            page: window._personnelPage || 1,
            per_page: perPage
        };
    }

    function buildQueryString(params) {
        var parts = [];
        if (params.search) parts.push('search=' + encodeURIComponent(params.search));

        if (params.school) parts.push('school=' + encodeURIComponent(params.school));
        if (params.designation) parts.push('designation=' + encodeURIComponent(params.designation));
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
        var search = window.location.search;
        var params = {};
        if (search) {
            search.slice(1).split('&').forEach(function(pair) {
                var i = pair.indexOf('=');
                if (i > 0) { params[decodeURIComponent(pair.slice(0, i))] = decodeURIComponent(pair.slice(i + 1)); }
            });
        }
        if (params.page) window._personnelPage = Math.max(1, parseInt(params.page, 10));
        if (params.per_page) window._personnelPerPage = parseInt(params.per_page, 10);

        var searchEl = document.getElementById('personnel-search');
        var schoolEl = document.getElementById('filter-school');
        var designationEl = document.getElementById('filter-designation');
        var perEl = document.getElementById('personnel-per-page');
        var perCards = document.getElementById('personnel-cards-per-page');
        var perFooter = document.getElementById('personnel-per-page-footer');

        if (params.search && searchEl && document.activeElement !== searchEl) searchEl.value = params.search;
        if (params.school && schoolEl && document.activeElement !== schoolEl) schoolEl.value = params.school;
        if (params.designation && designationEl && document.activeElement !== designationEl) designationEl.value = params.designation;

        var perPageValue = params.per_page || '10';
        if (perEl && document.activeElement !== perEl) perEl.value = perPageValue;
        if (perCards && document.activeElement !== perCards) perCards.value = perPageValue;
        if (perFooter && document.activeElement !== perFooter) perFooter.value = perPageValue;
    }

    function escapeHtml(s) {
        if (s == null || s === '') return '—';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }



    function renderRoleBadge(role) {
        if (role === 'admin') return '<span class="badge-role badge-role-admin">Admin</span>';
        if (role === 'sub-admin') return '<span class="badge-role badge-role-subadmin">Sub-admin</span>';
        return '<span class="badge-role badge-role-personnel">Personnel</span>';
    }

    async function loadPersonnel(skipReadUrl) {
        if (!skipReadUrl) readParamsFromUrl();
        var params = getQueryParams();
        var loading = document.getElementById('personnel-loading');
        var empty = document.getElementById('personnel-empty');
        var wrap = document.getElementById('personnel-wrap');
        var cardsWrap = document.getElementById('personnel-cards-wrap');

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
            var r = await fetch(baseUrl + '/api/personnel' + qs, {
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
            // Update page number from server response to ensure consistency
            // This is important when the requested page doesn't exist (e.g., last page changed)
            window._personnelPage = meta.current_page || 1;

            syncUrl(getQueryParams());

            if (data.length === 0 && (meta.total === undefined || meta.total === 0)) {
                if (empty) empty.classList.remove('d-none');
                if (wrap) wrap.classList.add('d-none');
                if (cardsWrap) cardsWrap.classList.add('d-none');
                return;
            }

            if (empty) empty.classList.add('d-none');
            renderTable(data, meta);
            renderCards(data, meta);
            renderPagination(meta);
        } catch (e) {
            if (e.name === 'AbortError') return;
            if (loading) loading.classList.add('d-none');
            showError('Error loading data.');
        }
    }

    function renderTable(data, meta) {
        var wrap = document.getElementById('personnel-wrap');
        var tbody = document.getElementById('personnel-tbody');
        if (!wrap || !tbody) return;
        var rows = [];
        data.forEach(function(p) {
            var name = (p && p.name) || '';
            var empId = (p && p.employee_id) || '—';
            var designation = (p && p.designation) || '—';
            var school = (p && p.school) || '—';
            var status = (p && p.status) || 'active';
            var role = (p && p.role) || 'personnel';
            var showUrl = baseUrl + '/personnel/' + (p && p.id);

            rows.push('<tr class="personnel-row" data-user-id="' + (p && p.id) + '" tabindex="0" role="button" style="cursor: pointer;">');
            rows.push('<td class="col-title"><a href="' + showUrl + '" class="text-decoration-none text-dark"><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(name) + '">' + escapeHtml(name) + '</span></a></td>');
            rows.push('<td>' + escapeHtml(empId) + '</td>');
            rows.push('<td><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(designation) + '">' + escapeHtml(designation) + '</span></td>');
            rows.push('<td><span class="text-truncate-cell d-inline-block" title="' + escapeHtml(school) + '">' + escapeHtml(school) + '</span></td>');

            rows.push('</tr>');
        });
        tbody.innerHTML = rows.join('');
        wrap.classList.remove('d-none');
        if (window.innerWidth < 768) {
            wrap.classList.add('d-none');
            if (cardsWrap) cardsWrap.classList.remove('d-none');
        }
    }

    function renderCards(data, meta) {
        var wrap = document.getElementById('personnel-cards-wrap');
        var container = document.getElementById('personnel-cards');
        if (!wrap || !container) return;
        container.innerHTML = data.map(function(p) {
            var name = (p && p.name) || '';
            var empId = (p && p.employee_id) || '—';
            var designation = (p && p.designation) || '—';
            var school = (p && p.school) || '—';
            var status = (p && p.status) || 'active';
            var role = (p && p.role) || 'personnel';
            var showUrl = baseUrl + '/personnel/' + (p && p.id);
            return '<div class="personnel-card-mobile" data-id="' + (p && p.id) + '">' +
                '<div class="card-mobile-title"><a href="' + showUrl + '" class="text-decoration-none text-dark">' + escapeHtml(name) + '</a></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Employee ID:</span><span>' + escapeHtml(empId) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">Position:</span><span>' + escapeHtml(designation) + '</span></div>' +
                '<div class="card-mobile-row"><span class="card-mobile-label">School:</span><span>' + escapeHtml(school) + '</span></div>' +
                '</div>';
        }).join('');
        if (window.innerWidth < 768 && data.length > 0) wrap.classList.remove('d-none');
    }

    function renderPagination(meta) {
        var total = meta.total || 0, from = meta.from || 0, to = meta.to || 0, current = meta.current_page || 1, last = meta.last_page || 1;
        var info = 'Showing ' + (total ? (from + '–' + to + ' of ' + total) : '0') + ' ';
        document.getElementById('personnel-pagination-info').textContent = info;
        var cardsInfo = document.getElementById('personnel-cards-pagination-info');
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
                a.addEventListener('click', function(e) { e.preventDefault(); window._personnelPage = num; loadPersonnel(true); });
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
        makePageNav('personnel-pagination');
        makePageNav('personnel-cards-pagination');
    }

    // Event listeners
    var searchTimeout = null;
    document.getElementById('personnel-search') && document.getElementById('personnel-search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { window._personnelPage = 1; loadPersonnel(true); }, 350);
    });

    // Auto-submit on filter dropdown changes
    ['filter-school', 'filter-designation'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', function() { window._personnelPage = 1; loadPersonnel(true); });
    });

    // Per-page changes
    document.getElementById('personnel-per-page') && document.getElementById('personnel-per-page').addEventListener('change', function() {
        window._personnelPage = 1;
        var per = document.getElementById('personnel-cards-per-page');
        var perFooter = document.getElementById('personnel-per-page-footer');
        if (per) per.value = this.value;
        if (perFooter) perFooter.value = this.value;
        loadPersonnel(true);
    });
    document.getElementById('personnel-per-page-footer') && document.getElementById('personnel-per-page-footer').addEventListener('change', function() {
        window._personnelPage = 1;
        var per = document.getElementById('personnel-per-page');
        var perCards = document.getElementById('personnel-cards-per-page');
        if (per) per.value = this.value;
        if (perCards) perCards.value = this.value;
        loadPersonnel(true);
    });
    document.getElementById('personnel-cards-per-page') && document.getElementById('personnel-cards-per-page').addEventListener('change', function() {
        window._personnelPage = 1;
        var per = document.getElementById('personnel-per-page');
        var perFooter = document.getElementById('personnel-per-page-footer');
        if (per) per.value = this.value;
        if (perFooter) perFooter.value = this.value;
        loadPersonnel(true);
    });

    // Clear all filters button
    document.getElementById('btn-clear-filters') && document.getElementById('btn-clear-filters').addEventListener('click', function() {
        var searchEl = document.getElementById('personnel-search');
        var schoolEl = document.getElementById('filter-school');
        var designationEl = document.getElementById('filter-designation');

        if (searchEl) searchEl.value = '';
        if (schoolEl) schoolEl.value = '';
        if (designationEl) designationEl.value = '';

        window._personnelPage = 1;

        // Clear URL parameters
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, '', window.location.pathname);
        }

        loadPersonnel(true);
    });

    // Row click navigation
    var table = document.getElementById('personnel-table');
    if (table) {
        table.addEventListener('click', function(e) {
            var row = e.target.closest('tr.personnel-row');
            if (!row || e.target.tagName === 'A') return;
            var link = row.querySelector('a');
            if (link) window.location.href = link.getAttribute('href');
        });
    }

    // Export functionality
    var exportBaseUrl = "{{ url(route('reports.excel')) }}";
    var goBtn = document.getElementById('exportExcelGo');
    var selectAllBtn = document.getElementById('exportSelectAll');
    var clearAllBtn = document.getElementById('exportClearAll');
    var checkboxes = document.querySelectorAll('.export-user-cb');

    function buildExportUrl() {
        var ids = [];
        checkboxes.forEach(function(cb) {
            if (cb.checked) ids.push(cb.value);
        });
        if (ids.length === 0) return exportBaseUrl;
        return exportBaseUrl + '?' + ids.map(function(id) { return 'user_id[]=' + encodeURIComponent(id); }).join('&');
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

    // Initial load
    loadPersonnel();
})();
</script>
@endpush
@endsection
