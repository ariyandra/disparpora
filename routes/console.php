<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
// Mailtrap SDK classes (optional - ensure package is installed via composer if you use the API client)
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Custom artisan command to send a test email via Mailtrap API
Artisan::command('send-mail', function () {
    $this->info('Sending test email via Laravel Mail (SMTP)...');

    try {
        $resetUrl = url('/password/reset/testtoken?email=' . urlencode('adekari35@gmail.com') . '&role=pelatih');

        Mail::send('emails.password_reset', ['resetUrl' => $resetUrl, 'role' => 'pelatih'], function ($m) {
            $m->to('adekari35@gmail.com')->subject('Test Mailtrap SMTP via Laravel Mail');
            $m->from(config('mail.from.address') ?: 'noreply@disparpora.local', config('mail.from.name') ?: 'DISPARPORA');
        });

        $this->info('Mail sent (check Mailtrap inbox).');
    } catch (\Throwable $e) {
        $this->error('Send failed: ' . $e->getMessage());
        return 1;
    }

    return 0;
})->purpose('Send Mail via Laravel Mail (SMTP)');
