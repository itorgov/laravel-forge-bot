<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Current token.
     *
     * @return BelongsTo
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }

    /**
     * Current server.
     *
     * @return HasOne
     */
    public function server(): HasOne
    {
        return $this->hasOne(Server::class);
    }

    /**
     * Current site.
     *
     * @return HasOne
     */
    public function site(): HasOne
    {
        return $this->hasOne(Site::class);
    }
}
