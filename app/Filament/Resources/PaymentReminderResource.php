<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentReminderResource\Pages;
use App\Models\PaymentReminder;
use App\Services\AiBillingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentReminderResource extends Resource
{
    protected static ?string $model = PaymentReminder::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Payment Reminders';
    protected static ?string $navigationGroup = 'Billing (Optional)';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Payment Reminder')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\TextInput::make('code')->maxLength(64)->unique(ignoreRecord: true),
                Forms\Components\Select::make('invoice_id')->relationship('invoice', 'code')->searchable()->preload(),
                Forms\Components\Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload(),
                Forms\Components\Select::make('channel')->options([
                    'email' => 'Email', 'sms' => 'SMS', 'whatsapp' => 'WhatsApp',
                ])->default('email'),
                Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft', 'pending_approval' => 'Pending Approval', 'sent' => 'Sent', 'cancelled' => 'Cancelled',
                ])->default('draft'),
                Forms\Components\DateTimePicker::make('scheduled_for'),
                Forms\Components\Toggle::make('consent_ok')->label('Consent verified'),
                Forms\Components\Textarea::make('body')->rows(5)->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('invoice.code')->label('Invoice'),
            Tables\Columns\TextColumn::make('channel')->badge(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\IconColumn::make('consent_ok')->boolean()->label('Consent'),
            Tables\Columns\TextColumn::make('scheduled_for')->dateTime()->since(),
            Tables\Columns\TextColumn::make('sent_at')->dateTime()->since()->toggleable(),
        ])->actions([
            Tables\Actions\Action::make('approveSend')
                ->label('Approve & Send')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn (PaymentReminder $record) => $record->status === 'draft')
                ->action(function (PaymentReminder $record) {
                    try {
                        app(AiBillingService::class)->approveAndSendReminder($record, auth()->user()?->name ?? 'owner');
                        Notification::make()->title('Reminder sent')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentReminders::route('/'),
            'create' => Pages\CreatePaymentReminder::route('/create'),
            'edit' => Pages\EditPaymentReminder::route('/{record}/edit'),
        ];
    }
}
