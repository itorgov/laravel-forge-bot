<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

use App\User;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Commands\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    /**
     * Returns an authenticated user.
     *
     * @return User
     */
    protected function user(): User
    {
        return Auth::user();
    }
}
