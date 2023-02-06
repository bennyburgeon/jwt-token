<?php

namespace App\Http\Controllers;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
class InvoiceController extends Controller
{
    
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'customer_id' => 'required',
                'products' => 'required',
                'discount' => 'required'
            ]);
            try {
                DB::beginTransaction();
                $products_quantity=$request->products;
                $products=$products_quantity['product'];
                $quantity=$products_quantity['quantity'];
                $invoice = new Invoice();
                $invoice->customer_id = $request->customer_id;
                $invoice->company_id = auth()->user()->company_id;
                $invoice->description = $request->description;
                $invoice->discount = $request->discount;
                
                if ($invoice->save()) {
                    $datas =  Product::whereIn('product_id', $products)->get();
                    $price=0;
                    foreach ($products as $product) {
                        foreach ($datas as $data) {
                            $sgst=0;
                            $cgst=0;
                            $total=0;
                            if($data->product_id==$product){
                                $invoice_details = new InvoiceDetail();
                                $invoice_details->invoice_id = $invoice->invoice_id;
                                $invoice_details->product_id = $data->product_id;
                                $invoice_details->price = $data->price;
                                $invoice_details->quantity = $data->quantity;
                                $invoice_details->cgst = $data->cgst;
                                $invoice_details->sgst = $data->sgst;
                                $cgst=($data->price*$data->cgst/100);
                                $sgst=($data->price*$data->sgst/100);
                                $total=($data->price+$cgst+$sgst)*$data->quantity;
                                $price+=$total;
                                $invoice_details->total = $total;
                                $invoice_details->save();
                            }
                        }
                    }
                    $invoice_update = Invoice::findOrFail($invoice->invoice_id);
                    $invoice_update->price = $price;
                    $invoice_update->total = ($price-$request->discount);
                    $invoice_update->save();
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => "Invoice added successfully"]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => "Something1 went wrong"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function list()
    {
        try {
            $limit = request('limit') ? request('limit') : 10;
            $data =  Invoice::where('company_id',auth()->user()->company_id)->where('status',1)->paginate($limit);
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Invoice List"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $data =  Invoice::with('details')->where('invoice_id',$id)->first();
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Invoice Details"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
        
    }
    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required',
                'customer_id' => 'required',
                'products' => 'required',
                'discount' => 'required'
            ]);
                $invoice = Invoice::findOrFail($request->id);
                $products=$request->products;
                $datas =  Product::whereIn('product_id', $products)->get();
                InvoiceDetail::where('invoice_id', $request->id)->delete();
                $price=0;
                $sgst=0;
                $cgst=0;
                foreach ($products as $product) {
                    foreach ($datas as $data) {
                        if($data->product_id==$product){
                            $invoice_details = new InvoiceDetail();
                            $invoice_details->invoice_id = $invoice->invoice_id;
                            $invoice_details->product_id = $data->product_id;
                            $invoice_details->price = $data->price;
                            $invoice_details->cgst = $data->cgst;
                            $invoice_details->sgst = $data->sgst;
                            $cgst+=$data->cgst;
                            $sgst+=$data->sgst;
                            $price+=($data->price +$cgst+$sgst);
                            $invoice_details->save();
                        }
                    }
                }

                $invoice->customer_id = $request->customer_id;
                $invoice->company_id = auth()->user()->company_id;
                $invoice->description = $request->description;
                $invoice->discount = $request->discount;
                $invoice->price = $price;
                $invoice->total = ($price-$request->discount);
            if ($invoice->save()) {
                return response()->json(['status' => 'success', 'message' => 'Invoice updated successfully']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }
    public function delete(Request $request)
    {
        try {
            if(Invoice::find($request->id)){
                if(InvoiceDetail::where('invoice_id', $request->id)->get()){
                    InvoiceDetail::where('invoice_id', $request->id)->delete();
                }
                Invoice::find($request->id)->delete();
                return response()->json(['status' => 'success', 'message' => "Invoice Deleted Successfully"]);
            }else{
                return response()->json(['status' => 'error', 'message' => "Invoice details Not Found"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }
    public function view_details(Request $request, $id)
    {
        try {
            $data =  InvoiceDetail::where('invoice_id',$id)->get();
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Invoice Details"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
        
    }
    public function exportPdf($id)
    {
        $data['data']=Invoice::with('details','customers','details.products')->where('invoice_id',$id)->first();
        
        $pdf = Pdf::loadView('pdfExport',$data);
        return $pdf->download('invoice.pdf');
        
    }

}
