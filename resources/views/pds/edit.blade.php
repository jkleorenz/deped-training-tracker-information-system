@extends('layouts.app')

@section('title', ($isOwn ? 'My' : $user->name . "'s") . ' Personal Data Sheet - ' . config('app.name'))

@php
    $draftUrl = $isOwn ? route('pds.draft') : route('personnel.pds.draft', $user);
    $updateUrl = $isOwn ? route('pds.update') : route('personnel.pds.update', $user);
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
                'date_exam_conferment' => $e->date_exam_conferment ? $e->date_exam_conferment->format('Y-m-d') : null,
                'place_exam_conferment' => $e->place_exam_conferment ?? '',
                'license_number' => $e->license_number ?? '',
                'license_valid_until' => $e->license_valid_until ? $e->license_valid_until->format('Y-m-d') : null,
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
                'from_date' => $w->from_date ? $w->from_date->format('Y-m-d') : null,
                'to_date' => $w->to_date ? $w->to_date->format('Y-m-d') : null,
                'position_title' => $w->position_title ?? '',
                'department_agency' => $w->department_agency ?? '',
                'status_of_appointment' => $w->status_of_appointment ?? '',
                'govt_service_yn' => $w->govt_service_yn ?? '',
            ])->values()->all();
    }
    $childrenInitial = $pds->children_names ? array_map('trim', preg_split('/[\r\n,]+/', $pds->children_names, -1, PREG_SPLIT_NO_EMPTY)) : [];
    if (old('children_names')) {
        $childrenInitial = array_map('trim', preg_split('/[\r\n,]+/', old('children_names'), -1, PREG_SPLIT_NO_EMPTY));
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
            <p class="text-muted small mb-0">Complete in 4 steps. Use <strong>Save Draft</strong> or auto-save to keep progress. PDF export matches the official PDS layout.</p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('reports.pds-pdf', $isOwn ? [] : ['user_id' => $user->id]) }}" class="btn btn-deped" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i> Print PDS</a>
            @if(!$isOwn)
                <a href="{{ route('personnel.show', $user) }}" class="btn btn-outline-secondary">Back to profile</a>
            @endif
        </div>
    </div>
</div>

{{-- 4-step stepper --}}
<nav class="pds-stepper" aria-label="PDS steps">
    <button type="button" class="pds-stepper-item active" data-step="1" aria-current="step">
        <span class="step-num">1</span><span>Personal Information</span>
    </button>
    <button type="button" class="pds-stepper-item" data-step="2"><span class="step-num">2</span><span>Family &amp; Education</span></button>
    <button type="button" class="pds-stepper-item" data-step="3"><span class="step-num">3</span><span>Work &amp; Eligibility</span></button>
    <button type="button" class="pds-stepper-item" data-step="4"><span class="step-num">4</span><span>Declaration</span></button>
    <div class="pds-progress-bar-wrap">
        <div class="pds-progress-bar-fill" id="pdsProgressFill" style="width: 25%;"></div>
    </div>
    <span class="pds-progress-pct" id="pdsProgressPct">25%</span>
</nav>

