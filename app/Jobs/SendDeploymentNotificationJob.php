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
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Fluent;

class SendDeploymentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    public User $user;

    /**
     * @var array
     */
    public array $deploymentInfo;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param array $deploymentInfo
     */
    public function __construct(User $user, array $deploymentInfo)
    {
        $this->user = $user;
        $this->deploymentInfo = $deploymentInfo;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags(): array
    {
        return [
            'deployment-notification',
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
                $server = new Fluent(Arr::get($this->deploymentInfo, 'server'));
                $site = new Fluent(Arr::get($this->deploymentInfo, 'site'));
                $commit = new Fluent([
                    'hash' => Arr::get($this->deploymentInfo, 'commit_hash'),
                    'url' => Arr::get($this->deploymentInfo, 'commit_url'),
                    'author' => Arr::get($this->deploymentInfo, 'commit_author'),
                    'message' => Arr::get($this->deploymentInfo, 'commit_message'),
                ]);

                $message = [
                    '*Deployment complete!*',
                    "*Server:* [{$server->name}](https://forge.laravel.com/servers/{$server->id})",
                    "*Site:* [{$site->name}](https://forge.laravel.com/servers/{$server->id}/sites/{$site->id})",
                    "*Status:* {$this->getStatus()}",
                    "*Commit author:* {$commit->author}",
                    "*Commit hash:* [$commit->hash]($commit->url)",
                    "*Commit message:* {$commit->message}",
                ];

                OutboundMessage::make($this->user, implode("\n", $message))
                    ->parseMode(OutboundMessage::PARSE_MODE_MARKDOWN)
                    ->send();
            }, function () {
                $this->release(10);
            });
    }

    /**
     * Get formatted status of deployment.
     *
     * @return string
     */
    protected function getStatus(): string
    {
        $status = Arr::get($this->deploymentInfo, 'status');

        switch ($status) {
            case 'success':
                return "✅ {$status}";

            case 'failed':
                return "❌ {$status}";

            default:
                return $status;
        }
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
