@extends('layouts.app')

@section('title', 'Manage Trainings - ' . config('app.name'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title mb-1">Manage Trainings & Seminars</h4>
        <p class="text-muted small mb-0">Add, edit, and assign personnel to trainings.</p>
    </div>
    <button type="button" class="btn btn-deped" data-bs-toggle="modal" data-bs-target="#modalAddTraining"><i class="bi bi-plus-lg me-1"></i> Add Training</button>
</div>
<div class="card">
    <div class="card-body">
        <div id="trainings-loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            <p class="mt-2 mb-0 text-muted small">Loading trainings...</p>
        </div>
        <div class="table-responsive d-none" id="trainings-wrap">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Provider</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Hours</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="trainings-tbody"></tbody>
            </table>
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
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">—</option>
                            <option value="seminar">Seminar</option>
                            <option value="training">Training</option>
                            <option value="workshop">Workshop</option>
                            <option value="conference">Conference</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provider</label>
                        <input type="text" name="provider" class="form-control">
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
                        <label class="form-label">Type</label>
                        <select name="type" id="edit-type" class="form-select">
                            <option value="">—</option>
                            <option value="seminar">Seminar</option>
                            <option value="training">Training</option>
                            <option value="workshop">Workshop</option>
                            <option value="conference">Conference</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Provider</label>
                        <input type="text" name="provider" id="edit-provider" class="form-control">
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

@push('scripts')
<script>
(function() {
    const baseUrl = '{{ url("/") }}';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let assignTrainingId = null;

    function headers() {
        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    async function loadTrainings() {
        const r = await fetch(baseUrl + '/api/trainings', { headers: { 'Accept': 'application/json' } });
        const json = await r.json();
        return json.data || [];
    }

    async function loadPersonnel() {
        const r = await fetch(baseUrl + '/api/personnel', { headers: { 'Accept': 'application/json' } });
        const json = await r.json();
        return json.data || [];
    }

    function renderTrainings(list) {
        const wrap = document.getElementById('trainings-wrap');
        const loading = document.getElementById('trainings-loading');
        const tbody = document.getElementById('trainings-tbody');
        loading.classList.add('d-none');
        wrap.classList.remove('d-none');
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-journal-check display-5 d-block mb-2 opacity-50"></i><p class="mb-0">No trainings yet.</p><p class="small mb-0 mt-1">Click "Add Training" to create one.</p></td></tr>';
            return;
        }
        tbody.innerHTML = list.map(t => `
            <tr>
                <td>${escapeHtml(t.title)}</td>
                <td>${escapeHtml(t.type ? t.type.charAt(0).toUpperCase() + t.type.slice(1).toLowerCase() : '—')}</td>
                <td>${escapeHtml(t.provider)}</td>
                <td>${formatDate(t.start_date)}</td>
                <td>${formatDate(t.end_date)}</td>
                <td>${t.hours != null ? t.hours : '—'}</td>
                <td class="align-middle">
                    <div class="d-flex align-items-center justify-content-start gap-1 flex-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="${t.id}">Edit</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary assign-btn" data-id="${t.id}" data-title="${escapeAttr(t.title)}">Assign</button>
                    </div>
                </td>
            </tr>
        `).join('');
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
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
    function escapeAttr(s) {
        if (s == null) return '';
        return String(s).replace(/"/g, '&quot;');
    }

    document.getElementById('trainings-tbody').addEventListener('click', async function(e) {
        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            const id = editBtn.getAttribute('data-id');
            try {
                const r = await fetch(baseUrl + '/api/trainings/' + id, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) throw new Error('Failed');
                const json = await r.json();
                const t = json.data;
                document.getElementById('edit-training-id').value = t.id;
                document.getElementById('edit-title').value = t.title || '';
                document.getElementById('edit-type').value = t.type || '';
                document.getElementById('edit-provider').value = t.provider || '';
                document.getElementById('edit-venue').value = t.venue || '';
                document.getElementById('edit-start_date').value = t.start_date ? String(t.start_date).slice(0, 10) : '';
                document.getElementById('edit-end_date').value = t.end_date ? String(t.end_date).slice(0, 10) : '';
                document.getElementById('edit-hours').value = t.hours != null ? t.hours : '';
                document.getElementById('edit-description').value = t.description || '';
                new bootstrap.Modal(document.getElementById('modalEditTraining')).show();
            } catch (err) {
                alert('Error loading training.');
            }
            return;
        }
        const btn = e.target.closest('.assign-btn');
        if (!btn) return;
        assignTrainingId = btn.getAttribute('data-id');
        document.getElementById('assign-training-title').textContent = btn.getAttribute('data-title');
        const personnel = await loadPersonnel();
        const sel = document.getElementById('assign-user-ids');
        sel.innerHTML = personnel.map(p => `<option value="${p.id}">${escapeHtml(p.name)} (${escapeHtml(p.employee_id || p.email)})</option>`).join('');
        new bootstrap.Modal(document.getElementById('modalAssign')).show();
    });

    document.getElementById('btn-do-assign').addEventListener('click', async function() {
        const sel = document.getElementById('assign-user-ids');
        const userIds = Array.from(sel.selectedOptions).map(o => parseInt(o.value, 10));
        const attendedDate = document.getElementById('assign-attended-date').value || null;
        if (userIds.length === 0) {
            alert('Select at least one personnel.');
            return;
        }
        try {
            const r = await fetch(baseUrl + '/api/trainings/' + assignTrainingId + '/attach', {
                method: 'POST',
                headers: headers(),
                body: JSON.stringify({ user_ids: userIds, attended_date: attendedDate })
            });
            if (!r.ok) throw new Error('Failed');
            bootstrap.Modal.getInstance(document.getElementById('modalAssign')).hide();
            const list = await loadTrainings();
            renderTrainings(list);
        } catch (e) {
            alert('Error assigning personnel.');
        }
    });

    document.getElementById('btn-update-training').addEventListener('click', async function() {
        const id = document.getElementById('edit-training-id').value;
        const data = {
            title: document.getElementById('edit-title').value,
            type: document.getElementById('edit-type').value || null,
            provider: document.getElementById('edit-provider').value || null,
            venue: document.getElementById('edit-venue').value || null,
            start_date: document.getElementById('edit-start_date').value,
            end_date: document.getElementById('edit-end_date').value,
            hours: document.getElementById('edit-hours').value ? parseInt(document.getElementById('edit-hours').value, 10) : null,
            description: document.getElementById('edit-description').value || null
        };
        try {
            const r = await fetch(baseUrl + '/api/trainings/' + id, {
                method: 'PUT',
                headers: headers(),
                body: JSON.stringify(data)
            });
            if (!r.ok) throw new Error('Failed');
            bootstrap.Modal.getInstance(document.getElementById('modalEditTraining')).hide();
            const list = await loadTrainings();
            renderTrainings(list);
        } catch (e) {
            alert('Error updating training.');
        }
    });

    document.getElementById('btn-save-training').addEventListener('click', async function() {
        const form = document.getElementById('form-add-training');
        const data = {
            title: form.title.value,
            type: form.type.value || null,
            provider: form.provider.value || null,
            venue: form.venue.value || null,
            start_date: form.start_date.value,
            end_date: form.end_date.value,
            hours: form.hours.value ? parseInt(form.hours.value, 10) : null,
            description: form.description.value || null
        };
        try {
            const r = await fetch(baseUrl + '/api/trainings', {
                method: 'POST',
                headers: headers(),
                body: JSON.stringify(data)
            });
            if (!r.ok) throw new Error('Failed');
            bootstrap.Modal.getInstance(document.getElementById('modalAddTraining')).hide();
            form.reset();
            const list = await loadTrainings();
            renderTrainings(list);
        } catch (e) {
            alert('Error saving training.');
        }
    });

    (async function init() {
        const list = await loadTrainings();
        renderTrainings(list);
    })();
})();
</script>
@endpush
@endsection
