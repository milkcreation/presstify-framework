<?php

namespace tiFy\Wp\Partial\Partials\Pagination;

use tiFy\Partial\Partials\Pagination\PaginationUrl;

class WpPaginationUrl extends PaginationUrl
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($baseurl = null)
    {
        $baseurl = $baseurl
            ?: url_factory(url()->full())->deleteSegments('/page/\d')->appendSegment('page/%d')->getDecode();

        parent::__construct($baseurl);
    }
}