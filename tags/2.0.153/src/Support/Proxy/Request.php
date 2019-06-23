<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Http\Request as RequestContract;

/**
 * @method static mixed all(array|mixed|null $keys = null)
 * @method static string|array|null cookie($key = null, $default = null)
 * @method static string getBasePath()
 * @method static string getHost()
 * @method static mixed input(string|null $key = null, $default = null)
 * @method static boolean isMethod(string $method)
 * @method static boolean isSecure()
 * @method static string method()
 */
class Request extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'request';
    }
}