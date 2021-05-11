<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Kernel\Application;
use Pollen\WpPost\WpPostProxy;
use Pollen\WpTaxonomy\WpTaxonomyProxy;
use Pollen\WpUser\WpUserProxy;

class WpApplication extends Application implements WpApplicationInterface
{
    use WpPostProxy;
    use WpTaxonomyProxy;
    use WpUserProxy;
}