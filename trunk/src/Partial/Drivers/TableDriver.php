<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;

class TableDriver extends PartialDriver implements TableDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var bool $header Activation de l'entête de table.
             */
            'header'  => true,
            /**
             * @var bool $footer Activation du pied de table.
             */
            'footer'  => true,
            /**
             * @var string[] $columns Intitulé des colonnes.
             */
            'columns' => [
                'Lorem',
                'Ipsum',
            ],
            /**
             * @var array[] $datas Données de la table.
             */
            'datas'   => [
                [
                    'lorem dolor',
                    'ipsum dolor',
                ],
                [
                    'lorem amet',
                    'ipsum amet',
                ],
            ],
            /**
             * @var string $none Intitulé de la table lorsque la table ne contient aucune donnée.
             */
            'none'    => __('Aucun élément à afficher dans le tableau', 'tify'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        $this->set('count', count($this->get('columns', [])));

        return $this;
    }
}