<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use ToneflixCode\ApprovableNotifications\Events\ApprovableNotificationCreated;
use ToneflixCode\ApprovableNotifications\Events\ApprovableNotificationUpdated;
use ToneflixCode\ApprovableNotifications\Exception\InvalidRecipientExeption;

test('can send notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    $notif = $sender->sendApprovableNotification(
        recipient: $user, // The recieving model
        title: $faker->sentence(4), // The title of the notification
        message: $faker->sentence(10), // The notification text message body
        data: ['icon' => 'fas fa-info'], // Any extra data you would like to store,
    );

    expect($notif instanceof Model)->toBeTrue();
    expect($notif->data['icon'] === 'fas fa-info')->toBeTrue();
});

test('can send notifications with actionables', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $actionable = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    $notif = $sender->sendApprovableNotification(
        recipient: $user, // The recieving model
        title: $faker->sentence(4), // The title of the notification
        message: $faker->sentence(10), // The notification text message body
        data: ['icon' => 'fas fa-info'], // Any extra data you would like to store,
        actionable: $actionable // Any model you would like to access or reference later during retrieval
    );

    expect($notif->actionable instanceof Model)->toBeTrue();
    expect($notif->actionable->id === $actionable->id)->toBeTrue();
});

test('can reject notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    $notif = $sender->sendApprovableNotification(
        recipient: $user, // The recieving model
        title: $faker->sentence(4), // The title of the notification
        message: $faker->sentence(10), // The notification text message body
    );

    expect($notif->markAsRejected())->toBeTrue();
});

test('can accept notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    $notif = $sender->sendApprovableNotification(
        recipient: $user, // The recieving model
        title: $faker->sentence(4), // The title of the notification
        message: $faker->sentence(10), // The notification text message body
    );

    expect($notif->markAsApproved())->toBeTrue();
});

test('can mark notifications as read', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    $notif = $sender->sendApprovableNotification(
        recipient: $user, // The recieving model
        title: $faker->sentence(4), // The title of the notification
        message: $faker->sentence(10), // The notification text message body
    );

    expect($notif->markAsRead())->toBeTrue();
});

test('can batch mark notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    foreach (range(1, 5) as $value) {
        $sender->sendApprovableNotification(
            recipient: $user, // The recieving model
            title: $faker->sentence(4), // The title of the notification
            message: $faker->sentence(10), // The notification text message body
        );
    }

    expect($user->approvableNotifications->markAsApproved())->toBeNull();
    expect($user->approvableNotifications->markAsRejected())->toBeNull();
    expect($user->approvableNotifications->markAsRead())->toBeNull();
    expect($user->approvableNotifications->markAsUnread())->toBeNull();
});

test('can access notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    foreach (range(1, 5) as $value) {
        $sender->sendApprovableNotification(
            recipient: $user, // The recieving model
            title: $faker->sentence(4), // The title of the notification
            message: $faker->sentence(10), // The notification text message body
        );
    }

    expect($user->approvableNotifications->count() === 5)->toBeTrue();
});

test('can access unread notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    foreach (range(1, 5) as $value) {
        $sender->sendApprovableNotification(
            recipient: $user, // The recieving model
            title: $faker->sentence(4), // The title of the notification
            message: $faker->sentence(10), // The notification text message body
        );
    }

    expect(
        $user->unreadApprovableNotifications->every('read_at', null) &&
            $user->unreadApprovableNotifications->count()
    )->toBeTrue();
});

test('can access pending notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    foreach (range(1, 5) as $value) {
        $n = $sender->sendApprovableNotification(
            recipient: $user, // The recieving model
            title: $faker->sentence(4), // The title of the notification
            message: $faker->sentence(10), // The notification text message body
        );

        $n->status = 1;
        $n->save();
    }

    expect(
        $user->pendingApprovableNotifications->every('status', 1) &&
            $user->pendingApprovableNotifications->count()
    )->toBeTrue();
});

test('can access approved notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    foreach (range(1, 5) as $value) {
        $n = $sender->sendApprovableNotification(
            recipient: $user, // The recieving model
            title: $faker->sentence(4), // The title of the notification
            message: $faker->sentence(10), // The notification text message body
        );

        $n->status = 2;
        $n->save();
    }

    expect(
        $user->approvedApprovableNotifications->every('status', 2) &&
            $user->approvedApprovableNotifications->count()
    )->toBeTrue();
});

test('can access rejected notifications', function () {

    $faker = \Faker\Factory::create();

    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    foreach (range(1, 5) as $value) {
        $n = $sender->sendApprovableNotification(
            recipient: $user, // The recieving model
            title: $faker->sentence(4), // The title of the notification
            message: $faker->sentence(10), // The notification text message body
        );

        $n->status = 0;
        $n->save();
    }

    expect(
        $user->rejectedApprovableNotifications->every('status', 0) &&
            $user->rejectedApprovableNotifications->count()
    )->toBeTrue();
});

test('ApprovableNotificationUpdated event is dispatched', function () {
    Event::fake([
        ApprovableNotificationUpdated::class,
    ]);

    $faker = \Faker\Factory::create();
    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    $n = $sender->sendApprovableNotification(
        recipient: $user, // The recieving model
        title: $faker->sentence(4), // The title of the notification
        message: $faker->sentence(10), // The notification text message body
    );

    $n->markAsApproved();

    Event::assertDispatched(ApprovableNotificationUpdated::class, function ($event) {
        return $event->notification->approved === true;
    });
});

test('ApprovableNotificationCreated event is dispatched', function () {
    Event::fake([
        ApprovableNotificationCreated::class,
    ]);

    $faker = \Faker\Factory::create();
    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    $sender->sendApprovableNotification(
        recipient: $user, // The recieving model
        title: $faker->sentence(4), // The title of the notification
        message: $faker->sentence(10), // The notification text message body
    );

    Event::assertDispatched(ApprovableNotificationCreated::class, function ($event) {
        return $event->notification->seen === false;
    });
});

it('throws exception when a notification is sent to an invalid model', function () {
    $faker = \Faker\Factory::create();
    $user = \ToneflixCode\ApprovableNotifications\Tests\Models\School::factory()->create();
    $sender = \ToneflixCode\ApprovableNotifications\Tests\Models\User::factory()->create();

    $sender->sendApprovableNotification(
        recipient: $user, // The recieving model
        title: $faker->sentence(4), // The title of the notification
        message: $faker->sentence(10), // The notification text message body
    );
})->throws(InvalidRecipientExeption::class);
