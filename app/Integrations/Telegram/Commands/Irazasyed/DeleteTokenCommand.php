<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

use App\Integrations\Telegram\Dialogs\DeleteTokenDialog;

class DeleteTokenCommand extends Command
{
    /**
     * @var string Command name.
     */
    protected $name = 'deletetoken';

    /**
     * @var string Command description.
     */
    protected $description = 'Deletes a Laravel Forge API token.';

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        DeleteTokenDialog::start();
    }
}
