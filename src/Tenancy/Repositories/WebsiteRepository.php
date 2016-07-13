<?php

namespace Hyn\Tenancy\Repositories;

use Hyn\Framework\Repositories\BaseRepository;
use Hyn\Tenancy\Contracts\WebsiteRepositoryContract;
use Hyn\Tenancy\Models\Hostname;

class WebsiteRepository extends BaseRepository implements WebsiteRepositoryContract
{
    /**
     * @var \Hyn\Tenancy\Models\Website
     */
    protected $website;

    /**
     * @var \Hyn\Tenancy\Contracts\HostnameRepositoryContract
     */
    protected $hostname;

    /**
     * @param Hostname $hostname
     *
     * @return \Hyn\Tenancy\Models\Website
     */
    public function findByHostname(Hostname $hostname)
    {
        return $hostname->website;
    }

    /**
     * Return default website.
     *
     * @return \Hyn\Tenancy\Models\Website
     */
    public function getDefault()
    {
        return $this->hostname->getDefault()->website;
    }

    /**
     * Create a pagination object.
     *
     * @param int $per_page
     *
     * @return mixed
     */
    public function paginated($per_page = 20)
    {
        return $this->website->paginate($per_page);
    }
}
