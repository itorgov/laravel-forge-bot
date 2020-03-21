<?php

namespace Tests\Unit\Integrations\Telegram;

use App\Integrations\Telegram\Exceptions\TelegramBotException;
use App\Integrations\Telegram\LongmanTelegramBotApi;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LongmanTelegramBotApiTest extends TestCase
{
    private function rawRequest(string $methodName, $data = []): Response
    {
        sleep(1); // To avoid 429 error;
        return Http::post(
            vsprintf('https://api.telegram.org/bot%s/%s', [config('services.telegram.bot.api_key'), $methodName]),
            $data
        );
    }

    /**
     * @test
     */
    public function can_succsessfuly_set_a_webhook_url()
    {
        sleep(1); // To avoid 429 error;
        $telegram = new LongmanTelegramBotApi(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_API_USERNAME'));
        $this->rawRequest('deleteWebhook');

        tap($this->rawRequest('getWebhookInfo')->json(), function ($body) {
            $this->assertTrue($body['ok']);
            $this->assertEquals('', $body['result']['url']);
        });

        $telegram->setWebhook(route('integrations.telegram.webhook'));

        tap($this->rawRequest('getWebhookInfo')->json(), function ($body) {
            $this->assertTrue($body['ok']);
            $this->assertEquals(route('integrations.telegram.webhook'), $body['result']['url']);
        });
    }

    /**
     * @test
     */
    public function cannot_set_an_invalid_webhook_url()
    {
        sleep(1); // To avoid 429 error;
        $this->expectException(TelegramBotException::class);
        $telegram = new LongmanTelegramBotApi(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_API_USERNAME'));

        $telegram->setWebhook('invalid-url');
    }

    /**
     * @test
     */
    public function can_succsessfuly_delete_a_webhook()
    {
        sleep(1); // To avoid 429 error;
        $telegram = new LongmanTelegramBotApi(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_API_USERNAME'));
        $telegram->setWebhook(route('integrations.telegram.webhook'));

        tap($this->rawRequest('getWebhookInfo')->json(), function ($body) {
            $this->assertTrue($body['ok']);
            $this->assertEquals(route('integrations.telegram.webhook'), $body['result']['url']);
        });

        $this->rawRequest('deleteWebhook');

        tap($this->rawRequest('getWebhookInfo')->json(), function ($body) {
            $this->assertTrue($body['ok']);
            $this->assertEquals('', $body['result']['url']);
        });
    }
}
