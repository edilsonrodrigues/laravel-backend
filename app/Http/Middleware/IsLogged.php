<?php

namespace App\Http\Middleware;

use App\Core\Library\JWT;
use Carbon\Carbon;
use Closure;

class IsLogged
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            // return $next($request);
            $headerAuth = $request->bearerToken();
            $rsJWT = JWT::decode($headerAuth, env('APP_KEY'), false);
            if ($rsJWT) {
                //              $dateNow = Carbon::now(new \DateTimeZone('America/Sao_Paulo'));
                //              $dateHash = Carbon::createFromTimestamp($rsJWT->exp);

                //              if($dateNow->isAfter($dateHash)){
                //                  return response()->json(['message'=>'Autorization expirada!']);
                //              }

                return $next($request);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'login jwt não autorizado!'], 400);
        }
    }
}
