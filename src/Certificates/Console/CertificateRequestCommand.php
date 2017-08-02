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

namespace Hyn\Tenancy\Certificates\Console;

use AcmePhp\Core\AcmeClient;
use AcmePhp\Core\Exception\Server\MalformedServerException;
use AcmePhp\Ssl\CertificateRequest;
use AcmePhp\Ssl\DistinguishedName;
use AcmePhp\Ssl\Generator\KeyPairGenerator;
use Hyn\Tenancy\Certificates\Solvers\TenancyHttpSolver;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Exceptions\CertificateRequestFailure;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Console\Command;

class CertificateRequestCommand extends Command
{
    protected $signature = 'tenancy:certificate {website}';

    protected $description = 'Requests new Lets Encrypt certificate for tenant';

    public function handle(AcmeClient $acme, WebsiteRepository $websites)
    {
        $website = $this->argument('website');

        $website = $websites->findByUuid($website);

        if (!$website) {
            throw new \InvalidArgumentException("Invalid website");
        }

        try {
            $data = $acme->registerAccount();
        } catch (MalformedServerException $e) {
        }

        $solver = new TenancyHttpSolver();

        /** @var Hostname $commonName */
        $commonName = $website->hostnames->first();

        $challenges = $acme->requestAuthorization($commonName->fqdn);

        $challenge = collect($challenges)->first(function ($challenge) use ($solver) {
            return $solver->supports($challenge);
        });

        $solver->solve($challenge);

        $check = $acme->challengeAuthorization($challenge);

        if (!isset($check['status']) || $check['status'] !== 'valid') {
            throw new CertificateRequestFailure();
        }

        $name = new DistinguishedName(
            $commonName->fqdn,
            null, null, null, null, null,
            $website->customer ? $website->customer->email : null,
            $website->hostnames->reject(function ($hostname) use ($commonName) {
                return $hostname->fqdn === $commonName->fqdn;
            })->pluck('fqdn')
        );

        $csr = new CertificateRequest(
            $name,
            $keyPair = (new KeyPairGenerator())->generateKeyPair()
        );

        $response = $acme->requestCertificate($commonName->fqdn, $csr);

        $certificate = $response->getCertificate();
    }
}
