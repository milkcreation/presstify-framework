<?php

declare(strict_types=1);

namespace tiFy\Form\Factory;

use LogicException;
use Pollen\Encryption\EncrypterProxy;
use Pollen\Session\AttributeKeyBag;
use Pollen\Support\Proxy\SessionProxy;
use tiFy\Contracts\Form\SessionFactory as SessionFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;

class SessionFactory extends AttributeKeyBag implements SessionFactoryContract
{
    use EncrypterProxy;
    use FormAwareTrait;
    use SessionProxy;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Jeton d'identification.
     * @var string|null
     */
    protected $token;

    /**
     * @inheritDoc
     */
    public function boot(): SessionFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('session.boot', [&$this]);

            $this->session()->addAttributeKeyBag($this->getKey(), $this);

            $this->booted = true;

            $this->form()->event('session.booted', [&$this]);
        }

        return $this;
    }

    /**
     * Génération du jeton de qualification.
     *
     * @return void
     */
    protected function generateToken(): void
    {
        $this->token = $this->encrypt(json_encode(['name' => $this->form()->getAlias(), 'id' => uniqid('', true)]));
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        if (is_null($this->token)) {
            $this->generateToken();
        }
        return $this->token;
    }
}