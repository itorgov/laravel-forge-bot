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
            'text' => 'If you like this bot and find it useful, please give a star to '.
                '[the GitHub repository](https://github.com/itorgov/laravel-forge-bot). '.
                'Of course, if you would like to support it, please consider a donation ' .
                'using my [PayPal](https://paypal.me/WiDe) page. Your donation would help ' .
                '[me](https://t.me/ivantorgov) a lot to continue running this bot and covering hosting costs.',
            'parse_mode' => 'Markdown',
        ]);
    }
}
