<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FactoryRequest;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Form\FactoryValidation;
use tiFy\Kernel\Params\ParamsBag;

class Request extends ParamsBag implements FactoryRequest
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

        $this->events()->listen('form.set.current', [$this, 'handle'], -999999);

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->prepare();

        if (!wp_verify_nonce($this->get('_token', ''), 'Form' . $this->form()->name())) :
            return false;
        endif;

        $this->form()->prepare();

        $this->events('request.handle');

        /**
         * Validations.
         * @var FactoryField $field
         */
        foreach($this->fields() as $name => $field) :
            $check = true;

            $field->setValue($this->get($field->getName()));

            // Validation de champ requis.
            if ($field->getRequired('check')) :
                $value = $field->getValue($field->getRequired('raw', true));

                /** @var FactoryValidation $valid */
                $valid = app('form.factory.validation', [$this->form()]);

                if (!$check = $valid->call($field->getRequired('call'), $value, $field->getRequired('args', []))) :
                    $this->notices()->add(
                        'error',
                        sprintf($field->getRequired('message'), $field->getTitle()),
                        [
                            'type'    => 'field',
                            'field'   => $field->getSlug(),
                            'test'    => 'required'
                        ]
                    );
                endif;
            endif;

            // Validations complémentaires.
            if ($check) :
                if ($validations = $field->get('validations', [])) :
                    $value = $field->getValue($field->getRequired('raw', true));

                    /** @var FactoryValidation $valid */
                    $valid = app('form.factory.validation', [$this->form()]);

                    foreach ($validations as $validation) :
                        if (!$valid->call($validation['call'], $value, $validation['args'])) :
                            $this->notices()->add(
                                'error',
                                sprintf($validation['message'], $field->getTitle()),
                                [
                                    'field'   => $field->getSlug()
                                ]
                            );
                        endif;
                    endforeach;
                endif;
            endif;

            $this->events('request.validation.field.' . $field->getType(), [&$field]);
            $this->events('request.validation.field', [&$field]);
        endforeach;

        $this->events('request.validation', [&$this]);

        if ($this->notices()->has('error')) :
            $this->resetFields();

            return null;
        endif;

        $this->events('request.submit', [&$this]);

        if ($this->notices()->has('error')) :
            $this->resetFields();

            return null;
        endif;

        $this->events('request.success', [&$this]);

        $redirect = add_query_arg(
            [
                'success' => $this->form()->name()
            ],
            $this->get(
                '_http_referer',
                request()->server('HTTP_REFERER')
            )
        );

        $this->events('request.redirect', [&$redirect]);

        if ($redirect) :
            wp_redirect($redirect);
            exit;
        endif;

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->parse(call_user_func([request(), $this->form()->getMethod()]));
    }

    /**
     * {@inheritdoc}
     */
    public function resetFields()
    {
        foreach($this->fields() as $field) :
            if (!$field->supports('transport')) :
                $field->resetValue();
            endif;
        endforeach;
    }
}