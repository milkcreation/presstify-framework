<?php

namespace tiFy\Components\AdminView\ListTable\Item;

use ArrayIterator;
use Illuminate\Support\Collection;
use tiFy\AdminView\AdminViewControllerInterface;
use tiFy\Apps\Attributes\AbstractAttributesIterator;

class ItemController extends AbstractAttributesIterator implements ItemInterface
{
    /**
     * Classe de rappel de la vue associée.
     * @var AdminViewControllerInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param AdminViewControllerInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($attrs = [], AdminViewControllerInterface $app)
    {
        parent::__construct((array)$attrs, $app);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimary()
    {
         if (($db = $this->app->getDb()) && ($primary = $db->getPrimary()) && $this->has($primary)) :
            return $this->get($primary);
         else :
            return Arr::first($this->attributes);
         endif;
    }
}