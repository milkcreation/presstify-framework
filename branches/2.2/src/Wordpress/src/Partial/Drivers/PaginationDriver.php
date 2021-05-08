<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers;

use Pollen\Partial\Drivers\PaginationDriver as BasePaginationDriver;
use Pollen\Partial\Drivers\PaginationDriverInterface as BasePaginationDriverInterface;
use tiFy\Wordpress\Partial\Drivers\Pagination\PaginationQuery;
use tiFy\Wordpress\Partial\Drivers\Pagination\PaginationQueryInterface;

class PaginationDriver extends BasePaginationDriver
{
    /**
     * @inheritDoc
     */
    public function parseQuery(): BasePaginationDriverInterface
    {
        $this->query = $this->pull('query');
        if (!$this->query instanceof PaginationQueryInterface) {
            $this->query = new PaginationQuery($this->query);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseUrl(): BasePaginationDriverInterface
    {
        if ($this->has('url.base')) {
            $this->query->setBaseUrl($this->get('url.base', null));
        }

        if ($this->has('url.segment')) {
            $this->query->setSegmentUrl($this->get('url.segment'));
        } else {
            $this->query->setSegmentUrl(true);
        }

        if ($this->has('url.index')) {
            $this->query->setPageIndex($this->get('url.index'));
        }

        if (!is_array($this->get('url'))) {
            $this->query->setBaseUrl($this->get('url', null));
        }

        return $this;
    }
}