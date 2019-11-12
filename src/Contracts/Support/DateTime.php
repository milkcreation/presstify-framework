<?php declare(strict_types=1);

namespace tiFy\Contracts\Support;

use Carbon\Carbon;
use DateTime as BaseDateTime;
use DateTimeZone;

/**
 * @mixin Carbon
 */
interface DateTime
{
    /**
     * {@inheritDoc}
     *
     * @return BaseDateTime|static|null
     */
    public static function createFromFormat($format, $time, $tz=null);

    /**
     * Récupération du fuseau horaire par défaut.
     *
     * @return DateTimeZone
     */
    public static function getGlobalTimeZone(): DateTimeZone;

    /**
     * Définition du fuseau horaire par défaut.
     *
     * @param DateTimeZone|null $tz
     *
     * @return DateTimeZone
     */
    public static function setGlobalTimeZone(?DateTimeZone $tz = null): DateTimeZone;
}