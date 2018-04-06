<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyFest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:fest {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $usersOffset = 0;
        $usersLimit = 10;
        // get tomorrow fests
        $tomorrowFests = DB::connection('mongodb')->collection('fests')->where('date',  (new \DateTime('tomorrow'))->format('Y-m-d'))->get();

        foreach ($tomorrowFests as $tomorrowFest) {
            $subscribedUsers = $this->subscribedUsers($tomorrowFest['name'], $usersLimit, $usersOffset);

            // will handle by parts
            while(count($subscribedUsers)) {

                foreach ($subscribedUsers as $subscribedUser) {
                    $activities = $this->usersActivities($subscribedUser['vk_id']);
                    foreach ($activities as $activity) {
                        vkApi_messagesSend($activity['vk_id'], 'Завтра фестиваль. Ты записан на активность - ' . $activity['name']);
                        sleep(2);
                    }
                }

                $usersOffset += 10;
                $subscribedUsers = $this->subscribedUsers($tomorrowFest['name'], $usersLimit, $usersOffset);
            }
        }
    }

    /**
     * Get subscribed users
     *
     * @param $city
     * @param $limit
     * @param $offset
     * @return mixed
     */
    private function subscribedUsers($city, $limit, $offset)
    {
        return DB::connection('mongodb')
            ->collection('subscribers')
            ->where('city', $city)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * Get user's activities
     *
     * @param $userId
     * @return mixed
     */
    private function usersActivities($userId)
    {
        return DB::connection('mongodb')
            ->collection('activities')
            ->where('vk_id', $userId)
            ->get();
    }
}
