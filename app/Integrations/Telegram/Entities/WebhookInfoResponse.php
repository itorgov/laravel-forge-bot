<?php

namespace App\Integrations\Telegram\Entities;

use Illuminate\Support\Fluent;

/**
 * Class WebhookInfoResponse
 * @package App\Integrations\Telegram\Entities
 *
 * @property bool|null $ok
 * @property WebhookInfo|null $result
 */
class WebhookInfoResponse extends Fluent
{
    // It behaves as Fluent class.

    public function __construct($attributes = [])
    {
        $attributes['result'] = new WebhookInfo($attributes['result'] ?? []);

        parent::__construct($attributes);
    }
}
