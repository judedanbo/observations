<?php

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use App\Models\AuditorGeneralReport;
use App\Models\Finding;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Seed roles and permissions
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    // Create and authenticate user
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Administrator');
    $this->actingAs($this->user);
});

test('authenticated user can access reports list', function () {
    $reports = AuditorGeneralReport::factory()->count(3)->create();

    // Verify reports exist in database
    expect(AuditorGeneralReport::count())->toBe(3);

    foreach ($reports as $report) {
        $this->assertDatabaseHas('auditor_general_reports', [
            'id' => $report->id,
            'title' => $report->title,
        ]);
    }
});

test('created_by is set automatically when authenticated', function () {
    // User is authenticated from beforeEach
    expect(auth()->check())->toBeTrue();

    // Create a report without explicitly setting created_by
    $report = AuditorGeneralReport::factory()->create([
        'title' => 'Auto Created Report',
    ]);

    // The created_by field should be automatically set to the authenticated user
    expect($report->created_by)->toBe(auth()->id());

    $this->assertDatabaseHas('auditor_general_reports', [
        'id' => $report->id,
        'created_by' => auth()->id(),
    ]);
});

test('user can create auditor general report', function () {
    $reportData = [
        'title' => 'Test Report 2024',
        'description' => 'Test description for the report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL,
        'report_year' => 2024,
        'period_start' => '2024-01-01',
        'period_end' => '2024-12-31',
        'executive_summary' => 'Executive summary content',
        'methodology' => 'Methodology used in the audit',
        'status' => AuditorGeneralReportStatusEnum::DRAFT,
        'created_by' => $this->user->id,
    ];

    $report = AuditorGeneralReport::create($reportData);

    $this->assertDatabaseHas('auditor_general_reports', [
        'title' => 'Test Report 2024',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
        'created_by' => $this->user->id,
        'status' => AuditorGeneralReportStatusEnum::DRAFT->value,
    ]);

    expect($report)->toBeInstanceOf(AuditorGeneralReport::class);
    expect($report->title)->toBe('Test Report 2024');
});

test('user can add findings to report', function () {
    $report = AuditorGeneralReport::factory()->create(['created_by' => $this->user->id]);
    $findings = Finding::factory()->count(3)->create();
    foreach ($findings as $index => $finding) {
        $report->findings()->attach($finding->id, [
            'section_category' => 'financial',
            'report_section_order' => $index + 1,
            'highlighted_finding' => false,
            'report_context' => 'Context for finding ' . ($index + 1),
        ]);
    }
    $report->refresh();
    expect($report->findings)->toHaveCount(3);
    $this->assertDatabaseHas('auditor_general_report_findings', [
        'auditor_general_report_id' => $report->id,
        'finding_id' => $findings->first()->id,
        'section_category' => 'financial',
    ]);
});

test('totals are recalculated when findings are added', function () {
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $this->user->id,
    ]);
    $finding1 = Finding::factory()->create(['amount' => 25000]);
    $finding2 = Finding::factory()->create(['amount' => 15000]);
    $report->findings()->attach([$finding1->id, $finding2->id]);
    $report->calculateTotals();
    $report->save();
    $report->refresh();

    // Verify totals using accessor methods
    expect($report->total_findings_count)->toBe(2);
    expect($report->total_amount_involved)->toBe(40000.0);
});

test('user can update report when in draft status', function () {
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $this->user->id,
        'status' => AuditorGeneralReportStatusEnum::DRAFT,
        'title' => 'Original Title',
    ]);

    expect($report->canBeEdited())->toBeTrue();

    $report->update([
        'title' => 'Updated Title',
        'description' => 'Updated description',
    ]);

    $report->refresh();
    expect($report->title)->toBe('Updated Title');
    expect($report->description)->toBe('Updated description');

    $this->assertDatabaseHas('auditor_general_reports', [
        'id' => $report->id,
        'title' => 'Updated Title',
        'description' => 'Updated description',
    ]);
});

test('user cannot update report when published', function () {
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $this->user->id,
        'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
        'title' => 'Original Title',
    ]);
    expect($report->canBeEdited())->toBeFalse();
});

test('user can submit report for review', function () {
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $this->user->id,
        'status' => AuditorGeneralReportStatusEnum::DRAFT,
    ]);
    expect($report->canBeEdited())->toBeTrue();
    $report->markAsUnderReview();
    $report->refresh();
    expect($report->status)->toBe(AuditorGeneralReportStatusEnum::UNDER_REVIEW);
});

test('user can filter reports by status', function () {
    $draftReport = AuditorGeneralReport::factory()->create([
        'status' => AuditorGeneralReportStatusEnum::DRAFT,
    ]);
    $publishedReport = AuditorGeneralReport::factory()->create([
        'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
    ]);
    $draftReports = AuditorGeneralReport::where('status', AuditorGeneralReportStatusEnum::DRAFT)->get();
    $publishedReports = AuditorGeneralReport::where('status', AuditorGeneralReportStatusEnum::PUBLISHED)->get();
    expect($draftReports)->toHaveCount(1);
    expect($publishedReports)->toHaveCount(1);
    expect($draftReports->contains($draftReport))->toBeTrue();
    expect($publishedReports->contains($publishedReport))->toBeTrue();
});

