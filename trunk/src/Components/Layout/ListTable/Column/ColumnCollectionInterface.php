<?php

namespace tiFy\Components\Layout\ListTable\Column;

use Illuminate\Support\Collection;
use tiFy\Components\Layout\ListTable\Column\ColumnItemInterface;
use tiFy\Apps\Layout\LayoutControllerInterface;

interface ColumnCollectionInterface
{
    /**
     * Récupération de la liste des colonnes.
     *
     * @return Collection|ColumnItemInterface[]
     */
    public function all();

    /**
     * Récupération d'une colonne.
     *
     * @param string $name Nom de qualification.
     *
     * @return ColumnItemInterface
     */
    public function get($name);

    /**
     * Récupération de la liste des entêtes HTML.
     *
     * @param bool $with_id Action de l'id HTML.
     *
     * @return array
     */
    public function getHeaders($with_id = true);

    /**
     * Récupération de la liste des noms de qualification des colonnes masquées.
     *
     * @return string[]
     */
    public function getHidden();

    /**
     * Récupération de la liste des informations complètes concernant les colonnes.
     *
     * @return array
     */
    public function getInfos();

    /**
     * Récupération de la liste de définition des colonnes.
     * @internal Couple nom de qualification => intitulé.
     *
     * @return array
     */
    public function getList();

    /**
     * Récupération du nom de qualification de la colonne principale.
     *
     * @return string
     */
    public function getPrimary();

    /**
     * Récupération de la liste des noms de qualification des colonnes ouverte à l'ordonnacement.
     *
     * @return string[]
     */
    public function getSortable();

    /**
     * Récupération de la liste des noms de qualification des colonnes visibles.
     *
     * @return string[]
     */
    public function getVisible();

    /**
     * Récupération du nombre de colonne affichée.
     *
     * @return int
     */
    public function countVisible();

    /**
     * Vérifie si le nom de qualification d'une colonne correspond à la colonne principale.
     *
     * @param string $name Nom de qualification de la colonne à vérifier.
     *
     * @return bool
     */
    public function isPrimary($name);

    /**
     * Traitement de la liste des colonnes.
     *
     * @param array $columns Liste des colonnes.
     *
     * @return void
     */
    public function parse($columns = []);
}