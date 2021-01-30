<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class PhpSpreadSheetController extends Controller
{
    public function index(Request $request){

        // get parameter from Request
        $startDate = Carbon::createFromFormat('d-m-Y', $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d-m-Y', $request->endDate)->format('Y-m-d');
    
        // dd($startDate);

        //make a new spreadsheet object
        $spreadsheet = new Spreadsheet();
        //get current active sheet (first sheet)
        $sheet = $spreadsheet->getActiveSheet();
        //set the value of cell a1 to "Hello World!"
        $sheet->setCellValue('A1', $startDate);

        //make an xlsx writer object using above spreadsheet
        $writer = new Xlsx($spreadsheet);
        //write the file in current directory
        // $writer->save('hello_world.xlsx');
        //redirect to the file
        // echo "<meta http-equiv='refresh' content='0;url=hello_world.xlsx'/>";
        $filename = 'name-of-the-generated-file';

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		
		$writer->save('php://output'); // download file 

    }
}
