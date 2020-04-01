<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

use App\Integrations\Telegram\Dialogs\AddTokenDialog;

class AddTokenCommand extends Command
{
    /**
     * @var string Command name.
     */
    protected $name = 'addtoken';

    /**
     * @var string Command description.
     */
    protected $description = 'Adds a Laravel Forge API token for managing your servers.';

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        AddTokenDialog::start();
    }
}
