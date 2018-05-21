<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyFest extends Command
{
    const MAX_DAYS_INTERVAL = 3;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:fest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users who subscribed on a fest in a city';

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
        $today = new \DateTime();
        for ($nextDays = 0; $nextDays <= self::MAX_DAYS_INTERVAL; $nextDays++) {
            // fucking business case - only today and in three days
            if (!in_array($nextDays, [0, self::MAX_DAYS_INTERVAL])) {
                continue;
            }
            $clonedToday = clone $today;
            $formattedNextDay = $clonedToday->add(new \DateInterval("P{$nextDays}D"))->format('Y-m-d');
            $fests = DB::connection('mongodb')->collection('fests')->where('date', $formattedNextDay)->get();
            dump($fests);
            foreach ($fests as $fest) {
                $usersOffset = 0;
                $usersLimit = 10;
                $subscribedUsers = DB::connection('mongodb')
                    ->collection('subscribers')
                    ->where('city_id', (int) $fest['id'])
                    ->skip($usersOffset)
                    ->take($usersLimit)
                    ->get();
                    
                while(count($subscribedUsers)) {
                    $message = '';
                    foreach ($subscribedUsers as $subscribedUser) {
                        if ($nextDays === 0) {
                            $message .= 'Напоминаем, что фестиваль в городе - ' . mb_strtoupper($fest['name']) . ' состоится сегодня.' . PHP_EOL;
                        } else {
                            $message .= 'Напоминаем, что до фестиваля в городе - ' . mb_strtoupper($fest['name']) . ' осталось ' . $nextDays . ' дня.' . PHP_EOL ;
                        }
                        
                    }
                    vkApi_messagesSend($subscribedUser['vk_id'], $message);
                    sleep(2);
                    $usersOffset += 10;
                    $subscribedUsers = DB::connection('mongodb')
                        ->collection('subscribers')
                        ->where('city_id', (int) $fest['id'])
                        ->skip($usersOffset)
                        ->take($usersLimit)
                        ->get();
                }
            }

        }

    }
}
