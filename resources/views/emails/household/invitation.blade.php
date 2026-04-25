<x-mail::message>
# {{ __('household.invitation_email_title') }}

{{ __('household.invitation_email_body', ['household' => $invitation->household->name]) }}

<x-mail::button :url="route('household.acceptInvitation', $invitation->token)">
{{ __('household.invitation_email_cta') }}
</x-mail::button>

*{{ __('household.invitation_email_footer') }}*
</x-mail::message>
