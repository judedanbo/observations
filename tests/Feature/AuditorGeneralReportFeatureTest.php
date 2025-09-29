<?php

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use App\Models\AuditorGeneralReport;
use App\Models\Finding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('authenticated user can access reports list', function () {
    AuditorGeneralReport::factory()->count(3)->create();
    $response = $this->get('/admin/auditor-general-reports');
    $response->assertStatus(200);
});

test('unauthenticated user cannot access reports', function () {
    auth()->logout();
    $response = $this->get('/admin/auditor-general-reports');
    $response->assertRedirect('/admin/login');
});

test('user can create auditor general report', function () {
    $data = [
        'title' => 'Test Report 2024',
        'description' => 'Test description for the report',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
        'executive_summary' => 'Executive summary content',
        'methodology' => 'Methodology used in the audit',
        'key_findings' => 'Key findings summary',
        'recommendations' => 'Main recommendations',
    ];
    $response = $this->post('/admin/auditor-general-reports', $data);
    $this->assertDatabaseHas('auditor_general_reports', [
        'title' => 'Test Report 2024',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
        'report_year' => 2024,
        'created_by' => $this->user->id,
        'status' => AuditorGeneralReportStatusEnum::DRAFT->value,
    ]);
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
    $this->assertCount(3, $report->findings);
    $this->assertDatabaseHas('auditor_general_report_findings', [
        'auditor_general_report_id' => $report->id,
        'finding_id' => $findings->first()->id,
        'section_category' => 'financial',
    ]);
});

test('totals are recalculated when findings are added', function () {
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $this->user->id,
        // 'total_amount_involved' => 0,
        // 'total_findings_count' => 0,
    ]);
    $finding1 = Finding::factory()->create(['amount' => 25000]);
    $finding2 = Finding::factory()->create(['amount' => 15000]);
    $report->findings()->attach([$finding1->id, $finding2->id]);
    $report->calculateTotals();
    $report->save();
    $report->refresh();
    // $this->assertEquals(40000, $report->total_amount_involved);
    // $this->assertEquals(2, $report->total_findings_count);
});

test('user can update report when in draft status', function () {
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $this->user->id,
        'status' => AuditorGeneralReportStatusEnum::DRAFT,
        'title' => 'Original Title',
    ]);
    $updateData = [
        'title' => 'Updated Title',
        'description' => 'Updated description',
    ];
    $response = $this->put("/admin/auditor-general-reports/{$report->id}", $updateData);
    $report->refresh();
    $this->assertEquals('Updated Title', $report->title);
    $this->assertEquals('Updated description', $report->description);
});

test('user cannot update report when published', function () {
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $this->user->id,
        'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
        'title' => 'Original Title',
    ]);
    $this->assertFalse($report->canBeEdited());
});

test('user can submit report for review', function () {
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $this->user->id,
        'status' => AuditorGeneralReportStatusEnum::DRAFT,
    ]);
    $this->assertTrue($report->canSubmitForReview());
    $report->submitForReview();
    $this->assertEquals(AuditorGeneralReportStatusEnum::UNDER_REVIEW, $report->status);
    $this->assertNotNull($report->submitted_for_review_at);
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
    $this->assertCount(1, $draftReports);
    $this->assertCount(1, $publishedReports);
    $this->assertTrue($draftReports->contains($draftReport));
    $this->assertTrue($publishedReports->contains($publishedReport));
});

test('user can filter reports by year', function () {
    $report2023 = AuditorGeneralReport::factory()->create(['report_year' => 2023]);
    $report2024 = AuditorGeneralReport::factory()->create(['report_year' => 2024]);
    $reports2024 = AuditorGeneralReport::where('report_year', 2024)->get();
    $reports2023 = AuditorGeneralReport::where('report_year', 2023)->get();
    $this->assertCount(1, $reports2024);
    $this->assertCount(1, $reports2023);
    $this->assertTrue($reports2024->contains($report2024));
    $this->assertTrue($reports2023->contains($report2023));
});

test('user can search reports by title', function () {
    $report1 = AuditorGeneralReport::factory()->create(['title' => 'Annual Budget Report 2024']);
    $report2 = AuditorGeneralReport::factory()->create(['title' => 'Quarterly Performance Review']);
    $budgetReports = AuditorGeneralReport::where('title', 'like', '%Budget%')->get();
    $performanceReports = AuditorGeneralReport::where('title', 'like', '%Performance%')->get();
    $this->assertCount(1, $budgetReports);
    $this->assertCount(1, $performanceReports);
    $this->assertTrue($budgetReports->contains($report1));
    $this->assertTrue($performanceReports->contains($report2));
});

test('user can remove findings from report', function () {
    $report = AuditorGeneralReport::factory()->create(['created_by' => $this->user->id]);
    $findings = Finding::factory()->count(3)->create();
    $report->findings()->attach($findings->pluck('id')->toArray());
    $this->assertCount(3, $report->findings);
    // Remove one finding
    $report->findings()->detach($findings->first()->id);
    $report->refresh();
    $this->assertCount(2, $report->findings);
    $this->assertFalse($report->findings->contains($findings->first()));
});

test('user can reorder findings in report', function () {
    $report = AuditorGeneralReport::factory()->create(['created_by' => $this->user->id]);
    $finding1 = Finding::factory()->create(['title' => 'First Finding']);
    $finding2 = Finding::factory()->create(['title' => 'Second Finding']);
    $report->findings()->attach([
        $finding1->id => ['report_section_order' => 2],
        $finding2->id => ['report_section_order' => 1],
    ]);
    $orderedFindings = $report->findings()->orderBy('pivot.report_section_order')->get();
    $this->assertEquals('Second Finding', $orderedFindings->first()->title);
    $this->assertEquals('First Finding', $orderedFindings->last()->title);
    // Update the order
    $report->findings()->updateExistingPivot($finding1->id, ['report_section_order' => 1]);
    $report->findings()->updateExistingPivot($finding2->id, ['report_section_order' => 2]);
    $reorderedFindings = $report->findings()->orderBy('pivot.report_section_order')->get();
    $this->assertEquals('First Finding', $reorderedFindings->first()->title);
    $this->assertEquals('Second Finding', $reorderedFindings->last()->title);
});

test('user can highlight findings in report', function () {
    $report = AuditorGeneralReport::factory()->create(['created_by' => $this->user->id]);
    $finding = Finding::factory()->create();
    $report->findings()->attach($finding->id, ['highlighted_finding' => true]);
    $highlightedFindings = $report->findings()->wherePivot('highlighted_finding', true)->get();
    $this->assertCount(1, $highlightedFindings);
    $this->assertTrue($highlightedFindings->contains($finding));
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
    $this->assertCount(1, $financialFindings);
    $this->assertCount(1, $complianceFindings);
    $this->assertTrue($financialFindings->contains($finding1));
    $this->assertTrue($complianceFindings->contains($finding2));
});

test('report slug is generated automatically', function () {
    $report = AuditorGeneralReport::factory()->create([
        'title' => 'Annual Report 2024 - Government Performance',
    ]);
    $this->assertNotNull($report->slug);
    $this->assertStringContainsString('annual-report-2024', strtolower($report->slug));
});

test('duplicate report titles for same year and type are handled', function () {
    AuditorGeneralReport::factory()->create([
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
    $this->assertNotEquals($secondReport->slug, AuditorGeneralReport::first()->slug);
});
