<?php

namespace App\Filament\Resources;

use App\Enums\UserPaymentStatusEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action as ActionInfolist;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPluralLabel(): string
    {
        return __('Siswa');
    }

    public static function getModelLabel(): string
    {
        return __('Siswa');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_payment.registration_number')->label('No. Pendaftaran')->searchable(),
                TextColumn::make('name')->label('Nama Lengkap')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('user_payment.created_at')->label('Daftar Pada'),
                TextColumn::make('user_payment.status')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()->schema([
                    Section::make()->columns(['sm' => 3])->schema([
                        TextEntry::make('user_payment.status')
                            ->label('Status')
                            ->badge(),
                        TextEntry::make('user_payment.registration_number')->label('No. Pendaftaran'),
                        TextEntry::make('user_payment.created_at')
                            ->label('Tanggal Pembayaran')
                            ->dateTime('d M Y H:i'),
                    ]),
                    Section::make()->columns(['sm' => 3])->schema([
                        TextEntry::make('name')->label('Nama Lengkap'),
                        TextEntry::make('email'),
                    ]),
                    Section::make()->columns(['sm' => 3])->schema([
                        ImageEntry::make('user_payment.proof_of_payment')->label('Bukti Pembayaran'),
                    ]),
                ]),
                Actions::make([
                    ActionInfolist::make('confirm')
                        ->label('Konfirmasi Pembayaran')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->action(function (User $record) {
                            $record->user_payment->status = 'confirmed';
                            $record->user_payment->save();
                        })
                        ->visible(fn($record) => $record->user_payment->status == UserPaymentStatusEnum::WAITING_CONFIRMATION)
                        ->requiresConfirmation(),
                    ActionInfolist::make('reject')
                        ->label('Tolak Pembayaran')
                        ->icon('heroicon-m-x-mark')
                        ->color('danger')
                        ->action(function (User $record) {
                            $record->user_payment->status = 'rejected';
                            $record->user_payment->save();
                        })
                        ->visible(fn($record) => $record->user_payment->status == UserPaymentStatusEnum::WAITING_CONFIRMATION)
                        ->requiresConfirmation()
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('email', 'not like', '%admin%');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
            /* 'create' => Pages\CreateUser::route('/create'), */
            /* 'edit' => Pages\EditUser::route('/{record}/edit'), */
        ];
    }
}
