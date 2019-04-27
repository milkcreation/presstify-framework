<?php declare(strict_types=1);

namespace tiFy\Support;

use Carbon\Carbon;
use DateTimeZone;
use Exception;

class DateTime extends Carbon
{
    /**
     * Instance du fuseau horaire utilisé par défaut.
     * @var DateTimeZone
     */
    protected static $GlobalTimeZone;

    /**
     * DateTime constructor.
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
            $tz = self::$GlobalTimeZone;
        }
        parent::__construct($time, $tz);
    }

    /**
     * Définition du fuseau horaire par défaut.
     *
     * @param DateTimeZone|null $tz
     *
     * @return DateTimeZone
     */
    public static function setGlobalTimeZone(?DateTimeZone $tz = null)
    {
        return self::$GlobalTimeZone = $tz ?: new DateTimeZone((request()->server('TZ') ?: 'UTC'));
    }

    /**
     * Récupération du fuseau horaire par défaut.
     *
     * @return DateTimeZone
     */
    public function getGlobalTimeZone()
    {
        return self::$GlobalTimeZone ? : self::setGlobalTimeZone();
    }
}