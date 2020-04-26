<?php

namespace App\Integrations\Telegram\Commands\Irazasyed;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Exceptions\CouldNotUploadInputFile;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Objects\Message as MessageObject;

class WebhookCommand extends Command
{
    private const IMAGE_CACHE_KEY = 'images-ids:deployment-webhooks-section.png';

    /**
     * @var string Command name.
     */
    protected $name = 'webhook';

    /**
     * @var string Command description.
     */
    protected $description = 'This command returns a webhook URL for receiving deployment notifications without providing an API token.';

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->replyWithMessage([
            'text' => "It's the webhook URL for this chat:\n*{$this->user()->forgeWebhookUrl()}*",
            'parse_mode' => 'Markdown',
        ]);

        if (Cache::has(self::IMAGE_CACHE_KEY)) {
            $this->sendImageUsingFileId();
        } else {
            $this->sendImageUsingFile();
        }
    }

    /**
     * Sends a screenshot of Forge's "Deployment Webhooks" section using file ID.
     * If Telegram will return an error then it will try to send the screenshot using file.
     *
     * @return void
     */
    private function sendImageUsingFileId(): void
    {
        try {
            $this->sendImage(Cache::get(self::IMAGE_CACHE_KEY));
        } catch (CouldNotUploadInputFile $exception) {
            report($exception);
            $this->sendImageUsingFile();
        }
    }

    /**
     * Sends a screenshot of Forge's "Deployment Webhooks" section using file.
     * Saves the file ID for using it in the future.
     *
     * @return void
     */
    private function sendImageUsingFile(): void
    {
        $image = InputFile::create(Storage::disk()->path('telegram/deployment-webhooks-section.png'));
        $response = $this->sendImage($image);

        Cache::forever(
            self::IMAGE_CACHE_KEY,
            collect($response->photo)
                ->pluck('file_id', 'width')
                ->sortKeysDesc()
                ->first()
        );
    }

    /**
     * Sends image.
     *
     * @param InputFile|string $image
     * @return MessageObject
     */
    private function sendImage($image): MessageObject
    {
        return $this->replyWithPhoto([
            'photo' => $image,
            'caption' => "Copy that URL and paste it to this section in your Laravel Forge site details.",
        ]);
    }
}
