<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\StockHistory;

class StockController extends Controller
{    
    public function getStocks(Request $request)
    {
        $stocks = Stock::with('product', 'outlet')->get();

        return response()->json([
            'data' => $stocks,
        ]);
    }
    
    public function getStock($id)
    {
        $stock = Stock::with('product', 'outlet', 'stockHistories')->find($id);

        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }

        return response()->json([
            'data' => $stock,
        ]);
    }
    
    public function addStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'outlet_id' => 'required|exists:outlets,id',
            'quantity' => 'required|integer',
        ]);

        $stock = Stock::create([
            'product_id' => $request->product_id,
            'outlet_id' => $request->outlet_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'message' => 'Stock created successfully',
            'data' => $stock,
        ], 201);
    }
    
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|string|in:add,reduce',
            'note' => 'required|string',
        ]);

        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }

        if ($request->type === 'add') {
            $stock->quantity += $request->quantity;
        } else {
            $stock->quantity -= $request->quantity;
        }

        $stock->save();

        $history = StockHistory::create([
            'stock_id' => $stock->id,
            'quantity' => $request->quantity,
            'current_stock' => $stock->quantity,
            'type' => $request->type,
            'reference' => $request->reference ?? '',
            'user' => $request->user()->name,
            'note' => $request->note,
        ]);

        return response()->json([
            'message' => 'Stock updated successfully',
            'data' => $history,
        ]);
    }
    
    public function deleteStock($id)
    {
        $stock = Stock::find($id);

        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }

        // Delete related histories first
        $stock->stockHistories()->delete();

        $stock->delete();

        return response()->json([
            'message' => 'Stock deleted successfully',
        ]);
    }
}
