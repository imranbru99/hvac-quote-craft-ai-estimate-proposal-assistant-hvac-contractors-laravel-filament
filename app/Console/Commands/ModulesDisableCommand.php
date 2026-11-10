<?php

namespace App\Console\Commands;

use App\Services\ModuleRegistry;
use Illuminate\Console\Command;

class ModulesDisableCommand extends Command
{
    protected $signature = 'modules:disable {key}';
    protected $description = 'Manage business modules for HvacQuoteCraft';

    public function handle(ModuleRegistry $modules): int
    {
        $modules->disable($this->argument('key')); $this->info('Disabled '.$this->argument('key'));

        return self::SUCCESS;
    }
}
