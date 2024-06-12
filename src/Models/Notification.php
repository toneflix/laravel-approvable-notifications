<?php

namespace ToneflixCode\ApprovableNotifications\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ToneflixCode\ApprovableNotifications\ApprovableNotificationCollection;
use ToneflixCode\ApprovableNotifications\Events\ApprovableNotificationCreated;
use ToneflixCode\ApprovableNotifications\Events\ApprovableNotificationUpdated;

/**
 * @property \Illuminate\Support\Carbon|null $read_at The time when the notification was read
 * @property Model|\ToneflixCode\ApprovableNotifications\Traits\HasApprovableNotifications $notifiable
 * @property Model|\ToneflixCode\ApprovableNotifications\Traits\SendsApprovableNotifications $notifier
 * @property bool $seen If the notification has been seen
 * @property bool $pending If the notification is till pending
 * @property bool $rejected If the notification has been rejected
 * @property bool $approved If the notification has been approved
 * @method \Illuminate\Database\Eloquent\Builder unread() Scope the query to return unread messages
 * @method \Illuminate\Database\Eloquent\Builder read() Scope the query to return read messages
 */
class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'status',
        'notifier_id',
        'notifier_type',
        'actionable_id',
        'actionable_type',
        'notifiable_id',
        'notifiable_type',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'data',
        'title',
        'message',
        'notifier_id',
        'notifier_type',
        'actionable_id',
        'actionable_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => \Illuminate\Database\Eloquent\Casts\AsCollection::class,
        'read_at' => 'datetime',
        'status' => 'integer'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1,
        'data' => '{}',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'seen',
        'pending',
        'rejected',
        'approved',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::updated(function (self $model) {
            if ($model->isDirty('status')) {

                ApprovableNotificationUpdated::dispatch($model);

                if ($model->approved) {
                    $model->notifiable->approvedNotificationCallback($model);
                }

                if ($model->rejected) {
                    $model->notifiable->rejectedNotificationCallback($model);
                }
            }
        });

        static::created(function (self $model) {
            ApprovableNotificationCreated::dispatch($model);
            $model->notifier->newNotificationCallback($model);
        });
    }

    public function getTable(): string
    {
        return config('approvable-notifications.table_name', 'approvable_notifications');
    }

    public function notifier(): MorphTo
    {
        return $this->morphTo();
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function seen(): Attribute
    {
        return Attribute::make(
            get: fn () => (bool)$this->read_at,
            // set: function (\Illuminate\Support\Carbon $val) {
            //     $this->attributes['read_at'] = $val;
            // }
        );
    }

    public function pending(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 1,
        );
    }

    public function approved(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 2,
        );
    }

    public function rejected(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === 0,
        );
    }

    /**
     * Change the notification's status to read
     *
     * @return bool
     */
    public function markAsRead(): bool
    {
        $this->read_at = now();
        return $this->save();
    }

    /**
     * Change the notification's status to read
     *
     * @return bool
     */
    public function markAsUnread(): bool
    {
        $this->read_at = null;
        return $this->save();
    }

    /**
     * Approve notification's associated action
     *
     * @return bool
     */
    public function markAsApproved(): bool
    {
        $this->status = 2;
        return $this->save();
    }

    /**
     * Reject notification's associated action
     *
     * @return bool
     */
    public function markAsRejected(): bool
    {
        $this->status = 0;
        return $this->save();
    }

    /**
     * Scope the query to return unread messages
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread(Builder $query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope the query to return read messages
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead(Builder $query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope the query to return approved messages
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved(Builder $query)
    {
        return $query->whereStatus(2);
    }

    /**
     * Scope the query to return pending messages
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query)
    {
        return $query->whereStatus(1);
    }

    /**
     * Scope the query to return rejected messages
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected(Builder $query)
    {
        return $query->whereStatus(0);
    }

    /**
     * Create a new database notification collection instance.
     *
     * @param  array  $models
     * @return \ToneflixCode\ApprovableNotifications\ApprovableNotificationCollection
     */
    public function newCollection(array $models = [])
    {
        return new ApprovableNotificationCollection($models);
    }
}
