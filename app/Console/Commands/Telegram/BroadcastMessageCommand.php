<?php

namespace App\Console\Commands\Telegram;

use App\Jobs\SendMessageJob;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class BroadcastMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:broadcast-message
                           {--only-private : Send message to private chats only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a message to all users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $message = $this->ask('Enter your message');

        User::query()
            ->when(
                $this->option('only-private'),
                function (Builder $query) {
                    $query->where('telegram_chat_id', 'not like', '-%');
                }
            )
            ->each(function (User $user) use ($message) {
                SendMessageJob::dispatch($user, $message);
            });

        return 0;
    }
}
