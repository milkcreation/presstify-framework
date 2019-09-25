<?php declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\View\ViewController;

class MetaboxViewer extends ViewController
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [

    ];

    /**
     * Délégation d'appel des méthodes de l'application associée.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->mixins)) {
            return call_user_func_array(
                [$this->engine->get('metabox'), $name],
                $arguments
            );
        }
    }
}