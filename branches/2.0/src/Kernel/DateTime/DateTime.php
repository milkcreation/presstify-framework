<?php

namespace tiFy\Kernel\DateTime;

use Carbon\Carbon;
use DateTimeZone;
class DateTime extends Carbon
{
    public function __construct(?string $time = null, $tz = null)
    {
        if (is_null($tz)) :
            $tz = new DateTimeZone(request()->server('TZ') ? : 'UTC');
        endif;

        parent::__construct($time, $tz);
    }
}