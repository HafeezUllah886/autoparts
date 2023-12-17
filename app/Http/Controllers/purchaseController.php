<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\ledger;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\purchase_draft;
use App\Models\stock;
use App\Models\transactions;
use Illuminate\Http\Request;

class purchaseController extends Controller
{
    public function purchase()
    {
        $vendors = account::where('type', '!=', 'Business')->get();
        $paidFroms = account::where('type', 'Business')->get();
        $products = products::all();
        return view('purchase.purchase')->with(compact('vendors', 'products', 'paidFroms'));
    }

    public function StoreDraft(request $req)
    {
        $check = purchase_draft::where('product_id', $req->product)->count();
        if ($check > 0) {
            return "Existing";
        }

        purchase_draft::create(
            [
                'product_id' => $req->product,
                'qty' => $req->qty,
                'rate' => $req->rate,
            ]
        );

        products::where('id', $req->product)->update(
            [
                'price' => $req->price,
            ]
        );

        return "Done";
    }

    public function draftItems()
    {
        $items = purchase_draft::with('product')->get();

        return view('purchase.draft')->with(compact('items'));
    }

    public function updateDraftQty($id, $qty)
    {
        $item = purchase_draft::find($id);
        $item->qty = $qty;
        $item->save();

        return "Qty Updated";
    }

    public function updateDraftRate($id, $rate)
    {
        $item = purchase_draft::find($id);
        $item->rate = $rate;
        $item->save();

        return "Rate Updated";
    }

    public function deleteDraft($id)
    {
        purchase_draft::find($id)->delete();
        return "Draft deleted";
    }

    public function storePurchase(request $req)
    {
        $req->validate([
            'date' => 'required',
            'vendor' => 'required',
            'walkIn' => 'required_if:vendor,0',
            'amount' => 'required_if:isPaid,Partial',
            'paidFrom' => 'required_unless:isPaid,No',
        ], [
            'date.required' => 'Select Date',
            'vendor.required' => 'Select Vendor',
            'amount' => 'Enter Paid Amount',
            'paidFrom' => 'Select Account',
            'walkn' => 'Enter Vendor Name'
        ]);
        $ref = getRef();
        $vendor = null;
        $walkIn = null;
        $amount = null;
        $paidFrom = null;
        if ($req->isPaid == 'Yes') {
            if ($req->vendor == 0) {
                $walkIn = $req->walkIn;
            } else {
                $vendor = $req->vendor;
            }
            $paidFrom = $req->paidFrom;
        } elseif ($req->isPaid == 'No') {
            $vendor = $req->vendor;
        } else {
            $vendor = $req->vendor;
            $paidFrom = $req->paidFrom;
            $amount = $req->amount;
        }

        $purchase = purchase::create([
            'vendor' => $vendor,
            'walking' => $walkIn,
            'paidFrom' => $paidFrom,
            'date' => $req->date,
            'desc' => $req->desc,
            'amount' => $amount,
            'isPaid' => $req->isPaid,
            'ref' => $ref,
        ]);

        $desc = "<strong>Purchased</strong><br/> Bill No. " . $purchase->id;
        $items = purchase_draft::all();
        $total = 0;
        $amount1 = 0;
        foreach ($items as $item) {
            $amount1 = $item->rate * $item->qty;
            $total += $amount1;
            purchase_details::create([
                'bill_id' => $purchase->id,
                'product_id' => $item->product_id,
                'rate' => $item->rate,
                'qty' => $item->qty,
                'date' => $req->date,
                'ref' => $ref,
            ]);

            stock::create([
                'product_id' => $item->product_id,
                'date' => $req->date,
                'desc' => $desc,
                'cr' => $item->qty,
                'ref' => $ref
            ]);
        }
        $desc1 = "<strong>Products Purchased</strong><br/>Bill No. " . $purchase->id;
        $desc2 = "<strong>Products Purchased</strong><br/>Partial payment of Bill No. " . $purchase->id;
        if ($req->vendor != 0) {
            $check_vendor = account::find($req->vendor);
            if ($req->isPaid == 'Yes') {
                createTransaction($req->paidFrom, $req->date, 0, $total, $desc1, "Purchase", $ref);
                createTransaction($req->vendor, $req->date, $total, $total, $desc1, "Purchase", $ref);
            } elseif ($req->isPaid == 'No') {
                if ($check_vendor->type == "Vendor") {
                    createTransaction($req->vendor, $req->date, $total, 0, $desc1, "Purchase", $ref);
                } else {
                    createTransaction($req->vendor, $req->date, 0, $total, $desc1, "Purchase", $ref);
                }
            } else {
                if ($check_vendor->type == "Vendor") {
                    createTransaction($req->vendor, $req->date, $total, $req->amount, $desc2, "Purchase", $ref);
                } else {
                    createTransaction($req->vendor, $req->date, $req->amount, $total, $desc2, "Purchase", $ref);
                }
                createTransaction($req->paidFrom, $req->date, 0, $req->amount, $desc1, "Purchase", $ref);
            }
        } else {
            createTransaction($req->paidFrom, $req->date, 0, $total, $desc1, "Purchase", $ref);
        }
        $ledger_head = null;
        $ledger_type = null;
        $ledger_details = "Stock Purchased";
        $ledger_amount = null;
        $v_acct = account::find($req->vendor);
        $p_acct = account::find($req->paidFrom);
        if ($req->isPaid == "Yes") {
            if ($req->vendor == 0) {
                $ledger_head = $req->walkIn . "(Walk-In)";
            } else {
                $ledger_head = $v_acct->title;
            }
            $ledger_type = $p_acct->title . "/Paid";
            $ledger_amount = $total;
        } elseif ($req->isPaid == "No") {
            $ledger_head = $v_acct->title;
            $ledger_type = "/Unpaid";
            $ledger_amount = $total;
        } else {
            $ledger_head = $v_acct->title;
            $ledger_type = $p_acct->title . "/Partial";
            $ledger_amount = $req->amount;
        }
        addLedger($req->date, $ledger_head, $ledger_type, $ledger_details, $ledger_amount, $ref);
        purchase_draft::truncate();
        return redirect('/purchase/history');
    }
    public function history()
    {
        $history = purchase::with('vendor_account', 'account')->orderBy('id', 'desc')->get();
        return view('purchase.history')->with(compact('history'));
    }

