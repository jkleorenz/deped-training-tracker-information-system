@php
    $pds = $user->personalDataSheet;
    $fullName = $user->name;
    if ($pds && (trim($pds->surname ?? '') !== '' || trim($pds->first_name ?? '') !== '')) {
        $first = trim($pds->first_name ?? '');
        $middle = trim($pds->middle_name ?? '');
        $middleInitial = $middle !== '' ? mb_substr($middle, 0, 1) . '.' : '';
        $surname = trim($pds->surname ?? '');
        $parts = array_filter([$first, $middleInitial, $surname]);
        $ext = trim($pds->name_extension ?? '');
        $fullName = implode(' ', $parts) . ($ext !== '' ? ' ' . $ext : '');
    }
    $profilePhotoUrl = $pds?->photo_url;
@endphp
<div class="d-flex align-items-start gap-3">
    {{-- Left: Personnel details --}}
    <div class="flex-grow-1 min-w-0">
        <dl class="row mb-0 small">
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Name</dt>
            <dd class="col-sm-9 col-md-10">{{ $fullName }}</dd>

            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Email</dt>
            <dd class="col-sm-9 col-md-10">{{ $pds && $pds->email_address ? $pds->email_address : $user->email }}</dd>

            @php $empId = $pds && $pds->agency_employee_no ? $pds->agency_employee_no : $user->employee_id; @endphp
            @if($empId)
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Employee ID</dt>
            <dd class="col-sm-9 col-md-10">{{ $empId }}</dd>
            @endif

            @if($user->designation)
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Designation</dt>
            <dd class="col-sm-9 col-md-10">{{ $user->designation }}</dd>
            @endif

            @if($user->school)
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">School / Office</dt>
            <dd class="col-sm-9 col-md-10">{{ $user->school }}</dd>
            @endif

            @if($pds && $pds->date_of_birth)
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Date of birth</dt>
            <dd class="col-sm-9 col-md-10">{{ \Carbon\Carbon::parse($pds->date_of_birth)->format('F j, Y') }}</dd>
            @endif

            @if($pds && trim($pds->place_of_birth ?? '') !== '')
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Place of birth</dt>
            <dd class="col-sm-9 col-md-10">{{ $pds->place_of_birth }}</dd>
            @endif

            @if($pds && $pds->sex)
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Sex</dt>
            <dd class="col-sm-9 col-md-10">{{ ucfirst($pds->sex) }}</dd>
            @endif

            @if($pds && $pds->civil_status)
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Civil status</dt>
            <dd class="col-sm-9 col-md-10">{{ ucfirst($pds->civil_status) }}{{ $pds->civil_status === 'other' && $pds->civil_status_other ? ' (' . $pds->civil_status_other . ')' : '' }}</dd>
            @endif

            @if($pds && trim($pds->telephone ?? '') !== '')
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Telephone</dt>
            <dd class="col-sm-9 col-md-10">{{ $pds->telephone }}</dd>
            @endif

            @if($pds && trim($pds->mobile ?? '') !== '')
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Mobile</dt>
            <dd class="col-sm-9 col-md-10">{{ $pds->mobile }}</dd>
            @endif

            @if($pds && ($pds->residential_barangay || $pds->residential_city || $pds->residential_province))
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Residential address</dt>
            <dd class="col-sm-9 col-md-10">
                {{ implode(', ', array_filter([
                    $pds->residential_house_no,
                    $pds->residential_street,
                    $pds->residential_subdivision,
                    $pds->residential_barangay,
                    $pds->residential_city,
                    $pds->residential_province,
                    $pds->residential_zip,
                ])) }}
            </dd>
            @endif

            @if($pds && ($pds->permanent_barangay || $pds->permanent_city || $pds->permanent_province))
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Permanent address</dt>
            <dd class="col-sm-9 col-md-10">
                {{ implode(', ', array_filter([
                    $pds->permanent_house_no,
                    $pds->permanent_street,
                    $pds->permanent_subdivision,
                    $pds->permanent_barangay,
                    $pds->permanent_city,
                    $pds->permanent_province,
                    $pds->permanent_zip,
                ])) }}
            </dd>
            @endif

            @if($pds && $pds->citizenship)
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">Citizenship</dt>
            <dd class="col-sm-9 col-md-10">{{ ucfirst($pds->citizenship) }}{{ $pds->citizenship === 'dual' && $pds->dual_citizenship_country ? ' (' . $pds->dual_citizenship_country . ')' : '' }}</dd>
            @endif

            @if($pds && trim($pds->tin_no ?? '') !== '')
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">TIN</dt>
            <dd class="col-sm-9 col-md-10">{{ $pds->tin_no }}</dd>
            @endif

            @if($pds && trim($pds->philhealth_no ?? '') !== '')
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">PhilHealth No.</dt>
            <dd class="col-sm-9 col-md-10">{{ $pds->philhealth_no }}</dd>
            @endif

            @if($pds && trim($pds->pagibig_id ?? '') !== '')
            <dt class="col-sm-3 col-md-2 text-muted fw-normal">PAG-IBIG ID</dt>
            <dd class="col-sm-9 col-md-10">{{ $pds->pagibig_id }}</dd>
            @endif
        </dl>
    </div>

    {{-- Right: Profile Picture --}}
    <div class="flex-shrink-0 text-center d-none d-md-block" style="min-width: 140px;">
        <div class="personnel-profile-photo-wrap" id="personnelProfilePhotoWrap" style="width: 130px; height: 160px; border: 2px solid var(--deped-primary); border-radius: 6px; overflow: hidden; margin: 0 auto; background: #f8fafc;">
            @if($profilePhotoUrl)
                <img src="{{ $profilePhotoUrl }}" alt="{{ $fullName }}" id="personnelProfilePhoto" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @else
                <div id="personnelProfilePhotoPlaceholder" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted" style="font-size: 0.8rem;">
                    <i class="bi bi-person-fill" style="font-size: 3rem; opacity: 0.3;"></i>
                    <span class="mt-1">No photo</span>
                </div>
            @endif
        </div>
        <p class="small text-muted mt-2 mb-0">Profile Photo</p>
    </div>
</div>
