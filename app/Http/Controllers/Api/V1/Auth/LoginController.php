<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isNull;

class LoginController extends Controller
{

    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (isNull($user)) {
            return response()->json(
                [
                    'error' => 'The Provided credentials are incorrect'
                ],
                422
            );
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'error' => 'The Provided credentials are incorrect'
                ],
                422
            );
        }
        $device = substr($request->userAgent() ?? '', 0, 255);

        $token = $user->createToken($device)->plainTextToken;

        return response()->json([
            'access_token' => $token
        ]);
    }
}
