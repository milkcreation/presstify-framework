<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Template\Factory\FactoryLabels;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Labels extends FactoryLabels
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function all_items()
    {
        return sprintf(
            $this->hasGender() ? __('Toutes les %s', 'tify') : __('Tous les %s', 'tify'),
            $this->getPlural()
        );
    }

    /**
     * @inheritDoc
     */
    public function no_item()
    {
        return sprintf(
            $this->hasGender() ? __('Aucune %s trouvée.', 'tify') : __('Aucun %s trouvé.', 'tify'),
            $this->getSingular()
        );
    }

    /**
     * @inheritDoc
     */
    public function page_title()
    {
        return sprintf(
            $this->hasGender() ? __('Toutes les %s', 'tify') : __('Tous les %s', 'tify'),
            $this->getPlural()
        );
    }

    /**
     * @inheritDoc
     */
    public function search_item()
    {
        return sprintf(
            $this->hasGender() ? __('Rechercher une %s', 'tify') : __('Rechercher un %s', 'tify'),
            $this->getSingular()
        );
    }
}