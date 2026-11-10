<?php

namespace App\Services;

class ModuleRegistry
{
    public function all(): array
    {
        return config('modules', []);
    }

    public function enabled(string $key): bool
    {
        $module = config("modules.{$key}");

        return is_array($module) && (bool) ($module['enabled'] ?? false);
    }

    public function enable(string $key): void
    {
        $this->write($key, true);
    }

    public function disable(string $key): void
    {
        $module = config("modules.{$key}");
        if (is_array($module) && ($module['locked'] ?? false)) {
            throw new \RuntimeException("Module [{$key}] is locked.");
        }
        $this->write($key, false);
    }

    protected function write(string $key, bool $enabled): void
    {
        $modules = $this->all();
        if (! isset($modules[$key])) {
            throw new \InvalidArgumentException("Unknown module [{$key}]");
        }
        $modules[$key]['enabled'] = $enabled;
        file_put_contents(config_path('modules.php'), "<?php\n\nreturn ".var_export($modules, true).";\n");
    }

    public function navigation(): array
    {
        return collect($this->all())
            ->filter(fn ($m) => $m['enabled'] ?? false)
            ->map(fn ($m, $key) => ['key' => $key] + $m)
            ->values()
            ->all();
    }
}
