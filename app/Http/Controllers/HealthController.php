<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

/**
 * This controller provide the health route  for
 * the Parque Seguro Asset Management application.
 *
 * @version   v1.0
 *
 * @return \Illuminate\Http\JsonResponse

 */
class HealthController extends BaseController
{
    /**
     * Returns a fixed JSON content ({ "status": "ok"}) which indicate the app is up and running
     */
    public function get()
    {
        return response()->json([
            'status' => 'ok',
        ]);
    }
}
