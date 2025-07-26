<div class="w-full md:w-auto">
    <x-auth-header :title="__('Selesaikan Pembayaran')" :description="__('Berikut rincian pembayaran yang harus diselesaikan.')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <flux:modal name="success" class="md:w-96 text-gray-300" :dismissible="false" @close="closeModal">
        <div class="space-y-6">
            <div>
                <div class="flex justify-center">
                    <flux:icon.circle-check class="text-green-500 dark:text-green-300 w-15 h-15"/>
                </div>
                <flux:heading size="lg" class="text-center mt-6 font-semibold">Terima kasih telah mendaftar</flux:heading>
                <flux:text>Silahkan tunjukkan nomor pendaftaran ini pada saat registrasi ulang dikantor.</flux:text>
            </div>
            <flux:input type="string" value="{{ $registration_number }}" disabled class="mb-4"/>
        </div>
    </flux:modal>

    <form wire:submit="pay" class="flex justify-center">
        <div class="rounded-xl dark:text-gray-300">
            <div class="overflow-hidden dark:border-neutral-700 flex items-center mt-4">
                <table class="text-md">
                    <tr>
                        <td>SPP</td>
                        <td class="text-sm px-14">1 Bulan</td>
                        <td class="font-semibold">Rp. 5.000.000</td>
                    </tr>
                    <tr>
                        <td>Asrama</td>
                        <td class="text-sm px-14">1 Bulan</td>
                        <td class="font-semibold">Rp. 350.000</td>
                    </tr>
                    <tr>
                        <td>Konsumsi</td>
                        <td class="text-sm px-14">1 Bulan</td>
                        <td class="font-semibold">Rp. 800.000</td>
                    </tr>
                    <tr>
                        <td class="font-semibold">TOTAL</td>
                        <td class="text-sm px-14"></td>
                        <td class="font-semibold">Rp. 6.150.000</td>
                    </tr>
                </table>
            </div>
            <p class="py-4">Silahkan lakukan pembayaran ke rekening berikut ini sebelum:</p>
            <h3 class="font-bold text-xl text-center">{{ date('d F Y H:i:s', strtotime($expired_payment) ) }}</h3>
            <div class="py-4 overflow-hidden dark:border-neutral-700 flex items-center">
                <table class="text-md">
                    <tr>
                        <td>Nama Bank</td>
                        <td class="px-6">:</td>
                        <td class="font-semibold">Bank Central Asia</td>
                    </tr>
                    <tr>
                        <td>Nomor Rekening</td>
                        <td class="px-6">:</td>
                        <td class="font-semibold">1234 1234 1234 1234</td>
                    </tr>
                    <tr>
                        <td>Nama Akun</td>
                        <td class="px-6">:</td>
                        <td class="font-semibold">Bimbel UNIBEN</td> </tr>
                    <tr>
                        <td>Nominal</td>
                        <td class="px-6">:</td>
                        <td class="font-semibold">Rp. 6.150.000</td>
                    </tr>
                </table>
            </div>
            <div class="my-4">
                <flux:input type="file" wire:model="proof_of_payment" label="Upload Bukti Pembayaran" accept="image/*"/>
                <div class="flex justify-center py-4 ">
                    <flux:button variant="primary" type="submit" class="w-full bg-[#51a2ff]">{{ __('Upload') }}</flux:button>
                </div>
            </div>
        </div>
    </form>

</div>
