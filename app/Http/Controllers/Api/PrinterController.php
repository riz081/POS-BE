<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Printer;

class PrinterController extends Controller
{
    //add printer
    public function addPrinter(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'connection_type' => 'required|string',
            'paper_width' => 'required|integer',
            'outlet_id' => 'required|integer',
        ]);

        $printer = Printer::create([
            'name' => $request->name,
            'connection_type' => $request->connection_type,
            'paper_width' => $request->paper_width,
            'outlet_id' => $request->outlet_id,
            'mac_address' => $request->mac_address,
            'ip_address' => $request->ip_address,
        ]);

        return response()->json([
            'message' => 'Printer added successfully',
            'data' => $printer,
        ], 201);
    }

    //get printers by outlet
    public function getPrintersByOutlet($outlet_id)
    {
        $printers = Printer::where('outlet_id', $outlet_id)->get();

        return response()->json([
            'data' => $printers,
        ]);
    }
}
