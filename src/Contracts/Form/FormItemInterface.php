<?php

namespace tiFy\Contracts\Form;

use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Forms\FormItemController;

interface FormItemInterface
{
    /**
     * Récupération de la classe de rappel du formulaire.
     *
     * @return FormItemController
     */
    public function getForm();

    /**
     * Récupération d'un champ.
     *
     * @param string $field_slug Identifiant de qualification du champ.
     *
     * @return FieldItemController
     */
    public function getField($field_slug);

    /**
     * Traitement des variables de requête au moment de la soumission du formulaire.
     * @internal La méthode de surcharge de la classe parse_query_var_{field_slug} doit exister.
     *
     * @param string $field_slug Identifiant de qualification du champ.
     * @param mixed $value Valeur du champ.
     *
     * @return void
     */
    public function parseQueryVar($field_slug, $value);

    /**
     * Vérification d'intégrité des variables de requête au moment de la soumission du formulaire.
     *
     * @param FieldItemController $field Classe de rappel du champ à vérifier.
     * @param mixed $errors Liste des erreurs existantes.
     *
     * @return void
     */
    public function checkQueryVar($field, &$errors);

    /**
     * Execution de fonction de court-cicuitage.
     * @internal La méthode de surcharge de la classe call_{callback} doit exister.
     * @see \
     *
     * @param string $callback Identifiant de qualification de la méthode de rappel. ex. handle_successfully.
     *
     * @return callable
     */
    public function call($hookname, $args = []);

    /**
     * Traitement par défaut des variables de requête lors de la soumission.
     *
     * @param string $field_slug Identifiant de qualification du champ.
     * @param array $value Valeur de retour.
     *
     * @return mixed
     */
    public function parse_query_vars($field_slug, $value);

    /**
     * Vérification par défaut de l'intégrité des variables de requêtes.
     *
     * @param FieldItemController $field Classe de rappel du controleur de champ.
     * @param array Liste des erreurs existantes.
     *
     * @return void
     */
    public function check_query_vars($field, &$errors);

    /**
     * Affichage du formulaire.
     *
     * @param bool $echo Activation de l'affichage ou retour.
     *
     * @return string
     */
    public function display($echo = false);
}