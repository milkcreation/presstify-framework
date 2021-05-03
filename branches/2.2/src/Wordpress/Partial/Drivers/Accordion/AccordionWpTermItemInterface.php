<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers\Accordion;

use Pollen\Partial\Drivers\Accordion\AccordionItemInterface;
use WP_Term;

interface AccordionWpTermItemInterface extends AccordionItemInterface
{
    /**
     * Récupération du terme Wordpress associé.
     *
     * @return WP_Term
     */
    public function term(): WP_Term;
}