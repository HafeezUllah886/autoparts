<?php

namespace App\Http\Controllers;

use App\Exports\productsExport;
use App\Http\Controllers\Controller;
use App\Models\products;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class priceAdjustmentController extends Controller
{
    public function index()
    {
        $products = products::all();

        return view('products.adjustment', compact('products'));
    }

    public function store(request $request)
    {
        $ids = $request->ids;

        foreach($ids as $key => $id)
        {
            $product = products::find($id);
            $product->update(
                [
                    'pprice' => $request->pprice[$key] ?? 0,
                    'price' => $request->price[$key] ?? 0,
                ]
            );
        }

        return redirect('/products')->with('success', 'Prices Updated');
    }

    public function export()
    {
        return Excel::download(new productsExport, 'products.xlsx');
    }
}
