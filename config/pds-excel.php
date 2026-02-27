<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PDS Excel template path
    |--------------------------------------------------------------------------
    | The app looks for the first .xlsx in storage/app/pds-templates/.
    | You can override with a full path here, e.g. storage_path('app/pds-templates/CS-Form-212.xlsx').
    */
    'template_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Cell mapping: PDS field => [ 'sheet' => index, 'cell' => 'A1' ]
    |--------------------------------------------------------------------------
    | Fill these after running: php artisan pds:dump-excel-template
    | Sheet index is 0-based. Leave null for fields not in the template.
    */
    'cells' => [
        // Section I – Personal Information (Sheet 0 = Page 1)
        'surname' => ['sheet' => 0, 'cell' => 'D10'],
        'name_extension' => ['sheet' => 0, 'cell' => 'L12'],
        'first_name' => ['sheet' => 0, 'cell' => 'D11'],
        'middle_name' => ['sheet' => 0, 'cell' => 'D12'],
        'date_of_birth' => ['sheet' => 0, 'cell' => 'D13'],
        'place_of_birth' => ['sheet' => 0, 'cell' => 'D15'],
        'sex' => ['sheet' => 0, 'cell' => 'D16'],
        'civil_status' => ['sheet' => 0, 'cell' => 'D17'],
        'civil_status_other' => ['sheet' => 0, 'cell' => 'D19'],
        'height' => ['sheet' => 0, 'cell' => 'D22'],
        'weight' => ['sheet' => 0, 'cell' => 'D24'],
        'blood_type' => ['sheet' => 0, 'cell' => 'D25'],
        'umid_id' => ['sheet' => 0, 'cell' => 'D27'],
        'pagibig_id' => ['sheet' => 0, 'cell' => 'D29'],
        'philhealth_no' => ['sheet' => 0, 'cell' => 'D31'],
        'philsys_number' => ['sheet' => 0, 'cell' => 'D32'],
        'tin_no' => ['sheet' => 0, 'cell' => 'D33'],
        'agency_employee_no' => ['sheet' => 0, 'cell' => 'D34'],
        'date_of_appointment' => null,
        'citizenship' => ['sheet' => 0, 'cell' => 'J13'],
        'dual_citizenship_type' => ['sheet' => 0, 'cell' => 'J15'],
        'dual_citizenship_country' => ['sheet' => 0, 'cell' => 'J16'],
        'residential_house_no' => ['sheet' => 0, 'cell' => 'I17'],
        'residential_street' => ['sheet' => 0, 'cell' => 'L17'],
        'residential_subdivision' => ['sheet' => 0, 'cell' => 'I19'],
        'residential_barangay' => ['sheet' => 0, 'cell' => 'L19'],
        'residential_city' => ['sheet' => 0, 'cell' => 'I22'],
        'residential_province' => ['sheet' => 0, 'cell' => 'L22'],
        'residential_zip' => ['sheet' => 0, 'cell' => 'I24'],
        'permanent_house_no' => ['sheet' => 0, 'cell' => 'I25'],
        'permanent_street' => ['sheet' => 0, 'cell' => 'L25'],
        'permanent_subdivision' => ['sheet' => 0, 'cell' => 'I27'],
        'permanent_barangay' => ['sheet' => 0, 'cell' => 'L27'],
        'permanent_city' => ['sheet' => 0, 'cell' => 'I29'],
        'permanent_province' => ['sheet' => 0, 'cell' => 'L29'],
        'permanent_zip' => ['sheet' => 0, 'cell' => 'I31'],
        'telephone' => ['sheet' => 0, 'cell' => 'I32'],
        'mobile' => ['sheet' => 0, 'cell' => 'I33'],
        'email_address' => ['sheet' => 0, 'cell' => 'I34'],

        // Section II – Family Background
        'spouse_surname' => ['sheet' => 0, 'cell' => 'D36'],
        'spouse_name_extension' => ['sheet' => 0, 'cell' => 'G38'],
        'spouse_first_name' => ['sheet' => 0, 'cell' => 'D37'],
        'spouse_middle_name' => ['sheet' => 0, 'cell' => 'D38'],
        'spouse_occupation' => ['sheet' => 0, 'cell' => 'D39'],
        'spouse_employer_business_name' => ['sheet' => 0, 'cell' => 'D40'],
        'spouse_business_address' => ['sheet' => 0, 'cell' => 'D41'],
        'spouse_telephone' => ['sheet' => 0, 'cell' => 'D42'],
        'children_names' => null,
        'father_surname' => ['sheet' => 0, 'cell' => 'D43'],
        'father_first_name' => ['sheet' => 0, 'cell' => 'D44'],
        'father_middle_name' => ['sheet' => 0, 'cell' => 'D45'],
        'father_name_extension' => ['sheet' => 0, 'cell' => 'G45'],
        'mother_surname' => ['sheet' => 0, 'cell' => 'D47'],
        'mother_first_name' => ['sheet' => 0, 'cell' => 'D48'],
        'mother_middle_name' => ['sheet' => 0, 'cell' => 'D49'],
        'mother_name_extension' => ['sheet' => 0, 'cell' => 'G49'],

        // Section III – Educational Background (rows 54–58 = elem, secondary, voc, college, grad)
        'elem_school' => ['sheet' => 0, 'cell' => 'D54'],
        'elem_degree_course' => ['sheet' => 0, 'cell' => 'G54'],
        'elem_period_from' => ['sheet' => 0, 'cell' => 'J54'],
        'elem_period_to' => ['sheet' => 0, 'cell' => 'K54'],
        'elem_highest_level_units' => ['sheet' => 0, 'cell' => 'L54'],
        'elem_year_graduated' => ['sheet' => 0, 'cell' => 'M54'],
        'elem_scholarship_honors' => ['sheet' => 0, 'cell' => 'N54'],
        'secondary_school' => ['sheet' => 0, 'cell' => 'D55'],
        'secondary_degree_course' => ['sheet' => 0, 'cell' => 'G55'],
        'secondary_period_from' => ['sheet' => 0, 'cell' => 'J55'],
        'secondary_period_to' => ['sheet' => 0, 'cell' => 'K55'],
        'secondary_highest_level_units' => ['sheet' => 0, 'cell' => 'L55'],
        'secondary_year_graduated' => ['sheet' => 0, 'cell' => 'M55'],
        'secondary_scholarship_honors' => ['sheet' => 0, 'cell' => 'N55'],
        'voc_school' => ['sheet' => 0, 'cell' => 'D56'],
        'voc_degree_course' => ['sheet' => 0, 'cell' => 'G56'],
        'voc_period_from' => ['sheet' => 0, 'cell' => 'J56'],
        'voc_period_to' => ['sheet' => 0, 'cell' => 'K56'],
        'voc_highest_level_units' => ['sheet' => 0, 'cell' => 'L56'],
        'voc_year_graduated' => ['sheet' => 0, 'cell' => 'M56'],
        'voc_scholarship_honors' => ['sheet' => 0, 'cell' => 'N56'],
        'college_school' => ['sheet' => 0, 'cell' => 'D57'],
        'college_degree_course' => ['sheet' => 0, 'cell' => 'G57'],
        'college_period_from' => ['sheet' => 0, 'cell' => 'J57'],
        'college_period_to' => ['sheet' => 0, 'cell' => 'K57'],
        'college_highest_level_units' => ['sheet' => 0, 'cell' => 'L57'],
        'college_year_graduated' => ['sheet' => 0, 'cell' => 'M57'],
        'college_scholarship_honors' => ['sheet' => 0, 'cell' => 'N57'],
        'grad_school' => ['sheet' => 0, 'cell' => 'D58'],
        'grad_degree_course' => ['sheet' => 0, 'cell' => 'G58'],
        'grad_period_from' => ['sheet' => 0, 'cell' => 'J58'],
        'grad_period_to' => ['sheet' => 0, 'cell' => 'K58'],
        'grad_highest_level_units' => ['sheet' => 0, 'cell' => 'L58'],
        'grad_year_graduated' => ['sheet' => 0, 'cell' => 'M58'],
        'grad_scholarship_honors' => ['sheet' => 0, 'cell' => 'N58'],
    ],

    /*
    |--------------------------------------------------------------------------
    | 23. Name of children (I37–I48) + Date of birth dd/mm/yyyy (M37–M48)
    |--------------------------------------------------------------------------
    */
    'children' => [
        'sheet' => 0,
        'start_row' => 37,
        'max_rows' => 12,
        'name_column' => 'I',
        'dob_column' => 'M',
    ],

    /*
    |--------------------------------------------------------------------------
    | Photo cell (passport-sized photo placeholder in template)
    |--------------------------------------------------------------------------
    */
    'photo' => ['sheet' => 3, 'cell' => 'K51'],

    /*
    |--------------------------------------------------------------------------
    | Repeating sections (civil service, work experience, etc.)
    |--------------------------------------------------------------------------
    | start_row = first data row; columns = field => column letter.
    */
    'civil_service' => [
        'sheet' => 1,
        'start_row' => 5,
        'max_rows' => 7,
        'columns' => [
            'eligibility_type' => 'A',
            'rating' => 'F',
            'date_exam_conferment' => 'G',
            'place_exam_conferment' => 'I',
            'license_number' => 'J',
            'license_valid_until' => 'K',
        ],
    ],
    'work_experience' => [
        'sheet' => 1,
        'start_row' => 18,
        'max_rows' => 28,
        'columns' => [
            'from_date' => 'A',
            'to_date' => 'C',
            'position_title' => 'D',
            'department_agency' => 'G',
            'status_of_appointment' => 'J',
            'govt_service_yn' => 'K',
        ],
    ],
    'voluntary_work' => [
        'sheet' => 2,
        'start_row' => 6,
        'max_rows' => 12,
        'columns' => [
            'conducted_sponsored_by' => 'A',
            'inclusive_dates_from' => 'E',
            'inclusive_dates_to' => 'F',
            'number_of_hours' => 'G',
            'position_nature_of_work' => 'H',
        ],
    ],
    'learning_development' => [
        'sheet' => 2,
        'start_row' => 18,
        'max_rows' => 21, // rows 18–38 (CONDUCTED/ SPONSORED BY in column I)
        'columns' => [
            'title_of_ld' => 'A',
            'type_of_ld' => 'H',
            'number_of_hours' => 'G',
            'inclusive_dates_from' => 'E',
            'inclusive_dates_to' => 'F',
            'organization_name_address' => 'I', // CONDUCTED/ SPONSORED BY (Write in full) — I18:I38
        ],
    ],
    'other_information' => [
        'sheet' => 2,
        'start_row' => 42,
        'max_rows' => 7, // A42:A48, C42:C48, I42:I48
        'columns' => [
            'special_skills_hobbies' => 'A',
            'non_academic_distinctions' => 'C',
            'membership_in_associations' => 'I',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page 4 (Sheet 3): Q34–40 YES/NO — put "✓ YES" or "✓ NO" before the word
    |--------------------------------------------------------------------------
    | If answer is Y: write "✓ YES" in yes_cell. If N: write "✓ NO" in no_cell. If blank: leave both empty.
    */
    'page4_yn' => [
        'sheet' => 3,
        'check_char' => '✓',
        'questions' => [
            'related_third_degree_yn'   => ['yes_cell' => 'H6',  'no_cell' => 'K6'],   // 34a
            'related_fourth_degree_yn'  => ['yes_cell' => 'H8',  'no_cell' => 'K8'],   // 34b
            'admin_offense_yn'          => ['yes_cell' => 'H13', 'no_cell' => 'K13'],  // 35a
            'criminally_charged_yn'     => ['yes_cell' => 'H18', 'no_cell' => 'K18'],  // 35b
            'convicted_yn'             => ['yes_cell' => 'H23', 'no_cell' => 'K23'],  // 36
            'separated_from_service_yn' => ['yes_cell' => 'H27', 'no_cell' => 'K27'], // 37
            'candidate_election_yn'    => ['yes_cell' => 'H31', 'no_cell' => 'K31'],  // 38a
            'resigned_campaign_yn'     => ['yes_cell' => 'H34', 'no_cell' => 'K34'],  // 38b
            'immigrant_resident_yn'     => ['yes_cell' => 'H37', 'no_cell' => 'K37'],  // 39
            'indigenous_group_yn'      => ['yes_cell' => 'H43', 'no_cell' => 'K43'],  // 40a
            'pwd_yn'                   => ['yes_cell' => 'H45', 'no_cell' => 'K45'],  // 40b
            'solo_parent_yn'           => ['yes_cell' => 'H47', 'no_cell' => 'K47'],  // 40c
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page 4 (Sheet 3): "If YES, give details" — write only when corresponding Y/N is Y
    |--------------------------------------------------------------------------
    */
    'page4_details' => [
        'sheet' => 3,
        'items' => [
            ['yn_field' => ['related_third_degree_yn', 'related_fourth_degree_yn'], 'value_field' => 'related_authority_details', 'cell' => 'H11'],   // 34
            ['yn_field' => 'admin_offense_yn', 'value_field' => 'admin_offense_details', 'cell' => 'H15'],                                           // 35a
            ['yn_field' => 'criminally_charged_yn', 'value_field' => 'criminally_charged_date_filed', 'cell' => 'K20'],                                // 35b Date Filed
            ['yn_field' => 'criminally_charged_yn', 'value_field' => 'criminally_charged_status', 'cell' => 'K21'],                                  // 35b Status of Case/s
            ['yn_field' => 'convicted_yn', 'value_field' => 'convicted_details', 'cell' => 'H25'],                                                    // 36
            ['yn_field' => 'separated_from_service_yn', 'value_field' => 'separated_from_service_details', 'cell' => 'H29'],                           // 37
            ['yn_field' => 'candidate_election_yn', 'value_field' => 'candidate_election_details', 'cell' => 'K32'],                                  // 38a
            ['yn_field' => 'resigned_campaign_yn', 'value_field' => 'resigned_campaign_details', 'cell' => 'K35'],                                    // 38b
            ['yn_field' => 'immigrant_resident_yn', 'value_field' => 'immigrant_resident_details', 'cell' => 'H39'],                                   // 39
            ['yn_field' => 'indigenous_group_yn', 'value_field' => 'indigenous_group_specify', 'cell' => 'L44'],                                     // 40a
            ['yn_field' => 'pwd_yn', 'value_field' => 'pwd_id_no', 'cell' => 'L46'],                                                                  // 40b
            ['yn_field' => 'solo_parent_yn', 'value_field' => 'solo_parent_id_no', 'cell' => 'L48'],                                                  // 40c
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page 4 (Sheet 3): References — NAME (A52:A54), ADDRESS (F52:F54), CONTACT (G52:G54)
    |--------------------------------------------------------------------------
    */
    'page4_references' => [
        'sheet' => 3,
        'rows' => [52, 53, 54],
        'columns' => [
            'name' => 'A',
            'address' => 'F',
            'contact' => 'G',
        ],
        'fields' => [
            ['ref1_name', 'ref1_address', 'ref1_contact'],
            ['ref2_name', 'ref2_address', 'ref2_contact'],
            ['ref3_name', 'ref3_address', 'ref3_contact'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page 4 (Sheet 3): Government Issued ID — D61, D62, D63
    |--------------------------------------------------------------------------
    */
    'page4_govt_id' => [
        'sheet' => 3,
        'cells' => [
            'govt_id_type' => 'D61',
            'govt_id_number' => 'D62',
            'govt_id_place_date_issue' => 'D64',
        ],
    ],
];
