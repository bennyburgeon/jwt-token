<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:customers',
                'mobile' => 'required',
                'status' => 'required',
                'address' => 'required'
            ]);
            try {
                DB::beginTransaction();
            $customer = new Customer();
            $customer->company_id = auth()->user()->company_id;
            $customer->customer_name = $request->name;
            $customer->contact_number = $request->mobile;
            $customer->email = $request->email;
            $customer->status = $request->status;
            $customer->address = $request->address;
            
            if ($customer->save()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => "Customer registered successfully"]);
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
            $data =  Customer::where('company_id',auth()->user()->company_id)->where('status',1);
            if(request('name')){
                $name=request('name');
                $data =$data->where('customer_name', 'LIKE', "%{$name}%");
            }
            if(request('mobile')){
                $mobile=request('mobile');
                $data =$data->where('contact_number',$mobile);
            }
            $data = $data->paginate($limit);
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Customer List"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $data =  Customer::where('customer_id',$id)->first();
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Customer Details"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
        
    }

   
    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:customers,email,'.$request->id.',customer_id',
                'mobile' => 'required',
                'status' => 'required',
                'address' => 'required'
            ]);
            try {
                DB::beginTransaction();
            $customer = Customer::findOrFail($request->id);
            $customer->customer_name = $request->name;
            $customer->contact_number = $request->mobile;
            $customer->email = $request->email;
            $customer->status = $request->status;
            $customer->address = $request->address;
            if ($customer->save()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Customer updated successfully']);
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
            if(Customer::find($request->id)){
                Customer::find($request->id)->delete();
                return response()->json(['status' => 'success', 'message' => "Customer Deleted Successfully"]);
            }else{
                return response()->json(['status' => 'error', 'message' => "Customer details Not Found"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }

}
