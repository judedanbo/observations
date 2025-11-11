<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\ToCollection;

class TitleSheetImport implements HasReferencesToOtherSheets, ToCollection
{
    public function collection(Collection $collection)
    {
        // dd($collection);
    }
}
