<?php

namespace tiFy\Api\Recaptcha\Field;

use tiFy\Contracts\Api\Recaptcha as RecaptchaInterface;
use tiFy\Field\FieldController;

/**
 * Class Recaptcha
 * @package tiFy\Api\Recaptcha\Field
 *
 * @see https://developers.google.com/recaptcha/docs/display
 */
class Recaptcha extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [
        'sitekey'  => '',
        'theme'    => 'light',
        'tabindex' => 0
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        /** @var RecaptchaInterface $recaptcha */
        $recaptcha = app('api.recaptcha');

        if (!$this->get('attrs.id')) :
            $this->set('attrs.id', 'tiFyField-recapcha--' . $this->getIndex());
        endif;

        $this->set('attrs.data-tabindex', $this->get('tabindex'));

        if (!$this->get('sitekey')) :
            $this->set('sitekey', $recaptcha->getSiteKey());
        endif;

        $recaptcha->addWidgetRender(
            $this->get('attrs.id'),
            [
                'sitekey' => $this->get('sitekey'),
                'theme'   => $this->get('theme')
            ]
        );
    }
}