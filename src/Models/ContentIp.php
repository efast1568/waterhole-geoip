<?php

namespace Subarist\WaterholeGeoIp\Models;

use Illuminate\Database\Eloquent\Model;

class ContentIp extends Model
{
    protected $table = 'content_ip';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'content_type',
        'content_id',
        'ip_address',
    ];
}
