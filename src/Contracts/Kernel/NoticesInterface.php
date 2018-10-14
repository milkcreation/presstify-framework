<?php

namespace tiFy\Contracts\Kernel;

interface NoticesInterface
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
     * @param string $type Identifiant de qualification du type à vérifier.
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