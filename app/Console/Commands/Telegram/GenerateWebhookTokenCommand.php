<?php

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Str;

class GenerateWebhookTokenCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:generate-webhook-token
                           {--show : Display the telegram webhook token instead of modifying files}
                           {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the telegram webhook token in the environment file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $token = $this->generateRandomToken();

        if ($this->option('show')) {
            $this->comment($token);

            return 0;
        }

        // Next, we will replace the telegram webhook token in the environment file.
        if (! $this->setTokenInEnvironmentFile($token)) {
            return 0;
        }

        $this->info('The Telegram webhook token set successfully.');

        return 0;
    }

    /**
     * Generate a random webhook token for the telegram.
     *
     * @return string
     */
    protected function generateRandomToken(): string
    {
        return Str::random(32);
    }

    /**
     * Set the telegram webhook token in the environment file.
     *
     * @param string $token
     *
     * @return bool
     */
    protected function setTokenInEnvironmentFile($token): bool
    {
        $currentToken = env('TELEGRAM_BOT_WEBHOOK_TOKEN');

        if (strlen($currentToken) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($token);

        return true;
    }

    /**
     * Write a new environment file with the given token.
     *
     * @param string $token
     *
     * @return void
     */
    protected function writeNewEnvironmentFileWith($token): void
    {
        file_put_contents($this->environmentFilePath(), preg_replace(
            $this->tokenReplacementPattern(),
            'TELEGRAM_BOT_WEBHOOK_TOKEN='.$token,
            file_get_contents($this->environmentFilePath())
        ));
    }

    /**
     * Get a regex pattern that will match env TELEGRAM_WEBHOOK_TOKEN with any random token.
     *
     * @return string
     */
    protected function tokenReplacementPattern(): string
    {
        $escaped = preg_quote('='.env('TELEGRAM_BOT_WEBHOOK_TOKEN'), '/');

        return "/^TELEGRAM_BOT_WEBHOOK_TOKEN{$escaped}/m";
    }

    /**
     * Get a path to environment file.
     *
     * @return string
     */
    protected function environmentFilePath(): string
    {
        return base_path('.env');
    }
}
