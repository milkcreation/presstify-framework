<?php declare(strict_types=1);

namespace tiFy\Contracts\Field;

interface Repeater extends FieldFactory
{
    /**
     * Récupération de l'url de traitement Xhr.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Définition de l'url de traitement Xhr.
     *
     * @param string|null $url
     *
     * @return $this
     */
    public function setUrl(?string $url = null): Repeater;
}