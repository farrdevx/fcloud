<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'quantity',
        'sub_total_amount',
        'grand_total_amount',
        'discount_amount',
        'is_paid',
        'book_trx_id',
        'package_id',
        'promo_code_id',
        'proof',
        'external_id',
    ];


    public static function generateUniqueTrxId()
    {
        $prefix = 'FARRX';
        do {
            $randomString = $prefix . mt_rand(1000, 99999);
        } while (self::where('book_trx_id', $randomString)->exists());

        return $randomString;
    }
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class, 'promo_code_id');
    }
    public function external(): BelongsTo
    {
        return $this->belongsTo(External::class, 'external_id');
    }

    public function externals():HasMany
    {
        return $this->hasMany(External::class);
    }


}
