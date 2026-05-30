<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteMemberRequest;
use App\Http\Requests\UpdateHouseholdRequest;
use App\Mail\HouseholdInvitationMail;
use App\Models\HouseholdInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class HouseholdController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $household = $request->user()->household;
        $household->load(['members:id,name,email,household_id', 'invitations' => function ($q) {
            $q->whereNull('accepted_at')->latest();
        }]);

        return response()->json(['data' => $household]);
    }

    public function update(UpdateHouseholdRequest $request): JsonResponse
    {
        $household = $request->user()->household;
        $household->update($request->validated());

        return response()->json(['data' => $household]);
    }

    public function invite(InviteMemberRequest $request): JsonResponse
    {
        $household = $request->user()->household;

        $existingPending = $household->invitations()
            ->where('email', $request->validated('email'))
            ->whereNull('accepted_at')
            ->exists();

        if ($existingPending) {
            return response()->json([
                'message' => __('household.invitation_already_sent'),
            ], 422);
        }

        $invitation = $household->invitations()->create([
            'email' => $request->validated('email'),
            'token' => Str::random(64),
        ]);

        $invitation->setRelation('household', $household);
        Mail::to($invitation->email)->send(new HouseholdInvitationMail($invitation));

        return response()->json(['data' => $invitation], 201);
    }

    public function removeMember(Request $request, User $user): JsonResponse
    {
        $household = $request->user()->household;

        Gate::authorize('removeMember', [$household, $user]);

        $user->update(['household_id' => null]);

        return response()->json(null, 204);
    }

    public function myInvitation(Request $request): JsonResponse
    {
        $invitation = HouseholdInvitation::query()
            ->where('email', $request->user()->email)
            ->whereNull('accepted_at')
            ->with('household:id,name')
            ->latest()
            ->first();

        return response()->json(['data' => $invitation]);
    }

    public function acceptInvitation(Request $request, string $token): JsonResponse
    {
        $invitation = HouseholdInvitation::query()
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        $user = $request->user();

        if ($user->email !== $invitation->email) {
            return response()->json([
                'message' => __('household.invitation_email_mismatch'),
            ], 403);
        }

        DB::transaction(function () use ($user, $invitation) {
            $oldHousehold = $user->household;

            $invitation->update(['accepted_at' => now()]);
            $user->update(['household_id' => $invitation->household_id]);

            if ($oldHousehold
                && $oldHousehold->owner_id === $user->id
                && $oldHousehold->members()->count() === 0
            ) {
                $oldHousehold->delete();
            }
        });

        return response()->json(['message' => __('household.invitation_accepted')]);
    }
}
