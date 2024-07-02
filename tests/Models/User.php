<?php

namespace ToneflixCode\ApprovableNotifications\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use ToneflixCode\ApprovableNotifications\Models\Notification;
use ToneflixCode\ApprovableNotifications\Traits\ApprovableNotifiable;

class User extends Authenticatable
{
    use ApprovableNotifiable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Will be called when a notification is approved
     */
    public function approvedNotificationCallback(Notification $notification): void
    {
        // dump($notification);
    }

    /**
     * Will be called when a notification is rejected
     */
    public function rejectedNotificationCallback(Notification $notification): void
    {
        // dump($notification);
    }
}
