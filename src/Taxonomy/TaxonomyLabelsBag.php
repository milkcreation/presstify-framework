<?php

namespace tiFy\Taxonomy;

use Illuminate\Support\Str;
use tiFy\Kernel\Labels\LabelsBag;

/**
 * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
 */
class TaxonomyLabelsBag extends LabelsBag
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name' => Str::ucfirst($this->getPlural()),

            'singular_name' => Str::ucfirst($this->getSingular()),

            'menu_name' => _x(Str::ucfirst($this->getPlural()), 'admin menu', 'tify'),

            'all_items' => !$this->getGender()
                ? sprintf(__('Tous les %s', 'tify'), $this->getPlural())
                : sprintf(__('Toutes les %s', 'tify'), $this->getPlural()),

            'edit_item' => $this->defaultEditItem(),

            'view_item' => !$this->getGender()
                ? sprintf(__('Voir cet %s', 'tify'), $this->getSingular())
                : sprintf(__('Voir cette %s', 'tify'), $this->getSingular()),

            'update_item' => !$this->getGender()
                ? sprintf(__('Mettre à jour ce %s', 'tify'), $this->getSingular())
                : sprintf(__('Mettre à jour cette %s', 'tify'), $this->getSingular()),

            'add_new_item' => !$this->getGender()
                ? sprintf(__('Ajouter un %s', 'tify'), $this->getSingular())
                : sprintf(__('Ajouter une %s', 'tify'), $this->getSingular()),

            'new_item_name' => !$this->getGender()
                ? sprintf(__('Créer un %s', 'tify'), $this->getSingular())
                : sprintf(__('Créer une %s', 'tify'), $this->getSingular()),

            'parent_item' => !$this->getGender()
                ? sprintf(__('%s parent', 'tify'), Str::ucfirst($this->getSingular()))
                : sprintf(__('%s parent', 'tify'), Str::ucfirst($this->getSingular())),

            'parent_item_colon' => !$this->getGender()
                ? sprintf(__('%s parent', 'tify'), Str::ucfirst($this->getSingular()))
                : sprintf(__('%s parent', 'tify'), Str::ucfirst($this->getSingular())),

            'search_items' => !$this->getGender()
                ? sprintf(__('Rechercher un %s', 'tify'), $this->getSingular())
                : sprintf(__('Rechercher une %s', 'tify'), $this->getSingular()),

            'popular_items' => sprintf(__('%s populaires', 'tify'), Str::ucfirst($this->getPlural())),

            'separate_items_with_commas' => sprintf(__('Séparer les %s par une virgule', 'tify'), $this->getPlural()),

            'add_or_remove_items' => sprintf(__('Ajouter ou supprimer des %s', 'tify'), $this->getPlural()),

            'choose_from_most_used' => !$this->getGender()
                ? sprintf(__('Choisir parmi les %s les plus utilisés', 'tify'), $this->getPlural())
                : sprintf(__('Choisir parmi les %s les plus utilisées', 'tify'), $this->getPlural()),

            'not_found' => !$this->getGender()
                ? sprintf(__('Aucun %s trouvé', 'tify'), Str::ucfirst($this->getSingular()))
                : sprintf(__('Aucune %s trouvée', 'tify'), Str::ucfirst($this->getSingular())),


            /* @todo
             * 'datas_item'                 => $this->defaultDatasItem(),
             * 'import_items'               => sprintf(__('Importer des %s', 'tify'), $this->getPlural()),
             * 'export_items'               => sprintf(__('Export des %s', 'tify'), $this->getPlural()),
             */
        ];
    }
}