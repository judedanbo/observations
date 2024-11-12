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
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FindingsSheetImport implements ToCollection, WithHeadingRow, WithValidation, WithCalculatedFormulas
{
    private $audit_section;
    public function __construct($audit_section = '')
    {
        $this->audit_section = $audit_section;
    }

    public function collection(Collection $collection)
    {
        // dd($collection);
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

            // $audit->institutions()->attach($institution->id);

            $observation = Observation::create([
                'audit_id' => $audit->id,
                'title' => $row['title_of_finding'],
                'status' => ObservationStatusEnum::ISSUED,
            ]);
            $type = $row['control_type'] === 'Financial' ? 'financial' : ($row['control_type'] === 'Internal Control' ? 'internal_control' : ($row['control_type'] === 'Compliance' ? 'compliance' : ''));
            // dd($type);
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
                    'finding_id' => $finding->id,
                    'paragraphs' => $row['report_paragraphs'],
                    'title' => $row['title_of_finding'],
                    'section' => $this->audit_section,
                    'type' => $type,
                    'amount' => $row['amount'],
                    'recommendation' => $row['recommendation'],
                    'amount_recovered' => $row['amount_recovered'],
                    'surcharge_amount' => null,
                    'implementation_date' => $row['implementation_dateyear'] ? Carbon::createFromTimestamp(($row['implementation_dateyear'] - 25569) * 86400)->toDateString() : null,
                    'implementation_status' => $row['implementation_status'],
                    'comments' => $row['comments_if_any'],
                ]
            );
        }
        $audit->institutions()->attach($institution->id);
    }
    public function headingRow(): int
    {
        return 4;
    }
    public function rules(): array
    {
        return [
            '*.title_of_finding' => 'required|max:250',
            '*.control_type' => 'required|max:20',
            // '*.financial' => 'nullable|max:20',
            // '*.internal_control' => 'nullable|max:20',
            // '*.compliance' => 'nullable|max:20',
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
            'report_paragraphs.required' => 'The report paragraphs column of row :index is required.',
            'title_of_finding.required' => 'The title of finding column of row :index is required.',
            'title_of_finding.max' => 'The title of finding column of row :index must not exceed 250 characters.',
            'control_type.max' => 'The control type column of row :index must not exceed 20 characters.',
            'amount.decimal' => 'The amount column of row :index must be a decimal with up to 4 decimal places.',
            'amount.min' => 'The amount column of row :index must be at least 1.',
            'recommendation.required' => 'The recommendation column of row :index is required.',
            'implementation_dateyear.date' => 'The implementation date column of row :index must be a valid date.',
            'implementation_dateyear.min' => 'The implementation date column of row :index must be between 2000 and 2100.',
            'implementation_dateyear.max' => 'The implementation date column of row :index must be between 2000 and 2100.',
            'implementation_dateyear.integer' => 'The :attribute column of row :index must be an integer',
            'implementation_status.max' => 'The implementation status column of row :index must not exceed 50 characters.',
            'comments_if_any.max' => 'The comments column of row :index must not exceed 250 characters.',
            'amount_recovered.decimal' => 'The amount recovered column of row :index must be a decimal with up to 4 decimal places.',

        ];
    }
    public function customValidationAttributes(): array
    {
        return [
            'implementation_dateyear' => 'Implementation date',
        ];
    }
}
