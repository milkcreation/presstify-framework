<?php

use tiFy\tiFy;
use tiFy\Contracts\Field\FieldItemInterface;
use tiFy\Contracts\Kernel\EventsInterface;
use tiFy\Contracts\Partial\PartialItemInterface;
use tiFy\Contracts\Views\ViewInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Field\Field;
use tiFy\Form\Form;
use tiFy\Kernel\Kernel;
use tiFy\Partial\Partial;
use tiFy\Route\Route;

/**
 * KERNEL
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!function_exists('app')) :
    /**
     * App - Controleur de l'application.
     * {@internal Si $abstract est null > Retourne l'instance de l'appication.}
     * {@internal Si $abstract est qualifié > Retourne la résolution du service qualifié.}
     *
     * @param null|string $abstract Nom de qualification du service.
     * @param array $args Liste des variables passé en arguments lors de la résolution du service.
     *
     * @return \tiFy\Contracts\App\AppInterface|\tiFy\App\Container\AppContainer
     */
    function app($abstract = null, $args = [])
    {
        $factory = Kernel::App();

        if (is_null($abstract)) :
            return $factory;
        endif;

        return tiFy::instance()->resolve($abstract, $args);
    }
endif;

if (!function_exists('assets')) :
    /**
     * Assets - Controleur des assets.
     * @see \tiFy\Kernel\Assets\Assets
     *
     * @return string
     */
    function assets()
    {
        return Kernel::Assets();
    }
endif;

if (!function_exists('class_info')) :
    /**
     * ClassInfo - Controleur d'informations sur une classe.
     * @see \tiFy\Kernel\ClassInfo\ClassInfo
     *
     * @param string|object Nom complet ou instance de la classe.
     *
     * @return string
     */
    function class_info($class)
    {
        return Kernel::ClassInfo($class);
    }
endif;

if (!function_exists('class_loader')) :
    /**
     * ClassLoader - Controleur de déclaration d'espaces de nom et d'inclusion de fichier automatique.
     *
     * @return \tiFy\Kernel\Composer\ClassLoader
     */
    function class_loader()
    {
        return Kernel::ClassLoader();
    }
endif;

if (!function_exists('config')) :
    /**
     * Config - Controleur de configuration.
     * {@internal Si $key est null > Retourne la classe de rappel du controleur.}
     * {@internal Si $key est un tableau > Utilise le tableau en tant que liste des attributs de configuration à définir.}
     *
     * @param null|array|string Clé d'indice|Liste des attributs de configuration à définir.
     *
     * @return mixed|\tiFy\Kernel\Config\Config
     */
    function config($key = null, $default = null)
    {
        $factory = Kernel::Config();

        if (is_null($key)) :
            return $factory;
        endif;

        if (is_array($key)) :
            return $factory->set($key);
        endif;

        return $factory->get($key, $default);
    }
endif;

if (!function_exists('container')) :
    /**
     * Container - Controleur d'injection de dépendances.
     * {@internal Si $alias est null > Retourne la classe de rappel du controleur.}
     * @deprecated
     *
     * @param string $abstract Nom de qualification du service à récupérer.
     *
     * @return \tiFy\Kernel\Container\Container
     */
    function container($abstract = null)
    {
        $factory = Kernel::Container();

        if (is_null($abstract)) :
            return $factory;
        endif;

        return $factory->get($abstract);
    }
endif;

if (!function_exists('events')) :
    /**
     * Events - Controleur d'événements.
     *
     * @return EventsInterface
     */
    function events()
    {
        return Kernel::Events();
    }
endif;

if (!function_exists('field')) :
    /**
     * Field - Controleur de champs.
     *
     * @param null|string $name Nom de qualification.
     * @param mixed $id Nom de qualification ou Liste des attributs de configuration.
     * @param mixed $attrs Liste des attributs de configuration.
     *
     * @return null|Field|FieldItemInterface
     */
    function field($name = null, $id = null, $attrs = null)
    {
        /** @var Field $factory */
        $factory = app(Field::class);

        if (is_null($name)) :
            return $factory;
        endif;

        return $factory->get($name, $id, $attrs);
    }
endif;

if (!function_exists('form')) :
    /**
     * Formulaire - Controleur de champs.
     *
     * @param null|string $name Nom de qualification du formulaire.
     *
     * @return null|Field|FieldItemInterface
     */
    function form($name = null)
    {
        /** @var Form $factory */
        $factory = app(Form::class);

        if (is_null($name)) :
            return $factory;
        endif;

        return $factory->get($name);
    }
