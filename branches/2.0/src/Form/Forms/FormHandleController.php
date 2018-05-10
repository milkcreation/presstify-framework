<?php

namespace tiFy\Form\Forms;

use Illuminate\Support\Arr;
use tiFy\Form\Fields\FieldIntegrityCheckController;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Forms\FormItemController;

class FormHandleController extends AbstractCommonDependency
{
    /**
     * Liste des variables de requête globales.
     * @var array
     */
    private $globalQueryVars = [];

    /**
     * Liste des variables de requête en correspondance avec les champs du formulaire.
     * @var array
     */
    private $fieldQueryVars = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param FormItemController $Form Classe de rappel du controleur de formulaire associé.
     *
     * @return void
     */
    public function __construct(FormItemController $form)
    {
        parent::__construct($form);

        $this->_parseGlobalQueryVars();
    }

    /**
     * Traitement de la requête de soumission du formulaire.
     *
     * @return bool|void
     */
    public function proceed()
    {
        // Bypass
        if (!$nonce = $this->getGlobalVar($this->getForm()->getNonce())) :
            return false;
        endif;

        /// Provenance de la soumission du formulaire
        if (!\wp_verify_nonce($nonce, 'submit_' . $this->getForm()->getUid())) :
            \wp_die(
                __( '<h2>Erreur lors de la vérification d\'origine de la soumission de formulaire</h2>' .
                    '<p>Impossible de déterminer l\'origine de la soumission de votre formulaire.</p>',
                    'tify'
                ),
                __('Erreur de soumission de formulaire', 'tify'),
                500
            );
        endif;

        // Définition de la session
        $this->getTransport()->initSession();

        /// Vérification de la validité de la session existante
        if (!$this->getTransport()->getTransient()) :
            \wp_die(
                __( '<h2>Erreur lors de la soumission du formulaire</h2>' .
                    '<p>Votre session de soumission de formulaire est invalide ou arrivée à expiration</p>',
                    'tify'
                ),
                500
            );
        endif;

        // Traitement des variables de requête
        if (!$this->_parseQueryVars()) :
            return;
        endif;

        // Vérification d'intégrité des champs de formulaire
        if (!$this->_checkFieldQueryVars()) :
            return;
        endif;

        // Court-cicuitage du traitement de la requête
        $this->call('handle_submit_request', [&$this]);

        // Affichage du formulaire et des erreurs suite au traitement de la requête
        if ($this->hasError()) :
            return;
        endif;

        // Court-cicuitage du traitement avant la redirection
        if (!$this->_setSuccess()) :
            return;
        endif;

        // Post traitement avant la redirection
        $this->call('handle_successfully', [&$this]);

        // Redirection après le traitement
        $redirect = add_query_arg(
            $this->_getRedirectQueryArgs(),
            $this->getGlobalVar('_wp_http_referer', home_url('/'))
        );

        // Court-cicuitage de la redirection
        $this->call('handle_redirect', [&$redirect]);

        if ($redirect) :
            wp_redirect($redirect);
            exit;
        endif;
    }

    /**
     * Traitement des variables de requête globale.
     *
     * @return array
     */
    private function _parseGlobalQueryVars()
    {
        $method = $this->getForm()->get('method', '');
        switch ($method) :
            default :
            case 'request' :
                $this->globalQueryVars = $this->appRequest()->all();
                break;
            case 'get' :
            case 'post' :
                $this->globalQueryVars = $this->appRequest($method)->all();
                break;
        endswitch;
    }

    /**
     * Traitement des variables de requête.
     *
     * @return bool
     */
    private function _parseQueryVars()
    {
        $values = $this->getGlobalVar($this->getForm()->getUid());
        $fields = $this->getFields();
        $vars = [];

        foreach ($fields as $field) :
            if (!$field->support('request')) :
                continue;
            endif;

            $vars[$field->getSlug()] = null;

            $value = (isset($values[$field->getName()])) ? $values[$field->getName()] : null;

            // Court-circuitage de la définition de la valeur du champ
            $vars[$field->getSlug()] = $this->getController()->parseQueryVar($field->getSlug(), $value);
            $this->call('handle_parse_query_field_value', [&$value, $field, $this]);

            $field->setValue($vars[$field->getSlug()]);
        endforeach;

        $this->fieldQueryVars = $vars;

        // Court-circuitage de la définition des valeurs de champ
        $this->call('handle_parse_query_fields_vars', [&$this->fieldQueryVars, $fields, $this]);

        foreach ($fields as $field) :
            if (!$field->support('request')) :
                continue;
            endif;

            $field->setValue($this->fieldQueryVars[$field->getSlug()]);
        endforeach;

        return !empty($this->fieldQueryVars);
    }

