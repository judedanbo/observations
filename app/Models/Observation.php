<?php

namespace App\Models;

use App\Enums\ObservationStatusEnum;
use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observation extends Model
{
    use HasFactory, LogAllTraits, SoftDeletes;

    protected $fillable = [
        'audit_id', // 'audit_id' is the foreign key
        'title',
        'criteria',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'audit_id' => 'integer',
        'status' => ObservationStatusEnum::class,
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function actions(): HasMany
    {
        return $this->HasMany(Action::class);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function causes(): HasManyThrough
    {
        return $this->hasManyThrough(Cause::class, Finding::class);
    }

    public function effects(): HasManyThrough
    {
        return $this->hasManyThrough(Effect::class, Finding::class);
    }

    public function recommendations(): HasManyThrough
    {
        return $this->hasManyThrough(Recommendation::class, Finding::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function scopeDraft(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::DRAFT);
    }

    public function scopeInReview(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::IN_REVIEW);
    }

    public function scopeIssued(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::ISSUED);
    }

    public function scopeTeamResolved(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::TEAM_RESOLVED);
    }

    public function scopeReported(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::REPORTED);
    }

    public function scopeDaResolved(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::DA_RESOLVED);
    }

    public function scopeRaResolved(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::RA_RESOLVED);
    }

    public function scopeAgResolved(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::AG_RESOLVED);
    }

    public function scopePacResolved(Builder $query)
    {
        return $query->where('status', ObservationStatusEnum::PAC_RESOLVED);
    }

    public function review()
    {
        //  TODO: Implement send for review method.
        $this->status = ObservationStatusEnum::IN_REVIEW;
        $this->save();
    }

    public function issue()
    {
        //  TODO: Implement issue observation method.
        $this->status = ObservationStatusEnum::ISSUED;
        $this->save();
    }

    public function receive()
    {
        //  TODO: Implement issue observation method.
        $this->status = ObservationStatusEnum::RECEIVED;
        $this->save();
    }

    public function respond()
    {
        //  TODO: Implement issue observation method.
        $this->status = ObservationStatusEnum::RESPONDED;
        $this->save();
    }

    public function resolve(ObservationStatusEnum $by)
    {
        //  TODO: Implement issue observation method.
        $this->status = $by;
        $this->save();
    }

    public static function getForm($auditId = null): array
    {
        return [
            Select::make('audit_id')
                ->hidden(function () use ($auditId) {
                    return $auditId !== null;
                })
                ->relationship('audit', 'title')
                ->required(),
            TextInput::make('title')
                ->required()
                ->live()
                ->maxLength(250),
            RichEditor::make('criteria')
                ->columnSpanFull(),
            // Actions::make([
            //     Action::make('Save')
            //         ->label('Generate data')
            //         ->icon('heroicon-m-arrow-path')
            //         ->outlined()
            //         ->color('gray')
            //         ->visible(function (string $operation) {
            //             if ($operation !== 'create') {
            //                 return false;
            //             }
            //             if (!app()->environment('local')) {
            //                 return false;
            //             }
            //             return true;
            //         })
            //         ->action(function ($livewire) {
            //             $data = Observation::factory()->make()->toArray();
            //             $livewire->form->fill($data);
            //         }),
            // ])
            //     ->label('Actions')
            //     ->columnSpanFull(),
        ];
    }
}
