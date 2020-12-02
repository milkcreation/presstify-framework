<?php declare(strict_types=1);

namespace tiFy\Asset;

use tiFy\Container\ServiceProvider;
use tiFy\Support\Locale;
use tiFy\Support\Proxy\Url;

class AssetServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = ['asset'];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('asset', function () {
            $instance = new Asset($this->getContainer());

            $instance->setDataJs('base_url', Url::root()->render(), false);
            $instance->setDataJs('scope', Url::scope(), false);
            $instance->setDataJs(
                'locale',
                ($locale = Locale::getLanguage()) ? $locale : ['language' => '', 'iso' => []]
            );

            return $instance;
        });
    }
}