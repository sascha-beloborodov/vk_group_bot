<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UsersController extends AppBaseController
{
    use Helper;

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
        $query = DB::connection('mongodb')
            ->collection('messages')
            ->where('data.user_id', (int)$id)
            ->orWhere('data.to_user_id', (int)$id)
            ->orderBy('data.date', 'desc');

        if ($request->get('type')) {
            $query = $query->where('data.type', $request->get('type'));
        }
        $totalMessages = $query->count();
        $cloneQuery = clone  $query;
        $unreadMessages = $cloneQuery->where('unread', 1)->count();

        $messages = $query->paginate($request->get('per_page', self::DEFAULT_PER_PAGE));

        $messageItems = $messages->items();
        $ids = [];

        $this->markNewestMessages($messageItems);

        $swapMessages = $messages->map(function(&$item) use ($messageItems) {
            foreach ($messageItems as $messageItem) {
                if ((string) $item['_id'] == (string) $messageItem['_id']) {
                    $item['is_new'] = $messageItem['is_new'];
                    return $item;
                }
            }
            return $item;
        });

        $messages = new LengthAwarePaginator(
            $swapMessages,
            $messages->total(),
            $messages->perPage(),
            $messages->currentPage()
        );

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
                'messages' => $messages,
                'totalMessages' => $totalMessages,
                'unreadMessages' => $unreadMessages,
            ]);
    }

    public function users(Request $request)
    {
        $query = DB::connection('mongodb')
            ->collection('vk_users')
            ->orderBy('created_at', -1);

        if ($request->get('type')) {
            $query = $query->where('data.type', $request->get('type'));
        }

        $users = $query->paginate($request->get('per_page', 100));

        $userItems = $users->items();

        $swapUser = $users->map(function(&$user) {
            $user['attempts'] = DB
                ::connection('mongodb')
                ->collection('faq_attempts')
                ->where('vk_id', $user['vk_id'])
                ->first();

            $user['lastMessage'] = DB
                ::connection('mongodb')
                ->collection('messages')
                ->where('data.user_id', $user['vk_id'])
                ->orderBy('created_at', -1)
                ->first();

            return $user;
        });

        $swapUser = $swapUser->sortByDesc('lastMessage.created_at')->values()->all();

        $users = new LengthAwarePaginator(
            $swapUser,
            $users->total(),
            $users->perPage(),
            $users->currentPage()
        );

        return response()
            ->json([
                'users' => $users
            ]);
    }

}
