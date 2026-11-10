<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Models\ServiceRequest;
use App\Services\AiQuoteService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Service Requests';
    protected static ?string $navigationGroup = 'Field Operations';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Service Request Intake')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\TextInput::make('code')->maxLength(64)->unique(ignoreRecord: true),
                Forms\Components\Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload(),
                Forms\Components\Select::make('priority')->options([
                    'low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'emergency' => 'Emergency',
                ])->default('normal'),
                Forms\Components\Select::make('status')->options([
                    'intake' => 'Intake', 'qualified' => 'Qualified', 'scheduled' => 'Scheduled',
                    'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled',
                ])->default('intake'),
                Forms\Components\TextInput::make('equipment')->placeholder('Rooftop unit / furnace / mini-split'),
                Forms\Components\TextInput::make('address')->columnSpan(2),
                Forms\Components\DateTimePicker::make('requested_at'),
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable()->limit(36),
            Tables\Columns\TextColumn::make('customer.name')->toggleable(),
            Tables\Columns\TextColumn::make('priority')->badge(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('equipment')->toggleable(),
            Tables\Columns\TextColumn::make('requested_at')->dateTime()->since(),
        ])->actions([
            Tables\Actions\Action::make('aiDraftEstimate')
                ->label('AI Draft Estimate')
                ->icon('heroicon-o-sparkles')
                ->color('primary')
                ->action(function (ServiceRequest $record) {
                    app(AiQuoteService::class)->draftEstimateFromIntake($record);
                    Notification::make()->title('Estimate drafted from intake')->success()->send();
                }),
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
