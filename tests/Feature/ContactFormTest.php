<?php

use App\Mail\ContactFormReceived;
use Illuminate\Support\Facades\Mail;

it('renders the contact page successfully', function () {
    $this->get(route('contact'))->assertOk();
});

it('sends a ContactFormReceived mailable on valid submission', function () {
    Mail::fake();

    $this->post(route('contact.send'), [
        'name' => 'Ana Kovač',
        'email' => 'ana@example.com',
        'subject' => 'Test subject',
        'message' => 'Hello, this is a test message.',
    ])->assertRedirect(route('contact'));

    Mail::assertQueued(ContactFormReceived::class, function (ContactFormReceived $mail) {
        return $mail->senderName === 'Ana Kovač'
            && $mail->senderEmail === 'ana@example.com'
            && $mail->contactSubject === 'Test subject'
            && $mail->messageBody === 'Hello, this is a test message.'
            && $mail->hasReplyTo('ana@example.com');
    });
});

it('silently passes when the honeypot field is filled', function () {
    Mail::fake();

    $this->post(route('contact.send'), [
        'name' => 'Bot',
        'email' => 'bot@spam.com',
        'subject' => 'Buy now',
        'message' => 'Click here',
        '_hp' => 'I am a bot',
    ])->assertRedirect(route('contact'));

    Mail::assertNothingOutgoing();
});

it('validates required fields', function (string $field) {
    Mail::fake();

    $data = [
        'name' => 'Ana Kovač',
        'email' => 'ana@example.com',
        'subject' => 'Test subject',
        'message' => 'Hello, this is a test message.',
    ];

    unset($data[$field]);

    $this->post(route('contact.send'), $data)->assertSessionHasErrors($field);

    Mail::assertNothingOutgoing();
})->with(['name', 'email', 'subject', 'message']);
