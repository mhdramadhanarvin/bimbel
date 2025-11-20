<?php

namespace App\Filament\Exports;

use App\Enums\UserGenderEnum;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    protected static $tz = 'Asia/Jakarta';

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('userPayment.registration_number')->label('No. Pendaftaran'),
            ExportColumn::make('name')->label('Nama Lengkap'),
            ExportColumn::make('email'),
            ExportColumn::make('gender')->label('Jenis Kelamin')->state(function (User $record) {
                return $record->gender == UserGenderEnum::MALE ? 'Laki - Laki' : 'Perempuan';
            }),
            ExportColumn::make('place_of_birth')->state(function (User $record) {
                return $record->place_of_birth . ', ' . Carbon::parse($record->date_of_birth)->format('d F Y');
            }),
            ExportColumn::make('religion')->state(function (User $record) {
                return $record->religion->getLabel();
            }),
            ExportColumn::make('address')->label('Alamat'),
            ExportColumn::make('phone_number')->label('No. HP'),
            ExportColumn::make('programmer')->label('Program')->state(function (User $record) {
                return $record->programme->getLabel();
            }),
            ExportColumn::make('origin_school')->label('Asal Sekolah'),
            ExportColumn::make('parent_name')->label('Nama Orangtua'),
            ExportColumn::make('parent_phone_number')->label('No. HP Orangtua'),
            ExportColumn::make('parent_address')->label('Alamat Orangtua'),
            ExportColumn::make('')->label('Daftar Pada')->state(function (User $record) {
                return Carbon::parse($record->userPayment->created_at, self::$tz)->format('d F Y');
            }),
            ExportColumn::make('')->label('Akhir Pembelajaran')->state(function (User $record) {
                return Carbon::parse($record->created_at, self::$tz)->addMonth()->format('d F Y');
            })
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Data siswa selesai di export dan ' . number_format($export->successful_rows) . ' data telah dieskpor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' gagal ekpor data.';
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return "data-siswa-dikonfirmasi-{$export->getKey()}.xlsx";
    }
}
