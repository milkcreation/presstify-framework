<?php declare(strict_types=1);

namespace tiFy\Form;

use tiFy\Form\Concerns\AddonAwareTrait;
use tiFy\Metabox\MetaboxDriver;

abstract class AddonMetaboxDriver extends MetaboxDriver
{
    use AddonAwareTrait;

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->form()->formManager()->resources("/addon/{$this->addon()->getAlias()}/metabox");
    }
}