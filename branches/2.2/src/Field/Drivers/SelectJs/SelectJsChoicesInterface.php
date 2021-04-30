<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\SelectJs;

use tiFy\Field\Drivers\Select\SelectChoiceInterface;
use tiFy\Field\Drivers\Select\SelectChoicesInterface;
use tiFy\Field\Drivers\SelectDriverInterface;

interface SelectJsChoicesInterface extends SelectChoicesInterface
{
    /**
     * Traitement de la requête de traitement des éléments.
     *
     * @param array $args
     *
     * @return void
     */
    public function query(array $args): void;

    /**
     * Définition du controleur de champ associé.
     *
     * @param SelectDriverInterface $field
     *
     * @return static
     */
    public function setField(SelectDriverInterface $field): SelectJsChoicesInterface;

    /**
     * Définition du controleur d'élement.
     *
     * @param SelectChoiceInterface $item
     *
     * @return static
     */
    public function setItem(SelectChoiceInterface $item): SelectJsChoicesInterface;

    /**
     * @inheritdoc
     */
    public function walker($items = [], $depth = 0, $parent = null): string;

    /**
     * @inheritdoc
     */
    public function walk($item, $name = null): SelectChoiceInterface;
}