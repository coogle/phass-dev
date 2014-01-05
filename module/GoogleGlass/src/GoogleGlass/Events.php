<?php

namespace GoogleGlass;

class Events
{
    const EVENT_SUBSCRIPTION_SHARE = "GoogleGlass\Events\Subscription\Share";
    const EVENT_SUBSCRIPTION_DELETE = "GoogleGlass\Events\Subscription\Delete";
    const EVENT_SUBSCRIPTION_LAUNCH = "GoogleGlass\Events\Subscription\Launch";
    const EVENT_SUBSCRIPTION_REPLY = "GoogleGlass\Events\Subscription\Reply";
    const EVENT_SUBSCRIPTION_LOCATION = "GoogleGlass\Events\Subscription\Location";
    const EVENT_SUBSCRIPTION_CUSTOM = "GoogleGlass\Events\Subscription\Custom";
    
    const EVENT_NEW_AUTH_TOKEN = "GoogleGlass\Events\OAuth\NewToken";
}