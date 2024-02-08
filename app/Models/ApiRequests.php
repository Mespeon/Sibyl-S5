<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiRequests extends Model {
    use SoftDeletes;

    protected $fillable = [
        'endpoint',
        'method',
        'request_ip',
        'request_header',
        'request_body',
        'agent',
        'response',
        'response_status'
    ];

    protected $hidden = ['deleted_at'];

    public function setNewApiRequestLog($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        $this->save();
    }
}
