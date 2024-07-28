<?php

namespace ToneflixCode\ApprovableNotifications\Traits;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use stdClass;
use ToneflixCode\ApprovableNotifications\Exception\InvalidRecipientExeption;
use ToneflixCode\ApprovableNotifications\Models\Notification;

/**
 * @property EloquentCollection<TKey,EloquentCollection> $approvableSentNotifications Get the entity's sent notifications.
 */
trait SendsApprovableNotifications
{
    /**
     * Send a notification to the entity.
     *
     * @param  HasApprovableNotifications|Model  $recipient  The recieving model
     * @param  string  $title  The title of the notification
     * @param  string  $message  The notification message body
     * @param  array|Collection|stdClass  $data  Any extra data you would like to store
     * @param  ?Model  $actionable  Any model you would like to access or reference later during retrieval
     */
    public function sendApprovableNotification(
        HasApprovableNotifications|Model $recipient,
        string $title,
        string $message,
        array|Collection|stdClass $data = new stdClass,
        ?Model $actionable = null,
    ): \ToneflixCode\ApprovableNotifications\Models\Notification {
        if (! method_exists($recipient, 'approvableNotifications')) {
            throw InvalidRecipientExeption::message($recipient);
        }

        return $recipient->approvableNotifications()->create([
            'data' => $data,
            'title' => $title,
            'message' => $message,
            'notifier_id' => $this->id,
            'notifier_type' => $this->getMorphClass(),
            'actionable_id' => $actionable?->id,
            'actionable_type' => $actionable?->getMorphClass(),
        ]);
    }

    /**
     * Get the entity's sent notifications.
     */
    public function approvableSentNotifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Notification::class, 'notifier')->latest();
    }

    /**
     * Will be called when a new notification is sent
     */
    public function newNotificationCallback(Notification $notification): void {}
}
