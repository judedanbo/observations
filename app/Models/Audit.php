<?php

namespace App\Models;

use App\Enums\AuditStatusEnum;
use App\Enums\AuditTypeEnum;
use App\Http\Traits\LogAllTraits;
use App\Imports\ObservationImport;
use Carbon\Carbon;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Livewire\Component;

class Audit extends Model
{
    use HasFactory, LogAllTraits, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'year',
        'type',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'status' => AuditStatusEnum::class,
        'type' => AuditTypeEnum::class,
    ];

    public function importObservations(string $file)
    {
        (new ObservationImport)->import($file);
    }

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function auditTeams(): HasMany
    {
        return $this->hasMany(AuditTeam::class);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', AuditStatusEnum::IN_PROGRESS);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class);
    }

    public function districts(): BelongsToMany
    {
        return $this->belongsToMany(District::class);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', AuditStatusEnum::PLANNED);
    }

    public function scopeIssued(Builder $query): Builder
    {
        return $query->where('status', AuditStatusEnum::ISSUED);
    }

    public function scopeTransmitted(Builder $query): Builder
    {
        return $query->where('status', AuditStatusEnum::TRANSMITTED);
    }

    public function scopeTerminated(Builder $query): Builder
    {
        return $query->where('status', AuditStatusEnum::TERMINATED);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', AuditStatusEnum::ARCHIVED);
    }

    public function scopeSearchPlannedStart(Builder $query, array $data): Builder
    {
        return $query->when(
            $data['planned_start_date_from'],
            fn(Builder $query, $value) => $query->whereDate('planned_start_date', '>=', $value)
        )
            ->when(
                $data['planned_start_date_to'],
                fn(Builder $query, $value) => $query->whereDate('planned_start_date', '<=', $value)
            );
    }

    public function scopeSearchActualStart(Builder $query, array $data): Builder
    {
        return $query->when(
            $data['actual_start_date_from'],
            fn(Builder $query, $value) => $query->whereDate('actual_start_date', '>=', $value)
        )
            ->when(
                $data['actual_start_date_to'],
                fn(Builder $query, $value) => $query->whereDate('actual_start_date', '<=', $value)
            );
    }

    public function start()
    {
        $this->status = AuditStatusEnum::IN_PROGRESS;
        $this->actual_start_date = now();
        $this->save();
    }

    public function issue()
    {
        $this->status = AuditStatusEnum::ISSUED;
        $this->actual_end_date = now();
        $this->save();
    }

    public function transmit()
    {
        $this->status = AuditStatusEnum::TRANSMITTED;
        $this->save();
    }

    public function terminate()
    {
        $this->status = AuditStatusEnum::TERMINATED;
        $this->save();
    }

    public function archive()
    {
        $this->status = AuditStatusEnum::ARCHIVED;
        $this->save();
    }

    public function createTeam(array|int|null $team = null): Team
    {

        if ($this->teams()->count() > 0) {
            if (gettype($team) === 'integer') {
                return Team::where('id', $team)->first();
            }
        }
        if (gettype($team) === 'array') {
            return $this->teams()->create(['name' => $team['name']]);
        }

        return $this->teams()->create(['name' => $this->title . ' Team']);
    }

    public function addTeam($team)
    {
        $this->teams()->attach($team);
    }

    public function addTeamMember(array|int|null $team, array $member)
    {
        $team = $this->createTeam($team);
        if ($team === null) {
            return;
        }
        // $newTeam  = $this->teams()->attach($team['team_id']);
        $team->staff()?->sync($member);
    }

    // public function statuses(): BelongsToMany
    // {
    //     return $this->belongsToMany(Status::class)
    //         ->withTimestamps();
    // }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }

    public function findings(): HasManyThrough
    {
        return $this->hasManyThrough(Finding::class, Observation::class);
    }

    public function getScheduleAttribute(): string
    {
        if ($this->planned_start_date === null) {
            if ($this->planned_end_date !== null) {
                return 'To end ' . $this->planned_end_date->diffForHumans(['options' => Carbon::ONE_DAY_WORDS]);
            }

            return 'Not scheduled';
        }
        if ($this->planned_start_date->gt(now())) {
            return 'To start ' . $this->planned_start_date->diffForHumans(['options' => Carbon::ONE_DAY_WORDS]);
        }
        if ($this->planned_start_date->lt(now())) {
            if ($this->status === AuditStatusEnum::PLANNED) {
                return 'planned start passed ' . $this->planned_start_date->diffForHumans([
                    'options' => Carbon::ONE_DAY_WORDS,
                ]);
            }
            if ($this->planned_end_date?->lt(now())) {
                if ($this->status === AuditStatusEnum::IN_PROGRESS) {
                    return 'passed due' . $this->planned_end_date->diffForHumans([
                        'options' => Carbon::ONE_DAY_WORDS,
                    ]);
                }
            }
            // return 'On time';
        }
        if ($this->planned_start_date->eq($this->planned_end_date)) {
            return $this->planned_start_date->format('d M Y');
        }

        return 'On time';
        // return $this->planned_start_date->format('d M Y') . ' - ' . $this->planned_end_date->format('d M Y');
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(250)
                ->columnSpanFull(),
            Select::make('type')
                ->options(AuditTypeEnum::class)
                ->native(false)
                ->required(),
            TextInput::make('year')
                ->label('Audit year')
                ->required()
                ->numeric()
                ->maxValue(date('Y'))
                ->minValue(date('Y', strtotime('-10 years')))
                ->default(date('Y')),
            Textarea::make('description')
                ->columnSpanFull(),
            DatePicker::make('planned_start_date')
                ->native(false),
            DatePicker::make('planned_end_date')
                ->native(false),
            DatePicker::make('actual_start_date')
                ->native(false),
            DatePicker::make('actual_end_date')
                ->native(false),
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
            //         ->action(function (Component $livewire) {
            //             // $this->fillForm();
            //             $data = Audit::factory()->make()->toArray();

            //             $livewire->form->fill($data);
            //         }),
            // ])
            //     ->label('Actions')
            //     ->columnSpanFull(),
        ];
    }
}
