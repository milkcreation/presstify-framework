<?php declare(strict_types=1);

namespace tiFy\Wordpress\Field;

use tiFy\Contracts\Field\FieldFactory as BaseFieldFactoryContract;
use tiFy\Field\FieldFactory as BaseFieldFactory;

abstract class FieldFactory extends BaseFieldFactory
{
    /**
     * {@inheritDoc}
     *
     * @return $this
     */
    public function parse(): BaseFieldFactoryContract
    {
        $this->set('viewer.directory', __DIR__ . '/Resources/views/' . $this->getAlias());

        parent::parse();

        $this->parseDefaults();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function viewer($view = null, $data = [])
    {
        if (is_null($this->viewer)) {
            $this->viewer = app()->get('field.viewer', [$this]);
        }

        if (func_num_args() === 0) {
            return $this->viewer;
        }

        return $this->viewer->make("_override::{$view}", $data);
    }
}