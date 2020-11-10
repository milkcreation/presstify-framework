<?php declare(strict_types=1);

namespace tiFy\Routing;

use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Http\Response as ResponseContract;
use tiFy\Contracts\Http\RedirectResponse;
use tiFy\Contracts\Routing\Redirector;
use tiFy\Contracts\View\Engine;
use tiFy\Http\Response;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\View;
use tiFy\Support\Proxy\Request;
use tiFy\Support\Proxy\Redirect;

class BaseController extends ParamsBag
{
    /**
     * Instance de conteneur d'injection de dépendances.
     * @var Container|null
     */
    protected $container;

    /**
     * Indicateur d'activation du mode de déboguage.
     * @var bool|null
     */
    protected $debug;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container|null $container Instance de conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container;

        $this->boot();
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot(): void { }

    /**
     * Vérification d'activation du mode de deboguage.
     *
     * @return bool
     */
    protected function debug(): bool
    {
        return is_null($this->debug) ? env('APP_DEBUG', false) : $this->debug;
    }

    /**
     * Récupération de l'instance du conteneur d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * Vérification d'existance d'un gabarit d'affichage.
     *
     * @param string $view Nom de qualification du gabarit.
     *
     * @return bool
     */
    public function hasView(string $view): bool
    {
        return $this->viewEngine()->exists($view);
    }

    /**
     * Récupération de l'instance du gestionnaire de redirection|Redirection vers un chemin.
     *
     * @param string|null $path url absolue|relative de redirection.
     * @param int $status Statut de redirection.
     * @param array $headers Liste des entêtes complémentaires associées à la redirection.
     *
     * @return RedirectResponse|Redirector
     */
    public function redirect(?string $path = null, int $status = 302, array $headers = [])
    {
        if (is_null($path)) {
            return Redirect::getInstance();
        } else {
            return Redirect::to($path, $status, $headers);
        }
    }

    /**
     * Redirection vers la page d'origine.
     *
     * @param int $status Statut de redirection.
     * @param array $headers Liste des entêtes complémentaires associées à la redirection.
     *
     * @return RedirectResponse
     */
    public function referer(int $status = 302, array $headers = []): RedirectResponse
    {
         return $this->redirect(Request::header('referer'), $status, $headers);
    }

    /**
     * Récupération de l'affichage d'un gabarit.
     *
     * @param string $view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return string
     */
    public function render(string $view, array $data = []): string
    {
        return $this->viewEngine()->render($view, $data);
    }

    /**
     * Récupération de la reponse HTTP.
     *
     * @param string $content.
     * @param int $status
     * @param array $headers
     *
     * @return ResponseContract
     */
    public function response($content = '', int $status = 200, array $headers = []): ?ResponseContract
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Redirection vers une route déclarée.
     *
     * @param string $name Nom de qualification de la route.
     * @param array $params Liste des paramètres de définition de l'url de la route.
     * @param int $status Statut de redirection.
     * @param array $headers Liste des entêtes complémentaires associées à la redirection.
     *
     * @return RedirectResponse
     */
    public function route(string $name, array $params= [], int $status = 302, array $headers = []): RedirectResponse
    {
        return Redirect::route($name, $params, $status, $headers);
    }

    /**
     * Définition de l'activation du mode de deboguage.
     *
     * @param bool $debug
     *
     * @return static
     */
    protected function setDebug(bool $debug = true): self
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Définition des variables partagées à l'ensemble des vues.
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return $this
     */
    public function share($key, $value = null): self
    {
        $keys = !is_array($key) ? [$key => $value] : $key;

        foreach ($keys as $k => $v) {
            $this->viewEngine()->share($k, $v);
        }

        return $this;
    }

    /**
     * Génération de la reponse HTTP associé à l'affichage d'un gabarit.
     *
     * @param string $view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ResponseContract
     */
    public function view(string $view, array $data = []): ?ResponseContract
    {
        return $this->response($this->render($view, $data));
    }

    /**
     * Moteur d'affichage des gabarits d'affichage.
     *
     * @return Engine
     */
    public function viewEngine(): Engine
    {
        return View::getInstance()->getEngine();
    }
}