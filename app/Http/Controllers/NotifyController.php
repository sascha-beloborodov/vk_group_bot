<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CreateFAQRequest;
use App\Http\Requests\UpdateFAQRequest;
use App\Jobs\MassNotice;
use App\Repositories\FAQRepository;
use Carbon\Carbon;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Response;

class NotifyController extends AppBaseController
{
    const DEFAULT_PER_PAGE = 100;

    public function cities(Request $request)
    {
        return response()->json(
           DB::connection('mongodb')
               ->collection('subscribers')
               ->groupBy('city_id')
               ->get(['city', 'city_id'])
        );
    }

    public function usersCount(Request $request)
    {
        $count = (int) $request->get('cityId') ?
            DB::connection('mongodb')->collection('subscribers')->where('city_id', (int) $request->get('cityId'))->count() :
            0;
        return response()->json($count);
    }

    public function notifications(Request $request)
    {
        $notifications = DB::connection('mongodb')
            ->collection('moment_notifications')
            ->orderBy('created_at', -1)
            ->paginate($request->get('per_page', 20));
        return response()->json($notifications);
    }

    public function notify(Request $request)
    {
        if (empty($request->get('text')) || !(int) $request->get('cityId')) {
            return $this->sendError('Не заполнены поля');
        }
        $recipientsCount = DB
            ::connection('mongodb')
            ->collection('subscribers')
            ->groupBy('vk_id')
            ->count();
        $insertedId = DB::connection('mongodb')->collection('moment_notifications')->insertGetId([
            'created_at' => Carbon::now(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d H:i:s'),
            'created_at_utc' => Carbon::now(new \DateTimeZone('utc'))->format('Y-m-d H:i:s'),
            'text' => $request->get('text'),
            'city' => (int) $request->get('cityId'),
            'sent' => 0,
            'is_working' => 0,
            'queued' => 1,
            'totalRecipients' => $recipientsCount,
            'successRecipients' => 0
        ]);
        MassNotice::dispatch($insertedId, (int) $request->get('cityId'))->delay(now()->addSecond(100));
    }
}