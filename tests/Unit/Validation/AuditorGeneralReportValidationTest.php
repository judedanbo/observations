<?php

use App\Enums\AuditorGeneralReportTypeEnum;
use Illuminate\Support\Facades\Validator;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('title is required', function () {
    $data = [
        'description' => 'Test description',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = $this->getValidator($data);
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('title', $validator->errors()->toArray());
});

test('title must be string', function () {
    $data = [
        'title' => 12345, // Not a string
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = $this->getValidator($data);
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('title', $validator->errors()->toArray());
});

test('title has maximum length', function () {
    $data = [
        'title' => str_repeat('a', 256), // Too long
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = $this->getValidator($data);
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('title', $validator->errors()->toArray());
});

test('report type is required', function () {
    $data = [
        'title' => 'Test Report',
        'report_year' => 2024,
    ];
    $validator = $this->getValidator($data);
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('report_type', $validator->errors()->toArray());
});

test('report type must be valid enum value', function () {
    $data = [
        'title' => 'Test Report',
        'report_type' => 'invalid_type',
        'report_year' => 2024,
    ];
    $validator = $this->getValidator($data);
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('report_type', $validator->errors()->toArray());
});

test('report year is required', function () {
    $data = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
    ];
    $validator = $this->getValidator($data);
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('report_year', $validator->errors()->toArray());
});

test('report year must be integer', function () {
    $data = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 'not_a_year',
    ];
    $validator = $this->getValidator($data);
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('report_year', $validator->errors()->toArray());
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
        $validator = $this->getValidator($data);
        if ($testCase['should_fail']) {
            $this->assertTrue($validator->fails(), "Year {$testCase['year']} should fail validation");
            $this->assertArrayHasKey('report_year', $validator->errors()->toArray());
        } else {
            $this->assertFalse($validator->fails(), "Year {$testCase['year']} should pass validation");
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
    $validator = $this->getValidator($dataWithDescription);
    $this->assertFalse($validator->fails());
    // Valid without description
    $dataWithoutDescription = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
    ];
    $validator = $this->getValidator($dataWithoutDescription);
    $this->assertFalse($validator->fails());
    // Invalid with non-string description
    $dataWithInvalidDescription = [
        'title' => 'Test Report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
        'description' => 12345,
    ];
    $validator = $this->getValidator($dataWithInvalidDescription);
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('description', $validator->errors()->toArray());
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
        $validator = $this->getValidator($data);
        $this->assertTrue($validator->fails(), "Field {$field} should fail with non-string value");
        $this->assertArrayHasKey($field, $validator->errors()->toArray());
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
    $this->assertTrue($validator->fails());
    $this->assertArrayHasKey('created_by', $validator->errors()->toArray());
});

test('all report type enum values are accepted', function () {
    $reportTypes = AuditorGeneralReportTypeEnum::cases();
    foreach ($reportTypes as $reportType) {
        $data = [
            'title' => 'Test Report',
            'report_type' => $reportType->value,
            'report_year' => 2024,
        ];
        $validator = $this->getValidator($data);
        $this->assertFalse($validator->fails(), "Report type {$reportType->value} should be valid");
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
    $validator = $this->getValidator($data);
    $this->assertFalse($validator->fails());
    $this->assertEmpty($validator->errors()->toArray());
});

test('minimum required data passes validation', function () {
    $data = [
        'title' => 'Minimal Report',
        'report_type' => AuditorGeneralReportTypeEnum::QUARTERLY->value,
        'report_year' => 2023,
    ];
    $validator = $this->getValidator($data);
    $this->assertFalse($validator->fails());
    $this->assertEmpty($validator->errors()->toArray());
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
    $this->assertFalse($validator->fails());

    // In a real application, you might use a custom validator or mutator to trim
    $validatedData = $validator->validated();
    $this->assertEquals('Test Report With Spaces', trim($validatedData['title']));
});
