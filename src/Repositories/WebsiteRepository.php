<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/hyn/multi-tenant
 * @see https://hyn.me
 * @see https://patreon.com/tenancy
 */

namespace Hyn\Tenancy\Repositories;

use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository as Contract;
use Hyn\Tenancy\Events\Websites as Events;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Traits\DispatchesEvents;
use Hyn\Tenancy\Validators\WebsiteValidator;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Database\Eloquent\Builder;

class WebsiteRepository implements Contract
{
    use DispatchesEvents;
    /**
     * @var Website
     */
    protected $website;
    /**
     * @var WebsiteValidator
     */
    protected $validator;
    /**
     * @var Factory
     */
    protected $cache;

    /**
     * WebsiteRepository constructor.
     * @param Website $website
     * @param WebsiteValidator $validator
     * @param Factory $cache
     */
    public function __construct(Website $website, WebsiteValidator $validator, Factory $cache)
    {
        $this->website = $website;
        $this->validator = $validator;
        $this->cache = $cache;
    }

    /**
     * @param string $uuid
     * @return Website|null
     */
    public function findByUuid(string $uuid): ?Website
    {
        return $this->cache->remember("tenancy.website.$uuid", config('tenancy.website.cache'), function () use ($uuid) {
            return $this->website->newQuery()->where('uuid', $uuid)->first();
        });
    }

    /**
     * @param Website $website
     * @return Website
     */
    public function create(Website &$website): Website
    {
        if ($website->exists) {
            return $this->update($website);
        }

        $this->emitEvent(
            new Events\Creating($website)
        );

        $this->validator->save($website);

        $website->save();

        $this->cache->flush("tenancy.website.{$website->uuid}");

        $this->emitEvent(
            new Events\Created($website)
        );

        return $website;
    }

    /**
     * @param Website $website
     * @return Website
     */
    public function update(Website &$website): Website
    {
        if (!$website->exists) {
            return $this->create($website);
        }

        $this->emitEvent(
            new Events\Updating($website)
        );

        $this->validator->save($website);

        $dirty = $website->getDirty();

        $website->save();

        $this->cache->flush("tenancy.website.{$website->uuid}");

        $this->emitEvent(
            new Events\Updated($website, $dirty)
        );

        return $website;
    }

    /**
     * @param Website $website
     * @param bool $hard
     * @return Website
     */
    public function delete(Website &$website, $hard = false): Website
    {
        $this->emitEvent(
            new Events\Deleting($website)
        );

        $this->validator->delete($website);

        $hard ? $website->forceDelete() : $website->delete();

        $this->cache->flush("tenancy.website.{$website->uuid}");

        $this->emitEvent(
            new Events\Deleted($website)
        );

        return $website;
    }

    /**
     * @warn Only use for querying.
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->website->newQuery();
    }
}
