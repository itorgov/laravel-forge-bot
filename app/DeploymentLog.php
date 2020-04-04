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
     * Returns an URL to a page with this log using the Instant View feature of Telegram.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        $query = http_build_query([
            'url' => route('deployment-logs.show', [$this]),
            'rhash' => config('services.telegram.instant_view.hash'),
        ]);

        return "https://t.me/iv?{$query}";
    }
}
