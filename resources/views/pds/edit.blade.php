@extends('layouts.app')

@section('title', ($isOwn ? 'My' : $user->name . "'s") . ' Personal Data Sheet - ' . config('app.name'))

@push('styles')
<style>
    .pds-form-section { display: none; }
    .pds-form-section.active { display: block; animation: pdsFadeIn 0.25s ease; }
    @keyframes pdsFadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .pds-nav-pill {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1rem;
        border-radius: 12px;
        color: #64748b;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }
    .pds-nav-pill:hover { background: #f1f5f9; color: #334155; }
    .pds-nav-pill.active { background: var(--deped-primary); color: #fff; border-color: var(--deped-primary); box-shadow: 0 2px 8px rgba(30, 53, 255, 0.3); }
    .pds-nav-pill i { font-size: 1.1rem; opacity: 0.9; }
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
    .pds-form-label {
        font-weight: 500;
        color: #475569;
        font-size: 0.875rem;
        margin-bottom: 0.35rem;
    }
    .pds-form-control {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.55rem 0.85rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .pds-form-control:focus {
        border-color: var(--deped-primary);
        box-shadow: 0 0 0 3px rgba(30, 53, 255, 0.15);
        outline: none;
    }
    .pds-form-control.is-invalid { border-color: #dc2626; }
    .pds-radio-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }
    .pds-radio-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.9rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pds-radio-option:hover { border-color: #cbd5e1; background: #f8fafc; }
    .pds-radio-option input { margin: 0; accent-color: var(--deped-primary); }
    .pds-radio-option input:checked + span { font-weight: 600; color: var(--deped-primary); }
    .pds-sticky-bar {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        padding: 1rem 0;
        margin: 0 -1.5rem -1.5rem -1.5rem;
        padding: 1rem 1.5rem;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
        border-radius: 0 0 16px 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        justify-content: space-between;
    }
    .pds-progress-wrap {
        background: #f1f5f9;
        border-radius: 999px;
        height: 6px;
        overflow: hidden;
        max-width: 200px;
    }
    .pds-progress-fill { height: 100%; background: linear-gradient(90deg, var(--deped-primary), var(--deped-primary-light)); border-radius: 999px; transition: width 0.3s ease; }
    .pds-same-address {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pds-same-address:hover { background: #dbeafe; }
    .pds-same-address input { margin: 0; accent-color: var(--deped-primary); }
    .pds-same-address span { font-weight: 500; color: #1e40af; font-size: 0.9rem; }
    .pds-permanent-disabled .pds-form-control { background: #f1f5f9; color: #64748b; cursor: not-allowed; }
    .breadcrumb { --bs-breadcrumb-divider-color: #94a3b8; }
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
            <p class="text-muted small mb-0">Section I — Personal Information. Complete each section and save.</p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <a href="{{ route('reports.pds-pdf', $isOwn ? [] : ['user_id' => $user->id]) }}" class="btn btn-deped" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i> Print PDS</a>
            @if(!$isOwn)
                <a href="{{ route('personnel.show', $user) }}" class="btn btn-outline-secondary">Back to profile</a>
            @endif
        </div>
    </div>
</div>

{{-- Section navigation --}}
<nav class="d-flex flex-wrap gap-2 mb-4" aria-label="PDS sections">
    <button type="button" class="pds-nav-pill active" data-pds-section="personal" aria-current="step">
        <i class="bi bi-person"></i> Personal Info
    </button>
    <button type="button" class="pds-nav-pill" data-pds-section="ids"> <i class="bi bi-card-list"></i> Government IDs </button>
    <button type="button" class="pds-nav-pill" data-pds-section="addresses"> <i class="bi bi-geo-alt"></i> Addresses </button>
    <button type="button" class="pds-nav-pill" data-pds-section="contact"> <i class="bi bi-telephone"></i> Contact </button>
</nav>

<div class="card pds-section-card">
    <div class="card-body">
        <form method="POST" action="{{ $isOwn ? route('pds.update') : route('personnel.pds.update', $user) }}" id="pdsForm">
            @csrf

            {{-- Section 1: Personal Info --}}
            <div class="pds-form-section active" id="section-personal" data-section="personal">
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-person-badge"></i> 1–2. Name</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="pds-form-label">1. Surname</label>
                                <input type="text" class="form-control pds-form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname', $pds->surname) }}" maxlength="100">
                                @error('surname')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-2">
                                <label class="pds-form-label">Name extension</label>
                                <input type="text" class="form-control pds-form-control" name="name_extension" value="{{ old('name_extension', $pds->name_extension) }}" maxlength="20" placeholder="e.g. JR">
                            </div>
                            <div class="col-md-3">
                                <label class="pds-form-label">2. First name</label>
                                <input type="text" class="form-control pds-form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', $pds->first_name) }}" maxlength="100">
                                @error('first_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="pds-form-label">Middle name</label>
                                <input type="text" class="form-control pds-form-control" name="middle_name" value="{{ old('middle_name', $pds->middle_name) }}" maxlength="100">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-calendar-event"></i> 3–4. Birth</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="pds-form-label">3. Date of birth</label>
                                <input type="date" class="form-control pds-form-control" name="date_of_birth" value="{{ old('date_of_birth', $pds->date_of_birth?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-8">
                                <label class="pds-form-label">4. Place of birth</label>
                                <input type="text" class="form-control pds-form-control" name="place_of_birth" value="{{ old('place_of_birth', $pds->place_of_birth) }}" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-gender-ambiguous"></i> 5–9. Sex, civil status &amp; physical</div>
                    <div class="card-body">
                        <div class="row g-3">
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
                                <div class="pds-radio-group flex-wrap">
                                    @foreach(['single' => 'Single', 'married' => 'Married', 'widowed' => 'Widowed', 'separated' => 'Separated', 'other' => 'Other'] as $val => $label)
                                        <label class="pds-radio-option">
                                            <input type="radio" name="civil_status" value="{{ $val }}" {{ old('civil_status', $pds->civil_status) === $val ? 'checked' : '' }}>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                    <input type="text" class="form-control pds-form-control d-inline-block" name="civil_status_other" value="{{ old('civil_status_other', $pds->civil_status_other) }}" placeholder="If Other, specify" style="max-width: 160px;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="pds-form-label">7. Height (m)</label>
                                <input type="number" class="form-control pds-form-control" name="height" value="{{ old('height', $pds->height) }}" step="0.01" min="0" max="3" placeholder="1.65">
                            </div>
                            <div class="col-md-2">
                                <label class="pds-form-label">8. Weight (kg)</label>
                                <input type="number" class="form-control pds-form-control" name="weight" value="{{ old('weight', $pds->weight) }}" step="0.01" min="0" max="500" placeholder="70">
                            </div>
                            <div class="col-md-2">
                                <label class="pds-form-label">9. Blood type</label>
                                <input type="text" class="form-control pds-form-control" name="blood_type" value="{{ old('blood_type', $pds->blood_type) }}" maxlength="10" placeholder="e.g. O">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Government IDs & Citizenship --}}
            <div class="pds-form-section" id="section-ids" data-section="ids">
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-card-list"></i> 10–15. Government / Agency IDs</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <label class="pds-form-label">10. UMID ID No.</label>
                                <input type="text" class="form-control pds-form-control" name="umid_id" value="{{ old('umid_id', $pds->umid_id) }}" maxlength="50">
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="pds-form-label">11. PAG-IBIG ID No.</label>
                                <input type="text" class="form-control pds-form-control" name="pagibig_id" value="{{ old('pagibig_id', $pds->pagibig_id) }}" maxlength="50">
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="pds-form-label">12. PhilHealth No.</label>
                                <input type="text" class="form-control pds-form-control" name="philhealth_no" value="{{ old('philhealth_no', $pds->philhealth_no) }}" maxlength="50">
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="pds-form-label">13. PhilSys Number (PSN)</label>
                                <input type="text" class="form-control pds-form-control" name="philsys_number" value="{{ old('philsys_number', $pds->philsys_number) }}" maxlength="50">
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="pds-form-label">14. TIN No.</label>
                                <input type="text" class="form-control pds-form-control" name="tin_no" value="{{ old('tin_no', $pds->tin_no) }}" maxlength="50">
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="pds-form-label">15. Agency Employee No.</label>
                                <input type="text" class="form-control pds-form-control" name="agency_employee_no" value="{{ old('agency_employee_no', $pds->agency_employee_no ?? $user->employee_id) }}" maxlength="50">
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="pds-form-label">Date of Appointment</label>
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
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <span class="text-muted small">If dual:</span>
                            <label class="pds-radio-option">
                                <input type="radio" name="dual_citizenship_type" value="by_birth" {{ old('dual_citizenship_type', $pds->dual_citizenship_type) === 'by_birth' ? 'checked' : '' }}>
                                <span>By birth</span>
                            </label>
                            <label class="pds-radio-option">
                                <input type="radio" name="dual_citizenship_type" value="by_naturalization" {{ old('dual_citizenship_type', $pds->dual_citizenship_type) === 'by_naturalization' ? 'checked' : '' }}>
                                <span>By naturalization</span>
                            </label>
                            <input type="text" class="form-control pds-form-control" name="dual_citizenship_country" value="{{ old('dual_citizenship_country', $pds->dual_citizenship_country) }}" placeholder="Country" style="max-width: 180px;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Addresses --}}
            <div class="pds-form-section" id="section-addresses" data-section="addresses">
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-house-door"></i> 17. Residential address</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="pds-form-label">House/Block/Lot No.</label><input type="text" class="form-control pds-form-control" name="residential_house_no" value="{{ old('residential_house_no', $pds->residential_house_no) }}" maxlength="100"></div>
                            <div class="col-md-6"><label class="pds-form-label">Street</label><input type="text" class="form-control pds-form-control" name="residential_street" value="{{ old('residential_street', $pds->residential_street) }}" maxlength="255"></div>
                            <div class="col-md-6"><label class="pds-form-label">Subdivision/Village</label><input type="text" class="form-control pds-form-control" name="residential_subdivision" value="{{ old('residential_subdivision', $pds->residential_subdivision) }}" maxlength="255"></div>
                            <div class="col-md-6"><label class="pds-form-label">Barangay</label><input type="text" class="form-control pds-form-control" name="residential_barangay" value="{{ old('residential_barangay', $pds->residential_barangay) }}" maxlength="100"></div>
                            <div class="col-md-6"><label class="pds-form-label">City/Municipality</label><input type="text" class="form-control pds-form-control" name="residential_city" value="{{ old('residential_city', $pds->residential_city) }}" maxlength="100"></div>
                            <div class="col-md-4"><label class="pds-form-label">Province</label><input type="text" class="form-control pds-form-control" name="residential_province" value="{{ old('residential_province', $pds->residential_province) }}" maxlength="100"></div>
                            <div class="col-md-2"><label class="pds-form-label">ZIP code</label><input type="text" class="form-control pds-form-control" name="residential_zip" value="{{ old('residential_zip', $pds->residential_zip) }}" maxlength="20"></div>
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
                            <div class="col-md-6"><label class="pds-form-label">House/Block/Lot No.</label><input type="text" class="form-control pds-form-control" name="permanent_house_no" id="permanent_house_no" value="{{ old('permanent_house_no', $pds->permanent_house_no) }}" maxlength="100"></div>
                            <div class="col-md-6"><label class="pds-form-label">Street</label><input type="text" class="form-control pds-form-control" name="permanent_street" id="permanent_street" value="{{ old('permanent_street', $pds->permanent_street) }}" maxlength="255"></div>
                            <div class="col-md-6"><label class="pds-form-label">Subdivision/Village</label><input type="text" class="form-control pds-form-control" name="permanent_subdivision" id="permanent_subdivision" value="{{ old('permanent_subdivision', $pds->permanent_subdivision) }}" maxlength="255"></div>
                            <div class="col-md-6"><label class="pds-form-label">Barangay</label><input type="text" class="form-control pds-form-control" name="permanent_barangay" id="permanent_barangay" value="{{ old('permanent_barangay', $pds->permanent_barangay) }}" maxlength="100"></div>
                            <div class="col-md-6"><label class="pds-form-label">City/Municipality</label><input type="text" class="form-control pds-form-control" name="permanent_city" id="permanent_city" value="{{ old('permanent_city', $pds->permanent_city) }}" maxlength="100"></div>
                            <div class="col-md-4"><label class="pds-form-label">Province</label><input type="text" class="form-control pds-form-control" name="permanent_province" id="permanent_province" value="{{ old('permanent_province', $pds->permanent_province) }}" maxlength="100"></div>
                            <div class="col-md-2"><label class="pds-form-label">ZIP code</label><input type="text" class="form-control pds-form-control" name="permanent_zip" id="permanent_zip" value="{{ old('permanent_zip', $pds->permanent_zip) }}" maxlength="20"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: Contact --}}
            <div class="pds-form-section" id="section-contact" data-section="contact">
                <div class="pds-section-card">
                    <div class="card-header"><i class="bi bi-telephone"></i> 19–21. Contact information</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="pds-form-label">19. Telephone no.</label>
                                <input type="text" class="form-control pds-form-control" name="telephone" value="{{ old('telephone', $pds->telephone) }}" maxlength="50">
                            </div>
                            <div class="col-md-4">
                                <label class="pds-form-label">20. Mobile no.</label>
                                <input type="text" class="form-control pds-form-control" name="mobile" value="{{ old('mobile', $pds->mobile) }}" maxlength="50">
                            </div>
                            <div class="col-md-4">
                                <label class="pds-form-label">21. E-mail address</label>
                                <input type="email" class="form-control pds-form-control @error('email_address') is-invalid @enderror" name="email_address" value="{{ old('email_address', $pds->email_address ?? $user->email) }}" maxlength="255">
                                @error('email_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sticky save bar --}}
            <div class="pds-sticky-bar mt-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="pds-progress-wrap" style="width: 140px;" title="Section progress">
                        <div class="pds-progress-fill" id="pdsProgressFill" style="width: 25%;"></div>
                    </div>
                    <span class="text-muted small" id="pdsSectionLabel">Section 1 of 4</span>
                    <div class="d-flex gap-2 ms-2">
                        <button type="button" class="btn btn-outline-secondary btn-pds-prev" id="pdsPrevBtn" aria-label="Previous section" style="display: none;">
                            <i class="bi bi-chevron-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-deped btn-pds-next" id="pdsNextBtn" aria-label="Next section">
                            Next <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-deped px-4"><i class="bi bi-check-lg me-1"></i> Save PDS</button>
                    @if($isOwn)
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    @else
                        <a href="{{ route('personnel.show', $user) }}" class="btn btn-outline-secondary">Cancel</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var sections = ['personal', 'ids', 'addresses', 'contact'];
    var pills = document.querySelectorAll('[data-pds-section]');
    var sectionEls = document.querySelectorAll('.pds-form-section');
    var progressFill = document.getElementById('pdsProgressFill');
    var sectionLabel = document.getElementById('pdsSectionLabel');

    function getCurrentIndex() {
        var active = document.querySelector('.pds-form-section.active');
        if (!active) return 0;
        return sections.indexOf(active.getAttribute('data-section'));
    }

    function showSection(id) {
        sectionEls.forEach(function(el) {
            el.classList.toggle('active', el.getAttribute('data-section') === id);
        });
        pills.forEach(function(pill) {
            var active = pill.getAttribute('data-pds-section') === id;
            pill.classList.toggle('active', active);
            pill.setAttribute('aria-current', active ? 'step' : null);
        });
        var idx = sections.indexOf(id);
        if (progressFill) progressFill.style.width = ((idx + 1) / 4 * 100) + '%';
        if (sectionLabel) sectionLabel.textContent = 'Section ' + (idx + 1) + ' of 4';
        var prevBtn = document.getElementById('pdsPrevBtn');
        var nextBtn = document.getElementById('pdsNextBtn');
        if (prevBtn) prevBtn.style.display = idx <= 0 ? 'none' : 'inline-block';
        if (nextBtn) nextBtn.style.display = idx >= 3 ? 'none' : 'inline-block';
    }

    pills.forEach(function(pill) {
        pill.addEventListener('click', function() {
            showSection(this.getAttribute('data-pds-section'));
        });
    });

    var prevBtn = document.getElementById('pdsPrevBtn');
    var nextBtn = document.getElementById('pdsNextBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            var idx = getCurrentIndex();
            if (idx > 0) showSection(sections[idx - 1]);
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            var idx = getCurrentIndex();
            if (idx < 3) showSection(sections[idx + 1]);
        });
    }

    // Same as residential address
    var sameCheck = document.getElementById('pdsSameAsResidential');
    var permanentCard = document.getElementById('permanent-address-card');
    var residentialNames = ['residential_house_no','residential_street','residential_subdivision','residential_barangay','residential_city','residential_province','residential_zip'];
    var permanentIds = ['permanent_house_no','permanent_street','permanent_subdivision','permanent_barangay','permanent_city','permanent_province','permanent_zip'];

    function copyResidentialToPermanent() {
        residentialNames.forEach(function(name, i) {
            var from = document.querySelector('input[name="' + name + '"]');
            var to = document.getElementById(permanentIds[i]);
            if (from && to) to.value = from.value;
        });
    }

    function setPermanentReadonly(readonly) {
        var container = document.querySelector('.pds-permanent-fields');
        permanentIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.readOnly = readonly;
            }
        });
        if (container) container.classList.toggle('pds-permanent-disabled', readonly);
    }

    if (sameCheck) {
        sameCheck.addEventListener('change', function() {
            if (this.checked) {
                copyResidentialToPermanent();
                setPermanentReadonly(true);
            } else {
                setPermanentReadonly(false);
            }
        });
    }
})();
</script>
@endpush
@endsection
