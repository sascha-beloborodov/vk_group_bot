<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const FIRST_TASK = 1;
    const SECOND_TASK = 2;
    const THIRD_TASK = 3;
    const FOURTH_TASK = 4;
    const FIFTH_TASK = 5;
    const SIXTH_TASK = 6;
    const SEVENTH_TASK = 7;

    const GROUP_ID = 166541174;

    public $num;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $num)
    {
        Log::useFiles(storage_path().'/logs/check_task.log');
        Log::info('task fall');
        $this->num = $num;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path().'/logs/check_task.log');
        switch($this->num) {
            case self::FIRST_TASK:
                $this->handleFirstTask();
                break;
            case self::SECOND_TASK:
                $this->handleFirstTask();
                break;
            case self::THIRD_TASK:
                $this->handleFirstTask();
                break;
            case self::FOURTH_TASK:
                $this->handleFirstTask();
                break;
            case self::SIXTH_TASK:
                $this->handleFirstTask();
                break;
            case self::FIRST_TASK:
                $this->handleFirstTask();
                break;
            case self::SEVENTH_TASK:
                $this->handleFirstTask();
                break;
        }
    }

    private function handleFirstTask()
    {
        $limit = 20;
        $offset = 0;
        $users = $this->getSunmarUsers($limit, $offset);
        while ($users->count()) {
            foreach ($users as $user) {
                try {
                    $response = _vkApi_call('groups.isMember', [
                        'group_id' => self::GROUP_ID,
                        'user_id' => $user['vk_id']
                    ]);
                    Log::info($response);
                    DB
                        ::connection('mongodb')
                        ->collection('sunmar_user')
                        ->where('completed', 1)
                        ->where('vk_id'< (int) $user['vk_id'])
                        ->update(['first_task' => [
                            'completed' => (int) $response,
                        ]])
                        ->update(['first_task.history_checks' => [
                            '$push' => [
                                'checked_at_utc' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
                                'completed' => (int) $response
                            ]
                        ]]);
                    sleep(1);
                } catch (\Exception $e) {
                    Log::info('Cannot get data for user id - ' . $user['vk_id']);
                    continue;
                }
            }
            $offset += $limit;
            $users = $this->getSunmarUsers($limit, $offset);
        }
    }


    private function getSunmarUsers(int $limit, int $offset): \Illuminate\Support\Collection
    {
        return DB
            ::connection('mongodb')
            ->collection('sunmar_user')
            ->where('completed', 1)
            ->skip($offset)
            ->take($limit)
            ->get();
    }
}
