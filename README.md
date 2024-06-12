# Laravel Fileable

[![Test & Lint](https://github.com/toneflix/laravel-approvable-notifications/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/toneflix/laravel-approvable-notifications/actions/workflows/php.yml)
[![Latest Stable Version](http://poser.pugx.org/toneflix-code/approvable-notifications/v)](https://packagist.org/packages/toneflix-code/approvable-notifications) [![Total Downloads](http://poser.pugx.org/toneflix-code/approvable-notifications/downloads)](https://packagist.org/packages/toneflix-code/approvable-notifications) [![Latest Unstable Version](http://poser.pugx.org/toneflix-code/approvable-notifications/v/unstable)](https://packagist.org/packages/toneflix-code/approvable-notifications) [![License](http://poser.pugx.org/toneflix-code/approvable-notifications/license)](https://packagist.org/packages/toneflix-code/approvable-notifications) [![PHP Version Require](http://poser.pugx.org/toneflix-code/approvable-notifications/require/php)](https://packagist.org/packages/toneflix-code/approvable-notifications)
[![codecov](https://codecov.io/gh/toneflix/laravel-approvable-notifications/graph/badge.svg?token=SHm1zYOgLH)](https://codecov.io/gh/toneflix/laravel-fileable)

<!-- ![GitHub Actions](https://github.com/toneflix/laravel-approvable-notifications/actions/workflows/main.yml/badge.svg) -->

Approvable Notifications and to your project and handles the missing features of the Laravel notification system, the ability for users to interact with database notifications.

## Use Cases

1. Friend Requests
2. Access Requests
3. Anything that requires a third party user to approve or reject.

## Installation

1. Install the package via composer:

   ```bash
   composer require toneflix-code/approvable-notifications
   ```

2. Publish resources (migrations and config files):

   ```shell
   php artisan publish:approvable-notifications
   ```

3. Run the migrations with the following command:

   ```shell
   php artisan migrate
   ```

4. Done!

## Package Discovery

Laravel automatically discovers and publishes service providers but optionally after you have installed Laravel Fileable, open your Laravel config file config/app.php and add the following lines.

In the $providers array add the service providers for this package.

```php
ToneflixCode\ApprovableNotifications\ApprovableNotificationsServiceProvider::class
```

Add the facade of this package to the $aliases array.

```php
'ApprovableNotifications' => ToneflixCode\ApprovableNotifications\Facades\ApprovableNotifications::class
```

## Usage

### The `SendsApprovableNotifications` Trait

To be able to send notifications using Approvable Notifications add the `\ToneflixCode\ApprovableNotifications\Traits\SendsApprovableNotifications` trait to your model:

```php
namespace App\Models;

use ToneflixCode\ApprovableNotifications\Traits\SendsApprovableNotifications;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SendsApprovableNotifications;
}
```

That's it, we only have to use that trait in our model! Now your users may send approvable notifications.

> **Note:** you can use `SendsApprovableNotifications` trait on any model, it doesn't have to be the user model.

### The `HasApprovableNotifications` Trait

For a model to be able to receive notifications using Approvable Notifications you will also need to add the `\ToneflixCode\ApprovableNotifications\Traits\HasApprovableNotifications` trait to your model:

```php
namespace App\Models;

use ToneflixCode\ApprovableNotifications\Traits\HasApprovableNotifications;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApprovableNotifications;
}
```

Alternatively, if your model sends and recieves notifications, you can simply add only the `ApprovableNotifiable` trait on your model.

```php
namespace App\Models;

use ToneflixCode\ApprovableNotifications\Traits\ApprovableNotifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use ApprovableNotifiable;
}
```

Now your model can both send and recieve approvable notifications.

### Sending notifications

To send a notification, call the `sendApprovableNotification` method on the sender model, passing the `recipient` model, `title`, `message`, optional `data` and optional `actionable` model parameters.

```php
$faker = \Faker\Factory::create();
$user = \App\Models\User::find(1);
$sender =\App\Models\User::find(12);
$actionable =\App\Models\User::find(5);

$notif = $sender->sendApprovableNotification(
    recipient: $user, // The recieving model
    title: $faker->sentence(4), // The title of the notification
    message: $faker->sentence(10), // The notification text message body
    data: ['icon' => 'fas fa-info'], // Any extra data you would like to store,
    actionable: $actionable, // Any model you would like to access or reference later during retrieval
);
```

### Accessing the Notifications

Once notifications are stored in the database, you need a convenient way to access them from your notifiable entities. The `ToneflixCode\ApprovableNotifications\Traits\HasApprovableNotifications` trait includes a `approvableNotifications` Eloquent relationship that returns the notifications for the entity. To fetch notifications, you may access this method like any other Eloquent relationship. By default, notifications will be sorted by the `created_at` timestamp with the most recent notifications at the beginning of the collection:

```php
$user = App\Models\User::find(1);

foreach ($user->approvableNotifications as $notification) {
    echo $notification->title;
}
```

If you want to retrieve only the "unread" notifications, you may use the `unreadApprovableNotifications` relationship. Again, these notifications will be sorted by the `created_at` timestamp with the most recent notifications at the beginning of the collection:

```php
$user = App\Models\User::find(1);

foreach ($user->unreadApprovableNotifications as $notification) {
    echo $notification->title;
}
```

Or to retrieve "approved" notifications

```php
foreach ($user->approvedApprovableNotifications as $notification) {
    echo $notification->title;
}
```

### Accessing the senders Notifications

The sender of a notification also has the ability to access the notifications they sent. The `ToneflixCode\ApprovableNotifications\Traits\SendsApprovableNotifications` trait includes a `approvableSentNotifications` Eloquent relationship that returns the notifications for the entity that sent them.

```php
$user = App\Models\User::find(1);

foreach ($user->approvableSentNotifications as $notification) {
    echo $notification->title;
}
```

### Marking Notifications as Read

Typically, you will want to mark a notification as "read" when a user views it. The `ToneflixCode\ApprovableNotifications\Traits\HasApprovableNotifications` trait provides a `markAsRead` method, which updates the `read_at` column on the notification's database record:

```php
$user = App\Models\User::find(1);

foreach ($user->unreadApprovableNotifications as $notification) {
    echo $notification->markAsRead();
}
```

However, instead of looping through each notification, you may use the `markAsRead` method directly on a collection of notifications:

```php
$user->unreadApprovableNotifications->markAsRead();
```

You may also use a mass-update query to mark all of the notifications as read without retrieving them from the database:

```php
$user = App\Models\User::find(1);
$user->unreadApprovableNotifications()->update(['read_at' => now()]);
```

### Marking Notifications as Approved or Rejected

The primary purpose of this library is to allow you approve or reject actions associated with your models. The `ToneflixCode\ApprovableNotifications\Traits\HasApprovableNotifications` trait provides the `markAsApproved` and `markAsRejected` methods, which will update the `status` column on the notification's database record:

```php
$user = App\Models\User::find(1);

foreach ($user->approvableNotifications as $notification) {
    echo $notification->markAsApproved();
}
```

```php
$user = App\Models\User::find(1);

foreach ($user->approvableNotifications as $notification) {
    echo $notification->markAsRejected();
}
```

However, instead of looping through each notification, you may use the `markAsApproved` and `markAsRejected` methods directly on a collection of notifications:

```php
$user->approvableNotifications->markAsApproved();
```

```php
$user->approvableNotifications->markAsRejected();
```

### Deleting Notifications

You may delete the notifications to remove them from the table entirely:

```php
$user->notifications()->delete();
```

### Events and Callback

When an event is interacted with or updated, we dispatch the `ToneflixCode\ApprovableNotifications\Events\NotificationUpdated` which you can listen to and perform further actions if required, the event will contain the associated `notification` model.

Alternattively, you can also implement the `approvedCallback` and the `rejectedCallback` methods on your `HasApprovableNotifications` entity, both of which will be called at the appropriete time as the names imply and will provided with the associated `$notification` model as the first and only parameter.

```php
namespace App\Models;

use ToneflixCode\ApprovableNotifications\Traits\HasApprovableNotifications;
use Illuminate\Foundation\Auth\User as Authenticatable;
use ToneflixCode\ApprovableNotifications\Models\Notification;

class User extends Authenticatable
{
    use HasApprovableNotifications;

    public function approvedCallback(Notification $notification) {
        // Perform any other actions here
    }

    public function rejectedCallback(Notification $notification) {
        // Perform any other actions here
    }
}
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email code@toneflix.com.ng instead of using the issue tracker.

## Credits

- [Toneflix Code](https://github.com/toneflix)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
