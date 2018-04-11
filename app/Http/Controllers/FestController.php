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

class FestController extends AppBaseController
{
    public function festList(Request $request)
    {
        return DB::connection('mongodb')->collection('fests')->paginate(15);
    }

    public function all()
    {
        return DB::connection('mongodb')->collection('fests')->get();
    }

    public function fest($id, Request $request)
    {
        return DB::connection('mongodb')
            ->collection('fests')
            ->where('_id', $id)
            ->first();
    }

    public function create(Request $request)
    {
        try {
            DB::connection('mongodb')->collection('fests')->insert([
                'created_at' => Carbon::now(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d H:i:s'),
                'created_at_utc' => Carbon::now(new \DateTimeZone('utc'))->format('Y-m-d H:i:s'),
                'id' => $request->get('id'),
                'date' => $request->get('date'),
                'name' => $request->get('name')
            ]);
            return $this->sendResponse(['success' => true], 'Success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function edit($id, Request $request)
    {
        try {
            DB
                ::connection('mongodb')
                ->collection('fests')
                ->where('_id', $id)
                ->update([
                    'id' => $request->get('id'),
                    'name' => $request->get('name'),
                    'date' => $request->get('date')
                ]);
            return $this->sendResponse(['success' => true], 'Success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

}