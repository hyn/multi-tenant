<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://laravel-tenancy.com
 * @see https://github.com/hyn/multi-tenant
 */

namespace Hyn\Tenancy\Controllers;

use Hyn\Tenancy\Website\Directory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class MediaController
 *
 * @package Hyn\Tenancy\Controllers
 * @use Route::get('/media/{path}', Hyn\Tenancy\Controllers\MediaController::class)
 *          ->where('path', '.+')
 *          ->name('tenant.media');
 */
class MediaController
{
    /**
     * @var Directory
     */
    private $directory;

    public function __construct(Directory $directory)
    {
        $this->directory = $directory;
    }

    public function __invoke(string $path)
    {
        if ($this->directory->exists($path)) {
            return new BinaryFileResponse(
                $this->directory->get($path)
            );
        }

        return abort(404);
    }
}
