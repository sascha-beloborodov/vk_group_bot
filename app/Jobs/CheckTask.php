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

    public $num;

    public $token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $num, $token = null)
    {
        Log::useFiles(storage_path().'/logs/check_task.log');
        Log::info('task fall - ' . $num);
        $this->num = $num;
        $this->token = $token;
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
                $this->handleSecondTask();
                break;
            case self::THIRD_TASK:
                $this->handleThirdTask();
                break;
            case self::FOURTH_TASK:
            case self::FIFTH_TASK:
            case self::SIXTH_TASK:
            case self::SEVENTH_TASK:
            default:
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
                    sleep(1);
                    Log::info($response);
                    DB
                        ::connection('mongodb')
                        ->collection('sunmar_user')
                        ->where('vk_id', (int) $user['vk_id'])
                        ->update(['first_task.completed' => (int) $response, 'current_tasks_completed' => 1]);

                    DB
                        ::connection('mongodb')
                        ->collection('sunmar_user')
                        ->where('vk_id', (int) $user['vk_id'])
                        ->update(['first_task.history_checks' => [
                            '$push' => [
                                'checked_at_utc' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
                                'completed' => (int) $response
                            ]
                        ]]);
                    
                } catch (\Exception $e) {
                    sleep(1);
                    Log::info('Cannot get data for user id - ' . $user['vk_id']);
                    continue;
                }
            }
            $offset += $limit;
            $users = $this->getSunmarUsers($limit, $offset);
        }
    }

    private function handleSecondTask()
    {
        $usersLimit = 20;
        $usersOffset = 0;
        $users = $this->getSunmarUsers($usersLimit, $usersOffset);
        while ($users->count()) {
            foreach ($users as $user) {
                $limit = 100;
                $offset = 0;
                try {
                    $response = $this->executeVKRequest('wall.get', [
                        'owner_id' => $user['vk_id'],
                        'offset' => $offset,
                        'count' => $limit,
                        'filter' => 'owner'
                    ]);
                    Log::info('response');
                    Log::info($response);
                    sleep(1);
                } catch (\Exception $e) {
                    sleep(1);
                    Log::info('Cannot get data for user id - ' . $user['vk_id']);
                    continue;
                }

                $exit = false;
                sleep(1);
                while (isset($response['count']) && $response['count'] > 0 && isset($response['items'])) {
                    Log::info('items');
                    Log::info($response['items']);
                    foreach ($response['items'] as $item) {
                        if (!empty($item['copy_history']) && is_array($item['copy_history'])) {
                            Log::info('copy_history');
                            Log::info($item['copy_history']);
                            foreach ($item['copy_history'] as $source) {
                                Log::info('source');
                                Log::info($source['owner_id']);
                                Log::info('group id ' . config('app.sunmar_group_id'));
                                Log::info('user id ' . $user['vk_id']);

                                if (!empty($source['owner_id']) && abs($source['owner_id']) == config('app.sunmar_group_id')) {
                                    Log::info('save');

                                    DB
                                        ::connection('mongodb')
                                        ->collection('sunmar_user')
                                        ->where('vk_id', (int) $user['vk_id'])
                                        ->update(['second_task.completed' => 1, 'current_tasks_completed' => 1]);
                                    DB
                                        ::connection('mongodb')
                                        ->collection('sunmar_user')
                                        ->where('vk_id', (int) $user['vk_id'])
                                        ->update(['$push' => [
                                            'second_task.history_checks' => [
                                                'completed' => 1,
                                                'time' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
                                            ]
                                        ]]);
                                    $exit = true;
                                }
                            }
                        }
                    }
                    if ($exit) break;
                    $offset += $limit;
                    $response = $this->executeVKRequest('wall.get', [
                        'owner_id' => $user['vk_id'],
                        'offset' => $offset,
                        'count' => $limit,
                        'filter' => 'owner'
                    ]);
                    Log::info('response while');
                    Log::info($response);
                    sleep(1);
                }
            }
            $usersOffset += $usersLimit;
            $users = $this->getSunmarUsers($usersLimit, $usersOffset);
        }
    }

    private function handleThirdTask()
    {
        $usersLimit = 20;
        $usersOffset = 0;
        $users = $this->getSunmarUsers($usersLimit, $usersOffset);
        while ($users->count()) {
            foreach ($users as $user) {
                $limit = 100;
                $offset = 0;
                try {
                    $response = $this->executeVKRequest('wall.get', [
                        'owner_id' => $user['vk_id'],
                        'offset' => $offset,
                        'count' => $limit,
                        'filter' => 'owner'
                    ]);
                    Log::info('3 response');
                    Log::info($response);
                    sleep(1);
                } catch (\Exception $e) {
                    sleep(1);
                    Log::info('Cannot get data for user id - ' . $user['vk_id']);
                    continue;
                }
                $exit = false;

                while (isset($response['count']) && $response['count'] > 0 && isset($response['items'])) {
                    Log::info('3 items');
                    Log::info($response['items']);
                    foreach ($response['items'] as $item) {
                        Log::info('3 item');
                        Log::info($item);
                        if (!empty($item['post_type']) && $item['post_type'] != 'post') continue;
                        if (!empty($item['attachments']) && is_array($item['attachments'])) {
                            foreach ($item['attachments'] as $attachment) {
                                if (!empty($attachment['type']) &&
                                    $attachment['type'] == 'photo' && 
                                    !empty($attachment['photo']['text']) &&
                                    (stripos($attachment['photo']['text'], config('app.sunmar_keyword')) !== false ||
                                    stripos($item['text'], config('app.sunmar_keyword')) !== false)
                                    )
                                {
                                    $exit = true;
                                    DB
                                        ::connection('mongodb')
                                        ->collection('sunmar_user')
                                        ->where('vk_id', (int) $user['vk_id'])
                                        ->update(['third_task.completed' => 1, 'current_tasks_completed' => 1]);
                                    DB
                                        ::connection('mongodb')
                                        ->collection('sunmar_user')
                                        ->where('vk_id', (int) $user['vk_id'])
                                        ->update(['$push' => [
                                            'third_task.history_checks' => [
                                                'completed' => 1,
                                                'time' => (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
                                            ]
                                        ]]);
                                }
                            }
                        }
                    }
                    if ($exit) break;
                    $offset += $limit;
                    $response = $this->executeVKRequest('wall.get', [
                        'owner_id' => $user['vk_id'],
                        'offset' => $offset,
                        'count' => $limit,
                        'filter' => 'owner'
                    ]);
                    sleep(1);
                }
            }
            $usersOffset += $usersLimit;
            $users = $this->getSunmarUsers($usersLimit, $usersOffset);
        }
    }

    private function executeVKRequest(string $method, array $params) :array
    {
        return _vkApi_call($method, $params, $this->token);
    }

    /**
     * Paginate users
     *
     * @param integer $limit
     * @param integer $offset
     * @return \Illuminate\Support\Collection
     */
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
