<?php

namespace tiFy\PostType;

use Illuminate\Support\Str;
use tiFy\Kernel\Labels\LabelsBag;

/**
 * @see https://codex.wordpress.org/Function_Reference/register_post_type
 */
class PostTypeLabelsBag extends LabelsBag
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name' => Str::ucfirst($this->getPlural()),

            'singular_name' => Str::ucfirst($this->getSingular()),

            'add_new' => !$this->getGender()
                ? sprintf(__('Ajouter un %s', 'tify'), $this->getSingular())
                : sprintf(__('Ajouter une %s', 'tify'), $this->getSingular()),

            'add_new_item' => !$this->getGender()
                ? sprintf(__('Ajouter un %s', 'tify'), $this->getSingular())
                : sprintf(__('Ajouter une %s', 'tify'), $this->getSingular()),

            'edit_item' => $this->defaultEditItem(),

            'new_item' => !$this->getGender()
                ? sprintf(__('Créer un %s', 'tify'), $this->getSingular())
                : sprintf(__('Créer une %s', 'tify'), $this->getSingular()),

            'view_item' => !$this->getGender()
                ? sprintf(__('Voir cet %s', 'tify'), $this->getSingular())
                : sprintf(__('Voir cette %s', 'tify'), $this->getSingular()),

            'view_items' => sprintf(__('Voir ces %s', 'tify'), $this->getPlural()),

            'search_items' => !$this->getGender()
                ? sprintf(__('Rechercher un %s', 'tify'), $this->getSingular())
                : sprintf(__('Rechercher une %s', 'tify'), $this->getSingular()),

            'not_found' => !$this->getGender()
                ? sprintf(__('Aucun %s trouvé', 'tify'), Str::ucfirst($this->getSingular()))
                : sprintf(__('Aucune %s trouvée', 'tify'), Str::ucfirst($this->getSingular())),

            'not_found_in_trash' => !$this->getGender()
                ? sprintf(__('Aucun %s dans la corbeille', 'tify'), Str::ucfirst($this->getSingular()))
                : sprintf(__('Aucune %s dans la corbeille', 'tify'), Str::ucfirst($this->getSingular())),

            'parent_item_colon' => !$this->getGender()
                ? sprintf(__('%s parent', 'tify'), Str::ucfirst($this->getSingular()))
                : sprintf(__('%s parent', 'tify'), Str::ucfirst($this->getSingular())),

            'all_items' => !$this->getGender()
                ? sprintf(__('Tous les %s', 'tify'), $this->getPlural())
                : sprintf(__('Toutes les %s', 'tify'), $this->getPlural()),

            'archives' => !$this->getGender()
                ? sprintf(__('Tous les %s', 'tify'), $this->getPlural())
                : sprintf(__('Toutes les %s', 'tify'), $this->getPlural()),

            'attributes' => !$this->getGender()
                ? sprintf(__('Tous les %s', 'tify'), $this->getPlural())
                : sprintf(__('Toutes les %s', 'tify'), $this->getPlural()),

            // @todo 'insert_into_item' => ''

            // @todo 'uploaded_to_this_item' => ''

            // @todo 'featured_image' => ''

            // @todo 'set_featured_image' => ''

            // @todo 'remove_featured_image' => ''

            // @todo 'use_featured_image' => ''

            'menu_name' => _x(Str::ucfirst($this->getPlural()), 'admin menu', 'tify'),

            // @todo 'filter_items_list' => ''

            // @todo 'items_list_navigation' => ''

            // @todo 'items_list' => ''

            // @todo 'filter_items_list' => ''

            'name_admin_bar' => _x(Str::ucfirst($this->getSingular()), 'add new on admin bar', 'tify'),

            /* @todo
             * 'datas_item'                 => $this->defaultDatasItem(),
             * 'import_items'               => sprintf(__('Importer des %s', 'tify'), $this->getPlural()),
             * 'export_items'               => sprintf(__('Export des %s', 'tify'), $this->getPlural()),
             */
        ];
    }
}