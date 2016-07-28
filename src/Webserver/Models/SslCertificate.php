<?php

namespace Hyn\Webserver\Models;

use Cache;
use Config;
use Hyn\Tenancy\Abstracts\Models\SystemModel;
use Hyn\Tenancy\Models\Customer;
use Hyn\Webserver\Tools\CertificateParser;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class SslCertificate.
 */
class SslCertificate extends SystemModel
{
    use PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'Hyn\Webserver\Presenters\SslCertificatePresenter';

    /**
     * @var array
     */
    protected $fillable = ['tenant_id', 'certificate', 'authority_bundle', 'key'];

    /**
     * @var array
     */
    protected $appends = ['pathKey', 'pathPem', 'pathCrt', 'pathCa'];

    /**
     * @return array
     */
    public function getDates()
    {
        return ['validates_at', 'invalidates_at'];
    }

    public function getIsExpired()
    {
        return $this->invalidates_at ? $this->invalidates_at->isPast() : null;
    }

    /**
     * @return CertificateParser|null
     */
    public function getX509Attribute()
    {
        if (! Cache::has('ssl-x509-'.$this->id)) {
            Cache::add('ssl-x509-'.$this->id, $this->certificate ? new CertificateParser($this->certificate) : null, 3600);
        }

        return Cache::get('ssl-x509-'.$this->id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hostnames()
    {
        return $this->hasMany(SslHostname::class);
    }

    /**
     * @return string
     */
    public function getPathKeyAttribute()
    {
        return $this->publishPath('key');
    }

    /**
     * @param string $postfix
     *
*@return string
     */
    public function publishPath($postfix = 'key')
    {
        return sprintf('%s/%s/certificate.%s', Config::get('webserver.ssl.path'), $this->id, $postfix);
    }

    /**
     * @return string
     */
    public function getPathPemAttribute()
    {
        return $this->publishPath('pem');
    }

    /**
     * @return string
     */
    public function getPathCrtAttribute()
    {
        return $this->publishPath('crt');
    }

    /**
     * @return string
     */
    public function getPathCaAttribute()
    {
        return $this->publishPath('ca');
    }
}