test('user can filter reports by year', function () {
    $report2023 = AuditorGeneralReport::factory()->create(['report_year' => 2023]);
    $report2024 = AuditorGeneralReport::factory()->create(['report_year' => 2024]);
    $reports2024 = AuditorGeneralReport::where('report_year', 2024)->get();
    $reports2023 = AuditorGeneralReport::where('report_year', 2023)->get();
    expect($reports2024)->toHaveCount(1);
    expect($reports2023)->toHaveCount(1);
    expect($reports2024->contains($report2024))->toBeTrue();
    expect($reports2023->contains($report2023))->toBeTrue();
});

test('user can search reports by title', function () {
    $report1 = AuditorGeneralReport::factory()->create(['title' => 'Annual Budget Report 2024']);
    $report2 = AuditorGeneralReport::factory()->create(['title' => 'Quarterly Performance Review']);
    $budgetReports = AuditorGeneralReport::where('title', 'like', '%Budget%')->get();
    $performanceReports = AuditorGeneralReport::where('title', 'like', '%Performance%')->get();
    expect($budgetReports)->toHaveCount(1);
    expect($performanceReports)->toHaveCount(1);
    expect($budgetReports->contains($report1))->toBeTrue();
    expect($performanceReports->contains($report2))->toBeTrue();
});

test('user can remove findings from report', function () {
    $report = AuditorGeneralReport::factory()->create(['created_by' => $this->user->id]);
    $findings = Finding::factory()->count(3)->create();
    $report->findings()->attach($findings->pluck('id')->toArray());
    expect($report->findings)->toHaveCount(3);
    // Remove one finding
    $report->findings()->detach($findings->first()->id);
    $report->refresh();
    expect($report->findings)->toHaveCount(2);
    expect($report->findings->contains($findings->first()))->toBeFalse();
});

test('user can reorder findings in report', function () {
    $report = AuditorGeneralReport::factory()->create(['created_by' => $this->user->id]);
    $finding1 = Finding::factory()->create(['title' => 'First Finding']);
    $finding2 = Finding::factory()->create(['title' => 'Second Finding']);
    $report->findings()->attach([
        $finding1->id => ['report_section_order' => 2],
        $finding2->id => ['report_section_order' => 1],
    ]);
    $orderedFindings = $report->findings()->orderBy('auditor_general_report_findings.report_section_order')->get();
    expect($orderedFindings->first()->title)->toBe('Second Finding');
    expect($orderedFindings->last()->title)->toBe('First Finding');
    // Update the order
    $report->findings()->updateExistingPivot($finding1->id, ['report_section_order' => 1]);
    $report->findings()->updateExistingPivot($finding2->id, ['report_section_order' => 2]);
    $reorderedFindings = $report->findings()->orderBy('auditor_general_report_findings.report_section_order')->get();
    expect($reorderedFindings->first()->title)->toBe('First Finding');
    expect($reorderedFindings->last()->title)->toBe('Second Finding');
});

test('user can highlight findings in report', function () {
    $report = AuditorGeneralReport::factory()->create(['created_by' => $this->user->id]);
    $finding = Finding::factory()->create();
    $report->findings()->attach($finding->id, ['highlighted_finding' => true]);
    $highlightedFindings = $report->findings()->wherePivot('highlighted_finding', true)->get();
    expect($highlightedFindings)->toHaveCount(1);
    expect($highlightedFindings->contains($finding))->toBeTrue();
});

test('user can categorize findings in report', function () {
    $report = AuditorGeneralReport::factory()->create(['created_by' => $this->user->id]);
    $finding1 = Finding::factory()->create();
    $finding2 = Finding::factory()->create();
    $report->findings()->attach([
        $finding1->id => ['section_category' => 'financial'],
        $finding2->id => ['section_category' => 'compliance'],
    ]);
    $financialFindings = $report->findings()->wherePivot('section_category', 'financial')->get();
    $complianceFindings = $report->findings()->wherePivot('section_category', 'compliance')->get();
    expect($financialFindings)->toHaveCount(1);
    expect($complianceFindings)->toHaveCount(1);
    expect($financialFindings->contains($finding1))->toBeTrue();
    expect($complianceFindings->contains($finding2))->toBeTrue();
});

test('report slug is generated automatically', function () {
    $report = AuditorGeneralReport::factory()->create([
        'title' => 'Annual Report 2024 - Government Performance',
    ]);
    expect($report->slug)->not->toBeNull();
    expect(strtolower($report->slug))->toContain('annual-report-2024');
});

test('duplicate report titles for same year and type are handled', function () {
    $firstReport = AuditorGeneralReport::factory()->create([
        'title' => 'Annual Report 2024',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL,
        'report_year' => 2024,
    ]);
    // This should still work as Laravel will handle slug uniqueness
    $secondReport = AuditorGeneralReport::factory()->create([
        'title' => 'Annual Report 2024',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL,
        'report_year' => 2024,
    ]);
    expect($secondReport->slug)->not->toBe($firstReport->slug);
    expect($firstReport->slug)->toBe('annual-report-2024');
    expect($secondReport->slug)->toBe('annual-report-2024-2');
});
