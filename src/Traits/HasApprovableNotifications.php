<?php

namespace Toneflix\ApprovableNotifications\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Toneflix\ApprovableNotifications\Models\Notification;

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
     */
    public function approvableNotifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return Builder
     */
    public function unreadApprovableNotifications()
    {
        return $this->approvableNotifications()->unread();
    }

    /**
     * Get the entity's approved notifications.
     *
     * @return Builder
     */
    public function approvedApprovableNotifications()
    {
        return $this->approvableNotifications()->approved();
    }

    /**
     * Get the entity's pending notifications.
     *
     * @return Builder
     */
    public function pendingApprovableNotifications()
    {
        return $this->approvableNotifications()->pending();
    }

    /**
     * Get the entity's rejected notifications.
     *
     * @return Builder
     */
    public function rejectedApprovableNotifications()
    {
        return $this->approvableNotifications()->rejected();
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return Builder
     */
    public function readApprovableNotifications()
    {
        return $this->approvableNotifications()->read();
    }

    /**
     * Get the entity's notifier.
     */
    public function approvableNotifier(): MorphOne
    {
        return $this->morphOne(Notification::class, 'notifier');
    }

    /**
     * Get the entity's actionable.
     */
    public function approvableNotificationActionable(): MorphOne
    {
        return $this->morphOne(Notification::class, 'actionable');
    }

    /**
     * Will be called when a notification is approved
     */
    public function approvedNotificationCallback(Notification $notification): void {}

    /**
     * Will be called when a notification is rejected
     */
    public function rejectedNotificationCallback(Notification $notification): void {}
}
