@extends('layouts.app')

@section('title', ($isOwn ? 'My' : $user->name . "'s") . ' Personal Data Sheet - ' . config('app.name'))

@php
    $draftUrl = $isOwn ? route('pds.draft') : route('personnel.pds.draft', $user);
    $updateUrl = $isOwn ? route('pds.update') : route('personnel.pds.update', $user);
    $importLdUrl = $isOwn ? route('api.pds.importable-trainings') : route('api.personnel.pds.importable-trainings', $user);
    $oldEligibility = old('eligibility');
    $eligibilityRows = $oldEligibility !== null
        ? array_values(array_map(fn($r) => (object) array_merge(
            ['eligibility_type' => '', 'rating' => '', 'date_exam_conferment' => null, 'place_exam_conferment' => '', 'license_number' => '', 'license_valid_until' => null],
            is_array($r) ? $r : (array) $r
        ), $oldEligibility))
        : ($pds->civilServiceEligibilities ?? collect())->sortBy('sort_order')->values();
    if ($eligibilityRows instanceof \Illuminate\Support\Collection) {
        $eligibilityRows = $eligibilityRows->isEmpty()
            ? [((object) ['eligibility_type' => '', 'rating' => '', 'date_exam_conferment' => null, 'place_exam_conferment' => '', 'license_number' => '', 'license_valid_until' => null])]
            : $eligibilityRows->map(fn($e) => (object) [
                'eligibility_type' => $e->eligibility_type ?? '',
                'rating' => $e->rating ?? '',
                'date_exam_conferment' => $e->date_exam_conferment ? \Carbon\Carbon::parse($e->date_exam_conferment)->format('Y-m-d') : null,
                'place_exam_conferment' => $e->place_exam_conferment ?? '',
                'license_number' => $e->license_number ?? '',
                'license_valid_until' => $e->license_valid_until ? \Carbon\Carbon::parse($e->license_valid_until)->format('Y-m-d') : null,
            ])->values()->all();
    }
    $oldWork = old('work');
    $workRows = $oldWork !== null
        ? array_values(array_map(fn($r) => (object) array_merge(
            ['from_date' => null, 'to_date' => null, 'position_title' => '', 'department_agency' => '', 'status_of_appointment' => '', 'govt_service_yn' => ''],
            is_array($r) ? $r : (array) $r
        ), $oldWork))
        : ($pds->workExperiences ?? collect())->sortBy('sort_order')->values();
    if ($workRows instanceof \Illuminate\Support\Collection) {
        $workRows = $workRows->isEmpty()
            ? [((object) ['from_date' => null, 'to_date' => null, 'position_title' => '', 'department_agency' => '', 'status_of_appointment' => '', 'govt_service_yn' => ''])]
            : $workRows->map(fn($w) => (object) [
                'from_date' => $w->from_date ? \Carbon\Carbon::parse($w->from_date)->format('Y-m-d') : null,
                'to_date' => $w->to_date ? \Carbon\Carbon::parse($w->to_date)->format('Y-m-d') : null,
                'position_title' => $w->position_title ?? '',
                'department_agency' => $w->department_agency ?? '',
                'status_of_appointment' => $w->status_of_appointment ?? '',
                'govt_service_yn' => $w->govt_service_yn ?? '',
            ])->values()->all();
    }
    $childrenData = $pds->children_data ?? [];
    if (old('children_data')) {
        $decoded = is_string(old('children_data')) ? json_decode(old('children_data'), true) : old('children_data');
        $childrenData = is_array($decoded) ? $decoded : [];
    }
    $childrenInitial = [];
    foreach ($childrenData as $row) {
        $childrenInitial[] = (object) [
            'name' => $row['name'] ?? '',
            'dob' => isset($row['dob']) ? (\Carbon\Carbon::parse($row['dob'])->format('Y-m-d') ?? '') : '',
        ];
    }
    if (empty($childrenInitial) && ($pds->children_names || old('children_names'))) {
        $namesStr = old('children_names', $pds->children_names);
        $names = array_map('trim', preg_split('/[\r\n,]+/', $namesStr, -1, PREG_SPLIT_NO_EMPTY));
        foreach ($names as $n) {
            $childrenInitial[] = (object) ['name' => $n, 'dob' => ''];
        }
    }
    $oldVoluntary = old('voluntary');
    $voluntaryRows = $oldVoluntary !== null
        ? array_values(array_map(fn($r) => (object) array_merge(
            ['conducted_sponsored_by' => '', 'inclusive_dates_from' => null, 'inclusive_dates_to' => null, 'position_nature_of_work' => '', 'number_of_hours' => null],
            is_array($r) ? $r : (array) $r
        ), $oldVoluntary))
        : ($pds->voluntaryWorks ?? collect())->sortBy('sort_order')->values();
    if ($voluntaryRows instanceof \Illuminate\Support\Collection) {
        $voluntaryRows = $voluntaryRows->isEmpty()
            ? [((object) ['conducted_sponsored_by' => '', 'inclusive_dates_from' => null, 'inclusive_dates_to' => null, 'position_nature_of_work' => '', 'number_of_hours' => null])]
            : $voluntaryRows->map(fn($v) => (object) [
                'conducted_sponsored_by' => $v->conducted_sponsored_by ?? '',
                'inclusive_dates_from' => $v->inclusive_dates_from ? \Carbon\Carbon::parse($v->inclusive_dates_from)->format('Y-m-d') : null,
                'inclusive_dates_to' => $v->inclusive_dates_to ? \Carbon\Carbon::parse($v->inclusive_dates_to)->format('Y-m-d') : null,
                'position_nature_of_work' => $v->position_nature_of_work ?? '',
                'number_of_hours' => $v->number_of_hours ?? null,
            ])->values()->all();
    }
    $oldLd = old('learning_development');
    $ldRows = $oldLd !== null
        ? array_values(array_map(fn($r) => (object) array_merge(
            ['organization_name_address' => '', 'title_of_ld' => '', 'type_of_ld' => '', 'type_of_ld_specify' => '', 'number_of_hours' => null, 'inclusive_dates_from' => null, 'inclusive_dates_to' => null],
            is_array($r) ? $r : (array) $r
        ), $oldLd))
        : ($pds->learningDevelopments ?? collect())->sortBy('sort_order')->values();
    if ($ldRows instanceof \Illuminate\Support\Collection) {
        $ldRows = $ldRows->isEmpty()
            ? []  // Allow empty L&D - no blank row injection
            : $ldRows->map(fn($l) => (object) [
                'organization_name_address' => $l->organization_name_address ?? '',
                'title_of_ld' => $l->title_of_ld ?? '',
                'type_of_ld' => $l->type_of_ld ?? '',
                'type_of_ld_specify' => $l->type_of_ld_specify ?? '',
                'number_of_hours' => $l->number_of_hours ?? null,
                'inclusive_dates_from' => $l->inclusive_dates_from ? \Carbon\Carbon::parse($l->inclusive_dates_from)->format('Y-m-d') : null,
                'inclusive_dates_to' => $l->inclusive_dates_to ? \Carbon\Carbon::parse($l->inclusive_dates_to)->format('Y-m-d') : null,
            ])->values()->all();
    }
@endphp

