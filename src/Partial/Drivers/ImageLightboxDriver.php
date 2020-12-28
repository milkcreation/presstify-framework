<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\Drivers\ImageLightbox\ImageLightboxItem;
use tiFy\Partial\Drivers\ImageLightbox\ImageLightboxItemInterface;
use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;
use tiFy\Validation\Validator as v;

/**
 * @see https://github.com/marekdedic/imagelightbox
 * @see http://marekdedic.github.io/imagelightbox/
 */
class ImageLightboxDriver extends PartialDriver implements ImageLightboxDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var string|null $group Groupe d'affectation de la liste des éléments.
             */
            'group'   => null,
            /**
             * @var string|array|ImageLightboxItemInterface[] $items Liste des éléments.
             */
            'items'   => [
                'https://picsum.photos/id/768/800/800',
                'https://picsum.photos/id/669/800/800',
                'https://picsum.photos/id/646/800/800',
                'https://picsum.photos/id/883/800/800',
            ],
            /**
             * @var array $options Liste des options {
             * @see https://github.com/marekdedic/imagelightbox
             *
             * @var string $selector Par défaut 'a[data-imagelightbox]'.
             * @var string $id Par défaut 'imagelightbox'.
             * @var string $allowedTypes Type de fichier permis. ex. 'png|jpg|jpeg|gif' Par défaut tous.
             * @var int $animationSpeed Vitesse d'animation. Par défaut 250.
             * @var bool $activity Affichage de l'indicateur d'activité. Par défaut false.
             * @var bool $arrows Affichage des flèches de navigation suivant/précédent. Par défaut false.
             * @var bool $button Affichage du bouton de fermeture. Par défaut false.
             * @var bool $caption Affichage des légendes. Par défaut false.
             * @var bool $enableKeyboard Activation des raccourcis clavier (flèches d/g + echap). Par défaut true.
             * @var bool $history ??? enable image permalinks and history Par défaut false.
             * @var bool $fullscreen Activation du mode plein écran. Par défaut false.
             * @var int $gutter Window height less height of image as a percentage. Par défaut 10.
             * @var int $offsetY Vertical offset in terms of gutter. Par défaut 0.
             * @var bool $navigation Affichage de la navigation. Par défaut false.
             * @var bool $overlay Affichage du fond de recouvrement. Par défaut false.
             * @var bool $preloadNext Préchargement des images en tâche de fond. Par défaut true.
             * @var bool $quitOnEnd Fermeture à l'issue de l'affichage de la dernière image. Par défaut false.
             * @var bool $quitOnImgClick Fermeture au clique sur l'image. Par défaut false.
             * @var bool $quitOnDocClick Fermeture au clique en dehors de l'image Par défaut true.
             * @var bool $quitOnEscKey Fermeture avec la touche echap. Par défaut true.
             * }
             */
            'options' => [],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function fetchItem($item): ?ImageLightboxItemInterface
    {
        if ($item instanceof ImageLightboxItemInterface) {
            return $item;
        } elseif (is_array($item)) {
            if (isset($item['src']) && ($instance = $this->fetchItem($item['src']))) {
                unset($item['src']);
                return $instance->set($item);
            } elseif (isset($item['content'])) {
                return (new ImageLightboxItem())->set($item);
            } else {
                return null;
            }
        } elseif (is_string($item) && v::url()->validate($item)) {
            return (new ImageLightboxItem())->set([
                'src' => $item,
            ]);
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        $this->parseItems();

        $this->set('attrs.data-control', 'image-lightbox');
        $this->set('attrs.data-options', $this->pull('options', []));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseItems(): ImageLightboxDriverInterface
    {
        $items = [];

        foreach ((array)$this->get('items', []) as $item) {
            if ($item = $this->fetchItem($item)) {
                if (!$item->get('group')) {
                    $item->set('group', $this->get('group') ?: $this->getId());
                }

                $items[] = $item->parse();
            }
        }

        return $this->set('items', $items);
    }
}