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
            ->where('vk_id', (int) $id)
            ->first();

        $messages = DB::connection('mongodb')
            ->collection('messages')
            ->where('data.user_id', (int) $id)
            ->orderBy('data.date', 'desc')
            ->paginate(50);

        return response()
            ->json([
                'user' => $user,
                'messages' => $messages
            ]);
    }

}
