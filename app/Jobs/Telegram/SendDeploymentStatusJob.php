<?php

namespace App\Jobs\Telegram;

use Illuminate\Support\Fluent;
use GuzzleHttp\Client;
use App\Jobs\Job;

class SendDeploymentStatusJob extends Job
{
    /**
     * @var int $chatId
     */
    private $chatId;

    /**
     * @var array $deploymentInfo
     */
    private $deploymentInfo;

    /**
     * Create a new job instance.
     *
     * @param int $chatId
     * @param array $deploymentInfo
     *
     * @return void
     */
    public function __construct(int $chatId, array $deploymentInfo)
    {
        $this->chatId = $chatId;
        $this->deploymentInfo = $deploymentInfo;

        logger('Deployment info', $deploymentInfo);
    }

    /**
     * Execute the job.
     *
     * @param Client $client
     *
     * @return void
     */
    public function handle(Client $client): void
    {
        $server = new Fluent(data_get($this->deploymentInfo, 'server'));
        $site = new Fluent(data_get($this->deploymentInfo, 'site'));
        $commit = new Fluent([
            'hash' => data_get($this->deploymentInfo, 'commit_hash'),
            'url' => data_get($this->deploymentInfo, 'commit_url'),
            'author' => data_get($this->deploymentInfo, 'commit_author'),
            'message' => data_get($this->deploymentInfo, 'commit_message')
        ]);

        $message = [
            '*Deployment complete!*',
            "*Server:* [{$server->name}](https://forge.laravel.com/servers/{$server->id})",
            "*Site:* [{$site->name}](https://forge.laravel.com/servers/{$server->id}/sites/{$site->id})",
            '*Status:* ' . $this->getStatus(),
            '*Commit author:* ' . $commit->author,
            "*Commit hash:* [$commit->hash]($commit->url)",
            '*Commit message:* ' . $commit->message,
        ];

        $client->post('sendMessage', [
            'json' => [
                'chat_id' => $this->chatId,
                'parse_mode' => 'Markdown',
                'text' => implode("\n", $message)
            ]
        ]);
    }

    /**
     * Get formatted status of deployment.
     *
     * @return string
     */
    protected function getStatus(): string
    {
        $status = data_get($this->deploymentInfo, 'status');

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
