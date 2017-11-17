<?php

namespace Hyn\Tenancy\Commands;

use Hyn\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Hyn\Tenancy\Traits\DispatchesEvents;
use Hyn\Tenancy\Events\Websites as Events;

class RestoreCommand extends Command
{
    use DispatchesEvents;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenancy:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore all tenant databases present in the system db.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $websites = Website::all();

        foreach ($websites as $website) {
            $this->emitEvent(new Events\Created($website));
        }
    }
}
