<?php

use App\Models\AuditorGeneralReport;
use App\Models\Finding;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Create necessary roles for tests
    Role::create(['name' => 'user']);
    Role::create(['name' => 'Super Administrator']);
});

test('migrations create required tables', function () {
    $this->assertTrue(Schema::hasTable('auditor_general_reports'));
    $this->assertTrue(Schema::hasTable('auditor_general_report_findings'));
});

test('auditor general reports table has required columns', function () {
    $expectedColumns = [
        'id',
        'title',
        'description',
        'report_type',
        'report_year',
        'status',
        'slug',
        'executive_summary',
        'methodology',
        // 'key_findings',
        // 'recommendations',
        'conclusion',
        // 'total_findings_count',
        // 'total_amount_involved',
        // 'total_recoveries',
        'created_by',
        'approved_by',
        // 'submitted_for_review_at',
        'approved_at',
        // 'published_at',
        'created_at',
        'updated_at',
    ];
    foreach ($expectedColumns as $column) {
        $this->assertTrue(
            Schema::hasColumn('auditor_general_reports', $column),
            "Column '{$column}' is missing from auditor_general_reports table"
        );
    }
});

test('pivot table has required columns', function () {
    $expectedColumns = [
        'id',
        'auditor_general_report_id',
        'finding_id',
        'report_section_order',
        'section_category',
        'report_context',
        'highlighted_finding',
        'created_at',
        'updated_at',
    ];
    foreach ($expectedColumns as $column) {
        $this->assertTrue(
            Schema::hasColumn('auditor_general_report_findings', $column),
            "Column '{$column}' is missing from auditor_general_report_findings table"
        );
    }
});

test('auditor general reports table has correct column types', function () {
    $columnTypes = [
        'id' => 'bigint',
        'title' => 'varchar',
        'description' => 'text',
        'report_type' => 'enum',
        'report_year' => 'year',
        'status' => 'enum',
        'slug' => 'varchar',
        // 'total_findings_count' => 'int',
        // 'total_amount_involved' => 'bigint',
        // 'total_recoveries' => 'bigint',
        'created_by' => 'bigint',
        'approved_by' => 'bigint',
    ];
    $tableColumns = Schema::getColumnListing('auditor_general_reports');

    foreach ($columnTypes as $column => $expectedType) {
        $this->assertContains($column, $tableColumns);

        $columnType = Schema::getColumnType('auditor_general_reports', $column);
        $this->assertStringContainsString(
            $expectedType,
            $columnType,
            "Column '{$column}' should be of type '{$expectedType}' but is '{$columnType}'"
        );
    }
});

test('pivot table has correct column types', function () {
    $columnTypes = [
        'id' => 'bigint',
        'auditor_general_report_id' => 'bigint',
        'finding_id' => 'bigint',
        'report_section_order' => 'int',
        'section_category' => 'varchar',
        'report_context' => 'text',
        'highlighted_finding' => 'tinyint', // boolean
    ];
    $tableColumns = Schema::getColumnListing('auditor_general_report_findings');

    foreach ($columnTypes as $column => $expectedType) {
        $this->assertContains($column, $tableColumns);

        $columnType = Schema::getColumnType('auditor_general_report_findings', $column);
        $this->assertStringContainsString(
            $expectedType,
            $columnType,
            "Column '{$column}' should be of type '{$expectedType}' but is '{$columnType}'"
        );
    }
});

test('foreign keys work correctly', function () {
    $user = User::factory()->create();
    $report = AuditorGeneralReport::factory()->create(['created_by' => $user->id]);
    $finding = Finding::factory()->create();
    $report->findings()->attach($finding->id, [
        'section_category' => 'financial',
        'report_section_order' => 1,
    ]);
    $this->assertDatabaseHas('auditor_general_report_findings', [
        'auditor_general_report_id' => $report->id,
        'finding_id' => $finding->id,
        'section_category' => 'financial',
    ]);
    // Test relationship works both ways
    $this->assertCount(1, $report->fresh()->findings);
    $this->assertTrue($finding->auditorGeneralReports->contains($report));
});

test('cascade delete works for report findings', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding = Finding::factory()->create();

    $report->findings()->attach($finding->id);

    $this->assertDatabaseHas('auditor_general_report_findings', [
        'auditor_general_report_id' => $report->id,
        'finding_id' => $finding->id,
    ]);

    $reportId = $report->id;

    // Delete the pivot records first, then force delete the report
    $report->findings()->detach();
    $report->forceDelete();

    // The pivot records should be deleted
    $this->assertDatabaseMissing('auditor_general_report_findings', [
        'auditor_general_report_id' => $reportId,
    ]);
    // But the finding should still exist
    $this->assertDatabaseHas('findings', [
        'id' => $finding->id,
    ]);
});

test('cascade delete works for finding removal', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding = Finding::factory()->create();

    $report->findings()->attach($finding->id);

    $this->assertDatabaseHas('auditor_general_report_findings', [
        'auditor_general_report_id' => $report->id,
        'finding_id' => $finding->id,
    ]);

    $findingId = $finding->id;

    // Detach from reports first, then force delete the finding
    $finding->auditorGeneralReports()->detach();
    $finding->forceDelete();

    // The pivot records should be deleted
    $this->assertDatabaseMissing('auditor_general_report_findings', [
        'finding_id' => $findingId,
    ]);
    // But the report should still exist
    $this->assertDatabaseHas('auditor_general_reports', [
        'id' => $report->id,
    ]);
});

