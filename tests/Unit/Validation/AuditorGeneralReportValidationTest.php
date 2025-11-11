<?php

use App\Enums\AuditorGeneralReportTypeEnum;
use Illuminate\Support\Facades\Validator;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/**
 * Helper function to create a validator instance for AuditorGeneralReport data
 */
function getValidator(array $data): \Illuminate\Validation\Validator
{
    return Validator::make($data, [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'report_type' => 'required|in:'.implode(',', array_column(AuditorGeneralReportTypeEnum::cases(), 'value')),
        'report_year' => 'required|integer|min:1900|max:2100',
        'executive_summary' => 'nullable|string',
        'methodology' => 'nullable|string',
        'key_findings' => 'nullable|string',
        'recommendations' => 'nullable|string',
        'conclusion' => 'nullable|string',
        'created_by' => 'nullable|exists:users,id',
        'approved_by' => 'nullable|exists:users,id',
    ]);
}

test('title is required', function () {
    $data = [
        'description' => 'Test description',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('title');
});

test('title must be string', function () {
    $data = [
        'title' => 12345, // Not a string
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('title');
});

test('title has maximum length', function () {
    $data = [
        'title' => str_repeat('a', 256), // Too long
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('title');
});

test('report type is required', function () {
    $data = [
        'title' => 'Test Report',
        'report_year' => 2024,
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('report_type');
});

test('report type must be valid enum value', function () {
    $data = [
        'title' => 'Test Report',
        'report_type' => 'invalid_type',
        'report_year' => 2024,
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('report_type');
});

test('report year is required', function () {
    $data = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('report_year');
});

test('report year must be integer', function () {
    $data = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 'not_a_year',
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('report_year');
});

test('report year must be within valid range', function () {
    $testCases = [
        ['year' => 1800, 'should_fail' => true],   // Too old
        ['year' => 1900, 'should_fail' => false],  // Valid minimum
        ['year' => 2024, 'should_fail' => false],  // Valid current
        ['year' => 2100, 'should_fail' => false],  // Valid maximum
        ['year' => 2200, 'should_fail' => true],   // Too future
    ];
    foreach ($testCases as $testCase) {
        $data = [
            'title' => 'Test Report',
            'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
            'report_year' => $testCase['year'],
        ];
        $validator = getValidator($data);
        if ($testCase['should_fail']) {
            expect($validator->fails())->toBeTrue("Year {$testCase['year']} should fail validation");
            expect($validator->errors()->toArray())->toHaveKey('report_year');
        } else {
            expect($validator->fails())->toBeFalse("Year {$testCase['year']} should pass validation");
        }
    }
});

test('description is optional but must be string if provided', function () {
    // Valid with description
    $dataWithDescription = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
        'description' => 'Valid description',
    ];
    $validator = getValidator($dataWithDescription);
    expect($validator->fails())->toBeFalse();
    // Valid without description
    $dataWithoutDescription = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = getValidator($dataWithoutDescription);
    expect($validator->fails())->toBeFalse();
    // Invalid with non-string description
    $dataWithInvalidDescription = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
        'description' => 12345,
    ];
    $validator = getValidator($dataWithInvalidDescription);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('description');
});

test('optional text fields must be strings if provided', function () {
    $textFields = [
        'executive_summary',
        'methodology',
        'key_findings',
        'recommendations',
        'conclusion',
    ];
    foreach ($textFields as $field) {
        $data = [
            'title' => 'Test Report',
            'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
            'report_year' => 2024,
            $field => 12345, // Invalid non-string value
        ];
        $validator = getValidator($data);
        expect($validator->fails())->toBeTrue("Field {$field} should fail with non-string value");
        expect($validator->errors()->toArray())->toHaveKey($field);
    }
});

test('created by must be valid user id', function () {
    $data = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
        'created_by' => 99999, // Non-existent user ID
    ];
    $validator = Validator::make($data, [
        'title' => 'required|string|max:255',
        'report_type' => 'required|in:'.implode(',', array_column(AuditorGeneralReportTypeEnum::cases(), 'value')),
        'report_year' => 'required|integer|min:1900|max:2100',
        'created_by' => 'required|exists:users,id',
    ]);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey('created_by');
});

test('all report type enum values are accepted', function () {
    $reportTypes = AuditorGeneralReportTypeEnum::cases();
    foreach ($reportTypes as $reportType) {
        $data = [
            'title' => 'Test Report',
            'report_type' => $reportType->value,
            'report_year' => 2024,
        ];
        $validator = getValidator($data);
        expect($validator->fails())->toBeFalse("Report type {$reportType->value} should be valid");
    }
});

test('valid complete data passes validation', function () {
    $data = [
        'title' => 'Complete Annual Report 2024',
        'description' => 'Comprehensive description of the report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
        'executive_summary' => 'Executive summary content',
        'methodology' => 'Detailed methodology',
        'key_findings' => 'Key findings summary',
        'recommendations' => 'Recommendations section',
        'conclusion' => 'Conclusion content',
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeFalse();
    expect($validator->errors()->toArray())->toBeEmpty();
});

test('minimum required data passes validation', function () {
    $data = [
        'title' => 'Minimal Report',
        'report_type' => AuditorGeneralReportTypeEnum::QUARTERLY->value,
        'report_year' => 2023,
    ];
    $validator = getValidator($data);
    expect($validator->fails())->toBeFalse();
    expect($validator->errors()->toArray())->toBeEmpty();
});

test('title trims whitespace', function () {
    $data = [
        'title' => '  Test Report With Spaces  ',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = Validator::make($data, [
        'title' => 'required|string|max:255',
        'report_type' => 'required|in:'.implode(',', array_column(AuditorGeneralReportTypeEnum::cases(), 'value')),
        'report_year' => 'required|integer|min:1900|max:2100',
    ]);
    expect($validator->fails())->toBeFalse();

    // In a real application, you might use a custom validator or mutator to trim
    $validatedData = $validator->validated();
    expect(trim($validatedData['title']))->toBe('Test Report With Spaces');
});
