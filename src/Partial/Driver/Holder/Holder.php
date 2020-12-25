<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\Holder;

use tiFy\Contracts\Partial\Holder as HolderContract;
use tiFy\Partial\PartialDriver;

class Holder extends PartialDriver implements HolderContract
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string $content Contenu de remplacement.
             */
            'content'          => '',
            /**
             * @var int $width Rapport de largeur relatif à la hauteur.
             */
            'width'            => 100,
            /**
             * @var int $height Rapport de hauteur relatif à la largeur.
             */
            'height'           => 100,
        ]);
    }
}