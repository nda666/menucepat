@component('mail::message')
# Password Reset

Apakah Anda saat ini sedang ingin melakukan **Reset Password** pada Applikasi Absensi IRIS?
Jika ya, silahkan tekan tombol di bawah ini.

@component('mail::button', ['url' => 'https://menucepat.com?token=' . $token])
    Reset Password
@endcomponent

Atau silahkan copy dan paste link dibawah ini.
https://menucepat.com?token={{ $token }}

Terima Kasih,<br>
{{ config('app.name') }}
@endcomponent
