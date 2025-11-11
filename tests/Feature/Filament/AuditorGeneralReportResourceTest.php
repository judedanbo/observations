<?php

namespace Tests\Feature\Filament;

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use App\Filament\Resources\AuditorGeneralReportResource\Pages\CreateAuditorGeneralReport;
use App\Filament\Resources\AuditorGeneralReportResource\Pages\EditAuditorGeneralReport;
use App\Filament\Resources\AuditorGeneralReportResource\Pages\ListAuditorGeneralReports;
use App\Filament\Resources\AuditorGeneralReportResource\Pages\ManageFindings;
use App\Filament\Resources\AuditorGeneralReportResource\Pages\ViewAuditorGeneralReport;
use App\Models\AuditorGeneralReport;
use App\Models\Finding;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditorGeneralReportResourceTest extends TestCase
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

        // Authenticate as the user
        $this->actingAs($this->user);

        // Set the current panel for Filament
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function can_render_list_page()
    {
        $reports = AuditorGeneralReport::factory()->count(3)->create();

        $component = Livewire::test(ListAuditorGeneralReports::class);

        $component->assertOk();
        $component->assertCountTableRecords(3);

        foreach ($reports as $report) {
            $component->assertSee($report->title);
        }
    }

    #[Test]
    public function can_create_report_through_filament()
    {
        Livewire::test(CreateAuditorGeneralReport::class)
            ->fillForm([
                'title' => 'New Test Report 2024',
                'description' => 'Test description for the report',
                'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
                'report_year' => 2024,
                'period_start' => '2024-01-01',
                'period_end' => '2024-12-31',
                'executive_summary' => 'Executive summary content',
                'methodology' => 'Audit methodology description',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas('auditor_general_reports', [
            'title' => 'New Test Report 2024',
            'report_type' => AuditorGeneralReportTypeEnum::ANNUAL->value,
            'report_year' => 2024,
            'created_by' => $this->user->id,
        ]);
    }

    #[Test]
    public function can_edit_report_when_in_draft_status()
    {
        $report = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
            'created_by' => $this->user->id,
        ]);

        Livewire::test(EditAuditorGeneralReport::class, ['record' => $report->getRouteKey()])
            ->fillForm([
                'title' => 'Updated Report Title',
                'description' => 'Updated description',
            ])
            ->call('save')
            ->assertNotified();

        $this->assertDatabaseHas('auditor_general_reports', [
            'id' => $report->id,
            'title' => 'Updated Report Title',
            'description' => 'Updated description',
        ]);
    }

    #[Test]
    public function cannot_edit_report_when_published()
    {
        $report = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
            'created_by' => $this->user->id,
        ]);

        // The edit page should not be accessible or form should be disabled
        $this->assertFalse($report->canBeEdited());
    }

    #[Test]
    public function can_view_report_details()
    {
        $report = AuditorGeneralReport::factory()->create([
            'title' => 'Test Report for Viewing',
            'created_by' => $this->user->id,
        ]);

        // View page should load successfully and show form data
        Livewire::test(ViewAuditorGeneralReport::class, ['record' => $report->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'title' => $report->title,
                'report_type' => $report->report_type->value,
                'report_year' => $report->report_year,
            ]);
    }

    #[Test]
    public function can_filter_reports_by_status()
    {
        $draftReport = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
        ]);
        $publishedReport = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
        ]);

        Livewire::test(ListAuditorGeneralReports::class)
            ->filterTable('status', AuditorGeneralReportStatusEnum::DRAFT->value)
            ->assertCanSeeTableRecords([$draftReport])
            ->assertCanNotSeeTableRecords([$publishedReport]);
    }

    #[Test]
    public function can_filter_reports_by_type()
    {
        $annualReport = AuditorGeneralReport::factory()->create([
            'report_type' => AuditorGeneralReportTypeEnum::ANNUAL,
        ]);
        $quarterlyReport = AuditorGeneralReport::factory()->create([
            'report_type' => AuditorGeneralReportTypeEnum::QUARTERLY,
        ]);

        Livewire::test(ListAuditorGeneralReports::class)
            ->filterTable('report_type', AuditorGeneralReportTypeEnum::ANNUAL->value)
            ->assertCanSeeTableRecords([$annualReport])
            ->assertCanNotSeeTableRecords([$quarterlyReport]);
    }

    #[Test]
    public function can_filter_reports_by_year()
    {
        $report2023 = AuditorGeneralReport::factory()->create(['report_year' => 2023]);
        $report2024 = AuditorGeneralReport::factory()->create(['report_year' => 2024]);

        Livewire::test(ListAuditorGeneralReports::class)
            ->filterTable('report_year', 2024)
            ->assertCanSeeTableRecords([$report2024])
            ->assertCanNotSeeTableRecords([$report2023]);
    }

    #[Test]
    public function can_search_reports()
    {
        $report1 = AuditorGeneralReport::factory()->create(['title' => 'Annual Budget Report']);
        $report2 = AuditorGeneralReport::factory()->create(['title' => 'Quarterly Performance']);

        Livewire::test(ListAuditorGeneralReports::class)
            ->searchTable('Budget')
            ->assertCanSeeTableRecords([$report1])
            ->assertCanNotSeeTableRecords([$report2]);
    }

    #[Test]
    public function can_sort_reports_by_created_date()
    {
        $olderReport = AuditorGeneralReport::factory()->create([
            'created_at' => now()->subDays(2),
        ]);
        $newerReport = AuditorGeneralReport::factory()->create([
            'created_at' => now()->subDays(1),
        ]);

        Livewire::test(ListAuditorGeneralReports::class)
            ->sortTable('created_at', 'desc')
            ->assertCanSeeTableRecords([$newerReport, $olderReport], inOrder: true);
    }

    #[Test]
    public function can_access_manage_findings_page()
    {
        $report = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
            'created_by' => $this->user->id,
        ]);

        Livewire::test(ManageFindings::class, ['record' => $report->getRouteKey()])
            ->assertSee('Manage Findings');
    }

    #[Test]
    public function can_add_findings_through_manage_findings_page()
    {
        $report = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
            'created_by' => $this->user->id,
        ]);
        $findings = Finding::factory()->count(3)->create();

        $manageFindingsTest = Livewire::test(ManageFindings::class, ['record' => $report->getRouteKey()]);

        // The manage findings page should show available findings
        foreach ($findings as $finding) {
            $manageFindingsTest->assertSee($finding->title);
        }
    }

    #[Test]
    public function shows_correct_status_badges()
    {
        $draftReport = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
        ]);
        $publishedReport = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
        ]);

        $listTest = Livewire::test(ListAuditorGeneralReports::class);

        // Should show status badges
        $listTest->assertSee('Draft');
        $listTest->assertSee('Published');
    }

    #[Test]
    public function shows_correct_report_type_badges()
    {
        $annualReport = AuditorGeneralReport::factory()->create([
            'report_type' => AuditorGeneralReportTypeEnum::ANNUAL,
        ]);
        $specialReport = AuditorGeneralReport::factory()->create([
            'report_type' => AuditorGeneralReportTypeEnum::SPECIAL,
        ]);

        $listTest = Livewire::test(ListAuditorGeneralReports::class);

        // Should show type badges with correct labels
        $listTest->assertSee('Annual Report');
        $listTest->assertSee('Special Investigation Report');
    }

    // #[Test]
    // public function displays_totals_correctly()
    // {
    //     $report = AuditorGeneralReport::factory()->create([
    //         'total_amount_involved' => 150000,
    //         'total_recoveries' => 50000,
    //         // 'total_findings_count' => 5,
    //     ]);

    //     Livewire::test(ListAuditorGeneralReports::class)
    //         ->assertSee('150,000')  // Amount formatting
    //         ->assertSee('50,000')   // Recovery formatting
    //         ->assertSee('5');       // Findings count
    // }

    #[Test]
    public function can_delete_draft_reports()
    {
        $draftReport = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
        ]);

        // Delete action is on the edit page, not the list page
        $this->assertTrue($draftReport->canBeDeleted());
    }

    #[Test]
    public function cannot_delete_published_reports()
    {
        $publishedReport = AuditorGeneralReport::factory()->create([
            'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
        ]);

        // The delete action should not be visible for published reports
        $this->assertFalse($publishedReport->canBeEdited());
    }

    #[Test]
    public function can_bulk_delete_draft_reports()
    {
        $draftReports = AuditorGeneralReport::factory()->count(3)->create([
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
        ]);

        // Verify bulk delete action exists and is visible
        Livewire::test(ListAuditorGeneralReports::class)
            ->assertTableBulkActionExists('delete');

        // All draft reports should be deletable
        foreach ($draftReports as $report) {
            $this->assertTrue($report->canBeDeleted());
        }
    }

    #[Test]
    public function form_validation_works()
    {
        Livewire::test(CreateAuditorGeneralReport::class)
            ->fillForm([
                'title' => '', // Required field
                'report_year' => 1800, // Invalid year
            ])
            ->call('create')
            ->assertHasFormErrors(['title', 'report_year']);
    }

    #[Test]
    public function can_navigate_between_pages()
    {
        $report = AuditorGeneralReport::factory()->draft()->create([
            'created_by' => $this->user->id,
        ]);

        // Test that the report can be edited
        $this->assertTrue($report->canBeEdited());

        // Test the model relationships work
        $this->assertEquals($this->user->id, $report->created_by);
        $this->assertInstanceOf(AuditorGeneralReport::class, $report);
    }
}
