<?php

namespace App\Http\Controllers;

trait Helper {

    /**
     * @param $messages array
     */
    public function markNewestMessages(&$messages)
    {
        foreach ($messages as &$messageItem) {
            if ($messageItem['unread'] == 1) {
                $_id = $messageItem['_id'];
                $ids[] = (string) $_id;
                $messageItem['is_new'] = 1;
            } else {
                $messageItem['is_new'] = 0;
            }
        }
    }
}