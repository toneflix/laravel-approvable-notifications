<?php

namespace ToneflixCode\ApprovableNotifications\Traits;

use ToneflixCode\ApprovableNotifications\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property Collection<TKey,Collection> $approvableNotifications Get the entity's notifications
 * @property Collection<TKey,Collection> $readApprovableNotifications Get the entity's read notifications
 * @property Collection<TKey,Collection> $unreadApprovableNotifications Get the entity's unread notifications
 * @property Collection<TKey,Collection> $pendingApprovableNotifications Get the entity's pending notifications
 * @property Collection<TKey,Collection> $approvedApprovableNotifications Get the entity's approved notifications
 * @property Collection<TKey,Collection> $rejectedApprovableNotifications Get the entity's rejected notifications
 * @property Collection<TKeys,Collection> $approvableNotifier Get the entity's notifier.
 */
trait HasApprovableNotifications
{
    /**
     * Get the entity's notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function approvableNotifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function unreadApprovableNotifications()
    {
        return $this->approvableNotifications()->unread();
    }

    /**
     * Get the entity's approved notifications.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function approvedApprovableNotifications()
    {
        return $this->approvableNotifications()->approved();
    }

    /**
     * Get the entity's pending notifications.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function pendingApprovableNotifications()
    {
        return $this->approvableNotifications()->pending();
    }

    /**
     * Get the entity's rejected notifications.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function rejectedApprovableNotifications()
    {
        return $this->approvableNotifications()->rejected();
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function readApprovableNotifications()
    {
        return $this->approvableNotifications()->read();
    }

    /**
     * Get the entity's notifier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function approvableNotifier(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Notification::class, 'notifier');
    }

    /**
     * Get the entity's actionable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function approvableNotificationActionable(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Notification::class, 'actionable');
    }

    /**
     * Will be called when a notification is approved
     * @param Notification $notification
     * 
     * @return void
     */
    public function approvedCallback(Notification $notification): void
    {
    }

    /**
     * Will be called when a notification is rejected
     * @param Notification $notification
     *
     * @return void
     */
    public function rejectedCallback(Notification $notification): void
    {
    }
}
