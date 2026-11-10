<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditEventResource\Pages;
use App\Models\AuditEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditEventResource extends Resource
{
    protected static ?string $model = AuditEvent::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Audit Log';
    protected static ?string $navigationGroup = 'Governance';
    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Audit Event')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('entity_type'),
                Forms\Components\TextInput::make('entity_id')->numeric(),
                Forms\Components\TextInput::make('action'),
                Forms\Components\TextInput::make('actor'),
                Forms\Components\Textarea::make('notes')->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->searchable(),
            Tables\Columns\TextColumn::make('action')->badge(),
            Tables\Columns\TextColumn::make('entity_type'),
            Tables\Columns\TextColumn::make('entity_id'),
            Tables\Columns\TextColumn::make('actor'),
            Tables\Columns\TextColumn::make('notes')->limit(40),
            Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
        ])->actions([
            Tables\Actions\ViewAction::make(),
        ])->bulkActions([]);
    }

    public static function canCreate(): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditEvents::route('/'),
        ];
    }
}
