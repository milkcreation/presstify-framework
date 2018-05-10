<?php

namespace tiFy\Form\Fields;

use Illuminate\Support\Collection;
use tiFy\Apps\AppTrait;
use tiFy\Form\CommonDependencyAwareTrait;
use tiFy\Form\Fields\FieldItemController;

class FieldItemCollectionController extends Collection
{
    use CommonDependencyAwareTrait, AppTrait;

    /**
     * Liste des classes de rappel des champs associé à un formulaire.
     * @var FieldItemController[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param FieldItemController[] $items Liste des classes de rappel des champs associé à un formulaire.
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->first(function ($item) {
            $this->setForm($item->getForm());
        });
    }

    /**
     * Récupération d'un champ selon son identifiant de qualification.
     *
     * @param string $slug Identifiant de qualification du champ.
     *
     * @return null|FieldItemController
     */
    public function getField($slug)
    {
        if ($key = $this->search(function ($item) use ($slug) {
            return $item->getSlug() === $slug;
        })) :
            return $this->get($key);
        endif;
    }

    /**
     * Récupération de la liste des champs par ordre d'affichage.
     *
     * @return FieldItemController[]
     */
    public function byOrder()
    {
        return $this->sortBy(function ($item) {
            return $item->getOrder();
        });
    }
}