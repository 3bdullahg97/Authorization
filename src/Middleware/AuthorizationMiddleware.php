<?php

namespace Luqta\Authorization\Middleware;

use Closure;
use Lcobucci\JWT\Parser;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if ($request->hasHeader('Authorization')) {
            $token = explode(' ', $request->header('Authorization'))[1];
            $parser = new Parser();
            $jwt = $parser->parse($token);

            if (in_array($jwt->getClaim('user_role'), $roles)) {
                $request->merge([
                    'user_id' => $jwt->getClaim('sub'),
                    'user_role' => $jwt->getClaim('user_role'),

                ]);
                return $next($request);
            }
        }
        return response('Unauthorized', 401);

    }
}
