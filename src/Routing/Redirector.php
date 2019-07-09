<?php declare(strict_types=1);

namespace tiFy\Routing;

use Psr\Http\Message\ResponseInterface;
use tiFy\Http\RedirectResponse;
use tiFy\Contracts\Routing\Redirector as RedirectorContract;
use tiFy\Contracts\Routing\Router;

class Redirector implements RedirectorContract
{
    /**
     * Instance du gestion de routage
     * @var Router
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param Router $manager Instance du gestion de routage
     *
     * @return void
     */
    public function __construct(Router $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Création d'une instance de reponse de redirection PSR.
     * @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
     *
     * @param string|null $url Url de redirection.
     * @param int $status Code du statut de redirection.
     * @param array $headers Liste des entêtes complémentaires.
     *
     * @return ResponseInterface|null
     */
    public function to(string $path, int $status = 302, array $headers = []): ?ResponseInterface
    {
        return RedirectResponse::createPsr($path, $status, $headers);
    }

    /**
     * Création d'une instance de reponse de redirection PSR basé sur le nom de qualification d'une route nommée.
     *
     * @param string $name Nom de qualification de la route.
     * @param array $parameters Liste des paramètres passés en argument dans l'url de la route.
     * @param int $status Code du statut de redirection.
     * @param array $headers Liste des entêtes complémentaires.
     *
     * @return ResponseInterface|null
     */
    public function route(
        string $name,
        array $parameters = [],
        int $status = 302,
        array $headers = []
    ): ?ResponseInterface {
        return $this->to($this->manager->url($name, $parameters), $status, $headers);
    }
}