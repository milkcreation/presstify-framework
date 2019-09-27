<?php declare(strict_types=1);

namespace tiFy\Encryption;

use tiFy\Container\ServiceProvider;

class EncryptionServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'encrypter'
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->add('encrypter', function () {
            return new Encrypter();
        });
    }
}