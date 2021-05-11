<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Kernel\Application;
use Pollen\WpPost\WpPostProxy;
use Pollen\WpTerm\WpTermProxy;
use Pollen\WpUser\WpUserProxy;

class WpApplication extends Application implements WpApplicationInterface
{
    use WpPostProxy;
    use WpTermProxy;
    use WpUserProxy;
}