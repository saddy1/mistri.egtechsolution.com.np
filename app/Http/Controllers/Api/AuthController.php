<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Google_Client;


class AuthController extends Controller
{

public function googleLogin(Request $request)
{
    $request->validate([
        'id_token' => 'required',
        'device_id' => 'required',
    ]);

    // ✅ Verify Google Token
    $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);

    $payload = $client->verifyIdToken($request->id_token);

    if (!$payload) {
        return response()->json(['message' => 'Invalid Google token'], 401);
    }

    // ✅ Extract real email + name
    $email = $payload['email'];
    $name = $payload['name'] ?? 'User';

    $user = User::firstOrCreate(
        ['email' => $email],
        ['name' => $name]
    );

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user,
    ]);
}




    /*
    |--------------------------------------------------------------------------
    | SEND OTP
    |--------------------------------------------------------------------------
    */
public function me(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ],401);
    }

    return response()->json([
        'success' => true,
        'user' => $user,
        'profile_image_url' =>
            $user->profile_image
            ? asset($user->profile_image)
            : null
    ]);
}
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'device_id' => 'required'
        ]);

        // check existing user
        $user = User::where(
            'phone',
            $request->phone
        )->first();

        /*
        |--------------------------------------------------------------------------
        | SAME DEVICE LOGIN (NO OTP)
        |--------------------------------------------------------------------------
        */

        if ($user && $user->device_id == $request->device_id) {

            $token = $user
                ->createToken('auth_token')
                ->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login success (same device)',
                'token' => $token,
                'user' => $user
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE OTP
        |--------------------------------------------------------------------------
        */

        $otp = rand(1000, 9999);

        // remove old otp
        Otp::where('phone', $request->phone)->delete();

        // save otp
        Otp::create([
            'phone' => $request->phone,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP generated',
            'otp' => $otp // testing only
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFY OTP
    |--------------------------------------------------------------------------
    */

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required',
            'device_id' => 'required'
        ]);

        // find otp
        $otpRecord = Otp::where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // expiry check
        if (now()->greaterThan($otpRecord->expires_at)) {

            $otpRecord->delete();

            return response()->json([
                'success' => false,
                'message' => 'OTP expired'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | LOGIN OR REGISTER USER
        |--------------------------------------------------------------------------
        */

        $user = User::where(
            'phone',
            $request->phone
        )->first();

        // new user
        if (!$user) {

            $user = User::create([
                'phone' => $request->phone,
                'device_id' => $request->device_id,
                'is_verified' => true
            ]);
        } else {

            // update device
            $user->device_id = $request->device_id;
            $user->is_verified = true;
            $user->save();
        }

        // delete otp
        $otpRecord->delete();

        // create login token
        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }
    /*
|--------------------------------------------------------------------------
| COMPLETE PROFILE
|--------------------------------------------------------------------------
*/

public function completeProfile(Request $request)
{
    try {
    $validated = $request->validate([
        'name' => 'nullable|string',
        'email' => 'nullable|email',
        'address' => 'required|string',
        'profile_image' => 'nullable|image|max:2048',
        'gender' => 'nullable|string',
        'phone' => 'nullable|string',
    ]);
} catch (\Illuminate\Validation\ValidationException $e) {
    return response()->json([
        'success' => false,
        'errors' => $e->errors()
    ], 422);
}

    $user = $request->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ],401);
    }

    /*
    |--------------------------------------------------------------------------
    | IMAGE UPLOAD
    |--------------------------------------------------------------------------
    */

    if ($request->hasFile('profile_image')) {

        $path = public_path('profile');

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $image = $request->file('profile_image');

        $imageName =
            uniqid().'_profile.'.
            $image->getClientOriginalExtension();

        $image->move($path, $imageName);

        $user->profile_image =
            'profile/'.$imageName;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

$user->name = $request->name;
$user->address = $request->address;
$user->gender = $request->gender; // ✅ ADD THIS
$user->phone = $request->phone;
$user->profile_completed = true;


    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => $user
    ]);
}
    
}