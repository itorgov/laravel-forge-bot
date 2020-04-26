<?php

namespace App\Jobs;

use App\Integrations\Telegram\Entities\OutboundMessage;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    public User $user;

    /**
     * @var string
     */
    public string $text;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $text
     * @return void
     */
    public function __construct(User $user, string $text)
    {
        $this->user = $user;
        $this->text = $text;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags(): array
    {
        return [
            'message',
            'user:'.$this->user->id,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws LimiterTimeoutException
     */
    public function handle(): void
    {
        // From official documentation (https://core.telegram.org/bots/faq):
        // The API will not allow bulk notifications to more than ~30 users per second.
        // Also note that your bot will not be able to send more than 20 messages per minute to the same group.
        Redis::throttle('telegram-api')
            ->allow(25)
            ->every(1)
            ->then(function () {
                OutboundMessage::make($this->user, $this->text)
                    ->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)
                    ->send();
            }, function () {
                $this->release(10);
            });
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return Carbon
     */
    public function retryUntil(): Carbon
    {
        return now()->addMinutes(10);
    }
}
