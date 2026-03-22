<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteMemberRequest;
use App\Http\Requests\UpdateHouseholdRequest;
use App\Models\HouseholdInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        return response()->json(['data' => $invitation], 201);
    }

    public function removeMember(Request $request, User $user): JsonResponse
    {
        $household = $request->user()->household;

        Gate::authorize('removeMember', [$household, $user]);

        $user->update(['household_id' => null]);

        return response()->json(null, 204);
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

        $invitation->update(['accepted_at' => now()]);
        $user->update(['household_id' => $invitation->household_id]);

        return response()->json(['message' => __('household.invitation_accepted')]);
    }
}
