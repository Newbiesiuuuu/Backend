<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Helpers\params;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(protected ImageHelper $imageHelper) {
    }
    /** 現在ユーザー */
    public function index(Request $request) {
        return $request->user();
    }
    /** 特定ユーザー */
    public function show($id) {
        try {
            User::where('id', $id)->firstOrFail();
        } catch(ModelNotFoundException $e) {
            $errMsg = $e->getMessage();
            return response()->json(['error' => $errMsg], 500);
        }
    }

    /** アップデート */
    public function update(Request $request) {
        try {
            $request->validate([
                'id' => 'required',
                'nickname' => 'filled',
                'password' => 'filled', 
                'profileImage' => 'filled',
            ]);
        } catch(ValidationException $e) {
            $errMsg = $e->errors();
            $status = $e->status;
            return response()->json(['error' => $errMsg], $status);
        }
        // userId
        $userId = $request->id;
        // update
        // 예 외 처 리 추 가 해 야 함   
            // update Nickname
            if (isset($request->nickname)) {
                $nickname = User::where('id', $userId)->update(['nickname' => $request->nickname]);
                if(!$nickname) {
                    return response()->json(['error' => 'Failed to update nickname'], 500);
                }
            }   
            // update Password
            if (isset($request->password)) {
                $password = User::where('id', $userId)->update(['password' => Hash::make($request->password)]);
                if(!$password) {
                    return response()->json(['error' => 'Failed to update password'], 500);
                }
            }
            // update ProfileImage
            if (isset($request->profileImage)) {
                $path = $this->imageHelper->storeImage($request->profileImage, $userId, params::profile);
                if(!$path) {
                    return response()->json(['error' => 'Failed to save image'], 500);
                }
                $profileImage = User::where('id', $userId)->update(['profile_image' => $path]);
                if(!$profileImage) {
                    return response()->json(['error' => 'Failed to update profile image']);
                }
            }
        return response()->json(['message' => 'User updated successfully'], 200);
    }
}
