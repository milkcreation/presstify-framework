<?php

namespace tiFy\View;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\View\ViewPatternController as ViewPatternControllerContract;

class ViewServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'view.pattern',
        'view.pattern.controller',
        'view.pattern.controller.config'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'after_setup_tify',
            function () {
                app()->get('view.pattern');
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerPattern();
    }

    /**
     * Déclaration des contrôleurs de gestion des motifs d'affichage.
     *
     * @return void
     */
    public function registerPattern()
    {
        $this->app->share('view.pattern', ViewPattern::class);

        $this->app->add('view.pattern.controller', function ($name, $attrs) {
            if ($attrs instanceof ViewPatternControllerContract) :
                return call_user_func_array($attrs, [$name]);
            elseif (is_string($attrs) && class_exists($attrs)) :
                return (($resolved = new $attrs()) instanceof ViewPatternControllerContract)
                    ? call_user_func_array($resolved, [$name])
                    : $resolved;
            else :
                return call_user_func_array(new ViewPatternController($attrs), [$name]);
            endif;
        });
    }
}