<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstimateResource\Pages;
use App\Models\Estimate;
use App\Services\AiQuoteService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EstimateResource extends Resource
{
    protected static ?string $model = Estimate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationLabel = 'Estimates';
    protected static ?string $navigationGroup = 'Estimates & Proposals';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('HVAC Estimate')
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
                    Forms\Components\TextInput::make('code')->maxLength(64)->unique(ignoreRecord: true),
                    Forms\Components\Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload(),
                    Forms\Components\Select::make('service_request_id')->relationship('serviceRequest', 'code')->searchable()->preload(),
                    Forms\Components\Select::make('job_ticket_id')->relationship('jobTicket', 'code')->searchable()->preload(),
                    Forms\Components\Select::make('technician_id')->relationship('technician', 'name')->searchable()->preload(),
                    Forms\Components\TextInput::make('equipment')->maxLength(255),
                    Forms\Components\TextInput::make('address')->maxLength(255)->columnSpan(2),
                    Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('tax')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('discount')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('total')->numeric()->prefix('$'),
                    Forms\Components\Select::make('status')->options([
                        'draft' => 'Draft',
                        'pending_approval' => 'Pending Approval',
                        'approved' => 'Approved',
                        'sent' => 'Sent',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'expired' => 'Expired',
                    ])->required(),
                    Forms\Components\TextInput::make('valid_days')->numeric()->default(30),
                    Forms\Components\DateTimePicker::make('issued_at'),
                    Forms\Components\DateTimePicker::make('valid_until'),
                    Forms\Components\DateTimePicker::make('approved_at'),
                    Forms\Components\TextInput::make('approved_by')->maxLength(120),
                    Forms\Components\Toggle::make('messaging_consent')->label('Messaging consent on file'),
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
                Tables\Columns\TextColumn::make('name')->searchable()->limit(36),
                Tables\Columns\TextColumn::make('customer.name')->toggleable(),
                Tables\Columns\TextColumn::make('total')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\IconColumn::make('messaging_consent')->boolean()->label('Consent'),
                Tables\Columns\TextColumn::make('valid_until')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'approved' => 'Approved',
                    'sent' => 'Sent',
                    'accepted' => 'Accepted',
                ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Estimate $record) => $record->status === 'draft')
                    ->action(function (Estimate $record) {
                        app(AiQuoteService::class)->approveEstimate($record, auth()->user()?->name ?? 'owner');
                        Notification::make()->title('Estimate approved')->success()->send();
                    }),
                Tables\Actions\Action::make('buildProposal')
                    ->label('AI Proposal')
                    ->icon('heroicon-o-document-text')
                    ->action(function (Estimate $record) {
                        app(AiQuoteService::class)->buildProposal($record, auth()->user()?->name ?? 'owner');
                        Notification::make()->title('Proposal drafted')->success()->send();
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
            'index' => Pages\ListEstimates::route('/'),
            'create' => Pages\CreateEstimate::route('/create'),
            'edit' => Pages\EditEstimate::route('/{record}/edit'),
        ];
    }
}
