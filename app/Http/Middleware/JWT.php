<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWT
{

    public function handle($request, Closure $next)
    {


        try {
            //Access token from the request        
            $token = JWTAuth::parseToken();
            //Try authenticating user       
            $user = $token->authenticate();
        } catch (TokenExpiredException $e) {

            return $this->unauthorized('Token hasexpired');
        } catch (TokenInvalidException $e) {

            return $this->unauthorized('Token is invalid.');
        } catch (JWTException $e) {

            return $this->unauthorized('Token couldn\'t be found.');
        }
        return $next($request);

    }

    private function unauthorized($message = null)
    {
        return response()->json([
            'message' => $message ? $message : 'Erişim Yetkiniz Yok',
            'success' => false
        ], 401);
    }
}
