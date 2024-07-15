<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class Register extends Controller
{

    //Register User
    public function register(Request $request)
    {

        $reg = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'number_phone' => 'required|unique:users,number_phone',
            'password' => 'required|confirmed|min:8|max:20'
        ]);
        if ($reg->fails()) {
            return response()->json([
                "status : " => 0,
                "message : " => $reg->errors()
            ]);
        } else {

            $newUser = User::create([

                'name' => $request->name,
                'email' => $request->email,
                'number_phone' => $request->number_phone,
                'role' => 'User',
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                "status" => 1,
                "message" => "Register is successfuly",
                // "data" => ch
            ]);
        }
    }
    //Login User
    public function Login(Request $request)
    {


        $reg = Validator::make($request->all(), [

            'email' => 'required',
            'password' => 'required|confirmed|min:8|max:40'
        ]);

        if ($reg->fails()) {
            return response()->json([
                "status : " => 0,
                "message : " => $reg->errors()
            ]);
        } else {
            $userEmail = User::where('email', "=", $request->email)->first();

            if (isset($userEmail->id)) {
                if (Hash::check($request->password, $userEmail->password)) {

                    $token = $userEmail->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        "status :" => 1,
                        "message : " => "Login is successfuly",
                        "Token = " => $token
                    ]);
                } else {
                    return response()->json([
                        "status : " => 0,
                        "Message : " => "The password is not True"
                    ]);
                }
            } else {
                return response()->json([
                    "status = " => 0,
                    "message :" => "User not Found"
                ]);
            }
        }
    }
    //Register Admin
    public function registerAdmin(Request $request)
    {

        $reg = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'number_phone' => 'required|unique:users,number_phone',
            'Code' => 'required',
            'password' => 'required|confirmed|min:8|max:20'
            //password_confirmation
            //password
        ]);
        if ($reg->fails()) {
            return response()->json([
                "status : " => 0,
                "message : " => $reg->errors()
            ]);
        } else {

            if (Code::where('code', '=', $request->Code)->first()) {

                $Admin = User::create([

                    'name' => $request->name,
                    'email' => $request->email,
                    'number_phone' => $request->number_phone,
                    'role' => 'Admin',
                    'password' => Hash::make($request->password),

                ]);
                return response()->json([
                    "status" => 1,
                    "message" => "Register is successfuly",
                    "data" => $Admin
                ]);
            } else {
                return response()->json([
                    "status" => 0,
                    "message" => "Register is not successfuly beacuse code is valid",

                ]);
            }
        }
    }
    public function Logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([

            "status :" => 1,
            "message : " => "User logout successfuly"
        ]);
    }
    public function profile()
    {
        return response()->json([
            "status" => 1,
            "message" => "User Profile information",
            "data" => auth()->user()
        ]);
    }
    public function editProfile(Request $request)
    {

        $userId = auth()->user()->id;

        $find = user::find($userId);

        if ($find) {
            $check = User::where('email', $request->email)
                ->orwhere('number_phone', $request->number_phone)->first();

            if ($check) {
                return response()->json([
                    'message' => 'The Email is Required or number Phone'
                ], 404);
            } else {
                $find->name = !empty($request->name) ? $request->name : $find->name;
                $find->email = !empty($request->email) ? $request->email : $find->email;
                $find->number_phone = !empty($request->number_phone) ? $request->number_phone : $find->number_phone;
                $find->save();
                return response()->json([
                    'status' => 1,
                    'message ' => 'The Update is successfully',
                    'data' => $find
                ]);
            }
        } else {
            return response()->json([
                'status' => 0,
                'message ' => 'user is not found'
            ], 404);
        }
    }
    public function deletUser($id)
    {

        $find = User::find($id);
        if ($find) {

            $find->delete();

            return response()->json([
                'status ' => 1,
                'message ' => 'Delete User is successfully'
            ]);
        } else {
            return response()->json([
                'status ' => 0,
                'message ' => 'User is not Found!'
            ], 404);
        }
    }
    public function showUsers()
    {

        $getUsers = User::where('role', '=', 'User')->get();
        if ($getUsers) {
            return response()->json([
                'status ' => 1,
                'data' => $getUsers
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'User not Fond'
            ]);
        }
    }
}
