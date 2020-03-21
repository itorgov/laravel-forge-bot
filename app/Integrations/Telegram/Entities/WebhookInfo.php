<?php

namespace App\Integrations\Telegram\Entities;

use Illuminate\Support\Fluent;

/**
 * Class WebhookInfo
 * @package App\Integrations\Telegram\Entities
 *
 * @property string|null $url Webhook URL, may be empty if webhook is not set up
 * @property bool|null $has_custom_certificate True, if a custom certificate was provided for webhook certificate checks
 * @property int|null $pending_update_count Number of updates awaiting delivery
 * @property int|null $last_error_date Unix time for the most recent error that happened when trying to deliver an update via webhook
 * @property string|null $last_error_message Error message in human-readable format for the most recent error that happened when trying to deliver an update via webhook
 * @property int|null $max_connections Maximum allowed number of simultaneous HTTPS connections to the webhook for update delivery
 * @property string[]|null $allowed_updates A list of update types the bot is subscribed to. Defaults to all update types
 */
class WebhookInfo extends Fluent
{
    // It behaves as Fluent class.
}
