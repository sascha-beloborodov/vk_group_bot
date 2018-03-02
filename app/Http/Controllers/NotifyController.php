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

    public function notify(Request $request)
    {
        $recipientsCount = DB
            ::connection('mongodb')
            ->collection('subscribers')
            ->distinct('vk_id')
            ->count();
        $insertedId = DB::connection('mongodb')->collection('moment_notifications')->insertGetId([
            'created_at' => Carbon::now(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d H:i:s'),
            'created_at_utc' => Carbon::now(new \DateTimeZone('utc'))->format('Y-m-d H:i:s'),
            'text' => $request->get('text', ''),
            'sent' => 0,
            'queued' => 1,
            'totalRecipients' => $recipientsCount,
            'successRecipients' => 0
        ]);
        MassNotice::dispatch($insertedId)->delay(now()->addMinutes(15));
    }
}