<?php

declare(strict_types=1);

use App\App;
use Illuminate\Database\Query\Builder as LaraDatabaseQueryBuilder;
use League\Uri\Contracts\UriInterface as LeagueUri;
use Pollen\Asset\AssetManagerInterface;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Http\RedirectResponseInterface;
use Pollen\Http\RequestInterface;
use Pollen\Log\LogManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Validation\ValidatorInterface;
use Psr\Http\Message\UriInterface;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Cron\CronJob;
use tiFy\Contracts\Cron\CronManager;
use tiFy\Contracts\Database\Database;
use tiFy\Contracts\Filesystem\Filesystem;
use tiFy\Contracts\Filesystem\StorageManager;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Form\FormManager;
use tiFy\Contracts\Kernel\ClassLoader;
use tiFy\Contracts\Kernel\Config;
use tiFy\Contracts\Kernel\Path;
use tiFy\Contracts\PostType\PostTypeFactory;
use tiFy\Contracts\PostType\PostType;
use tiFy\Contracts\Routing\Url;
use tiFy\Contracts\Support\ClassInfo;
use tiFy\Contracts\Taxonomy\TaxonomyFactory;
use tiFy\Contracts\Taxonomy\Taxonomy;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Contracts\Template\TemplateManager;
use tiFy\Contracts\User\User;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Field\FieldDriverInterface;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Partial\PartialDriverInterface;
use tiFy\Support\Env;
use tiFy\tiFy;

if (!function_exists('app')) {
    /**
     * App - Gestionnaire de l'application.
     * {@internal Si $abstract est null > Retourne l'instance de l'appication.}
     * {@internal Si $abstract est qualifié > Retourne la résolution du service qualifié.}
     *
     * @param string|null $abstract Nom de qualification du service.
     * @param array $args Liste des variables passé en arguments lors de la résolution du service.
     *
     * @return App|mixed
     */
    function app(?string $abstract = null, array $args = [])
    {
        /* @var App $factory */
        $factory = container('app');

        if (is_null($abstract)) {
            return $factory;
        }
        return $factory->get($abstract, $args);
    }
}

if (!function_exists('asset')) {
    /**
     * Instance du gestionnaire des assets.
     *
     * @return AssetManagerInterface
     */
    function asset(): AssetManagerInterface
    {
        return app(AssetManagerInterface::class);
    }
}

if (!function_exists('class_info')) {
    /**
     * ClassInfo - Gestionnaire de classes.
     *
     * @param string|object Nom complet ou instance de la classe.
     *
     * @return ClassInfo
     */
    function class_info($class): ClassInfo
    {
        return app('class-info', [$class]);
    }
}

if (!function_exists('class_loader')) {
    /**
     * ClassLoader - Gestionnaire de déclaration d'espaces de nom et d'inclusion de fichier automatique.
     *
     * @return ClassLoader
     */
    function class_loader(): ClassLoader
    {
        return container('class-loader');
    }
}

if (!function_exists('config')) {
    /**
     * Config - Gestionnaire de configuration de l'application.
     * {@internal
     * - null $key Retourne l'instance du controleur de configuration.
     * - array $key Définition d'attributs de configuration.
     * - string $key Récupération de la valeur d'un attribut de configuration.
     * }
     *
     * @param null|array|string Clé d'indice (Syntaxe à point permise)|Liste des attributs de configuration à définir.
     * @param mixed $default Valeur de retour par défaut lors de la récupération d'un attribut.
     *
     * @return Config|mixed
     */
    function config($key = null, $default = null)
    {
        /* @var Config $factory */
        $factory = container('config');

        if (is_null($key)) {
            return $factory;
        }
        if (is_array($key)) {
            return $factory->set($key);
        }
        return $factory->get($key, $default);
    }
}

if (!function_exists('container')) {
    /**
     * Container - Gestionnaire de fournisseur de services.
     * {@internal Si $alias est null > Retourne la classe de rappel du controleur.}
     *
     * @param string|null $abstract Nom de qualification du service à récupérer.
     *
     * @return Container|mixed
     */
    function container(?string $abstract = null)
    {
        /** @var tiFy $factory */
        $factory = tiFy::instance();

        if (is_null($abstract)) {
            return $factory;
        }
        return $factory->get($abstract);
    }
}

