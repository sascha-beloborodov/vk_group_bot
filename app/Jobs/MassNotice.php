<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MassNotice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string $notificationId
     */
    public $notificationId;

    /**
     * @var string $cityId
     */
    public $cityId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationId, $cityId)
    {
        $this->notificationId = $notificationId;
        $this->cityId = $cityId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path().'/logs/notification.log');

        $offset = 0;
        $limit = 5;      

        $notice = DB::connection('mongodb')->collection('moment_notifications')->where('_id', $this->notificationId)->first();
        $recipients = !empty($notice['activity']) ? $this->getUsersWithActivities($limit, $offset, $notice['activities']) : $this->getUsersWithSubscribe($limit, $offset);
        $totalRecipients = 0;
        // loop parts of recepients
        while ($recipients->count()) {
            $totalRecipients += $recipients->count();
            // the notice is updated every iteration
            $notice = DB::connection('mongodb')->collection('moment_notifications')->where('_id', $this->notificationId)->first();
            foreach ($recipients as $recipient) {
                try {
                    vkApi_messagesSend($recipient['vk_id'], $notice['text']);
                    sleep(1);
                } catch (\Exception $e) {
                    Log::info('Send fail to - ' . $recipient['vk_id']);
                    continue;
                }
            }
            sleep(1);
            $offset += 5;
            $this->updateNotificationInfo($this->notificationId, [
                'successRecipients' => $notice['successRecipients'] + $recipients->count(),
                'is_working' => 1
            ]);
            // get other recipients
            $recipients = !empty($notice['activity']) ? $this->getUsersWithActivities($limit, $offset, $notice['activities']) : $this->getUsersWithSubscribe($limit, $offset);
        }
        $this->updateNotificationInfo($this->notificationId, [
            'successRecipients' => $totalRecipients,
            'is_working' => 0,
            'sent' => 1,
            'queued' => 0,
        ]);
    }

    /**
     * Update current notification
     *
     * @param string $notificationId
     * @param array $updatedData
     * @return void
     */
    private function updateNotificationInfo($notificationId, $updatedData)
    {
        DB::connection('mongodb')
            ->collection('moment_notifications')
            ->where(['_id' => $notificationId])
            ->update($updatedData);
    }

    /**
     * Get users with activites by parts
     *
     * @param int $limit
     * @param int $offset
     * @param array[string] $activities
     * @return Illuminate\Support\Collection
     */
    private function getUsersWithActivities($limit, $offset, $activities = null)
    {
        $query = DB
            ::connection('mongodb')
            ->collection('activities')
            ->where('city_id', (int) $this->cityId);
        if (!empty($activities) && is_array($activities)) {
            $query = $query->whereIn('activities', $activities);
        }
        return $query->skip($offset)->take($limit)->get();
    }

    /**
     * Get users with subscribers by parts
     *
     * @param int $limit
     * @param int $offset
     * @return Illuminate\Support\Collection
     */
    private function getUsersWithSubscribe($limit, $offset)
    {
        return DB
            ::connection('mongodb')
            ->collection('subscribers')
            ->where('city_id', (int) $this->cityId)
            ->skip($offset)
            ->take($limit)
            ->get();
    }
}
