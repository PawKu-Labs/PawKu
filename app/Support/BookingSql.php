<?php

namespace App\Support;

class BookingSql
{
    /** Tanggal booking: slot_date, atau tanggal check_in_at (hotel) dalam WIB. */
    public const DATE = "coalesce(bookings.slot_date, (bookings.check_in_at at time zone 'Asia/Jakarta')::date)";

    /** Status yang tidak dihitung di laporan mobile. */
    public const EXCLUDED = ['dibatalkan', 'ditolak', 'kedaluwarsa'];
}