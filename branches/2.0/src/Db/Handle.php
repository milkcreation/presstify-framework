<?php

namespace tiFy\Db;

use tiFy\Apps\AppController;
use tiFy\Db\DbControllerInterface;

class Handle extends AppController
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
     * Enregistrement d'un élément en base de données.
     * 
     * @param array $data Liste des données de l'élément.
     *
     * @return int
     */
    public function record($data = [])
    {
        $primary = $this->db->getPrimary();

        if (!empty($data[$primary]) && $this->db->select()->count([$primary => $data[$primary]])) :
            return $this->update($data[$primary], $data);
        else :
            return $this->create($data);
        endif;
    }

    /**
     * Création d'un nouvel élément en base de données.
     *
     * @param array $data Liste des données de l'élément.
     *
     * @return int
     */
    public function create($data = [])
    {
        // Extraction des metadonnées
        if (isset($data['item_meta'])) :
            $metas = $data['item_meta'];
            unset($data['item_meta']);
        else :
            $metas = false;
        endif;

        // Formatage des données
        $data = $this->db->parser()->validate($data);
        $data = array_map('maybe_serialize', $data);

        // Enregistrement de l'élément en base de données
        $this->db->sql()->insert($this->db->getTableName(), $data);
        $id = $this->db->sql()->insert_id;

        // Enregistrement des metadonnées de l'élément en base
        if (is_array($metas) && $this->db->hasMeta()) :
            foreach ($metas as $meta_key => $meta_value) :
                $this->db->meta()->update($id, $meta_key, $meta_value);
            endforeach;
        endif;

        return $id;
    }

    /**
     * Mise à jour d'un élément en base de données.
     *
     * @param mixed $id Identifiant de qualification de la clé primaire.
     * @param array $data Liste des données de l'élément.
     *
     * @return int
     */
    public function update($id, $data = [])
    {
        // Extraction des metadonnées
        if (isset($data['item_meta'])) :
            $metas = $data['item_meta'];
            unset($data['item_meta']);
        else :
            $metas = false;
        endif;

        // Formatage des données
        $data = $this->db->parser()->validate($data);
        $data = array_map('maybe_serialize', $data);

        $this->db->sql()->update($this->db->getTableName(), $data, [$this->db->getPrimary() => $id]);

        // Enregistrement des metadonnées de l'élément en base
        if (is_array($metas) && $this->db->hasMeta()) :
            foreach ((array)$metas as $meta_key => $meta_value) :
                $this->db->meta()->update($id, $meta_key, $meta_value);
            endforeach;
        endif;

        return $id;
    }

    /**
     * Suppression d'un élément en base de données.
     *
     * @param mixed $id Identifiant de qualification de la clé primaire.
     *
     * @return int
     */
    public function delete_by_id($id)
    {
        return $this->db->sql()->delete($this->db->getTableName(), [$this->db->getPrimary() => $id]);
    }

    /**
     *
     */
    public function prepare($query, $args)
    {
        return $this->db->sql()->prepare($query, $args);
    }

    /**
     *
     */
    public function query($query)
    {
        return $this->db->sql()->query($query);
    }

    /**
     *
     */
    public function replace($data = [], $format = null)
    {
        return $this->db->sql()->replace($this->db->getTableName(), $data, $format);
    }

    /**
     *
     */
    public function delete($where, $where_format = null)
    {
        return $this->db->sql()->delete($this->db->getTableName(), $where, $where_format);
    }

    /**
     * Récupération de la valeur numérique de la prochaine clé primaire
     *
     * @return int
     */
    public function next()
    {
        if ($last_insert_id = $this->db->sql()->query("SELECT LAST_INSERT_ID() FROM {$this->wpdb_table}")) :
            return ++$last_insert_id;
        endif;
    }
}