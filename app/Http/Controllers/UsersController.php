<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends AppBaseController
{
    const DEFAULT_PER_PAGE = 100;

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
            ->paginate($request->get('per_page', self::DEFAULT_PER_PAGE));


        $messageItems = $messages->items();
        $ids = [];
        foreach ($messageItems as &$messageItem) {
            if ($messageItem['unread'] == 1) {
                $_id = $messageItem['_id'];
                $ids[] = (string) $_id;
                $messageItem['is_new'] = 1;
            } else {
                $messageItem['is_new'] = 0;
            }
        }

        $messages->each(function($item) use ($messageItems) {
            foreach ($messageItems as $messageItem) {
                if ((string) $item['_id'] == (string) $messageItem['_id']) {
                    $item['is_new'] = $messageItem['is_new'];
                }
            }
        });

        if (!empty($ids)) {
            DB
                ::connection('mongodb')
                ->collection('messages')
                ->whereIn('_id', $ids)
                ->update([
                    'unread' => 0
                ]);
        }


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
