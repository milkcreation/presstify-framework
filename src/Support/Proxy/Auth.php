<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Auth\Signin;
use tiFy\Contracts\Auth\Signup;

/**
 * @method static Signin registerSignin(string $name, array $attrs = [])
 * @method static Signup registerSignup(string $name, array $attrs = [])
 * @method static Signin|null signin(string $name)
 * @method static Signup|null signup(string $name)
 */
class Auth extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'auth';
    }
}