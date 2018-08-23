<?php

namespace tiFy\App\Templates;

use Illuminate\Support\Arr;
use tiFy\App\AppInterface;
use tiFy\App\Templates\AppTemplateController;
use tiFy\Kernel\Templates\Engine;

class AppEngine extends Engine
{
    /**
     * Classe de rappel du controleur d'application associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * Liste des attributs de configuration.
     * @var array {
     *      @var string $directory Chemin absolu vers le répertoire par défaut des templates.
     *      @var string $ext Extension des fichiers de template.
     *      @var string $controller Controleur de template.
     *      @var string $args Liste des variables passées en argument au controleur de template.
     * }
     */
    protected $attributes = [
        'directory'     => null,
        'ext'           => 'php',
        'controller'    => AppTemplateController::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration.
     * @param AppInterface $app Classe de rappel du controleur d'application associée.
     *
     * @return void
     */
    public function __construct($attrs = [], AppInterface $app)
    {
        $this->app = $app;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     *
     * @return AppTemplateInterface
     */
    public function make($name, $args = [])
    {
        $controller = $this->getController();

        return new $controller($this, $name, $this->app);
    }

    /**
     * Affichage d'une vue basé sur un template.
     *
     * @param string $name Nom de qualification du template.
     * @param array $args Liste des variables passées en arguments.
     *
     * @return AppTemplateInterface
     */
    public function view($name, $args = [])
    {
        $template = $this->make($name, $args);

        $this->app->appAddAction(
            'template_redirect',
            function () use ($template) {
                echo $template->render();
                exit;
            },
            0
        );

        return $template;
    }
}