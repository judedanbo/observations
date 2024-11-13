<?php

namespace App\Models;

use App\Http\Traits\LogAllTraits;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, LogAllTraits, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }

    public function audits(): BelongsToMany
    {
        return $this->belongsToMany(Audit::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function observations(): BelongsToMany
    {
        return $this->belongsToMany(Observation::class);
    }

    public function findings(): BelongsToMany
    {
        return $this->belongsToMany(Finding::class);
    }

    public function recommendations(): BelongsToMany
    {
        return $this->belongsToMany(Recommendation::class);
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(Action::class);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class);
    }

    public function followUps(): BelongsToMany
    {
        return $this->belongsToMany(FollowUp::class);
    }
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class);
    }
    public function getFilenameAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(250),
            Textarea::make('description')
                ->columnSpanFull(),
            FileUpload::make('file')
                // ->multiple()
                ->acceptedFileTypes([
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-pexcel',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/msword',
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                ])
                ->required(),
            Actions::make([
                Action::make('Save')
                    ->label('Generate data')
                    ->icon('heroicon-m-arrow-path')
                    ->outlined()
                    ->color('gray')
                    ->visible(function (string $operation) {
                        if ($operation !== 'create') {
                            return false;
                        }
                        if (! app()->environment('local')) {
                            return false;
                        }

                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Document::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ])
                ->label('Actions')
                ->columnSpanFull(),
        ];
    }
}
