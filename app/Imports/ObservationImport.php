<?php

namespace App\Imports;

use App\Enums\ObservationStatusEnum;
use App\Models\Audit;
use App\Models\District;
use App\Models\Finding;
use App\Models\Institution;
use App\Models\Observation;
use App\Models\Recommendation;
use App\Models\Region;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ObservationImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;

    private $audit_section;

    public function __construct($audit_section = '')
    {
        $this->audit_section = $audit_section;
    }


    public function collection(Collection $collection)
    {
        $loaded = 0;
        foreach ($collection as $row) {
            if ($row['covered_entity'] == null) {
                continue;
            }
            $region = Region::firstOrCreate([
                'name' => $row['region'],
            ]);
            $district = District::firstOrCreate([
                'name' => $row['district'],
                'region_id' => $region->id,
            ]);
            $institution = Institution::firstOrCreate([
                'name' => $row['covered_entity'],
                'district_id' => $district->id,
            ]);

            $audit = Audit::firstOrCreate([
                'title' => 'Audit of ' . $row['covered_entity'] . ' ' . $row['report_financial_year'],
                'year' => $row['report_financial_year'],
                'status' => 'issued',
            ]);

            $audit->institutions()->attach($institution->id);

            $observation = Observation::create([
                'audit_id' => $audit->id,
                'title' => $row['title_of_finding'],
                'status' => ObservationStatusEnum::ISSUED,
            ]);
            $type = $row['financial'] !== null ? 'financial' : ($row['internal_control'] !== null ? 'internal_control' : ($row['compliance'] !== null ? 'compliance' : ''));

            $finding = Finding::create([
                'observation_id' => $observation->id,
                'title' => $row['title_of_finding'],
                'type' => $type,
                'amount' => $row['amount'],
                'surcharge_amount' => $row['surcharge_amount'] ?? null,
            ]);

            $recommendation = Recommendation::create([
                'finding_id' => $finding->id,
                'title' => $row['recommendation'],
            ]);

            $report = Report::firstOrCreate(
                [
                    'institution_id' => $institution->id,
                    'audit_id' => $audit->id,
                    'paragraphs' => $row['report_paragraphs'],
                    'title' => $row['title_of_finding'],
                    'section' => $this->audit_section,
                    'type' => [
                        $row['financial'] !== null ? 'Financial' : '',
                        $row['internal_control'] !== null ? 'Internal Control' : '',
                        $row['compliance'] !== null ? 'Compliance' : ''
                    ],
                    'amount' => $row['amount'],
                    'recommendation' => $row['recommendation'],
                    'amount_recovered' => $row['amount_recovered'],
                    'surcharge_amount' => null,
                    'implementation_date' => Carbon::createFromTimestamp(($row['implementation_dateyear'] - 25569) * 86400)->toDateString(),
                    'implementation_status' => $row['implementation_status'],
                    'comments' => $row['comments_if_any']
                ]
            );
        }
    }

    public function headingRow(): int
    {
        return 4;
    }

    public function rules(): array
    {
        return [
            '*.region' => 'required|max:50',
            '*.district' => 'required|max:100',
            '*.covered_entity' => 'required|max:250',
            '*.report_financial_year' => 'required|integer|min:2000|max:2100',
            '*.title_of_finding' => 'required|max:250',
            '*.financial' => 'nullable|max:20',
            '*.internal_control' => 'nullable|max:20',
            '*.compliance' => 'nullable|max:20',
            '*.amount' => 'nullable|decimal:0,4|min:1',
            '*.recommendation' => 'required|max:250',
            '*.report_paragraphs' => 'required|max:15',
            '*.implementation_dateyear' => 'nullable|integer|min:36524|max:73049', // excel dates for 2000 to 2100
            '*.implementation_status' => 'nullable|max:50',
            '*.comments_if_any' => 'nullable|max:250',
            '*.amount_recovered' => 'nullable|decimal:0,4',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'implementation_dateyear.date' => 'The implementation date column of row :index must be a valid date.',
            'implementation_dateyear.min' => 'The implementation date column of row :index must be between 2000 and 2100.',
            'implementation_dateyear.max' => 'The implementation date column of row :index must be between 2000 and 2100.',
        ];
    }


    public function customValidationAttributes(): array
    {
        return [
            'implementation_dateyear' => 'Implementation date'
        ];
    }
}
