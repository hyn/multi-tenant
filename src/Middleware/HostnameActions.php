<?php

namespace Hyn\Tenancy\Middleware;

use Closure;
use Hyn\Tenancy\Contracts\CurrentHostname;
use Hyn\Tenancy\Events\Hostnames\Redirected;
use Hyn\Tenancy\Events\Hostnames\Secured;
use Hyn\Tenancy\Events\Hostnames\UnderMaintenance;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Traits\DispatchesEvents;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class HostnameActions
{
    use DispatchesEvents;
    /**
     * @var CurrentHostname|Hostname
     */
    protected $hostname;

    /**
     * @var Redirector
     */
    protected $redirect;

    /**
     * @param CurrentHostname $hostname
     * @param Redirector $redirect
     */
    public function __construct(CurrentHostname $hostname, Redirector $redirect)
    {
        $this->hostname = $hostname;
        $this->redirect = $redirect;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->hostname) {
            if ($this->hostname->under_maintenance_since) {
                return $this->maintenance($this->hostname);
            }

            if ($this->hostname->redirect_to) {
                return $this->redirect($this->hostname);
            }

            if (!$request->secure() && $this->hostname->force_https) {
                return $this->secure($this->hostname, $request);
            }
        }

        return $next($request);
    }

    /**
     * @param Hostname $hostname
     * @return RedirectResponse
     */
    protected function redirect(Hostname $hostname)
    {
        $this->emitEvent(new Redirected($hostname));

        return $this->redirect->away($hostname->redirect_to);
    }

    /**
     * @param Hostname $hostname
     * @param Request $request
     * @return RedirectResponse
     */
    protected function secure(Hostname $hostname, Request $request)
    {
        $this->emitEvent(new Secured($hostname));

        return $this->redirect->secure($request->path());
    }

    /**
     * @param Hostname $hostname
     */
    protected function maintenance(Hostname $hostname)
    {
        $this->emitEvent(new UnderMaintenance($hostname));

        throw new MaintenanceModeException($hostname->under_maintenance_since->timestamp);
    }
}
