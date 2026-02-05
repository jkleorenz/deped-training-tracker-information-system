@extends('layouts.app')

@section('title', 'Admin Dashboard - ' . config('app.name'))

@push('styles')
<style>
.card-hover { transition: box-shadow 0.2s ease; }
.card-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.stat-card-icon { width: 48px; height: 48px; border-radius: 12px; background: rgba(30, 90, 168, 0.12); color: var(--deped-primary); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; }
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
.quick-action-tile:hover { color: #1e293b; border-color: var(--deped-primary); box-shadow: 0 4px 12px rgba(30, 90, 168, 0.12); transform: translateY(-2px); }
.quick-action-tile .tile-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 0.75rem;
    border-radius: 14px;
    background: rgba(30, 90, 168, 0.1);
    color: var(--deped-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.quick-action-tile .tile-label { font-weight: 600; font-size: 0.9375rem; }
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
    <div class="col-md-4">
        <a href="{{ route('personnel.index') }}" class="text-decoration-none text-dark">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="stat-card-icon flex-shrink-0"><i class="bi bi-people"></i></div>
                    <div class="min-w-0">
                        <h6 class="text-muted mb-1">Personnel</h6>
                        <p class="mb-0 stat-number" id="stat-personnel">—</p>
                        <span class="small text-muted">View all →</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('trainings.manage') }}" class="text-decoration-none text-dark">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="stat-card-icon flex-shrink-0"><i class="bi bi-journal-check"></i></div>
                    <div class="min-w-0">
                        <h6 class="text-muted mb-1">Trainings / Seminars</h6>
                        <p class="mb-0 stat-number" id="stat-trainings">—</p>
                        <span class="small text-muted">Manage →</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('personnel.index') }}" class="text-decoration-none text-dark">
            <div class="card h-100 card-hover">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="stat-card-icon flex-shrink-0"><i class="bi bi-clipboard-check"></i></div>
                    <div class="min-w-0">
                        <h6 class="text-muted mb-1">Attendance Records</h6>
                        <p class="mb-0 stat-number" id="stat-attendance">—</p>
                        <span class="small text-muted">View all →</span>
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
            <a href="{{ route('reports.excel') }}" class="quick-action-tile">
                <div class="tile-icon"><i class="bi bi-file-earmark-excel"></i></div>
                <span class="tile-label">Export to Excel</span>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('trainings.manage') }}" class="quick-action-tile">
                <div class="tile-icon"><i class="bi bi-upload"></i></div>
                <span class="tile-label">Import from Excel</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const baseUrl = '{{ url("/") }}';
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function get(url) {
        const r = await fetch(baseUrl + url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        return r.json();
    }

    (async function loadStats() {
        try {
            const [personnelRes, trainingsRes] = await Promise.all([
                get('/api/personnel'),
                get('/api/trainings')
            ]);
            const personnelCount = (personnelRes.data || []).length;
            const trainingsCount = (trainingsRes.data || []).length;
            document.getElementById('stat-personnel').textContent = personnelCount;
            document.getElementById('stat-trainings').textContent = trainingsCount;
            // Attendance = user_trainings count; we don't have a direct endpoint, show trainings for now or leave as —
            document.getElementById('stat-attendance').textContent = '—';
        } catch (e) {
            console.error(e);
        }
    })();
})();
</script>
@endpush
@endsection
