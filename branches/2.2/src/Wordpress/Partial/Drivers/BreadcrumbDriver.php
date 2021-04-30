<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers;

use tiFy\Partial\Drivers\Breadcrumb\BreadcrumbCollectionInterface as BaseBreadcrumbCollectionInterface;
use tiFy\Wordpress\Partial\Drivers\Breadcrumb\BreadcrumbCollection;
use tiFy\Partial\Drivers\BreadcrumbDriver as BaseBreadcrumbDriver;

class BreadcrumbDriver extends BaseBreadcrumbDriver
{
    /**
     * @inheritDoc
     */
    public function collection(): BaseBreadcrumbCollectionInterface
    {
        if (is_null($this->collection)) {
            $this->collection = new BreadcrumbCollection($this);
        }

        return $this->collection;
    }
}