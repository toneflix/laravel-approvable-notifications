<?php

namespace Toneflix\ApprovableNotifications\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Toneflix\ApprovableNotifications\Models\Notification;
use Toneflix\ApprovableNotifications\Traits\SendsApprovableNotifications;

class School extends Authenticatable
{
    use HasFactory;
    use SendsApprovableNotifications;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
    ];

    /**
     * Will be called when a new notification is sent
     */
    public function newNotificationCallback(Notification $notification): void
    {
        // dump($notification);
    }
}
