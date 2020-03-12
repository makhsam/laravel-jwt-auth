<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Session extends Model
{
    protected $fillable = [
        'user_id', 'ip', 'user_agent', 'os', 'os_version', 'browser', 'browser_version', 'device', 'device_type',
    ];

    protected $dates = [
        'create_time', 'signed_in_time'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;


    public static function getSessionId(Request $request) 
    {
        // Create new session in case user signs up again from the same browser
        $session = self::where('id', $request->input('session.id'))
                       ->where('user_id', $request->input('user_id'))->first();

        if ($session) {
            $session->signed_in_time = Carbon::now();
            $session->save();

            return $session->id;
        }

        // Else, create new session and return it
        $session = new self;
        $session->id = uniqid();
        $session->user_id = $request->input('user_id');
        $session->ip = $request->ip();
        $session->user_agent = $request->input('session.user_agent');
        $session->os = $request->input('session.os');
        $session->os_version = $request->input('session.os_version');
        $session->browser = $request->input('session.browser');
        $session->browser_version = $request->input('session.browser_version');
        $session->device = $request->input('session.device');
        $session->device_type = $request->input('session.device_type'); // mobile, tablet, desktop

        $session->create_time = Carbon::now();
        $session->signed_in_time = Carbon::now();

        $session->save();
        return $session->id;
    }

}
