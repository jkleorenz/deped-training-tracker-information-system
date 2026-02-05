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
@endsection
