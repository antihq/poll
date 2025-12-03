<?php

namespace App\Notifications;

use App\Models\TeamInvitation as TeamInvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitation extends Notification
{
    use Queueable;

    public function __construct(public TeamInvitationModel $invitation) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invitation = $this->invitation;
        $team = $invitation->team;
        $acceptUrl = url()->signedRoute('teams.invitations.accept', $invitation);

        return (new MailMessage)
            ->subject(__('You have been invited to join :team', ['team' => $team->name]))
            ->greeting(__('Hello!'))
            ->line(__('You have been invited to join the team ":team".', ['team' => $team->name]))
            ->action(__('Accept Invitation'), $acceptUrl)
            ->line(__('If you do not wish to join, you may ignore this email.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
