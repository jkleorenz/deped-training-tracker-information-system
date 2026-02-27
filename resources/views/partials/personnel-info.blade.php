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
@endphp
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
