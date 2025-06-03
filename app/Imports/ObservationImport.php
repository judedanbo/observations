<?php

namespace App\Imports;

use App\Enums\AuditStatusEnum;
use App\Enums\ObservationStatusEnum;
use App\Models\Audit;
use App\Models\Department;
use App\Models\District;
use App\Models\Finding;
use App\Models\Institution;
use App\Models\Observation;
use App\Models\Recommendation;
use App\Models\Region;
use App\Models\Report;
use App\Models\Status;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;

class ObservationImport implements ToCollection, WithHeadingRow, WithValidation, WithMultipleSheets
{
    use Importable;

    private $auditSection;
    private $managementLetter;

    public function __construct($auditSection = '', $managementLetter = null)
    {
        $this->auditSection = $auditSection;
        $this->managementLetter = $managementLetter;
    }

    public function sheets(): array
    {
        return [
            'Title' => new TitleSheetImport(),
            'Details' => new FindingsSheetImport($this->auditSection, $this->managementLetter),
        ];
    }

    public function collection(Collection $collection)
    {
        $loaded = 0;
        // dd($collection);
        foreach ($collection as $row) {
            if ($row['covered_entity'] == null) {
                continue;
            }
            $department = $row['department'] ?? 'Unknown Department';
            $auditRegion = $row['audit_region'] ?? 'Unknown Region';
            $auditUnit = $row['audit_unit'] ?? 'Unknown Unit';
            $region =  $row['region'];
            $district =  $row['district'];

            $departmentModel = Department::firstOrCreate([
                'name' => Str::of($department)->replace('_', ' '),
            ]);
            $unitModel = Unit::firstOrCreate([
                'name' => Str::of($auditUnit)->replace('_', ' '),
                'department_id' => $departmentModel->id,
            ]);
            $region = Region::firstOrCreate([
                'name' => Str::of($region)->replace('_', " "),
            ]);
            $district = District::firstOrCreate([
                'name' => Str::of($district)->replace('_', ' '),
                'region_id' => $region->id,
            ]);
            $institution = Institution::firstOrCreate([
                'name' => $row['covered_entity'],
                'district_id' => $district->id,
            ]);

            $audit = Audit::firstOrCreate([
                'title' => 'Audit of ' . $row['covered_entity'] . ' ' . $row['report_financial_year'],
                'year' => $row['report_financial_year'],
                'status' => AuditStatusEnum::ISSUED,
            ]);

            $audit->institutions()->attach($institution->id);

            $audit->district()->attach($district->id);

            $observation = Observation::create([
                'audit_id' => $audit->id,
                'title' => $row['title_of_finding'],
                'status' => ObservationStatusEnum::REPORTED,
            ]);
            $type = $row['financial'] !== null ? 'financial' : ($row['internal_control'] !== null ? 'internal_control' : ($row['compliance'] !== null ? 'compliance' : ''));
            $finding = Finding::create([
                'observation_id' => $observation->id,
                'title' => $row['title_of_finding'],
                'type' => $type,
                'amount' => $row['amount'],
                'surcharge_amount' => $row['surcharge_amount'] ?? null,
            ]);
            if ($row['implementation_status'] !== '') {
                dd($row['implementation_status']);
                // $finding->implementation_date = Carbon::createFromTimestamp(($row['implementation_dateyear'] - 25569) * 86400)->toDateString();
                $status = Status::create([
                    // 'finding_id' => $finding->id,
                    'name' => $row['implementation_status'],
                    'implementation_date' => Carbon::createFromTimestamp(($row['implementation_dateyear'] - 25569) * 86400)->toDateString(),
                ]);
                $finding->statuses->attach($status->id);
            }


            $recommendation = Recommendation::create([
                'finding_id' => $finding->id,
                'title' => $row['recommendation'],
            ]);

            // $type = $row['financial'] !== null ? 'financial' : ($row['internal_control'] !== null ? 'internal_control' : ($row['compliance'] !== null ? 'compliance' : ''));
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
                    'implementation_date' => Carbon::createFromTimestamp(($row['implementation_dateyear'] - 25569) * 86400)->toDateString(),
                    'implementation_status' => $row['implementation_status'],
                    'comments' => $row['comments_if_any'],
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
            // '*.region' => 'required|max:50',
            // '*.district' => 'required|max:100',
            // '*.covered_entity' => 'required|max:250',
            // '*.report_financial_year' => 'required|integer|min:2000|max:2100',
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
            'implementation_dateyear' => 'Implementation date',
        ];
    }
}
