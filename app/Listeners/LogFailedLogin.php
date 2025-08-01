<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;

class LogFailedLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Failed  $event
     * @return void
     */
    public function handle(Failed $event)
{
    $now = new Carbon();

    try {
        $email = $event->credentials['email'] ?? 'desconhecido';

        DB::table('login_attempts')->insert([
            'username' => $email,
            'user_agent' => request()->header('User-Agent'),
            'remote_ip' => request()->ip(),
            'successful' => 0,
            'created_at' => $now,
        ]);
    } catch (\Exception $e) {
        Log::debug('Erro ao registar tentativa de login falhada: ' . $e->getMessage());
    }
}
}