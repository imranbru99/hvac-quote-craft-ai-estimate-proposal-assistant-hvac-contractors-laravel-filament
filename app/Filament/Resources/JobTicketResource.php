<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobTicketResource\Pages;
use App\Models\JobTicket;
use App\Services\AiBillingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobTicketResource extends Resource
{
    protected static ?string $model = JobTicket::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Job Tickets';
    protected static ?string $navigationGroup = 'Field Operations';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Job Ticket')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\TextInput::make('code')->maxLength(64)->unique(ignoreRecord: true),
                Forms\Components\Select::make('service_request_id')->relationship('serviceRequest', 'code')->searchable()->preload(),
                Forms\Components\Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload(),
                Forms\Components\Select::make('technician_id')->relationship('technician', 'name')->searchable()->preload(),
                Forms\Components\Select::make('status')->options([
                    'scheduled' => 'Scheduled', 'en_route' => 'En Route', 'on_site' => 'On Site',
                    'completed' => 'Completed', 'invoiced' => 'Invoiced',
                ])->default('scheduled'),
                Forms\Components\Select::make('priority')->options([
                    'low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'emergency' => 'Emergency',
                ])->default('normal'),
                Forms\Components\TextInput::make('labor_hours')->numeric(),
                Forms\Components\TextInput::make('parts_total')->numeric()->prefix('$'),
                Forms\Components\TextInput::make('labor_total')->numeric()->prefix('$'),
                Forms\Components\DateTimePicker::make('scheduled_at'),
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')->limit(32)->searchable(),
            Tables\Columns\TextColumn::make('technician.name')->label('Tech')->toggleable(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('labor_total')->money('USD'),
            Tables\Columns\TextColumn::make('parts_total')->money('USD'),
            Tables\Columns\TextColumn::make('scheduled_at')->dateTime()->since(),
        ])->actions([
            Tables\Actions\Action::make('aiInvoice')
                ->label('AI Draft Invoice')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->visible(fn (JobTicket $record) => $record->status === 'completed' || $record->status === 'invoiced' || true)
                ->action(function (JobTicket $record) {
                    $invoice = app(AiBillingService::class)->draftInvoiceFromJob($record);
                    $record->update(['status' => 'invoiced']);
                    Notification::make()->title('Draft invoice '.$invoice->code.' created')->success()->send();
                }),
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobTickets::route('/'),
            'create' => Pages\CreateJobTicket::route('/create'),
            'edit' => Pages\EditJobTicket::route('/{record}/edit'),
        ];
    }
}
