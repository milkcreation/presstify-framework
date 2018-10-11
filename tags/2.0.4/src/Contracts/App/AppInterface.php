<?php

namespace tiFy\Contracts\App;

use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;
use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Contracts\Views\ViewInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Kernel\Assets\Assets;
use tiFy\Kernel\ClassInfo\ClassInfo;
use tiFy\Kernel\Request\Request;
use tiFy\Kernel\Logger\Logger;

interface AppInterface
{
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
     * @param string $path Chemin relatif vers le fichier du dossier des assets.
     *
     * @return string
     */
    public function appAssetUrl($path = '');

    /**
     * Récupération de la classe de rappel du controleur des Assets.
     *
     * @return Assets
     */
    public function appAssets();

    /**
     * Initialisation du controleur d'application.
     * @internal Lancé à l'issue de l'initialisation complète.
     *
     * @return void
     */
    public function appBoot();

    /**
     * Récupération de la classe de reflection d'une application déclarée.
     *
     * @return ClassInfo|\ReflectionClass
     */
    public function appClassInfo();

    /**
     * Récupération du nom complet de l'application.
     *
     * @return string
     */
    public function appClassname();

    /**
     * {@inheritdoc}
     */
    public function appConfig($key = null, $default = []);

    /**
     * Récupération de la classe de rappel du conteneur d'injection de dépendances.
     *
     * @return ContainerInterface
     */
    public function appContainer();

    /**
     * Récupération du chemin absolu vers le repertoire de stockage de l'application.
     *
     * @return string
     */
    public function appDirname();

    /**
     * Déclaration d'un événement.
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
    public function appEvents();

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
    public function appLowerName($name = null);

    /**
     * Récupération de l'espace de nom de l'application.
     *
     * @return string
     */
    public function appNamespace();

    /**
     * Récupération du chemin relatif vers le répertoire de stockage de l'application.
     * @internal Basé sur le chemin absolu de la racine du projet
     *
     * @return string
     */
    public function appRelPath();

    /**
     * Récupération de la classe de rappel de propriété de la requête globale.
     *
     * @param string $property Propriété de la requête à traiter $_POST (alias post, request)|$_GET (alias get, query)|$_COOKIE (alias cookie, cookies)|attributes|$_FILES (alias files)|SERVER (alias server)|headers.
     *
     * @return Request|FileBag|HeaderBag|ParameterBag|ServerBag
     */
    public function appRequest($property = '');

    /**
     * Déclaration d'un service.
     *
     * @param string $alias Nom de qualification du service.
     * @param string|object|callable $concrete Nom de classe|Instance de classe|fonction anonyme.
     * @param bool $singleton Indicateur d'instance unique.
     *
     * @return void
     */
    public function appServiceAdd($alias, $concrete = null, $singleton = false);

    /**
     * Récupération d'un service.
     *
     * @param string $alias Nom de qualification du service.
     * @param array $args Liste des variables passées en argument au service.
     *
     * @return null|object
     */
    public function appServiceGet($alias, $args = []);

    /**
     * Vérification d'existance d'un service.
     *
     * @param string $alias Nom de qualification du service.
     *
     * @return bool
     */
    public function appServiceHas($alias);

    /**
     * Déclaration d'un service d'instance unique.
     *
     * @param string $alias Nom de qualification du service.
     * @param string|object|callable $concrete Nom de classe|Instance de classe|fonction anonyme.
     *
     * @return void
     */
    public function appServiceShare($alias, $concrete = null);

    /**
     * Récupération du nom court de l'application.
     *
     * @return string
     */
    public function appShortname();

    /**
     * Récupération de la classe de rappel du controleur de templates.
     *
     * @param array $options Liste des options de configuration du controleur de template.
     * 
     * @return ViewsInterface
     */
    public function appTemplates($options = []);

    /**
     * Définition de fonction d'appel.
     *
     * @param string $name Nom de qualification d'appel de la fonction.
     * @param string|callable Fonction à executer.
     *
     * @return ViewsInterface
     */
    public function appTemplateMacro($name, $function);

    /**
     * Définition d'un template d'affichage.
     *
     * @param string $name Nom de qualification du gabarit.
     * @param array $args Listes des variables passées en argument.
     *
     * @return ViewInterface
     */
    public function appTemplateMake($name, $args = []);

    /**
     * Rendu d'un gabarit d'affichage.
     *
     * @param string $name Nom de qualification du gabarit.
     * @param array $args Listes des variables passées en argument.
     *
     * @return string
     */
    public function appTemplateRender($name, $args = []);

    /**
     * Récupération d'une vue basée sur un gabarit d'affichage.
     *
     * @param string $name Nom de qualification du gabarit.
     * @param array $args Listes des variables passées en argument.
     *
     * @return string
     */
    public function appTemplateView($name, $args = []);

    /**
     * {@inheritdoc}
     */
    public function appUpperName($name = null);

    /**
     * Récupération de l'url absolue vers le repertoire de stockage de l'application.
     *
     * @return string
     */
    public function appUrl();
}