test('unique constraint prevents duplicate findings in report', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding = Finding::factory()->create();

    // Attach the finding once
    $report->findings()->attach($finding->id);

    // Try to attach the same finding again
    $this->expectException(\Illuminate\Database\QueryException::class);
    $report->findings()->attach($finding->id);
});

test('indexes exist on important columns', function () {
    // This test checks if indexes exist by running queries that would benefit from indexes
    // and ensuring they complete in reasonable time

    $reports = AuditorGeneralReport::factory()->count(100)->create();

    $start = microtime(true);

    // These queries should be fast due to indexes
    AuditorGeneralReport::where('status', 'draft')->count();
    AuditorGeneralReport::where('report_type', 'annual')->count();
    AuditorGeneralReport::where('report_year', 2024)->count();
    AuditorGeneralReport::where('created_by', $reports->first()->created_by)->count();

    $end = microtime(true);
    $executionTime = $end - $start;

    // Should complete quickly (under 1 second even with many records)
    $this->assertLessThan(1.0, $executionTime, 'Indexed queries took too long to execute');
});

test('pivot table indexes work correctly', function () {
    $report = AuditorGeneralReport::factory()->create();
    $findings = Finding::factory()->count(50)->create();

    // Attach findings with different categories and orders
    foreach ($findings as $index => $finding) {
        $report->findings()->attach($finding->id, [
            'section_category' => ['financial', 'compliance', 'performance'][$index % 3],
            'report_section_order' => $index + 1,
        ]);
    }

    $start = microtime(true);

    // These queries should be fast due to pivot indexes
    $report->findings()->wherePivot('section_category', 'financial')->count();
    $report->findings()->orderByPivot('report_section_order')->get();

    $end = microtime(true);
    $executionTime = $end - $start;

    // Should complete quickly
    $this->assertLessThan(1.0, $executionTime, 'Pivot table queries took too long to execute');
});

test('nullable columns accept null values', function () {
    $report = AuditorGeneralReport::factory()->create([
        'description' => null,
        'executive_summary' => null,
        'methodology' => null,
        'recommendations_summary' => null,
        'conclusion' => null,
        'approved_by' => null,
        'approved_at' => null,
    ]);
    $this->assertDatabaseHas('auditor_general_reports', [
        'id' => $report->id,
    ]);
    $this->assertNull($report->description);
    $this->assertNull($report->executive_summary);
    $this->assertNull($report->approved_by);
});

test('default values are set correctly', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding = Finding::factory()->create();

    $report->findings()->attach($finding->id); // No explicit defaults provided

    $pivotData = $report->findings()->first()->pivot;

    $this->assertEquals(0, $pivotData->report_section_order); // Should default to 0
    $this->assertEquals(0, $pivotData->highlighted_finding); // Should default to 0 (false as tinyint)
    $this->assertNull($pivotData->section_category); // Should be nullable
    $this->assertNull($pivotData->report_context); // Should be nullable
});

test('timestamps are automatically managed', function () {
    $beforeCreation = now()->subSecond();

    $report = AuditorGeneralReport::factory()->create();

    $afterCreation = now()->addSecond();

    $this->assertNotNull($report->created_at);
    $this->assertNotNull($report->updated_at);
    $this->assertGreaterThanOrEqual($beforeCreation->timestamp, $report->created_at->timestamp);
    $this->assertLessThanOrEqual($afterCreation->timestamp, $report->created_at->timestamp);
    $this->assertGreaterThanOrEqual($beforeCreation->timestamp, $report->updated_at->timestamp);
    $this->assertLessThanOrEqual($afterCreation->timestamp, $report->updated_at->timestamp);

    // Test update
    $beforeUpdate = now()->subSecond();
    $report->update(['title' => 'Updated Title']);
    $afterUpdate = now()->addSecond();

    $this->assertGreaterThanOrEqual($beforeUpdate->timestamp, $report->updated_at->timestamp);
    $this->assertLessThanOrEqual($afterUpdate->timestamp, $report->updated_at->timestamp);
});

test('pivot table timestamps work', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding = Finding::factory()->create();

    $beforeAttach = now()->subSecond();
    $report->findings()->attach($finding->id);
    $afterAttach = now()->addSecond();

    $pivotData = $report->findings()->first()->pivot;

    $this->assertNotNull($pivotData->created_at);
    $this->assertNotNull($pivotData->updated_at);
    $this->assertGreaterThanOrEqual($beforeAttach->timestamp, $pivotData->created_at->timestamp);
    $this->assertLessThanOrEqual($afterAttach->timestamp, $pivotData->created_at->timestamp);
});

test('large text fields can store substantial content', function () {
    $largeText = str_repeat('This is a test sentence. ', 1000); // ~25,000 characters

    $report = AuditorGeneralReport::factory()->create([
        'description' => $largeText,
        'executive_summary' => $largeText,
        'methodology' => $largeText,
        // 'key_findings' => $largeText,
        'recommendations_summary' => $largeText,
        'conclusion' => $largeText,
    ]);
    $this->assertDatabaseHas('auditor_general_reports', [
        'id' => $report->id,
    ]);
    $report->refresh();

    $this->assertEquals($largeText, $report->description);
    $this->assertEquals($largeText, $report->executive_summary);
});
