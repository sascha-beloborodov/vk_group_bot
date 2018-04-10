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

class PhotoController extends AppBaseController
{
    public function photos(Request $request)
    {
        return DB::connection('mongodb')->collection('photos')->paginate(15);
    }

    public function photo($id, Request $request)
    {
        return DB::connection('mongodb')
            ->collection('photos')
            ->where('_id', $id)
            ->first();
    }

    public function create(Request $request)
    {
        try {
            DB::connection('mongodb')->collection('photos')->insert([
                'created_at' => Carbon::now(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d H:i:s'),
                'created_at_utc' => Carbon::now(new \DateTimeZone('utc'))->format('Y-m-d H:i:s'),
                'city_id' => $request->get('id'),
                'city' => $request->get('city'),
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
                ->collection('photos')
                ->whereIn('_id', $id)
                ->update([
                    'city_id' => $request->get('id'),
                    'city' => $request->get('city'),
                    'name' => $request->get('name')
                ]);
            return $this->sendResponse(['success' => true], 'Success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

}