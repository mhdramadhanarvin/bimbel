<?php

namespace App\Filament\Resources;

use App\Enums\UserPaymentStatusEnum;
use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Exports\Enums\ExportFormat;
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
use Illuminate\Database\Eloquent\Model;

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
                TextColumn::make('userPayment.registration_number')->label('No. Pendaftaran')->searchable()->placeholder('-'),
                TextColumn::make('name')->label('Nama Lengkap')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('userPayment.created_at')->label('Daftar Pada')->getStateUsing(fn($record) => Carbon::parse($record->created_at, 'Asia/Jakarta')->format('d F Y')),
                TextColumn::make('')->label('Akhir Belajar')->getStateUsing(fn($record) => Carbon::parse($record->created_at, 'Asia/Jakarta')->addMonth()->format('d F Y')),
                TextColumn::make('userPayment.status')
                    ->label('Status')
                    ->badge()
                    ->placeholder('Belum Melakukan Pendaftaran')
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->modifyQueryUsing(fn(Builder $query) => $query->leftJoinRelationship('userPayment')->where('user_payments.status', UserPaymentStatusEnum::APPROVED))
                    ->label('Export Data')
                    ->columnMapping(false)
                    ->formats([
                        ExportFormat::Xlsx
                    ])
            ])
            ->bulkActions([
                /* Tables\Actions\BulkActionGroup::make([ */
                /* Tables\Actions\DeleteBulkAction::make(), */
                /* ]), */
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()->schema([
                    Section::make()->columns(['sm' => 4])->schema([
                        TextEntry::make('userPayment.status')
                            ->label('Status')
                            ->badge()
                            ->placeholder('Belum Melakukan Pembayaran'),
                        TextEntry::make('userPayment.registration_number')->label('No. Pendaftaran')->placeholder('-'),
                        TextEntry::make('userPayment.created_at')
                            ->label('Tanggal Pembayaran')
                            ->dateTime('d M Y H:i')
                            ->placeholder('2000/01/01'),
                        TextEntry::make('userPayment.created_at')
                            ->label('Tanggal Akhir Pembelajaran')
                            ->dateTime('d M Y')
                            ->placeholder('2000/01/01')
                            ->getStateUsing(fn($record) => Carbon::parse($record->userPayment->created_at, 'Asia/Jakarta')->addMonth()->format('d F Y')),
                    ]),
                    Section::make('Data Siswa')->columns(['sm' => 3])->schema([
                        TextEntry::make('name')->label('Nama Lengkap'),
                        TextEntry::make('email'),
                        TextEntry::make('place_of_birth')->label('Tempat, Tanggal Lahir')->state(function (Model $record): string {
                            return $record->place_of_birth . ', ' . Carbon::parse($record->date_of_birth)->isoFormat('D MMMM Y');
                        }),
                        TextEntry::make('gender')->badge()->label('Jenis Kelamin'),
                        TextEntry::make('religion')->label('Agama'),
                        TextEntry::make('address')->label('Alamat'),
                        TextEntry::make('phone_number')->label('No. HP'),
                        TextEntry::make('programme')->label('Program'),
                        TextEntry::make('origin_school')->label('Asal Sekolah'),
                    ]),
                    Section::make('Data Orangtua')->columns(['sm' => 3])->schema([
                        TextEntry::make('parent_name')->label('Nama Orangtua'),
                        TextEntry::make('parent_phone_number')->label('No. HP Orangtua'),
                        TextEntry::make('parent_address')->label('Alamat Orangtua'),
                    ]),
                    Section::make()->columns(['sm' => 3])->schema([
                        ImageEntry::make('userPayment.proof_of_payment')->label('Bukti Pembayaran'),
                    ]),
                ]),
                Actions::make([
                    ActionInfolist::make('confirm')
                        ->label('Konfirmasi Pembayaran')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->action(function (User $record) {
                            $record->userPayment->status = 'confirmed';
                            $record->userPayment->save();
                        })
                        ->visible(fn($record) => $record->userPayment?->status == UserPaymentStatusEnum::WAITING_CONFIRMATION)
                        ->requiresConfirmation(),
                    ActionInfolist::make('reject')
                        ->label('Tolak Pembayaran')
                        ->icon('heroicon-m-x-mark')
                        ->color('danger')
                        ->action(function (User $record) {
                            $record->userPayment->status = 'rejected';
                            $record->userPayment->save();
                        })
                        ->visible(fn($record) => $record->userPayment?->status == UserPaymentStatusEnum::WAITING_CONFIRMATION)
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
