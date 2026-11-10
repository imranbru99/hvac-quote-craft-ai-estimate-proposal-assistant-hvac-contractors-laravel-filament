<?php

namespace App\Filament\Pages;

use App\Services\ModuleRegistry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ModuleControl extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static string $view = 'filament.pages.module-control';
    protected static ?string $navigationGroup = 'Platform';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Module Control';

    public ?array $data = [];

    public function mount(ModuleRegistry $modules): void
    {
        $state = [];
        foreach ($modules->all() as $key => $module) {
            $state[$key] = (bool) ($module['enabled'] ?? false);
        }
        $this->form->fill($state);
    }

    public function form(Form $form): Form
    {
        $modules = app(ModuleRegistry::class);
        $schema = [];
        foreach ($modules->all() as $key => $module) {
            $schema[] = Forms\Components\Toggle::make($key)
                ->label($module['name'])
                ->helperText($module['description'] ?? '')
                ->disabled((bool) ($module['locked'] ?? false));
        }

        return $form->schema($schema)->columns(2)->statePath('data');
    }

    public function save(ModuleRegistry $modules): void
    {
        foreach ($this->form->getState() as $key => $enabled) {
            try {
                $enabled ? $modules->enable($key) : $modules->disable($key);
            } catch (\Throwable $e) {
                Notification::make()->danger()->title($e->getMessage())->send();
            }
        }
        Notification::make()->success()->title('Modules updated')->send();
    }
}
