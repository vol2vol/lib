<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user()->load('role'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'login' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->has('login')) {
            $user->login = $request->login;
        }


        if ($request->has('password')) {
            $user->password = $request->password;
        }
        $user->save();

        return response()->json([
            'message' => 'Профиль успешно обновлён',
            'user' => $user->load('role')
        ]);
    }
}
