<?php declare(strict_types=1);

namespace tiFy\Support;

use Carbon\Carbon;
use DateTime as BaseDateTime;
use DateTimeZone;
use tiFy\Contracts\Support\DateTime as DateTimeContract;
use Exception;

class DateTime extends Carbon implements DateTimeContract
{
    /**
     * Instance du fuseau horaire utilisé par défaut.
     * @var DateTimeZone
     */
    protected static $GlobalTimeZone;

    /**
     * CONSTRUCTEUR.
     *
     * @param string|null $time
     * @param null|DateTimeZone $tz
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct($time = null, $tz = null)
    {
        if (is_null($tz)) {
            $tz = self::getGlobalTimeZone();
        }
        parent::__construct($time, $tz);
    }

    /**
     * {@inheritDoc}
     *
     * @return BaseDateTime|static|null
     */
    public static function createFromFormat($format, $time, $tz = null)
    {
        return parent::createFromFormat($format, $time, is_null($tz) ? self::getGlobalTimeZone() : $tz);
    }

    /**
     * @inheritDoc
     */
    public static function getGlobalTimeZone(): DateTimeZone
    {
        return self::$GlobalTimeZone ?: self::setGlobalTimeZone();
    }

    /**
     * @inheritDoc
     */
    public static function setGlobalTimeZone(?DateTimeZone $tz = null): DateTimeZone
    {
        return self::$GlobalTimeZone = $tz ?: new DateTimeZone(
            getenv('APP_TIMEZONE') ?: request()->server('TZ', ini_get('date.timezone') ?: 'UTC')
        );
    }
}