if (!function_exists('cron')) {
    /**
     * Cron - Gestionnaire de tâches planifiées.
     *
     * @param string|null $name Nom de qualification de la tâche déclarée.
     *
     * @return CronManager|CronJob|null
     */
    function cron(?string $name = null)
    {
        /* @var CronManager $manager */
        $manager = app('cron');

        if (is_null($name)) {
            return $manager;
        }
        return $manager->get($name);
    }
}

if (!function_exists('database')) {
    /**
     * Database - Gestionnaire de base de données.
     *
     * @param string|null $table Nom de qualification de la table de base de données spécifique.
     *
     * @return Database|LaraDatabaseQueryBuilder|null
     */
    function database(?string $table = null)
    {
        /* @var Database $manager */
        $manager = app('database');

        if (is_null($table)) {
            return $manager;
        }
        return $manager::table($table);
    }
}

if (!function_exists('env')) {
    /**
     * Events - Gestionnaire de variables d'environnement.
     *
     * @param string $key Clé d'indice d'une variable
     * @param mixed $default Valeur de retour par défaut
     *
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}


if (!function_exists('events')) {
    /**
     * Répartiteur d'événements.
     *
     * @return EventDispatcherInterface
     */
    function events()
    {
        return app(EventDispatcherInterface::class);
    }
}

if (!function_exists('field')) {
    /**
     * Field - Gestionnaire de champs.
     *
     * @param string|null $alias Alias de qualification.
     * @param mixed $idOrParams Identifiant de qualification|Liste des attributs de configuration.
     * @param array $params Liste des attributs de configuration.
     *
     * @return FieldContract|FieldDriverInterface|null
     */
    function field(?string $alias = null, $idOrParams = null, array $params = [])
    {
        /* @var FieldContract $manager */
        $manager = app(FieldContract::class);

        if (is_null($alias)) {
            return $manager;
        }
        return $manager->get($alias, $idOrParams, $params);
    }
}

if (!function_exists('form')) {
    /**
     * Formulaire - Gestionnaire de formulaire.
     *
     * @param null|string $name Nom de qualification du formulaire.
     *
     * @return null|FormManager|FormFactory
     */
    function form(?string $name = null)
    {
        /* @var FormManager $factory */
        $factory = app(FormManager::class);

        if (is_null($name)) {
            return $factory;
        }
        return $factory->get($name);
    }
}

if (!function_exists('logger')) {
    /**
     * Gestionnaire de journalisation|Déclaration d'un message de journalisation.
     *
     * @param string|null $message
     * @param array $context
     *
     * @return LogManagerInterface|void
     */
    function logger(?string $message = null, array $context = []): ?LogManagerInterface
    {
        /* @var LogManagerInterface $manager */
        $manager = app(LogManagerInterface::class);

        if ($message === null) {
            return $manager;
        }
        $manager->debug($message, $context);
    }
}

if (!function_exists('partial')) {
    /**
     * Partial - Gestionnaire de portions d'affichage.
     *
     * @param string|null $alias Alias de qualification.
     * @param mixed $idOrParams Identifiant de qualification|Liste des attributs de configuration.
     * @param array $params Liste des attributs de configuration.
     *
     * @return PartialContract|PartialDriverInterface|null
     */
    function partial(?string $alias = null, $idOrParams = null, array $params = [])
    {
        /* @var PartialContract $manager */
        $manager = app(PartialContract::class);

        if (is_null($alias)) {
            return $manager;
        }
        return $manager->get($alias, $idOrParams, $params);
    }
}

if (!function_exists('paths')) {
    /**
     * Path - Gestionnaire des chemins vers les répertoires de l'application.
     *
     * @return Path
     */
    function paths(): Path
    {
        return container('path');
    }
}

if (!function_exists('post_type')) {
    /**
     * PostType - Gestionnaire des types de contenu ou instance d'un type de contenu déclaré.
     *
     * @param string|null $name Nom de qualification du type de contenu.
     *
     * @return PostType|PostTypeFactory
     */
    function post_type(?string $name = null)
    {
        /* @var PostType $manager */
        $manager = app('post-type');

        if (is_null($name)) {
            return $manager;
        }
        return $manager->get($name);
    }
}

