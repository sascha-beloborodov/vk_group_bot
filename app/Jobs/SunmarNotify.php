<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SunmarNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;

    /**
     * 
     * @param string $message
     */
    public function __construct(string $message)
    {
        if (!strlen((string) $message)) {
            throw new \Exception('Nothing to send');
        }
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path().'/logs/sunmarmessage.log');

        $offset = 0;
        $limit = 20;      

        $users = $this->getSunmarUsers($limit, $offset);
        // loop parts of users
        while ($users->count()) {
            foreach ($users as $user) {
                Log::info('Send fail to - ' . $user['vk_id']);
                continue;
                try {

                    vkApi_messagesSend($user['vk_id'], $this->message);
                    sleep(1);
                } catch (\Exception $e) {
                    Log::info('Send fail to - ' . $user['vk_id']);
                    continue;
                }
            }
            $offset += 20;
            // get other users + 20
            $users = $this->getSunmarUsers($limit, $offset);
        }
    }

    /**
     * Get users by page
     *
     * @param integer $limit
     * @param integer $offset
     * @return void
     */
    private function getSunmarUsers(int $limit, int $offset)
    {
        return DB
            ::connection('mongodb')
            ->collection('sunmar_user')
            ->where('registration_completed', 1)
            ->where('current_tasks_completed', 0)
            ->skip($offset)
            ->take($limit)
            ->get();
    }
}
