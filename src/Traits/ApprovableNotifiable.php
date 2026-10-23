<?php

namespace Toneflix\ApprovableNotifications\Traits;

trait ApprovableNotifiable
{
    use HasApprovableNotifications, SendsApprovableNotifications;
}
