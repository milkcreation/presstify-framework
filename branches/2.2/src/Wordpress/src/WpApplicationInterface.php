<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Kernel\ApplicationInterface;
use Pollen\WpPost\WpPostProxyInterface;
use Pollen\WpTerm\WpTermProxyInterface;
use Pollen\WpUser\WpUserProxyInterface;

interface WpApplicationInterface extends
    ApplicationInterface,
    WpPostProxyInterface,
    WpTermProxyInterface,
    WpUserProxyInterface
{

}