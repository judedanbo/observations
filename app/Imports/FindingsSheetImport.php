<?php

namespace App\Imports;

use App\Enums\ObservationStatusEnum;
use App\Models\Audit;
use App\Models\Department;
use App\Models\District;
use App\Models\Finding;
use App\Models\Institution;
use App\Models\Observation;
use App\Models\Recommendation;
use App\Models\Region;
use Illuminate\Support\Str;
use App\Models\Report;
use App\Models\Status;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FindingsSheetImport implements ToCollection, WithHeadingRow, WithValidation, WithCalculatedFormulas
{
    private $auditSection;
    private $managementLetter;
    public function __construct($auditSection = '', $managementLetter)
    {
        $this->auditSection = $auditSection;
        $this->managementLetter = $managementLetter;
    }

    public function collection(Collection $collection)
    {
        // dd($this->managementLetter);
        foreach ($collection as $row) {
            if ($row['covered_entity'] == null) {
                continue;
            }
            $department = Str::of($row['department']);
            $auditRegion = Str::of($row['audit_region'])->replace('-', ' ');
            $auditUnit = Str::of($row['audit_unit'])->replace('-', ' ');
            $region = Str::of($row['region'])->replace('-', ' ');
            $district = Str::of($row['district'])->replace('-', ' ');

            // if (Department::where('short_name', $department)->exists()) {
            $departmentModel = Department::where('short_name', $department)->first();
            // } else {
            //     $departmentModel = Department::create([
            //         'name' => Str::of($department)->replace('_', ' '),
            //         'short_name' => Str::of($department)->replace('_', ' '),
            //     ]);
            // }
            // if (is_null($departmentModel)) {
            //     dd('error in departtment');
            // }
            $unitModel = Unit::firstOrCreate([
                'name' => Str::of($auditUnit)->replace('_', ' '),
                'department_id' => $departmentModel->id,
            ]);
            $region = Region::firstOrCreate([
                'name' => $region,
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
                'title' => $row['audit_title'], //'Audit of ' . $row['covered_entity'] . ' ' . $row['report_financial_year'],
                'year' => $row['report_financial_year'],
                'status' => 'issued',
                'type' => $this->auditSection,
            ]);

            if ($this->managementLetter) {
                $audit->addManagementLetter($this->managementLetter);
            }

            $observation = Observation::create([
                'audit_id' => $audit->id,
                'title' => $row['title_of_finding'],
                'status' => ObservationStatusEnum::REPORTED,
            ]);
            $type = $row['control_type'] === 'Financial' ? 'financial' : ($row['control_type'] === 'Internal Control' ? 'internal_control' : ($row['control_type'] === 'Compliance' ? 'compliance' : ''));
            // dd($type);
            $finding = Finding::create([
                'observation_id' => $observation->id,
                'title' => $row['title_of_finding'],
                'type' => $type,
                'amount' => $row['amount'] ?? 0,
                'surcharge_amount' => $row['surcharge_amount'] ?? 0,
            ]);
            if ($row['implementation_status'] !== null) {
                // dd($row['implementation_status']);
                // $finding->implementation_date = Carbon::createFromTimestamp(($row['implementation_dateyear'] - 25569) * 86400)->toDateString();
                $status = Status::create([
                    // 'finding_id' => $finding->id,
                    'name' => $row['implementation_status'],
                    'implementation_date' => $row['implementation_dateyear'] ? Carbon::createFromTimestamp(($row['implementation_dateyear'] - 25569) * 86400)->toDateString() : null,
                ]);
                // dd($status);
                $finding->statuses()->attach($status->id);
            }

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
                    'section' => $this->auditSection,
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
        $audit->districts()->attach($district->id);

        $audit->units()->attach($unitModel->id);
    }
    public function headingRow(): int
    {
        return 4;
    }
    public function rules(): array
    {
        return [
            '*.department' => 'required|exists:departments,short_name',
            '*.title_of_finding' => 'required|max:250',
            '*.control_type' => 'required|max:20',
            '*.region' => 'required|max:50',
            '*.district' => 'required|max:50',
            '*.covered_entity' => 'required|max:250',
            '*.report_financial_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            '*.audit_title' => 'required|unique:audits,title|max:250',
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
            'region.required' => 'The region field in the Title Sheet is required.',
            'region.max' => 'The region field in the Title Sheet must not exceed 50 characters.',
            'district.required' => 'The district field in the Title Sheet is required.',
            'district.max' => 'The district field in the Title Sheet must not exceed 50 characters.',
            'covered_entity.required' => 'The covered entity field in the Title Sheet is required.',
            'covered_entity.max' => 'The covered entity field in the Title Sheet must not exceed 250 characters.',
            'report_financial_year.required' => 'The report financial year field in the Title Sheet is required.',
            'report_financial_year.integer' => 'The report financial year field in the Title Sheet must be an number.',
            'report_financial_year.min' => 'The report financial year field in the Title Sheet must be not be before the year 2000.',
            'report_financial_year.max' => 'The report financial year field in the Title Sheet must be not be after the year' . date('Y') + 1,
            'audit_title.required' => 'The audit title field in the Title Sheet is is required.',
            'audit_title.unique' => 'An audit of the same title in the Audit Title field in the Title Sheet is already imported. please ensure that the audit title is in the title sheet.',
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
