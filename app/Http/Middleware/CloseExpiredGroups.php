<?php

namespace App\Http\Middleware;

use App\Services\GroupService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CloseExpiredGroups
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && Schema::hasTable('bulk_groups')) {
            app(GroupService::class)->closeExpired();
        }

        return $next($request);
    }
}