@push('styles')
<style>
    .pds-step-section { display: none; }
    .pds-step-section.active { display: block; animation: pdsFadeIn 0.25s ease; }
    @keyframes pdsFadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .pds-stepper {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }
    .pds-stepper-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pds-stepper-item:hover { color: #334155; background: #f1f5f9; }
    .pds-stepper-item.active { background: var(--deped-primary); color: #fff; border-color: var(--deped-primary); cursor: default; }
    .pds-stepper-item.done { background: #dcfce7; color: #166534; border-color: #86efac; }
    .pds-stepper-item .step-num { width: 1.5rem; height: 1.5rem; border-radius: 50%; background: rgba(255,255,255,0.3); display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; }
    .pds-stepper-item.active .step-num { background: rgba(255,255,255,0.4); }
    .pds-stepper-item.done .step-num { background: #22c55e; color: #fff; }
    .pds-progress-bar-wrap {
        flex: 1;
        min-width: 120px;
        max-width: 200px;
        height: 8px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .pds-progress-bar-fill { height: 100%; background: linear-gradient(90deg, var(--deped-primary), var(--deped-primary-light)); border-radius: 999px; transition: width 0.3s ease; }
    .pds-progress-pct { font-size: 0.8125rem; font-weight: 600; color: #475569; min-width: 3rem; }
    .pds-section-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .pds-section-card .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 0.9rem 1.25rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .pds-section-card .card-header i { color: var(--deped-primary); font-size: 1.15rem; }
    .pds-section-card .card-body { padding: 1.25rem 1.5rem; }
    .pds-form-label { font-weight: 500; color: #475569; font-size: 0.875rem; margin-bottom: 0.35rem; }
    .pds-form-control {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.55rem 0.85rem;
        font-size: 1rem;
        min-height: 44px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .pds-form-control:focus {
        border-color: var(--deped-primary);
        box-shadow: 0 0 0 3px rgba(30, 53, 255, 0.15);
        outline: none;
    }
    .pds-form-control.is-invalid { border-color: #dc2626; }
    .pds-radio-group { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; }
    .pds-radio-option {
        display: flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 0.9rem; border-radius: 10px; border: 1px solid #e2e8f0;
        background: #fff; cursor: pointer; transition: all 0.2s; min-height: 44px; align-items: center;
    }
    .pds-radio-option:hover { border-color: #cbd5e1; background: #f8fafc; }
    .pds-radio-option input { margin: 0; accent-color: var(--deped-primary); }
    .pds-radio-option input:checked + span { font-weight: 600; color: var(--deped-primary); }
    .pds-sticky-bar {
        position: sticky; bottom: 0; left: 0; right: 0;
        background: #fff; border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem; margin: 0 -1.5rem -1.5rem -1.5rem;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.06); border-radius: 0 0 16px 16px;
        display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between;
    }
    .pds-same-address {
        display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem;
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; margin-bottom: 1rem;
        cursor: pointer; transition: all 0.2s;
    }
    .pds-same-address:hover { background: #dbeafe; }
    .pds-same-address input { margin: 0; accent-color: var(--deped-primary); }
    .pds-same-address span { font-weight: 500; color: #1e40af; font-size: 0.9rem; }
    .pds-instruction-banner {
        display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem;
        background: #E6F0FF; border: 1px solid #CCE0FF; border-radius: 12px; margin-bottom: 1rem; line-height: 1.45;
    }
    .pds-instruction-banner i { color: #0066CC; font-size: 1.1rem; flex-shrink: 0; }
    .pds-instruction-banner .pds-instruction-text { font-size: 0.875rem; color: #0066CC; font-weight: 500; margin: 0; }
    .pds-permanent-disabled .pds-form-control { background: #f1f5f9; color: #64748b; cursor: not-allowed; }
    .pds-name-box {
        border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem 1.25rem; background: #f8fafc; margin-bottom: 1rem;
    }
    .pds-name-box .pds-name-box-title {
        font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 0.5rem;
    }
    .pds-name-box .row { margin-bottom: 0; }
    .breadcrumb { --bs-breadcrumb-divider-color: #94a3b8; }
    .pds-subsection-title { font-size: 0.8125rem; font-weight: 600; color: #64748b; margin: 1rem 0 0.5rem; padding-bottom: 0.35rem; border-bottom: 1px solid #e2e8f0; }
    .pds-subsection-title:first-child { margin-top: 0; }
    .pds-conditional { overflow: hidden; transition: opacity 0.2s, max-height 0.3s ease; }
    .pds-conditional.hidden { max-height: 0; opacity: 0; pointer-events: none; margin: 0 !important; padding: 0 !important; }
    .pds-card-repeat {
        border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 0.75rem; overflow: hidden; background: #fff;
    }
    .pds-card-repeat-header {
        padding: 0.75rem 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: space-between; cursor: pointer; min-height: 44px;
    }
    .pds-card-repeat-header:hover { background: #f1f5f9; }
    .pds-card-repeat-header .card-title-text { font-weight: 600; font-size: 0.9rem; color: #334155; }
    .pds-card-repeat-body { padding: 1rem; border-top: 1px solid #e2e8f0; }
    .pds-card-repeat.collapsed .pds-card-repeat-body { display: none; }

    /* Collapsible Section Styles */
    .pds-collapsible-section {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 1rem;
        overflow: hidden;
        background: #fff;
        transition: box-shadow 0.2s ease;
    }
    .pds-collapsible-section:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .pds-collapsible-section.has-data {
        border-color: #bfdbfe;
        background: #fafcff;
    }
    .pds-collapsible-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        cursor: pointer;
        transition: background 0.2s ease;
        user-select: none;
    }
    .pds-collapsible-section.has-data .pds-collapsible-header {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    .pds-collapsible-header:hover {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    }
    .pds-collapsible-section.has-data .pds-collapsible-header:hover {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    }
    .pds-collapsible-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: #334155;
    }
    .pds-collapsible-title i {
        color: var(--deped-primary);
        font-size: 1.15rem;
    }
    .pds-collapsible-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.75rem;
        background: var(--deped-primary);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 999px;
        margin-left: 0.5rem;
        line-height: 1;
        white-space: nowrap;
    }
    .pds-collapsible-badge .count {
        margin-right: 0.35rem;
    }
    .pds-collapsible-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: all 0.2s ease;
    }
    .pds-collapsible-toggle:hover {
        background: #f8fafc;
        color: #334155;
    }
    .pds-collapsible-section.expanded .pds-collapsible-toggle {
        transform: rotate(180deg);
    }
    .pds-collapsible-body {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out, padding 0.3s ease;
    }
    .pds-collapsible-section.expanded .pds-collapsible-body {
        max-height: 5000px;
        padding: 1.25rem 1.5rem;
    }
    .pds-section-summary {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 1.25rem;
        background: #f8fafc;
        border-top: 1px dashed #e2e8f0;
        font-size: 0.875rem;
        color: #64748b;
    }
    .pds-section-summary-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .pds-section-summary-item strong {
        color: #334155;
    }
    .pds-quick-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.75rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #475569;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pds-quick-action-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .pds-cards-grid {
        display: grid;
        gap: 0.75rem;
    }
    .pds-btn-add-item {
        padding: 0.5rem 1rem; font-weight: 600; font-size: 0.875rem; border-radius: 10px;
        display: inline-flex; align-items: center; gap: 0.35rem; margin-top: 0.5rem;
    }
    .pds-btn-remove { padding: 0.35rem 0.5rem; color: #94a3b8; border: none; background: transparent; border-radius: 8px; cursor: pointer; transition: color 0.2s, background 0.2s; }
    .pds-btn-remove:hover { color: #dc2626; background: #fef2f2; }
    .pds-declaration-box {
        background: #fef3c7; border: 1px solid #f59e0b; border-radius: 12px; padding: 1.25rem;
        font-size: 0.9375rem; line-height: 1.5; color: #92400e;
    }
    .pds-declaration-box strong { display: block; margin-bottom: 0.5rem; }
    @media (max-width: 767.98px) {
        .pds-stepper-item span:not(.step-num) { display: none; }
        .pds-progress-bar-wrap { max-width: 80px; }
        .pds-sticky-bar { flex-direction: column; align-items: stretch; }
        .pds-sticky-bar .d-flex { justify-content: center; }
    }

/* Modern action buttons */
.action-buttons {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}
.action-btn {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    text-decoration: none;
    color: inherit;
}
.action-btn i {
    font-size: 1.2rem;
}
.action-btn-print-sta {
    background: #DC2626;
    color: white;
}
.action-btn-pds-excel {
    background: #059669;
    color: white;
    width: auto;
    height: auto;
    padding: 10px 16px;
    font-size: 0.875rem;
    font-weight: 500;
    white-space: nowrap;
}
.action-btn-edit-pds {
    background: #6B7280;
    color: white;
}
.action-btn-sta-excel {
    background: transparent;
    border: 2px solid #9CA3AF;
    color: #9CA3AF;
}
.action-btn-import-excel {
    background: #2563EB;
    color: white;
}
.action-btn:hover:not(.disabled) {
    transform: scale(1.05) translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}
.action-btn:active:not(.disabled) {
    transform: scale(0.97);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.action-btn:focus {
    outline: 2px solid #2563EB;
    outline-offset: 2px;
}
.action-btn.disabled {
    opacity: 0.5;
    pointer-events: none;
}
/* Tooltip customization */
.tooltip-inner {
    background-color: #111827;
    color: white;
    border-radius: 6px;
    font-size: 0.875rem;
}
.tooltip-arrow::before {
    border-bottom-color: #111827;
}
</style>
@endpush

@section('content')
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            @if($isOwn)
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @else
                <li class="breadcrumb-item"><a href="{{ route('personnel.index') }}">Personnel</a></li>
                <li class="breadcrumb-item"><a href="{{ route('personnel.show', $user) }}">{{ $user->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">Personal Data Sheet</li>
        </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mt-2">
        <div>
            <h4 class="page-title mb-1">Personal Data Sheet</h4>
            <p class="text-muted small mb-0">Complete in 6 steps. Use <strong>Save Draft</strong> or auto-save to keep progress. PDF export matches the official PDS layout.</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('reports.pds-excel', $isOwn ? [] : ['user_id' => $user->id]) }}" class="action-btn action-btn-pds-excel" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Export PDS Excel">
                <i class="bi bi-file-earmark-excel me-1"></i> Export PDS Excel
            </a>
        </div>
        @if(!$isOwn)
            <a href="{{ route('personnel.show', $user) }}" class="btn btn-outline-secondary">Back to profile</a>
        @endif
    </div>
</div>

{{-- 5-step stepper --}}
<nav class="pds-stepper" aria-label="PDS steps">
    <button type="button" class="pds-stepper-item active" data-step="1" aria-current="step">
        <span class="step-num">1</span><span>Personal Information</span>
    </button>
    <button type="button" class="pds-stepper-item" data-step="2"><span class="step-num">2</span><span>Family &amp; Education</span></button>
    <button type="button" class="pds-stepper-item" data-step="3"><span class="step-num">3</span><span>Work &amp; Eligibility</span></button>
    <button type="button" class="pds-stepper-item" data-step="4"><span class="step-num">4</span><span>Voluntary, L&amp;D &amp; Other</span></button>
    <button type="button" class="pds-stepper-item" data-step="5"><span class="step-num">5</span><span>Page 4 (Q34–40 &amp; References)</span></button>
    <button type="button" class="pds-stepper-item" data-step="6"><span class="step-num">6</span><span>Declaration</span></button>
    <div class="pds-progress-bar-wrap">
        <div class="pds-progress-bar-fill" id="pdsProgressFill" style="width: 17%;"></div>
    </div>
    <span class="pds-progress-pct" id="pdsProgressPct">17%</span>
</nav>

<div class="card pds-section-card">
    <div class="card-body">
        <form method="POST" action="{{ $updateUrl }}" id="pdsForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="children_names" id="pdsChildrenNamesInput" value="{{ old('children_names', $pds->children_names) }}">
            <input type="hidden" name="children_data" id="pdsChildrenDataInput" value="{{ old('children_data', $pds->children_data ? json_encode($pds->children_data) : '') }}">

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center rounded-3 mb-4" role="alert" id="pdsErrorBanner">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0 me-2"></i>
                <div>
                    <strong>Please fix the errors below.</strong>
                    <span class="d-block small mt-1">Fields with errors are highlighted. Correct them and save again.</span>
                </div>
                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Step 1: Personal Information --}}
            <div class="pds-step-section active" id="pds-step-1" data-step="1">
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-person-badge"></i> Identity — Name</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">1. Surname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control pds-form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname', $pds->surname) }}" maxlength="100" data-required-step="1" required>
                                @error('surname')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="pds-form-label">Name extension</label>
                                <input type="text" class="form-control pds-form-control" name="name_extension" value="{{ old('name_extension', $pds->name_extension) }}" maxlength="20" placeholder="e.g. JR">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="pds-form-label">2. First name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control pds-form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', $pds->first_name) }}" maxlength="100" data-required-step="1" required>
                                @error('first_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="pds-form-label">Middle name</label>
                                <input type="text" class="form-control pds-form-control" name="middle_name" value="{{ old('middle_name', $pds->middle_name) }}" maxlength="100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-calendar-event"></i> Birth &amp; physical</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">3. Date of birth (dd/mm/yyyy)</label>
                                <input type="date" class="form-control pds-form-control" name="date_of_birth" value="{{ old('date_of_birth', $pds->date_of_birth ? \Carbon\Carbon::parse($pds->date_of_birth)->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="pds-form-label">4. Place of birth</label>
                                <input type="text" class="form-control pds-form-control" name="place_of_birth" value="{{ old('place_of_birth', $pds->place_of_birth) }}" maxlength="255">
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">5. Sex at birth</label>
                                <div class="pds-radio-group">
                                    <label class="pds-radio-option">
                                        <input type="radio" name="sex" value="male" {{ old('sex', $pds->sex) === 'male' ? 'checked' : '' }}>
                                        <span>Male</span>
                                    </label>
                                    <label class="pds-radio-option">
                                        <input type="radio" name="sex" value="female" {{ old('sex', $pds->sex) === 'female' ? 'checked' : '' }}>
                                        <span>Female</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">6. Civil status</label>
                                <select class="form-select pds-form-control" name="civil_status" id="pdsCivilStatus">
                                    <option value="">— Select —</option>
                                    @foreach(['single' => 'Single', 'married' => 'Married', 'widowed' => 'Widowed', 'separated' => 'Separated', 'other' => 'Other'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('civil_status', $pds->civil_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="mt-2 pds-conditional" id="pdsCivilStatusOtherWrap">
                                    <input type="text" class="form-control pds-form-control" name="civil_status_other" value="{{ old('civil_status_other', $pds->civil_status_other) }}" placeholder="If Other, specify" maxlength="50">
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="pds-form-label">7. Height (m)</label>
                                <input type="number" class="form-control pds-form-control" name="height" value="{{ old('height', $pds->height) }}" step="0.01" min="0" max="3" placeholder="e.g. 1.65">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="pds-form-label">8. Weight (kg)</label>
                                <input type="number" class="form-control pds-form-control" name="weight" value="{{ old('weight', $pds->weight) }}" step="0.01" min="0" max="500" placeholder="e.g. 70">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="pds-form-label">9. Blood type</label>
                                <select class="form-control pds-form-control" name="blood_type">
                                    <option value="" {{ old('blood_type', $pds->blood_type) === '' || old('blood_type', $pds->blood_type) === null ? 'selected' : '' }}>Select blood type</option>
                                    <option value="A+" {{ old('blood_type', $pds->blood_type) === 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_type', $pds->blood_type) === 'A-' ? 'selected' : '' }}>A−</option>
                                    <option value="B+" {{ old('blood_type', $pds->blood_type) === 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_type', $pds->blood_type) === 'B-' ? 'selected' : '' }}>B−</option>
                                    <option value="AB+" {{ old('blood_type', $pds->blood_type) === 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_type', $pds->blood_type) === 'AB-' ? 'selected' : '' }}>AB−</option>
                                    <option value="O+" {{ old('blood_type', $pds->blood_type) === 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_type', $pds->blood_type) === 'O-' ? 'selected' : '' }}>O−</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-card-list"></i> Government IDs</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="pds-form-label">10. UMID ID No.</label>
                                <input type="text" class="form-control pds-form-control" name="umid_id" value="{{ old('umid_id', $pds->umid_id) }}" maxlength="50">
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="pds-form-label">11. PAG-IBIG ID No.</label>
                                <input type="text" class="form-control pds-form-control" name="pagibig_id" value="{{ old('pagibig_id', $pds->pagibig_id) }}" maxlength="50">
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="pds-form-label">12. PhilHealth No.</label>
                                <input type="text" class="form-control pds-form-control pds-mask-philhealth" name="philhealth_no" value="{{ old('philhealth_no', $pds->philhealth_no) }}" maxlength="14" placeholder="00-000000000-0" inputmode="numeric" autocomplete="off">
                                <small class="text-muted">Format: 00-000000000-0</small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="pds-form-label">13. PhilSys Number (PSN)</label>
                                <input type="text" class="form-control pds-form-control pds-mask-psn" name="philsys_number" value="{{ old('philsys_number', $pds->philsys_number) }}" maxlength="25" inputmode="numeric" autocomplete="off">
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="pds-form-label">14. TIN No.</label>
                                <input type="text" class="form-control pds-form-control pds-mask-tin" name="tin_no" value="{{ old('tin_no', $pds->tin_no) }}" maxlength="15" placeholder="000-000-000-000" inputmode="numeric" autocomplete="off">
                                <small class="text-muted">Format: 000-000-000-000</small>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="pds-form-label">15. Agency Employee No.</label>
                                <input type="text" class="form-control pds-form-control" name="agency_employee_no" value="{{ old('agency_employee_no', $pds->agency_employee_no ?? $user->employee_id) }}" maxlength="50">
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="pds-form-label">Date of appointment</label>
                                <input type="date" class="form-control pds-form-control" name="date_of_appointment" value="{{ old('date_of_appointment', $pds->date_of_appointment ? \Carbon\Carbon::parse($pds->date_of_appointment)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-flag"></i> 16. Citizenship</div>
                    <div class="card-body">
                        <div class="pds-radio-group flex-wrap mb-3">
                            <label class="pds-radio-option">
                                <input type="radio" name="citizenship" value="filipino" {{ old('citizenship', $pds->citizenship) === 'filipino' ? 'checked' : '' }}>
                                <span>Filipino</span>
                            </label>
                            <label class="pds-radio-option">
                                <input type="radio" name="citizenship" value="dual" {{ old('citizenship', $pds->citizenship) === 'dual' ? 'checked' : '' }}>
                                <span>Dual citizenship</span>
                            </label>
                        </div>
                        <div class="pds-conditional" id="pdsDualCitizenshipWrap">
                            <div class="d-flex flex-wrap gap-3 align-items-center mb-2">
                                <span class="text-muted small">If dual:</span>
                                <label class="pds-radio-option">
                                    <input type="radio" name="dual_citizenship_type" value="by_birth" {{ old('dual_citizenship_type', $pds->dual_citizenship_type) === 'by_birth' ? 'checked' : '' }}>
                                    <span>By birth</span>
                                </label>
                                <label class="pds-radio-option">
                                    <input type="radio" name="dual_citizenship_type" value="by_naturalization" {{ old('dual_citizenship_type', $pds->dual_citizenship_type) === 'by_naturalization' ? 'checked' : '' }}>
                                    <span>By naturalization</span>
                                </label>
                            </div>
                            <label class="pds-form-label">Country</label>
                            <input type="text" class="form-control pds-form-control" name="dual_citizenship_country" value="{{ old('dual_citizenship_country', $pds->dual_citizenship_country) }}" placeholder="Country" maxlength="100" style="max-width: 280px;">
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-house-door"></i> 17. Residential address</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6"><label class="pds-form-label">House/Block/Lot No.</label><input type="text" class="form-control pds-form-control" name="residential_house_no" value="{{ old('residential_house_no', $pds->residential_house_no) }}" maxlength="100"></div>
                            <div class="col-12 col-md-6"><label class="pds-form-label">Street</label><input type="text" class="form-control pds-form-control" name="residential_street" value="{{ old('residential_street', $pds->residential_street) }}" maxlength="255"></div>
                            <div class="col-12 col-md-6"><label class="pds-form-label">Subdivision/Village</label><input type="text" class="form-control pds-form-control" name="residential_subdivision" value="{{ old('residential_subdivision', $pds->residential_subdivision) }}" maxlength="255"></div>
                            <div class="col-12 col-md-6"><label class="pds-form-label">Barangay</label><input type="text" class="form-control pds-form-control" name="residential_barangay" value="{{ old('residential_barangay', $pds->residential_barangay) }}" maxlength="100"></div>
                            <div class="col-12 col-md-6"><label class="pds-form-label">City/Municipality</label><input type="text" class="form-control pds-form-control" name="residential_city" value="{{ old('residential_city', $pds->residential_city) }}" maxlength="100" list="pds-city-list"></div>
                            <div class="col-12 col-md-4"><label class="pds-form-label">Province</label><input type="text" class="form-control pds-form-control" name="residential_province" value="{{ old('residential_province', $pds->residential_province) }}" maxlength="100" list="pds-province-list"></div>
                            <div class="col-12 col-md-2"><label class="pds-form-label">ZIP code</label><input type="text" class="form-control pds-form-control" name="residential_zip" value="{{ old('residential_zip', $pds->residential_zip) }}" maxlength="20"></div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card" id="permanent-address-card">
                    <div class="card-header"><i class="bi bi-pin-map"></i> 18. Permanent address</div>
                    <div class="card-body">
                        <label class="pds-same-address">
                            <input type="checkbox" id="pdsSameAsResidential" autocomplete="off">
                            <span><i class="bi bi-clipboard-check me-1"></i> Same as residential address</span>
                        </label>
                        <div class="row g-3 pds-permanent-fields">
                            <div class="col-12 col-md-6"><label class="pds-form-label">House/Block/Lot No.</label><input type="text" class="form-control pds-form-control" name="permanent_house_no" id="permanent_house_no" value="{{ old('permanent_house_no', $pds->permanent_house_no) }}" maxlength="100"></div>
                            <div class="col-12 col-md-6"><label class="pds-form-label">Street</label><input type="text" class="form-control pds-form-control" name="permanent_street" id="permanent_street" value="{{ old('permanent_street', $pds->permanent_street) }}" maxlength="255"></div>
                            <div class="col-12 col-md-6"><label class="pds-form-label">Subdivision/Village</label><input type="text" class="form-control pds-form-control" name="permanent_subdivision" id="permanent_subdivision" value="{{ old('permanent_subdivision', $pds->permanent_subdivision) }}" maxlength="255"></div>
                            <div class="col-12 col-md-6"><label class="pds-form-label">Barangay</label><input type="text" class="form-control pds-form-control" name="permanent_barangay" id="permanent_barangay" value="{{ old('permanent_barangay', $pds->permanent_barangay) }}" maxlength="100"></div>
                            <div class="col-12 col-md-6"><label class="pds-form-label">City/Municipality</label><input type="text" class="form-control pds-form-control" name="permanent_city" id="permanent_city" value="{{ old('permanent_city', $pds->permanent_city) }}" maxlength="100"></div>
                            <div class="col-12 col-md-4"><label class="pds-form-label">Province</label><input type="text" class="form-control pds-form-control" name="permanent_province" id="permanent_province" value="{{ old('permanent_province', $pds->permanent_province) }}" maxlength="100"></div>
                            <div class="col-12 col-md-2"><label class="pds-form-label">ZIP code</label><input type="text" class="form-control pds-form-control" name="permanent_zip" id="permanent_zip" value="{{ old('permanent_zip', $pds->permanent_zip) }}" maxlength="20"></div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-telephone"></i> 19–21. Contact information</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">19. Telephone no.</label>
                                <input type="text" class="form-control pds-form-control pds-input-phone" name="telephone" value="{{ old('telephone', $pds->telephone) }}" maxlength="50" inputmode="tel" placeholder="e.g. 02 8123 4567">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">20. Mobile no.</label>
                                <input type="text" class="form-control pds-form-control pds-input-phone" name="mobile" value="{{ old('mobile', $pds->mobile) }}" maxlength="50" inputmode="tel" placeholder="e.g. 0917 123 4567">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">21. E-mail address</label>
                                <input type="email" class="form-control pds-form-control @error('email_address') is-invalid @enderror" name="email_address" value="{{ old('email_address', $pds->email_address ?? $user->email) }}" maxlength="255">
                                @error('email_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Family & Educational Background --}}
            <div class="pds-step-section" id="pds-step-2" data-step="2">
                <div class="pds-section-card pds-conditional {{ old('civil_status', $pds->civil_status) !== 'married' ? '' : 'hidden' }}" id="pdsSpouseNA">
                    <div class="card-header"><i class="bi bi-person-hearts"></i> II. Family Background — 22. Spouse</div>
                    <div class="card-body">
                        <p class="text-muted mb-0"><strong>N/A</strong> (Not applicable — civil status is Single)</p>
                    </div>
                </div>
                <div class="pds-section-card pds-conditional" id="pdsSpouseWrap">
                    <div class="card-header"><i class="bi bi-person-hearts"></i> II. Family Background — 22. Spouse</div>
                    <div class="card-body">
                        <div class="pds-name-box">
                            <div class="pds-name-box-title">Surname | First name | Middle name | Name extension (JR., SR.)</div>
                            <div class="row g-2">
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="spouse_surname" value="{{ old('spouse_surname', $pds->spouse_surname) }}" maxlength="100" placeholder="Surname"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="spouse_first_name" value="{{ old('spouse_first_name', $pds->spouse_first_name) }}" maxlength="100" placeholder="First name"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="spouse_middle_name" value="{{ old('spouse_middle_name', $pds->spouse_middle_name) }}" maxlength="100" placeholder="Middle name"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="spouse_name_extension" value="{{ old('spouse_name_extension', $pds->spouse_name_extension) }}" maxlength="20" placeholder="e.g. JR., SR."></div>
                            </div>
                        </div>
                        <div class="pds-name-box">
                            <div class="pds-name-box-title">Occupation | Employer/Business name</div>
                            <div class="row g-2">
                                <div class="col-12 col-md-6"><input type="text" class="form-control pds-form-control" name="spouse_occupation" value="{{ old('spouse_occupation', $pds->spouse_occupation) }}" maxlength="255" placeholder="Occupation"></div>
                                <div class="col-12 col-md-6"><input type="text" class="form-control pds-form-control" name="spouse_employer_business_name" value="{{ old('spouse_employer_business_name', $pds->spouse_employer_business_name) }}" maxlength="255" placeholder="Employer / Business name"></div>
                            </div>
                        </div>
                        <div class="pds-name-box">
                            <div class="pds-name-box-title">Business address | Telephone no.</div>
                            <div class="row g-2">
                                <div class="col-12 col-md-6"><input type="text" class="form-control pds-form-control" name="spouse_business_address" value="{{ old('spouse_business_address', $pds->spouse_business_address) }}" maxlength="500" placeholder="Business address"></div>
                                <div class="col-12 col-md-6"><input type="text" class="form-control pds-form-control pds-input-phone" name="spouse_telephone" value="{{ old('spouse_telephone', $pds->spouse_telephone) }}" maxlength="50" placeholder="Telephone no."></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Children - Collapsible Section --}}
                <div class="pds-collapsible-section expanded" id="childrenSection" data-section="children">
                    <div class="pds-collapsible-header" onclick="toggleSection('childrenSection')">
                        <div class="pds-collapsible-title">
                            <i class="bi bi-people"></i>
                            <span>23. Name of children (full name, list all) — DATE OF BIRTH (dd/mm/yyyy)</span>
                            <span class="pds-collapsible-badge" id="childrenCount" style="display: none;">
                                <span class="count">0</span> children recorded
                            </span>
                        </div>
                        <button type="button" class="pds-collapsible-toggle" aria-label="Toggle section">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="pds-collapsible-body">
                        <label class="d-flex align-items-center gap-2 mb-3">
                            <input type="checkbox" id="pdsNoChildren" autocomplete="off">
                            <span>I have no children</span>
                        </label>
                        <div class="pds-conditional" id="pdsChildrenListWrap">
                            <div id="pdsChildrenCards"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm pds-btn-add-item" id="pdsAddChildBtn"><i class="bi bi-plus-lg"></i> Add child</button>
                        </div>
                    </div>
                    <div class="pds-section-summary" id="childrenSummary" style="display: none;">
                        <span class="pds-section-summary-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong id="childrenSummaryCount">0</strong> children recorded
                        </span>
                        <button type="button" class="pds-quick-action-btn ms-auto" onclick="toggleSection('childrenSection'); event.stopPropagation();">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-person"></i> 24. Father's name</div>
                    <div class="card-body">
                        <div class="pds-name-box">
                            <div class="pds-name-box-title">Surname | First name | Middle name | Name extension (JR., SR.)</div>
                            <div class="row g-2">
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="father_surname" value="{{ old('father_surname', $pds->father_surname) }}" maxlength="100" placeholder="Surname"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="father_first_name" value="{{ old('father_first_name', $pds->father_first_name) }}" maxlength="100" placeholder="First name"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="father_middle_name" value="{{ old('father_middle_name', $pds->father_middle_name) }}" maxlength="100" placeholder="Middle name"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="father_name_extension" value="{{ old('father_name_extension', $pds->father_name_extension) }}" maxlength="20" placeholder="e.g. JR., SR."></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-person"></i> 25. Mother's maiden name</div>
                    <div class="card-body">
                        <div class="pds-name-box">
                            <div class="pds-name-box-title">Surname | First name | Middle name | Name extension (JR., SR.)</div>
                            <div class="row g-2">
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="mother_surname" value="{{ old('mother_surname', $pds->mother_surname) }}" maxlength="100" placeholder="Surname"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="mother_first_name" value="{{ old('mother_first_name', $pds->mother_first_name) }}" maxlength="100" placeholder="First name"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="mother_middle_name" value="{{ old('mother_middle_name', $pds->mother_middle_name) }}" maxlength="100" placeholder="Middle name"></div>
                                <div class="col-12 col-md-3"><input type="text" class="form-control pds-form-control" name="mother_name_extension" value="{{ old('mother_name_extension', $pds->mother_name_extension) }}" maxlength="20" placeholder="e.g. JR., SR."></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-journal-bookmark"></i> III. Educational Background</div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Name of school (in full), degree/course, period (From – To), highest level/units if not graduated, scholarship/honors.</p>
                        @php $eduLevels = ['elem' => 'ELEMENTARY', 'secondary' => 'SECONDARY', 'voc' => 'VOCATIONAL / TRADE COURSE', 'college' => 'COLLEGE', 'grad' => 'GRADUATE STUDIES']; @endphp
                        @foreach($eduLevels as $key => $label)
                        <div class="pds-section-card mb-3">
                            <div class="card-header"><i class="bi bi-book"></i> {{ $label }}</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12"><label class="pds-form-label">Name of school (write in full)</label><input type="text" class="form-control pds-form-control" name="{{ $key }}_school" value="{{ old("{$key}_school", $pds->{"{$key}_school"}) }}" maxlength="255"></div>
                                    <div class="col-12 col-md-6"><label class="pds-form-label">Degree / Course (write in full)</label><input type="text" class="form-control pds-form-control" name="{{ $key }}_degree_course" value="{{ old("{$key}_degree_course", $pds->{"{$key}_degree_course"}) }}" maxlength="255"></div>
                                    <div class="col-12 col-md-3"><label class="pds-form-label">Period from</label><input type="text" class="form-control pds-form-control" name="{{ $key }}_period_from" value="{{ old("{$key}_period_from", $pds->{"{$key}_period_from"}) }}" maxlength="20" placeholder="e.g. 06/2010"></div>
                                    <div class="col-12 col-md-3"><label class="pds-form-label">Period to</label><input type="text" class="form-control pds-form-control" name="{{ $key }}_period_to" value="{{ old("{$key}_period_to", $pds->{"{$key}_period_to"}) }}" maxlength="20" placeholder="e.g. 03/2014"></div>
                                    <div class="col-12 col-md-6"><label class="pds-form-label">Highest level / Units (if not graduated)</label><input type="text" class="form-control pds-form-control" name="{{ $key }}_highest_level_units" value="{{ old("{$key}_highest_level_units", $pds->{"{$key}_highest_level_units"}) }}" maxlength="255"></div>
                                    <div class="col-12 col-md-3"><label class="pds-form-label">Year Graduated</label><input type="text" class="form-control pds-form-control" name="{{ $key }}_year_graduated" value="{{ old("{$key}_year_graduated", $pds->{"{$key}_year_graduated"}) }}" maxlength="10" placeholder="e.g. 2014"></div>
                                    <div class="col-12 col-md-3"><label class="pds-form-label">Scholarship / Academic honors</label><input type="text" class="form-control pds-form-control" name="{{ $key }}_scholarship_honors" value="{{ old("{$key}_scholarship_honors", $pds->{"{$key}_scholarship_honors"}) }}" maxlength="255" placeholder="e.g. WITH HONOR"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Step 3: Work Experience & Eligibility --}}
            <div class="pds-step-section" id="pds-step-3" data-step="3">
                {{-- Civil Service Eligibility - Collapsible --}}
                <div class="pds-collapsible-section expanded" id="eligibilitySection" data-section="eligibility">
                    <div class="pds-collapsible-header" onclick="toggleSection('eligibilitySection')">
                        <div class="pds-collapsible-title">
                            <i class="bi bi-award"></i>
                            <span>IV. Civil Service Eligibility</span>
                            <span class="pds-collapsible-badge" id="eligibilityCount" style="display: none;">
                                <span class="count">0</span> entries recorded
                            </span>
                        </div>
                        <button type="button" class="pds-collapsible-toggle" aria-label="Toggle section">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="pds-collapsible-body">
                        <div class="pds-instruction-banner">
                            <i class="bi bi-clipboard-check"></i>
                            <p class="pds-instruction-text">CES/CSEE/Career Service/RA 1080 (Board/Bar)/Under Special Laws/Category II/IV Eligibility and eligibilities for uniformed personnel. (Continue on separate sheet if necessary.)</p>
                        </div>
                        <div id="pdsEligibilityCards" class="pds-cards-grid"></div>
                        <button type="button" class="btn btn-deped btn-sm pds-btn-add-item" id="pdsAddEligibilityBtn"><i class="bi bi-plus-lg"></i> Add eligibility</button>
                    </div>
                    <div class="pds-section-summary" id="eligibilitySummary" style="display: none;">
                        <span class="pds-section-summary-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong id="eligibilitySummaryCount">0</strong> eligibility entries added
                        </span>
                        <button type="button" class="pds-quick-action-btn ms-auto" onclick="toggleSection('eligibilitySection'); event.stopPropagation();">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </div>
                </div>

                {{-- Work Experience - Collapsible --}}
                <div class="pds-collapsible-section expanded" id="workSection" data-section="work">
                    <div class="pds-collapsible-header" onclick="toggleSection('workSection')">
                        <div class="pds-collapsible-title">
                            <i class="bi bi-briefcase"></i>
                            <span>V. Work Experience</span>
                            <span class="pds-collapsible-badge" id="workCount" style="display: none;">
                                <span class="count">0</span> entries recorded
                            </span>
                        </div>
                        <button type="button" class="pds-collapsible-toggle" aria-label="Toggle section">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="pds-collapsible-body">
                        <div class="pds-instruction-banner">
                            <i class="bi bi-briefcase"></i>
                            <p class="pds-instruction-text">Include private employment. Start from your most recent work. Description of duties in attached Work Experience Sheet.</p>
                        </div>
                        <div id="pdsWorkCards" class="pds-cards-grid"></div>
                        <button type="button" class="btn btn-deped btn-sm pds-btn-add-item" id="pdsAddWorkBtn"><i class="bi bi-plus-lg"></i> Add work experience</button>
                        <p class="text-muted small mt-2 mb-0">(Continue on separate sheet if necessary)</p>
                    </div>
                    <div class="pds-section-summary" id="workSummary" style="display: none;">
                        <span class="pds-section-summary-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong id="workSummaryCount">0</strong> work experience entries added
                        </span>
                        <button type="button" class="pds-quick-action-btn ms-auto" onclick="toggleSection('workSection'); event.stopPropagation();">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </div>
                </div>
            </div>

            {{-- Step 4: Voluntary Work, L&D, Other Information (Page 3) --}}
            <div class="pds-step-section" id="pds-step-4" data-step="4">
                {{-- Voluntary Work - Collapsible --}}
                <div class="pds-collapsible-section expanded" id="voluntarySection" data-section="voluntary">
                    <div class="pds-collapsible-header" onclick="toggleSection('voluntarySection')">
                        <div class="pds-collapsible-title">
                            <i class="bi bi-hand-heart"></i>
                            <span>VI. Voluntary Work / Civic / NGO Organizations</span>
                            <span class="pds-collapsible-badge" id="voluntaryCount" style="display: none;">
                                <span class="count">0</span> entries recorded
                            </span>
                        </div>
                        <button type="button" class="pds-collapsible-toggle" aria-label="Toggle section">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="pds-collapsible-body">
                        <div class="pds-instruction-banner">
                            <i class="bi bi-info-circle"></i>
                            <p class="pds-instruction-text">List voluntary work or involvement in civic, non-government, people, or voluntary organization(s). (Continue on separate sheet if necessary.)</p>
                        </div>
                        <div id="pdsVoluntaryCards" class="pds-cards-grid"></div>
                        <button type="button" class="btn btn-deped btn-sm pds-btn-add-item" id="pdsAddVoluntaryBtn"><i class="bi bi-plus-lg"></i> Add voluntary work</button>
                    </div>
                    <div class="pds-section-summary" id="voluntarySummary" style="display: none;">
                        <span class="pds-section-summary-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong id="voluntarySummaryCount">0</strong> voluntary work entries added
                        </span>
                        <button type="button" class="pds-quick-action-btn ms-auto" onclick="toggleSection('voluntarySection'); event.stopPropagation();">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </div>
                </div>

                {{-- Learning & Development - Collapsible --}}
                <div class="pds-collapsible-section expanded" id="ldSection" data-section="ld">
                    <div class="pds-collapsible-header" onclick="toggleSection('ldSection')">
                        <div class="pds-collapsible-title">
                            <i class="bi bi-mortarboard"></i>
                            <span>VII. Learning and Development (L&amp;D) Interventions</span>
                            <span class="pds-collapsible-badge" id="ldCount" style="display: none;">
                                <span class="count">0</span> entries recorded
                            </span>
                        </div>
                        <button type="button" class="pds-collapsible-toggle" aria-label="Toggle section">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="pds-collapsible-body">
                        <div class="pds-instruction-banner">
                            <i class="bi bi-info-circle"></i>
                            <p class="pds-instruction-text">List training programs attended. For each entry, provide <strong>Title of L&amp;D</strong>, <strong>CONDUCTED/ SPONSORED BY (Write in full)</strong>, type (Managerial/Supervisory/Technical), number of hours, and inclusive dates. (Continue on separate sheet if necessary.)</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="pdsImportLdBtn" title="Import 10 most recent trainings from the Training Tracker">
                                <i class="bi bi-download me-1"></i> {{ $isOwn ? 'Import from my trainings' : 'Import from trainings' }}
                            </button>
                            <span class="text-muted small">Imported entries are editable.</span>
                        </div>
                        <div id="pdsLdCards" class="pds-cards-grid"></div>
                        <div id="pdsLdEmptyState" class="text-center py-4 text-muted" style="display: none;">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            <span class="small">No L&D entries yet. Click "Add L&D / training" to add one.</span>
                        </div>
                        <button type="button" class="btn btn-deped btn-sm pds-btn-add-item" id="pdsAddLdBtn"><i class="bi bi-plus-lg"></i> Add L&amp;D / training</button>
                    </div>
                    <div class="pds-section-summary" id="ldSummary" style="display: none;">
                        <span class="pds-section-summary-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong id="ldSummaryCount">0</strong> L&amp;D entries added
                        </span>
                        <button type="button" class="pds-quick-action-btn ms-auto" onclick="toggleSection('ldSection'); event.stopPropagation();">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </div>
                </div>

                {{-- Other Information - Collapsible --}}
                <div class="pds-collapsible-section expanded" id="otherSection" data-section="other">
                    <div class="pds-collapsible-header" onclick="toggleSection('otherSection')">
                        <div class="pds-collapsible-title">
                            <i class="bi bi-stars"></i>
                            <span>VIII. Other Information</span>
                        </div>
                        <button type="button" class="pds-collapsible-toggle" aria-label="Toggle section">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="pds-collapsible-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="pds-form-label">31. Special skills and hobbies</label>
                                <textarea class="form-control pds-form-control" name="special_skills_hobbies" rows="3" placeholder="e.g. Public speaking, sports, arts" maxlength="1000">{{ old('special_skills_hobbies', $pds->special_skills_hobbies) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">32. Non-academic distinctions / recognition</label>
                                <textarea class="form-control pds-form-control" name="non_academic_distinctions" rows="3" placeholder="e.g. Awards, citations" maxlength="1000">{{ old('non_academic_distinctions', $pds->non_academic_distinctions) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">33. Membership in association/organization</label>
                                <textarea class="form-control pds-form-control" name="membership_in_associations" rows="3" placeholder="e.g. Professional associations, civic groups" maxlength="1000">{{ old('membership_in_associations', $pds->membership_in_associations) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 5: Page 4 — Questions 34–40 (official), References, Govt ID --}}
            <div class="pds-step-section" id="pds-step-5" data-step="5">
                @php
                    $page4Yn = function($name) use ($pds) { $v = old($name, $pds->{$name}); return $v === 'Y' || $v === 'N' ? $v : ''; };
                @endphp
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-diagram-3"></i> 34–40. Page 4 Questions</div>
                    <div class="card-body">
                        <p class="pds-form-label mb-2">34. Are you related by consanguinity or affinity to the appointing or recommending authority, or to the chief of bureau or office or to the person who has immediate supervision over you in the Office, Bureau or Department where you will be appointed,</p>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="pds-form-label">a. within the third degree?</label>
                                <div class="pds-radio-group">
                                    <label class="pds-radio-option"><input type="radio" name="related_third_degree_yn" value="Y" {{ $page4Yn('related_third_degree_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="related_third_degree_yn" value="N" {{ $page4Yn('related_third_degree_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="pds-form-label">b. within the fourth degree (for Local Government Unit - Career Employees)?</label>
                                <div class="pds-radio-group">
                                    <label class="pds-radio-option"><input type="radio" name="related_fourth_degree_yn" value="Y" {{ $page4Yn('related_fourth_degree_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="related_fourth_degree_yn" value="N" {{ $page4Yn('related_fourth_degree_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="pds-conditional {{ ($page4Yn('related_third_degree_yn') !== 'Y' && $page4Yn('related_fourth_degree_yn') !== 'Y') ? 'hidden' : '' }}" data-show-any="related_third_degree_yn,related_fourth_degree_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, give details:</label>
                                    <textarea class="form-control pds-form-control" name="related_authority_details" rows="2">{{ old('related_authority_details', $pds->related_authority_details) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12"><div class="pds-subsection-title mt-3">35.</div></div>
                            <div class="col-12">
                                <label class="pds-form-label">a. Have you ever been found guilty of any administrative offense?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="admin_offense_yn" value="Y" {{ $page4Yn('admin_offense_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="admin_offense_yn" value="N" {{ $page4Yn('admin_offense_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('admin_offense_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="admin_offense_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, give details:</label>
                                    <textarea class="form-control pds-form-control" name="admin_offense_details" rows="2">{{ old('admin_offense_details', $pds->admin_offense_details) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">b. Have you been criminally charged before any court?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="criminally_charged_yn" value="Y" {{ $page4Yn('criminally_charged_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="criminally_charged_yn" value="N" {{ $page4Yn('criminally_charged_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('criminally_charged_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="criminally_charged_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, give details:</label>
                                    <div class="row g-2">
                                        <div class="col-12 col-md-4">
                                            <label class="pds-form-label">Date Filed:</label>
                                            <input type="date" class="form-control pds-form-control" name="criminally_charged_date_filed" value="{{ old('criminally_charged_date_filed', $pds->criminally_charged_date_filed) }}" placeholder="MM/DD/YYYY">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="pds-form-label">Status of Case/s:</label>
                                            <input type="text" class="form-control pds-form-control" name="criminally_charged_status" value="{{ old('criminally_charged_status', $pds->criminally_charged_status) }}" maxlength="255">
                                        </div>
                                        <div class="col-12">
                                            <label class="pds-form-label">Details (optional)</label>
                                            <textarea class="form-control pds-form-control" name="criminally_charged_details" rows="2">{{ old('criminally_charged_details', $pds->criminally_charged_details) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12"><div class="pds-subsection-title mt-3">36.</div></div>
                            <div class="col-12">
                                <label class="pds-form-label">Have you ever been convicted of any crime or violation of any law, decree, ordinance or regulation by any court or tribunal?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="convicted_yn" value="Y" {{ $page4Yn('convicted_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="convicted_yn" value="N" {{ $page4Yn('convicted_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('convicted_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="convicted_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, give details:</label>
                                    <textarea class="form-control pds-form-control" name="convicted_details" rows="2">{{ old('convicted_details', $pds->convicted_details) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12"><div class="pds-subsection-title mt-3">37.</div></div>
                            <div class="col-12">
                                <label class="pds-form-label">Have you ever been separated from the service in any of the following modes: resignation, retirement, dropped from the rolls, dismissal, termination, end of term, finished contract or phased out (abolition) in the public or private sector?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="separated_from_service_yn" value="Y" {{ $page4Yn('separated_from_service_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="separated_from_service_yn" value="N" {{ $page4Yn('separated_from_service_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('separated_from_service_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="separated_from_service_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, give details:</label>
                                    <textarea class="form-control pds-form-control" name="separated_from_service_details" rows="2">{{ old('separated_from_service_details', $pds->separated_from_service_details) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12"><div class="pds-subsection-title mt-3">38.</div></div>
                            <div class="col-12">
                                <label class="pds-form-label">a. Have you ever been a candidate in a national or local election held within the last year (except Barangay election)?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="candidate_election_yn" value="Y" {{ $page4Yn('candidate_election_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="candidate_election_yn" value="N" {{ $page4Yn('candidate_election_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('candidate_election_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="candidate_election_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, give details:</label>
                                    <textarea class="form-control pds-form-control" name="candidate_election_details" rows="2">{{ old('candidate_election_details', $pds->candidate_election_details) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">b. Have you resigned from the government service during the three (3)-month period before the last election to promote/actively campaign for a national or local candidate?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="resigned_campaign_yn" value="Y" {{ $page4Yn('resigned_campaign_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="resigned_campaign_yn" value="N" {{ $page4Yn('resigned_campaign_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('resigned_campaign_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="resigned_campaign_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, give details:</label>
                                    <textarea class="form-control pds-form-control" name="resigned_campaign_details" rows="2">{{ old('resigned_campaign_details', $pds->resigned_campaign_details) }}</textarea>
                                </div>
                            </div>
                            <div class="col-12"><div class="pds-subsection-title mt-3">39.</div></div>
                            <div class="col-12">
                                <label class="pds-form-label">Have you acquired the status of an immigrant or permanent resident of another country?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="immigrant_resident_yn" value="Y" {{ $page4Yn('immigrant_resident_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="immigrant_resident_yn" value="N" {{ $page4Yn('immigrant_resident_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('immigrant_resident_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="immigrant_resident_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, give details (country):</label>
                                    <input type="text" class="form-control pds-form-control" name="immigrant_resident_details" value="{{ old('immigrant_resident_details', $pds->immigrant_resident_details) }}" placeholder="Country" maxlength="255">
                                </div>
                            </div>
                            <div class="col-12"><div class="pds-subsection-title mt-3">40.</div></div>
                            <div class="col-12">
                                <p class="pds-form-label mb-2">Pursuant to: (a) Indigenous People's Act (RA 8371); (b) Magna Carta for Disabled Persons (RA 7277, as amended); and (c) Expanded Solo Parents Welfare Act (RA 11861), please answer the following items:</p>
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">a. Are you a member of any indigenous group?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="indigenous_group_yn" value="Y" {{ $page4Yn('indigenous_group_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="indigenous_group_yn" value="N" {{ $page4Yn('indigenous_group_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('indigenous_group_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="indigenous_group_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, please specify:</label>
                                    <input type="text" class="form-control pds-form-control" name="indigenous_group_specify" value="{{ old('indigenous_group_specify', $pds->indigenous_group_specify) }}" maxlength="255">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">b. Are you a person with disability?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="pwd_yn" value="Y" {{ $page4Yn('pwd_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="pwd_yn" value="N" {{ $page4Yn('pwd_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('pwd_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="pwd_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, please specify ID No.:</label>
                                    <input type="text" class="form-control pds-form-control" name="pwd_id_no" value="{{ old('pwd_id_no', $pds->pwd_id_no) }}" placeholder="ID No." maxlength="100">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="pds-form-label">c. Are you a solo parent?</label>
                                <div class="pds-radio-group mb-2">
                                    <label class="pds-radio-option"><input type="radio" name="solo_parent_yn" value="Y" {{ $page4Yn('solo_parent_yn') === 'Y' ? 'checked' : '' }}> <span>YES</span></label>
                                    <label class="pds-radio-option"><input type="radio" name="solo_parent_yn" value="N" {{ $page4Yn('solo_parent_yn') === 'N' ? 'checked' : '' }}> <span>NO</span></label>
                                </div>
                                <div class="pds-conditional {{ $page4Yn('solo_parent_yn') !== 'Y' ? 'hidden' : '' }}" data-show-when="solo_parent_yn" data-show-value="Y">
                                    <label class="pds-form-label">If YES, please specify ID No.:</label>
                                    <input type="text" class="form-control pds-form-control" name="solo_parent_id_no" value="{{ old('solo_parent_id_no', $pds->solo_parent_id_no) }}" placeholder="ID No." maxlength="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-person-lines-fill"></i> 41. References</div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">(Person not related by consanguinity or affinity to applicant/appointee)</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="pds-form-label">Name</th>
                                        <th class="pds-form-label">Office / Residential Address</th>
                                        <th class="pds-form-label">Contact No. and/or Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach([1 => 'ref1', 2 => 'ref2', 3 => 'ref3'] as $num => $pre)
                                    <tr>
                                        <td><input type="text" class="form-control pds-form-control border-0" name="{{ $pre }}_name" value="{{ old($pre.'_name', $pds->{$pre.'_name'}) }}" placeholder="Full name" maxlength="255"></td>
                                        <td><input type="text" class="form-control pds-form-control border-0" name="{{ $pre }}_address" value="{{ old($pre.'_address', $pds->{$pre.'_address'}) }}" placeholder="Address"></td>
                                        <td><input type="text" class="form-control pds-form-control border-0" name="{{ $pre }}_contact" value="{{ old($pre.'_contact', $pds->{$pre.'_contact'}) }}" placeholder="Contact / Email" maxlength="255"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-card-checklist"></i> 42. Declaration &amp; Government ID</div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Declaration will be confirmed in the next step. Provide government-issued ID information below.</p>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">Government Issued ID (e.g. Passport, GSIS, SSS, PRC, Driver's License)</label>
                                <select class="form-select pds-form-control" name="govt_id_type">
                                    <option value="">— Select ID Type —</option>
                                    <option value="Philippine Passport" {{ old('govt_id_type', $pds->govt_id_type) == 'Philippine Passport' ? 'selected' : '' }}>Philippine Passport</option>
                                    <option value="PhilSys National ID (ePhilID / PhilID)" {{ old('govt_id_type', $pds->govt_id_type) == 'PhilSys National ID (ePhilID / PhilID)' ? 'selected' : '' }}>PhilSys National ID (ePhilID / PhilID)</option>
                                    <option value="Driver's License (LTO)" {{ old('govt_id_type', $pds->govt_id_type) == "Driver's License (LTO)" ? 'selected' : '' }}>Driver's License (LTO)</option>
                                    <option value="UMID (Unified Multi-Purpose ID)" {{ old('govt_id_type', $pds->govt_id_type) == 'UMID (Unified Multi-Purpose ID)' ? 'selected' : '' }}>UMID (Unified Multi-Purpose ID)</option>
                                    <option value="SSS ID" {{ old('govt_id_type', $pds->govt_id_type) == 'SSS ID' ? 'selected' : '' }}>SSS ID</option>
                                    <option value="GSIS eCard" {{ old('govt_id_type', $pds->govt_id_type) == 'GSIS eCard' ? 'selected' : '' }}>GSIS eCard</option>
                                    <option value="PRC ID" {{ old('govt_id_type', $pds->govt_id_type) == 'PRC ID' ? 'selected' : '' }}>PRC ID</option>
                                    <option value="Voter's ID (COMELEC)" {{ old('govt_id_type', $pds->govt_id_type) == "Voter's ID (COMELEC)" ? 'selected' : '' }}>Voter's ID (COMELEC)</option>
                                    <option value="Postal ID (PHLPost)" {{ old('govt_id_type', $pds->govt_id_type) == 'Postal ID (PHLPost)' ? 'selected' : '' }}>Postal ID (PHLPost)</option>
                                    <option value="TIN ID (BIR)" {{ old('govt_id_type', $pds->govt_id_type) == 'TIN ID (BIR)' ? 'selected' : '' }}>TIN ID (BIR)</option>
                                    <option value="PhilHealth ID" {{ old('govt_id_type', $pds->govt_id_type) == 'PhilHealth ID' ? 'selected' : '' }}>PhilHealth ID</option>
                                    <option value="Senior Citizen ID" {{ old('govt_id_type', $pds->govt_id_type) == 'Senior Citizen ID' ? 'selected' : '' }}>Senior Citizen ID</option>
                                    <option value="PWD ID" {{ old('govt_id_type', $pds->govt_id_type) == 'PWD ID' ? 'selected' : '' }}>PWD ID</option>
                                    <option value="OFW ID (OWWA ID)" {{ old('govt_id_type', $pds->govt_id_type) == 'OFW ID (OWWA ID)' ? 'selected' : '' }}>OFW ID (OWWA ID)</option>
                                    <option value="Seaman's Book (Seafarer's Record Book)" {{ old('govt_id_type', $pds->govt_id_type) == "Seaman's Book (Seafarer's Record Book)" ? 'selected' : '' }}>Seaman's Book (Seafarer's Record Book)</option>
                                    <option value="Police Clearance ID" {{ old('govt_id_type', $pds->govt_id_type) == 'Police Clearance ID' ? 'selected' : '' }}>Police Clearance ID</option>
                                    <option value="NBI Clearance" {{ old('govt_id_type', $pds->govt_id_type) == 'NBI Clearance' ? 'selected' : '' }}>NBI Clearance</option>
                                    <option value="Firearms License ID" {{ old('govt_id_type', $pds->govt_id_type) == 'Firearms License ID' ? 'selected' : '' }}>Firearms License ID</option>
                                    <option value="Barangay ID" {{ old('govt_id_type', $pds->govt_id_type) == 'Barangay ID' ? 'selected' : '' }}>Barangay ID</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">ID/License/Passport No.</label>
                                <input type="text" class="form-control pds-form-control" name="govt_id_number" value="{{ old('govt_id_number', $pds->govt_id_number) }}" maxlength="100">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">Date/Place of Issuance</label>
                                <input type="text" class="form-control pds-form-control" name="govt_id_place_date_issue" value="{{ old('govt_id_place_date_issue', $pds->govt_id_place_date_issue) }}" placeholder="e.g. Manila, 01 Jan 2020" maxlength="255">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="pds-form-label">Date Accomplished</label>
                                <input type="date" class="form-control pds-form-control" name="date_accomplished" value="{{ old('date_accomplished', $pds->date_accomplished ? \Carbon\Carbon::parse($pds->date_accomplished)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-12 col-md-5">
                                <label class="pds-form-label">Upload photo</label>
                                <input type="file" class="form-control pds-form-control" name="photo" id="pdsPhotoInput" accept="image/jpeg,image/png,image/jpg">
                                <p class="small text-muted mt-1 mb-0">JPEG or PNG, max 5 MB. Will be cropped to 4.5×3.5 cm.</p>
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-start">
                                <div class="pds-photo-preview-wrap border rounded bg-light overflow-hidden flex-shrink-0" id="pdsPhotoPreviewWrap" style="width:135px;height:105px;">
                                    <img src="{{ $pds->photo_url ?? '' }}" alt="PDS photo" id="pdsPhotoPreview" class="pds-photo-preview-img" style="width:100%;height:100%;object-fit:cover;{{ $pds->photo_url ? '' : 'display:none;' }}">
                                    <span class="d-block w-100 h-100 d-flex align-items-center justify-content-center small text-muted" id="pdsPhotoPlaceholder" style="{{ $pds->photo_url ? 'display:none;' : '' }}">No photo</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 p-2 rounded bg-light small text-muted">
                            <strong>Photo requirement:</strong> Passport-sized, unfiltered digital picture, taken within the last 6 months. Required size: 4.5 cm × 3.5 cm. The system will crop your image to this size without stretching.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 6: Declaration (42. DECLARATION - official text) --}}
            <div class="pds-step-section" id="pds-step-6" data-step="6">
                <div class="pds-declaration-box mb-4">
                    <strong><i class="bi bi-exclamation-triangle me-1"></i> 42. Declaration</strong>
                    I declare under oath that I have personally accomplished this Personal Data Sheet which is a true, correct, and complete statement pursuant to the provisions of pertinent laws, rules, and regulations of the Republic of the Philippines. I authorize the agency head/authorized representative to verify/validate the contents stated herein. I agree that any misrepresentation made in this document and its attachments shall cause the filing of administrative/criminal case/s against me.
                </div>
                <p class="text-muted mb-4">Review your entries in previous steps if needed, then click <strong>Save and submit</strong> to finalize your Personal Data Sheet.</p>
            </div>

            {{-- Sticky navigation --}}
            <div class="pds-sticky-bar mt-4">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-secondary btn-pds-prev" id="pdsPrevBtn" aria-label="Previous step" style="display: none;">
                        <i class="bi bi-chevron-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-deped btn-pds-next" id="pdsNextBtn" aria-label="Next step">
                        Next <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="pdsSaveDraftBtn" aria-label="Save draft">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Save Draft
                    </button>
                    <span class="text-muted small" id="pdsDraftStatus"></span>
                </div>
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-deped px-4 btn-pds-submit" id="pdsSubmitBtn" style="display: none;"><i class="bi bi-check-lg me-1"></i> Save and submit</button>
                        @if($isOwn)
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        @else
                            <a href="{{ route('personnel.show', $user) }}" class="btn btn-outline-secondary">Cancel</a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<datalist id="pds-province-list">
    @foreach(['Abra','Agusan del Norte','Agusan del Sur','Aklan','Albay','Antique','Apayao','Aurora','Basilan','Bataan','Batanes','Batangas','Benguet','Biliran','Bohol','Bukidnon','Bulacan','Cagayan','Camarines Norte','Camarines Sur','Camiguin','Capiz','Catanduanes','Cavite','Cebu','Cotabato','Davao de Oro','Davao del Norte','Davao del Sur','Davao Occidental','Davao Oriental','Dinagat Islands','Eastern Samar','Guimaras','Ifugao','Ilocos Norte','Ilocos Sur','Iloilo','Isabela','Kalinga','La Union','Laguna','Lanao del Norte','Lanao del Sur','Leyte','Maguindanao','Marinduque','Masbate','Metro Manila','Misamis Occidental','Misamis Oriental','Mountain Province','Negros Occidental','Negros Oriental','Northern Samar','Nueva Ecija','Nueva Vizcaya','Occidental Mindoro','Oriental Mindoro','Palawan','Pampanga','Pangasinan','Quezon','Quirino','Rizal','Romblon','Samar','Sarangani','Siquijor','Sorsogon','South Cotabato','Southern Leyte','Sultan Kudarat','Sulu','Surigao del Norte','Surigao del Sur','Tarlac','Tawi-Tawi','Zambales','Zamboanga del Norte','Zamboanga del Sur','Zamboanga Sibugay'] as $prov)
        <option value="{{ $prov }}">
    @endforeach
</datalist>
<datalist id="pds-city-list">
    <option value="Manila"><option value="Quezon City"><option value="Caloocan"><option value="Las Piñas"><option value="Makati"><option value="Malabon"><option value="Mandaluyong"><option value="Marikina"><option value="Muntinlupa"><option value="Navotas"><option value="Parañaque"><option value="Pasay"><option value="Pasig"><option value="Pateros"><option value="San Juan"><option value="Taguig"><option value="Valenzuela">
</datalist>

@push('scripts')
<script>
(function() {
    var STEP_COUNT = 6;
    var draftUrl = @json($draftUrl);
    var currentStep = 1;
    var steps = document.querySelectorAll('.pds-step-section');
    var stepperItems = document.querySelectorAll('.pds-stepper-item');
    var progressFill = document.getElementById('pdsProgressFill');
    var progressPct = document.getElementById('pdsProgressPct');
    var prevBtn = document.getElementById('pdsPrevBtn');
    var nextBtn = document.getElementById('pdsNextBtn');
    var submitBtn = document.getElementById('pdsSubmitBtn');
    var form = document.getElementById('pdsForm');

    /**
     * ============================================
     * PDS SCROLL MANAGER
     * ============================================
     * Handles scroll position persistence and restoration across the 6-page form.
     *
     * UX Rules:
     * - Next navigation (forward): Always scroll to top (scrollTop = 0)
     * - Back navigation (backward): Restore scroll position if page has filled fields, else top
     * - Direct navigation (tabs): Restore scroll position if page has filled fields, else top
     *
     * Technical Implementation:
     * - Uses sessionStorage for temporary persistence (cleared on tab close)
     * - Debounced scroll listener (100ms) to avoid excessive writes
     * - Scroll position saved per page (keyed by step number 1-6)
     * - Filled state detection: checks inputs, selects, textareas, checkboxes, radios
     * - Restoration happens after page render via requestAnimationFrame + setTimeout
     */

    // Storage key prefix for sessionStorage
    var SCROLL_DATA_KEY = 'pds_scroll_data';

    /**
     * Data structure for storing scroll positions and filled-state per page
     * {
     *   page1: { scrollY: 123, hasData: true, timestamp: 1234567890 },
     *   page2: { scrollY: 0, hasData: false, timestamp: 1234567891 },
     *   ...
     * }
     */
    var scrollData = {};

    // Debounce timer for scroll events
    var scrollDebounceTimer = null;
    var SCROLL_DEBOUNCE_MS = 100;

    /**
     * Initialize scroll data from sessionStorage
     */
    function initScrollData() {
        try {
            var stored = sessionStorage.getItem(SCROLL_DATA_KEY);
            if (stored) {
                scrollData = JSON.parse(stored);
            }
        } catch (e) {
            scrollData = {};
        }
        // Ensure all pages have entries
        for (var i = 1; i <= STEP_COUNT; i++) {
            var key = 'page' + i;
            if (!scrollData[key]) {
                scrollData[key] = { scrollY: 0, hasData: false, timestamp: 0 };
            }
        }
    }

    /**
     * Persist scroll data to sessionStorage
     */
    function persistScrollData() {
        try {
            sessionStorage.setItem(SCROLL_DATA_KEY, JSON.stringify(scrollData));
        } catch (e) {
            // sessionStorage not available or quota exceeded
        }
    }

    /**
     * Get the storage key for a specific page
     * @param {number} pageId - Page number (1-6)
     * @returns {string} - Storage key
     */
    function getPageKey(pageId) {
        return 'page' + pageId;
    }

    /**
     * Save current scroll position for a page
     * @param {number} pageId - Page number (1-6)
     */
    function saveScroll(pageId) {
        var key = getPageKey(pageId);
        if (!scrollData[key]) {
            scrollData[key] = { scrollY: 0, hasData: false, timestamp: 0 };
        }
        scrollData[key].scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
        scrollData[key].timestamp = Date.now();
        persistScrollData();
    }

    /**
     * Check if a page has any filled fields
     * @param {number} pageId - Page number (1-6)
     * @returns {boolean} - True if page has filled data
     */
    function pageHasData(pageId) {
        var stepEl = document.getElementById('pds-step-' + pageId);
        if (!stepEl) return false;

        // Check all input types that could have data
        var inputs = stepEl.querySelectorAll('input, select, textarea');
        for (var i = 0; i < inputs.length; i++) {
            var input = inputs[i];

            // Skip hidden inputs and buttons
            if (input.type === 'hidden' || input.type === 'button' || input.type === 'submit') {
                continue;
            }

            // Check checkboxes and radio buttons
            if (input.type === 'checkbox' || input.type === 'radio') {
                if (input.checked) return true;
                continue;
            }

            // Check text inputs, textareas, selects
            if (input.value && input.value.trim() !== '') {
                return true;
            }
        }

        // Check for dynamic content (child cards, eligibility cards, etc.)
        var dynamicCards = stepEl.querySelectorAll('[data-child-card], [data-eligibility-card], [data-work-card], [data-voluntary-card], [data-ld-card]');
        if (dynamicCards.length > 0) {
            // If there are more than 1 card, or if the first card has data
            if (dynamicCards.length > 1) return true;
            // Check if the single card has any filled inputs
            var cardInputs = dynamicCards[0].querySelectorAll('input, select, textarea');
            for (var j = 0; j < cardInputs.length; j++) {
                var cardInput = cardInputs[j];
                if (cardInput.type === 'hidden') continue;
                if (cardInput.type === 'checkbox' || cardInput.type === 'radio') {
                    if (cardInput.checked) return true;
                } else if (cardInput.value && cardInput.value.trim() !== '') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Update the hasData flag for a page
     * @param {number} pageId - Page number (1-6)
     */
    function updatePageDataFlag(pageId) {
        var key = getPageKey(pageId);
        if (!scrollData[key]) {
            scrollData[key] = { scrollY: 0, hasData: false, timestamp: 0 };
        }
        scrollData[key].hasData = pageHasData(pageId);
        persistScrollData();
    }

    /**
     * Restore scroll position for a page
     * @param {number} pageId - Page number (1-6)
     * @param {string} mode - Navigation mode: 'next', 'back', or 'direct'
     */
    function restoreScroll(pageId, mode) {
        // Always scroll to top for forward navigation
        if (mode === 'next') {
            window.scrollTo({ top: 0, behavior: 'auto' });
            return;
        }

        // For back/direct navigation, check if page has data
        var key = getPageKey(pageId);
        var pageData = scrollData[key];

        // If no data stored or page has no filled fields, go to top
        if (!pageData || !pageData.hasData) {
            window.scrollTo({ top: 0, behavior: 'auto' });
            return;
        }

        // Restore saved scroll position
        // Use requestAnimationFrame + setTimeout to ensure DOM is fully rendered
        requestAnimationFrame(function() {
            setTimeout(function() {
                // Double-check the page still has data (in case fields were cleared)
                if (pageHasData(pageId)) {
                    var scrollY = pageData.scrollY || 0;
                    // Ensure we don't scroll past document height
                    var maxScroll = document.documentElement.scrollHeight - window.innerHeight;
                    if (scrollY > maxScroll) scrollY = Math.max(0, maxScroll);
                    window.scrollTo({ top: scrollY, behavior: 'auto' });
                } else {
                    window.scrollTo({ top: 0, behavior: 'auto' });
                }
            }, 0);
        });
    }

    /**
     * Debounced scroll event handler
     */
    function onScroll() {
        if (scrollDebounceTimer) {
            clearTimeout(scrollDebounceTimer);
        }
        scrollDebounceTimer = setTimeout(function() {
            saveScroll(currentStep);
            updatePageDataFlag(currentStep);
        }, SCROLL_DEBOUNCE_MS);
    }

    /**
     * Attach scroll listener with cleanup
     */
    function attachScrollListener() {
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /**
     * Navigate to a specific step with proper scroll handling
     * @param {number} step - Target step (1-6)
     * @param {string} mode - Navigation mode: 'next', 'back', or 'direct'
     */
    function goToStepWithScroll(step, mode) {
        // Save current page state before leaving
        saveScroll(currentStep);
        updatePageDataFlag(currentStep);

        // Perform the step transition
        goToStep(step);

        // Handle scroll restoration after transition
        restoreScroll(step, mode);
    }

    // Initialize scroll data on load
    initScrollData();
    attachScrollListener();

    // Save scroll position before page unload
    window.addEventListener('beforeunload', function() {
        saveScroll(currentStep);
        updatePageDataFlag(currentStep);
    });

    /**
     * ============================================
     * COLLAPSIBLE SECTIONS MANAGER
     * ============================================
     * Handles expandable/collapsible sections for multi-entry pages
     * (Step 3: Work & Eligibility, Step 4: Voluntary, L&D & Other)
     */

    /**
     * Toggle collapsible section expand/collapse
     * @param {string} sectionId - The section element ID
     */
    window.toggleSection = function(sectionId) {
        var section = document.getElementById(sectionId);
        if (!section) return;
        
        var isExpanded = section.classList.contains('expanded');
        
        if (isExpanded) {
            section.classList.remove('expanded');
            // Show summary when collapsed and has data
            updateSectionSummary(sectionId);
        } else {
            section.classList.add('expanded');
            // Hide summary when expanded
            var summary = section.querySelector('.pds-section-summary');
            if (summary) summary.style.display = 'none';
        }
        
        // Save preference to sessionStorage
        try {
            var sectionKey = 'pds_section_' + sectionId;
            sessionStorage.setItem(sectionKey, isExpanded ? 'collapsed' : 'expanded');
        } catch (e) {}
    };
    
    /**
     * Update section summary and badge based on card data
     * @param {string} sectionId - The section element ID
     */
    function updateSectionSummary(sectionId) {
        var section = document.getElementById(sectionId);
        if (!section) return;
        
        var cardsContainer = section.querySelector('[id$="Cards"]');
        var summary = section.querySelector('.pds-section-summary');
        var badge = section.querySelector('.pds-collapsible-badge');
        var countSpan = badge ? badge.querySelector('.count') : null;
        
        // Count entries based on card elements
        var entryCount = 0;
        
        // For children section, check if "I have no children" is checked
        if (sectionId === 'childrenSection') {
            var noChildrenCheck = document.getElementById('pdsNoChildren');
            if (noChildrenCheck && noChildrenCheck.checked) {
                entryCount = 0; // Force 0 when user declares no children
            } else if (cardsContainer) {
                var cards = cardsContainer.querySelectorAll('[data-child-card]');
                cards.forEach(function(card) {
                    var inputs = card.querySelectorAll('input, textarea, select');
                    var hasData = false;
                    for (var i = 0; i < inputs.length; i++) {
                        var input = inputs[i];
                        if (input.type === 'hidden') continue;
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            if (input.checked) { hasData = true; break; }
                        } else if (input.value && input.value.trim() !== '') {
                            hasData = true; break;
                        }
                    }
                    if (hasData) entryCount++;
                });
            }
        } else if (cardsContainer) {
            var cards = cardsContainer.querySelectorAll('[data-eligibility-card], [data-work-card], [data-voluntary-card], [data-ld-card]');
            
            // Check if cards have actual data (not just empty placeholders)
            cards.forEach(function(card) {
                var inputs = card.querySelectorAll('input, textarea, select');
                var hasData = false;
                for (var i = 0; i < inputs.length; i++) {
                    var input = inputs[i];
                    if (input.type === 'hidden') continue;
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        if (input.checked) { hasData = true; break; }
                    } else if (input.value && input.value.trim() !== '') {
                        hasData = true; break;
                    }
                }
                if (hasData) entryCount++;
            });
        }
        
        // Update badge
        if (badge && countSpan) {
            countSpan.textContent = entryCount;
            badge.style.display = entryCount > 0 ? 'inline-flex' : 'none';
        }
        
        // Update summary
        if (summary) {
            var summaryCount = summary.querySelector('[id$="SummaryCount"]');
            if (summaryCount) summaryCount.textContent = entryCount;
            summary.style.display = (!section.classList.contains('expanded') && entryCount > 0) ? 'flex' : 'none';
        }
        
        // Add/remove has-data class
        if (entryCount > 0) {
            section.classList.add('has-data');
        } else {
            section.classList.remove('has-data');
        }
    }
    
    /**
     * Initialize collapsible sections on page load
     */
    function initCollapsibleSections() {
        var sections = document.querySelectorAll('.pds-collapsible-section');
        sections.forEach(function(section) {
            var sectionId = section.id;
            
            // Restore preference from sessionStorage
            try {
                var sectionKey = 'pds_section_' + sectionId;
                var preference = sessionStorage.getItem(sectionKey);
                
                if (preference === 'collapsed') {
                    section.classList.remove('expanded');
                    updateSectionSummary(sectionId);
                }
            } catch (e) {}
        });
    }
    
    /**
     * Update all collapsible section summaries (call after cards are added/removed)
     */
    window.updateAllCollapsibleSummaries = function() {
        var sections = ['eligibilitySection', 'workSection', 'voluntarySection', 'ldSection', 'childrenSection'];
        sections.forEach(function(sectionId) {
            updateSectionSummary(sectionId);
        });
    };

    function goToStep(step) {
        step = Math.max(1, Math.min(STEP_COUNT, step));
        currentStep = step;
        steps.forEach(function(el) {
            var s = parseInt(el.getAttribute('data-step'), 10);
            el.classList.toggle('active', s === step);
        });
        stepperItems.forEach(function(el) {
            var s = parseInt(el.getAttribute('data-step'), 10);
            el.classList.remove('active', 'done');
            if (s === step) el.classList.add('active');
            else if (s < step) el.classList.add('done');
        });
        if (progressFill) progressFill.style.width = (step / STEP_COUNT * 100) + '%';
        if (progressPct) progressPct.textContent = Math.round(step / STEP_COUNT * 100) + '%';
        if (prevBtn) prevBtn.style.display = step <= 1 ? 'none' : 'inline-block';
        if (nextBtn) nextBtn.style.display = step >= STEP_COUNT ? 'none' : 'inline-block';
        if (submitBtn) submitBtn.style.display = step >= STEP_COUNT ? 'inline-block' : 'none';
    }

    function step1Valid() {
        var s = form.querySelector('input[name="surname"]');
        var f = form.querySelector('input[name="first_name"]');
        return s && f && s.value.trim() !== '' && f.value.trim() !== '';
    }

    stepperItems.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var step = parseInt(this.getAttribute('data-step'), 10);
            if (step <= currentStep || (step === 2 && step1Valid()) || step > 2) {
                // Determine navigation mode for direct navigation
                var mode = step > currentStep ? 'next' : (step < currentStep ? 'back' : 'direct');
                goToStepWithScroll(step, mode);
            }
        });
    });
    if (prevBtn) prevBtn.addEventListener('click', function() { goToStepWithScroll(currentStep - 1, 'back'); });
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (currentStep === 1 && !step1Valid()) {
                form.querySelector('input[name="surname"]').focus();
                return;
            }
            goToStepWithScroll(currentStep + 1, 'next');
        });
    }

    // Save Draft + autosave
    var draftStatus = document.getElementById('pdsDraftStatus');
    var saveDraftBtn = document.getElementById('pdsSaveDraftBtn');
    function syncChildrenInput() {
        var container = document.getElementById('pdsChildrenCards');
        var namesHidden = document.getElementById('pdsChildrenNamesInput');
        var dataHidden = document.getElementById('pdsChildrenDataInput');
        if (!container || !namesHidden) return;
        var parts = [];
        var dataRows = [];
        container.querySelectorAll('[data-child-card]').forEach(function(card) {
            var nameInp = card.querySelector('input[data-child-name]');
            var dobInp = card.querySelector('input[data-child-dob]');
            var nameVal = nameInp ? nameInp.value.trim() : '';
            var dobVal = dobInp ? dobInp.value : '';
            if (nameVal) parts.push(nameVal);
            dataRows.push({ name: nameVal, dob: dobVal || null });
        });
        namesHidden.value = parts.join("\n");
        if (dataHidden) dataHidden.value = JSON.stringify(dataRows);
    }
    function collectFormData() {
        syncChildrenInput();
        return new FormData(form);
    }
    function saveDraft() {
        var fd = collectFormData();
        fd.append('_token', document.querySelector('input[name="_token"]').value);
        var req = new XMLHttpRequest();
        req.open('POST', draftUrl);
        req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        req.setRequestHeader('Accept', 'application/json');
        req.onload = function() {
            if (req.status >= 200 && req.status < 300) {
                if (draftStatus) { draftStatus.textContent = 'Draft saved.'; setTimeout(function() { draftStatus.textContent = ''; }, 3000); }
            } else {
                if (draftStatus) draftStatus.textContent = 'Save failed. Try again.';
            }
        };
        req.onerror = function() { if (draftStatus) draftStatus.textContent = 'Save failed.'; };
        req.send(fd);
    }
    if (saveDraftBtn) saveDraftBtn.addEventListener('click', saveDraft);
    setInterval(saveDraft, 30000);

    // Photo preview (4.5×3.5 cm proportion)
    var photoInput = document.getElementById('pdsPhotoInput');
    var photoPreview = document.getElementById('pdsPhotoPreview');
    var photoPlaceholder = document.getElementById('pdsPhotoPlaceholder');
    if (photoInput && photoPreview && photoPlaceholder) {
        photoInput.addEventListener('change', function() {
            var file = this.files && this.files[0];
            if (file && file.type.indexOf('image/') === 0) {
                var r = new FileReader();
                r.onload = function() {
                    photoPreview.src = r.result;
                    photoPreview.style.display = 'block';
                    photoPlaceholder.style.display = 'none';
                };
                r.readAsDataURL(file);
            } else {
                photoPreview.src = '';
                photoPreview.style.display = 'none';
                photoPlaceholder.style.display = 'flex';
            }
        });
    }

    // Progressive disclosure
    var citizenshipRadios = form.querySelectorAll('input[name="citizenship"]');
    var dualWrap = document.getElementById('pdsDualCitizenshipWrap');
    function toggleDual() {
        var dual = form.querySelector('input[name="citizenship"]:checked');
        if (dualWrap) dualWrap.classList.toggle('hidden', !dual || dual.value !== 'dual');
    }
    citizenshipRadios.forEach(function(r) { r.addEventListener('change', toggleDual); });
    toggleDual();

    var civilSelect = document.getElementById('pdsCivilStatus');
    var spouseWrap = document.getElementById('pdsSpouseWrap');
    var spouseNA = document.getElementById('pdsSpouseNA');
    function toggleSpouse() {
        var v = civilSelect ? civilSelect.value : '';
        var isMarried = (v === 'married');
        if (spouseWrap) spouseWrap.classList.toggle('hidden', !isMarried);
        if (spouseNA) spouseNA.classList.toggle('hidden', isMarried);
    }
    if (civilSelect) civilSelect.addEventListener('change', toggleSpouse);
    toggleSpouse();

    var civilOtherWrap = document.getElementById('pdsCivilStatusOtherWrap');
    function toggleCivilOther() {
        if (civilOtherWrap) civilOtherWrap.classList.toggle('hidden', !civilSelect || civilSelect.value !== 'other');
    }
    if (civilSelect) civilSelect.addEventListener('change', toggleCivilOther);
    toggleCivilOther();

    var noChildrenCheck = document.getElementById('pdsNoChildren');
    var childrenListWrap = document.getElementById('pdsChildrenListWrap');
    function toggleChildrenList() {
        if (childrenListWrap) childrenListWrap.classList.toggle('hidden', noChildrenCheck && noChildrenCheck.checked);
        // Update section summary to reflect 'no children' state
        updateSectionSummary('childrenSection');
    }
    if (noChildrenCheck) noChildrenCheck.addEventListener('change', toggleChildrenList);
    toggleChildrenList();

    // Page 4: show/hide "If YES" details by radio
    function refreshPage4Conditionals() {
        form.querySelectorAll('[data-show-when][data-show-value]').forEach(function(wrap) {
            if (wrap.hasAttribute('data-show-any')) return;
            var name = wrap.getAttribute('data-show-when');
            var want = wrap.getAttribute('data-show-value');
            var radio = form.querySelector('input[name="' + name + '"][value="' + want + '"]');
            wrap.classList.toggle('hidden', !radio || !radio.checked);
        });
        form.querySelectorAll('[data-show-any][data-show-value]').forEach(function(wrap) {
            var names = wrap.getAttribute('data-show-any').split(',').map(function(s) { return s.trim(); });
            var want = wrap.getAttribute('data-show-value');
            var anyChecked = names.some(function(name) {
                var radio = form.querySelector('input[name="' + name + '"][value="' + want + '"]');
                return radio && radio.checked;
            });
            wrap.classList.toggle('hidden', !anyChecked);
        });
    }
    form.querySelectorAll('input[type="radio"]').forEach(function(r) {
        if (form.querySelector('[data-show-when="' + r.name + '"]') || form.querySelector('[data-show-any]')) r.addEventListener('change', refreshPage4Conditionals);
    });
    refreshPage4Conditionals();

    // Same as residential
    var sameCheck = document.getElementById('pdsSameAsResidential');
    var resNames = ['residential_house_no','residential_street','residential_subdivision','residential_barangay','residential_city','residential_province','residential_zip'];
    var permIds = ['permanent_house_no','permanent_street','permanent_subdivision','permanent_barangay','permanent_city','permanent_province','permanent_zip'];
    function copyResidentialToPermanent() {
        resNames.forEach(function(name, i) {
            var from = form.querySelector('input[name="' + name + '"]');
            var to = document.getElementById(permIds[i]);
            if (from && to) to.value = from.value;
        });
    }
    function setPermanentReadonly(readonly) {
        permIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.readOnly = readonly;
        });
        var container = form.querySelector('.pds-permanent-fields');
        if (container) container.classList.toggle('pds-permanent-disabled', readonly);
    }
    if (sameCheck) {
        sameCheck.addEventListener('change', function() {
            if (this.checked) { copyResidentialToPermanent(); setPermanentReadonly(true); }
            else setPermanentReadonly(false);
        });
    }

    // Children cards
    var childrenCards = document.getElementById('pdsChildrenCards');
    var addChildBtn = document.getElementById('pdsAddChildBtn');
    var childIndex = {{ count($childrenInitial) }};
    var childrenInitial = @json($childrenInitial);
    function addChildCard(item) {
        if (!childrenCards) return;
        if (typeof item !== 'object' || item === null) item = { name: (item || '').toString(), dob: '' };
        var nameVal = (item.name || '').toString().replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        var dobVal = (item.dob || '').toString();
        var id = 'child-' + (childIndex++);
        var div = document.createElement('div');
        div.className = 'pds-card-repeat';
        div.setAttribute('data-child-card', '');
        div.innerHTML = '<div class="pds-card-repeat-header">' +
            '<span class="card-title-text">Child ' + (childrenCards.querySelectorAll("[data-child-card]").length + 1) + '</span>' +
            '<button type="button" class="pds-btn-remove pds-remove-child" aria-label="Remove"><i class="bi bi-trash"></i></button>' +
            '</div><div class="pds-card-repeat-body">' +
            '<div class="row g-2">' +
            '<div class="col-12 col-md-6"><label class="pds-form-label">Full name</label>' +
            '<input type="text" class="form-control pds-form-control" data-child-name maxlength="255" value="' + nameVal + '" placeholder="Full name of child"></div>' +
            '<div class="col-12 col-md-6"><label class="pds-form-label">DATE OF BIRTH (dd/mm/yyyy)</label>' +
            '<input type="date" class="form-control pds-form-control" data-child-dob value="' + dobVal + '"></div>' +
            '</div></div>';
        childrenCards.appendChild(div);
        div.querySelector('input[data-child-name]').addEventListener('input', function() {
            syncChildrenInput();
            updateSectionSummary('childrenSection');
        });
        div.querySelector('input[data-child-dob]').addEventListener('input', function() {
            syncChildrenInput();
            updateSectionSummary('childrenSection');
        });
        div.querySelector('input[data-child-dob]').addEventListener('change', function() {
            syncChildrenInput();
            updateSectionSummary('childrenSection');
        });
        div.querySelector('.pds-remove-child').addEventListener('click', function() {
            div.remove();
            syncChildrenInput();
            updateAllCollapsibleSummaries();
        });
        syncChildrenInput();
        updateSectionSummary('childrenSection');
    }
    childrenInitial.forEach(function(v) { addChildCard(v); });
    if (childrenInitial.length === 0) addChildCard('');
    if (addChildBtn) addChildBtn.addEventListener('click', function() { addChildCard(''); });
    // Initial summary update for children
    updateSectionSummary('childrenSection');

    // Eligibility cards (collapsible, repeatable)
    var eligibilityRows = @json($eligibilityRows);
    var eligibilityContainer = document.getElementById('pdsEligibilityCards');
    var eligTpl = function(i, e) {
        e = e || {};
        return '<div class="pds-card-repeat" data-eligibility-card>' +
            '<div class="pds-card-repeat-header">' +
            '<span class="card-title-text">' + (e.eligibility_type || 'Eligibility ' + (i + 1)) + '</span>' +
            '<button type="button" class="pds-btn-remove pds-remove-eligibility" aria-label="Remove"><i class="bi bi-trash"></i></button>' +
            '</div><div class="pds-card-repeat-body">' +
            '<div class="row g-2">' +
            '<div class="col-12"><label class="pds-form-label">Eligibility (write in full)</label><input type="text" class="form-control pds-form-control" name="eligibility[' + i + '][eligibility_type]" value="' + (e.eligibility_type || '').replace(/"/g, '&quot;') + '" maxlength="500" placeholder="e.g. Career Service"></div>' +
            '<div class="col-12 col-md-4"><label class="pds-form-label">Rating</label><input type="text" class="form-control pds-form-control" name="eligibility[' + i + '][rating]" value="' + (e.rating || '').replace(/"/g, '&quot;') + '" maxlength="50"></div>' +
            '<div class="col-12 col-md-4"><label class="pds-form-label">Date of exam / conferment</label><input type="date" class="form-control pds-form-control" name="eligibility[' + i + '][date_exam_conferment]" value="' + (e.date_exam_conferment || '') + '"></div>' +
            '<div class="col-12 col-md-4"><label class="pds-form-label">Valid until</label><input type="date" class="form-control pds-form-control" name="eligibility[' + i + '][license_valid_until]" value="' + (e.license_valid_until || '') + '"></div>' +
            '<div class="col-12"><label class="pds-form-label">Place of exam / conferment</label><input type="text" class="form-control pds-form-control" name="eligibility[' + i + '][place_exam_conferment]" value="' + (e.place_exam_conferment || '').replace(/"/g, '&quot;') + '" maxlength="255"></div>' +
            '<div class="col-12"><label class="pds-form-label">License number</label><input type="text" class="form-control pds-form-control" name="eligibility[' + i + '][license_number]" value="' + (e.license_number || '').replace(/"/g, '&quot;') + '" maxlength="100"></div>' +
            '</div></div></div>';
    };
    var eligIndex = eligibilityRows.length;
    eligibilityRows.forEach(function(e, i) {
        eligibilityContainer.insertAdjacentHTML('beforeend', eligTpl(i, e));
    });
    function reindexEligibility() {
        var cards = eligibilityContainer.querySelectorAll('[data-eligibility-card]');
        cards.forEach(function(card, i) {
            card.querySelectorAll('input').forEach(function(inp) {
                var n = inp.getAttribute('name');
                if (n && n.indexOf('eligibility[') === 0) inp.setAttribute('name', n.replace(/eligibility\[\d+\]/, 'eligibility[' + i + ']'));
            });
            var title = card.querySelector('.card-title-text');
            if (title) title.textContent = 'Eligibility ' + (i + 1);
        });
    }
    document.getElementById('pdsAddEligibilityBtn').addEventListener('click', function() {
        eligibilityContainer.insertAdjacentHTML('beforeend', eligTpl(eligIndex++, {}));
        var newCard = eligibilityContainer.lastElementChild;
        newCard.querySelector('.pds-card-repeat-header').addEventListener('click', function() { newCard.classList.toggle('collapsed'); });
        newCard.querySelector('.pds-remove-eligibility').addEventListener('click', function() {
            if (eligibilityContainer.querySelectorAll('[data-eligibility-card]').length <= 1) return;
            newCard.remove();
            reindexEligibility();
            updateAllCollapsibleSummaries();
        });
        // Update summary when input changes
        newCard.querySelectorAll('input').forEach(function(inp) {
            inp.addEventListener('input', function() { updateSectionSummary('eligibilitySection'); });
        });
        reindexEligibility();
        updateSectionSummary('eligibilitySection');
    });
    eligibilityContainer.querySelectorAll('[data-eligibility-card]').forEach(function(card) {
        card.querySelector('.pds-card-repeat-header').addEventListener('click', function() { card.classList.toggle('collapsed'); });
        card.querySelector('.pds-remove-eligibility').addEventListener('click', function() {
            if (eligibilityContainer.querySelectorAll('[data-eligibility-card]').length <= 1) return;
            card.remove();
            reindexEligibility();
            updateAllCollapsibleSummaries();
        });
        // Update summary when input changes
        card.querySelectorAll('input').forEach(function(inp) {
            inp.addEventListener('input', function() { updateSectionSummary('eligibilitySection'); });
        });
    });
    // Initial summary update for eligibility
    updateSectionSummary('eligibilitySection');

    // Work experience cards
    var workRows = @json($workRows);
    var workContainer = document.getElementById('pdsWorkCards');
    var workTpl = function(i, w) {
        w = w || {};
        return '<div class="pds-card-repeat" data-work-card>' +
            '<div class="pds-card-repeat-header">' +
            '<span class="card-title-text">' + (w.position_title || 'Work ' + (i + 1)) + '</span>' +
            '<button type="button" class="pds-btn-remove pds-remove-work" aria-label="Remove"><i class="bi bi-trash"></i></button>' +
            '</div><div class="pds-card-repeat-body">' +
            '<div class="row g-2">' +
            '<div class="col-12 col-md-6"><label class="pds-form-label">From date</label><input type="date" class="form-control pds-form-control" name="work[' + i + '][from_date]" value="' + (w.from_date || '') + '"></div>' +
            '<div class="col-12 col-md-6"><label class="pds-form-label">To date</label><input type="date" class="form-control pds-form-control" name="work[' + i + '][to_date]" value="' + (w.to_date || '') + '"></div>' +
            '<div class="col-12"><label class="pds-form-label">Position title (write in full)</label><input type="text" class="form-control pds-form-control" name="work[' + i + '][position_title]" value="' + (w.position_title || '').replace(/"/g, '&quot;') + '" maxlength="255" placeholder="e.g. Teacher I"></div>' +
            '<div class="col-12"><label class="pds-form-label">Department / Agency / Company</label><input type="text" class="form-control pds-form-control" name="work[' + i + '][department_agency]" value="' + (w.department_agency || '').replace(/"/g, '&quot;') + '" maxlength="500" placeholder="e.g. DepEd Division Office"></div>' +
            '<div class="col-12 col-md-6"><label class="pds-form-label">Status of appointment</label><input type="text" class="form-control pds-form-control" name="work[' + i + '][status_of_appointment]" value="' + (w.status_of_appointment || '').replace(/"/g, '&quot;') + '" maxlength="100" placeholder="e.g. Permanent"></div>' +
            '<div class="col-12 col-md-6"><label class="pds-form-label">Government service (Y/N)</label><select class="form-select pds-form-control" name="work[' + i + '][govt_service_yn]"><option value="">—</option><option value="Y"' + (w.govt_service_yn === 'Y' ? ' selected' : '') + '>Yes</option><option value="N"' + (w.govt_service_yn === 'N' ? ' selected' : '') + '>No</option></select></div>' +
            '</div></div></div>';
    };
    var workIndex = workRows.length;
    workRows.forEach(function(w, i) {
        workContainer.insertAdjacentHTML('beforeend', workTpl(i, w));
    });
    function reindexWork() {
        var cards = workContainer.querySelectorAll('[data-work-card]');
        cards.forEach(function(card, i) {
            card.querySelectorAll('input, select').forEach(function(el) {
                var n = el.getAttribute('name');
                if (n && n.indexOf('work[') === 0) el.setAttribute('name', n.replace(/work\[\d+\]/, 'work[' + i + ']'));
            });
            var title = card.querySelector('.card-title-text');
            if (title) title.textContent = 'Work ' + (i + 1);
        });
    }
    document.getElementById('pdsAddWorkBtn').addEventListener('click', function() {
        workContainer.insertAdjacentHTML('beforeend', workTpl(workIndex++, {}));
        var newCard = workContainer.lastElementChild;
        newCard.querySelector('.pds-card-repeat-header').addEventListener('click', function() { newCard.classList.toggle('collapsed'); });
        newCard.querySelector('.pds-remove-work').addEventListener('click', function() {
            if (workContainer.querySelectorAll('[data-work-card]').length <= 1) return;
            newCard.remove();
            reindexWork();
            updateAllCollapsibleSummaries();
        });
        // Update summary when input changes
        newCard.querySelectorAll('input, select').forEach(function(inp) {
            inp.addEventListener('input', function() { updateSectionSummary('workSection'); });
            inp.addEventListener('change', function() { updateSectionSummary('workSection'); });
        });
        reindexWork();
        updateSectionSummary('workSection');
    });
    workContainer.querySelectorAll('[data-work-card]').forEach(function(card) {
        card.querySelector('.pds-card-repeat-header').addEventListener('click', function() { card.classList.toggle('collapsed'); });
        card.querySelector('.pds-remove-work').addEventListener('click', function() {
            if (workContainer.querySelectorAll('[data-work-card]').length <= 1) return;
            card.remove();
            reindexWork();
            updateAllCollapsibleSummaries();
        });
        // Update summary when input changes
        card.querySelectorAll('input, select').forEach(function(inp) {
            inp.addEventListener('input', function() { updateSectionSummary('workSection'); });
            inp.addEventListener('change', function() { updateSectionSummary('workSection'); });
        });
    });
    // Initial summary update for work
    updateSectionSummary('workSection');

    // Voluntary work cards
    var voluntaryRows = @json($voluntaryRows);
    var voluntaryContainer = document.getElementById('pdsVoluntaryCards');
    var voluntaryTpl = function(i, v) {
        v = v || {};
        return '<div class="pds-card-repeat" data-voluntary-card>' +
            '<div class="pds-card-repeat-header">' +
            '<span class="card-title-text">' + (v.conducted_sponsored_by || 'Voluntary ' + (i + 1)) + '</span>' +
            '<button type="button" class="pds-btn-remove pds-remove-voluntary" aria-label="Remove"><i class="bi bi-trash"></i></button>' +
            '</div><div class="pds-card-repeat-body">' +
            '<div class="row g-2">' +
            '<div class="col-12"><label class="pds-form-label">29. Name &amp; Address of Organization (Write in full)</label><input type="text" class="form-control pds-form-control" name="voluntary[' + i + '][conducted_sponsored_by]" value="' + (v.conducted_sponsored_by || '').replace(/"/g, '&quot;') + '" maxlength="500"></div>' +
            '<div class="col-12 col-md-3"><label class="pds-form-label">Inclusive dates from</label><input type="date" class="form-control pds-form-control" name="voluntary[' + i + '][inclusive_dates_from]" value="' + (v.inclusive_dates_from || '') + '"></div>' +
            '<div class="col-12 col-md-3"><label class="pds-form-label">Inclusive dates to</label><input type="date" class="form-control pds-form-control" name="voluntary[' + i + '][inclusive_dates_to]" value="' + (v.inclusive_dates_to || '') + '"></div>' +
            '<div class="col-12 col-md-3"><label class="pds-form-label">Number of hours</label><input type="number" class="form-control pds-form-control" name="voluntary[' + i + '][number_of_hours]" value="' + (v.number_of_hours || '') + '" min="0" placeholder="0"></div>' +
            '<div class="col-12 col-md-3"><label class="pds-form-label">Position / Nature of work</label><input type="text" class="form-control pds-form-control" name="voluntary[' + i + '][position_nature_of_work]" value="' + (v.position_nature_of_work || '').replace(/"/g, '&quot;') + '" maxlength="255"></div>' +
            '</div></div></div>';
    };
    var voluntaryIndex = voluntaryRows.length;
    voluntaryRows.forEach(function(v, i) { voluntaryContainer.insertAdjacentHTML('beforeend', voluntaryTpl(i, v)); });
    function reindexVoluntary() {
        var cards = voluntaryContainer.querySelectorAll('[data-voluntary-card]');
        cards.forEach(function(card, i) {
            card.querySelectorAll('input').forEach(function(inp) {
                var n = inp.getAttribute('name');
                if (n && n.indexOf('voluntary[') === 0) inp.setAttribute('name', n.replace(/voluntary\[\d+\]/, 'voluntary[' + i + ']'));
            });
            var title = card.querySelector('.card-title-text');
            if (title) title.textContent = 'Voluntary ' + (i + 1);
        });
    }
    document.getElementById('pdsAddVoluntaryBtn').addEventListener('click', function() {
        voluntaryContainer.insertAdjacentHTML('beforeend', voluntaryTpl(voluntaryIndex++, {}));
        var newCard = voluntaryContainer.lastElementChild;
        newCard.querySelector('.pds-card-repeat-header').addEventListener('click', function() { newCard.classList.toggle('collapsed'); });
        newCard.querySelector('.pds-remove-voluntary').addEventListener('click', function() {
            if (voluntaryContainer.querySelectorAll('[data-voluntary-card]').length <= 1) return;
            newCard.remove();
            reindexVoluntary();
            updateAllCollapsibleSummaries();
        });
        // Update summary when input changes
        newCard.querySelectorAll('input').forEach(function(inp) {
            inp.addEventListener('input', function() { updateSectionSummary('voluntarySection'); });
        });
        reindexVoluntary();
        updateSectionSummary('voluntarySection');
    });
    voluntaryContainer.querySelectorAll('[data-voluntary-card]').forEach(function(card) {
        card.querySelector('.pds-card-repeat-header').addEventListener('click', function() { card.classList.toggle('collapsed'); });
        card.querySelector('.pds-remove-voluntary').addEventListener('click', function() {
            if (voluntaryContainer.querySelectorAll('[data-voluntary-card]').length <= 1) return;
            card.remove();
            reindexVoluntary();
            updateAllCollapsibleSummaries();
        });
        // Update summary when input changes
        card.querySelectorAll('input').forEach(function(inp) {
            inp.addEventListener('input', function() { updateSectionSummary('voluntarySection'); });
        });
    });
    // Initial summary update for voluntary
    updateSectionSummary('voluntarySection');

    // Learning development cards
    var ldRows = @json($ldRows);
    var ldContainer = document.getElementById('pdsLdCards');
    var importLdUrl = @json($importLdUrl);
    var ldTypes = ['Managerial', 'Supervisory', 'Technical', 'Other'];
    var ldTpl = function(i, l) {
        l = l || {};
        var typeOpts = ldTypes.map(function(t) {
            var sel = (l.type_of_ld || '') === t ? ' selected' : '';
            return '<option value="' + t + '"' + sel + '>' + t + '</option>';
        }).join('');
        if (l.type_of_ld && ldTypes.indexOf(l.type_of_ld) === -1) {
            typeOpts += '<option value="' + (l.type_of_ld || '').replace(/"/g, '&quot;') + '" selected>' + (l.type_of_ld || '').replace(/</g, '&lt;') + '</option>';
        }
        return '<div class="pds-card-repeat" data-ld-card>' +
            '<div class="pds-card-repeat-header">' +
            '<span class="card-title-text">' + (l.title_of_ld || 'L&amp;D ' + (i + 1)) + '</span>' +
            '<button type="button" class="pds-btn-remove pds-remove-ld" aria-label="Remove"><i class="bi bi-trash"></i></button>' +
            '</div><div class="pds-card-repeat-body">' +
            '<div class="row g-2">' +
            '<div class="col-12"><label class="pds-form-label">30. Title of L&amp;D Interventions/Training Programs (Write in full)</label><input type="text" class="form-control pds-form-control" name="learning_development[' + i + '][title_of_ld]" value="' + (l.title_of_ld || '').replace(/"/g, '&quot;') + '" maxlength="500"></div>' +
            '<div class="col-12"><label class="pds-form-label">CONDUCTED/ SPONSORED BY (Write in full)</label><input type="text" class="form-control pds-form-control" name="learning_development[' + i + '][organization_name_address]" value="' + (l.organization_name_address || '').replace(/"/g, '&quot;') + '" maxlength="500" placeholder="Name and address of organization that conducted or sponsored the training"></div>' +
            '<div class="col-12 col-md-4"><label class="pds-form-label">31. Type of L&amp;D</label><select class="form-select pds-form-control pds-ld-type-select" name="learning_development[' + i + '][type_of_ld]"><option value="">— Select —</option>' + typeOpts + '</select></div>' +
            '<div class="col-12 col-md-4 pds-ld-other-wrap pds-conditional' + ((l.type_of_ld || '') === 'Other' ? '' : ' hidden') + '"><label class="pds-form-label">If Other, please specify</label><input type="text" class="form-control pds-form-control" name="learning_development[' + i + '][type_of_ld_specify]" value="' + (l.type_of_ld_specify || '').replace(/"/g, '&quot;') + '" maxlength="100" placeholder="Specify type"></div>' +
            '<div class="col-12 col-md-4"><label class="pds-form-label">Number of hours</label><input type="number" class="form-control pds-form-control" name="learning_development[' + i + '][number_of_hours]" value="' + (l.number_of_hours || '') + '" min="0" placeholder="0"></div>' +
            '<div class="col-12 col-md-4"><label class="pds-form-label">Inclusive dates from</label><input type="date" class="form-control pds-form-control" name="learning_development[' + i + '][inclusive_dates_from]" value="' + (l.inclusive_dates_from || '') + '"></div>' +
            '<div class="col-12 col-md-4"><label class="pds-form-label">Inclusive dates to</label><input type="date" class="form-control pds-form-control" name="learning_development[' + i + '][inclusive_dates_to]" value="' + (l.inclusive_dates_to || '') + '"></div>' +
            '</div></div></div>';
    };
    var ldIndex = ldRows.length;
    var ldEmptyState = document.getElementById('pdsLdEmptyState');
    function updateLdEmptyState() {
        var hasCards = ldContainer.querySelectorAll('[data-ld-card]').length > 0;
        if (ldEmptyState) ldEmptyState.style.display = hasCards ? 'none' : 'block';
    }
    ldRows.forEach(function(l, i) { ldContainer.insertAdjacentHTML('beforeend', ldTpl(i, l)); });
    ldContainer.querySelectorAll('[data-ld-card]').forEach(function(card) { attachLdCardListeners(card); });
    updateLdEmptyState();
    function reindexLd() {
        var cards = ldContainer.querySelectorAll('[data-ld-card]');
        cards.forEach(function(card, i) {
            card.querySelectorAll('input, select').forEach(function(el) {
                var n = el.getAttribute('name');
                if (n && n.indexOf('learning_development[') === 0) el.setAttribute('name', n.replace(/learning_development\[\d+\]/, 'learning_development[' + i + ']'));
            });
            var titleEl = card.querySelector('.card-title-text');
            var titleInp = card.querySelector('input[name*="[title_of_ld]"]');
            if (titleEl) titleEl.textContent = (titleInp && titleInp.value.trim()) ? titleInp.value.trim() : 'L&D ' + (i + 1);
        });
    }
    function attachLdCardListeners(card) {
        card.querySelector('.pds-card-repeat-header').addEventListener('click', function() { card.classList.toggle('collapsed'); });
        card.querySelector('.pds-remove-ld').addEventListener('click', function(e) {
            e.stopPropagation();
            var cardCount = ldContainer.querySelectorAll('[data-ld-card]').length;
            if (cardCount === 1) {
                // Last card: clear all inputs instead of removing
                card.querySelectorAll('input, select').forEach(function(el) {
                    if (el.tagName === 'SELECT') {
                        el.selectedIndex = 0;
                    } else {
                        el.value = '';
                    }
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                });
                // Hide the "Other" specify field if visible
                var otherWrap = card.querySelector('.pds-ld-other-wrap');
                if (otherWrap) otherWrap.classList.add('hidden');
                // Update title
                var titleEl = card.querySelector('.card-title-text');
                if (titleEl) titleEl.textContent = 'L&D 1';
                updateSectionSummary('ldSection');
            } else {
                card.remove();
                reindexLd();
                updateLdEmptyState();
                updateAllCollapsibleSummaries();
            }
        });
        var titleInp = card.querySelector('input[name*="[title_of_ld]"]');
        if (titleInp) titleInp.addEventListener('input', function() {
            var t = card.querySelector('.card-title-text');
            if (t) t.textContent = this.value.trim() || 'L&D ' + (Array.prototype.indexOf.call(ldContainer.querySelectorAll('[data-ld-card]'), card) + 1);
        });
        // Update summary when any input changes
        card.querySelectorAll('input, select').forEach(function(inp) {
            inp.addEventListener('input', function() { updateSectionSummary('ldSection'); });
            inp.addEventListener('change', function() { updateSectionSummary('ldSection'); });
        });
    }
    document.getElementById('pdsAddLdBtn').addEventListener('click', function() {
        ldContainer.insertAdjacentHTML('beforeend', ldTpl(ldIndex++, {}));
        var newCard = ldContainer.lastElementChild;
        attachLdCardListeners(newCard);
        reindexLd();
        updateLdEmptyState();
        updateSectionSummary('ldSection');
        // Scroll to the new card
        newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    // Initial summary update for L&D
    updateSectionSummary('ldSection');
    document.getElementById('pdsImportLdBtn').addEventListener('click', function() {
        var btn = this;
        var origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Loading...';
        fetch(importLdUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                var items = res.data || [];
                if (items.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Trainings',
                        text: 'No trainings found in your record.',
                        confirmButtonColor: 'var(--deped-primary)',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                items.forEach(function(l) {
                    ldContainer.insertAdjacentHTML('beforeend', ldTpl(ldIndex++, l));
                    var newCard = ldContainer.lastElementChild;
                    attachLdCardListeners(newCard);
                });
                reindexLd();
                updateLdEmptyState();
                updateSectionSummary('ldSection');
                if (items.length > 0) {
                    var firstNew = ldContainer.querySelectorAll('[data-ld-card]')[ldContainer.querySelectorAll('[data-ld-card]').length - items.length];
                    if (firstNew) firstNew.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            })
            .catch(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load trainings. Please try again.',
                    confirmButtonColor: 'var(--deped-primary)',
                    confirmButtonText: 'OK'
                });
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            });
    });
    ldContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('pds-ld-type-select')) {
            var wrap = e.target.closest('.pds-card-repeat-body').querySelector('.pds-ld-other-wrap');
            if (wrap) wrap.classList.toggle('hidden', e.target.value !== 'Other');
        }
    });

    // Input masks (simple)
    form.querySelectorAll('.pds-mask-tin').forEach(function(inp) {
        inp.addEventListener('input', function() {
            var v = this.value.replace(/\D/g, '').slice(0, 12);
            this.value = v.replace(/(\d{3})(\d{3})(\d{3})(\d{3})/, '$1-$2-$3-$4').replace(/-$/, '');
        });
    });
    form.querySelectorAll('.pds-mask-philhealth').forEach(function(inp) {
        inp.addEventListener('input', function() {
            var v = this.value.replace(/\D/g, '').slice(0, 12);
            if (v.length <= 2) this.value = v;
            else if (v.length <= 11) this.value = v.slice(0,2) + '-' + v.slice(2);
            else this.value = v.slice(0,2) + '-' + v.slice(2,11) + '-' + v.slice(11,12);
        });
    });
    form.querySelectorAll('.pds-input-phone').forEach(function(inp) {
        inp.addEventListener('input', function() {
            this.value = this.value.replace(/[^\d\s\+\-]/g, '');
        });
    });

    // Scroll to first error on load (override scroll restoration for validation errors)
    var firstInvalid = form.querySelector('.is-invalid');
    if (firstInvalid) {
        var stepEl = firstInvalid.closest('.pds-step-section');
        if (stepEl) {
            var errorStep = parseInt(stepEl.getAttribute('data-step'), 10);
            currentStep = errorStep;
            goToStep(errorStep);
        }
        setTimeout(function() { firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
    } else {
        // On initial load without errors, ensure we start at top
        window.scrollTo({ top: 0, behavior: 'auto' });
    }

    // Sync children before submit; confirm before final submit
    form.addEventListener('submit', function(e) {
        syncChildrenInput();
        if (form.dataset.allowSubmit === '1') return;
        e.preventDefault();
        Swal.fire({
            title: 'Save and submit?',
            text: 'This will finalize your Personal Data Sheet. You can still edit it later if needed.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1E35FF',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, save and submit'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.dataset.allowSubmit = '1';
                form.submit();
            }
        });
    });

    @if(session('success'))
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'success', title: 'Saved', text: @json(session('success')), confirmButtonColor: '#1E35FF' });
    }
    @endif

    // Initialize collapsible sections on page load
    initCollapsibleSections();
})();
</script>
@endpush
@endsection
