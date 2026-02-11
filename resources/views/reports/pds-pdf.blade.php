<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Personal Data Sheet - {{ $user->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 12px; }
        .pds-table { width: 100%; border-collapse: collapse; border: 2px solid #000; }
        .pds-table td, .pds-table th { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        .pds-table .section-header { background-color: #d9d9d9; font-weight: bold; text-align: left; }
        .pds-table .label-cell { background-color: #e8e8e8; font-weight: bold; width: 22%; }
        .pds-table .value-cell { min-height: 18px; }
        .text-center { text-align: center; }
        .header-block { text-align: center; margin-bottom: 8px; }
        .header-block h1 { font-size: 14px; margin: 4px 0; font-weight: bold; }
        .header-block .warning { font-size: 8px; font-weight: bold; margin: 4px 0; }
        .header-block .instructions { font-size: 7px; margin: 2px 0; }
        .cb { display: inline-block; width: 12px; height: 12px; border: 1px solid #000; margin-right: 6px; vertical-align: middle; text-align: center; line-height: 12px; font-size: 10px; font-weight: bold; overflow: hidden; }
        .cb-label { margin-right: 10px; }
        .two-col { width: 50%; }
    </style>
</head>
<body>
    <div class="header-block">
        <h1>PERSONAL DATA SHEET</h1>
        <p class="warning">WARNING: Any misrepresentation in this Personal Data Sheet may result in the filing of administrative or criminal case(s) against the person concerned.</p>
        <p class="instructions">Complete all applicable sections accurately. Refer to the official Guide to Filling Out the PDS when in doubt. Select the appropriate options; indicate N/A where not applicable. Do not abbreviate.</p>
    </div>

    @php
        $p = $pds ?? null;
        $na = 'N/A';
        $val = function($v) use ($na) { return $v !== null && $v !== '' ? $v : $na; };
        $dateVal = function($d) use ($na) {
            if (!$d) return $na;
            return $d instanceof \Carbon\Carbon ? $d->format('d/m/Y') : \Carbon\Carbon::parse($d)->format('d/m/Y');
        };
    @endphp

    <table class="pds-table">
        <tr>
            <td colspan="3" class="section-header">I. PERSONAL INFORMATION</td>
        </tr>
        <tr>
            <td class="label-cell">1. SURNAME</td>
            <td class="value-cell">{{ $val($p?->surname) }}</td>
            <td class="value-cell" rowspan="3" style="width:18%;vertical-align:top;">
                <strong>NAME EXTENSION (JR., SR)</strong><br>
                <span style="border-bottom:1px solid #000;">{{ $val($p?->name_extension) }}</span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">2. FIRST NAME</td>
            <td class="value-cell">{{ $val($p?->first_name) }}</td>
        </tr>
        <tr>
            <td class="label-cell">MIDDLE NAME</td>
            <td class="value-cell">{{ $val($p?->middle_name) }}</td>
        </tr>
        <tr>
            <td class="label-cell">3. DATE OF BIRTH (dd/mm/yyyy)</td>
            <td class="value-cell" colspan="2">{{ $dateVal($p?->date_of_birth) }}</td>
        </tr>
        <tr>
            <td class="label-cell">4. PLACE OF BIRTH</td>
            <td class="value-cell" colspan="2">{{ $val($p?->place_of_birth) }}</td>
        </tr>
        <tr>
            <td class="label-cell">5. SEX AT BIRTH</td>
            <td class="value-cell" colspan="2">
                <span class="cb">{{ ($p && $p->sex === 'male') ? '✓' : '' }}</span><span class="cb-label">Male</span>
                <span class="cb">{{ ($p && $p->sex === 'female') ? '✓' : '' }}</span><span class="cb-label">Female</span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">6. CIVIL STATUS</td>
            <td class="value-cell" colspan="2">
                <span class="cb">{{ ($p && $p->civil_status === 'single') ? '✓' : '' }}</span><span class="cb-label">Single</span>
                <span class="cb">{{ ($p && $p->civil_status === 'married') ? '✓' : '' }}</span><span class="cb-label">Married</span>
                <span class="cb">{{ ($p && $p->civil_status === 'widowed') ? '✓' : '' }}</span><span class="cb-label">Widowed</span>
                <span class="cb">{{ ($p && $p->civil_status === 'separated') ? '✓' : '' }}</span><span class="cb-label">Separated</span>
                <span class="cb">{{ ($p && $p->civil_status === 'other') ? '✓' : '' }}</span><span class="cb-label">Other/s:</span> {{ $val($p?->civil_status_other) }}
            </td>
        </tr>
        <tr>
            <td class="label-cell">7. HEIGHT (m)</td>
            <td class="value-cell" colspan="2">{{ $val($p?->height) }}</td>
        </tr>
        <tr>
            <td class="label-cell">8. WEIGHT (kg)</td>
            <td class="value-cell" colspan="2">{{ $val($p?->weight) }}</td>
        </tr>
        <tr>
            <td class="label-cell">9. BLOOD TYPE</td>
            <td class="value-cell" colspan="2">{{ $val($p?->blood_type) }}</td>
        </tr>
        <tr>
            <td class="label-cell">10. UMID ID NO.</td>
            <td class="value-cell" colspan="2">{{ $val($p?->umid_id) }}</td>
        </tr>
        <tr>
            <td class="label-cell">11. PAG-IBIG ID NO.</td>
            <td class="value-cell" colspan="2">{{ $val($p?->pagibig_id) }}</td>
        </tr>
        <tr>
            <td class="label-cell">12. PHILHEALTH NO.</td>
            <td class="value-cell" colspan="2">{{ $val($p?->philhealth_no) }}</td>
        </tr>
        <tr>
            <td class="label-cell">13. PhilSys Number (PSN)</td>
            <td class="value-cell" colspan="2">{{ $val($p?->philsys_number) }}</td>
        </tr>
        <tr>
            <td class="label-cell">14. TIN NO.</td>
            <td class="value-cell" colspan="2">{{ $val($p?->tin_no) }}</td>
        </tr>
        <tr>
            <td class="label-cell">15. AGENCY EMPLOYEE NO.</td>
            <td class="value-cell" colspan="2">{{ $val($p?->agency_employee_no ?? $user->employee_id) }}</td>
        </tr>
        <tr>
            <td class="label-cell">DATE OF APPOINTMENT (dd/mm/yyyy)</td>
            <td class="value-cell" colspan="2">{{ $dateVal($p?->date_of_appointment) }}</td>
        </tr>
        <tr>
            <td class="label-cell section-header">16. CITIZENSHIP</td>
            <td class="value-cell" colspan="2">
                <span class="cb">{{ ($p && $p->citizenship === 'filipino') ? '✓' : '' }}</span><span class="cb-label">Filipino</span>
                <span class="cb">{{ ($p && $p->citizenship === 'dual') ? '✓' : '' }}</span><span class="cb-label">Dual Citizenship</span>
                <span class="cb">{{ ($p && $p->dual_citizenship_type === 'by_birth') ? '✓' : '' }}</span><span class="cb-label">by birth</span>
                <span class="cb">{{ ($p && $p->dual_citizenship_type === 'by_naturalization') ? '✓' : '' }}</span><span class="cb-label">by naturalization</span>
                Pls. indicate country: {{ $val($p?->dual_citizenship_country) }}
            </td>
        </tr>
        <tr>
            <td class="label-cell section-header">17. RESIDENTIAL ADDRESS</td>
            <td class="value-cell" colspan="2">
                <table width="100%" style="border:none;"><tr><td style="border:none;padding:0;">House/Block/Lot No.</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->residential_house_no) }}</td><td style="border:none;padding:0 4px;">Street</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->residential_street) }}</td></tr>
                <tr><td style="border:none;padding:0;">Subdivision/Village</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->residential_subdivision) }}</td><td style="border:none;padding:0 4px;">Barangay</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->residential_barangay) }}</td></tr>
                <tr><td style="border:none;padding:0;">City/Municipality</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->residential_city) }}</td><td style="border:none;padding:0 4px;">Province</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->residential_province) }}</td></tr>
                <tr><td style="border:none;padding:0;">ZIP CODE</td><td colspan="3" style="border:none;border-bottom:1px solid #000;">{{ $val($p?->residential_zip) }}</td></tr></table>
            </td>
        </tr>
        <tr>
            <td class="label-cell section-header">18. PERMANENT ADDRESS</td>
            <td class="value-cell" colspan="2">
                <table width="100%" style="border:none;"><tr><td style="border:none;padding:0;">House/Block/Lot No.</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->permanent_house_no) }}</td><td style="border:none;padding:0 4px;">Street</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->permanent_street) }}</td></tr>
                <tr><td style="border:none;padding:0;">Subdivision/Village</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->permanent_subdivision) }}</td><td style="border:none;padding:0 4px;">Barangay</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->permanent_barangay) }}</td></tr>
                <tr><td style="border:none;padding:0;">City/Municipality</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->permanent_city) }}</td><td style="border:none;padding:0 4px;">Province</td><td style="border:none;border-bottom:1px solid #000;">{{ $val($p?->permanent_province) }}</td></tr>
                <tr><td style="border:none;padding:0;">ZIP CODE</td><td colspan="3" style="border:none;border-bottom:1px solid #000;">{{ $val($p?->permanent_zip) }}</td></tr></table>
            </td>
        </tr>
        <tr>
            <td class="label-cell">19. TELEPHONE NO.</td>
            <td class="value-cell" colspan="2">{{ $val($p?->telephone) }}</td>
        </tr>
        <tr>
            <td class="label-cell">20. MOBILE NO.</td>
            <td class="value-cell" colspan="2">{{ $val($p?->mobile) }}</td>
        </tr>
        <tr>
            <td class="label-cell">21. E-MAIL ADDRESS (if any)</td>
            <td class="value-cell" colspan="2">{{ $val($p?->email_address ?? $user->email) }}</td>
        </tr>
    </table>
</body>
</html>
