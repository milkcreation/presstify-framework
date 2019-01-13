<?php

use tiFy\tiFy;
use tiFy\Contracts\Kernel\Assets;
use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Contracts\Field\FieldController;
use tiFy\Contracts\Field\FieldManager;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Form\FormManager;
use tiFy\Contracts\Kernel\EventsManager;
use tiFy\Contracts\Kernel\Logger;
use tiFy\Contracts\Kernel\Request;
use tiFy\Contracts\Kernel\Validator;
use tiFy\Contracts\Partial\PartialController;
use tiFy\Contracts\Partial\PartialManager;
use tiFy\Contracts\PostType\PostTypeManager;
use tiFy\Contracts\PostType\PostTypeFactory;
use tiFy\Contracts\Routing\Router;
use tiFy\Contracts\Routing\Route;
use tiFy\Contracts\Routing\Url;
use tiFy\Contracts\Routing\UrlFactory;
use tiFy\Contracts\Taxonomy\TaxonomyManager;
use tiFy\Contracts\Taxonomy\TaxonomyFactory;
use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Contracts\View\ViewPattern;
use tiFy\Contracts\View\ViewPatternController;
use tiFy\Contracts\Wp\WpManager;
use tiFy\Contracts\Wp\PageHook;
use tiFy\Contracts\Wp\PageHookItem;
use tiFy\Kernel\Kernel;
use tiFy\Kernel\Http\RedirectResponse as HttpRedirect;

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
     *
     * @return Assets
     */
    function assets()
    {
        /** @var Assets $factory */
        $factory = app('assets');

        return $factory;
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
     * Controleur de configuration.
     * {@internal
     * - null $key Retourne l'instance du controleur de configuration.
     * - array $key Définition d'attributs de configuration.
     * - string $key Récupération de la valeur d'un attribut de configuration.
     * }
     *
     * @param null|array|string Clé d'indice (Syntaxe à point permise)|Liste des attributs de configuration à définir.
     * @param mixed $default Valeur de retour par défaut lors de la récupération d'un attribut.
     *
     * @return mixed|\tiFy\Kernel\Config\Config
     */
    function config($key = null, $default = null)
    {
        /** @var \tiFy\Kernel\Config\Config $factory */
        $factory = Kernel::Config();

        if (is_null($key)) :
            return $factory;
        elseif (is_array($key)) :
            return $factory->set($key);
        else :
            return $factory->get($key, $default);
        endif;
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
     * @return EventsManager
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
     * @return null|FieldManager|FieldController
     */
    function field($name = null, $id = null, $attrs = null)
    {
        /** @var FieldManager $manager */
        $manager = app('field');

        if (is_null($name)) :
            return $manager;
        endif;

        return $manager->get($name, $id, $attrs);
    }
endif;

if (!function_exists('form')) :
    /**
     * Formulaire - Controleur de champs.
     *
     * @param null|string $name Nom de qualification du formulaire.
     *
     * @return null|FormManager|FormFactory
     */
    function form($name = null)
    {
        /** @var FormManager $factory */
        $factory = app('form');

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
     * @return Logger
     */
    function logger()
    {
        return app('logger');
    }
endif;

if (!function_exists('page_hook')) :
    /**
     * Instance de controleur de page d'accroche
     * {@internal
     * - null $name Récupére l'instance du controleur.
     * - string $name Récupére l'instance du controleur de l'élément déclaré.
     * - array $name Déclaration des éléments
     * }
     *
     * @param null|string $name Nom de qualification de l'élément à récupérer.
     *
     * @return PageHook|PageHookItem
     */
    function page_hook($name = null)
    {
        /** @var PageHook $factory */
        $factory = app()->get('wp.page-hook');

        if (is_null($name)) :
            return $factory;
        elseif (is_array($name)) :
            return $factory->set($name);
        else :
            return $factory->get($name);
        endif;
    }
endif;

if (!function_exists('params')) :
    /**
     * Instance de contrôleur de paramètres.
     *
     * @param mixed $params Liste des paramètres.
     *
     * @return ParamsBag
     */
    function params($params = [])
    {
        /** @var ParamsBag $factory */
        $factory = app('params.bag', [$params]);

        return $factory;
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
     * @return null|PartialManager|PartialController
     */
    function partial($name = null, $id = null, $attrs = null)
    {
        /** @var PartialManager $manager */
        $manager = app('partial');

        if (is_null($name)) :
            return $manager;
        endif;

        return $manager->get($name, $id, $attrs);
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

if (!function_exists('pattern')) :
    /**
     * Instance de controleur de motifs d'affichage ou Instance d'un motif.
     *
     * @param null|string Nom de qualification du motif.
     * @param array $params Liste des paramètres appliqués au motif.
     *
     * @return null|ViewPattern|ViewPatternController
     */
    function pattern($name = null, array $params = [])
    {
        /** @var ViewPattern $manager */
        $manager = app()->get('view.pattern');

        if (is_null($name)) :
            return $manager;
        elseif ($pattern = $manager->get($name)) :
            return $pattern->param($params);
        else :
            return null;
        endif;
    }
endif;

if (!function_exists('post_type')) :
    /**
     * Récupération de l'intance du controleur des types de contenu ou instance d'un type de contenu déclaré.
     *
     * @param null|string $name Nom de qualification du type de contenu.
     *
     * @return PostTypeManager|PostTypeFactory
     */
    function post_type($name = null)
    {
        /** @var PostTypeManager $manager */
        $manager = app()->get('post-type');

        if (is_null($name)) :
            return $manager;
        endif;

        return $manager->get($name);
    }
endif;

if (!function_exists('redirect')) {
    /**
     * HTTP - Récupération d'une instance du contrôleur de redirection ou redirection vers une url.
     *
     * @param string|null $to Url de redirection.
     * @param int $status Code de la redirection.
     * @param array $headers Liste des entête HTTP
     * @param bool $secure Activation de la sécurisation
     *
     * @return HttpRedirect
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (is_null($to)) :
            return app('redirect');
        endif;

        return app('redirect', [$to, $status, $headers, $secure]);
    }
}

if (!function_exists('request')) :
    /**
     * HTTP - Controleur de traitement de la requête principale.
     *
     * @return Request
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
     * @return null|object
     */
    function resolve($name)
    {
        return container($name);
    }
}

if (! function_exists('route')) {
    /**
     * Routing - Récupération de l'url vers une route.
     *
     * @param string $name Nom de qualification de la route.
     * @param array $parameters Liste des variables passées en argument dans l'url.
     * @param boolean $absolute Activation de sortie de l'url absolue.
     *
     * @return string
     */
    function route($name, $parameters = [], $absolute = true)
    {
        return router()->url($name, $parameters, $absolute);
    }
}

if (!function_exists('route_exists')) :
    /**
     * Vérification si la requête courante répond à une route déclarée.
     *
     * @return bool
     */
    function route_exists()
    {
        return router()->hasCurrent();
    }
endif;

if (! function_exists('router')) {
    /**
     * Routing - Récupération de l'instance du controleur de routage ou déclaration d'une nouvelle route.
     *
     * @param string $name Nom de qualification de la route.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return Router|Route
     */
    function router($name = null, $attrs = [])
    {
        /** @var Router $factory */
        $factory = app()->get('router');

        if (is_null($name)) :
            return $factory;
        endif;

        return $factory->register($name, $attrs);
    }
}

if (! function_exists('taxonomy')) {
    /**
     * Récupération de l'instance du contrôleur de taxonomies ou instance d'une taxonomie déclarée.
     *
     * @param null|string $name Nom de qualification de la taxonomie déclarée.
     *
     * @return TaxonomyManager|TaxonomyFactory
     */
    function taxonomy($name = null)
    {
        /** @var TaxonomyManager $manager */
        $manager = app()->get('taxonomy');

        if (is_null($name)) :
            return $manager;
        endif;

        return $manager->get($name);
    }
}

if (! function_exists('url')) {
    /**
     * Récupération de l'instance du contrôleur d'url.
     *
     * @return Url
     */
    function url()
    {
        return app()->get('url');
    }
}

if (! function_exists('url_factory')) {
    /**
     * Récupération de l'instance du contrôleur de traitement d'url.
     *
     * @param string $url Url à traiter.
     *
     * @return UrlFactory
     */
    function url_factory($url)
    {
        return app()->get('url.factory', [$url]);
    }
}

if (! function_exists('validator')) {
    /**
     * Récupération d'un instance du contrôleur de validation.
     *
     * @return Validator
     */
    function validator()
    {
        return app('validator');
    }
}

if (!function_exists('view')) :
    /**
     * View - Récupération d'une instance du controleur des vues ou l'affichage d'un gabarit.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'intance du controleur principal.}
     * {@internal Sinon récupére le gabarit d'affichage et passe les variables en argument.}
     *
     * @param null|string view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewController|ViewEngine
     */
    function view($view = null, $data = [])
    {
        $factory = Kernel::ViewEngine();

        if (func_num_args() === 0) :
            return $factory;
        endif;

        return $factory->make($view, $data);
    }
endif;

if (!function_exists('wp_env')) :
    /**
     * Instance du controleur d'environnement Wordpress.
     *
     * @return WpManager
     */
    function wp_env()
    {
        /** @var WpManager $factory */
        $factory = app()->get('wp');

        return $factory;
    }
endif;