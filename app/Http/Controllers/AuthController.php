<?php

namespace App\Http\Controllers;
use App\Models\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','forgotPassword','otpSend','otpVerification','setUsernamePassword']]);
    }
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'password' => 'required',
                'username' => 'required',
            ]);
            $token = auth()->attempt([
                'username' => request('username'), 
                'password' => request('password'), 
                'otp_verified_status' => 1
            ]);
            if (!$token) {
                return response()->json(['status' => 'error', 'message' => "Login Faild"]);
            }else{
                $data=$this->respondWithToken($token);
                return response()->json(['status' => 'success','data'=>$data ,'message' => "Login Success"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    public function me()
    {
        return response()->json(auth()->user());
    }
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
   

    public function forgotPassword(Request $request)
    {
        try {
            $this->validate($request, [
                'userid' => 'required',
                'new_password' => 'required|min:6',
                'confirm_password' => 'required|same:new_password'
            ]);
            $user=User::where('company_id',request('userid'))->where('otp_verified_status',1)->first();
            if($user){
                try {
                    DB::beginTransaction();
                    if ((Hash::check(request('new_password'), $user->password)) == true) {
                        return response()->json(['status' => 'error', 'message' => "Please enter a password which is not similar then current password."]);
                    }
                    User::where('company_id', request('userid'))
                        ->update([
                            'password' => Hash::make(request('new_password'))
                        ]);
                        DB::commit();
                        return response()->json(['status' => 'success',  'message' => "Password updated successfully."]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
                    }
                    
            }else{
                return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function otpSend(Request $request)
    {
        try {
            $this->validate($request, [
                'mobile' => 'required',
            ]);
            if(config('app.env')=="local"){
                $otp=1234;
            }else{
                $otp=rand(1000,9999);
            }
            if(User::where('contact_number',request('mobile'))->first()){
                try {
                    DB::beginTransaction();
                    $user = User::where('contact_number',request('mobile'))->first();
                    $user->otp = $otp;
                    $user->otp_send_at = Carbon::now();
                    if ($user->save()) {
                        DB::commit();
                        return response()->json(['status' => 'success', 'message' => "OTP send successfully"]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => "Mobile Number Doesnot exist"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function otpVerification(Request $request)
    {
        try {
            $this->validate($request, [
                'mobile' => 'required',
                'otp' => 'required',
            ]);
            if(User::where('contact_number',request('mobile'))->where('otp',request('otp'))->first()){
                $user = User::where('contact_number',request('mobile'))->where('otp',request('otp'))->first();
                
                $startTime = Carbon::parse($user->otp_send_at);
                $finishTime = Carbon::now();
                $totalDuration = $finishTime->diffInSeconds($startTime);
                if($totalDuration<60){
                    try {
                        DB::beginTransaction();
                        $user->otp_verified_status =1;
                        $user->otp_verified_at = Carbon::now();
                        $user->save();

                        if ($user->save()) {
                            DB::commit();
                            return response()->json(['status' => 'success','userid'=>$user->company_id, 'message' => "OTP Verified successfully"]);
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
                    }
                }else{
                    return response()->json(['status' => 'error', 'message' => "OTP expaired"]);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => "OTP not Verified"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function setUsernamePassword(Request $request)
    {
        try {
            $this->validate($request, [
                'userid' => 'required',
                'new_password' => 'required|min:6',
                'confirm_password' => 'required|same:new_password'
            ]);
            $user=User::where('company_id',request('userid'))->where('otp_verified_status',1)->first();
            if($user){
                try {
                    DB::beginTransaction();
                    User::where('company_id', request('userid'))
                        ->update([
                            'password' => Hash::make(request('new_password')),
                            'username' => $user->contact_number,
                            'status' => 1
                        ]);
                        DB::commit();
                        return response()->json(['status' => 'success',  'message' => "Password updated successfully."]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
                    }
                    
            }else{
                return response()->json(['status' => 'error', 'message' => "Something went wrong"]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
