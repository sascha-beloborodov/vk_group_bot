<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RunTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 
     */
    public $num;

    public $text;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $num, string $text)
    {
        $this->num = $num;
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path().'/logs/run_task.log');
        $offset = 0;
        $limit = 20;
        $participants = $this->getSunmarUsers($limit, $offset);

        while ($participants->count()) {
            foreach ($participants as $participant) {
                DB::connection('mongodb')
                    ->collection('state')
                    ->where('vk_id', (int) $participant['vk_id'])
                    ->update(['data.task' => $this->num, 'section' => 'sunmar']);
                try {
                    vkApi_messagesSend($participant['vk_id'], $this->text);
                    sleep(1);
                } catch (\Exception $e) {
                    Log::info('Send fail to - ' . $participant['vk_id']);
                    continue;
                }
            }

            $offset += 20;
            $participants = $this->getSunmarUsers($limit, $offset);
        }
        $this->resetTaskStatus();
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
            ->where('current_tasks_completed', 1)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    private function resetTaskStatus()
    {
        DB::connection('mongodb')->collection('sunmar_user')->update(['current_tasks_completed' => 0]);
    }
}
