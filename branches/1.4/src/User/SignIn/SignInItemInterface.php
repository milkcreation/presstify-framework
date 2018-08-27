<?php

namespace tiFy\User\SignIn;

interface SignInItemInterface
{
    /**
     * Vérification des droits d'authentification d'un utilisateur.
     *
     * @param \WP_User $user
     * @param string $username Identifiant de l'utilisateur passé en argument de la requête d'authentification.
     * @param string $password Mot de passe en clair passé en argument de la requête d'authentification.
     *
     * @return \WP_Error|\WP_User
     */
    public function authenticate($user, $username, $password);

    /**
     * Récupération de l'url de redirection du formulaire d'authentification.
     *
     * @param string $redirect_url Url de redirection personnalisée.
     * @param \WP_User $user Utilisateur courant.
     *
     * @return string
     */
    public function getFormRedirect($redirect_url = '', $user);

    /**
     * Récupération de l'url de redirection du formulaire d'authentification.
     *
     * @param string $redirect_url Url de redirection personnalisée.
     * @param \WP_User $user Utilisateur courant
     *
     * @return string
     */
    public function getLogoutRedirect($redirect_url = '', $user);

    /**
     * Affichage du formulaire d'authentification.
     *
     * @return string
     */
    public function form();

    /**
     * Affichage des champs additionnels du formulaire d'authentification.
     *
     * @return string
     */
    public function formAdditionnalFields();

    /**
     * Post-affichage du formulaire d'authentification.
     *
     * @return string
     */
    public function formAfter();

    /**
     * Pré-affichage du formulaire d'authentification.
     *
     * @return string
     */
    public function formBefore();

    /**
     * Affichage du corps du formulaire.
     *
     * @return string
     */
    public function formBody();

    /**
     * Affichage des notification d'erreurs de soumission au formulaire d'authentification.
     *
     * @return string
     */
    public function formErrors();

    /**
     * Affichage du champ "Mot de passe" du formulaire d'authentification.
     *
     * @return string
     */
    public function formFieldPassword();

    /**
     * Affichage du champ "Se souvenir de moi" du formulaire d'authentification.
     *
     * @return string
     */
    public function formFieldRemember();

    /**
     * Affichage du champ "Bouton de soumission" du formulaire d'authentification.
     *
     * @return string
     */
    public function formFieldSubmit();

    /**
     * Affichage du champ "Identifiant" du formulaire d'authentification.
     *
     * @return string
     */
    public function formFieldUsername();

    /**
     * Affichage du pied de formulaire.
     *
     * @return string
     */
    public function formFooter();

    /**
     * Affichage de l'entête du formulaire.
     *
     * @return string
     */
    public function formHeader();

    /**
     * Affichage des champs cachés (requis).
     *
     * @return string
     */
    public function formHiddenFields();

    /**
     * Affichage des message de notification d'informations.
     *
     * @return string
     */
    public function formInfos();

    /**
     * Affichage du lien de déconnection.
     *
     * @param array $attrs Liste des attributs de personnalisation.
     *
     * @return string
     */
    public function logoutLink($attrs = []);

    /**
     * Affichage du lien vers l'interface de récupération de mot de passe oublié.
     *
     * @return string
     */
    public function lostpasswordLink();

    /**
     * Action lancée en cas de succès de connection.
     *
     * @param string  $user_login Identifiant de connection.
     * @param \WP_User $user Object WP_User de l'utilisateur connecté.
     *
     * @return void
     */
    public function onLoginSuccess($user_login, $user);

    /**
     * Action lancée en cas de succès de deconnection.
     *
     * @return void
     */
    public function onLogoutSuccess();
}