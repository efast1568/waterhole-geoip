<?php

namespace Subarist\WaterholeGeoIp\Models;

use Illuminate\Database\Eloquent\Model;

class IpInfo extends Model
{
    protected $table = 'ip_info';

    protected $primaryKey = 'ip_address';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ip_address',
        'country_code',
    ];
}
