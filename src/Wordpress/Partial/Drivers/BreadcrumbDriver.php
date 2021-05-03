<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers;

use Pollen\Partial\Drivers\Breadcrumb\BreadcrumbCollectionInterface;
use Pollen\Partial\Drivers\BreadcrumbDriver as BaseBreadcrumbDriver;
use tiFy\Wordpress\Partial\Drivers\Breadcrumb\BreadcrumbCollection;

class BreadcrumbDriver extends BaseBreadcrumbDriver
{
    /**
     * @inheritDoc
     */
    public function collection(): BreadcrumbCollectionInterface
    {
        if (is_null($this->collection)) {
            $this->collection = new BreadcrumbCollection($this);
        }

        return $this->collection;
    }
}