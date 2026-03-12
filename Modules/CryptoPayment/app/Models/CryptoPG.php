<?php

namespace Modules\CryptoPayment\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CryptoPG extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['key', 'value'];
    protected $table = 'crypto_p_g';
}
