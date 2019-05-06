<?php declare(strict_types=1);

namespace tiFy\Template;

use tiFy\Container\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
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
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('template', function () {
            return new TemplateManager();
        });

        $this->getContainer()->add('template.factory', function ($attrs) {
            return new TemplateFactory($attrs);
        });
    }
}