<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalResource\Pages;
use App\Models\Proposal;
use App\Services\AiQuoteService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Proposals';
    protected static ?string $navigationGroup = 'Estimates & Proposals';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Customer Proposal')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\TextInput::make('code')->maxLength(64)->unique(ignoreRecord: true),
                Forms\Components\Select::make('estimate_id')->relationship('estimate', 'code')->searchable()->preload(),
                Forms\Components\Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('title')->maxLength(255)->columnSpan(2),
                Forms\Components\TextInput::make('total')->numeric()->prefix('$'),
                Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft',
                    'pending_approval' => 'Pending Approval',
                    'approved' => 'Approved',
                    'sent' => 'Sent',
                    'viewed' => 'Viewed',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                ])->default('draft'),
                Forms\Components\Textarea::make('summary')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('scope_of_work')->rows(5)->columnSpanFull(),
                Forms\Components\Textarea::make('terms')->rows(4)->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->limit(36)->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->toggleable(),
                Tables\Columns\TextColumn::make('total')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('sent_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approveSend')
                    ->label('Approve & Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (Proposal $record) => in_array($record->status, ['draft', 'approved'], true))
                    ->action(function (Proposal $record) {
                        app(AiQuoteService::class)->approveAndSendProposal($record, auth()->user()?->name ?? 'owner');
                        Notification::make()->title('Proposal sent')->success()->send();
                    }),
                Tables\Actions\Action::make('draftFollowup')
                    ->label('AI Follow-up')
                    ->icon('heroicon-o-bell-alert')
                    ->action(function (Proposal $record) {
                        app(AiQuoteService::class)->draftFollowup($record);
                        Notification::make()->title('Follow-up drafted')->success()->send();
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
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }
}
