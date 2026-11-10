<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalFollowupResource\Pages;
use App\Models\ProposalFollowup;
use App\Services\AiQuoteService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProposalFollowupResource extends Resource
{
    protected static ?string $model = ProposalFollowup::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Proposal Follow-ups';
    protected static ?string $navigationGroup = 'Estimates & Proposals';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Proposal Follow-up')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\TextInput::make('code')->maxLength(64)->unique(ignoreRecord: true),
                Forms\Components\Select::make('proposal_id')->relationship('proposal', 'code')->searchable()->preload(),
                Forms\Components\Select::make('estimate_id')->relationship('estimate', 'code')->searchable()->preload(),
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('proposal.code')->label('Proposal')->toggleable(),
                Tables\Columns\TextColumn::make('channel')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\IconColumn::make('consent_ok')->boolean()->label('Consent'),
                Tables\Columns\TextColumn::make('scheduled_for')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approveSend')
                    ->label('Approve & Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (ProposalFollowup $record) => $record->status === 'draft')
                    ->action(function (ProposalFollowup $record) {
                        try {
                            app(AiQuoteService::class)->approveAndSendFollowup($record, auth()->user()?->name ?? 'owner');
                            Notification::make()->title('Follow-up sent')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
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
            'index' => Pages\ListProposalFollowups::route('/'),
            'create' => Pages\CreateProposalFollowup::route('/create'),
            'edit' => Pages\EditProposalFollowup::route('/{record}/edit'),
        ];
    }
}
