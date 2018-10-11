<?php

namespace tiFy\Contracts\Metabox;

interface MetaboxInterface
{
    /**
     * Ajout d'un élément.
     *
     * @param string $screen Ecran d'affichage de l'élément.
     * @param array $attrs Liste des attributs de configuration de l'élément.
     *
     * @return $this
     */
    public function add($screen, $attrs = []);

    /**
     * Récupération de la liste des éléments.
     *
     * @return Collection|MetaboxItemController[]
     */
    public function getItems();

    /**
     * Déclaration d'une boîte de saisie à supprimer
     *
     * @param string $screen Ecran d'affichage de l'élément.
     * @param string $id Identifiant de qualification de la metaboxe
     * @param string $context normal|side|advanced
     *
     * @return void
     */
    public function remove($screen, $id, $context = 'normal');
}