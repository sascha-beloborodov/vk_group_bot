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

}