<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\FactorySession;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Kernel\Parameters\ParamsBagController;
use tiFy\Form\Factory\ResolverTrait;

class Session implements FactorySession
{
    use ResolverTrait;

    /**
     * Identifiant de session.
     * @var null
     */
    protected $session = null;

    /**
     * Préfixe de la clé d'indexe d'enregistrement de la données en cache.
     * @var string
     */
    protected $transientPrefix = 'tify_form_';

    /**
     * Délai d'expiration du cache
     * @internal MINUTE_IN_SECONDS | HOUR_IN_SECONDS | DAY_IN_SECONDS | WEEK_IN_SECONDS | YEAR_IN_SECONDS
     * @var float|int
     */
    protected $transientExpiration = HOUR_IN_SECONDS;

    /**
     * Liste des attributs privés.
     * @var array
     */
    protected $transientPrivate = ['session'];

    /**
     * CONSTRUCTEUR.
     *
     * @param FormFactory $form Instance du contrôleur de formulaire.
     *
     * @return void
     */
    public function __construct(FormFactory $form)
    {
        $this->form = $form;

        //$this->cleanTransient();
    }

    /**
     * Génération d'un identifiant de session.
     *
     * @return string
     */
    private function generateSession()
    {
        return \wp_hash(uniqid() . $this->getForm()->getUid());
    }

    /**
     * Récupération l'identifiant de session.
     *
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Récupération du préfixe de la clé d'indexe d'enregistrement de la données en cache.
     *
     * @return string
     */
    public function getTransientPrefix()
    {
        return $this->transientPrefix;
    }

    /**
     * Initialisation de la session.
     *
     * @return string
     */
    public function initSession()
    {
        if ($this->getSession()) :
        elseif ($session = $this->getHandle()->getGlobalVar('session_' . $this->getForm()->getUid())) :
            $this->session = $session;
        else :
            $this->session = $this->generateSession();
            $this->initTransient();
        endif;

        return $this->session;
    }

    /**
     * Initialisation du cache.
     *
     * @return bool
     */
    public function initTransient()
    {
        return $this->setTransient();
    }

    /**
     * Récupération d'attribut depuis les données de cache.
     *
     * @param string $key Clé d'indexe de l'attribut à récupérer. Retourne le liste complète si null.
     *
     * @return mixed
     */
    public function getTransient($attr = null)
    {
        // Bypass
        if (!$session = $this->getSession()) :
            return;
        endif;

        $transient = get_transient($this->getTransientPrefix() . $this->getSession());

        if (is_null($attr)) :
            return $transient;
        elseif (isset($transient[$attr])) :
            return $transient[$attr];
        endif;
    }

    /**
     * Suppression des données mises en cache.
     *
     * @return bool
     */
    public function deleteTransient()
    {
        // Bypass
        if (!$session = $this->getSession()) :
            return;
        endif;

        return delete_transient($this->getTransientPrefix() . $this->getSession());
    }

    /**
     * Définition de données enregistrées en cache.
     *
     * @param array $data Liste des données enregistrées.
     *
     * @return bool
     */
    public function setTransient($data = [])
    {
        // Bypass
        if (!$session = $this->getSession()) :
            return;
        endif;

        foreach ($this->transientPrivate as $attr) :
            if (isset($data[$attr])) :
                unset($data[$attr]);
            endif;
        endforeach;

        $data = array_merge(
            [
                'ID' => $this->getForm()->getName(),
                'session' => $this->getSession()
            ],
            $data
        );

        return set_transient($this->getTransientPrefix() . $this->getSession(), $data, $this->transientExpiration);
    }

    /**
     * Mise à jour des données mise en cache.
     *
     * @param array $data Liste des données mise à jour.
     *
     * @return bool
     */
    public function updateTransient($data = [])
    {
        // Bypass
        if (!$session = $this->getSession()) :
            return;
        endif;

        foreach ($this->transientPrivate as $attr) :
            if (isset($data[$attr])) :
                unset($data[$attr]);
            endif;
        endforeach;

        $data = array_merge(
            $this->getTransient(),
            $data
        );

        return set_transient($this->getTransientPrefix() . $this->getSession(), $data, $this->transientExpiration);
    }

    /**
     * Nettoyage du cache arrivé à expiration.
     * @todo
     *
     * @return bool
     */
    public function cleanTransient()
    {
        //return tify_purge_transient($this->getTransientPrefix(), $this->transientExpiration);
    }
}