<?php

namespace tiFy\Apps;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;
use tiFy\Components\Tools\ClassInfo\ClassInfo;

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
     * Récupération de l'url vers un asset.
     *
     * @param string $filename Chemin relatif vers le fichier du dossier des assets.
     *
     * @return string
     */
    public function appAsset($filename);

    /**
     * {@inheritdoc}
     */
    public function appClassLoad($namespace, $base_dir = null);

    /**
     * Récupération du nom complet de l'application.
     *
     * @return string
     */
    public function appClassname();

    /**
     * {@inheritdoc}
     */
    public function appConfig($key = null, $default = null);

    /**
     * Récupération du chemin absolu vers le repertoire de stockage de l'application.
     *
     * @param string|object $app Classe de rappel du controleur de l'application associée.
     *
     * @return string
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
     * Récupération de l'espace de nom de l'application.
     *
     * @return string
     */
    public function appNamespace();

    /**
     * Récupération de la classe de reflection d'une application déclarée.
     *
     * @return ClassInfo|\ReflectionClass
     */
    public function appClassInfo();

    /**
     * {@inheritdoc}
     */
    public function appRegister($attrs = []);

    /**
     * Récupération du chemin relatif vers le répertoire de stockage de l'application.
     * @internal Basé sur le chemin absolu de la racine du projet
     *
     * @return string
     */
    public function appRelPath($app = null);

    /**
     * Récupération de la classe de rappel de propriété de la requête globale.
     *
     * @param string $property Propriété de la requête à traiter $_POST (alias post, request)|$_GET (alias get, query)|$_COOKIE (alias cookie, cookies)|attributes|$_FILES (alias files)|SERVER (alias server)|headers.
     *
     * @return Request|FileBag|HeaderBag|ParameterBag|ServerBag
     */
    public function appRequest($property = '');

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
     * Récupération du nom court de l'application.
     *
     * @return string
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
     *
     * @return string
     */
    public function appTemplateMake($name);

    /**
     *
     * @return string
     */
    public function appTemplateRender($name, $args = []);

    /**
     * {@inheritdoc}
     */
    public function appUpperName($name = null, $underscore = true);

    /**
     * Récupération de l'url absolue vers le repertoire de stockage de l'application.
     *
     * @return string
     */
    public function appUrl($app = null);
}