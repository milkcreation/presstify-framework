<?php

namespace tiFy\View;

use tiFy\App\Container\AppServiceProvider;
use tiFy\View\Pattern\PatternFactory;

class ViewServiceProvider extends AppServiceProvider
{
	/**
	 * Liste des noms de qualification des services fournis.
	 * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
	 * @var string[]
	 */
	protected $provides = [
	    'view.pattern',
        'view.pattern.factory',
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
        $this->app->add('view.pattern.factory', function($name, $attrs = []) {
            return new PatternFactory($name, $attrs);
        });
    }
}