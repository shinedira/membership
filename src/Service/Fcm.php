<?php

namespace Timedoor\TmdMembership\Service;

use Timedoor\TmdMembership\Notifications\FcmStarter;
use Illuminate\Support\Facades\Log;
use Timedoor\TmdMembership\Infrastructure\Contracts\HasRoutesNotifications;

class Fcm
{
    /**
     * Create a broadcast to send FCM Notification.
     *
     * @param  \Timedoor\TmdMembership\Infrastructure\Contracts\HasRoutesNotifications  $model
     * @param  array $content
     *
     * @return void
     */
    public static function create(HasRoutesNotifications $model, array $content)
    {
        fcm::createLog($content);

        $model->notify(new FcmStarter($content));
    }

    /**
     * Create a log info after send FCM notification.
     *
     * @param  array $content
     *
     * @return void
     */
    protected static function createLog(array $content) {
        Log::info(sprintf('%s notification class was sent a FCM message', static::class), $content);
    }
}