    public function edit($id)
    {
        $bill = purchase::where('id', $id)->first();
        $vendors = account::where('type', '!=', 'Business')->get();
        $paidFroms = account::where('type', 'Business')->get();
        $products = products::all();

        return view('purchase.edit')->with(compact('bill', 'products', 'vendors', 'paidFroms'));
    }

    public function editItems($id)
    {
        $items = purchase_details::with('product')->where('bill_id', $id)->get();

        return view('purchase.edit_details')->with(compact('items'));
    }

    public function editAddItems(request $req, $id)
    {
        $check = purchase_details::where('product_id', $req->product)->where('bill_id', $id)->count();
        if ($check > 0) {
            return "Existing";
        }
        $bill = purchase::where('id', $id)->first();
        $purchase = purchase_details::create(
            [
                'bill_id' => $bill->id,
                'product_id' => $req->product,
                'qty' => $req->qty,
                'rate' => $req->rate,
                'ref' => $bill->ref,
            ]
        );
        $desc = "<strong>Purchased</strong><br/> Bill No. " . $purchase->id;
        stock::create([
            'product_id' => $purchase->product_id,
            'date' => $bill->date,
            'desc' => $desc,
            'cr' => $req->qty,
            'ref' => $bill->ref
        ]);
        updatePurchaseAmount($bill->id);
        return "Done";
    }

    public function deleteEdit($id)
    {

        $item = purchase_details::find($id);
        $bill = $item->bill;
        $item->delete();
        stock::where('ref', $bill->ref)->delete();
        updatePurchaseAmount($bill->id);
        return "Deleted";
    }

    public function updateEditQty($id, $qty)
    {
        $item = purchase_details::find($id);
        $item->qty = $qty;
        $item->save();

        $stock = stock::where('product_id', $item->product_id)->where('ref', $item->ref)->first();
        $stock->cr = $qty;
        $stock->save();

        updatePurchaseAmount($item->bill->id);
        return "Qty Updated";
    }
    public function updateEditRate($id, $rate)
    {
        $item = purchase_details::find($id);
        $item->rate = $rate;
        $item->save();
        updatePurchaseAmount($item->bill->id);
        return "Rate Updated";
    }

    public function deletePurchase($ref)
    {
        purchase_details::where('ref', $ref)->delete();
        transactions::where('ref', $ref)->delete();
        stock::where('ref', $ref)->delete();
        purchase::where('ref', $ref)->delete();
        ledger::where('ref', $ref)->delete();
        session()->forget('confirmed_password');
        return redirect('/purchase/history')->with('error', "Purchase Deleted");
    }

    public function stock1()
    {
        $products = products::all();
        $data = [];
        $balance = 0;
        $value = 0;

        foreach ($products as $product) {
            $stock_cr = stock::where('product_id', $product->id)->sum('cr');
            $stock_db = stock::where('product_id', $product->id)->sum('db');
            $balance = $stock_cr - $stock_db;
            $value = $balance * $product->price;

            $data[] = ['product' => $product->name, 'balance' => $balance, 'size' => $product->size, 'value' => $value, 'price' => $product->price];
        }

        return view('purchase.stock')->with(compact('data'));
    }
}
