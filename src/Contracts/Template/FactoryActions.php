<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Exception;
use Pollen\Routing\BaseController;

interface FactoryActions extends FactoryAwareTrait
{
    /**
     * Instance du controleur de requête HTTP.
     *
     * @return BaseController
     */
    public function controller(): BaseController;

    /**
     * Exécution de l'action.
     *
     * @param string $name Nom de qualification de l'action.
     * @param array ...$parameters Liste des paramètres dynamiques passés à l'action.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function do(string $name, ...$parameters);

    /**
     * Récupération de la clé d'indice de déclénchment d'une action.
     *
     * @return string
     */
    public function getIndex(): string;

    /**
     * Définition du controleur de requête HTTP associé.
     *
     * @param BaseController $controller
     *
     * @return static
     */
    public function setController(BaseController $controller): FactoryActions;
}