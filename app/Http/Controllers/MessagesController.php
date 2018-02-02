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
use Illuminate\Support\Facades\DB;
use Response;

class MessagesController extends AppBaseController
{
    public function index(Request $request)
    {
        return view('infyom.templates.messages.index');
    }

    public function getMessages(Request $request)
    {
        return response()
            ->json(
                DB::connection('mongodb')
                    ->collection('messages')
                    ->orderBy('messages.data.date', 'desc')
                    ->paginate(30)
            );
    }

    public function sendMessage($userVKId, Request $request)
    {
        vkApi_messagesSend($userVKId, $request->get('text', 'Test'));
        return $this->sendResponse([], 'Message is sent');
    }

}