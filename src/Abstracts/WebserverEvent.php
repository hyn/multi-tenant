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

namespace Hyn\Tenancy\Abstracts;

use Hyn\Tenancy\Models\Website;

abstract class WebserverEvent extends AbstractEvent
{
    /**
     * @var Website
     */
    public $website;

    public function __construct(Website $website, string $service)
    {
        $this->website = $website;
    }
}