endif;

if (!function_exists('logger')) :
    /**
     * Logger - Controleur de journalisation des actions.
     *
     * @return \tiFy\Kernel\Logger\Logger
     */
    function logger()
    {
        return Kernel::Logger();
    }
endif;

if (!function_exists('partial')) :
    /**
     * Partial - Contrôleurs d'affichage.
     *
     * @param null|string $name Nom de qualification.
     * @param mixed $id Nom de qualification ou Liste des attributs de configuration.
     * @param mixed $attrs Liste des attributs de configuration.
     *
     * @return null|Partial|PartialItemInterface
     */
    function partial($name = null, $id = null, $attrs = null)
    {
        /** @var Partial $factory */
        $factory = app(Partial::class);

        if (is_null($name)) :
            return $factory;
        endif;

        return $factory->get($name, $id, $attrs);
    }
endif;

if (!function_exists('paths')) :
    /**
     * Paths - Controleur des chemins vers les répertoires de l'application.
     *
     * @return \tiFy\Kernel\Filesystem\Paths
     */
    function paths()
    {
        return Kernel::Paths();
    }
endif;

if (!function_exists('request')) :
    /**
     * Request - Controleur de traitement de la requête principal
     *
     * @return \tiFy\Kernel\Http\Request
     */
    function request()
    {
        return Kernel::Request();
    }
endif;

if (! function_exists('resolve')) {
    /**
     * Resolve - Récupération d'une instance de service fourni par le conteneur d'injection de dépendances.
     *
     * @param string $name Nom de qualification du service
     *
     * @return mixed
     */
    function resolve($name)
    {
        return container($name);
    }
}

if (!function_exists('view')) :
    /**
     * View - Récupération d'un instance du controleur des vues ou l'affichage d'un gabarit.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'intance du controleur principal.}
     * {@internal Sinon récupére le gabarit d'affichage et passe les variables en argument.}
     *
     * @param null|string view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewsInterface|ViewInterface
     */
    function view($view = null, $data = [])
    {
        $factory = Kernel::TemplatesEngine();

        if (func_num_args() === 0) :
            return $factory;
        endif;

        return $factory->make($view, $data);
    }
endif;

/**
 * ROUTE
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!function_exists('is_route')) :
    /**
     * Indicateur de contexte de la requête principale.
     *
     * @return bool
     */
    function is_route()
    {
        return tiFy::instance()->get(Route::class)->is();
    }
endif;

if (!function_exists('tify_route_current_name')) :
    /**
     * Récupération du nom de qualification de la route courante à afficher.
     *
     * @return string
     */
    function tify_route_current_name()
    {
        return tiFy::instance()->get(Route::class)->currentName();
    }
endif;

if (!function_exists('tify_route_current_args')) :
    /**
     * Récupération des arguments de requête passés dans la route courante.
     *
     * @return array
     */
    function tify_route_current_args()
    {
        return tiFy::instance()->get(Route::class)->currentArgs();
    }
endif;

if (!function_exists('tify_route_exists')) :
    /**
     * Vérifie la correspondance du nom de qualification d'une route existante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool
     */
    function tify_route_exists($name)
    {
        return tiFy::instance()->get(Route::class)->exists($name);
    }
endif;

if (!function_exists('tify_route_has_current')) :
    /**
     * Vérifie si la page d'affichage courante correspond à une route déclarée
     *
     * @return bool
     */
    function tify_route_has_current()
    {
        return tiFy::instance()->get(Route::class)->hasCurrent();
    }
endif;

if (!function_exists('tify_route_is_current')) :
    /**
     * Vérifie de correspondance du nom de qualification la route courante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool
     */
    function tify_route_is_current($name)
    {
        return tiFy::instance()->get(Route::class)->isCurrent($name);
    }
endif;

if (!function_exists('tify_route_redirect')) :
    /**
     * Redirection de page vers une route déclarée.
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $args Liste arguments passés en variable de requête dans l'url
     * @param int $status_code Code de redirection. @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
     *
     * @return void
     */
    function tify_route_redirect($name, array $args = [], $status_code = 301)
    {
        return tiFy::instance()->get(Route::class)->redirect($name, $args, $status_code);
    }
endif;

if (!function_exists('tify_route_url')) :
    /**
     * Récupération de l'url d'une route déclarée
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $replacements Arguments de remplacement
     *
     * @return string
     */
    function tify_route_url($name, array $replacements = [])
    {
        return tiFy::instance()->get(Route::class)->url($name, $replacements);
    }
endif;