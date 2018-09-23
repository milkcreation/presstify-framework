<?php

namespace tiFy\Db;

use tiFy\App\AppController;
use tiFy\Db\DbControllerInterface;

class Meta extends AppController
{
    /**
     * Classe de rappel du controleur de base de données associé.
     * @var DbControllerInterface
     */
    protected $db;

    /**
     * CONSTRUCTEUR.
     *
     * @param DbControllerInterface $db Classe de rappel du controleur de base de données associé.
     *
     * @return void
     */
    public function __construct(DbControllerInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Récupération du nom préfixé (réel) de la table d'enregistrement des métadonnées.
     *
     * @var string
     */
    public function getTableName()
    {
        return $this->_get_meta_table($this->db->getMetaType());
    }

    /**
     * Nom de la colonne de clé primaire.
     *
     * @var string
     */
    public function getPrimary()
    {
        return 'user' == $this->db->getMetaType() ? 'umeta_id' : 'meta_id';
    }

    /**
     * Nom de la colonne de jointure
     *
     * @return string
     */
    public function getJoinCol()
    {
        return $this->db->getMetaJoinCol() ?: sanitize_key($this->db->getMetaType() . '_id');
    }

    /**
     * Récupération de la valeur de la metadonnée d'un élément
     *
     * @param int $id ID de l'item
     * @param string $meta_key Optionel. Index de la métadonnée. Retournera, s'il n'est pas spécifié toutes les metadonnées relative à l'objet.
     * @param bool $single Optionel, default is true. Si true, retourne uniquement la première valeur pour l'index meta_key spécifié. Ce paramètre n'a pas d'impact lorsqu'aucun index meta_key n'est spécifié.
     *
     * @return mixed
     */
    public function get($object_id, $meta_key = '', $single = true)
    {
        if (!($meta_type = $this->db->getMetaType()) || !is_numeric($object_id)) :
            return false;
        endif;

        $object_id = absint($object_id);
        if (!$object_id) :
            return false;
        endif;

        $check = apply_filters("get_{$meta_type}_metadata", null, $object_id, $meta_key, $single);
        if (null !== $check) :
            if ($single && is_array($check)) :
                return $check[0];
            else :
                return $check;
            endif;
        endif;

        if (!$meta_cache = wp_cache_get($object_id, "{$meta_type}_meta")) :
            $meta_cache = $this->update_meta_cache([$object_id]);
            $meta_cache = $meta_cache[$object_id];
        endif;

        if (!$meta_key) :
            return $meta_cache;
        endif;

        if (isset($meta_cache[$meta_key])) :
            if ($single) :
                return maybe_unserialize($meta_cache[$meta_key][0]);
            else :
                return array_map('maybe_unserialize', $meta_cache[$meta_key]);
            endif;
        endif;

        if ($single) :
            return '';
        else :
            return [];
        endif;
    }

    /**
     * Récupération de toutes les metadonnés d'un élément.
     *
     * @param int $id Identifiant de la clé primaire de l'élément dans la table principale.
     *
     * @return array
     */
    public function all($object_id)
    {
        return $this->get($object_id);
    }

    /**
     * Récupération de la valeur de la metadonnée d'un selon son identifiant de clé primaire de table des metadonnés.
     *
     * @param int $meta_id Identifiant de clé primaire de l'éléments dans la table des métadonnées.
     *
     * @return mixed
     */
    public function get_by_mid($meta_id)
    {
        if (!($meta_type = $this->db->getMetaType()) || !is_numeric($meta_id)) :
            return false;
        endif;

        $meta_id = absint($meta_id);
        if (!$meta_id) :
            return false;
        endif;

        $table = $this->getTableName();
        if (!$table) :
            return false;
        endif;

        $id_column = ('user' === $meta_type) ? 'umeta_id' : 'meta_id';

        $meta = $this->db->sql()->get_row(
            $this->db->sql()->prepare(
                "SELECT * FROM $table WHERE $id_column = %d",
                $meta_id
            )
        );

        if (empty($meta)) :
            return false;
        endif;

        if (isset($meta->meta_value)) :
            $meta->meta_value = maybe_unserialize($meta->meta_value);
        endif;

        return $meta;
    }

    /**
     * Ajout d'une metadonnée pour un élément.
     *
     * @param int $id Identifiant de qualification de l'élément dans la table principale.
     * @param string $meta_key Index de la métadonnée.
     * @param mixed $meta_value Valeur de la métadonnée. Les données non scalaires seront serialisées.
     * @param bool $unique Optionnel, true par défaut. Permet de définir si seule une metadonnée avec la même clé d'index (meta_key) est autorisée.
     *
     * @return int
     */
    public function add($object_id, $meta_key, $meta_value, $unique = true)
    {
        if (!($meta_type = $this->db->getMetaType()) || !$meta_key || !is_numeric($object_id)) :
            return false;
        endif;

        $object_id = absint($object_id);
        if (!$object_id) :
            return false;
        endif;

        $table = $this->getTableName();
        if (!$table) :
            return false;
        endif;

        $column = $this->getJoinCol();

        $meta_key = \wp_unslash($meta_key);
        $meta_value = \wp_unslash($meta_value);
        $meta_value = sanitize_meta($meta_key, $meta_value, $meta_type);

        $check = apply_filters("add_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value, $unique);
        if (null !== $check) :
            return $check;
        endif;

        if (
            $unique &&
            $this->db->sql()->get_var(
                $this->db->sql()->prepare(
                    "SELECT COUNT(*) FROM $table WHERE meta_key = %s AND $column = %d",
                    $meta_key,
                    $object_id
                )
            )
        ) :
            return false;
        endif;

        $_meta_value = $meta_value;
        $meta_value = maybe_serialize($meta_value);

        do_action("add_{$meta_type}_meta", $object_id, $meta_key, $_meta_value);

        $result = $this->db->sql()->insert(
            $table,
            [
                $column      => $object_id,
                'meta_key'   => $meta_key,
                'meta_value' => $meta_value,
            ]
        );

        if (!$result) :
            return false;
        endif;

        $mid = (int)$this->db->sql()->insert_id;

        wp_cache_delete($object_id, "{$meta_type}_meta");

        do_action("added_{$meta_type}_meta", $mid, $object_id, $meta_key, $_meta_value);

        return $mid;
    }

    /**
     * Mise à jour d'une metadonnée pour un élément.
     *
     * @param int $id Identifiant de qualification de l'élément dans la table principale.
     * @param string $meta_key Index de la métadonnée.
     * @param mixed $meta_value Valeur de la métadonnée. Les données non scalaires seront serialisées.
     * @param mixed $prev_value Optionnel. Valeur de contrôle de la métadonnée.
     *
     * @return bool
     */
    public function update($object_id, $meta_key, $meta_value, $prev_value = '')
    {
        if (!($meta_type = $this->db->getMetaType()) || !$meta_key || !is_numeric($object_id)) :
            return false;
        endif;

        $object_id = absint($object_id);
        if (!$object_id) :
            return false;
        endif;

        $table = $this->getTableName();
        if (!$table) :
            return false;
        endif;

        $column = $this->getJoinCol();
        $id_column = ('user' === $meta_type) ? 'umeta_id' : 'meta_id';

        $raw_meta_key = $meta_key;
        $meta_key = wp_unslash($meta_key);
        $passed_value = $meta_value;
        $meta_value = wp_unslash($meta_value);
        $meta_value = sanitize_meta($meta_key, $meta_value, $meta_type);

        $check = apply_filters("update_{$meta_type}_metadata", null, $object_id, $meta_key, $meta_value,
            $prev_value);
        if (null !== $check) :
            return (bool)$check;
        endif;

        if (empty($prev_value)) :
            $old_value = $this->get($object_id, $meta_key, false);
            if (count($old_value) == 1) :
                if ($old_value[0] === $meta_value) :
                    return false;
                endif;
            endif;
        endif;

        $meta_ids = $this->db->sql()->get_col(
            $this->db->sql()->prepare(
                "SELECT $id_column FROM $table WHERE meta_key = %s AND $column = %d",
                $meta_key,
                $object_id
            )
        );
        if (empty($meta_ids)) :
            return $this->add($object_id, $raw_meta_key, $passed_value);
        endif;

        $_meta_value = $meta_value;
        $meta_value = maybe_serialize($meta_value);

        $data = compact('meta_value');
        $where = [$column => $object_id, 'meta_key' => $meta_key];

        if (!empty($prev_value)) :
            $prev_value = maybe_serialize($prev_value);
            $where['meta_value'] = $prev_value;
        endif;

        foreach ($meta_ids as $meta_id) :
            do_action("update_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value);

            if ('post' == $meta_type) :
                do_action('update_postmeta', $meta_id, $object_id, $meta_key, $meta_value);
            endif;
        endforeach;

        $result = $this->db->sql()->update($table, $data, $where);
        if (!$result) :
            return false;
        endif;

        wp_cache_delete($object_id, "{$meta_type}_meta");

        foreach ($meta_ids as $meta_id) :
            do_action("updated_{$meta_type}_meta", $meta_id, $object_id, $meta_key, $_meta_value);

            if ('post' == $meta_type) :
                do_action('updated_postmeta', $meta_id, $object_id, $meta_key, $meta_value);
            endif;
        endforeach;

        return true;
    }

    /**
     * Suppression de métadonnée associée à un élément.
     *
     * @param int $id Identifiant de qualification de l'élément dans la table principale.
     * @param string $meta_key Index de la métadonnée.
     * @param mixed $meta_value Valeur de la métadonnée à supprimer.
     * @param bool $delete_all Permet la suppression de toutes le metadonnées pour une même meta_key. $objet_id doit valoir 0.
     *
     * @return bool
     */
    final function delete($object_id, $meta_key, $meta_value = '', $delete_all = false)
    {
        if (!($meta_type = $this->db->getMetaType()) || !$meta_key || !is_numeric($object_id) && !$delete_all) :
            return false;
        endif;

        $object_id = absint($object_id);
        if (!$object_id && !$delete_all) :
            return false;
        endif;

        $table = $this->getTableName();
        if (!$table) {
            return false;
        }

        $type_column = $this->getJoinCol();
        $id_column = ('user' === $meta_type) ? 'umeta_id' : 'meta_id';

        $meta_key = wp_unslash($meta_key);
        $meta_value = wp_unslash($meta_value);

        $check = apply_filters(
            "delete_{$meta_type}_metadata",
            null,
            $object_id,
            $meta_key,
            $meta_value,
            $delete_all
        );
        if (null !== $check) :
            return (bool)$check;
        endif;

        $_meta_value = $meta_value;
        $meta_value = maybe_serialize($meta_value);

        $query = $this->db->sql()->prepare("SELECT $id_column FROM $table WHERE meta_key = %s", $meta_key);

        if (!$delete_all) :
            $query .= $this->db->sql()->prepare(" AND $type_column = %d", $object_id);
        endif;

        if ('' !== $meta_value && null !== $meta_value && false !== $meta_value) :
            $query .= $this->db->sql()->prepare(" AND meta_value = %s", $meta_value);
        endif;

        $meta_ids = $this->db->sql()->get_col($query);
        if (!count($meta_ids)) :
            return false;
        endif;

        if ($delete_all) :
            $value_clause = '';
            if ('' !== $meta_value && null !== $meta_value && false !== $meta_value) :
                $value_clause = $this->db->sql()->prepare(" AND meta_value = %s", $meta_value);
            endif;

            $object_ids = $this->db->sql()->get_col(
                $this->db->sql()->prepare(
                    "SELECT $type_column FROM $table WHERE meta_key = %s $value_clause",
                    $meta_key
                )
            );
        endif;

        do_action("delete_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $_meta_value);

        if ('post' == $meta_type) :
            do_action('delete_postmeta', $meta_ids);
        endif;

        $query = "DELETE FROM $table WHERE $id_column IN( " . implode(',', $meta_ids) . " )";

        $count = $this->db->sql()->query($query);

        if (!$count) :
            return false;
        endif;

        if ($delete_all) :
            foreach ((array)$object_ids as $o_id) :
                wp_cache_delete($o_id, "{$meta_type}_meta");
            endforeach;
        else :
            wp_cache_delete($object_id, "{$meta_type}_meta");
        endif;

        do_action("deleted_{$meta_type}_meta", $meta_ids, $object_id, $meta_key, $_meta_value);

        if ('post' == $meta_type) :
            do_action('deleted_postmeta', $meta_ids);
        endif;

        return true;
    }

    /** == Suppression de toutes les métadonnées d'un élément == **/
    final function delete_all($id)
    {
        $table = $this->getTableName();
        if (!$table) :
            return false;
        endif;
        $column = $this->getJoinCol();

        $this->db->sql()->delete($table, [$column => $id], '%d');
    }

    /* = HELPERS = */
    /** == == **/
    private function _get_meta_table($type)
    {
        $table_name = $type . 'meta';

        if (! empty($this->db->sql()->{$table_name})) :
            return $this->db->sql()->{$table_name};
        endif;

        return ;
    }

    /** == == **/
    private function update_meta_cache($object_ids)
    {
        if (!($meta_type = $this->db->getMetaType())|| !$object_ids) :
            return false;
        endif;

        $table = $this->getTableName();
        if (!$table) :
            return false;
        endif;

        $column = $this->getJoinCol();

        if (!is_array($object_ids)) {
            $object_ids = preg_replace('|[^0-9,]|', '', $object_ids);
            $object_ids = explode(',', $object_ids);
        }

        $object_ids = array_map('intval', $object_ids);

        $cache_key = "{$meta_type}_meta";
        $ids = [];
        $cache = [];
        foreach ($object_ids as $id) {
            $cached_object = wp_cache_get($id, $cache_key);
            if (false === $cached_object) {
                $ids[] = $id;
            } else {
                $cache[$id] = $cached_object;
            }
        }

        if (empty($ids)) {
            return $cache;
        }

        // Get meta info
        $id_list = join(',', $ids);
        $id_column = ('user' === $meta_type) ? 'umeta_id' : 'meta_id';
        $meta_list = $this->db->sql()->get_results(
            "SELECT $column, meta_key, meta_value FROM $table WHERE $column IN ($id_list) ORDER BY $id_column ASC",
            ARRAY_A
        );

        if (!empty($meta_list)) {
            foreach ($meta_list as $metarow) {
                $mpid = intval($metarow[$column]);
                $mkey = $metarow['meta_key'];
                $mval = $metarow['meta_value'];

                // Force subkeys to be array type:
                if (!isset($cache[$mpid]) || !is_array($cache[$mpid])) {
                    $cache[$mpid] = [];
                }
                if (!isset($cache[$mpid][$mkey]) || !is_array($cache[$mpid][$mkey])) {
                    $cache[$mpid][$mkey] = [];
                }

                // Add a value to the current pid/key:
                $cache[$mpid][$mkey][] = $mval;
            }
        }

        foreach ($ids as $id) {
            if (!isset($cache[$id])) {
                $cache[$id] = [];
            }
            wp_cache_add($id, $cache[$id], $cache_key);
        }

        return $cache;
    }
}
