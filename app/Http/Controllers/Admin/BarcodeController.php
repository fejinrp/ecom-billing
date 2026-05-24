<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Helpers\BarcodeGenerator;

class BarcodeController extends Controller
{
    /**
     * Show the searchable barcode generator panel.
     */
    public function index()
    {
        // Fetch all active products ordered by productname for searchable list
        $products = Product::where('status', 1)
            ->orderBy('productname', 'asc')
            ->get();

        return view('admin.products.barcode', compact('products'));
    }

    /**
     * Generate the printable vector barcode sheet.
     */
    public function print(Request $request)
    {
        $request->validate([
            'pcode' => 'required|string|max:10',
            'pname' => 'required|string|max:255',
            'mrp' => 'required|numeric',
            'cprice' => 'required|numeric',
            'dprice' => 'required|numeric',
            'sdprice' => 'required|numeric',
            'qty' => 'required|integer|min:1|max:120',
        ]);

        $pcode = strtoupper(trim($request->input('pcode')));
        $pname = substr(trim($request->input('pname')), 0, 16); // Truncate to 16 characters as in legacy
        
        $mrp = round($request->input('mrp'));
        $cprice = round($request->input('cprice'));
        $dprice = round($request->input('dprice'));
        $sdprice = round($request->input('sdprice'));
        $qty = (int) $request->input('qty');

        // Generate obfuscation values
        $dealerObfuscation = 'M' . rand(0, 9);
        $sdealerObfuscation = 'SD' . rand(10, 99);

        // Generate crisp vector SVG Code39 barcode
        // A widthFactor of 1.15 makes the barcode fit perfectly on custom 36mm wide tags
        $barcodeSvg = BarcodeGenerator::code39($pcode, 1.1, 40);

        return view('admin.products.barcode_print', compact(
            'pcode',
            'pname',
            'mrp',
            'cprice',
            'dprice',
            'sdprice',
            'qty',
            'dealerObfuscation',
            'sdealerObfuscation',
            'barcodeSvg'
        ));
    }
}
