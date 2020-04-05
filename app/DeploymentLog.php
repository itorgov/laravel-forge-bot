<?php

namespace App;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;

class DeploymentLog extends Model
{
    use Uuid;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Returns an URL to a page with this log.
     * Uses the Instant View feature of Telegram if instant view hash is set.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        if (empty(config('services.telegram.instant_view.hash'))) {
            return route('deployment-logs.show', [$this]);
        }

        $query = http_build_query([
            'url' => route('deployment-logs.show', [$this]),
            'rhash' => config('services.telegram.instant_view.hash'),
        ]);

        return "https://t.me/iv?{$query}";
    }

    /**
     * Returns formatted date.
     *
     * @return string
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->toRfc850String();
    }
}
