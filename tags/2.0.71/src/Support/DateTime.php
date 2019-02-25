<?php declare(strict_types=1);

namespace tiFy\Support;

use Carbon\Carbon;
use DateTimeZone;

class DateTime extends Carbon
{
    /**
     * DateTime constructor.
     *
     * @param string|null $time
     * @param null $tz
     *
     * @return void
     */
    public function __construct(?string $time = null, $tz = null)
    {
        if (is_null($tz)) :
            $tz = new DateTimeZone(request()->server('TZ') ? : 'UTC');
        endif;

        parent::__construct($time, $tz);
    }
}