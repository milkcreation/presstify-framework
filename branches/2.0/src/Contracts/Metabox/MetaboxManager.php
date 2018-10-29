<?php

namespace tiFy\Contracts\Metabox;

interface MetaboxManager
{
    /**
     * Ajout d'un élément.
     *
     * @param string Nom de qualification.
     * @param null|string $screen Ecran d'affichage de l'élément. Null correspond à l'écran d'affichage courant.
     * @param array $attrs Liste des attributs de configuration de l'élément.
     *
     * @return $this
     */
    public function add($name, $screen = null, $attrs = []);

    /**
     * Récupération de la liste des éléments.
     *
     * @return Collection|MetaboxFactory[]
     */
    public function getItems();

    /**
     * Déclaration d'une boîte de saisie à supprimer
     *
     * @param string $screen Ecran d'affichage de l'élément.
     * @param string $id Identifiant de qualification de la metaboxe
     * @param string $context normal|side|advanced
     *
     * @return $this
     */
    public function remove($screen, $id, $context = 'normal');
}