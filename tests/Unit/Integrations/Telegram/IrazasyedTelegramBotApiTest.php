<?php

namespace Tests\Unit\Integrations\Telegram;

use App\Integrations\Telegram\Exceptions\TelegramBotException;
use App\Integrations\Telegram\IrazasyedTelegramBot;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IrazasyedTelegramBotApiTest extends TestCase
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
        $telegram = new IrazasyedTelegramBot(env('TELEGRAM_BOT_API_KEY'));
        $this->rawRequest('deleteWebhook');

        tap($this->rawRequest('getWebhookInfo')->json(), function ($body) {
            $this->assertTrue($body['ok']);
            $this->assertEquals('', $body['result']['url']);
        });

        sleep(1); // To avoid 429 error;
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
        $this->expectException(TelegramBotException::class);
        $telegram = new IrazasyedTelegramBot(env('TELEGRAM_BOT_API_KEY'));

        sleep(1); // To avoid 429 error;
        $telegram->setWebhook('invalid-url');
    }

    /**
     * @test
     */
    public function can_succsessfuly_delete_a_webhook()
    {
        $telegram = new IrazasyedTelegramBot(env('TELEGRAM_BOT_API_KEY'));
        sleep(1); // To avoid 429 error;
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
