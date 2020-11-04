<?php declare(strict_types=1);

namespace tiFy\Contracts\Form;

use tiFy\Contracts\View\Engine as ViewEngine;

interface FactoryResolver
{
    /**
     * Récupération de l'instance d'un addon
     *
     * @param string $name Nom de qualification de l'addon.
     *
     * @return AddonFactory
     */
    public function addon($name);

    /**
     * Récupération de l'instance du gestionnaire d'addons.
     *
     * @return FactoryAddons|AddonFactory[]
     */
    public function addons();

    /**
     * Récupération de l'instance du gestionnaire de boutons.
     *
     * @return FactoryButtons|ButtonController[]
     */
    public function buttons();

    /**
     * Récupération de l'instance du gestionnaire d'événements|déclenchement d'un événement.
     *
     * @param string $name Nom de qualification de l'événement.
     * @param array $args Liste des arguments complémentaires de déclenchement
     *
     * @return mixed|FactoryEvents
     */
    public function events($name = null, array $args = []);

    /**
     * Récupération de l'instance d'un champ.
     *
     * @param null|string $slug Identifiant de qualification du champ.
     *
     * @return FactoryField
     */
    public function field($slug = null);

    /**
     * Récupération de l'instance du gestionnaire de champs.
     *
     * @return FactoryFields|FactoryField[]
     */
    public function fields();

    /**
     * Récupération de l'instance du gestionnaire de formulaire.
     *
     * @return FormFactory
     */
    public function form();

    /**
     * Récupération de l'instance du gestionnaire de groupes.
     *
     * @return FactoryGroups|FactoryGroup[]
     */
    public function groups();

    /**
     * Récupération de l'instance du gestionnaire de traitement de requête de soumission.
     *
     * @return FactoryHandle
     */
    public function handle();

    /**
     * Récupération de l'instance du gestionnaire des messages de notification.
     *
     * @return FactoryNotices
     */
    public function notices();

    /**
     * Récupération d'une option|Liste complète des options.
     *
     * @param null|string $key Clé d'indice de l'option.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function option($key = null, $default = null);

    /**
     * Récupération de l'instance du gestionnaire d'options.
     *
     * @return FactoryOptions
     */
    public function options();

    /**
     * Résolution de service fourni.
     *
     * @param string $alias Nom de qualification du service.
     * @param array $args Liste des variables passées en argument au service.
     *
     * @return mixed
     */
    public function resolve($alias, $args = []);

    /**
     * Récupération de l'instance du gestionnaire de session.
     *
     * @return FactorySession
     */
    public function session();

    /**
     * Récupération de l'instance du gestionnaire.
     *
     * @return FactoryValidation
     */
    public function validation();

    /**
     * Récupération d'une instance du gestionnaire de gabarits d'affichage|Contenu du gabarit d'affichage.
     * {@internal
     *  - cas 1 : Aucun argument n'est passé à la méthode, retourne l'instance du controleur de gabarit d'affichage.
     *  - cas 2 : Rétourne le gabarit d'affichage en passant les variables en argument.
     * }
     *
     * @param null|string $view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewEngine|string
     */
    public function viewer(?string $view = null, array $data = []);
}