<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CreateFAQRequest;
use App\Http\Requests\UpdateFAQRequest;
use App\Jobs\RunTask;
use App\Jobs\CheckTask;
use App\Repositories\FAQRepository;
use Carbon\Carbon;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Response;

class SunmarController extends AppBaseController
{
    
    public function runTask(Request $request)
    {
        if (!$request->get('text')) {
            return response()->json(['message' => 'Нужно отправить хоть какой-нибудь текст'], 422);
        }
        if (! (int) $request->get('num')) {
            return response()->json(['message' => 'Что-то пошло не так'], 422);
        }
        $task = DB::connection('mongodb')
                    ->collection('sunmar_tasks')
                    ->where('num', (int) $request->get('num'))
                    ->first();
        if (!$task) {
            $this->createTask((int) $request->get('num'), $request->get('text'));
        }
        DB::connection('mongodb')->collection('sunmar_tasks')->where(['_id' => $task['_id']])->update(['is_active' => 1]);
        $this->disableTasks((int) $request->get('num'));
        RunTask::dispatch((int) $request->get('num'), $request->get('text'))->delay(now()->addSecond(1));
        return response()->json(['message' => 'Messages begin sending']);
    }


    public function checkTask(Request $request)
    {
        if (! (int) $request->get('num')) {
            return response()->json(['message' => 'Что-то пошло не так'], 422);
        }
        
        CheckTask::dispatch((int) $request->get('num'), $request->get('token'))->delay(now()->addSecond(1));
        return response()->json(['message' => '']);
    }

    public function getByNum($num, Request $request)
    {
        $task = DB::connection('mongodb')
            ->collection('sunmar_tasks')
            ->where('num', (int) $num)
            ->first();
        return response()->json($task ?? new \stdClass());
    }

    public function getAllTasks(Request $request)
    {
        return response()->json(DB::connection('mongodb')->collection('sunmar_tasks')->get());
    }

    public function deleteData(Request $request)
    {
        DB::connection('mongodb')->collection('sunmar_tasks')->delete();
        DB::connection('mongodb')->collection('state')->delete();
        DB::connection('mongodb')->collection('sunmar_user')->delete();
        return response()->json(['message' => '']);
    }


    public function getUsers(Request $request)
    {
        $users = DB::connection('mongodb')
            ->collection('sunmar_user')
            ->paginate(50);

        return response()
            ->json([
                'users' => $users
            ]);
    }

    /**
     * Create new task
     *
     * @param integer $num
     * @param string $text
     * @return void
     */
    private function createTask(int $num, string $text)
    {
        DB::connection('mongodb')
            ->collection('sunmar_tasks')
            ->insert([
                'num' => $num,
                'text' => $text,
                'is_active' => 1,
                'created_at' => (new \DateTime('now', new \DateTimeZone('Europe/Moscow')))->format('Y-m-d H:i:s'),
                'created_at_utc' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Disable other tasks exept that ran 
     *
     * @param integer $except
     * @return void
     */
    private function disableTasks(int $except)
    {
        DB::connection('mongodb')
            ->collection('sunmar_tasks')
            ->where('num', '<>',(int) $except)
            ->update(['is_active' => 0]);
    }
}