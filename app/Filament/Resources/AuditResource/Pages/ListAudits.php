<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Enums\AuditStatusEnum;
use App\Filament\Resources\AuditResource;
use App\Models\Audit;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\callback;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    public $auditStatuses;

    public function __construct()
    {
        $this->auditStatuses = Audit::query()
            ->selectRaw('status, count(*) as order_count')
            ->groupBy('status')
            ->pluck('order_count', 'status');
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Audits')
                ->badge($this->auditStatuses->sum()),
            'scheduled' => Tab::make('Planned')
                ->modifyQueryUsing(fn (Builder $query) => $query->scheduled())
                ->badge($this->auditStatuses[AuditStatusEnum::PLANNED->value] ?? 0),
            'in_progress' => Tab::make('In Progress')
                ->badge($this->auditStatuses[AuditStatusEnum::IN_PROGRESS->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->inProgress()),
            'issued' => Tab::make('Issued')
                ->badge($this->auditStatuses[AuditStatusEnum::ISSUED->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->issued()),
            'transmitted' => Tab::make('Transmitted')
                ->badge($this->auditStatuses[AuditStatusEnum::TRANSMITTED->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->transmitted()),
            'archived' => Tab::make('Archived')
                ->badge($this->auditStatuses[AuditStatusEnum::ARCHIVED->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->archived()),
            'terminated' => Tab::make('Terminated')
                ->badge($this->auditStatuses[AuditStatusEnum::TERMINATED->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->terminated())


        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver(),
        ];
    }
}
