<?php

namespace tiFy\Template;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Template\TemplateFactory as TemplateFactoryContract;

class TemplateServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'template',
        'template.factory'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerTemplate();
    }

    /**
     * Déclaration des contrôleurs de gestion des motifs d'affichage.
     *
     * @return void
     */
    public function registerTemplate()
    {
        $this->app->share('template', TemplateManager::class);

        $this->app->add('template.factory', function ($name, $attrs) {
            if ($attrs instanceof TemplateFactoryContract) :
                return call_user_func_array($attrs, [$name]);
            elseif (is_string($attrs) && class_exists($attrs)) :
                return (($resolved = new $attrs()) instanceof TemplateFactoryContract)
                    ? call_user_func_array($resolved, [$name])
                    : $resolved;
            else :
                return call_user_func_array(new TemplateFactory($attrs), [$name]);
            endif;
        });
    }
}