<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'contact_number' => 'required',
                'address' => 'required',
                'gst_no' => 'required'
            ]);
            try {
                DB::beginTransaction();
            $user = new User();
            $user->company_name = $request->name;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->contact_number = $request->contact_number;
            $user->gst_no = $request->gst_no;

            if ($user->save()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => "Company registered successfully"]);
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
            $data =  User::where('is_admin',0)->where('status',1);
            if(request('company_name'))
            {
                $company_name=request('company_name');
                $data =$data->where('company_name', 'LIKE', "%{$company_name}%");
            }
            $data =$data->paginate($limit);
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Company List"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }
    public function view(Request $request, $id)
    {
        try {
            $data =  User::where('is_admin',0)->where('company_id',$id)->first();
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Company Details"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
        
    }

   
    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'username' => 'required',
                'contact_number' => 'required',
                'address' => 'required',
                'gst_no' => 'required'
            ]);
            try {
                DB::beginTransaction();
                $users = User::findOrFail($request->id);
                $users->company_name = $request->name;
                $users->username = $request->username;
                $users->email = $request->email;
                $users->address = $request->address;
                $users->contact_number = $request->contact_number;
                $users->gst_no = $request->gst_no;
            if ($users->save()) {
                DB::rollBack();
                return response()->json(['status' => 'success', 'message' => 'Company updated successfully']);
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
            if(User::find($request->id)){
                User::find($request->id)->delete();
                return response()->json(['status' => 'success', 'message' => "Company Deleted Successfully"]);
            }else{
                return response()->json(['status' => 'error', 'message' => "Company details Not Found"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }

    
    public function dashboard(Request $request)
    {
        try {
            if(auth()->user()->is_admin==0){
                $data['products']=Product::where('company_id',auth()->user()->company_id)->count();
                $data['customers']=Customer::where('company_id',auth()->user()->company_id)->count();
                $data['invoices']=Invoice::where('company_id',auth()->user()->company_id)->count();
                return response()->json(['status' => 'success','data' => $data, 'message' => "Company dashboard"]);
            }else{
                $data['products']=Product::count();
                $data['customers']=Customer::count();
                $data['invoices']=Invoice::count();
                $data['companies']=User::count();
                return response()->json(['status' => 'success','data' => $data, 'message' => "Company dashboard"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    }
    public function profileView()
    {
        try {
            $data =  User::where('is_admin',0)->where('company_id',auth()->user()->company_id)->first();
            return response()->json(['status' => 'success', 'data' => $data,'message' => "Profile Details"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
        
    }
    public function profileEdit(Request $request)
    {
        try {
                $this->validate($request, [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'username' => 'required',
                    'address' => 'required',
                    'gst_no' => 'required'
                ]);
                try {
                    DB::beginTransaction();
                $users = User::findOrFail($request->id);
                $users->company_name = $request->name;
                $users->username = $request->username;
                $users->email = $request->email;
                $users->address = $request->address;
                $users->gst_no = $request->gst_no;
            if ($users->save()) {
                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Company updated successfully']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
        }
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
    }
    public function change_password(Request $request)
    {
        $input = $request->all();
        $userid = auth()->user()->company_id;
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);
            try {
                if ((Hash::check(request('old_password'), auth()->user()->password)) == false) {
                    return response()->json(['status' => 'error', 'message' => "Check your old password."]);
                } else if ((Hash::check(request('new_password'), auth()->user()->password)) == true) {
                    return response()->json(['status' => 'error', 'message' => "Please enter a password which is not similar then current password."]);
                } else {
                    User::where('company_id', $userid)->update(['password' => Hash::make(request('new_password'))]);
                    return response()->json(['status' => 'success',  'message' => "Password updated successfully."]);
                }
            } catch (\Exception $ex) {
               
                return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
            }
        
        
    }
}
