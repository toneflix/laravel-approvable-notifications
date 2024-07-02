<?php

namespace ToneflixCode\ApprovableNotifications;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @template TKey of array-key
 * @template TModel of DatabaseNotification
 *
 * @extends \Illuminate\Database\Eloquent\Collection<TKey, TModel>
 */
class ApprovableNotificationCollection extends Collection
{
    /**
     * Mark all notifications as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        $this->each->markAsRead();
    }

    /**
     * Mark all notifications as unread.
     *
     * @return void
     */
    public function markAsUnread()
    {
        $this->each->markAsUnread();
    }

    /**
     * Mark all notifications as approved.
     *
     * @return void
     */
    public function markAsApproved()
    {
        $this->each->markAsApproved();
    }

    /**
     * Mark all notifications as rejectd.
     *
     * @return void
     */
    public function markAsRejected()
    {
        $this->each->markAsRejected();
    }
}
