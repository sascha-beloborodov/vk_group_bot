<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CreateFAQRequest;
use App\Http\Requests\UpdateFAQRequest;
use App\Repositories\FAQRepository;
use Carbon\Carbon;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Response;

class MessagesController extends AppBaseController
{
    const DEFAULT_PER_PAGE = 100;

    public function index(Request $request)
    {
        return view('infyom.templates.messages.index');
    }

    public function getMessages(Request $request)
    {
        $query = DB::connection('mongodb')
            ->collection('messages')
            ->orderBy('messages.data.date', 'desc');

        if ($request->get('type')) {
            $query = $query->where('data.type', $request->get('type'));
        }
        $totalMessages = $query->count();
        $cloneQuery = clone  $query;
        $unreadMessages = $cloneQuery->where('unread', 1)->count();

        $messages = $query->paginate($request->get('per_page', self::DEFAULT_PER_PAGE));

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

    public function sendMessage($userVKId, Request $request)
    {
        DB::connection('mongodb')->collection('messages')->insert([
            'created_at' => Carbon::now(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d H:i:s'),
            'created_at_utc' => Carbon::now(new \DateTimeZone('utc'))->format('Y-m-d H:i:s'),
            'data' => [
                'body' => $request->get('text'),
                'from' => 'admin',
                'date' => time(),
                'to_user_id' => (int) $userVKId
            ]
        ]);
        vkApi_messagesSend($userVKId, $request->get('text', 'Test'));
        return $this->sendResponse([], 'Message is sent');
    }

    public function clearAttempts($id, Request $request)
    {
        return response()->json([
            'status' => DB
                    ::connection('mongodb')
                    ->collection('faq_attempts')
                    ->where('vk_id', (int) $id)
                    ->update([
                        'attempts' => 0
                    ])
        ]);
    }
}