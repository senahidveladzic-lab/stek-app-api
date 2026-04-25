<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteMemberRequest;
use App\Http\Requests\UpdateHouseholdRequest;
use App\Mail\HouseholdInvitationMail;
use App\Models\HouseholdInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HouseholdController extends Controller
{
    public function show(Request $request): Response
    {
        $household = $request->user()->household;
        $household->load(['members:id,name,email,household_id', 'invitations' => function ($q) {
            $q->whereNull('accepted_at')->latest();
        }]);

        return Inertia::render('settings/household', [
            'household' => $household,
        ]);
    }

    public function update(UpdateHouseholdRequest $request): RedirectResponse
    {
        $request->user()->household->update($request->validated());

        return back()->with('success', __('common.success'));
    }

    public function invite(InviteMemberRequest $request): RedirectResponse
    {
        $household = $request->user()->household;

        $existingPending = $household->invitations()
            ->where('email', $request->validated('email'))
            ->whereNull('accepted_at')
            ->exists();

        if ($existingPending) {
            return back()->withErrors(['email' => __('household.invitation_already_sent')]);
        }

        $invitation = $household->invitations()->create([
            'email' => $request->validated('email'),
            'token' => Str::random(64),
        ]);

        $invitation->setRelation('household', $household);
        Mail::to($invitation->email)->send(new HouseholdInvitationMail($invitation));

        return back()->with('success', __('common.success'));
    }

    public function removeMember(Request $request, User $user): RedirectResponse
    {
        $household = $request->user()->household;

        Gate::authorize('removeMember', [$household, $user]);

        $user->update(['household_id' => null]);

        return back()->with('success', __('common.success'));
    }

    public function acceptInvitation(Request $request, string $token): RedirectResponse
    {
        $invitation = HouseholdInvitation::query()
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        $user = $request->user();

        if ($user->email !== $invitation->email) {
            return redirect()->route('dashboard')
                ->with('error', __('household.invitation_email_mismatch'));
        }

        $invitation->update(['accepted_at' => now()]);
        $user->update(['household_id' => $invitation->household_id]);

        return redirect()->route('household.show')
            ->with('success', __('household.invitation_accepted'));
    }
}
