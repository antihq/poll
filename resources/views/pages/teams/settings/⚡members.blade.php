<?php

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitation as TeamInvitationNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Flux\Flux;
use Illuminate\Validation\Rule;

new class extends Component {
    public Team $team;
    public Collection $invitations;
    public Collection $members;

    public string $email = '';

    public function mount()
    {
        $this->authorize('manageMembers', $this->team);

        $this->invitations = $this->getInvitations();
        $this->members = $this->getMembers();
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('team_invitations', 'email')->where(
                    fn ($query) => $query->where('team_id', $this->team->id)
                ),
            ],
        ];
    }

    public function sendInvitation()
    {
        $this->validate();

        $invitation = $this->team->inviteMember($this->email);

        Notification::route('mail', $this->email)
            ->notify(new TeamInvitationNotification($invitation));

        Flux::toast(
            heading: __('Invitation sent'),
            text: __('The invitation was sent to :email.', ['email' => $this->email]),
            variant: 'success'
        );

        $this->reset('email');

        $this->invitations = $this->getInvitations();
    }

    private function getInvitations(): Collection
    {
        return $this->team->invitations()->latest()->get();
    }

    public function removeMember(User $member): void
    {
        abort_unless($this->team->isMember($member), 403);

        $this->team->removeMember($member);

        Flux::toast(
            heading: __('Member removed'),
            text: __('The member :name was removed from team.', ['name' => $member->name]),
            variant: 'success'
        );

        $this->members = $this->getMembers();
    }

    private function getMembers(): Collection
    {
        return $this->team->members()->get();
    }

    public function revokeInvitation(TeamInvitation $invitation): void
    {
        $this->authorize('revoke', $invitation);

        $invitation->delete();

        Flux::toast(
            heading: __('Invitation revoked'),
            text: __('The invitation for :email was revoked.', ['email' => $invitation->email]),
            variant: 'success'
        );

        $this->invitations = $this->getInvitations();
    }
}; ?>

<x-slot:breadcrumbs>
    @include('partials.team-settings-breadcrumbs', ['team' => $team, 'current' => __('Members')])
</x-slot>

<div>
    @include('partials.team-settings-heading')
    <div class="flex items-start max-md:flex-col">
        @include('partials.team-settings-sidebar', ['team' => $team])
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <section class="max-w-lg">
                <header>
                    <flux:heading>{{ __('Team Members') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('These are all members in your team.') }}</flux:text>
                </header>
                <div class="mt-4">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Member') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2 sm:gap-4">
                                        <flux:avatar :name="$team->user->name" class="max-sm:size-8" circle />
                                        <div class="flex flex-col">
                                            <flux:heading>
                                                {{ $team->user->name }}
                                                <flux:badge size="sm" color="yellow" class="ml-1 max-sm:hidden">
                                                    {{ __('Owner') }}
                                                </flux:badge>
                                            </flux:heading>
                                            <flux:text class="max-sm:hidden">{{ $team->user->email }}</flux:text>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <!-- Owner cannot be removed -->
                                </flux:table.cell>
                            </flux:table.row>
                            @foreach ($members as $member)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <div class="flex items-center gap-2 sm:gap-4">
                                            <flux:avatar :name="$member->name" class="max-sm:size-8" circle />
                                            <div class="flex flex-col">
                                                <flux:heading>
                                                    {{ $member->name }}
                                                    @if (auth()->id() === $member->id)
                                                    @endif
                                                </flux:heading>
                                                <flux:text class="max-sm:hidden">{{ $member->email }}</flux:text>
                                            </div>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell align="end">
                                        <flux:button
                                            variant="subtle"
                                            size="sm"
                                            wire:click="removeMember({{ $member->id }})"
                                        >
                                            {{ __('Remove') }}
                                        </flux:button>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            </section>
            <section class="mt-8">
                <header>
                    <flux:heading>{{ __('Invite Member') }}</flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Invite a new member to your team by entering their email address below.') }}
                    </flux:text>
                </header>
                <form wire:submit="sendInvitation" class="mt-4 max-w-lg space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Email address') }}</flux:label>
                        <flux:input.group>
                            <flux:input
                                type="email"
                                wire:model="email"
                                placeholder="{{ __('Enter member email') }}"
                                icon="user"
                            />
                            <flux:button type="submit">
                                {{ __('Send Invitation') }}
                            </flux:button>
                        </flux:input.group>
                        <flux:error name="email" />
                    </flux:field>
                </form>
            </section>
            <section class="mt-8 max-w-lg">
                <header>
                    <flux:heading>{{ __('Pending Invitations') }}</flux:heading>
                    <flux:text class="mt-2">
                        {{ __('These are invitations you have sent to join your team. You can manage them here.') }}
                    </flux:text>
                </header>
                <div class="mt-4">
                    @if ($invitations->isEmpty())
                        <div class="flex flex-col items-center justify-center py-8">
                            <flux:text>
                                <flux:icon name="user-plus" variant="mini" class="mb-4" />
                            </flux:text>
                            <flux:heading size="md" class="mb-1">{{ __('No pending invitations') }}</flux:heading>
                            <flux:text>{{ __('You haven\'t sent any invitations yet.') }}</flux:text>
                        </div>
                    @else
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Invitation') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($invitations as $invitation)
                                    <flux:table.row>
                                        <flux:table.cell>{{ $invitation->email }}</flux:table.cell>

                                        <flux:table.cell align="end">
                                            <flux:button
                                                color="danger"
                                                variant="subtle"
                                                size="sm"
                                                wire:click="revokeInvitation({{ $invitation->id }})"
                                            >
                                                {{ __('Revoke') }}
                                            </flux:button>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
