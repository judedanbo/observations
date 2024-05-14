<?php

namespace App\Imports;

use App\Models\Audit;
use App\Models\District;
use App\Models\Institution;
use App\Models\Region;
use App\Models\Report;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ObservationImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $audit_section;

    public function __construct($audit_section = '')
    {
        // dd($audit_section);
        $this->audit_section = $audit_section;
    }


    public function collection(Collection $collection)
    {
        // dd($this->audit_section);
        $loaded = 0;
        foreach ($collection as $row) {
            if ($row['covered_entity'] == null) {
                continue;
            }
            // $loaded = $loaded++;
            // dd($row['district']);
            $region = Region::firstOrCreate([
                'name' => $row['region'],
            ]);
            $district = District::firstOrCreate([
                'name' => $row['district'],
                'region_id' => $region->id,
            ]);
            // dd($row);
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
                    'implementation_date' => $row['implementation_dateyear'],
                    'implementation_status' => $row['implementation_status'],
                    'comments' => $row['comments_if_any']
                ]
            );

            // 'title' => $row['title_of_findings'],
            // $region->districts()->save(

            //     new District([
            //         'name' => $row['district'],
            //     ])
            // );
            // dd($region);
        }
        // return $loaded;
        // dd($collection);
    }

    public function headingRow(): int
    {
        return 4;
    }
}
