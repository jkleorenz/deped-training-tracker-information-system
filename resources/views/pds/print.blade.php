<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Data Sheet - {{ $surname }}, {{ $firstName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 0 auto;
            background: white;
        }

        @media print {
            .page {
                width: 100%;
                padding: 5mm;
                page-break-after: always;
            }
            .page:last-child {
                page-break-after: auto;
            }
        }

        /* Header */
        .pds-header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .pds-header h1 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .pds-header h2 {
            font-size: 11pt;
            font-weight: bold;
        }

        .pds-header .warning {
            font-size: 7pt;
            color: #c00;
            margin-top: 4px;
            font-style: italic;
        }

        /* Section styling */
        .pds-section {
            margin-bottom: 10px;
            border: 1px solid #000;
        }

        .section-header {
            background-color: #808080;
            color: #fff;
            padding: 3px 6px;
            font-weight: bold;
            font-size: 9pt;
        }

        .section-subheader {
            background-color: #d3d3d3;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
        }

        /* Tables */
        .pds-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pds-table td, .pds-table th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .pds-table th {
            background-color: #f0f0f0;
            font-size: 8pt;
            text-align: left;
        }

        .pds-table .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 25%;
        }

        .pds-table .value {
            width: 25%;
        }

        /* Photo box */
        .photo-box {
            width: 120px;
            height: 150px;
            border: 1px solid #000;
            float: right;
            margin-left: 10px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9f9f9;
            overflow: hidden;
        }

        .photo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            font-size: 8pt;
            color: #666;
        }

        /* Clearfix */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Name display */
        .name-display {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* Small text */
        .small-text {
            font-size: 7pt;
        }

        /* Checkbox styling */
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 3px;
            text-align: center;
            line-height: 10px;
            font-size: 10px;
        }

        .checkbox.checked {
            background-color: #000;
            color: #fff;
        }

        /* Yes/No questions */
        .yn-question {
            margin-bottom: 6px;
        }

        .yn-options {
            display: inline-block;
            margin-left: 10px;
        }

        /* Multi-column layout */
        .two-col {
            display: table;
            width: 100%;
        }

        .two-col > div {
            display: table-cell;
            width: 50%;
            padding: 4px;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }

        /* Footer note */
        .footer-note {
            font-size: 7pt;
            text-align: center;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #ccc;
        }

        /* Signature section */
        .signature-section {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 250px;
            display: inline-block;
            margin-top: 20px;
        }

        /* Data tables for repeating sections */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .data-table th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            border: 1px solid #000;
            font-size: 7pt;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
        }

        .data-table .center {
            text-align: center;
        }

        /* Empty row styling */
        .empty-row td {
            height: 20px;
        }

        /* References table */
        .references-table td {
            height: 25px;
        }

        /* Government ID section */
        .govt-id-section {
            margin-top: 15px;
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mb-2 { margin-bottom: 8px; }
        .p-2 { padding: 8px; }
        .border { border: 1px solid #000; }
    </style>
</head>
<body>
    <!-- PAGE 1: Personal Information, Family Background, Educational Background -->
    <div class="page">
        <!-- Header -->
        <div class="pds-header">
            <h1>CS FORM 212 (Revised 2017)</h1>
            <h2>PERSONAL DATA SHEET</h2>
            <div class="warning">
                WARNING: Any misrepresentation made in the Personal Data Sheet and the Work Experience Sheet shall cause the filing of administrative/criminal case/s against the person concerned.
            </div>
        </div>

        <!-- Personal Information -->
        <div class="pds-section">
            <div class="section-header">I. PERSONAL INFORMATION</div>
            <div style="padding: 8px;">
                <div class="photo-box">
                    @if($photoPath && file_exists($photoPath))
                        <img src="{{ $photoPath }}" alt="ID Photo">
                    @else
                        <span class="photo-placeholder">ID Picture<br>(Passport size)<br>4.5 cm x 3.5 cm<br>(taken within<br>the last<br>6 months)</span>
                    @endif
                </div>

                <table class="pds-table" style="width: calc(100% - 140px);">
                    <tr>
                        <td class="label">2. SURNAME</td>
                        <td class="value" colspan="3">{{ $surname }}</td>
                    </tr>
                    <tr>
                        <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;FIRST NAME</td>
                        <td class="value">{{ $firstName }}</td>
                        <td class="label" style="width: 15%;">NAME EXTENSION<br>(JR., SR.)</td>
                        <td class="value" style="width: 15%;">{{ $nameExtension }}</td>
                    </tr>
                    <tr>
                        <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;MIDDLE NAME</td>
                        <td class="value" colspan="3">{{ $middleName }}</td>
                    </tr>
                    <tr>
                        <td class="label">3. DATE OF BIRTH<br>&nbsp;&nbsp;&nbsp;&nbsp;(mm/dd/yyyy)</td>
                        <td class="value">{{ $dateOfBirth }}</td>
                        <td class="label">16. CITIZENSHIP</td>
                        <td class="value">{{ $citizenship }}</td>
                    </tr>
                    <tr>
                        <td class="label">4. PLACE OF BIRTH</td>
                        <td class="value" colspan="3">{{ $placeOfBirth }}</td>
                    </tr>
                    <tr>
                        <td class="label">5. SEX</td>
                        <td class="value">{{ $sex }}</td>
                        <td class="label" rowspan="2">17. RESIDENTIAL ADDRESS</td>
                        <td class="value" rowspan="2">{{ $residentialAddress }}</td>
                    </tr>
                    <tr>
                        <td class="label">6. CIVIL STATUS</td>
                        <td class="value">{{ $civilStatus }}</td>
                    </tr>
                    <tr>
                        <td class="label">7. HEIGHT (m)</td>
                        <td class="value">{{ $height }}</td>
                        <td class="label">18. PERMANENT ADDRESS</td>
                        <td class="value">{{ $permanentAddress }}</td>
                    </tr>
                    <tr>
                        <td class="label">8. WEIGHT (kg)</td>
                        <td class="value">{{ $weight }}</td>
                        <td class="label">19. TELEPHONE NO.</td>
                        <td class="value">{{ $telephone }}</td>
                    </tr>
                    <tr>
                        <td class="label">9. BLOOD TYPE</td>
                        <td class="value">{{ $bloodType }}</td>
                        <td class="label">20. MOBILE NO.</td>
                        <td class="value">{{ $mobile }}</td>
                    </tr>
                    <tr>
                        <td class="label">10. GSIS ID NO.</td>
                        <td class="value">{{ $gsisId }}</td>
                        <td class="label">21. EMAIL ADDRESS</td>
                        <td class="value">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td class="label">11. PAG-IBIG ID NO.</td>
                        <td class="value">{{ $pagibigId }}</td>
                        <td class="label" colspan="2" rowspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label">12. PHILHEALTH NO.</td>
                        <td class="value">{{ $philhealthNo }}</td>
                    </tr>
                    <tr>
                        <td class="label">13. SSS NO.</td>
                        <td class="value">{{ $philsysNumber }}</td>
                    </tr>
                    <tr>
                        <td class="label">14. TIN NO.</td>
                        <td class="value">{{ $tinNo }}</td>
                    </tr>
                    <tr>
                        <td class="label">15. AGENCY EMPLOYEE NO.</td>
                        <td class="value">{{ $agencyEmployeeNo }}</td>
                        <td class="label" colspan="2"></td>
                    </tr>
                </table>
                <div class="clearfix"></div>
            </div>
        </div>

        <!-- Family Background -->
        <div class="pds-section">
            <div class="section-header">II. FAMILY BACKGROUND</div>
            <table class="pds-table">
                <tr>
                    <td class="label" style="width: 20%;">22. SPOUSE'S SURNAME</td>
                    <td style="width: 30%;">{{ $spouse['surname'] }}</td>
                    <td class="label" style="width: 20%;">23. NAME of CHILDREN<br>(Write full name and list all)</td>
                    <td class="label" style="width: 30%;">DATE OF BIRTH<br>(mm/dd/yyyy)</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;FIRST NAME</td>
                    <td>{{ $spouse['firstName'] }}</td>
                    <td rowspan="12" style="vertical-align: top;">
                        @foreach($children as $index => $child)
                            @if($index < 12)
                                {{ $child['name'] }}<br>
                            @endif
                        @endforeach
                    </td>
                    <td rowspan="12" style="vertical-align: top;">
                        @foreach($children as $index => $child)
                            @if($index < 12)
                                {{ $child['dob'] }}<br>
                            @endif
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;MIDDLE NAME</td>
                    <td>{{ $spouse['middleName'] }}</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;OCCUPATION</td>
                    <td>{{ $spouse['occupation'] }}</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;EMPLOYER/BUSINESS NAME</td>
                    <td>{{ $spouse['employer'] }}</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;BUSINESS ADDRESS</td>
                    <td>{{ $spouse['businessAddress'] }}</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;TELEPHONE NO.</td>
                    <td>{{ $spouse['telephone'] }}</td>
                </tr>
                <tr>
                    <td class="label">24. FATHER'S SURNAME</td>
                    <td>{{ $father['surname'] }}</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;FIRST NAME</td>
                    <td>{{ $father['firstName'] }}</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;MIDDLE NAME</td>
                    <td>{{ $father['middleName'] }}</td>
                </tr>
                <tr>
                    <td class="label">25. MOTHER'S MAIDEN NAME</td>
                    <td>{{ $mother['surname'] }}</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;FIRST NAME</td>
                    <td>{{ $mother['firstName'] }}</td>
                </tr>
                <tr>
                    <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;MIDDLE NAME</td>
                    <td>{{ $mother['middleName'] }}</td>
                </tr>
            </table>
        </div>

        <!-- Educational Background -->
        <div class="pds-section">
            <div class="section-header">III. EDUCATIONAL BACKGROUND</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">26. LEVEL</th>
                        <th style="width: 25%;">NAME OF SCHOOL<br>(Write in full)</th>
                        <th style="width: 20%;">BASIC EDUCATION/DEGREE/COURSE<br>(Write in full)</th>
                        <th style="width: 10%;">PERIOD OF ATTENDANCE<br>From To</th>
                        <th style="width: 10%;">HIGHEST LEVEL/ UNITS EARNED<br>(if not graduated)</th>
                        <th style="width: 5%;">YEAR GRADUATED</th>
                        <th style="width: 5%;">SCHOLARSHIP/ ACADEMIC HONORS RECEIVED</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-bold">ELEMENTARY</td>
                        <td>{{ $education['elementary']['school'] }}</td>
                        <td>{{ $education['elementary']['degree'] }}</td>
                        <td class="center">{{ $education['elementary']['periodFrom'] }} - {{ $education['elementary']['periodTo'] }}</td>
                        <td>{{ $education['elementary']['highestLevel'] }}</td>
                        <td class="center">{{ $education['elementary']['yearGraduated'] }}</td>
                        <td>{{ $education['elementary']['honors'] }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">SECONDARY</td>
                        <td>{{ $education['secondary']['school'] }}</td>
                        <td>{{ $education['secondary']['degree'] }}</td>
                        <td class="center">{{ $education['secondary']['periodFrom'] }} - {{ $education['secondary']['periodTo'] }}</td>
                        <td>{{ $education['secondary']['highestLevel'] }}</td>
                        <td class="center">{{ $education['secondary']['yearGraduated'] }}</td>
                        <td>{{ $education['secondary']['honors'] }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">VOCATIONAL/<br>TRADE COURSE</td>
                        <td>{{ $education['vocational']['school'] }}</td>
                        <td>{{ $education['vocational']['degree'] }}</td>
                        <td class="center">{{ $education['vocational']['periodFrom'] }} - {{ $education['vocational']['periodTo'] }}</td>
                        <td>{{ $education['vocational']['highestLevel'] }}</td>
                        <td class="center">{{ $education['vocational']['yearGraduated'] }}</td>
                        <td>{{ $education['vocational']['honors'] }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">COLLEGE</td>
                        <td>{{ $education['college']['school'] }}</td>
                        <td>{{ $education['college']['degree'] }}</td>
                        <td class="center">{{ $education['college']['periodFrom'] }} - {{ $education['college']['periodTo'] }}</td>
                        <td>{{ $education['college']['highestLevel'] }}</td>
                        <td class="center">{{ $education['college']['yearGraduated'] }}</td>
                        <td>{{ $education['college']['honors'] }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">GRADUATE STUDIES</td>
                        <td>{{ $education['graduate']['school'] }}</td>
                        <td>{{ $education['graduate']['degree'] }}</td>
                        <td class="center">{{ $education['graduate']['periodFrom'] }} - {{ $education['graduate']['periodTo'] }}</td>
                        <td>{{ $education['graduate']['highestLevel'] }}</td>
                        <td class="center">{{ $education['graduate']['yearGraduated'] }}</td>
                        <td>{{ $education['graduate']['honors'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer-note">
            (Continue on separate sheet if necessary)
        </div>
    </div>

    <!-- PAGE 2: Civil Service Eligibility and Work Experience -->
    <div class="page page-break">
        <!-- Civil Service Eligibility -->
        <div class="pds-section">
            <div class="section-header">IV. CIVIL SERVICE ELIGIBILITY</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">27. CAREER SERVICE/ RA 1080 (BOARD/ BAR) UNDER SPECIAL LAWS/ CES/ CSEE<br>BARANGAY ELIGIBILITY / DRIVER'S LICENSE</th>
                        <th style="width: 10%;">RATING<br>(If applicable)</th>
                        <th style="width: 15%;">DATE OF EXAMINATION / CONFERMENT</th>
                        <th style="width: 25%;">PLACE OF EXAMINATION / CONFERMENT</th>
                        <th style="width: 10%;">LICENSE<br>(if applicable)</th>
                        <th style="width: 10%;">VALIDITY</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eligibilities as $eligibility)
                    <tr>
                        <td>{{ $eligibility['type'] }}</td>
                        <td class="center">{{ $eligibility['rating'] }}</td>
                        <td class="center">{{ $eligibility['date_exam'] }}</td>
                        <td>{{ $eligibility['place'] }}</td>
                        <td>{{ $eligibility['license_number'] }}</td>
                        <td class="center">{{ $eligibility['license_valid_until'] }}</td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="6">&nbsp;</td></tr>
                    @endforelse
                    @for($i = count($eligibilities); $i < 7; $i++)
                    <tr class="empty-row"><td colspan="6">&nbsp;</td></tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Work Experience -->
        <div class="pds-section">
            <div class="section-header">V. WORK EXPERIENCE</div>
            <div class="section-subheader">
                (Include private employment. Start from your recent work) Description of duties should be indicated in the attached Work Experience sheet.
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">28. INCLUSIVE DATES<br>(mm/dd/yyyy)</th>
                        <th style="width: 5%;"></th>
                        <th style="width: 25%;">POSITION TITLE<br>(Write in full/Do not abbreviate)</th>
                        <th style="width: 25%;">DEPARTMENT / AGENCY / OFFICE / COMPANY<br>(Write in full/Do not abbreviate)</th>
                        <th style="width: 15%;">MONTHLY SALARY</th>
                        <th style="width: 10%;">STATUS OF APPOINTMENT</th>
                        <th style="width: 8%;">GOV'T SERVICE<br>(Y/N)</th>
                    </tr>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workExperiences as $work)
                    <tr>
                        <td class="center">{{ $work['from_date'] }}</td>
                        <td class="center">{{ $work['to_date'] }}</td>
                        <td>{{ $work['position'] }}</td>
                        <td>{{ $work['department'] }}</td>
                        <td></td>
                        <td>{{ $work['status'] }}</td>
                        <td class="center">{{ $work['govt_service'] }}</td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="7">&nbsp;</td></tr>
                    @endforelse
                    @for($i = count($workExperiences); $i < 28; $i++)
                    <tr class="empty-row"><td colspan="7">&nbsp;</td></tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="footer-note">
            (Continue on separate sheet if necessary)
        </div>
    </div>

    <!-- PAGE 3: Voluntary Work, Learning & Development, Other Information -->
    <div class="page page-break">
        <!-- Voluntary Work -->
        <div class="pds-section">
            <div class="section-header">VI. VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">29. NAME & ADDRESS OF ORGANIZATION<br>(Write in full)</th>
                        <th style="width: 25%;">INCLUSIVE DATES<br>(mm/dd/yyyy)</th>
                        <th style="width: 10%;">NUMBER OF HOURS</th>
                        <th style="width: 30%;">POSITION / NATURE OF WORK</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>From &nbsp;&nbsp;&nbsp;&nbsp; To</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($voluntaryWorks as $work)
                    <tr>
                        <td>{{ $work['organization'] }}</td>
                        <td class="center">{{ $work['from_date'] }} &nbsp;&nbsp; {{ $work['to_date'] }}</td>
                        <td class="center">{{ $work['hours'] }}</td>
                        <td>{{ $work['position'] }}</td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="4">&nbsp;</td></tr>
                    @endforelse
                    @for($i = count($voluntaryWorks); $i < 7; $i++)
                    <tr class="empty-row"><td colspan="4">&nbsp;</td></tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Learning & Development -->
        <div class="pds-section">
            <div class="section-header">VII. LEARNING AND DEVELOPMENT (L&D) INTERVENTIONS/TRAINING PROGRAMS ATTENDED</div>
            <div class="section-subheader">
                (Start from the most recent L&D/training program and include only the relevant L&D/training taken for the last five (5) years for Division Chief/Executive/Managerial positions)
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">30. TITLE OF LEARNING AND DEVELOPMENT INTERVENTIONS/TRAINING PROGRAMS<br>(Write in full)</th>
                        <th style="width: 10%;">INCLUSIVE DATES<br>OF ATTENDANCE<br>(mm/dd/yyyy)</th>
                        <th style="width: 8%;"></th>
                        <th style="width: 8%;">NUMBER OF HOURS</th>
                        <th style="width: 12%;">Type of LD<br>(Managerial/ Supervisory/ Technical/etc)</th>
                        <th style="width: 32%;">CONDUCTED/ SPONSORED BY<br>(Write in full)</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>From</th>
                        <th>To</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($learningDevelopments as $ld)
                    <tr>
                        <td>{{ $ld['title'] }}</td>
                        <td class="center">{{ $ld['from_date'] }}</td>
                        <td class="center">{{ $ld['to_date'] }}</td>
                        <td class="center">{{ $ld['hours'] }}</td>
                        <td>{{ $ld['type'] }}</td>
                        <td>{{ $ld['organization'] }}</td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="6">&nbsp;</td></tr>
                    @endforelse
                    @for($i = count($learningDevelopments); $i < 21; $i++)
                    <tr class="empty-row"><td colspan="6">&nbsp;</td></tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Other Information -->
        <div class="pds-section">
            <div class="section-header">VIII. OTHER INFORMATION</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 33%;">31. SPECIAL SKILLS and HOBBIES</th>
                        <th style="width: 33%;">32. NON-ACADEMIC DISTINCTIONS / RECOGNITION<br>(Write in full)</th>
                        <th style="width: 34%;">33. MEMBERSHIP IN ASSOCIATION/ORGANIZATION<br>(Write in full)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxOther = max(count($specialSkills), count($nonAcademic), count($memberships), 7);
                    @endphp
                    @for($i = 0; $i < $maxOther; $i++)
                    <tr class="empty-row">
                        <td>{{ $specialSkills[$i] ?? '' }}</td>
                        <td>{{ $nonAcademic[$i] ?? '' }}</td>
                        <td>{{ $memberships[$i] ?? '' }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="footer-note">
            (Continue on separate sheet if necessary)
        </div>
    </div>

    <!-- PAGE 4: Questions 34-40, References, Government ID -->
    <div class="page page-break">
        <!-- Page 4 Questions -->
        <div class="pds-section">
            <div class="section-header">34-40. QUESTIONS</div>
            <table class="pds-table">
                <tr>
                    <td colspan="2">
                        <div class="yn-question">
                            <strong>34.</strong> Are you related by consanguinity or affinity to the appointing or recommending authority, or to the chief of bureau or office or to the person who has immediate supervision over you in the Office, Bureau or Department where you will be appointed,
                        </div>
                        <div style="margin-left: 20px;">
                            <div class="yn-question">
                                a. within the third degree?<br>
                                <span class="yn-options">
                                    <span class="checkbox {{ $page4Questions['related_third_degree']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['related_third_degree']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                    <span class="checkbox {{ $page4Questions['related_third_degree']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['related_third_degree']['answer'] === 'N' ? '✓' : '' }}</span> NO
                                </span>
                                @if($page4Questions['related_third_degree']['answer'] === 'Y' && $page4Questions['related_third_degree']['details'])
                                    <br><span style="margin-left: 20px;">If YES, give details: {{ $page4Questions['related_third_degree']['details'] }}</span>
                                @endif
                            </div>
                            <div class="yn-question">
                                b. within the fourth degree (for Local Government Unit - Career Employees)?<br>
                                <span class="yn-options">
                                    <span class="checkbox {{ $page4Questions['related_fourth_degree']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['related_fourth_degree']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                    <span class="checkbox {{ $page4Questions['related_fourth_degree']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['related_fourth_degree']['answer'] === 'N' ? '✓' : '' }}</span> NO
                                </span>
                                @if($page4Questions['related_fourth_degree']['answer'] === 'Y' && $page4Questions['related_third_degree']['details'])
                                    <br><span style="margin-left: 20px;">If YES, give details: {{ $page4Questions['related_third_degree']['details'] }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="yn-question">
                            <strong>35.</strong> a. Have you ever been found guilty of any administrative offense?<br>
                            <span class="yn-options">
                                <span class="checkbox {{ $page4Questions['admin_offense']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['admin_offense']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                <span class="checkbox {{ $page4Questions['admin_offense']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['admin_offense']['answer'] === 'N' ? '✓' : '' }}</span> NO
                            </span>
                            @if($page4Questions['admin_offense']['answer'] === 'Y' && $page4Questions['admin_offense']['details'])
                                <br><span style="margin-left: 20px;">If YES, give details: {{ $page4Questions['admin_offense']['details'] }}</span>
                            @endif
                        </div>
                        <div class="yn-question" style="margin-left: 20px;">
                            b. Have you been criminally charged before any court?<br>
                            <span class="yn-options">
                                <span class="checkbox {{ $page4Questions['criminally_charged']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['criminally_charged']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                <span class="checkbox {{ $page4Questions['criminally_charged']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['criminally_charged']['answer'] === 'N' ? '✓' : '' }}</span> NO
                            </span>
                            @if($page4Questions['criminally_charged']['answer'] === 'Y')
                                <br><span style="margin-left: 20px;">If YES, give details:</span>
                                <div style="margin-left: 40px;">
                                    Date Filed: {{ $page4Questions['criminally_charged']['date_filed'] ?? '' }}<br>
                                    Status of Case/s: {{ $page4Questions['criminally_charged']['status'] ?? '' }}
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="yn-question">
                            <strong>36.</strong> Have you ever been convicted of any crime or violation of any law, decree, ordinance or regulation by any court or tribunal?<br>
                            <span class="yn-options">
                                <span class="checkbox {{ $page4Questions['convicted']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['convicted']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                <span class="checkbox {{ $page4Questions['convicted']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['convicted']['answer'] === 'N' ? '✓' : '' }}</span> NO
                            </span>
                            @if($page4Questions['convicted']['answer'] === 'Y' && $page4Questions['convicted']['details'])
                                <br><span style="margin-left: 20px;">If YES, give details: {{ $page4Questions['convicted']['details'] }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="yn-question">
                            <strong>37.</strong> Have you ever been separated from the service in any of the following modes: resignation, retirement, dropped from the rolls, dismissal, termination, end of term, finished contract or phased out (abolition) in the public or private sector?<br>
                            <span class="yn-options">
                                <span class="checkbox {{ $page4Questions['separated_from_service']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['separated_from_service']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                <span class="checkbox {{ $page4Questions['separated_from_service']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['separated_from_service']['answer'] === 'N' ? '✓' : '' }}</span> NO
                            </span>
                            @if($page4Questions['separated_from_service']['answer'] === 'Y' && $page4Questions['separated_from_service']['details'])
                                <br><span style="margin-left: 20px;">If YES, give details: {{ $page4Questions['separated_from_service']['details'] }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="yn-question">
                            <strong>38.</strong> a. Have you ever been a candidate in a national or local election held within the last year (except Barangay election)?<br>
                            <span class="yn-options">
                                <span class="checkbox {{ $page4Questions['candidate_election']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['candidate_election']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                <span class="checkbox {{ $page4Questions['candidate_election']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['candidate_election']['answer'] === 'N' ? '✓' : '' }}</span> NO
                            </span>
                            @if($page4Questions['candidate_election']['answer'] === 'Y' && $page4Questions['candidate_election']['details'])
                                <br><span style="margin-left: 20px;">If YES, give details: {{ $page4Questions['candidate_election']['details'] }}</span>
                            @endif
                        </div>
                        <div class="yn-question" style="margin-left: 20px;">
                            b. Have you resigned from the government service during the three (3)-month period before the last election to promote/actively campaign for a national or local candidate?<br>
                            <span class="yn-options">
                                <span class="checkbox {{ $page4Questions['resigned_campaign']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['resigned_campaign']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                <span class="checkbox {{ $page4Questions['resigned_campaign']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['resigned_campaign']['answer'] === 'N' ? '✓' : '' }}</span> NO
                            </span>
                            @if($page4Questions['resigned_campaign']['answer'] === 'Y' && $page4Questions['resigned_campaign']['details'])
                                <br><span style="margin-left: 20px;">If YES, give details: {{ $page4Questions['resigned_campaign']['details'] }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="yn-question">
                            <strong>39.</strong> Have you acquired the status of an immigrant or permanent resident of another country?<br>
                            <span class="yn-options">
                                <span class="checkbox {{ $page4Questions['immigrant_resident']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['immigrant_resident']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                <span class="checkbox {{ $page4Questions['immigrant_resident']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['immigrant_resident']['answer'] === 'N' ? '✓' : '' }}</span> NO
                            </span>
                            @if($page4Questions['immigrant_resident']['answer'] === 'Y' && $page4Questions['immigrant_resident']['details'])
                                <br><span style="margin-left: 20px;">If YES, give details (country): {{ $page4Questions['immigrant_resident']['details'] }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="yn-question">
                            <strong>40.</strong> Pursuant to: (a) Indigenous People's Act (RA 8371); (b) Magna Carta for Disabled Persons (RA 7277, as amended); and (c) Expanded Solo Parents Welfare Act (RA 11861), please answer the following items:<br>
                            <div style="margin-left: 20px;">
                                a. Are you a member of any indigenous group?<br>
                                <span class="yn-options">
                                    <span class="checkbox {{ $page4Questions['indigenous_group']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['indigenous_group']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                    <span class="checkbox {{ $page4Questions['indigenous_group']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['indigenous_group']['answer'] === 'N' ? '✓' : '' }}</span> NO
                                </span>
                                @if($page4Questions['indigenous_group']['answer'] === 'Y' && $page4Questions['indigenous_group']['details'])
                                    <br><span style="margin-left: 20px;">If YES, please specify: {{ $page4Questions['indigenous_group']['details'] }}</span>
                                @endif
                            </div>
                            <div style="margin-left: 20px; margin-top: 8px;">
                                b. Are you a person with disability?<br>
                                <span class="yn-options">
                                    <span class="checkbox {{ $page4Questions['pwd']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['pwd']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                    <span class="checkbox {{ $page4Questions['pwd']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['pwd']['answer'] === 'N' ? '✓' : '' }}</span> NO
                                </span>
                                @if($page4Questions['pwd']['answer'] === 'Y' && $page4Questions['pwd']['id_no'])
                                    <br><span style="margin-left: 20px;">If YES, please specify ID No.: {{ $page4Questions['pwd']['id_no'] }}</span>
                                @endif
                            </div>
                            <div style="margin-left: 20px; margin-top: 8px;">
                                c. Are you a solo parent?<br>
                                <span class="yn-options">
                                    <span class="checkbox {{ $page4Questions['solo_parent']['answer'] === 'Y' ? 'checked' : '' }}">{{ $page4Questions['solo_parent']['answer'] === 'Y' ? '✓' : '' }}</span> YES
                                    <span class="checkbox {{ $page4Questions['solo_parent']['answer'] === 'N' ? 'checked' : '' }}">{{ $page4Questions['solo_parent']['answer'] === 'N' ? '✓' : '' }}</span> NO
                                </span>
                                @if($page4Questions['solo_parent']['answer'] === 'Y' && $page4Questions['solo_parent']['id_no'])
                                    <br><span style="margin-left: 20px;">If YES, please specify ID No.: {{ $page4Questions['solo_parent']['id_no'] }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- References -->
        <div class="pds-section">
            <div class="section-header">41. REFERENCES</div>
            <table class="data-table references-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">NAME</th>
                        <th style="width: 40%;">ADDRESS</th>
                        <th style="width: 20%;">TEL. NO.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($references as $ref)
                    <tr>
                        <td>{{ $ref['name'] }}</td>
                        <td>{{ $ref['address'] }}</td>
                        <td>{{ $ref['contact'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Government ID & Declaration -->
        <div class="pds-section">
            <div class="section-header">42. GOVERNMENT ISSUED ID</div>
            <table class="pds-table">
                <tr>
                    <td style="width: 50%;">
                        <div style="margin-bottom: 10px;">
                            <strong>Government Issued ID:</strong> {{ $govtId['type'] }}<br>
                            <strong>ID/License/Passport No.:</strong> {{ $govtId['number'] }}<br>
                            <strong>Date/Place of Issuance:</strong> {{ $govtId['place_date_issue'] }}
                        </div>
                        <div style="margin-top: 20px;">
                            <strong>Date Accomplished:</strong> {{ $dateAccomplished }}
                        </div>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: bottom;">
                        <div style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px;">
                            <strong>SIGNATURE OVER PRINTED NAME</strong>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Declaration -->
        <div class="pds-section" style="margin-top: 15px;">
            <div style="padding: 10px; font-size: 8pt;">
                <p style="margin-bottom: 10px;">
                    <strong>DECLARATION:</strong> I declare under oath that I have personally accomplished this Personal Data Sheet which is a true, correct and complete statement pursuant to the provisions of pertinent laws, rules and regulations of the Republic of the Philippines. I authorize the agency head/authorized representative to verify/validate the contents stated herein. I agree that any misrepresentation made in this document and its attachments shall cause the filing of administrative/criminal case/s against me.
                </p>
            </div>
        </div>

        <div class="footer-note" style="margin-top: 20px;">
            CS FORM 212 (Revised 2017), Page 4 of 4
        </div>
    </div>
</body>
</html>
