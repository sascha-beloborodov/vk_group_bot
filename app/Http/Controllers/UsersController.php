<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UsersController extends AppBaseController
{
    use Helper;

    const DEFAULT_PER_PAGE = 100;
    const SECTION_SUBSCRIBERS = 'subscribers';

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

        $type = $request->get('type');

        $users = $query->paginate($request->get('per_page', 50));
        // $limit = 50;
        // $offset = 0;
        // $users = $query->skip($offset)->take($limit)->get();

        // $userItems = $users->items();
        // $swapUser = new \Illuminate\Support\Collection;
        // while($users->count()) {
            $swapUser = $users->reduce(function($prev, &$user) use ($type, $request) {
                $user['attempts'] = DB
                    ::connection('mongodb')
                    ->collection('faq_attempts')
                    ->where('vk_id', (int) $user['vk_id'])
                    ->first();
    
                $user['lastMessage'] = DB
                    ::connection('mongodb')
                    ->collection('messages')
                    ->where('data.user_id', (int) $user['vk_id'])
                    ->orderBy('created_at', -1)
                    ->first();
    
                $user['activities'] = DB
                    ::connection('mongodb')
                    ->collection('activities')
                    ->where('vk_id', (int) $user['vk_id'])
                    ->get();
    
                if ($type == self::SECTION_SUBSCRIBERS) {
                    $user['subs'] = DB
                        ::connection('mongodb')
                        ->collection('subscribers')
                        ->where('vk_id', $user['vk_id'])
                        ->orderBy('created_at', -1)
                        ->get(['city']);
                }
    
                if ((int) $request->get('cityId')) {
                    $user['subs'] = DB
                        ::connection('mongodb')
                        ->collection('subscribers')
                        ->where('vk_id', (int) $user['vk_id'])
                        ->where('city_id', (int) $request->get('cityId'))
                        ->orderBy('created_at', -1)
                        ->get(['city']);
                    if (!empty($user['subs'])) {
                        $prev->push($user);
                    }
                    return $prev;
                } else {
                    $prev->push($user);
                    return $prev;
                }

            }, new \Illuminate\Support\Collection);
            // $swapUser->concat($swappedUser);
            // $offset += 50;
            // $users = $query->skip($offset)->take($limit)->get();
        // }


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

    public function getActivitiesByUserId($userVkId, Request $request)
    {
        return DB
            ::connection('mongodb')
            ->collection('activities')
            ->where('vk_id', (int) $userVkId)
            ->get();
    }

    public function subscriberCities()
    {
        return DB
            ::connection('mongodb')
            ->collection('subscribers')
            ->groupBy('city')
            ->get(['city', 'city_id']);
    }

}
