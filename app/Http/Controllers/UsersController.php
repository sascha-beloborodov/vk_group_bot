<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends AppBaseController
{
    public function userById($id, Request $request)
    {
        $user = DB::connection('mongodb')
            ->collection('vk_users')
            ->where('vk_id', (int)$id)
            ->first();

        return response()
            ->json([
                'user' => $user,
            ]);
    }

    public function usersMessages($id, Request $request)
    {
        $messages = DB::connection('mongodb')
            ->collection('messages')
            ->where('data.user_id', (int)$id)
            ->orWhere('data.to_user_id', (int)$id)
            ->orderBy('data.date', 'desc')
            ->paginate(5);

        return response()
            ->json([
                'messages' => $messages
            ]);
    }

    public function users(Request $request)
    {
        $users = DB::connection('mongodb')
            ->collection('vk_users')
            ->orderBy('created_at', 'desc')
            ->paginate(2);

        return response()
            ->json([
                'users' => $users
            ]);
    }

}
