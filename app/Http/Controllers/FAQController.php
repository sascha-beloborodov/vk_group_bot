<?php

namespace App\Http\Controllers;

use App\DataTables\FAQDataTable;
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

class FAQController extends AppBaseController
{
    /** @var  FAQRepository */
    private $fAQRepository;

    public function __construct(FAQRepository $fAQRepo)
    {
        $this->fAQRepository = $fAQRepo;
    }

    /**
     * Display a listing of the FAQ.
     *
     * @param Request $fAQDataTable
     * @return Response
     */
    public function index(Request $request)
    {
        return view('infyom.templates.faq.index');
    }

    public function getList(Request $request)
    {
        return response()
            ->json(
                DB::connection('mongodb')
                    ->collection('faq')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
            );
    }

    /**
     * Show the form for creating a new FAQ.
     *
     * @return Response
     */
    public function create()
    {
        return view('templates.faq.create');
    }

    /**
     * Store a newly created FAQ in storage.
     *
     * @param CreateFAQRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input['created_at'] = Carbon::now(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d H:i:s');
        $input['created_at_utc'] = Carbon::now(new \DateTimeZone('utc'))->format('Y-m-d H:i:s');
        $input['updated_at'] = null;
        $input['updated_at_utc'] = null;

        DB::connection('mongodb')->collection('faq')->insert($input);

        return $this->sendResponse([], 'faq saved!!');
    }

    /**
     * Display the specified FAQ.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $faq = DB::connection('mongodb')
            ->collection('faq')
            ->where(['_id' => $id])
            ->first();
        return $this->sendResponse($faq, 'FAQ');
    }

    /**
     * Show the form for editing the specified FAQ.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fAQ = $this->fAQRepository->findWithoutFail($id);

        if (empty($fAQ)) {
            Flash::error('F A Q not found');

            return redirect(route('fAQS.index'));
        }

        return view('templates.faq.edit')->with('fAQ', $fAQ);
    }

    /**
     * Update the specified FAQ in storage.
     *
     * @param  int              $id
     * @param UpdateFAQRequest $request
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $input = $request->all();
        $input['updated_at'] = Carbon::now(new \DateTimeZone('Europe/Moscow'))->format('Y-m-d H:i:s');
        $input['updated_at_utc'] = Carbon::now(new \DateTimeZone('utc'))->format('Y-m-d H:i:s');
        unset($input['_id']);
        unset($input['id']);
        DB::connection('mongodb')
            ->collection('faq')
            ->where(['_id' => $id])
            ->update($input);

        return $this->sendResponse([], 'faq saved!!');
    }

    /**
     * Remove the specified FAQ from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $isDeleted = DB::connection('mongodb')
            ->collection('faq')
            ->where(['_id' => $id])
            ->delete();
        return $this->sendResponse(['isDeleted' => $isDeleted], 'Deleted Successfully');
    }
}
