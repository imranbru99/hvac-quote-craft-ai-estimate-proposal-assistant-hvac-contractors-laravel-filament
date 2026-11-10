<?php

namespace App\Console\Commands;

use App\Services\ModuleRegistry;
use Illuminate\Console\Command;

class ModulesEnableCommand extends Command
{
    protected $signature = 'modules:enable {key}';
    protected $description = 'Manage business modules for HvacQuoteCraft';

    public function handle(ModuleRegistry $modules): int
    {
        $modules->enable($this->argument('key')); $this->info('Enabled '.$this->argument('key'));

        return self::SUCCESS;
    }
}
