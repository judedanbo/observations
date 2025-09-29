<?php

namespace App\Enums;

enum UnitTypeEnum: string
{
    case UNIT = 'unit';
    case SECTOR = 'sector';
    case DISTRICT = 'district';
    case BRANCH = 'branch';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNIT => 'Unit',
            self::DISTRICT => 'District',
            self::SECTOR => 'Sector',
            self::BRANCH => 'Branch',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::UNIT => 'Unit',
            self::SECTOR => 'Sector',
            self::DISTRICT => 'District',
            self::BRANCH => 'Branch',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNIT => 'blue',
            self::SECTOR => 'green',
            self::DISTRICT => 'yellow',
            self::BRANCH => 'orange',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::UNIT => 'heroicon-o-office-building',
            self::SECTOR => 'heroicon-o-building-office-2',
            self::DISTRICT => 'heroicon-o-map',
            self::BRANCH => 'heroicon-o-archive',
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
