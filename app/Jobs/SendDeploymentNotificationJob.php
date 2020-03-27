<?php

namespace App\Jobs;

use App\Integrations\Telegram\Entities\OutboundMessage;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

class SendDeploymentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User $user
     */
    private User $user;

    /**
     * @var array $deploymentInfo
     */
    private array $deploymentInfo;

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
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
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
}
