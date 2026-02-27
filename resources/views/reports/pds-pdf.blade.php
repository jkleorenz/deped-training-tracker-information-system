<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Personal Data Sheet - {{ $user->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 5px; line-height: 1.25; zoom: 1.08; }
        .instructions-top { font-size: 7px; margin-bottom: 1px; }
        .form-header { text-align: center; margin-bottom: 1px; }
        .form-header h1 { font-size: 11px; font-weight: bold; margin: 0 0 1px 0; }
        .form-header .form-meta { font-size: 7px; margin-top: 0; }
        .form-header .form-meta-right { float: right; text-align: right; margin-top: -14px; }
        .warning { font-size: 7px; font-weight: bold; margin-bottom: 2px; }
        .pds-table { width: 100%; border-collapse: collapse; border: 1px solid #000; table-layout: fixed; }
        .pds-table td, .pds-table th { border: 1px solid #000; padding: 1px 3px; vertical-align: top; }
        .section-header { background-color: #e0e0e0; font-weight: bold; padding: 1px 3px; font-size: 8px; }
        .label-cell { background-color: #f0f0f0; font-weight: bold; width: 20%; font-size: 7px; }
        .value-cell { min-height: 0; font-size: 8px; background-color: #fff; }
        .cb { display: inline-block; width: 8px; height: 8px; border: 1px solid #000; margin-right: 2px; vertical-align: middle; text-align: center; line-height: 8px; font-size: 7px; font-weight: bold; }
        .cb-label { margin-right: 6px; font-size: 7px; }
        .addr-grid { width: 100%; border: none; font-size: 7px; }
        .addr-grid td { border: none; padding: 0 3px 0 0; vertical-align: bottom; }
        .addr-grid .ul { border-bottom: 1px solid #000; }
        .edu-table { width: 100%; border-collapse: collapse; font-size: 7px; }
        .edu-table td, .edu-table th { border: 1px solid #000; padding: 1px 2px; }
        .edu-table th { background-color: #e8e8e8; font-weight: bold; }
        .footer-block { margin-top: 2px; font-size: 7px; }
        .footer-sig { margin-top: 2px; }
        .footer-sig-label { background-color: #f0f0f0; font-weight: bold; padding: 2px 3px; font-size: 7px; }
        .footer-sig-value { background-color: #fff; padding: 2px 3px; }
        .footer-sig-value .sig-line,
        .footer-sig-value .date-line { border-bottom: 1px solid #000; display: block; min-height: 10px; }
        .footer-sig-cols { width: 100%; border-collapse: collapse; border: 1px solid #000; table-layout: fixed; }
        .footer-sig-cols td { border: 1px solid #000; padding: 0; vertical-align: top; width: 50%; }
        .pds-name-cols { font-size: 7px; font-weight: bold; color: #333; }
        .pds-name-box-pdf { border: 1px solid #000; }
        .pds-name-box-pdf td { border: 1px solid #000; padding: 2px 3px; }
        .pds-photo-box-pdf { width: 127.58pt; height: 99.21pt; border: 1px solid #000; overflow: hidden; display: block; margin-bottom: 2px; }
        .pds-photo-box-pdf img { width: 127.58pt; height: 99.21pt; object-fit: cover; display: block; }
        /* Page 2: answer cells (yellow) per reference form */
        .page2-answer { background-color: #fffde7 !important; }
        .page2 .footer-sig-value { background-color: #fffde7 !important; }
        /* Page 4: Q34–40, References, Declaration, Govt ID, Photo — compact to fit one page */
        .pds-page4-wrap { font-size: 7pt; line-height: 1.05; margin: 0; padding: 0; box-sizing: border-box; }
        .pds-page4-wrap .pds-table { border-collapse: collapse; border: 1px solid #000; table-layout: fixed; width: 100%; }
        .pds-page4-wrap .pds-table td, .pds-page4-wrap .pds-table th { border: 1px solid #000; padding: 0 2px; vertical-align: top; font-size: 7pt; }
        .pds-page4-section-title { background: #e0e0e0; border: 1px solid #000; font-weight: bold; font-size: 8pt; text-transform: uppercase; padding: 1px 3px; }
        .pds-page4-yn-table { margin-top: 0; }
        .pds-page4-yn-table .pds-q-col { width: 70%; background: #f8f8f8; font-weight: bold; font-size: 6.5pt; line-height: 1.02; padding: 0 2px; }
        .pds-page4-yn-table .pds-yes-col { width: 15%; text-align: center; vertical-align: middle; padding: 0 1px; }
        .pds-page4-yn-table .pds-no-col { width: 15%; text-align: center; vertical-align: middle; padding: 0 1px; }
        .pds-page4-yn-table .pds-yn-header { background: #e0e0e0; font-weight: bold; font-size: 7pt; text-align: center; padding: 1px 2px; }
        .pds-page4-cb { display: inline-block; width: 6px; height: 6px; border: 1px solid #000; text-align: center; line-height: 6px; font-size: 5pt; vertical-align: middle; }
        .pds-page4-details-row .pds-q-col { background: #fff; border-top: none; }
        .pds-page4-details-row .pds-yes-col { text-align: left; font-weight: normal; font-size: 6.5pt; padding-left: 2px; vertical-align: top; }
        .pds-page4-ref-table { margin-top: 0; }
        .pds-page4-ref-table .pds-ref-th { background: #e0e0e0; font-weight: bold; font-size: 7pt; text-align: center; width: 33.33%; padding: 1px 2px; border: 1px solid #000; }
        .pds-page4-ref-table .pds-ref-td { width: 33.33%; font-size: 7pt; word-wrap: break-word; padding: 0 2px; }
        .pds-page4-declaration-box { border: 1px solid #000; padding: 1px 3px; margin-top: 0; }
        .pds-page4-declaration-box .declaration-text { text-align: justify; font-size: 6.5pt; line-height: 1.08; }
        .pds-page4-declaration-box .govt-id-table { margin-top: 1px; }
        .pds-page4-declaration-box .govt-id-table td { border: 1px solid #000; padding: 0 2px; font-size: 7pt; }
        .pds-page4-declaration-box .govt-id-table .govt-id-label { background: #f0f0f0; font-weight: bold; width: 33%; }
        .pds-page4-sig-box { border: 1px solid #000; min-height: 8px; background: #fff; display: block; }
        .pds-page4-oath-wrap { border: 1px solid #000; margin-top: 1px; overflow: hidden; }
        .pds-page4-oath-area { min-height: 8px; background: #fff; padding: 1px 3px; text-align: center; font-size: 6.5pt; line-height: 1.08; color: #c00; }
        .pds-page4-oath-label { background: #e0e0e0; padding: 1px 2px; text-align: center; font-size: 7pt; font-weight: bold; }
        .pds-page4-right-col { width: 28%; vertical-align: top; padding-left: 2px; border: none; }
        .pds-page4-photo-box { border: 1px solid #000; width: 4.5cm; height: 3.5cm; background: #fff; text-align: center; font-size: 6pt; line-height: 1; padding: 0; box-sizing: border-box; margin-bottom: 0; }
        .pds-page4-photo-box .pds-page4-photo-img { width: 4.5cm; height: 3.5cm; max-width: 100%; max-height: 100%; object-fit: cover; object-position: center; display: block; }
        .pds-page4-photo-label { font-size: 7pt; font-weight: bold; margin-top: 0; text-align: center; }
        .pds-page4-thumbmark-wrap { border: 1px solid #000; width: 2cm; margin-top: 1px; overflow: hidden; }
        .pds-page4-thumbmark-area { height: 0.9cm; background: #fff; }
        .pds-page4-thumbmark-label { background: #e0e0e0; padding: 0 1px; text-align: center; font-size: 6pt; font-weight: bold; }
        .pds-page4-left-col { width: 72%; vertical-align: top; border: none; padding: 0; }
        .pds-page4-bottom-table { margin-top: 0; border: none; }
        .pds-page4-bottom-table td { border: none; padding: 0; vertical-align: top; }
        .pds-page4-wrap { page-break-inside: avoid; }
        @media print { body { margin: 5px; } .footer-block { page-break-inside: avoid; } .pds-page4-wrap { page-break-inside: avoid; } }
    </style>
</head>
<body>

    <div class="form-header">
        <h1>PERSONAL DATA SHEET</h1>
        <p class="form-meta">CS Form No. 212 · Revised 2025</p>
    </div>
    <p class="instructions-top">Write or print legibly when handwritten. Tick the appropriate boxes; use a separate sheet if needed. Use N/A where not applicable. Do not abbreviate.</p>
    <p class="warning">WARNING: Any misrepresentation in this Personal Data Sheet or the Work Experience Sheet may result in administrative and/or criminal case(s) against the person concerned.</p>

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
                @if(!empty($photoPathAbsolute))
                <div class="pds-photo-box-pdf"><img src="{{ $photoPathAbsolute }}" alt="" /></div>
                @else
                <div class="pds-photo-box-pdf" style="background:#f5f5f5;font-size:6px;text-align:center;line-height:99pt;">Photo 4.5×3.5 cm</div>
                @endif
                <strong>NAME EXTENSION (JR., SR)</strong><br>
                <span style="border-bottom:1px solid #000;">{{ $val($p?->name_extension) }}</span>
            </td>
        </tr>
        <tr>
            <td class="label-cell">2. FIRST NAME</td>
            <td class="value-cell">{{ $val($p?->first_name) }}</td>
        </tr>
        <tr>
            <td class="label-cell">3. MIDDLE NAME</td>
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
            <td class="label-cell">13. PhilSys Number (PSN):</td>
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
            <td class="label-cell">16. CITIZENSHIP</td>
            <td class="value-cell" colspan="2">
                <span class="cb">{{ ($p && $p->citizenship === 'filipino') ? '✓' : '' }}</span><span class="cb-label">Filipino</span>
                <span class="cb">{{ ($p && $p->citizenship === 'dual') ? '✓' : '' }}</span><span class="cb-label">Dual Citizenship</span>
                <span class="cb">{{ ($p && $p->dual_citizenship_type === 'by_birth') ? '✓' : '' }}</span><span class="cb-label">by birth</span>
                <span class="cb">{{ ($p && $p->dual_citizenship_type === 'by_naturalization') ? '✓' : '' }}</span><span class="cb-label">by naturalization</span>
                Pls. indicate country: {{ $val($p?->dual_citizenship_country) }}
            </td>
        </tr>
        <tr>
            <td class="label-cell">17. RESIDENTIAL ADDRESS</td>
            <td class="value-cell" colspan="2">
                <table class="addr-grid"><tr>
                    <td>House/Block/Lot No.</td><td class="ul">{{ $val($p?->residential_house_no) }}</td>
                    <td>Street</td><td class="ul">{{ $val($p?->residential_street) }}</td>
                </tr><tr>
                    <td>Subdivision/Village</td><td class="ul">{{ $val($p?->residential_subdivision) }}</td>
                    <td>Barangay</td><td class="ul">{{ $val($p?->residential_barangay) }}</td>
                </tr><tr>
                    <td>City/Municipality</td><td class="ul">{{ $val($p?->residential_city) }}</td>
                    <td>Province</td><td class="ul">{{ $val($p?->residential_province) }}</td>
                </tr><tr>
                    <td>ZIP CODE</td><td class="ul" colspan="3">{{ $val($p?->residential_zip) }}</td>
                </tr></table>
            </td>
        </tr>
        <tr>
            <td class="label-cell">18. PERMANENT ADDRESS</td>
            <td class="value-cell" colspan="2">
                <table class="addr-grid"><tr>
                    <td>House/Block/Lot No.</td><td class="ul">{{ $val($p?->permanent_house_no) }}</td>
                    <td>Street</td><td class="ul">{{ $val($p?->permanent_street) }}</td>
                </tr><tr>
                    <td>Subdivision/Village</td><td class="ul">{{ $val($p?->permanent_subdivision) }}</td>
                    <td>Barangay</td><td class="ul">{{ $val($p?->permanent_barangay) }}</td>
                </tr><tr>
                    <td>City/Municipality</td><td class="ul">{{ $val($p?->permanent_city) }}</td>
                    <td>Province</td><td class="ul">{{ $val($p?->permanent_province) }}</td>
                </tr><tr>
                    <td>ZIP CODE</td><td class="ul" colspan="3">{{ $val($p?->permanent_zip) }}</td>
                </tr></table>
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

    <table class="pds-table" style="margin-top:4px;">
        <tr>
            <td colspan="5" class="section-header">II. FAMILY BACKGROUND</td>
        </tr>
        {{-- 22. Spouse — SURNAME | FIRST NAME | MIDDLE NAME | NAME EXTENSION (JR., SR.) --}}
        <tr class="pds-name-box-pdf">
            <td class="label-cell" rowspan="2" style="width:14%;vertical-align:top;">22. SPOUSE'S</td>
            <td class="pds-name-cols label-cell" style="width:22%;">SURNAME</td>
            <td class="pds-name-cols label-cell" style="width:22%;">FIRST NAME</td>
            <td class="pds-name-cols label-cell" style="width:22%;">MIDDLE NAME</td>
            <td class="pds-name-cols label-cell" style="width:20%;">NAME EXTENSION (JR., SR.)</td>
        </tr>
        <tr class="pds-name-box-pdf">
            <td class="value-cell">{{ $val($p?->spouse_surname) }}</td>
            <td class="value-cell">{{ $val($p?->spouse_first_name) }}</td>
            <td class="value-cell">{{ $val($p?->spouse_middle_name) }}</td>
            <td class="value-cell">{{ $val($p?->spouse_name_extension) }}</td>
        </tr>
        {{-- OCCUPATION | EMPLOYER/BUSINESS NAME — row 1: gray labels, row 2: white answers --}}
        <tr class="pds-name-box-pdf">
            <td class="label-cell" style="width:14%;"></td>
            <td class="pds-name-cols label-cell" colspan="2" style="width:43%;">OCCUPATION</td>
            <td class="pds-name-cols label-cell" colspan="2" style="width:43%;">EMPLOYER/BUSINESS NAME</td>
        </tr>
        <tr class="pds-name-box-pdf">
            <td class="label-cell" style="width:14%;"></td>
            <td class="value-cell" colspan="2" style="width:43%;">{{ $val($p?->spouse_occupation) }}</td>
            <td class="value-cell" colspan="2" style="width:43%;">{{ $val($p?->spouse_employer_business_name) }}</td>
        </tr>
        {{-- BUSINESS ADDRESS | TELEPHONE NO. — row 1: gray labels, row 2: white answers --}}
        <tr class="pds-name-box-pdf">
            <td class="label-cell" style="width:14%;"></td>
            <td class="pds-name-cols label-cell" colspan="2" style="width:43%;">BUSINESS ADDRESS</td>
            <td class="pds-name-cols label-cell" colspan="2" style="width:43%;">TELEPHONE NO.</td>
        </tr>
        <tr class="pds-name-box-pdf">
            <td class="label-cell" style="width:14%;"></td>
            <td class="value-cell" colspan="2" style="width:43%;">{{ $val($p?->spouse_business_address) }}</td>
            <td class="value-cell" colspan="2" style="width:43%;">{{ $val($p?->spouse_telephone) }}</td>
        </tr>
        <tr>
            <td class="label-cell">23. NAME of CHILDREN (Write full name and list all)</td>
            <td class="value-cell" colspan="4">{{ $val($p?->children_names) }}</td>
        </tr>
        {{-- 24. Father — 4 columns in 1 box --}}
        <tr class="pds-name-box-pdf">
            <td class="label-cell" rowspan="2" style="width:14%;vertical-align:top;">24. FATHER'S</td>
            <td class="pds-name-cols label-cell" style="width:21%;">SURNAME</td>
            <td class="pds-name-cols label-cell" style="width:21%;">FIRST NAME</td>
            <td class="pds-name-cols label-cell" style="width:21%;">MIDDLE NAME</td>
            <td class="pds-name-cols label-cell" style="width:23%;">NAME EXTENSION (JR., SR.)</td>
        </tr>
        <tr class="pds-name-box-pdf">
            <td class="value-cell">{{ $val($p?->father_surname) }}</td>
            <td class="value-cell">{{ $val($p?->father_first_name) }}</td>
            <td class="value-cell">{{ $val($p?->father_middle_name) }}</td>
            <td class="value-cell">{{ $val($p?->father_name_extension) }}</td>
        </tr>
        {{-- 25. Mother's maiden name — 4 columns in 1 box --}}
        <tr class="pds-name-box-pdf">
            <td class="label-cell" rowspan="2" style="width:14%;vertical-align:top;">25. MOTHER'S MAIDEN NAME</td>
            <td class="pds-name-cols label-cell" style="width:21%;">SURNAME</td>
            <td class="pds-name-cols label-cell" style="width:21%;">FIRST NAME</td>
            <td class="pds-name-cols label-cell" style="width:21%;">MIDDLE NAME</td>
            <td class="pds-name-cols label-cell" style="width:23%;">NAME EXTENSION (JR., SR.)</td>
        </tr>
        <tr class="pds-name-box-pdf">
            <td class="value-cell">{{ $val($p?->mother_surname) }}</td>
            <td class="value-cell">{{ $val($p?->mother_first_name) }}</td>
            <td class="value-cell">{{ $val($p?->mother_middle_name) }}</td>
            <td class="value-cell">{{ $val($p?->mother_name_extension) }}</td>
        </tr>
    </table>

    <table class="pds-table" style="margin-top:4px;">
        <tr>
            <td colspan="7" class="section-header">III. EDUCATIONAL BACKGROUND</td>
        </tr>
        <tr>
            <th class="edu-table" style="width:10%;">LEVEL</th>
            <th class="edu-table" style="width:18%;">NAME OF SCHOOL<br>(Write in full)</th>
            <th class="edu-table" style="width:18%;">BASIC EDUCATION/DEGREE/COURSE<br>(Write in full)</th>
            <th class="edu-table" style="width:14%;">PERIOD OF ATTENDANCE<br>From &nbsp;&nbsp;&nbsp;&nbsp; To</th>
            <th class="edu-table" style="width:12%;">HIGHEST LEVEL/UNITS EARNED<br>(if not graduated)</th>
            <th class="edu-table" style="width:10%;">Year Graduated</th>
            <th class="edu-table" style="width:18%;">SCHOLARSHIP/ACADEMIC HONORS RECEIVED</th>
        </tr>
        <tr>
            <td>ELEMENTARY</td>
            <td class="ul">{{ $val($p?->elem_school) }}</td>
            <td class="ul">{{ $val($p?->elem_degree_course) }}</td>
            <td class="ul">{{ $val($p?->elem_period_from) }} {{ $val($p?->elem_period_to) }}</td>
            <td class="ul">{{ $val($p?->elem_highest_level_units) }}</td>
            <td class="ul">{{ $val($p?->elem_year_graduated) }}</td>
            <td class="ul">{{ $val($p?->elem_scholarship_honors) }}</td>
        </tr>
        <tr>
            <td>SECONDARY</td>
            <td class="ul">{{ $val($p?->secondary_school) }}</td>
            <td class="ul">{{ $val($p?->secondary_degree_course) }}</td>
            <td class="ul">{{ $val($p?->secondary_period_from) }} {{ $val($p?->secondary_period_to) }}</td>
            <td class="ul">{{ $val($p?->secondary_highest_level_units) }}</td>
            <td class="ul">{{ $val($p?->secondary_year_graduated) }}</td>
            <td class="ul">{{ $val($p?->secondary_scholarship_honors) }}</td>
        </tr>
        <tr>
            <td>VOCATIONAL /<br>TRADE COURSE</td>
            <td class="ul">{{ $val($p?->voc_school) }}</td>
            <td class="ul">{{ $val($p?->voc_degree_course) }}</td>
            <td class="ul">{{ $val($p?->voc_period_from) }} {{ $val($p?->voc_period_to) }}</td>
            <td class="ul">{{ $val($p?->voc_highest_level_units) }}</td>
            <td class="ul">{{ $val($p?->voc_year_graduated) }}</td>
            <td class="ul">{{ $val($p?->voc_scholarship_honors) }}</td>
        </tr>
        <tr>
            <td>COLLEGE</td>
            <td class="ul">{{ $val($p?->college_school) }}</td>
            <td class="ul">{{ $val($p?->college_degree_course) }}</td>
            <td class="ul">{{ $val($p?->college_period_from) }} {{ $val($p?->college_period_to) }}</td>
            <td class="ul">{{ $val($p?->college_highest_level_units) }}</td>
            <td class="ul">{{ $val($p?->college_year_graduated) }}</td>
            <td class="ul">{{ $val($p?->college_scholarship_honors) }}</td>
        </tr>
        <tr>
            <td>GRADUATE STUDIES</td>
            <td class="ul">{{ $val($p?->grad_school) }}</td>
            <td class="ul">{{ $val($p?->grad_degree_course) }}</td>
            <td class="ul">{{ $val($p?->grad_period_from) }} {{ $val($p?->grad_period_to) }}</td>
            <td class="ul">{{ $val($p?->grad_highest_level_units) }}</td>
            <td class="ul">{{ $val($p?->grad_year_graduated) }}</td>
            <td class="ul">{{ $val($p?->grad_scholarship_honors) }}</td>
        </tr>
    </table>
    <p style="font-size:6px;margin-top:0;">(Continue on separate sheet if necessary)</p>

    <div class="footer-block">
        <div class="footer-sig">
            <table class="footer-sig-cols">
                <tr>
                    <td>
                        <div class="footer-sig-label">SIGNATURE</div>
                        <div class="footer-sig-value"><span class="sig-line"></span></div>
                    </td>
                    <td>
                        <div class="footer-sig-label">DATE</div>
                        <div class="footer-sig-value"><span class="date-line"></span></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Page 2 --}}
    <div style="page-break-before: always;"></div>

    @php
        $eligibilities = $p ? $p->civilServiceEligibilities : collect();
        $eligibilityRows = $eligibilities->take(5)->pad(5, null)->values();
        $workRows = $p ? $p->workExperiences->sortBy('sort_order')->values()->all() : [];
        $workEmptyRows = 5;
    @endphp

    <table class="pds-table" style="margin-top:0;">
        <tr>
            <td colspan="6" class="section-header">27. IV. CIVIL SERVICE ELIGIBILITY</td>
        </tr>
        <tr>
            <td class="label-cell" style="font-size:6px;width:18%;">CES/CSEE/CAREER SERVICE/RA 1080 (BOARD/BAR)/UNDER SPECIAL LAWS/CATEGORY II/IV ELIGIBILITY and ELIGIBILITIES FOR UNIFORMED PERSONNEL</td>
            <td class="label-cell" style="width:10%;">RATING (If Applicable)</td>
            <td class="label-cell" style="width:14%;">DATE OF EXAMINATION / CONFERMENT</td>
            <td class="label-cell" style="width:22%;">PLACE OF EXAMINATION / CONFERMENT</td>
            <td class="label-cell" style="width:14%;">LICENSE (if applicable) NUMBER</td>
            <td class="label-cell" style="width:12%;">Valid Until</td>
        </tr>
        @foreach($eligibilityRows as $e)
        <tr>
            <td class="value-cell">{{ $e ? $val($e->eligibility_type) : $na }}</td>
            <td class="value-cell">{{ $e ? $val($e->rating) : $na }}</td>
            <td class="value-cell">{{ $e && $e->date_exam_conferment ? $dateVal($e->date_exam_conferment) : $na }}</td>
            <td class="value-cell">{{ $e ? $val($e->place_exam_conferment) : $na }}</td>
            <td class="value-cell">{{ $e ? $val($e->license_number) : $na }}</td>
            <td class="value-cell">{{ $e && $e->license_valid_until ? $dateVal($e->license_valid_until) : $na }}</td>
        </tr>
        @endforeach
    </table>
    <p style="font-size:6px;margin-top:0;">(Continue on separate sheet if necessary)</p>

    <p style="font-size:7px;margin:2px 0 1px 0;"><strong>28.</strong> V. WORK EXPERIENCE</p>
    <p style="font-size:6px;margin-bottom:2px;">(Include private employment. Start from your recent work. Description of duties should be indicated in the attached Work Experience Sheet.)</p>
    <table class="pds-table">
        <tr>
            <td class="label-cell" colspan="2" style="width:14%;">INCLUSIVE DATES (dd/mm/yyyy)</td>
            <td class="label-cell" style="width:22%;">POSITION TITLE (Write in full/Do not abbreviate)</td>
            <td class="label-cell" style="width:28%;">DEPARTMENT / AGENCY / OFFICE / COMPANY (Write in full/Do not abbreviate)</td>
            <td class="label-cell" style="width:16%;">STATUS OF APPOINTMENT</td>
            <td class="label-cell" style="width:10%;">GOV'T SERVICE (Y/N)</td>
        </tr>
        <tr>
            <td class="label-cell" style="width:7%;">From</td>
            <td class="label-cell" style="width:7%;">To</td>
            <td class="label-cell"></td>
            <td class="label-cell"></td>
            <td class="label-cell"></td>
            <td class="label-cell"></td>
        </tr>
        @foreach($workRows as $w)
        <tr>
            <td class="value-cell">{{ $w && $w->from_date ? $dateVal($w->from_date) : $na }}</td>
            <td class="value-cell">{{ $w && $w->to_date ? $dateVal($w->to_date) : $na }}</td>
            <td class="value-cell">{{ $w ? $val($w->position_title) : $na }}</td>
            <td class="value-cell">{{ $w ? $val($w->department_agency) : $na }}</td>
            <td class="value-cell">{{ $w ? $val($w->status_of_appointment) : $na }}</td>
            <td class="value-cell">{{ $w ? $val($w->govt_service_yn) : $na }}</td>
        </tr>
        @endforeach
        @foreach(range(1, $workEmptyRows) as $i)
        <tr>
            <td class="value-cell">{{ $na }}</td>
            <td class="value-cell">{{ $na }}</td>
            <td class="value-cell">{{ $na }}</td>
            <td class="value-cell">{{ $na }}</td>
            <td class="value-cell">{{ $na }}</td>
            <td class="value-cell">{{ $na }}</td>
        </tr>
        @endforeach
    </table>
    <p style="font-size:6px;margin-top:0;">(Continue on separate sheet if necessary)</p>

    <div class="footer-block">
        <div class="footer-sig">
            <table class="footer-sig-cols">
                <tr>
                    <td>
                        <div class="footer-sig-label">SIGNATURE</div>
                        <div class="footer-sig-value"><span class="sig-line"></span></div>
                    </td>
                    <td>
                        <div class="footer-sig-label">DATE</div>
                        <div class="footer-sig-value"><span class="date-line"></span></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Page 4: Q34–40, References, Declaration, Govt ID, Photo --}}
    <div style="page-break-before: always;"></div>
    @include('reports.partials.pds-pdf-page4')

</body>
</html>
