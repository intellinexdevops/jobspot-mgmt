<?php

namespace App\Http\Controllers;

use App\Utils\Avatar;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helper\ResponseHelper;
use App\Http\Requests\UpdateUserRequest;

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
            "gender"=> $request->gender,
            "birthday"=> $request->birthday,
            "bio"=> $request->bio,
            "mobile"=> $request->mobile,
            "push_token"=>$request->push_token,
            "verification"=>$request->verification,
            "location_id"=>$request->location_id,
            "status"=> $request->status,
        ];

        if(isset($request->avatar)){
            $input["avatar"] = $request->avatar;
        }

        $user = User::create($input);
        $token = auth("api")->login($user);
        $user->update([
            "access_token" => $token
        ]);
        $data = auth('api')->user();
        $data["access_token"] = $token;
        $data["token_type"] = "bearer";
        $data["expires_in"] = auth('api')->factory()->getTTL() * 60 * 7;

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

        User::where('email', $data['email'])->update(['access_token'=>$token]);

        $data["access_token"] = $token;
        $data["token_type"] = "bearer";
        $data["expires_in"] = auth('api')->factory()->getTTL() * 60 * 7;        

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
        return response()->json([
            "code" => 1,
            "msg" => "Successfuly",
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

    public function profile(UpdateUserRequest $request){
        
        $user = auth("api")->user();

        $data = [];
        if(isset($request->username)){
            $data["username"] = $request->username;
        }
        if(isset($request->email)){
            $data["email"] = $request->email;
        }
        if(isset($request->avatar)){
            $data["avatar"] = $request->avatar;
        }
        if(isset($request->mobile)){
            $data["mobile"] = $request->mobile;
        }
        if(isset($request->gender)){
            $data["gender"] = $request->gender;
        }

        User::where('id', $user->id)->update($data);

        return ResponseHelper::success($data, 'Successfully updated profile.', 'success',200);

    }
}
