<?php

namespace cronv\Task\Management\Constants;

/**
 * Class RouterConstants
 *
 * This class contains constants for routers.
 *
 * @package cronv\Task\Management\Constants
 */
final class RouterConstants
{
    /** @var string The home route constant */
    public const HOME_ROUTE = 'home';

    /** @var string The login route constant */
    public const LOGIN_ROUTE = 'app_login';

    /** @var string The logout route constant */
    public const LOGOUT_ROUTE = 'app_logout';

    /**
     * Private constructor to prevent class instantiation.
     */
    private function __construct()
    {
        // Private constructor to prevent class instantiation
    }
}
