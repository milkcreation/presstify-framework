<?php declare(strict_types=1);

namespace _tiFy\Contracts\Metabox;

use _tiFy\Contracts\Support\Manager;

interface MetaboxManager extends Manager
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
     * Déclaration d'une boîte de saisie à supprimer
     *
     * @param string $id Identifiant de qualification HTML de la metaboxe.
     * @param string $screen Ecran d'affichage de l'élément. Null pour l'écran courant.
     * @param string $context normal|side|advanced.
     *
     * @return $this
     */
    public function remove($id, $screen = null, $context = 'normal');

    /**
     * Personnalisation des attributs de configuration d'une boîte à onglets.
     *
     * @param string $attrs Liste des attributs de personnalisation.
     * @param string $screen Ecran d'affichage de l'élément. Null pour l'écran courant.
     *
     * @return $this
     */
    public function tab($attrs = [], $screen = null);
}