<?php

namespace tiFy\Forms\Addons\CookieTransport;

use tiFy\Forms\Addons\AbstractAddonController;
use tiFy\Forms\Form\Form;

class CookieTransport extends AbstractAddonController
{
    /**
     * Liste des options du formulaire associé.
     * @var array
     */
    protected $formOptions = [
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
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        // Définition des fonctions de callback
        $this->callbacks = [
            'field_set_value'                => [$this, 'cb_field_set_value'],
            'handle_parse_query_fields_vars' => [$this, 'cb_handle_parse_query_fields_vars'],
        ];
    }

    /**
     * Court-circuitage des valeurs de champ
     *
     * @return void
     */
    public function cb_field_set_value(&$value, $field)
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
     * @param Handle $handle Classe de rappel de traitement du formulaire.
     *
     * @return void
     */
    public function cb_handle_parse_query_fields_vars(&$fields_vars, $fields, $handle)
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
        return $this->cookiePrefix . $this->form()->getUid();
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
