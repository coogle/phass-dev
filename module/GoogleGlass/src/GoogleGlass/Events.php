<?php

namespace GoogleGlass;

class Events
{
    /**
     * The following are all events triggered when we recieve a Google Glass
     * notification from a timeline subscription
     */
    const EVENT_SUBSCRIPTION_SHARE = 'GoogleGlass\Events\Subscription\Share';
    const EVENT_SUBSCRIPTION_DELETE = 'GoogleGlass\Events\Subscription\Delete';
    const EVENT_SUBSCRIPTION_LAUNCH = 'GoogleGlass\Events\Subscription\Launch';
    const EVENT_SUBSCRIPTION_REPLY = 'GoogleGlass\Events\Subscription\Reply';
    const EVENT_SUBSCRIPTION_LOCATION = 'GoogleGlass\Events\Subscription\Location';
    const EVENT_SUBSCRIPTION_CUSTOM = 'GoogleGlass\Events\Subscription\Custom';

    /**
     * When a timeline subscription notification is received this is triggered in an attempt
     * to resolve the opaque user ID given to us by Google (representing which user did the action)
     * to an OAuth2 token for that user. It allows us to do things like insert timeline items
     * when a notification is received.
     */
    const EVENT_SUBSCRIPTION_RESOLVE_USER = 'GoogleGlass\Events\Subscription\ResolveUser';

    const EVENT_NEW_AUTH_TOKEN = 'GoogleGlass\Events\OAuth\NewToken';
}