if (!function_exists('redirect')) {
    /**
     * HTTP - Gestionnaire de redirection HTTP.
     *
     * @param string|null $to Url de redirection.
     * @param int $status Code de la redirection.
     * @param array $headers Liste des entête HTTP
     * @param bool $secure Activation de la sécurisation
     *
     * @return RedirectResponseInterface
     */
    function redirect(?string $to = null, int $status = 302, array $headers = [], ?bool $secure = null)
    {
        if (is_null($to)) {
            return app('redirect');
        }
        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

if (!function_exists('request')) {
    /**
     * HTTP - Gestionnaire de traitement de la requête HTTP principale.
     *
     * @return RequestInterface
     */
    function request(): RequestInterface
    {
        return app(RequestInterface::class);
    }
}

if (!function_exists('route')) {
    /**
     * Routing - Récupération de l'url vers une route.
     *
     * @param string $name Nom de qualification de la route.
     * @param array $parameters Liste des variables passées en argument dans l'url.
     * @param boolean $absolute Activation de sortie de l'url absolue.
     *
     * @return string|null
     */
    function route(string $name, array $parameters = [], bool $absolute = true): ?string
    {
        /* @var RouterInterface $router */
        $router = app(RouterInterface::class);

        return $router->getNamedRouteUrl($name, $parameters, $absolute);
    }
}

if (!function_exists('storage')) {
    /**
     * Storage - Gestionnaire de point de montage.
     *
     * @param string|null Nom de qualification du point de montage à récupéré.
     *
     * @return StorageManager|Filesystem
     */
    function storage(?string $name = null)
    {
        /* @var StorageManager $manager */
        $manager = app('storage');

        if (is_null($name)) {
            return $manager;
        }
        return $manager->disk($name);
    }
}

if (!function_exists('taxonomy')) {
    /**
     * Taxonomy - Gestionnaire de taxonomies.
     *
     * @param string|null $name Nom de qualification de la taxonomie déclarée.
     *
     * @return Taxonomy|TaxonomyFactory
     */
    function taxonomy(?string $name = null)
    {
        /* @var Taxonomy $manager */
        $manager = app('taxonomy');

        if (is_null($name)) {
            return $manager;
        }
        return $manager->get($name);
    }
}

if (!function_exists('template')) {
    /**
     * Instance de controleur de gabarits d'affichage ou Instance d'un gabarit.
     *
     * @param string|null Nom de qualification du gabarit.
     * @param array $params Liste des paramètres appliqués au gabarit.
     *
     * @return TemplateFactory|TemplateManager|null
     */
    function template(?string $name = null, array $params = [])
    {
        /* @var TemplateManager $manager */
        $manager = app('template');

        if (is_null($name)) {
            return $manager;
        }

        if ($template = $manager->get($name)) {
            $template->param($params);
            return $template;
        }
        return null;
    }
}

if (!function_exists('url')) {
    /**
     * Récupération de l'instance du contrôleur d'url.
     *
     * @param UriInterface|LeagueUri|string|null $uri
     *
     * @return Url
     */
    function url($uri = null): Url
    {
        /** @var Url $url */
        $url = app('url');

        return is_null($uri) ? $url : $url->set($uri);
    }
}

if (!function_exists('user')) {
    /**
     * User - Gestionnaire d'utilisateurs.
     *
     * @return User
     */
    function user(): User
    {
        return app('user');
    }
}

if (!function_exists('validator')) {
    /**
     * Instance du gestionnaire de validation.
     *
     * @return ValidatorInterface
     */
    function validator(): ValidatorInterface
    {
        return app(ValidatorInterface::class);
    }
}

if (!function_exists('view')) {
    /**
     * View - Récupération d'une instance du controleur des vues ou l'affichage d'un gabarit.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'intance du controleur principal.}
     * {@internal Sinon récupére le gabarit d'affichage et passe les variables en argument.}
     *
     * @param null|string view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewEngine|string
     */
    function view($view = null, array $data = [])
    {
        /* @var ViewEngine $factory */
        $factory = app('view');

        if (func_num_args() === 0) {
            return $factory;
        }
        return $factory->render($view, $data);
    }
}
