<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\Slider;

use tiFy\Contracts\Partial\{PartialDriver as PartialDriverContract, Slider as SliderContract};
use tiFy\Partial\PartialDriver;
use tiFy\Validation\Validator as v;

class Slider extends PartialDriver implements SliderContract
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string[]|callable[] $items Liste des éléments. Liste de sources d'image|Liste de contenu HTML|Liste
             * de fonctions. défaut : @see https://picsum.photos/images
             */
            'items'   => [
                'https://picsum.photos/800/800/?image=768',
                'https://picsum.photos/800/800/?image=669',
                'https://picsum.photos/800/800/?image=646',
                'https://picsum.photos/800/800/?image=883',
            ],
            /**
             * @var array $options Liste des attributs de configuration du pilote d'affichage.
             * @see http://kenwheeler.github.io/slick/#settings
             */
            'options' => [],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverContract
    {
        parent::parseParams();

        $items = $this->get('items', []);
        foreach ($items as &$item) {
            if (is_callable($item)) {
                $item = call_user_func($item);
            } elseif (is_array($item)) {
            } elseif (v::url()->validate($item)) {
                $item = "<img src=\"{$item}\" alt=\"\"/>";
            }
        }
        $this->set([
            'items'              => $items,
            'attrs.data-control' => 'slider',
            'attrs.data-slick'   => htmlentities(json_encode($this->get('options', []))),
        ]);

        return $this;
    }
}