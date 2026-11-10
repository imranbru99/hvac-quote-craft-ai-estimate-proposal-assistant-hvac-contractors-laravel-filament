<?php

namespace App\Console\Commands;

use App\Services\ModuleRegistry;
use Illuminate\Console\Command;

class ModulesListCommand extends Command
{
    protected $signature = 'modules:list';
    protected $description = 'Manage business modules for HvacQuoteCraft';

    public function handle(ModuleRegistry $modules): int
    {
        $rows = collect($modules->all())->map(fn ($m, $key) => [$key, $m['name'], ($m['enabled'] ?? false) ? 'ON' : 'OFF', ($m['locked'] ?? false) ? 'yes' : 'no'])->values()->all();
        $this->table(['Key', 'Name', 'Status', 'Locked'], $rows);

        return self::SUCCESS;
    }
}
