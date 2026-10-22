<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasUuids;

    protected $table = 'bookings';
    public $timestamps = false; // tabel hanya punya created_at (diisi default DB)

    protected $fillable = [
        'slot_id', 'cat_id', 'owner_id', 'mitra_id', 'service_id',
        'queue_number', 'status', 'notes',
        'service_name', 'service_price',
        'slot_date', 'slot_start_time', 'slot_end_time',
        'check_in_at', 'check_out_at',
    ];

    protected $casts = [
        'slot_date'     => 'date',
        'service_price' => 'decimal:0',
        'check_in_at'   => 'datetime',
        'check_out_at'  => 'datetime',
        'created_at'    => 'datetime',
    ];

    public function cat()
    {
        return $this->belongsTo(Cat::class, 'cat_id');
    }
}