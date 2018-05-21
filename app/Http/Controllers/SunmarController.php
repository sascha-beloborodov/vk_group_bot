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
    
    public function runTask($num, Request $request)
    {
        if (!$request->get('text')) {
            return response()->json(['message' => 'Нужно отправить хоть какой-нибудь текст'], 422);
        }
        if (! (int) $num) {
            return response()->json(['message' => 'Что-то пошло не так'], 422);
        }
        $task = DB::connection('mongodb')
                    ->collection('sunmar_tasks')
                    ->where('num', (int) $num)
                    ->first();
        if ($task) {
            return response()->json(['message' => 'Задание уже было запущено'], 422);
        }
        $this->createTask((int) $num, $request->get('text'));
        $this->disableTask((int) $num);
        RunTask::dispatch((int) $num, $request->get('text'))->delay(now()->addSecond(1));
        return response()->json(['message' => 'Messages begin sending']);
    }


    public function checkTask($num, Request $request)
    {
        if (! (int) $num) {
            return response()->json(['message' => 'Что-то пошло не так'], 422);
        }
        dump($num);
        CheckTask::dispatch((int) $num)->delay(now()->addSecond(1));
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

    private function disableTasks(int $except)
    {
        $tasks = DB::connection('mongodb')
            ->collection('sunmar_tasks')
            ->where('num', '<>', (int) $num)
            ->get();

        foreach ($tasks as $task) {
            DB::connection('mongodb')
                ->collection('sunmar_tasks')
                ->where('num', (int) $task['num'])
                ->update(['is_active' => 0]);
        }
    }
}