<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

class DonateCommand extends Command
{
    /**
     * @var string Command name.
     */
    protected $name = 'donate';

    /**
     * @var string Command description.
     */
    protected $description = 'Support the project.';

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->replyWithMessage([
            'text' => 'If you like this bot then please give a star to '.
                '[the GitHub repository](https://github.com/itorgov/laravel-forge-bot). '.
                'Of course, if you want you can buy [me](https://t.me/ivantorgov) a cup of coffee, '.
                'a beer or just help with paying for hosting using [my PayPal page](https://paypal.me/WiDe).',
            'parse_mode' => 'Markdown',
        ]);
    }
}
