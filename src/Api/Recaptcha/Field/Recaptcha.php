<?php

namespace tiFy\Api\Recaptcha\Field;

use tiFy\Contracts\Api\Recaptcha as RecaptchaContract;
use tiFy\Field\FieldFactory;
use tiFy\Field\FieldView;

class Recaptcha extends FieldFactory
{
    /**
     * Instance du controleur de champ reCaptcha
     * @var RecaptchaContract
     */
    protected $recaptcha;

    /**
     * Liste des attributs de configuration.
     * @see https://developers.google.com/recaptcha/docs/display
     * @var array $attributs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var string $theme Couleur d'affichage du captcha. light|dark.
     *      @var string $sitekey Clé publique. Optionnel si l'API $recaptcha est active.
     *      @var string $secretkey Clé publique. Optionnel si l'API $recaptcha est active.
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'name'      => '',
        'value'     => '',
        'attrs'     => [],
        'viewer'    => [],
        'theme'     => 'light',
        'tabindex'  => 0,
        'sitekey'   => '',
        'secretkey' => ''
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->recaptcha = app('api.recaptcha');

        if (!$this->get('attrs.id')) :
            $this->set('attrs.id', 'Field-recapcha--' . $this->getIndex());
        endif;

        $this->set('attrs.data-tabindex', $this->get('tabindex'));

        if (!$this->get('sitekey')) :
            $this->set('sitekey', $this->recaptcha->getSiteKey());
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        $this->recaptcha->addWidgetRender($this->get('attrs.id'), [
            'sitekey' => $this->get('sitekey'),
            'theme'   => $this->get('theme')
        ]);

        return parent::display();
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) :
            $cinfo = class_info(Recaptcha::class);
            $default_dir = $cinfo->getDirname() . '/views/';
            $this->viewer = view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(FieldView::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : (is_dir($default_dir) ? $default_dir : $cinfo->getDirname())
                )
                ->set('field', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }
}