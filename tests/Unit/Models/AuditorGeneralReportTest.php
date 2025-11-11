<?php

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use App\Models\AuditorGeneralReport;
use App\Models\Finding;
use App\Models\User;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Create necessary roles for tests
    Role::create(['name' => 'user']);
    Role::create(['name' => 'Super Administrator']);
});

test('it can be created with valid data', function () {
    $user = User::factory()->create();

    $report = AuditorGeneralReport::create([
        'title' => 'Test Annual Report 2024',
        'description' => 'Test description',
        'report_type' => AuditorGeneralReportTypeEnum::ANNUAL,
        'report_year' => 2024,
        'status' => AuditorGeneralReportStatusEnum::DRAFT,
        'period_start' => '2024-01-01',
        'period_end' => '2024-12-31',
        'created_by' => $user->id,
    ]);
    $this->assertInstanceOf(AuditorGeneralReport::class, $report);
    $this->assertEquals('Test Annual Report 2024', $report->title);
    $this->assertEquals(AuditorGeneralReportTypeEnum::ANNUAL, $report->report_type);
    $this->assertEquals(2024, $report->report_year);
    $this->assertEquals(AuditorGeneralReportStatusEnum::DRAFT, $report->status);
});

test('it calculates totals correctly', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding1 = Finding::factory()->create(['amount' => 10000]);
    $finding2 = Finding::factory()->create(['amount' => 5000]);

    $report->findings()->attach([$finding1->id, $finding2->id]);
    $report->calculateTotals();
    $this->assertEquals(15000, $report->total_amount_involved);
    $this->assertEquals(2, $report->total_findings_count);
});

test('it calculates totals with recoveries', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding1 = Finding::factory()->create(['amount' => 10000]);
    $finding2 = Finding::factory()->create(['amount' => 5000]);

    // Create recoveries for findings
    $finding1->recoveries()->create(['amount' => 3000, 'comments' => 'Recovery 1']);
    $finding2->recoveries()->create(['amount' => 1000, 'comments' => 'Recovery 1']);

    $report->findings()->attach([$finding1->id, $finding2->id]);
    $report->calculateTotals();
    $this->assertEquals(15000, $report->total_amount_involved);
    $this->assertEquals(4000, $report->total_recoveries);
    $this->assertEquals(2, $report->total_findings_count);
});

test('it can transition status from draft to under review', function () {
    $report = AuditorGeneralReport::factory()->create([
        'status' => AuditorGeneralReportStatusEnum::DRAFT,
    ]);
    $this->assertTrue($report->canBeEdited());
    $report->markAsUnderReview();

    $this->assertEquals(AuditorGeneralReportStatusEnum::UNDER_REVIEW, $report->status);
    $this->assertFalse($report->canBeEdited());
});

test('it can transition status from under review to approved', function () {
    $approver = User::factory()->create();
    $report = AuditorGeneralReport::factory()->create([
        'status' => AuditorGeneralReportStatusEnum::UNDER_REVIEW,
    ]);
    $this->assertFalse($report->canBeEdited());
    $report->markAsApproved($approver);

    $this->assertEquals(AuditorGeneralReportStatusEnum::APPROVED, $report->status);
    $this->assertEquals($approver->id, $report->approved_by);
    $this->assertNotNull($report->approved_at);
});

test('it can transition status from approved to published', function () {
    $report = AuditorGeneralReport::factory()->create([
        'status' => AuditorGeneralReportStatusEnum::APPROVED,
    ]);
    $this->assertFalse($report->canBeEdited());
    $report->markAsPublished();

    $this->assertEquals(AuditorGeneralReportStatusEnum::PUBLISHED, $report->status);
    $this->assertNotNull($report->publication_date);
});

test('it prevents invalid status transitions', function () {
    $report = AuditorGeneralReport::factory()->create([
        'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
    ]);
    $this->assertFalse($report->canBeEdited());
});

test('it can be returned to draft from under review', function () {
    $report = AuditorGeneralReport::factory()->create([
        'status' => AuditorGeneralReportStatusEnum::UNDER_REVIEW,
    ]);
    $report->backToDraft();

    $this->assertEquals(AuditorGeneralReportStatusEnum::DRAFT, $report->status);
    $this->assertTrue($report->canBeEdited());
});

test('it has correct relationships', function () {
    $user = User::factory()->create();
    $approver = User::factory()->create();
    $report = AuditorGeneralReport::factory()->create([
        'created_by' => $user->id,
        'approved_by' => $approver->id,
    ]);
    $findings = Finding::factory()->count(3)->create();

    $report->findings()->attach($findings->pluck('id')->toArray());
    $this->assertInstanceOf(User::class, $report->creator);
    $this->assertEquals($user->id, $report->creator->id);
    $this->assertInstanceOf(User::class, $report->approver);
    $this->assertEquals($approver->id, $report->approver->id);
    $this->assertCount(3, $report->findings);
});

test('it has correct scopes', function () {
    AuditorGeneralReport::factory()->create(['status' => AuditorGeneralReportStatusEnum::DRAFT]);
    AuditorGeneralReport::factory()->create(['status' => AuditorGeneralReportStatusEnum::PUBLISHED]);
    AuditorGeneralReport::factory()->create([
        'report_year' => 2024,
        'status' => AuditorGeneralReportStatusEnum::UNDER_REVIEW,
    ]);
    AuditorGeneralReport::factory()->create([
        'report_year' => 2024,
        'status' => AuditorGeneralReportStatusEnum::APPROVED,
    ]);
    $this->assertEquals(1, AuditorGeneralReport::draft()->count());
    $this->assertEquals(1, AuditorGeneralReport::published()->count());
    $this->assertEquals(2, AuditorGeneralReport::forYear(2024)->count());
});

test('it prevents duplicate findings', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding = Finding::factory()->create();

    // Attach the same finding twice
    $report->findings()->attach($finding->id);

    // This should not create a duplicate due to unique constraint
    $this->expectException(\Illuminate\Database\QueryException::class);
    $report->findings()->attach($finding->id);
});

test('it orders findings correctly', function () {
    $report = AuditorGeneralReport::factory()->create();
    $finding1 = Finding::factory()->create(['title' => 'First Finding']);
    $finding2 = Finding::factory()->create(['title' => 'Second Finding']);
    $finding3 = Finding::factory()->create(['title' => 'Third Finding']);

    $report->findings()->attach([
        $finding1->id => ['report_section_order' => 3],
        $finding2->id => ['report_section_order' => 1],
        $finding3->id => ['report_section_order' => 2],
    ]);

    // Reload the findings to get the ordered collection
    $orderedFindings = $report->fresh()->findings;

    $this->assertEquals('Second Finding', $orderedFindings->first()->title);
    $this->assertEquals('Third Finding', $orderedFindings->skip(1)->first()->title);
    $this->assertEquals('First Finding', $orderedFindings->last()->title);
});
