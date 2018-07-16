<?php

namespace tiFy\Components\Layout\ListTable\Item;

use Illuminate\Support\Collection;
use tiFy\Apps\Item\AbstractAppItemIterator;
use tiFy\Apps\Layout\LayoutInterface;

class ItemController extends AbstractAppItemIterator implements ItemInterface
{
    /**
     * Classe de rappel de la vue associée.
     * @var LayoutInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param LayoutInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($attrs = [], LayoutInterface $app)
    {
        parent::__construct((array)$attrs, $app);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimary()
    {
         if (($db = $this->app->db()) && ($primary = $db->getPrimary()) && $this->has($primary)) :
            return $this->get($primary);
         else :
            return Arr::first($this->attributes);
         endif;
    }
}