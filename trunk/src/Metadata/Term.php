<?php

namespace tiFy\Metadata;

use tiFy\Apps\AppController;

final class Term extends AppController
{
    /**
     * Liste des clés d'identifications de metadonnées.
     * @internal Tableau multidimensionné où la clé de l'index de premier niveau qualifie la taxonomie associée.
     *
     * @var array
     */
    protected $metaKeys = [];

    /**
     * Liste des types d'enregistrement unique (true)|multiple (false) d'une metadonnée.
     * @internal Tableau multidimensionné où la clé de l'index de premier niveau qualifie la taxonomie associée.
     *
     * @var array
     */
    protected $single = [];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('edited_term', 'save', 10, 3);
    }

    /**
     * Récupération d'une métadonné de taxonomie.
     *
     * @param int $term_id Identifiant de qualification du terme de taxonomie.
     * @param string $meta_key Clé d'identification de la métadonnée enregistrées en base de données.
     *
     * @return mixed[]
     */
    public function get($term_id, $meta_key)
    {
        global $wpdb;

        $query = "SELECT meta_id, meta_value" .
            " FROM {$wpdb->termmeta}" .
            " WHERE 1" .
            " AND {$wpdb->termmeta}.term_id = %d" .
            " AND {$wpdb->termmeta}.meta_key = %s";

        if ($order = get_term_meta($term_id, '_order_' . $meta_key, true)) :
            $query .= " ORDER BY FIELD( {$wpdb->termmeta}.term_id," . implode(',', $order) . ")";
        endif;

        if (! $metas = $wpdb->get_results($wpdb->prepare($query, $term_id, $meta_key))) :
            return [];
        endif;

        $_metas = [];
        foreach ((array)$metas as $index => $args) :
            $_metas[$args->meta_id] = maybe_unserialize($args->meta_value);
        endforeach;

        return $_metas;
    }

    /**
     * Vérification si l'enregistrement de la métadonnée en base est de type unique.
     *
     * @param string $taxonomy Identifiant de qualification de la taxonomie associée.
     * @param string $meta_key Clé d'identification de la métadonnée enregistrées en base de données.
     *
     * @return bool
     */
    public function isSingle($taxonomy, $meta_key)
    {
        return isset($this->single[$taxonomy][$meta_key]) ? $this->single[$taxonomy][$meta_key] : false;
    }

    /**
     * Déclaration d'une métadonné.
     *
     * @param string $taxonomy Identifiant de qualification de la taxonomie associée.
     * @param string $meta_key Clé d'identification de la métadonnée enregistrées en base de données.
     * @param bool $single Type d'enregistrement de la metadonnées en base. true (unique)|false (multiple).
     * @param callable $sanitize_callback Fonction ou Méthode de rappel appelé avant la sauvegarde en base de données. wp_unslash par défaut.
     *
     * @return void
     */
    public function register($taxonomy, $meta_key, $single = false, $sanitize_callback = 'wp_unslash')
    {
        // Bypass
        if (!empty($this->metaKeys[$taxonomy]) && in_array($meta_key, $this->metaKeys[$taxonomy])) :
            return;
        endif;

        $this->metaKeys[$taxonomy][] = $meta_key;
        $this->single[$taxonomy][$meta_key] = $single;

        if ($sanitize_callback !== '') :
            add_filter("tify_sanitize_meta_term_{$taxonomy}_{$meta_key}", $sanitize_callback);
        endif;
    }

    /**
     * Enregistrement de metadonnées de taxonomie.
     *
     * @param int $term_id Identifiant de qualification du terme de taxonomie.
     * @param int $tt_id
     * @param string $taxonomy Identifiant de qualification de la taxonomie associée.
     *
     * @return void
     */
    public function save($term_id, $tt_id, $taxonomy)
    {
        // Bypass
        /// Contrôle s'il s'agit d'une routine de sauvegarde automatique.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) :
            return;
        endif;
        /// Contrôle si le script est executé via Ajax.
        if (defined('DOING_AJAX') && DOING_AJAX) :
            return;
        endif;

        // Vérification d'existance de metadonnées déclarées pour la taxonomy
        if (empty($this->metaKeys[$taxonomy])) :
            return;
        endif;

        // Déclaration des variables
        $meta_keys = $this->metaKeys[$taxonomy];
        $termmeta = [];
        $meta_ids = [];
        $meta_exists = [];
        $request = [];

        // Récupération des metadonnés en $_POST
        foreach ($this->metaKeys[$taxonomy] as $key) :
            if (! $this->appRequest('POST')->has($key)) :
                continue;
            endif;

            $request[$key] = $this->appRequest('POST')->get($key, '');
        endforeach;

        foreach ($meta_keys as $meta_key) :
            // Vérification d'existance de la metadonnées en base
            if ($_meta = $this->get($term_id, $meta_key)) :
                $meta_exists += $_meta;
            endif;

            if (! isset($request[$meta_key])) :
                continue;
            endif;

            // Récupération des meta_ids de metadonnées unique
            if ($this->isSingle($taxonomy, $meta_key)) :
                $meta_id = $_meta ? key($_meta) : uniqid();
                array_push($meta_ids, $meta_id);
                $termmeta[$meta_key][$meta_id] = $request[$meta_key];

            // Récupération des meta_ids de metadonnées multiple
            elseif ($this->isSingle($taxonomy, $meta_key) === false) :
                $meta_ids += array_keys($request[$meta_key]);
                $termmeta[$meta_key] = $request[$meta_key];
            endif;
        endforeach;

        // Suppression des metadonnées absente du processus de sauvegarde
        foreach ($meta_exists as $meta_id => $meta_value) :
            if (! in_array($meta_id, $meta_ids)) :
                delete_metadata_by_mid('term', $meta_id);
            endif;
        endforeach;

        // Sauvegarde des metadonnées (mise à jour ou ajout)
        foreach ($meta_keys as $meta_key) :
            if (!isset($termmeta[$meta_key])) :
                continue;
            endif;

            $order = [];
            foreach ((array)$termmeta[$meta_key] as $meta_id => $meta_value) :
                $meta_value = apply_filters("tify_sanitize_meta_term_{$taxonomy}_{$meta_key}", $meta_value);

                if (is_int($meta_id) && get_metadata_by_mid('term', $meta_id)) :
                    $_meta_id = $meta_id;
                    update_metadata_by_mid('term', $meta_id, $meta_value);
                else :
                    $_meta_id = add_term_meta($term_id, $meta_key, $meta_value);
                endif;
                // Récupération de l'ordre des metadonnées multiple
                if ($this->isSingle($taxonomy, $meta_key) === false) :
                    $order[] = $_meta_id;
                endif;
            endforeach;

            // Sauvegarde de l'ordre
            if (!empty($order)) :
                update_term_meta($term_id, '_order_' . $meta_key, $order);
            endif;
        endforeach;

        return;
    }
}