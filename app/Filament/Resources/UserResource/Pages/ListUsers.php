<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    if (isset($data['password']) && $data['password'] !== '') {
                        $data['password'] = bcrypt($data['password']);
                    } else {
                        $data['password'] = bcrypt('password');
                    }

                    return $data;
                })
                ->after(function ($record) {
                    $record->assignRole('user');
                    // $record->notify(new UserCreatedNotification($record));
                })
                ->slideOver(),
        ];
    }
}
