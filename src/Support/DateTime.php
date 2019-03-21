<?php declare(strict_types=1);

namespace tiFy\Support;

use Carbon\Carbon;
use DateTimeZone;
use Exception;

class DateTime extends Carbon
{
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
        if (is_null($tz)) :
            $tz = new DateTimeZone(request()->server('TZ') ? : 'UTC');
        endif;

        parent::__construct($time, $tz);
    }
}