@extends('layouts.app')

@section('title', $user->name . ' - ' . config('app.name'))

@push('styles')
<style>
.personnel-profile .card-header.card-header-green { background-color: #1e5aa8 !important; color: #fff; }
.personnel-profile .card-header.card-header-green .mb-0 { color: #fff; }
.personnel-profile .card-body { padding: 1.25rem 1.25rem; }
.personnel-profile .table td { padding: 0.75rem 0.85rem; vertical-align: middle; }
.personnel-profile .table thead th { padding: 0.75rem 0.85rem; white-space: nowrap; }
.personnel-profile .table-responsive { border-radius: 0.25rem; }
@media (min-width: 992px) {
    .personnel-profile .table thead th { position: sticky; top: 0; background: #f8f9fa; z-index: 1; box-shadow: 0 1px 0 0 #dee2e6; }
}
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
                <div class="d-flex gap-1">
                    <a href="{{ route('reports.pdf', ['user_id' => $user->id]) }}" class="btn btn-deped btn-sm"><i class="bi bi-file-pdf me-1"></i> Print PDF</a>
                    <a href="{{ route('reports.excel', ['user_id' => $user->id]) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportExcel"><i class="bi bi-upload me-1"></i> Import Excel</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Personnel info card --}}
    <div class="card mb-3">
        <div class="card-header card-header-green py-2">
            <h6 class="mb-0">Personnel info</h6>
        </div>
        <div class="card-body">
            <dl class="row mb-0 small">
                <dt class="col-sm-3 col-md-2 text-muted fw-normal">Name</dt>
                <dd class="col-sm-9 col-md-10">{{ $user->name }}</dd>

                <dt class="col-sm-3 col-md-2 text-muted fw-normal">Email</dt>
                <dd class="col-sm-9 col-md-10">{{ $user->email }}</dd>

                @if($user->employee_id)
                <dt class="col-sm-3 col-md-2 text-muted fw-normal">Employee ID</dt>
                <dd class="col-sm-9 col-md-10">{{ $user->employee_id }}</dd>
                @endif

                @if($user->designation)
                <dt class="col-sm-3 col-md-2 text-muted fw-normal">Designation</dt>
                <dd class="col-sm-9 col-md-10">{{ $user->designation }}</dd>
                @endif

                @if($user->department)
                <dt class="col-sm-3 col-md-2 text-muted fw-normal">Department</dt>
                <dd class="col-sm-9 col-md-10">{{ $user->department }}</dd>
                @endif

                @if($user->school)
                <dt class="col-sm-3 col-md-2 text-muted fw-normal">School / Office</dt>
                <dd class="col-sm-9 col-md-10">{{ $user->school }}</dd>
                @endif
            </dl>
        </div>
    </div>

    {{-- Seminars & trainings attended card --}}
    <div class="card">
        <div class="card-header card-header-green py-2">
            <h6 class="mb-0">Seminars & trainings attended</h6>
        </div>
        <div class="card-body">
            @if($user->trainings->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-journal-check display-6 d-block mb-2 opacity-50"></i>
                    <p class="mb-0">No seminars or trainings recorded.</p>
                    <p class="small mb-0 mt-1">Contact admin to add attendance records.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Provider</th>
                                <th>Venue</th>
                                <th>Date</th>
                                <th class="text-end">Hrs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->trainings as $t)
                            <tr>
                                <td>{{ $t->title }}</td>
                                <td>{{ $t->type ? ucfirst($t->type) : '—' }}</td>
                                <td>{{ $t->provider ?? '—' }}</td>
                                <td>{{ $t->venue ?? '—' }}</td>
                                <td>
                                    @if($t->start_date && $t->end_date)
                                        {{ $t->start_date->format('M j') }} – {{ $t->end_date->format('M j, Y') }}
                                    @elseif($t->start_date)
                                        {{ $t->start_date->format('M j, Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">{{ $t->hours ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
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
                <p class="small text-muted mb-3">Import trainings from an Excel file and assign them to <strong>{{ $user->name }}</strong>. Columns: Title, Type, Provider, Venue, Start Date, End Date, Hours, Attended Date, Remarks.</p>
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

    document.getElementById('btn-import-excel').addEventListener('click', async function(ev) {
        ev.preventDefault();
        const fileInput = document.getElementById('import-excel-file');
        const btn = document.getElementById('btn-import-excel');
        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner-border');
        const errEl = document.getElementById('import-excel-errors');

        if (!fileInput.files || !fileInput.files[0]) {
            alert('Please select an Excel file.');
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
                alert(json.message || 'Import completed.');
                window.location.reload();
            } else {
                var msg = json.message || 'Import failed.';
                var errList = json.errors ? (Array.isArray(json.errors) ? json.errors.join('\n') : Object.values(json.errors).flat().join('\n')) : '';
                errEl.textContent = errList || msg;
                errEl.classList.remove('d-none');
                alert(msg + (errList ? '\n\n' + errList : ''));
            }
        } catch (e) {
            alert('Error: ' + (e.message || 'Please try again.'));
        } finally {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    });
})();
</script>
@endpush
@endsection
