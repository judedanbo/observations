<?php

namespace App\Enums;

enum DepartmentEnum: string
{
    case CGAD = 'CGAD';
    case PSAD = 'PSAD';
    case CAD = 'CAD';
    case EIDA_NZ = 'EIDA_NZ';
    case EIDA_SZ = 'EIDA_SZ';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CGAD => 'Central Government and Agency Department',
            self::CAD => 'Commercial Audit Department',
            self::PSAD => 'Performance and Special Audit Department',
            self::EIDA_NZ => 'Educational Institutions and District Assemblies Northern Zone',
            self::EIDA_SZ => 'Educational Institutions and District Assemblies Southern Zone',
        };
    }
}
