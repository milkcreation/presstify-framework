<?php declare(strict_types=1);

namespace tiFy\Form\ButtonDrivers;

use tiFy\Contracts\Form\SubmitButtonDriver as SubmitButtonDriverContract;
use tiFy\Form\ButtonDriver as BaseButtonDriver;

class SubmitButtonDriver extends BaseButtonDriver implements SubmitButtonDriverContract
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            'type'      => 'submit',
            'content'   => __('Envoyer', 'tify')
        ]);
    }
}