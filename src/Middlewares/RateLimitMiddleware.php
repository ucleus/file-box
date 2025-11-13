<?php

namespace Middlewares;

use Services\RateLimiter;
use Utils\Response;

class RateLimitMiddleware
{
    public static function check($action)
    {
        $rateLimiter = new RateLimiter();
        $identifier = RateLimiter::getIdentifier();

        if (!$rateLimiter->check($identifier, $action)) {
            Response::error('Too many requests. Please try again later.', 429);
        }

        $rateLimiter->increment($identifier, $action);
        return true;
    }

    public static function checkDownload()
    {
        return self::check('download');
    }

    public static function checkPageView()
    {
        return self::check('page_view');
    }

    public static function checkOTPRequest()
    {
        return self::check('otp_request');
    }

    public static function checkOTPVerify()
    {
        return self::check('otp_verify');
    }
}
