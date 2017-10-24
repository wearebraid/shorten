<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    /**
     * Allow these attributes to be filled.
     *
     * @var array
     */
    protected $fillable = [
        'hash',
        'redirect_to',
    ];

    /**
     * The url attribute of a particular.
     *
     * @var array
     */
    protected $appends = [
        'url'
    ];

    /**
     * Hide unnecessary or private fields.
     *
     * @return array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the url attribute.
     *
     * @return void
     */
    public function getUrlAttribute()
    {
        return config()['base_url'] . '/' . $this->hash;
    }

    /**
     * Creates an absolutely unique hash for a given url.
     *
     * @return void
     */
    public function createUnique($url)
    {
        do {
            $hash = bin2hex(openssl_random_pseudo_bytes(4));
        } while (static::where('hash', $hash)->first());
        return static::create([
            'hash' => $hash,
            'redirect_to' => $url
        ]);
    }
}
