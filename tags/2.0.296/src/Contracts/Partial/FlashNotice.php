<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

interface FlashNotice extends PartialDriver
{
    /**
     * Ajout d'une notification à afficher.
     *
     * @param string $message Intitulé du message de notification.
     * @param string $type Type de message. error|info|success|warning.
     * @param array $attrs Liste des attributs de personnalisation du message de notification.
     *
     * @return static
     */
    public function add(string $message, string $type = 'error', array $attrs = []): FlashNotice;
}