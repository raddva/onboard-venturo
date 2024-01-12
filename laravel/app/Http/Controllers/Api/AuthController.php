<?php

namespace App\Http\Controllers\Api;

use App\Helpers\User\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AuthRequest;
use App\Http\Resources\User\UserResource;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        $login       = AuthHelper::login($credentials['email'], $credentials['password']);

        if (!$login['status']) {
            return response()->failed($login['error'], 422);
        }

        return response()->success($login['data']);
    }

    public function profile()
    {
        return response()->success(new UserResource(auth()->user()));
    }
}
