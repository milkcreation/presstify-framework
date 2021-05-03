<?php

declare(strict_types=1);

namespace tiFy\Contracts\Form;

use Pollen\Session\AttributeKeyBagInterface;
use Pollen\Support\Proxy\EncrypterProxyInterface;
use Pollen\Support\Proxy\SessionProxyInterface;

/**
 * @mixin \tiFy\Form\Concerns\FormAwareTrait
 */
interface SessionFactory extends EncrypterProxyInterface, AttributeKeyBagInterface, SessionProxyInterface
{
    /**
     * Chargement.
     *
     * @return SessionFactory
     */
    public function boot(): SessionFactory;

    /**
     * Récupération du jeton de qualification.
     *
     * @return string
     */
    public function getToken(): string;
}