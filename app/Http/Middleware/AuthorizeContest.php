<?php

namespace App\Http\Middleware;

use App\Repositories\ContestRepository;
use Illuminate\Http\Request;

class AuthorizeContest
{
    /**
     * @param Request $request
     * @param         $next
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, $next)
    {
        $route = app('router')->getRoutes()->match($request);
        $contest = $route->parameter('contest');

        /** @var \App\Entities\Contest $contest */
        $contest = app(ContestRepository::class)->find($contest);

        if (!$contest->isAvailable()) {
            return back()->withErrors('Contest not found!');
        }

        if ($contest->isPublic() || can_attend($contest)) {
            return $next($request);
        }

        $errorMessage = 'You do not have privilege access contest '.$contest->id;

        return redirect(route('contest.index'))->withErrors($errorMessage);
    }
}
