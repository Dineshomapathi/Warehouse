<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class ExportController extends Controller
{

    public function export() 
    {
        return Excel::download(new ExcelExport, 'Inventory.xlsx');
    }
    
}
