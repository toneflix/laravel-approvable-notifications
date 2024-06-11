<?php

namespace ToneflixCode\ApprovableNotifications\Traits;

trait ApprovableNotifiable
{
    use HasApprovableNotifications, SendsApprovableNotifications;
}
