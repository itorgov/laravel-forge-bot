<?php

namespace App\Integrations\Telegram\Entities;

use Illuminate\Support\Fluent;

/**
 * Class WebhookResponse
 * @package App\Integrations\Telegram\Entities
 *
 * @property bool|null $ok
 * @property bool|null $result
 * @property string|null $description
 */
class WebhookResponse extends Fluent
{
    // It behaves as Fluent class.
}
