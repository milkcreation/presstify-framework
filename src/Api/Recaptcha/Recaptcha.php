<?php

/**
 * @see https://github.com/google/recaptcha
 */

namespace tiFy\Api\Recaptcha;

use ReCaptcha\ReCaptcha as ReCaptchaSdk;
use ReCaptcha\RequestMethod;
use ReCaptcha\RequestMethod\SocketPost;

class Recaptcha extends ReCaptchaSdk
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $secretkey Clé secrète, requise pour la communication entre le site et Google. Veillez à ne surtout pas divulger cette clé !
     *      @var string $sitekey Clé du site, utilisée dans le code HTML
     * }
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    protected function __construct($attrs = [])
    {
        try {
            parent::__construct($attrs['secretkey'], (ini_get('allow_url_fopen') ? null : new SocketPost));
            $this->attributes = $attrs;
        } catch (\RuntimeException $e) {
            wp_die($e->getMessage(), __('Api reCaptcha : Erreur de configuration', 'tify'), $e->getCode());
        }
    }

    /**
     * Court-circuitage de l'instanciation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'instanciation.
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Initialisation.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public static function create($attrs = [])
    {
        return new static(
            array_merge(
                [
                    'secretkey' => '',
                    'sitekey'   => '',
                ],
                $attrs
            )
        );
    }

    /**
     * Récupération de la clé de site publique.
     *
     * @return string
     */
    public function getSiteKey()
    {
        if (isset($this->attributes['sitekey'])) :
            return $this->attributes['sitekey'];
        endif;
    }
}