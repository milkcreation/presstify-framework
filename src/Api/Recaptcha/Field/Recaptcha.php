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
    public function display()
    {
        $output = "";
        $output .= "<input type=\"hidden\" name=\"{$this->getName()}\" value=\"-1\">";
        $output .= "<div id=\"{$this->get('attrs.id')}\" class=\"g-recaptcha\" data-sitekey=\"{$this->get('sitekey')}\" data-theme=\"{$this->get('theme')}\" data-tabindex=\"{$this->get('tabindex')}\"></div>";

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        /** @var RecaptchaInterface $recaptcha */
        $recaptcha = app('api.recaptcha');

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