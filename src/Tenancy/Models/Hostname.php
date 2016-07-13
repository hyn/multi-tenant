<?php

namespace Hyn\Tenancy\Models;

use Carbon\Carbon;
use Hyn\Tenancy\Abstracts\Models\SystemModel;
use Hyn\Webserver\Models\SslCertificate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;
use Laracasts\Presenter\PresentableTrait;

/**
 * @property string         $hostname
 * @property bool           $prefer_https
 * @property int            $redirect_to
 * @property int            $sub_of
 * @property int            $website_id
 * @property int            $tenant_id
 * @property int            $ssl_certificate_id
 * @property Customer       $customer
 * @property Website        $website
 * @property Hostname       $redirectToHostname
 * @property Hostname       $subDomainOf
 * @property Collection     $subDomains
 * @property SslCertificate $certificate
 * @property Carbon         $created_at
 * @property Carbon         $updated_at
 * @property Carbon         $deleted_at
 */
class Hostname extends SystemModel
{
    use PresentableTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = 'Hyn\Tenancy\Presenters\HostnamePresenter';

    /**
     * @var array
     */
    protected $fillable = ['website_id', 'hostname', 'redirect_to', 'prefer_https', 'sub_of', 'tenant_id'];

    protected $appends = [];

    /**
     * The tenant who owns this hostname.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * The website this hostname is connected to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Host to redirect to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function redirectToHostname()
    {
        return $this->belongsTo(static::class, 'redirect_to');
    }

    /**
     * Host this is a sub domain of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subDomainOf()
    {
        return $this->belongsTo(static::class, 'sub_of');
    }

    /**
     * Sub domains of this hostname.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subDomains()
    {
        return $this->hasMany(static::class, 'sub_of');
    }

    /**
     * Certificate this hostname uses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function certificate()
    {
        return $this->belongsTo(SslCertificate::class, 'ssl_certificate_id');
    }

    /**
     * Identifies whether a redirect is required for this hostname.
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function redirectActionRequired()
    {
        // force to new hostname
        if ($this->redirect_to) {
            return $this->redirectToHostname->redirectActionRequired();
        }
        // figure out whether we need a redirect to https
        if ($this->prefer_https && ! Request::secure()) {
            return redirect()->secure(Request::path());
        }

        // if default hostname is loaded and this is not the default hostname
        if (Request::getHttpHost() != $this->hostname) {
            return redirect()->away("http://{$this->hostname}/".(Request::path() == '/' ? null : Request::path()));
        }
    }
}
