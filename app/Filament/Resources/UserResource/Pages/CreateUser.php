<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // dd($data['password']);
    //     if (isset($data['password']) && $data['password'] !== '') {
    //         $data['password'] = bcrypt($data['password']);
    //     } else {
    //         // unset($data['password']);
    //         $data['password'] = bcrypt('password');
    //     }

    //     return $data;
    // }
}
