<?php

namespace tiFy\Apps;

interface AppControllerInterface
{
    /**
     * Initialisation du controleur d'application.
     * @internal Lancé à l'issue de l'initialisation complète.
     *
     * @return void
     */
    public function appBoot();

    /**
     * Récupération du chemin absolu vers la racine du projet Web.
     *
     * @return void
     */
    public function appAbsPath();

    /**
     * Récupération du chemin absolu vers la racine de PresstiFy.
     *
     * @return void
     */
    public function appAbsDir();

    /**
     * Récupération de l'url absolue vers la racine de PresstiFy.
     *
     * @return void
     */
    public function appAbsUrl();

    /**
     * Ajout d'une action Wordpress.
     *
     * @param string $tag Identification de l'accroche.
     * @param string $method Méthode de la classe à executer.
     * @param int $priority Priorité d'execution.
     * @param int $accepted_args Nombre d'argument permis.
     *
     * @return bool
     */
    public function appAddAction($tag, $method = '', $priority = 10, $accepted_args = 1);

    /**
     * Ajout d'un filtre Wordpress.
     *
     * @param string $tag Identification de l'accroche.
     * @param string $class_method Méthode de la classe à executer.
     * @param int $priority Priorité d'execution.
     * @param int $accepted_args Nombre d'argument permis.
     *
     * @return bool
     */
    public function appAddFilter($tag, $method = '', $priority = 10, $accepted_args = 1);

    /**
     * {@inheritdoc}
     */
    public function appAsset($filename);

    /**
     * {@inheritdoc}
     */
    public function appClassLoad($namespace, $base_dir = null);

    /**
     * {@inheritdoc}
     */
    public function appClassname();

    /**
     * {@inheritdoc}
     */
    public function appConfig($key = null, $default = null);

    /**
     * {@inheritdoc}
     */
    public function appDirname($app = null);

    /**
     * {@inheritdoc}
     */
    public function appEvent();

    /**
     * Déclaration d'un événement.
     * @see http://event.thephpleague.com/2.0/emitter/basic-usage/
     *
     * @param string $name Identifiant de qualification de l'événement.
     * @param callable|ListenerInterface $listener Fonction anonyme ou Classe de traitement de l'événement.
     * @param int $priority Priorité de traitement.
     *
     * @return EmitterInterface
     */
    public function appEventListen($name, $listener, $priority = 0);

    /**
     * Déclenchement d'un événement.
     * @see http://event.thephpleague.com/2.0/events/classes/
     *
     * @param string|object $event Identifiant de qualification de l'événement.
     * @param mixed ... $args Variable(s) passée(s) en argument.
     *
     * @return null|EventInterface
     */
    public function appEventTrigger($event);

    /**
     * {@inheritdoc}
     */
    public function appExists();

    /**
     * {@inheritdoc}
     */
    public function appGet($key = null, $default = null);

    /**
     * {@inheritdoc}
     *
     * @return self|object
     */
    public static function appInstance($classname = null, $args = []);

    /**
     * Récupération de la classe de rappel de journalisation.
     *
     * @return Logger
     */
    public function appLog();

    /**
     * {@inheritdoc}
     */
    public function appLowerName($name = null, $separator = '-');

    /**
     * {@inheritdoc}
     */
    public function appNamespace();

    /**
     * {@inheritdoc}
     */
    public function appReflectionClass();

    /**
     * {@inheritdoc}
     */
    public function appRegister($attrs = []);

    /**
     * {@inheritdoc}
     */
    public function appRelPath($app = null);

    /**
     * {@inheritdoc}
     */
    public function appRequest($property = '');

    /**
     * Vérification d'existance d'une variable de requête globale
     * @deprecated
     *
     * @param string $key Identifiant de qualification de l'argument de requête
     * @param string $type Type de requête à traiter POST|GET|COOKIE|FILES|SERVER ...
     *
     *
     * @throws LogicException
     * @throws ReflectionException
     * @return mixed
     */
    public function appRequestHas($key, $type = '');

    /**
     * Récupération d'une variable de requête globale
     * @deprecated
     *
     * @param string $key Identifiant de qualification de l'argument de requête
     * @param mixed $default Valeur de retour par défaut
     * @param string $type Type de requête à traiter POST|GET|COOKIE|FILES|SERVER ...
     *
     * @return mixed
     */
    public function appRequestGet($key, $default = '', $type = '');

    /**
     * Définition d'une variable de requête globale
     * @deprecated
     *
     * @param array $parameters Liste des paramètres. Tableau associatif
     * @param string $type Type de requête à traiter POST|GET|COOKIE|FILES|SERVER ...
     *
     * @return mixed
     */
    public function appRequestAdd($parameters = [], $type = '');

    /**
     * {@inheritdoc}
     */
    public function appServiceAdd($alias, $concrete = null, $share = false);

    /**
     * {@inheritdoc}
     */
    public function appServiceGet($alias, $args = []);

    /**
     * {@inheritdoc}
     */
    public function appServiceHas($alias);

    /**
     * {@inheritdoc}
     */
    public function appServiceProvider($provider);

    /**
     * {@inheritdoc}
     */
    public function appServiceShare($alias, $concrete = null);

    /**
     * {@inheritdoc}
     */
    public function appSet($key, $value);

    /**
     * {@inheritdoc}
     */
    public function appShortname();

    /**
     * Récupération de la classe de rappel du controleur de templates.
     *
     * @return \League\Plates\Engine
     */
    public function appTemplates();

    /**
     *
     * @return string
     */
    public function appTemplateMake($name);

    /**
     * @todo
     * @return string
     */
    public function appTemplateRender($name, $args = []);

    /**
     * {@inheritdoc}
     */
    public function appUpperName($name = null, $underscore = true);

    /**
     * {@inheritdoc}
     */
    public function appUrl($app = null);
}