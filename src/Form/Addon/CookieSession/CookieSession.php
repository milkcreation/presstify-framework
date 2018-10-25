<?php

namespace tiFy\Form\Addon\CookieSession;

use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\AddonController;

class CookieSession extends AddonController
{
    /**
     * Liste des options par défaut du formulaire associé.
     * @var array
     */
    protected $defaultFormOptions = [
        'expire' => MONTH_IN_SECONDS
    ];

    /**
     * Liste des options par défaut des champs du formulaire associé.
     * @var array
     */
    protected $defaultFieldOptions = [
        'ignore' => false,
    ];

    /**
     * Prefixe du cookie d'enregistrement.
     * @var string
     */
    protected $cookiePrefix = 'tiFyFormsCookieTransport_';

    /**
     * Liste des données du cookie en cache.
     * @var null|array
     */
    protected $cookieDatas = null;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        return;

        $this->events()
            ->listen('field.init', [$this, 'onFieldInit'])
            ->listen('request.prepare', [$this, 'onRequestPrepare']);
    }

    /**
     * Court-circuitage des valeurs de champ
     *
     * @return void
     */
    public function onFieldInit(&$field)
    {
        if ($this->getFieldOption($field, 'ignore')) :
            return;
        endif;

        $value = $this->getCookieData($field->getSlug(), $value);
    }

    /**
     * Court-circuitage du traitement des variables de requête.
     *
     * @param $fields_vars
     * @param Field[] $fields Liste des classes de rappel de champ.
     *
     * @return void
     */
    public function onRequestPrepare(&$vars, $fields)
    {
        $datas = [];
        foreach ($fields as $field) :
            if ($this->getFieldAttr($field, 'ignore')) :
                continue;
            endif;

            $datas[$field->getSlug()] = $fields_vars[$field->getName()];
        endforeach;

        $this->setCookie($datas);
    }

    /**
     * Récupération de nom de qualification du cookie d'enregistrement des données.
     *
     * @return string
     */
    public function getCookieName()
    {
        return $this->cookiePrefix . $this->getForm()->getUid();
    }

    /**
     * Récupération de la durée en seconde du cycle de vie du cookie.
     *
     * @return int
     */
    public function getCookieExpire()
    {
        return (int)$this->getFormAttr('expire', 0);
    }

    /**
     * Définition du cookie.
     *
     * @param array $datas Liste des données d'enregistrement du cookie
     *
     * @return void
     */
    private function setCookie($datas = [])
    {
        setcookie(
            $this->getCookieName(),
            base64_encode(serialize(' ')),
            time() - $this->getCookieExpire(),
            SITECOOKIEPATH
        );
        setcookie(
            $this->getCookieName(),
            base64_encode(serialize($datas)),
            time() + $this->getCookieExpire(),
            SITECOOKIEPATH
        );
    }

    /**
     * Récupération de la valeur des données de cookie.
     *
     * @return array
     */
    private function getCookieDatas()
    {
        if (is_null($this->cookieDatas)) :
            if (isset($_COOKIE[$this->getCookieName()])) :
                return $this->cookieDatas = unserialize(base64_decode($_COOKIE[$this->getCookieName()]));
            else :
                return $this->cookieDatas = [];
            endif;
        else:
            return $this->cookieDatas;
        endif;
    }

    /**
     * Récupération de la valeur d'une donnée de cookie.
     *
     * @param string $key Clé d'index de la donnée à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    private function getCookieData($key, $default = '')
    {
        if (! $datas = $this->getCookieDatas()) :
            return $default;
        endif;

        if (isset($datas[$data])) :
            return $datas[$data];
        endif;

        return $default;
    }
}
