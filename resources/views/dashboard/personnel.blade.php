@extends('layouts.app')

@section('title', 'My Dashboard - ' . config('app.name'))

@push('styles')
<style>
.dashboard-personnel .card-header.card-header-green { background-color: #1E35FF !important; color: #fff; }
.dashboard-personnel .card-header.card-header-green .mb-0 { color: #fff; }
.dashboard-personnel .card-body { padding: 1.25rem 1.25rem; }
.dashboard-personnel .table td { padding: 0.75rem 0.85rem; vertical-align: middle; }
.dashboard-personnel .table thead th { padding: 0.75rem 0.85rem; white-space: nowrap; }
.dashboard-personnel .card-hover { transition: box-shadow 0.2s ease; }
.dashboard-personnel .card-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.dashboard-personnel .stat-card-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(30, 53, 255, 0.12); color: var(--deped-primary); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; }
.dashboard-personnel .stat-number { font-size: 1.75rem; font-weight: 700; color: var(--deped-primary); }
/* Action buttons: icon + text side-by-side, vertically centered */
.dashboard-personnel .d-flex.justify-content-between.mb-4 .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
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
            <a href="{{ route('reports.excel') }}" class="btn btn-outline-secondary btn-sm me-1"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</a>
            <a href="{{ route('reports.pdf') }}" class="btn btn-deped btn-sm me-1" target="_blank"><i class="bi bi-file-pdf me-1"></i> Print STA</a>
            <a href="{{ route('reports.pds-pdf') }}" class="btn btn-deped btn-sm" target="_blank"><i class="bi bi-person-vcard me-1"></i> Print PDS</a>
        </div>
    </div>

    {{-- Seminars & trainings attended: same green-header card as personnel profile --}}
    <div class="card mt-3">
        <div class="card-header card-header-green py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Seminars & Trainings Attended</h6>
            <button type="button" class="btn btn-light btn-sm py-1" data-bs-toggle="modal" data-bs-target="#modalAddMyTraining" title="Add training or seminar to your record">
                <i class="bi bi-plus-lg"></i> Add training / seminar
            </button>
        </div>
        <div class="card-body">
            <div id="trainings-loading" class="text-center py-5 d-none">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                <p class="mt-2 mb-0 text-muted small">Loading...</p>
            </div>
            <div id="trainings-empty" class="text-center py-5 text-muted {{ $trainings->isEmpty() ? '' : 'd-none' }}">
                <i class="bi bi-journal-check display-5 d-block mb-2 opacity-50"></i>
                <p class="mb-1">No seminars or trainings recorded.</p>
                <p class="small mb-0">Add a training or seminar to get started.</p>
            </div>
            <div class="table-responsive {{ $trainings->isEmpty() ? 'd-none' : '' }}" id="trainings-wrap">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Provider</th>
                            <th>Venue</th>
                            <th>Start</th>
                            <th>End</th>
                            <th class="text-end">Hrs</th>
                            <th>Attended</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="trainings-tbody">
                        @foreach($trainings as $t)
                        @php
                            $startStr = $t->start_date ? \Carbon\Carbon::parse($t->start_date)->format('Y-m-d') : null;
                            $endStr = $t->end_date ? \Carbon\Carbon::parse($t->end_date)->format('Y-m-d') : null;
                            $attendedStr = ($t->pivot && $t->pivot->attended_date) ? \Carbon\Carbon::parse($t->pivot->attended_date)->format('Y-m-d') : null;
                            $typeLabel = $t->type ? ucfirst(strtolower($t->type)) : '—';
                        @endphp
                        <tr>
                            <td>{{ $t->title }}</td>
                            <td>{{ $typeLabel }}</td>
                            <td>{{ $t->provider ?? '—' }}</td>
                            <td>{{ $t->venue ?? '—' }}</td>
                            <td>{{ $startStr ? \Carbon\Carbon::parse($t->start_date)->format('M j, Y') : '—' }}</td>
                            <td>{{ $endStr ? \Carbon\Carbon::parse($t->end_date)->format('M j, Y') : '—' }}</td>
                            <td class="text-end">{{ $t->hours !== null ? $t->hours : '—' }}</td>
                            <td>{{ $attendedStr ? \Carbon\Carbon::parse($t->pivot->attended_date)->format('M j, Y') : '—' }}</td>
                            <td class="text-end align-middle">
                                <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                                    <button type="button" class="btn btn-outline-secondary btn-sm edit-attendance" data-id="{{ $t->id }}" data-title="{{ e($t->title) }}" data-attended="{{ $attendedStr ?? '' }}" data-remarks="{{ e($t->pivot->remarks ?? '') }}" title="Edit attendance"><i class="bi bi-pencil-square"></i></button>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-attendance" data-id="{{ $t->id }}" data-title="{{ e($t->title) }}" title="Remove from my record"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">—</option>
                                    <option value="seminar">Seminar</option>
                                    <option value="training">Training</option>
                                    <option value="workshop">Workshop</option>
                                    <option value="conference">Conference</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Provider</label>
                                <input type="text" name="provider" class="form-control">
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

@push('scripts')
<script>
(function() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = (meta && meta.getAttribute('content')) || '';

    async function loadMyTrainings() {
        const wrap = document.getElementById('trainings-wrap');
        const loading = document.getElementById('trainings-loading');
        const empty = document.getElementById('trainings-empty');
        const tbody = document.getElementById('trainings-tbody');
        const statEl = document.getElementById('stat-my-trainings');

        function showLoading() {
            if (loading) { loading.classList.remove('d-none'); loading.textContent = 'Loading...'; }
            if (wrap) wrap.classList.add('d-none');
            if (empty) empty.classList.add('d-none');
        }
        function hideLoadingShowError(msg) {
            if (loading) {
                loading.classList.remove('d-none');
                loading.textContent = msg || 'Error loading data.';
            }
            if (wrap) wrap.classList.add('d-none');
            if (empty) empty.classList.add('d-none');
        }

        var loadingResolved = false;
        function resolveLoading() {
            if (loadingResolved) return;
            loadingResolved = true;
            if (loading) loading.classList.add('d-none');
        }

        var safetyTimer = setTimeout(function() {
            if (loading && loading.textContent === 'Loading...') {
                loadingResolved = true;
                hideLoadingShowError('Request timed out. Refresh the page.');
            }
        }, 12000);

        showLoading();

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(function() { controller.abort(); }, 10000);
            const r = await fetch('/api/my/record/trainings?t=' + Date.now(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store',
                credentials: 'same-origin',
                signal: controller.signal
            });
            clearTimeout(timeoutId);
            clearTimeout(safetyTimer);

            var json = {};
            try { json = await r.json(); } catch (_) { json = {}; }

            resolveLoading();

            if (!r.ok) {
                hideLoadingShowError(json.message || 'Error loading data.');
                return;
            }

            var data = Array.isArray(json.data) ? json.data : [];
            if (statEl) statEl.textContent = data.length;

            if (data.length === 0) {
                if (empty) empty.classList.remove('d-none');
                if (wrap) wrap.classList.add('d-none');
                return;
            }

            if (empty) empty.classList.add('d-none');
            if (wrap && tbody) {
                wrap.classList.remove('d-none');
                tbody.innerHTML = data.map(function(t) {
                    var title = (t && t.title != null) ? String(t.title) : '';
                    var type = (t && t.type) ? (t.type.charAt(0).toUpperCase() + t.type.slice(1).toLowerCase()) : '—';
                    var remarks = (t && t.remarks != null) ? String(t.remarks) : '';
                    return '<tr>' +
                        '<td>' + escapeHtml(title) + '</td>' +
                        '<td>' + escapeHtml(type) + '</td>' +
                        '<td>' + escapeHtml(t && t.provider) + '</td>' +
                        '<td>' + escapeHtml(t && t.venue) + '</td>' +
                        '<td>' + formatDate(t && t.start_date) + '</td>' +
                        '<td>' + formatDate(t && t.end_date) + '</td>' +
                        '<td class="text-end">' + (t && t.hours != null ? t.hours : '—') + '</td>' +
                        '<td>' + formatDate(t && t.attended_date) + '</td>' +
                        '<td class="text-end align-middle">' +
                        '<div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">' +
                        '<button type="button" class="btn btn-outline-secondary btn-sm edit-attendance" data-id="' + (t && t.id) + '" data-title="' + escapeHtml(title) + '" data-attended="' + (t && t.attended_date ? t.attended_date : '') + '" data-remarks="' + escapeHtml(remarks) + '" title="Edit attendance"><i class="bi bi-pencil-square"></i></button>' +
                        '<button type="button" class="btn btn-outline-danger btn-sm remove-attendance" data-id="' + (t && t.id) + '" data-title="' + escapeHtml(title) + '" title="Remove from my record"><i class="bi bi-trash"></i></button>' +
                        '</div></td></tr>';
                }).join('');
            }
        } catch (e) {
            clearTimeout(safetyTimer);
            resolveLoading();
            if (e && e.name === 'AbortError') {
                hideLoadingShowError('Request timed out. Refresh the page.');
            } else {
                hideLoadingShowError('Error loading data.');
            }
        }
    }

    function formatDate(isoStr) {
        if (!isoStr) return '—';
        const d = new Date(isoStr);
        if (isNaN(d.getTime())) return '—';
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    function escapeHtml(s) {
        if (s == null) return '—';
        const div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    // ——— Add training modal: load existing trainings into dropdown ———
    const modalAddMy = document.getElementById('modalAddMyTraining');
    if (modalAddMy) {
        modalAddMy.addEventListener('show.bs.modal', async function() {
            const sel = document.querySelector('#form-attach-existing select[name="training_id"]');
            if (!sel) return;
            sel.innerHTML = '<option value="">— Select —</option>';
            try {
                const r = await fetch('/api/my/trainings', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await r.json();
                const data = json.data || [];
                data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    const label = t.title + (t.start_date ? ' (' + formatDate(t.start_date) + ')' : '');
                    opt.textContent = label;
                    sel.appendChild(opt);
                });
            } catch (e) {
                console.error(e);
            }
        });
    }

    // ——— Attach to existing training ———
    document.getElementById('form-attach-existing').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-attach-existing');
        const form = e.target;
        const payload = {
            training_id: parseInt(form.training_id.value, 10),
            attended_date: form.attended_date.value || null,
            remarks: form.remarks.value || null
        };
        btn.disabled = true;
        try {
            const res = await fetch('/api/my/trainings/attach', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed to add');
            bootstrap.Modal.getInstance(document.getElementById('modalAddMyTraining')).hide();
            form.reset();
            loadMyTrainings();
            alert('Training added to your record.');
        } catch (err) {
            alert(err.message || 'Could not add training. You may already be assigned to this training.');
        } finally {
            btn.disabled = false;
        }
    });

    // ——— Add new training and attach self ———
    document.getElementById('form-add-new-training').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-add-new-training');
        const form = e.target;
        const payload = {
            title: form.title.value.trim(),
            type: form.type.value || null,
            provider: form.provider.value.trim() || null,
            venue: form.venue.value.trim() || null,
            start_date: form.start_date.value,
            end_date: form.end_date.value,
            hours: form.hours.value ? parseInt(form.hours.value, 10) : null,
            description: form.description.value.trim() || null,
            attended_date: form.attended_date.value || null,
            remarks: form.remarks.value.trim() || null
        };
        btn.disabled = true;
        try {
            const res = await fetch('/api/my/trainings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed to add');
            bootstrap.Modal.getInstance(document.getElementById('modalAddMyTraining')).hide();
            form.reset();
            loadMyTrainings();
            alert('Training added to your record.');
        } catch (err) {
            alert(err.message || 'Could not add training.');
        } finally {
            btn.disabled = false;
        }
    });

    // ——— Edit & Remove actions: delegate on table container so button clicks are handled ———
    var trainingsWrapEl = document.getElementById('trainings-wrap');
    if (trainingsWrapEl) {
        trainingsWrapEl.addEventListener('click', function(e) {
            var editBtn = e.target.closest('.edit-attendance');
            if (editBtn) {
                e.preventDefault();
                e.stopPropagation();
                var id = editBtn.getAttribute('data-id');
                var title = editBtn.getAttribute('data-title') || 'Training';
                var attended = editBtn.getAttribute('data-attended') || '';
                var remarks = editBtn.getAttribute('data-remarks') || '';
                var tidEl = document.getElementById('edit-attendance-training-id');
                var titleEl = document.getElementById('edit-attendance-training-title');
                var dateEl = document.getElementById('edit-attendance-date');
                var remarksEl = document.getElementById('edit-attendance-remarks');
                if (tidEl) tidEl.value = id || '';
                if (titleEl) titleEl.textContent = title;
                if (dateEl) dateEl.value = attended;
                if (remarksEl) remarksEl.value = remarks;
                var modalEl = document.getElementById('modalEditAttendance');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
                return;
            }
        var removeBtn = e.target.closest('.remove-attendance');
        if (removeBtn) {
            e.preventDefault();
            e.stopPropagation();
            var id = removeBtn.getAttribute('data-id');
            var title = removeBtn.getAttribute('data-title') || 'this training';
            if (!confirm("Remove \"" + title + "\" from your record?")) return;
            removeBtn.disabled = true;
            fetch('/api/my/record/trainings/' + id, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(function(res) { return res.json().catch(function() { return {}; }).then(function(data) { return { res: res, data: data }; }); })
            .then(function(o) {
                if (!o.res.ok) throw new Error(o.data.message || 'Failed to remove');
                if (typeof loadMyTrainings === 'function') loadMyTrainings();
                alert('Removed from your record.');
            })
            .catch(function(err) {
                alert(err.message || 'Could not remove.');
            })
            .finally(function() {
                removeBtn.disabled = false;
            });
        }
        });
    }

    var formEditAttendance = document.getElementById('form-edit-attendance');
    if (formEditAttendance) {
        formEditAttendance.addEventListener('submit', function(e) {
            e.preventDefault();
            var idEl = document.getElementById('edit-attendance-training-id');
            var id = idEl ? idEl.value : '';
            if (!id) { alert('Missing training.'); return; }
            var form = e.target;
            var payload = {
                attended_date: form.attended_date && form.attended_date.value ? form.attended_date.value : null,
                remarks: (form.remarks && form.remarks.value ? form.remarks.value.trim() : null) || null
            };
            var btn = document.getElementById('btn-save-attendance');
            if (btn) btn.disabled = true;
            fetch('/api/my/record/trainings/' + id, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            })
            .then(function(r) { return r.json().catch(function() { return {}; }).then(function(data) { return { res: r, data: data }; }); })
            .then(function(o) {
                if (!o.res.ok) throw new Error(o.data.message || 'Failed to update');
                var modalEl = document.getElementById('modalEditAttendance');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) inst.hide();
                }
                if (typeof loadMyTrainings === 'function') loadMyTrainings();
                alert('Attendance updated.');
            })
            .catch(function(err) {
                alert(err.message || 'Could not update.');
            })
            .finally(function() {
                if (btn) btn.disabled = false;
            });
        });
    }

    // Initial data is server-rendered; only call API when refreshing after add/edit/delete
    window.refreshMyTrainings = loadMyTrainings;
})();
</script>
@endpush
@endsection
