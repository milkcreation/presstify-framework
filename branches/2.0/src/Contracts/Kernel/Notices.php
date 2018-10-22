<?php

namespace tiFy\Contracts\Kernel;

interface Notices
{
    /**
     * Ajout d'un message de notification.
     *
     * @param string $type Type de notification.
     * @param string $message Message de notification.
     * @param array $datas Liste des données embarquées associées.
     *
     * @return string
     */
    public function add($type, $message = '', $datas = []);

    /**
     * Récupération de la liste des notifications par type.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function all($type);

    /**
     * Compte le nombre de notifications par type.
     *
     * @param string $type Type de notification.
     *
     * @return int
     */
    public function count($type);

    /**
     * Récupération de la liste d'une notification associée à un type.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function get($type);

    /**
     * Vérification d'existance d'une notification associée un type.
     *
     * @param string $type Type de notification.
     *
     * @return bool
     */
    public function has($type);

    /**
     * Récupération des données embarquées associée à une notification.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function getDatas($type);

    /**
     * Récupération des messages de notification par type.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function getMessages($type);

    /**
     * Récupération de la liste des types de notification déclarés.
     *
     * @return string[]
     */
    public function getTypes();

    /**
     * Vérification d'existance d'un type de notification.
     *
     * @param string $type Type de notification.
     *
     * @return bool
     */
    public function hasType($type);

    /**
     * Récupération de notification selon une liste d'arguments.
     *
     * @param string $type Type de notification.
     * @param array $query_args Liste d'arguments de données.
     *
     * @return array
     */
    public function query($type = 'error', $query_args = []);

    /**
     * Suppression de la liste des notifications à un type.
     *
     * @param string $type Type de notification.
     *
     * @return void
     */
    public function reset($type);

    /**
     * Affichage des messages de notification par type.
     *
     * @param string $type Type de notification.
     *
     * @return string
     */
    public function render($type);

    /**
     * Ajout d'un type de notification permis.
     *
     * @param string $type Type de notification permis.
     *
     * @return void
     */
    public function setType($type);

    /**
     * Définition des types de notification.
     *
     * @param array $types Liste des types de notification permis. error|warning|info|success par défaut.
     *
     * @return void
     */
    public function setTypes($types = ['error', 'warning', 'info', 'success']);
}