<x-mail::message>
# {{ __('contact.email.title') }}

**{{ __('contact.email.from') }}:** {{ $senderName }} ({{ $senderEmail }})
**{{ __('contact.email.subject_label') }}:** {{ $contactSubject }}

---

{{ $messageBody }}

---

*{{ __('contact.email.reply_hint', ['name' => $senderName]) }}*
</x-mail::message>
