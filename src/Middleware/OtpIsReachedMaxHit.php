<?php

namespace Timedoor\TmdMembership\Middleware;

use App\Models\Otp;
use Closure;

class OtpIsReachedMaxHit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $otp = Otp::getLatestOtp($request)->withoutExpired()->first();

        if ($otp) {
            if (!$otp->isExpired() && $otp->isReachedMax()) {
                abort(400, 'Bad Request. Token not expired yet');
            }
        }

        return $next($request);
    }
}
