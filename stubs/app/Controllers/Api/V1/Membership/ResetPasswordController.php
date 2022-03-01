<?php

namespace App\Http\Controllers\Api\V1\Membership;

use App\Http\Controllers\Controller;
use Timedoor\TmdMembership\Infrastructure\Auth\Password\ResetPasswords;

class ResetPasswordController extends Controller
{
    use ResetPasswords;
}
