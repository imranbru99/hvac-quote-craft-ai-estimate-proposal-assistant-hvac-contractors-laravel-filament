<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\AiBillingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Invoices';
    protected static ?string $navigationGroup = 'Billing (Optional)';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('HVAC Invoice')
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
                    Forms\Components\TextInput::make('code')->maxLength(64)->unique(ignoreRecord: true),
                    Forms\Components\Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload(),
                    Forms\Components\Select::make('job_ticket_id')->relationship('jobTicket', 'code')->searchable()->preload(),
                    Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('tax')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('total')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('amount_paid')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('amount_due')->numeric()->prefix('$'),
                    Forms\Components\Select::make('status')->options([
                        'draft' => 'Draft',
                        'pending_approval' => 'Pending Approval',
                        'approved' => 'Approved',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'void' => 'Void',
                    ])->required(),
                    Forms\Components\Select::make('payment_status')->options([
                        'unpaid' => 'Unpaid',
                        'partial' => 'Partial',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ]),
                    Forms\Components\DateTimePicker::make('issued_at'),
                    Forms\Components\DateTimePicker::make('due_at'),
                    Forms\Components\DateTimePicker::make('approved_at'),
                    Forms\Components\TextInput::make('approved_by')->maxLength(120),
                    Forms\Components\Toggle::make('reminder_consent')->label('Messaging consent on file'),
                    Forms\Components\Textarea::make('ai_draft')->rows(6)->columnSpanFull()->label('AI draft'),
                    Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->limit(32),
                Tables\Columns\TextColumn::make('customer.name')->toggleable(),
                Tables\Columns\TextColumn::make('total')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('amount_due')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('payment_status')->badge()->sortable(),
                Tables\Columns\IconColumn::make('reminder_consent')->boolean()->label('Consent'),
                Tables\Columns\TextColumn::make('due_at')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'approved' => 'Approved',
                    'paid' => 'Paid',
                ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Invoice $record) => $record->status === 'draft')
                    ->action(function (Invoice $record) {
                        app(AiBillingService::class)->approveInvoice($record, auth()->user()?->name ?? 'owner');
                        Notification::make()->title('Invoice approved')->success()->send();
                    }),
                Tables\Actions\Action::make('reconcile')
                    ->icon('heroicon-o-currency-dollar')
                    ->form([
                        Forms\Components\TextInput::make('amount')->numeric()->required()->prefix('$'),
                    ])
                    ->action(function (Invoice $record, array $data) {
                        app(AiBillingService::class)->reconcilePayment($record, (float) $data['amount']);
                        Notification::make()->title('Payment reconciled')->success()->send();
                    }),
                Tables\Actions\Action::make('draftReminder')
                    ->label('AI Reminder')
                    ->icon('heroicon-o-bell-alert')
                    ->action(function (Invoice $record) {
                        app(AiBillingService::class)->draftReminder($record);
                        Notification::make()->title('Reminder drafted')->success()->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