<div class="card pds-section-card">
    <div class="card-body">
        <form method="POST" action="{{ $updateUrl }}" id="pdsForm">
            @csrf
            <input type="hidden" name="children_names" id="pdsChildrenNamesInput" value="{{ old('children_names', $pds->children_names) }}">

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
                                <input type="date" class="form-control pds-form-control" name="date_of_birth" value="{{ old('date_of_birth', $pds->date_of_birth?->format('Y-m-d')) }}">
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
                                <input type="number" class="form-control pds-form-control" name="height" value="{{ old('height', $pds->height) }}" step="0.01" min="0" max="3" placeholder="1.65">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="pds-form-label">8. Weight (kg)</label>
                                <input type="number" class="form-control pds-form-control" name="weight" value="{{ old('weight', $pds->weight) }}" step="0.01" min="0" max="500" placeholder="70">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="pds-form-label">9. Blood type</label>
                                <input type="text" class="form-control pds-form-control" name="blood_type" value="{{ old('blood_type', $pds->blood_type) }}" maxlength="10" placeholder="e.g. O">
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
                                <input type="date" class="form-control pds-form-control" name="date_of_appointment" value="{{ old('date_of_appointment', $pds->date_of_appointment?->format('Y-m-d')) }}">
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
                <div class="pds-section-card pds-conditional" id="pdsSpouseWrap">
                    <div class="card-header"><i class="bi bi-person-hearts"></i> II. Family Background — 22. Spouse</div>
                    <div class="card-body">
                        <div class="pds-name-box">
                            <div class="pds-name-box-title">Surname | First name | Middle name</div>
                            <div class="row g-2">
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="spouse_surname" value="{{ old('spouse_surname', $pds->spouse_surname) }}" maxlength="100" placeholder="Surname"></div>
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="spouse_first_name" value="{{ old('spouse_first_name', $pds->spouse_first_name) }}" maxlength="100" placeholder="First name"></div>
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="spouse_middle_name" value="{{ old('spouse_middle_name', $pds->spouse_middle_name) }}" maxlength="100" placeholder="Middle name"></div>
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
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-people"></i> 23. Name of children (full name, list all)</div>
                    <div class="card-body">
                        <label class="d-flex align-items-center gap-2 mb-3">
                            <input type="checkbox" id="pdsNoChildren" autocomplete="off">
                            <span>I have no children</span>
                        </label>
                        <div class="pds-conditional" id="pdsChildrenListWrap">
                            <div id="pdsChildrenCards"></div>
                            <button type="button" class="btn btn-outline-primary btn-sm pds-btn-add-item" id="pdsAddChildBtn"><i class="bi bi-plus-lg"></i> Add child</button>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-person"></i> 24. Father's name</div>
                    <div class="card-body">
                        <div class="pds-name-box">
                            <div class="pds-name-box-title">Surname | First name | Middle name</div>
                            <div class="row g-2">
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="father_surname" value="{{ old('father_surname', $pds->father_surname) }}" maxlength="100" placeholder="Surname"></div>
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="father_first_name" value="{{ old('father_first_name', $pds->father_first_name) }}" maxlength="100" placeholder="First name"></div>
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="father_middle_name" value="{{ old('father_middle_name', $pds->father_middle_name) }}" maxlength="100" placeholder="Middle name"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-person"></i> 25. Mother's maiden name</div>
                    <div class="card-body">
                        <div class="pds-name-box">
                            <div class="pds-name-box-title">Surname | First name | Middle name</div>
                            <div class="row g-2">
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="mother_surname" value="{{ old('mother_surname', $pds->mother_surname) }}" maxlength="100" placeholder="Surname"></div>
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="mother_first_name" value="{{ old('mother_first_name', $pds->mother_first_name) }}" maxlength="100" placeholder="First name"></div>
                                <div class="col-12 col-md-4"><input type="text" class="form-control pds-form-control" name="mother_middle_name" value="{{ old('mother_middle_name', $pds->mother_middle_name) }}" maxlength="100" placeholder="Middle name"></div>
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
                                    <div class="col-12 col-md-6"><label class="pds-form-label">Scholarship / Academic honors</label><input type="text" class="form-control pds-form-control" name="{{ $key }}_scholarship_honors" value="{{ old("{$key}_scholarship_honors", $pds->{"{$key}_scholarship_honors"}) }}" maxlength="255" placeholder="e.g. WITH HONOR"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Step 3: Work Experience & Eligibility --}}
            <div class="pds-step-section" id="pds-step-3" data-step="3">
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-award"></i> IV. Civil Service Eligibility</div>
                    <div class="card-body">
                        <div class="pds-instruction-banner">
                            <i class="bi bi-clipboard-check"></i>
                            <p class="pds-instruction-text">CES/CSEE/Career Service/RA 1080 (Board/Bar)/Under Special Laws/Category II/IV Eligibility and eligibilities for uniformed personnel. (Continue on separate sheet if necessary.)</p>
                        </div>
                        <div id="pdsEligibilityCards"></div>
                        <button type="button" class="btn btn-deped btn-sm pds-btn-add-item" id="pdsAddEligibilityBtn"><i class="bi bi-plus-lg"></i> Add eligibility</button>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-briefcase"></i> V. Work Experience</div>
                    <div class="card-body">
                        <div class="pds-instruction-banner">
                            <i class="bi bi-briefcase"></i>
                            <p class="pds-instruction-text">Include private employment. Start from your most recent work. Description of duties in attached Work Experience Sheet.</p>
                        </div>
                        <div id="pdsWorkCards"></div>
                        <button type="button" class="btn btn-deped btn-sm pds-btn-add-item" id="pdsAddWorkBtn"><i class="bi bi-plus-lg"></i> Add work experience</button>
                        <p class="text-muted small mt-2 mb-0">(Continue on separate sheet if necessary)</p>
                    </div>
                </div>
            </div>

            {{-- Step 4: Declaration --}}
            <div class="pds-step-section" id="pds-step-4" data-step="4">
                <div class="pds-declaration-box mb-4">
                    <strong><i class="bi bi-exclamation-triangle me-1"></i> Declaration</strong>
                    Any misrepresentation in this Personal Data Sheet or the Work Experience Sheet may result in administrative and/or criminal case(s) against the person concerned. By submitting this form, you confirm that the information provided is true and correct to the best of your knowledge.
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
    var STEP_COUNT = 4;
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
            if (step <= currentStep || (step === 2 && step1Valid()) || step > 2) goToStep(step);
        });
    });
    if (prevBtn) prevBtn.addEventListener('click', function() { goToStep(currentStep - 1); });
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (currentStep === 1 && !step1Valid()) {
                form.querySelector('input[name="surname"]').focus();
                return;
            }
            goToStep(currentStep + 1);
        });
    }

    // Save Draft + autosave
    var draftStatus = document.getElementById('pdsDraftStatus');
    var saveDraftBtn = document.getElementById('pdsSaveDraftBtn');
    function syncChildrenInput() {
        var container = document.getElementById('pdsChildrenCards');
        var hidden = document.getElementById('pdsChildrenNamesInput');
        if (!container || !hidden) return;
        var parts = [];
        container.querySelectorAll('input[data-child-name]').forEach(function(inp) {
            var v = inp.value.trim();
            if (v) parts.push(v);
        });
        hidden.value = parts.join("\n");
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
    function toggleSpouse() {
        var v = civilSelect ? civilSelect.value : '';
        if (spouseWrap) spouseWrap.classList.toggle('hidden', v !== 'married');
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
    }
    if (noChildrenCheck) noChildrenCheck.addEventListener('change', toggleChildrenList);
    toggleChildrenList();

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
    function addChildCard(value) {
        if (!childrenCards) return;
        value = value || '';
        var id = 'child-' + (childIndex++);
        var div = document.createElement('div');
        div.className = 'pds-card-repeat';
        div.setAttribute('data-child-card', '');
        div.innerHTML = '<div class="pds-card-repeat-header">' +
            '<span class="card-title-text">Child ' + (childrenCards.querySelectorAll("[data-child-card]").length + 1) + '</span>' +
            '<button type="button" class="pds-btn-remove pds-remove-child" aria-label="Remove"><i class="bi bi-trash"></i></button>' +
            '</div><div class="pds-card-repeat-body">' +
            '<label class="pds-form-label">Full name</label>' +
            '<input type="text" class="form-control pds-form-control" data-child-name maxlength="255" value="' + (value.replace(/"/g, '&quot;')) + '" placeholder="Full name of child">' +
            '</div>';
        childrenCards.appendChild(div);
        div.querySelector('input[data-child-name]').addEventListener('input', syncChildrenInput);
        div.querySelector('.pds-remove-child').addEventListener('click', function() {
            div.remove();
            syncChildrenInput();
        });
        syncChildrenInput();
    }
    childrenInitial.forEach(function(v) { addChildCard(v); });
    if (childrenInitial.length === 0) addChildCard('');
    if (addChildBtn) addChildBtn.addEventListener('click', function() { addChildCard(''); });

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
        });
        reindexEligibility();
    });
    eligibilityContainer.querySelectorAll('[data-eligibility-card]').forEach(function(card) {
        card.querySelector('.pds-card-repeat-header').addEventListener('click', function() { card.classList.toggle('collapsed'); });
        card.querySelector('.pds-remove-eligibility').addEventListener('click', function() {
            if (eligibilityContainer.querySelectorAll('[data-eligibility-card]').length <= 1) return;
            card.remove();
            reindexEligibility();
        });
    });

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
        });
        reindexWork();
    });
    workContainer.querySelectorAll('[data-work-card]').forEach(function(card) {
        card.querySelector('.pds-card-repeat-header').addEventListener('click', function() { card.classList.toggle('collapsed'); });
        card.querySelector('.pds-remove-work').addEventListener('click', function() {
            if (workContainer.querySelectorAll('[data-work-card]').length <= 1) return;
            card.remove();
            reindexWork();
        });
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

    // Scroll to first error on load
    var firstInvalid = form.querySelector('.is-invalid');
    if (firstInvalid) {
        var stepEl = firstInvalid.closest('.pds-step-section');
        if (stepEl) goToStep(parseInt(stepEl.getAttribute('data-step'), 10));
        setTimeout(function() { firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
    }

    // Sync children before submit
    form.addEventListener('submit', function() { syncChildrenInput(); });
})();
</script>
@endpush
@endsection
