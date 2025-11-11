<?php

namespace Tests\Feature\Filament;

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use App\Models\AuditorGeneralReport;
use App\Models\Finding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditorGeneralReportResourceSimpleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('Super Administrator');
        $this->actingAs($this->user);
    }

    #[Test]
    public function can_create_auditor_general_report()
    {
        $reportData = [
            'title' => 'Test AG Report 2024',
            'description' => 'Test description',
            'report_type' => AuditorGeneralReportTypeEnum::ANNUAL,
            'report_year' => 2024,
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
            'executive_summary' => 'Executive summary content',
            'methodology' => 'Methodology content',
            'conclusion' => 'Conclusion content',
            'recommendations_summary' => 'Recommendations content',
            'created_by' => $this->user->id,
        ];

        $report = AuditorGeneralReport::create($reportData);

        $this->assertDatabaseHas('auditor_general_reports', [
            'title' => 'Test AG Report 2024',
            'report_year' => 2024,
            'created_by' => $this->user->id,
        ]);

        $this->assertInstanceOf(AuditorGeneralReport::class, $report);
        $this->assertEquals($this->user->id, $report->created_by);
    }

    #[Test]
    public function can_update_report_when_in_draft_status()
    {
        $report = AuditorGeneralReport::factory()->draft()->create([
            'created_by' => $this->user->id,
        ]);

        $this->assertTrue($report->canBeEdited());

        $report->update([
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ]);

        $this->assertDatabaseHas('auditor_general_reports', [
            'id' => $report->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ]);
    }

    #[Test]
    public function cannot_edit_published_report()
    {
        $report = AuditorGeneralReport::factory()->published()->create([
            'created_by' => $this->user->id,
        ]);

        $this->assertFalse($report->canBeEdited());
        $this->assertEquals(AuditorGeneralReportStatusEnum::PUBLISHED, $report->status);
    }

    #[Test]
    public function can_transition_report_status()
    {
        $report = AuditorGeneralReport::factory()->draft()->create([
            'created_by' => $this->user->id,
        ]);

        // Test transition to under review
        $report->markAsUnderReview();
        $report->refresh();
        $this->assertEquals(AuditorGeneralReportStatusEnum::UNDER_REVIEW, $report->status);

        // Test transition to approved
        $report->markAsApproved($this->user);
        $report->refresh();
        $this->assertEquals(AuditorGeneralReportStatusEnum::APPROVED, $report->status);
        $this->assertEquals($this->user->id, $report->approved_by);
        $this->assertNotNull($report->approved_at);

        // Test transition to published
        $report->markAsPublished();
        $report->refresh();
        $this->assertEquals(AuditorGeneralReportStatusEnum::PUBLISHED, $report->status);
        $this->assertNotNull($report->publication_date);
    }

    #[Test]
    public function can_attach_findings_to_report()
    {
        // Create necessary related data
        $report = AuditorGeneralReport::factory()->draft()->create();
        $findings = Finding::factory()->count(3)->create();

        // Attach findings to the report using simpler attach method
        $attachData = [];
        foreach ($findings as $index => $finding) {
            $attachData[$finding->id] = [
                'report_section_order' => $index,
                'section_category' => 'financial',
                'report_context' => 'Test context',
                'highlighted_finding' => $index === 0, // First one is highlighted
            ];
        }

        $report->findings()->attach($attachData);

        // Assert findings are attached
        $this->assertEquals(3, $report->findings()->count());
        $this->assertEquals(1, $report->getHighlightedFindings()->count());
        $this->assertEquals(3, $report->getFindingsByCategory('financial')->count());
    }

    #[Test]
    public function can_calculate_report_totals()
    {
        $report = AuditorGeneralReport::factory()->draft()->create();

        // Create findings with specific amounts
        $finding1 = Finding::factory()->create([
            'amount' => 10000,
        ]);
        $finding2 = Finding::factory()->create([
            'amount' => 20000,
        ]);
        $finding3 = Finding::factory()->create([
            'amount' => null, // Non-monetary finding
        ]);

        $report->findings()->attach([
            $finding1->id,
            $finding2->id,
            $finding3->id,
        ]);

        // Test calculated attributes
        $this->assertEquals(3, $report->total_findings_count);
        $this->assertEquals(2, $report->total_monitory_findings_count);
        $this->assertEquals(1, $report->total_non_monitory_findings_count);
    }

    #[Test]
    public function can_filter_reports_by_status()
    {
        $draftReport = AuditorGeneralReport::factory()->draft()->create();
        $publishedReport = AuditorGeneralReport::factory()->published()->create();
        $underReviewReport = AuditorGeneralReport::factory()->underReview()->create();

        $draftReports = AuditorGeneralReport::byStatus(AuditorGeneralReportStatusEnum::DRAFT)->get();
        $publishedReports = AuditorGeneralReport::byStatus(AuditorGeneralReportStatusEnum::PUBLISHED)->get();

        $this->assertTrue($draftReports->contains($draftReport));
        $this->assertFalse($draftReports->contains($publishedReport));
        $this->assertTrue($publishedReports->contains($publishedReport));
        $this->assertFalse($publishedReports->contains($draftReport));
    }

    #[Test]
    public function can_filter_reports_by_type()
    {
        $annualReport = AuditorGeneralReport::factory()->annual()->create();
        $quarterlyReport = AuditorGeneralReport::factory()->quarterly()->create();
        $specialReport = AuditorGeneralReport::factory()->special()->create();

        $annualReports = AuditorGeneralReport::byType(AuditorGeneralReportTypeEnum::ANNUAL)->get();
        $quarterlyReports = AuditorGeneralReport::byType(AuditorGeneralReportTypeEnum::QUARTERLY)->get();

        $this->assertTrue($annualReports->contains($annualReport));
        $this->assertFalse($annualReports->contains($quarterlyReport));
        $this->assertTrue($quarterlyReports->contains($quarterlyReport));
        $this->assertFalse($quarterlyReports->contains($specialReport));
    }

    #[Test]
    public function can_filter_reports_by_year()
    {
        $report2023 = AuditorGeneralReport::factory()->forYear(2023)->create();
        $report2024 = AuditorGeneralReport::factory()->forYear(2024)->create();
        $report2025 = AuditorGeneralReport::factory()->forYear(2025)->create();

        $reports2024 = AuditorGeneralReport::forYear(2024)->get();

        $this->assertFalse($reports2024->contains($report2023));
        $this->assertTrue($reports2024->contains($report2024));
        $this->assertFalse($reports2024->contains($report2025));
    }

    #[Test]
    public function can_delete_draft_report()
    {
        $draftReport = AuditorGeneralReport::factory()->draft()->create();

        $this->assertTrue($draftReport->canBeDeleted());

        $draftReport->delete();

        $this->assertSoftDeleted('auditor_general_reports', [
            'id' => $draftReport->id,
        ]);
    }

    #[Test]
    public function cannot_delete_published_report()
    {
        $publishedReport = AuditorGeneralReport::factory()->published()->create();

        $this->assertFalse($publishedReport->canBeDeleted());
    }

    #[Test]
    public function factory_states_work_correctly()
    {
        // Test draft state
        $draftReport = AuditorGeneralReport::factory()->draft()->create();
        $this->assertEquals(AuditorGeneralReportStatusEnum::DRAFT, $draftReport->status);
        $this->assertNull($draftReport->approved_by);

        // Test under review state
        $underReviewReport = AuditorGeneralReport::factory()->underReview()->create();
        $this->assertEquals(AuditorGeneralReportStatusEnum::UNDER_REVIEW, $underReviewReport->status);

        // Test approved state
        $approvedReport = AuditorGeneralReport::factory()->approved()->create();
        $this->assertEquals(AuditorGeneralReportStatusEnum::APPROVED, $approvedReport->status);
        $this->assertNotNull($approvedReport->approved_by);
        $this->assertNotNull($approvedReport->approved_at);

        // Test published state
        $publishedReport = AuditorGeneralReport::factory()->published()->create();
        $this->assertEquals(AuditorGeneralReportStatusEnum::PUBLISHED, $publishedReport->status);
        $this->assertNotNull($publishedReport->publication_date);

        // Test report type states
        $annualReport = AuditorGeneralReport::factory()->annual()->create();
        $this->assertEquals(AuditorGeneralReportTypeEnum::ANNUAL, $annualReport->report_type);

        $quarterlyReport = AuditorGeneralReport::factory()->quarterly()->create();
        $this->assertEquals(AuditorGeneralReportTypeEnum::QUARTERLY, $quarterlyReport->report_type);

        $specialReport = AuditorGeneralReport::factory()->special()->create();
        $this->assertEquals(AuditorGeneralReportTypeEnum::SPECIAL, $specialReport->report_type);

        $performanceReport = AuditorGeneralReport::factory()->performance()->create();
        $this->assertEquals(AuditorGeneralReportTypeEnum::PERFORMANCE, $performanceReport->report_type);

        $thematicReport = AuditorGeneralReport::factory()->thematic()->create();
        $this->assertEquals(AuditorGeneralReportTypeEnum::THEMATIC, $thematicReport->report_type);
    }

    #[Test]
    public function relationships_work_correctly()
    {
        $creator = User::factory()->create();
        $approver = User::factory()->create();

        $report = AuditorGeneralReport::factory()->create([
            'created_by' => $creator->id,
            'approved_by' => $approver->id,
        ]);

        // Refresh the model to ensure relationships are loaded
        $report->refresh();

        // Test creator relationship
        $this->assertInstanceOf(User::class, $report->creator);
        $this->assertEquals($creator->id, $report->creator->id);

        // Test approver relationship
        $this->assertInstanceOf(User::class, $report->approver);
        $this->assertEquals($approver->id, $report->approver->id);
    }
}
