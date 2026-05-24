<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Qorder;
use App\Models\QorderItem;
use App\Models\Product;
use App\Models\Stategst;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class QuotationController extends Controller
{
    /**
     * Display a listing of quotations.
     */
    public function index(Request $request)
    {
        $query = Qorder::where('qstate', 1); // Active quotations

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('morder_id', 'like', "%{$search}%");
            });
        }

        $quotations = $query->orderBy('order_id', 'desc')->paginate(15);

        return view('admin.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();
        $states = Stategst::orderBy('sname', 'asc')->get();
        $customers = \App\Models\User::where('usertype', 'C')->where('ustatus', 1)->orderBy('uname', 'asc')->get();

        return view('admin.quotations.create', compact('products', 'states', 'customers'));
    }

    /**
     * Store a newly created quotation in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'orderDate' => 'required|date',
            'clientName' => 'required|string|max:255',
            'mobileno' => 'required|string|max:25',
            'clientContact' => 'required|string',
            'subTotal' => 'required|numeric',
            'grandTotal' => 'required|numeric',
            'qtype' => 'required|integer',
            'qstate' => 'required|integer',
            'productName' => 'required|array',
            'quantity' => 'required|array',
            'rateValue' => 'required|array',
            'totalValue' => 'required|array',
            'signature' => 'nullable|image|max:1024',
        ]);

        $orderDate = date('Y-m-d', strtotime($request->input('orderDate')));
        $year = date('Y', strtotime($orderDate));

        // Calculate morder_id (invoice number) for this year
        $maxMorderId = Qorder::whereYear('order_date', $year)->max('morder_id');
        $morder_id = ($maxMorderId ?? 0) + 1;

        try {
            DB::beginTransaction();

            $clientname = strtoupper(trim($request->input('clientName')));
            $clientcontact = strtoupper(trim($request->input('clientContact')));
            $mobile = trim($request->input('mobileno'));
            $gsttin = trim($request->input('gsttin', ''));

            // Handle signature upload to legacy folder /Users/chikku/Downloads/admin/sign/
            $urlff = '';
            if ($request->hasFile('signature')) {
                $dir = '/Users/chikku/Downloads/admin/sign/';
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                $file = $request->file('signature');
                $ext = $file->getClientOriginalExtension();
                // Overwrite behavior or unique behavior: legacy had a simple signa.ext overwrite.
                // We will name it unique per quote or standard signa.ext. Let's make it unique per quote:
                $urlff = 'signa_' . time() . '.' . $ext;
                $file->move($dir, $urlff);
            }

            // Create Quotation
            $qorder = Qorder::create([
                'order_date' => $orderDate,
                'client_name' => $clientname,
                'client_contact' => $clientcontact,
                'sub_total' => $request->input('subTotal'),
                'grand_total' => $request->input('grandTotal'),
                'gstn' => $request->input('igst', 0),
                'user_id' => Auth::guard('admin')->id() ?? 1, // Logged in administrative user
                'morder_id' => $morder_id,
                'mobile' => $mobile,
                'gsttin' => $gsttin,
                'instamt' => $request->input('intcharge', 0),
                'shipamt' => $request->input('shipcharge', 0),
                'status' => 1,
                'qtype' => $request->input('qtype'),
                'signa' => $urlff,
                'discount' => $request->input('discount', 0),
                'gtotal' => $request->input('gTotal', $request->input('grandTotal')),
                'qstate' => $request->input('qstate')
            ]);

            // Save items (Wait: in qorder_item, product_id column stores string NAME of the product!)
            $productNames = $request->input('productName');
            $quantities = $request->input('quantity');
            $rates = $request->input('rateValue');
            $totals = $request->input('totalValue');
            $hsnsacs = $request->input('hsnsac');
            $gsts = $request->input('gst');
            $units = $request->input('unit');
            $wgsts = $request->input('wgst', []);

            for ($x = 0; $x < count($productNames); $x++) {
                QorderItem::create([
                    'order_id' => $qorder->order_id,
                    'product_id' => strtoupper($productNames[$x]), // String name
                    'hsnsan' => $hsnsacs[$x] ?? '',
                    'gst' => $gsts[$x] ?? 0,
                    'qty' => $quantities[$x],
                    'rate' => $rates[$x],
                    'unit' => $units[$x] ?? 'PCS',
                    'total' => $totals[$x],
                    'gstr' => $wgsts[$x] ?? 0
                ]);
            }

            DB::commit();
            return redirect()->route('admin.quotations.index')->with('success', 'Quotation successfully created! Code #' . $morder_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error creating quotation: ' . $e->getMessage());
        }
    }

    /**
     * Show the edit form.
     */
    public function edit($id)
    {
        $quotation = Qorder::with('items')->findOrFail($id);
        $products = Product::where('status', 1)->orderBy('productname', 'asc')->get();
        $states = Stategst::orderBy('sname', 'asc')->get();
        $customers = \App\Models\User::where('usertype', 'C')->where('ustatus', 1)->orderBy('uname', 'asc')->get();

        return view('admin.quotations.edit', compact('quotation', 'products', 'states', 'customers'));
    }

    /**
     * Update the quotation in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'orderDate' => 'required|date',
            'clientName' => 'required|string|max:255',
            'mobileno' => 'required|string|max:25',
            'clientContact' => 'required|string',
            'subTotal' => 'required|numeric',
            'grandTotal' => 'required|numeric',
            'qtype' => 'required|integer',
            'qstate' => 'required|integer',
            'productName' => 'required|array',
            'quantity' => 'required|array',
            'rateValue' => 'required|array',
            'totalValue' => 'required|array',
            'signature' => 'nullable|image|max:1024',
        ]);

        $qorder = Qorder::findOrFail($id);
        $orderDate = date('Y-m-d', strtotime($request->input('orderDate')));

        try {
            DB::beginTransaction();

            $clientname = strtoupper(trim($request->input('clientName')));
            $clientcontact = strtoupper(trim($request->input('clientContact')));
            $mobile = trim($request->input('mobileno'));
            $gsttin = trim($request->input('gsttin', ''));

            // Handle signature upload
            $urlff = $qorder->signa;
            if ($request->hasFile('signature')) {
                $dir = '/Users/chikku/Downloads/admin/sign/';
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                // Delete old signature file if it exists
                if ($urlff) {
                    @unlink($dir . $urlff);
                }

                $file = $request->file('signature');
                $ext = $file->getClientOriginalExtension();
                $urlff = 'signa_' . time() . '.' . $ext;
                $file->move($dir, $urlff);
            }

            // Update Qorder
            $qorder->update([
                'order_date' => $orderDate,
                'client_name' => $clientname,
                'client_contact' => $clientcontact,
                'sub_total' => $request->input('subTotal'),
                'grand_total' => $request->input('grandTotal'),
                'gstn' => $request->input('igst', 0),
                'mobile' => $mobile,
                'gsttin' => $gsttin,
                'instamt' => $request->input('intcharge', 0),
                'shipamt' => $request->input('shipcharge', 0),
                'qtype' => $request->input('qtype'),
                'signa' => $urlff,
                'discount' => $request->input('discount', 0),
                'gtotal' => $request->input('gTotal', $request->input('grandTotal')),
                'qstate' => $request->input('qstate')
            ]);

            // Re-insert items
            QorderItem::where('order_id', $qorder->order_id)->delete();

            $productNames = $request->input('productName');
            $quantities = $request->input('quantity');
            $rates = $request->input('rateValue');
            $totals = $request->input('totalValue');
            $hsnsacs = $request->input('hsnsac');
            $gsts = $request->input('gst');
            $units = $request->input('unit');
            $wgsts = $request->input('wgst', []);

            for ($x = 0; $x < count($productNames); $x++) {
                QorderItem::create([
                    'order_id' => $qorder->order_id,
                    'product_id' => strtoupper($productNames[$x]), // String name
                    'hsnsan' => $hsnsacs[$x] ?? '',
                    'gst' => $gsts[$x] ?? 0,
                    'qty' => $quantities[$x],
                    'rate' => $rates[$x],
                    'unit' => $units[$x] ?? 'PCS',
                    'total' => $totals[$x],
                    'gstr' => $wgsts[$x] ?? 0
                ]);
            }

            DB::commit();
            return redirect()->route('admin.quotations.index')->with('success', 'Quotation successfully updated! Code #' . $qorder->morder_id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error updating quotation: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete/cancel quotation.
     */
    public function destroy($id)
    {
        $qorder = Qorder::findOrFail($id);
        $qorder->update(['qstate' => 2]); // Inactive/cancelled

        return redirect()->route('admin.quotations.index')->with('success', 'Quotation successfully cancelled!');
    }

    /**
     * Print/View Quotation, Estimate, or Proforma Invoice.
     */
    public function print($id)
    {
        $quotation = Qorder::with('items')->findOrFail($id);
        return view('admin.quotations.print', compact('quotation'));
    }
}
