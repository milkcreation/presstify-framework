<?php

declare(strict_types=1);

use Illuminate\Database\Query\Builder as LaraDatabaseQueryBuilder;
use League\Uri\Contracts\UriInterface as LeagueUri;
use Pollen\Asset\AssetManagerInterface;
use Pollen\Config\ConfigInterface;
use Pollen\Database\DatabaseManagerInterface;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Field\FieldDriverInterface;
use Pollen\Field\FieldManagerInterface;
use Pollen\Filesystem\FilesystemInterface;
use Pollen\Filesystem\StorageManagerInterface;
use Pollen\Kernel\ApplicationInterface;
use Pollen\Kernel\Kernel;
use Pollen\Partial\PartialDriverInterface;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Http\RedirectResponseInterface;
use Pollen\Http\RequestInterface;
use Pollen\Form\FormManagerInterface;
use Pollen\Form\FormInterface;
use Pollen\Log\LogManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Support\ClassInfo;
use Pollen\Support\ClassLoader;
use Pollen\Support\Env;
use Pollen\Validation\ValidatorInterface;
use Psr\Http\Message\UriInterface;
use tiFy\Contracts\Cron\CronJob;
use tiFy\Contracts\Cron\CronManager;
use tiFy\Contracts\Routing\Url;
use tiFy\Contracts\View\Engine as ViewEngine;

if (!function_exists('app')) {
    /**
     * Instance de l'application
     *
     * @param string|null $abstract Nom de qualification du service.
     *
     * @return ApplicationInterface|mixed
     */
    function app(?string $abstract = null)
    {
        $app = Kernel::getInstance()->getApp();

        if ($abstract === null) {
            return $app;
        }

        return $app[$abstract] ?? null;
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
        return new ClassInfo($class);
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
        return app('class-loader');
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
     * @return ConfigInterface|mixed
     */
    function config($key = null, $default = null)
    {
        /* @var ConfigInterface $config */
        $config = app(ConfigInterface::class);

        if (is_null($key)) {
            return $config;
        }
        if (is_array($key)) {
            $config->set($key);

            return $config;
        }
        return $config->get($key, $default);
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
     * Instance du gestionnaire de base de données|Constructeur de requêtes d'une table de la base de données.
     *
     * @param string|null $table
     *
     * @return DatabaseManagerInterface|LaraDatabaseQueryBuilder|null
     */
    function database(?string $table = null)
    {
        /* @var DatabaseManagerInterface $manager */
        $manager = app(DatabaseManagerInterface::class);

        if ($table === null) {
            return $manager;
        }
        return $manager::table($table);
    }
}

if (!function_exists('env')) {
    /**
     * Récupération d'une variables d'environnement.
     *
     * @param string $key
     * @param mixed $default
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
     * Instance du répartiteur d'événements.
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
     * Instance du gestionnaire de champs|Instance d'un champ déclaré.
     *
     * @param string|null $alias
     * @param mixed $idOrParams
     * @param array $params
     *
     * @return FieldManagerInterface|FieldDriverInterface|null
     */
    function field(?string $alias = null, $idOrParams = null, array $params = [])
    {
        /* @var FieldManagerInterface $manager */
        $manager = app(FieldManagerInterface::class);

        if (is_null($alias)) {
            return $manager;
        }
        return $manager->get($alias, $idOrParams, $params);
    }
}

if (!function_exists('form')) {
    /**
     * Instance du gestionnaire de formulaires|Instance d'un formulaire.
     *
     * @param string|null $name
     *
     * @return FormManagerInterface|FormInterface|null
     */
    function form(?string $name = null)
    {
        /* @var FormManagerInterface $manager */
        $manager = app(FormManagerInterface::class);

        if ($name === null) {
            return $manager;
        }
        return $manager->get($name);
    }
}

if (!function_exists('logger')) {
    /**
     * Instance du gestionnaire de journalisation|Déclaration d'un message de journalisation.
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
     * Instance du gestionnaire de portions d'affichage|Instance d'une portion d'affichage déclarée.
     *
     * @param string|null $alias
     * @param mixed $idOrParams
     * @param array $params
     *
     * @return PartialManagerInterface|PartialDriverInterface|null
     */
    function partial(?string $alias = null, $idOrParams = null, array $params = [])
    {
        /* @var PartialManagerInterface $manager */
        $manager = app(PartialManagerInterface::class);

        if (is_null($alias)) {
            return $manager;
        }
        return $manager->get($alias, $idOrParams, $params);
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
     * Instance de la requête HTTP principale.
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
     * Récupération de l'url d'une route déclarée.
     *
     * @param string $name
     * @param array $parameters
     * @param boolean $absolute
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
     * Gestionnaire de système de fichier|Instance d'un point de montage.
     *
     * @param string|null $name
     *
     * @return StorageManagerInterface|FilesystemInterface
     */
    function storage(?string $name = null)
    {
        /* @var StorageManagerInterface $manager */
        $manager = app(StorageManagerInterface::class);

        if ($name === null) {
            return $manager;
        }
        return $manager->disk($name);
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
