<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(20);
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with('role')->where('user_id', $id)->firstOrFail();
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        if ($user->user_id === auth()->id()) {
            return response()->json(['message' => 'Нельзя редактировать самого себя'], 403);
        }

        if ($user->role && $user->role->slug === 'admin') {
            return response()->json(['message' => 'Нельзя редактировать администратора'], 403);
        }

        if ($request->has('role_id')) {
            $user->role_id = $request->role_id;
        }

        if ($request->has('login')) {
            $user->login = $request->login;
        }

        if ($request->has('password')) {
            $user->password = $request->password;
        }

        $user->save();

        return response()->json([
            'message' => 'Пользователь успешно обновлён',
            'user' => $user->load('role')
        ]);
    }

    public function destroy($id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        if ($user->user_id === auth()->id()) {
            return response()->json(['message' => 'Нельзя удалить самого себя'], 403);
        }

        if ($user->role && $user->role->slug === 'admin') {
            return response()->json(['message' => 'Нельзя удалить администратора'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Пользователь успешно удалён']);
    }
}
