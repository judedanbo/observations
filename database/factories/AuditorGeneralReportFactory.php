<?php

namespace Database\Factories;

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use App\Models\AuditorGeneralReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditorGeneralReport>
 */
class AuditorGeneralReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = AuditorGeneralReport::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4, true);
        $reportTypes = AuditorGeneralReportTypeEnum::cases();
        $statuses = AuditorGeneralReportStatusEnum::cases();

        return [
            'title' => rtrim($title, '.'),
            'description' => $this->faker->optional(0.7)->paragraph(),
            'report_type' => $this->faker->randomElement($reportTypes),
            'report_year' => $this->faker->numberBetween(2020, 2025),
            'period_start' => $this->faker->date(),
            'period_end' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'status' => $this->faker->randomElement($statuses),
            'executive_summary' => $this->faker->optional(0.6)->paragraphs(2, true),
            'methodology' => $this->faker->optional(0.5)->paragraphs(3, true),
            'conclusion' => $this->faker->optional(0.4)->paragraph(),
            'recommendations_summary' => $this->faker->optional(0.6)->paragraphs(3, true),
            'created_by' => User::factory(),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    /**
     * Indicate that the report is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuditorGeneralReportStatusEnum::DRAFT,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Indicate that the report is under review.
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AuditorGeneralReportStatusEnum::UNDER_REVIEW,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Indicate that the report is approved.
     */
    public function approved(): static
    {
        $approvedAt = $this->faker->dateTimeBetween('-2 weeks', 'now');

        return $this->state(fn (array $attributes) => [
            'status' => AuditorGeneralReportStatusEnum::APPROVED,
            'approved_by' => User::factory(),
            'approved_at' => $approvedAt,
        ]);
    }

    /**
     * Indicate that the report is published.
     */
    public function published(): static
    {
        $approvedAt = $this->faker->dateTimeBetween('-1 month', '-2 weeks');
        $publishedAt = $this->faker->dateTimeBetween($approvedAt, 'now');

        return $this->state(fn (array $attributes) => [
            'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
            'approved_by' => User::factory(),
            'approved_at' => $approvedAt,
            'publication_date' => $publishedAt->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the report is an annual report.
     */
    public function annual(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => AuditorGeneralReportTypeEnum::ANNUAL,
            'title' => 'Annual Report '.$this->faker->year(),
        ]);
    }

    /**
     * Indicate that the report is a quarterly report.
     */
    public function quarterly(): static
    {
        $quarter = $this->faker->randomElement(['Q1', 'Q2', 'Q3', 'Q4']);
        $year = $this->faker->year();

        return $this->state(fn (array $attributes) => [
            'report_type' => AuditorGeneralReportTypeEnum::QUARTERLY,
            'title' => "{$quarter} {$year} Quarterly Report",
        ]);
    }

    /**
     * Indicate that the report is a special report.
     */
    public function special(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => AuditorGeneralReportTypeEnum::SPECIAL,
            'title' => 'Special Investigation Report on '.$this->faker->words(3, true),
        ]);
    }

    /**
     * Indicate that the report is a performance audit report.
     */
    public function performance(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => AuditorGeneralReportTypeEnum::PERFORMANCE,
            'title' => 'Performance Audit Report - '.$this->faker->words(2, true),
        ]);
    }

    /**
     * Indicate that the report is a thematic report.
     */
    public function thematic(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => AuditorGeneralReportTypeEnum::THEMATIC,
            'title' => 'Thematic Report: '.$this->faker->words(3, true),
        ]);
    }

    /**
     * Indicate that the report has findings and totals.
     */
    public function withTotals(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_findings_count' => $this->faker->numberBetween(1, 20),
            'total_amount_involved' => $this->faker->numberBetween(10000, 1000000),
            'total_recoveries' => $this->faker->numberBetween(1000, 500000),
        ]);
    }

    /**
     * Indicate that the report has all text fields populated.
     */
    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $this->faker->paragraphs(2, true),
            'executive_summary' => $this->faker->paragraphs(3, true),
            'methodology' => $this->faker->paragraphs(4, true),
            'recommendations_summary' => $this->faker->paragraphs(4, true),
            'conclusion' => $this->faker->paragraphs(2, true),
        ]);
    }

    /**
     * Create a report for a specific year.
     */
    public function forYear(int $year): static
    {
        return $this->state(fn (array $attributes) => [
            'report_year' => $year,
        ]);
    }

    /**
     * Create a report by a specific user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }
}
