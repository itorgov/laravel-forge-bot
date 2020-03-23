<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

use App\Integrations\Telegram\Dialogs\AddTokenDialog;

class AddTokenCommand extends Command
{
    /**
     * @var string Command name.
     */
    protected $name = "addtoken";

    /**
     * @var string Command description.
     */
    protected $description = "Adds Laravel Forge API token for managing your servers.";

    /**
     * Handle the command.
     *
     * @param $arguments
     */
    public function handle($arguments)
    {
        AddTokenDialog::start();
    }
}
