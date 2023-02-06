<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product =  Product::get();
        $array=[
            'status'=>true,
            'message'=>"Successfully fetched.",
            'data'=>$product,
        ];
        return response()->json($array);die;
    }

    public function store(Request $request)
    {
        try {
            $rules = array(
                'code'      => 'required',
                'name'      => 'required',
                'price'     => 'required',
                'tax'       => 'required',
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
            $product=Product::where('code',$request->code)->first();
            if($product){
                $array=[
                    'status'=>false,
                    'message'=>"Sorry, This product code is already exist.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }
            $product=new Product;
            $product->code=$request->code;
            $product->name=$request->name;
            $product->description=$request->description;
            $product->price=$request->price;
            $product->tax=$request->tax;
            $product->status=$request->status;
            if($product->save()){
                $array=[
                    'status'=>true,
                    'message'=>"Product has been saved successfully.",
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
                'code'      => 'required',
                'name'      => 'required',
                'price'     => 'required',
                'tax'       => 'required',
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
            $product=Product::where('code',$request->code)->where('id','!=',$id)->first();
            if($product){
                $array=[
                    'status'=>false,
                    'message'=>"Sorry, This product code is already exist.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }
            $product=Product::where('id',$id)->first();
            if(!$product){
                $array=[
                    'status'=>false,
                    'message'=>"Sorry, This product does not exist.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }
            $product->code=$request->code;
            $product->name=$request->name;
            $product->description=$request->description;
            $product->price=$request->price;
            $product->tax=$request->tax;
            $product->status=$request->status;
            if($product->save()){
                $array=[
                    'status'=>true,
                    'message'=>"Product has been update successfully.",
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
            $product=Product::where('id',$id)->first();
            if(!$product){
                $array=[
                    'status'=>false,
                    'message'=>"Sorry, This product does not exist.",
                    'data'=>[],
                ];
                return response()->json($array);die;
            }else{
                if($product->delete()){
                    $array=[
                        'status'=>true,
                        'message'=>"Product has been deleted successfully.",
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
