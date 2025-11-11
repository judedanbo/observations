<?php

namespace App\Models;

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use App\Http\Traits\LogAllTraits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AuditorGeneralReport extends Model
{
    use HasFactory, LogAllTraits, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'report_type',
        'report_year',
        'publication_date',
        'period_start',
        'period_end',
        'status',
        'executive_summary',
        'methodology',
        'conclusion',
        'recommendations_summary',
        'metadata',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'report_type' => AuditorGeneralReportTypeEnum::class,
        'status' => AuditorGeneralReportStatusEnum::class,
        'report_year' => 'integer',
        'publication_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'approved_at' => 'datetime',
        'metadata' => 'json',
    ];

    protected static function booted(): void
    {
        static::creating(function (AuditorGeneralReport $report) {
            // Only set created_by if not already set and user is authenticated
            if (auth()->check() && empty($report->created_by)) {
                $report->created_by = auth()->id();
            }

            // Generate slug if not provided
            if (empty($report->slug) && ! empty($report->title)) {
                $report->slug = $report->generateUniqueSlug($report->title);
            }
        });

        static::updating(function (AuditorGeneralReport $report) {
            // Regenerate slug if title changed and slug wasn't manually set
            if ($report->isDirty('title') && ! $report->isDirty('slug')) {
                $originalSlug = Str::slug($report->getOriginal('title'));
                $currentSlug = $report->slug;

                // Only regenerate if the slug matches the old title's slug pattern
                if ($currentSlug === $originalSlug || Str::startsWith($currentSlug, $originalSlug.'-')) {
                    $report->slug = $report->generateUniqueSlug($report->title, $report->id);
                }
            }
        });

        // static::saving(function (AuditorGeneralReport $report) {
        //     // Auto-calculate totals when findings are attached
        //     if ($report->exists && $report->isDirty(['total_amount_involved', 'total_recoveries', 'total_findings_count']) === false) {
        //         $report->calculateTotals();
        //     }
        // });
    }

    // Relationships
    public function findings(): BelongsToMany
    {
        return $this->belongsToMany(Finding::class, 'auditor_general_report_findings')
            ->withPivot([
                'report_section_order',
                'section_category',
                'report_context',
                'highlighted_finding',
            ])
            ->withTimestamps()
            ->orderByPivot('report_section_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Helper methods
    public function calculateTotals(): void
    {
        // Reload the findings relationship to ensure fresh data
        // The actual totals are computed via accessor methods
        $this->load('findings');
    }

    public function markAsUnderReview(): void
    {
        if ($this->status->canTransitionTo(AuditorGeneralReportStatusEnum::UNDER_REVIEW)) {
            $this->update(['status' => AuditorGeneralReportStatusEnum::UNDER_REVIEW]);
        }
    }

    public function markAsApproved(User $approver): void
    {
        if ($this->status->canTransitionTo(AuditorGeneralReportStatusEnum::APPROVED)) {
            $this->update([
                'status' => AuditorGeneralReportStatusEnum::APPROVED,
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);
        }
    }

    public function markAsPublished(): void
    {
        if ($this->status->canTransitionTo(AuditorGeneralReportStatusEnum::PUBLISHED)) {
            $this->update([
                'status' => AuditorGeneralReportStatusEnum::PUBLISHED,
                'publication_date' => $this->publication_date ?? now()->toDateString(),
            ]);
        }
    }

    public function backToDraft(): void
    {
        if ($this->status->canTransitionTo(AuditorGeneralReportStatusEnum::DRAFT)) {
            $this->update([
                'status' => AuditorGeneralReportStatusEnum::DRAFT,
                'approved_by' => null,
                'approved_at' => null,
            ]);
        }
    }

    public function canBeEdited(): bool
    {
        return $this->status === AuditorGeneralReportStatusEnum::DRAFT;
    }

    public function canBeDeleted(): bool
    {
        return in_array($this->status, [
            AuditorGeneralReportStatusEnum::DRAFT,
            AuditorGeneralReportStatusEnum::UNDER_REVIEW,
        ]);
    }

    public function getHighlightedFindings(): BelongsToMany
    {
        return $this->findings()->wherePivot('highlighted_finding', true);
    }

    public function getFindingsByCategory(string $category): BelongsToMany
    {
        return $this->findings()->wherePivot('section_category', $category);
    }

    public function attachFindings(array $findingIds, array $options = []): void
    {
        $attachData = [];
        foreach ($findingIds as $index => $findingId) {
            $attachData[$findingId] = array_merge([
                'report_section_order' => $options['report_section_order'][$index] ?? $index,
                'section_category' => $options['section_category'][$index] ?? 'general',
                'report_context' => $options['report_context'][$index] ?? null,
                'highlighted_finding' => $options['highlighted_finding'][$index] ?? false,
            ], $options);
        }

        $this->findings()->attach($attachData);
        // $this->calculateTotals();
    }

    // Scopes
    public function scopeByStatus(Builder $query, AuditorGeneralReportStatusEnum $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByType(Builder $query, AuditorGeneralReportTypeEnum $type): Builder
    {
        return $query->where('report_type', $type);
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('report_year', $year);
    }

    public function scopeByYear(Builder $query): Builder
    {
        return $query->orderBy('report_year', 'desc');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', AuditorGeneralReportStatusEnum::PUBLISHED);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', AuditorGeneralReportStatusEnum::DRAFT);
    }

    public function getTotalFindingsCountAttribute()
    {
        return $this->findings()->count();
    }

    public function getTotalMonitoryFindingsCountAttribute()
    {
        return $this->findings()->whereNotNull('amount')->count();
    }

    public function getTotalNonMonitoryFindingsCountAttribute()
    {
        return $this->findings()->whereNull('amount')->count();
    }

    public function getTotalAmountInvolvedAttribute()
    {
        return $this->findings->sum('amount');
    }

    public function getTotalRecoveriesAttribute()
    {
        return $this->findings->sum('total_recoveries');
    }

    /**
     * Generate a unique slug from the given title
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        // Check for existing slugs and append number if necessary
        while ($this->slugExists($slug, $ignoreId)) {
            $counter++;
            $slug = $originalSlug.'-'.$counter;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists
     */
    protected function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = static::where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
