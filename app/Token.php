<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Tokens with specified name.
     *
     * @param Builder $query
     * @param string $name
     *
     * @return Builder
     */
    public function scopeName(Builder $query, string $name): Builder
    {
        return $query->where('name', $name);
    }
}
