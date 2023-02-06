<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
    
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'product_name' => 'required',
                'product_code' => 'required',
                'description' => 'required',
                'price' => 'required',
                'cgst' => 'required',
                'sgst' => 'required'
            ]);
            try {
                DB::beginTransaction();
                $product = new Product();
                $product->company_id = auth()->user()->company_id;
                $product->product_name = $request->product_name;
                $product->product_code = $request->product_code;
                $product->description = $request->description;
                $product->price = $request->price;
                $product->cgst = $request->cgst;
                $product->sgst = $request->sgst;

                if ($product->save()) {
                    DB::commit();
                    return response()->json(['status' => 'success', 'message' => "Product registered successfully"]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function list()
    {
        try {
            $limit = request('limit') ? request('limit') : 10;
            if (auth()->user()->is_admin==0) {
                $data =  Product::where('company_id',auth()->user()->company_id)->where('status',1);
                if(request('name')){
                    $name=request('name');
                    $data =$data->where('product_name', 'LIKE', "%{$name}%");
                }
                if(request('pcode')){
                    $pcode=request('pcode');
                    $data =$data->where('product_code',$pcode);
                }
                $data = $data->paginate($limit);
                return response()->json(['status' => 'success', 'data' => $data,'message' => "Product List"]);
            }else{
                $data =  Product::where('status',1);
                if(request('name')){
                    $name=request('name');
                    $data =$data->where('product_name', 'LIKE', "%{$name}%");
                }
                if(request('pcode')){
                    $pcode=request('pcode');
                    $data =$data->where('product_code',$pcode);
                }
                $data = $data->paginate($limit);;
                return response()->json(['status' => 'success', 'data' => $data,'message' => "Product List"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $data =  Product::where('product_id',$id)->where('company_id',auth()->user()->company_id)->first();
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Company Details"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
        
    }

   
    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'product_name' => 'required',
                'product_code' => 'required',
                'description' => 'required',
                'price' => 'required',
                'cgst' => 'required',
                'sgst' => 'required'
            ]);
            try {
                DB::beginTransaction();
                $product = Product::findOrFail($request->id);
                $product->product_name = $request->product_name;
                $product->product_code = $request->product_code;
                $product->description = $request->description;
                $product->price = $request->price;
                $product->cgst = $request->cgst;
                $product->sgst = $request->sgst;
            if ($product->save()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Product updated successfully']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
    }
    public function delete(Request $request)
    {
        try {
            if(Product::find($request->id)){
                Product::where('product_id',$request->id)->where('company_id',auth()->user()->company_id)->delete();
                return response()->json(['status' => 'success', 'message' => "Product Deleted Successfully"]);
            }else{
                return response()->json(['status' => 'error', 'message' => "Product details Not Found"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }

}
