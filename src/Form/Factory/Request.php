<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FactoryRequest;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Kernel\Parameters\ParamsBagController;
use tiFy\Form\Factory\ResolverTrait;

class Request extends ParamsBagController implements FactoryRequest
{
    use ResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param FormFactory $form Instance du contrôleur de formulaire.
     *
     * @return void
     */
    public function __construct(FormFactory $form)
    {
        $this->form = $form;

        $this->events()->listen('request.handle', [$this, 'handle']);
    }

    /**
     * Traitement de la requête de soumission du formulaire.
     *
     * @return bool|void
     */
    public function handle()
    {
        $this->prepare();

        if (!wp_verify_nonce($this->get('csrf-token', ''), 'Form' . $this->form()->name())) :
            return false;
        endif;

        /** @var FactoryField $field */
        foreach($this->fields() as $name => $field) :
            $check = true;

            // Test d'intégrité de champ requis.
            if ($field->getRequired('check')) :
                $value = $this->get($field->getName());
                $test = app('form.factory.validation', [$this->form()]);

                if (!$check = $test->call($field->getRequired('call'), $value, $field->getRequired('args', []))) :
                    $this->notices()->add(
                        'error',
                        sprintf($field->getRequired('message'), $field->getTitle()),
                        [
                            'type'    => 'field',
                            'slug'    => $field->getSlug(),
                            'check'   => 'required'
                        ]
                    );
                endif;
            endif;

            // Tests d'integrités complémentaires.
            if ($check) :
                if ($validations = $field->get('validations', [])) :
                    $value = $this->get($field->getName());
                    $test = app('form.factory.validation', [$this->form()]);

                    foreach ($validations as $validation) :
                        if (!$test->call($validation['call'], $value, $validation['args'])) :
                            $this->notices()->add(
                                'error',
                                sprintf($validation['message'], $field->getTitle()),
                                [
                                    'type'    => 'field',
                                    'slug'    => $field->getSlug()
                                ]
                            );
                        endif;
                    endforeach;
                endif;
            endif;
        endforeach;

        $this->events('request.handle.validate', [&$this]);

        if ($this->notices()->has('error')) :
            return;
        endif;

        $this->events('request.success', [&$this]);

        // Redirection après le traitement
        $redirect = add_query_arg(
            $this->_getRedirectQueryArgs(),
            $this->getGlobalVar('_wp_http_referer', home_url('/'))
        );

        $this->events('request.redirect', [&$redirect]);

        if ($redirect) :
            wp_redirect($redirect);
            exit;
        endif;
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
        /** @var FieldItemController $field */
        foreach ($fields as $field) :
            $field_errors = [];


            // Court-circuitage de la vérification d'intégrité d'un champ
            $this->getController()->checkQueryVar($field, $field_errors);
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
     *
     */
    public function prepare()
    {
        $this->parse(call_user_func([request(), $this->form()->getMethod()]));
    }
}