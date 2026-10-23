<?php

return [

    'types' => [
        'ayuda'       => 'Ayuda',
        'scholarship' => 'Scholarship',
        'job'         => 'Job',
        'training'    => 'Training',
    ],

    'statuses' => [
        'draft'     => 'Draft',
        'published' => 'Published',
        'closed'    => 'Closed',
    ],

    'year_levels' => [
        'shs11'    => 'Senior High, Grade 11',
        'shs12'    => 'Senior High, Grade 12',
        'college1' => 'College, 1st year',
        'college2' => 'College, 2nd year',
        'college3' => 'College, 3rd year',
        'college4' => 'College, 4th year',
        'college5' => 'College, 5th year',
        'graduate' => 'Graduate or post-graduate',
    ],

    'school_types' => [
        'public'  => 'Public (SUC, LUC)',
        'private' => 'Private',
    ],

    'sectors' => [
        '4ps'              => '4Ps beneficiary',
        'pwd'              => 'Person with disability (PWD)',
        'senior'           => 'Senior citizen',
        'solo_parent'      => 'Solo parent',
        'ip'               => 'Indigenous people',
        'farmer_fisher'    => 'Farmer or fisherfolk',
        'osy'              => 'Out-of-school youth',
        'displaced_worker' => 'Displaced or unemployed worker',
        'ofw_family'       => 'OFW or family of an OFW',
        'low_income'       => 'Low-income household',
    ],

    // Which "Who can apply" fields each listing type uses.
    // Fields not listed here are hidden in the form and ignored when saving.
    'targeting' => [
        'ayuda'       => ['location', 'sectors', 'age', 'income'],
        'scholarship' => ['location', 'year_levels', 'school_types', 'courses', 'sectors', 'age', 'income'],
        'job'         => ['location', 'courses', 'sectors', 'age'],
        'training'    => ['location', 'sectors', 'age'],
    ],

    // Optional detail fields. The organization picks only the ones it needs.
    'detail_fields' => [
        'scholarship' => [
            'benefits'  => ['label' => 'Benefits', 'input' => 'textarea', 'hint' => 'Tuition, monthly allowance, books...'],
            'slots'     => ['label' => 'Number of slots', 'input' => 'text', 'hint' => 'e.g. 50'],
            'gwa'       => ['label' => 'Minimum grade or GWA', 'input' => 'text', 'hint' => 'e.g. 85% or 2.0'],
            'documents' => ['label' => 'Documents needed', 'input' => 'textarea', 'hint' => 'One per line'],
            'renewal'   => ['label' => 'Renewal conditions', 'input' => 'textarea', 'hint' => ''],
        ],
        'ayuda' => [
            'assistance' => ['label' => 'Type of assistance', 'input' => 'text', 'hint' => 'e.g. Cash aid, rice, medical'],
            'amount'     => ['label' => 'Amount or items', 'input' => 'text', 'hint' => ''],
            'venue'      => ['label' => 'Venue', 'input' => 'text', 'hint' => ''],
            'schedule'   => ['label' => 'Schedule or distribution dates', 'input' => 'text', 'hint' => ''],
            'documents'  => ['label' => 'What to bring', 'input' => 'textarea', 'hint' => 'IDs, forms, certificates'],
        ],
        'job' => [
            'salary'          => ['label' => 'Salary range', 'input' => 'text', 'hint' => 'e.g. PHP 18,000 to 22,000 per month'],
            'employment_type' => ['label' => 'Employment type', 'input' => 'select', 'options' => ['Full-time', 'Part-time', 'Contract', 'Internship']],
            'work_setup'      => ['label' => 'Work setup', 'input' => 'select', 'options' => ['On-site', 'Remote', 'Hybrid']],
            'experience'      => ['label' => 'Experience required', 'input' => 'text', 'hint' => 'e.g. Fresh graduates welcome'],
            'vacancies'       => ['label' => 'Number of vacancies', 'input' => 'text', 'hint' => ''],
            'benefits'        => ['label' => 'Benefits', 'input' => 'textarea', 'hint' => ''],
        ],
        'training' => [
            'duration'     => ['label' => 'Duration', 'input' => 'text', 'hint' => 'e.g. 3 months'],
            'schedule'     => ['label' => 'Schedule', 'input' => 'text', 'hint' => 'e.g. Mon to Fri, 8 AM to 5 PM'],
            'certificate'  => ['label' => 'Certificate', 'input' => 'text', 'hint' => 'e.g. NC II'],
            'cost'         => ['label' => 'Cost or allowance', 'input' => 'text', 'hint' => 'e.g. Free, with daily allowance'],
            'slots'        => ['label' => 'Number of slots', 'input' => 'text', 'hint' => ''],
            'requirements' => ['label' => 'Requirements', 'input' => 'textarea', 'hint' => ''],
        ],
    ],

    'question_types' => [
        'text'     => 'Short answer',
        'textarea' => 'Paragraph',
        'number'   => 'Number',
        'date'     => 'Date',
        'select'   => 'Dropdown',
        'radio'    => 'Choose one',
        'checkbox' => 'Choose many',
    ],

    'choice_types' => ['select', 'radio', 'checkbox'],
];