    /**
     * Traitement de vérification des variables de requête des champs de formulaire.
     *
     * @return bool
     */
    private function _checkFieldQueryVars()
    {
        $errors = [];
        $fields = $this->getFields();

        // Vérification des variables de saisie du formulaire.
        foreach ($fields as $field) :
            $field_errors = [];

            // Test d'intégrité de champs requis
            if ($required = $field->isRequired()) :
                if ($required['active']) :
                    $CheckController = new FieldIntegrityCheckController($field);

                    if (!$CheckController->check($field->getValue(true), $required['cb'], $callback['args'])) :
                        $field_errors[] = [
                            'message' => sprintf($required['message'], $field->getLabel()),
                            'type'    => 'field',
                            'slug'    => $field->getSlug(),
                            'check'   => 'required',
                            'order'   => $field->getOrder()
                        ];
                    endif;
                endif;
            endif;

            if ($field_errors) :
                continue;
            endif;

            // Tests d'integrité
            if ($callbacks = $field->getIntegrityCallbacks()) :
                $CheckController= new FieldIntegrityCheckController($field);

                foreach ($callbacks as $callback) :
                    if (!$CheckController->check($field->getValue(true), $callback['cb'], $callback['args'])) :
                        $field_errors[] = [
                            'message' => sprintf($callback['message'], $field->getLabel(), $field->getValue()),
                            'type'    => 'field',
                            'slug'    => $field->getSlug(),
                            'check'   => $callback,
                            'order'   => $field->getOrder()
                        ];
                    endif;
                endforeach;
            endif;

            // Court-circuitage de la vérification d'intégrité d'un champ
            $field_errors = $this->getController()->checkQueryVar($field, $field_errors);
            $this->call('handle_check_field', [&$field_errors, $field]);

            if (!empty($field_errors)) :
                foreach ($field_errors as $field_error) :
                    $errors[] = $field_error;
                endforeach;
            endif;
        endforeach;

        // Court-circuitage de la vérification d'intégrité des champs
        $this->call('handle_check_fields', [&$errors, $fields]);

        // Traitement des erreurs
        foreach ($errors as $error) :
            if (is_string($error)) :
                $this->addError($error);
            else :
                $data = array_merge(
                    [
                        'message' => '',
                        'type'    => 'field',
                    ],
                    $error
                );
                $message = $data['message'];
                unset($data['message']);

                $this->addError($message, $data);
            endif;
        endforeach;

        if ($this->hasError()) :
            return false;
        else :
            return true;
        endif;
    }

    /**
     * Définition de l'indicateur de soumission réussie.
     *
     * @return bool
     */
    private function _setSuccess()
    {
        $this->globalQueryVars['success'] = $this->getSession();

        return $this->getTransport()->updateTransient(['success' => true]);
    }

    /**
     * Récupération des variables de requête de l'url de redirection du formulaire.
     *
     * @return array
     */
    private function _getRedirectQueryArgs()
    {
        return ['success' => $this->getSession()];
    }

    /**
     * Récupération de la liste des variables de requête globale.
     *
     * @return array
     */
    public function allGlobalVars()
    {
        return $this->globalQueryVars;
    }

    /**
     * Récupération d'une variable de requête globale.
     *
     * @param string $key Clé d'indexe de la vaiable à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getGlobalVar($key, $default = null)
    {
        return Arr::get($this->globalQueryVars, $key, $default);
    }

    /**
     * Récupération de la liste des variables de requête des champs de formulaire.
     *
     * @return array
     */
    public function allFieldVars()
    {
        return $this->fieldQueryVars;
    }

    /**
     * Récupération d'une variable de requête de champ de formulaire.
     *
     * @param string $key Clé d'indexe de la vaiable à récupérer.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getFieldVar($key, $default = '')
    {
        return Arr::get($this->fieldQueryVars, $key, $default);
    }

    /**
     * Vérifie si un formulaire a été soumis avec succès
     *
     * @return bool
     */
    public function isSuccessful()
    {
        if (!$transient = get_transient($this->getTransport()->getTransientPrefix() . $this->getGlobalVar('success'))) :
            return false;
        endif;

        if ($transient['ID'] != $this->getForm()->getName()) :
            return false;
        endif;

        return (!empty($transient['success']) && $transient['success']);
    }
}