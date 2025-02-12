<?php

namespace App\Http\Controllers\Api;

use App\Utils\Avatar;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helper\ResponseHelper;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(SignUpRequest $request)
    {

        $avatarUtils = new Avatar();

        $avatar = $avatarUtils->getRandomAvatar();

        $input = [
            "username" => $request->username,
            "nickname" => $request->nickname,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "avatar" => $avatar,
            "gender" => $request->gender,
            "birthday" => $request->birthday,
            "bio" => $request->bio,
            "mobile" => $request->mobile,
            "push_token" => $request->push_token,
            "verification" => $request->verification,
            "location_id" => $request->location_id,
            "status" => $request->status,
        ];

        if (isset($request->avatar)) {
            $input["avatar"] = $request->avatar;
        }

        $user = User::create($input);
        $token = auth("api")->login($user);
        $user->update([
            "access_token" => $token
        ]);
        $data = auth('api')->user();
        $data["avatar"] = env('CURRENT_URL') . Storage::url($data["avatar"]);
        $data["access_token"] = $token;

        return ResponseHelper::success($data, "Successfuly register!", "success", 201);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signin()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return ResponseHelper::error('Could not sign in with credentials!', "error", 401);
        }

        $data = auth('api')->user();
        $location = Location::where('id', $data->location_id)->first();
        if (isset($data->location_id)) {
            $data->location = $location->name;
        }

        User::where('email', $data['email'])->update(['access_token' => $token]);

        $data["access_token"] = $token;
        $data["avatar"] = env('CURRENT_URL') . Storage::url($data["avatar"]);

        return ResponseHelper::success($data, "Successfuly login!", "success", 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth('api')->user();
        $location = Location::where('id', $user->location_id)->first();
        if (isset($location)) {
            $user->location = $location->name;
        }
        $user->avatar = env('CURRENT_URL') . Storage::url($user->avatar);
        return response()->json([
            "code" => 1,
            "msg" => "Successfuly",
            "status" => "success",
            "data" => $user
        ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth("api")->logout();
        return ResponseHelper::success([], "Successfully logged out!", "suceess", 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function refresh()
    // {
    //     $token = auth('api')->refresh();
    //     return $this->respondWithToken($token);
    // }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => auth('api')->factory()->getTTL() * 60
    //     ]);
    // }

    public function profile(UpdateUserRequest $request)
    {

        $user = auth("api")->user();

        $data = [];
        if (isset($request->username)) {
            $data["username"] = $request->username;
        }
        if (isset($request->nickname)) {
            $data["nickname"] = $request->nickname;
        }
        if (isset($request->email)) {
            $data["email"] = $request->email;
        }
        if ($request->hasFile('avatar')) {
            // Validate the avatar file
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // max size 2MB
            ]);

            // Store the avatar file
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Save the file path to the database
            $data["avatar"] = $avatarPath;
        }
        if (isset($request->mobile)) {
            $data["mobile"] = $request->mobile;
        }
        if (isset($request->gender)) {
            $data["gender"] = $request->gender;
        }
        if (isset($request->push_token)) {
            $data["push_token"] = $request->push_token;
        }
        if (isset($request->location_id)) {
            $data["location_id"] = $request->location_id;
        }
        if (isset($request->bio)) {
            $data["bio"] = $request->bio;
        }

        User::where('id', $user->id)->update($data);

        return ResponseHelper::success($data, 'Successfully updated profile.', 'success', 200);
    }

    public function profileSetup(Request $request)
    {

        $user = auth("api")->user();

        $data = [];
        if (isset($request->nickname)) {
            $data["nickname"] = $request->nickname;
        }
        if ($request->hasFile('avatar')) {
            // Validate the avatar file
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // max size 2MB
            ]);

            // Store the avatar file
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Save the file path to the database
            $data["avatar"] = $avatarPath;
        }
        if (isset($request->mobile)) {
            $data["mobile"] = $request->mobile;
        }
        if (isset($request->location_id)) {
            $data["location_id"] = $request->location_id;
        }
        if (isset($request->bio)) {
            $data["bio"] = $request->bio;
        }
        if (isset($request->gender)) {
            $data["gender"] = $request->gender;
        }
        if (isset($request->birthday)) {
            $data["birthday"] = $request->birthday;
        }

        User::where('id', $user->id)->update($data);

        return ResponseHelper::success($data, 'Successfully updated profile.', 'success', 200);
    }

    public function check(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'username' => "required|unique:users"
        ]);
        if ($validate->fails()) {
            return ResponseHelper::error("Credentials already exists.", "error", 400);
        }
        return ResponseHelper::success([], "Credentials is available.", "success", 200);
    }
}
