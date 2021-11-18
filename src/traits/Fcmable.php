<?php

namespace Timedoor\TmdMembership\traits;

use App\Models\FcmToken;

trait Fcmable
{
    protected $fcmToken;

    public function currentFcmToken()
    {
        return $this->fcmToken;
    }

    public function withFcmToken($fcmToken)
    {
        $this->fcmToken = $fcmToken;

        return $this;
    }

    public function fcmToken()
    {
        return $this->morphOne(FcmToken::class, 'fcmable');
    }

    public function fcmTokens()
    {
        return $this->morphMany(FcmToken::class, 'fcmable');
    }
}

