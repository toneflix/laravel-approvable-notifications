<?php

namespace Toneflix\ApprovableNotifications\Exception;

use Illuminate\Database\Eloquent\Model;

/**
 * Exception thrown when attempting to send a notification to a model that does not
 * use the HasApprovableNotifications trait.
 */
class InvalidRecipientExeption extends \Exception
{
    public static function message(Model $model): self
    {
        return new self($model->getMorphClass().' is not using the Toneflix\ApprovableNotifications\Traits\HasApprovableNotifications trait');
    }
}
