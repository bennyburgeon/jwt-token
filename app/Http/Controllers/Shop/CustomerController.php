<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customer =  Customer::get();
        $array=[
            'status'=>true,
            'message'=>"Successfully fetched.",
            'data'=>$customer,
        ];
        return response()->json($array);die;
    }

    public function store(Request $request)
    {
        try {
            $rules = array(
                'name'      => 'required',
                'mobile_no' => 'required|numeric',
                'email'     => 'required|email',
                'address'   => 'required',
                'status'    => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()){
                $array=[
                    'status'=>false,
                    'message'=>$validator->errors()->first(),
                    'data'=>[],
                ];
                return response()->json($array);die;
            }     
            $customer=new Customer;
            $customer->name=$request->name;
            $customer->mobile_no=$request->mobile_no;
            $customer->email=$request->email;
            $customer->address=$request->address;
            $customer->status=$request->status;
            if($customer->save()){
                $array=[
                    'status'=>true,
                    'message'=>"Customer has been saved successfully.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }else{
                $array=[
                    'status'=>false,
                    'message'=>"Sorry, Something went wrong. Please try again.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }
        } catch (\Exception $e) {
            $array=[
                    'status'=>false,
                    'message'=>"Sorry, Something went wrong. Please try again.",
                    'data'=>[],
                ];
                return response()->json($array);die;
        }
    }
    
    public function update(Request $request)
    {
        try {
            $rules = array(
                'name'      => 'required',
                'mobile_no' => 'required|numeric',
                'email'     => 'required|email',
                'address'   => 'required',
                'status'    => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()){
                $array=[
                    'status'=>false,
                    'message'=>$validator->errors()->first(),
                    'data'=>[],
                ];
                return response()->json($array);die;
            }     
            $id=$request->id ?? 0;
            $customer=Customer::where('id',$id)->first();
            if(!$customer){
                $array=[
                    'status'=>false,
                    'message'=>"Sorry, This customer does not exist.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }
            $customer->name=$request->name;
            $customer->mobile_no=$request->mobile_no;
            $customer->email=$request->email;
            $customer->address=$request->address;
            $customer->status=$request->status;
            if($customer->save()){
                $array=[
                    'status'=>true,
                    'message'=>"Customer has been update successfully.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }else{
                $array=[
                    'status'=>false,
                    'message'=>"Sorry, Something went wrong. Please try again.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }
        } catch (\Exception $e) {
            $array=[
                    'status'=>false,
                    'message'=>"Sorry, Something went wrong. Please try again.",
                    'data'=>[],
                ];
                return response()->json($array);die;
        }
    }

    public function destroy(Request $request)
    {
        try{
            $id=$request->id ?? 0;
            $customer=Customer::where('id',$id)->first();
            if(!$customer){
                $array=[
                    'status'=>false,
                    'message'=>"Sorry, This customer does not exist.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }else{
                if($customer->delete()){
                    $array=[
                        'status'=>true,
                        'message'=>"Customer has been deleted successfully.",
                        'data'=>[],
                    ];
                    return response()->json($array);die;
                }else{
                    $array=[
                        'status'=>false,
                        'message'=>"Sorry, Something went wrong. Please try again.",
                        'data'=>[],
                    ];
                    return response()->json($array);die;
                }
            }
        }catch (\Exception $e) {
            $array=[
                    'status'=>false,
                    'message'=>"Sorry, Something went wrong. Please try again.",
                    'data'=>[],
                ];
                return response()->json($array);die;
        }
    }


}
