<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (gs('maintenance_mode') == 1) {

            if ($request->is('api/*')) {
                if($request->is('api/section-data/maintenance')){
                    return $next($request);
                }
                return response()->json([
                    'remark'=>'maintenance_mode',
                    'status'=>'error',
                    'message'=>['error'=> 'Our application is currently in maintenance mode.']
                ]);
            }else{
                return to_route('maintenance');
            }
        }
        return $next($request);
    }
}
