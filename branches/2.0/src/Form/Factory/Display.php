<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\FactoryDisplay;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\ResolverTrait;

class Display implements FactoryDisplay
{
    use ResolverTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @param FormFactory $form Instance du contrôleur de formulaire associé.
     *
     * @return void
     */
    public function __construct(FormFactory $form)
    {
        $this->form = $form;
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * Rendu de l'affichage.
     *
     * @return string
     */
    public function render()
    {
        $fields = $this->fields();
        $buttons = $this->buttons();
        $errors = $this->errors();

        return $this->form()->viewer('form', compact('errors', 'fields', 'buttons'));
    }

    /**
     * Traitement de la liste des attributs de configuration.
     * @todo Fractionner le traitement pour une meilleur lisibilité
     *
     * @return array
     */
    public function parse()
    {
        // Contenu
        if ($wrapper_attrs = $this->getForm()->get('wrapper')) :
            if(! is_array($wrapper_attrs)) :
                $wrapper_attrs = [
                    'attrs' => [
                        'id'    => 'tiFyForm-Wrapper--' . $this->getForm()->getUid(),
                        'class' => 'tiFyForm-Wrapper'
                    ]
                ];
            endif;

            $wrapper_attrs = array_merge(['tag' => 'div'], $wrapper_attrs);
            $wrapper_attrs['content'] = [$this, 'wrapper'];

            $wrapper = Partial::Tag($wrapper_attrs);
        else :
            $wrapper = $this->wrapper();
        endif;

        // Messages de notification
        if ($this->getHandle()->isSuccessful()) :
            $notices = Partial::Tag(
                [
                    'tag' => 'div',
                    'attrs' => [
                        'class' => 'tiFyForm-Notices tiFyForm-Notices--success'
                    ],
                    'content' => $this->getNotices()->display('success')
                ]
            );
        elseif($this->hasError()) :
            $notices = Partial::Tag(
                [
                    'tag' => 'div',
                    'attrs' => [
                        'class' => 'tiFyForm-Notices tiFyForm-Notices--error'
                    ],
                    'content' => $this->getNotices()->display('error')
                ]
            );
        else :
            $notices = '';
        endif;

        // Pré-Affichage du formulaire
        $form_before = $this->getForm()->get('before', '');

        // Post-Affichage du formulaire
        $form_after = $this->getForm()->get('after', '');

        // Formulaire
        if ($form_attrs = $this->getForm()->get('attrs', [])) :
            if(! is_array($form_attrs)) :
                $form_attrs = [
                    'id'    => 'tiFyForm-Content--' . $this->getForm()->getUid(),
                    'class' => 'tiFyForm-Content'
                ];
            endif;
        endif;

        Arr::set($form_attrs, 'method', $this->getForm()->getMethod());

        $action_link = remove_query_arg('success', $this->getForm()->getAction());
        $action = ($anchor = $this->getForm()->getOption('anchor')) ? \add_query_arg("#{$anchor}", $action_link) : $action_link;
        Arr::set($form_attrs, 'action', $action);

        if ($enctype = $this->getForm()->get('enctype')) :
            Arr::set($form_attrs, 'enctype', $enctype);
        endif;

        $form_content = Partial::Tag(
            [
                'tag' => 'form',
                'attrs' => $form_attrs,
                'content' => [$this, 'form_content']
            ]
        );

        // Contenu
        if($this->getHandle()->isSuccessful()) :
            if ($success_cb = $this->getForm()->getOption('success_cb')) :
                if ($callback === 'form') :
                    $content = $form_content;
                elseif (is_callable($callback)) :
                    $content = call_user_func_array($callback, [$this->getForm()]);
                endif;
            else :
                $content = '';
            endif;
        else :
            $content = $form_content;
        endif;

        // Liste des champs cachés
        $hidden_fields = wp_nonce_field('submit_' . $this->getForm()->getUid(), $this->getForm()->getNonce(), true, false);
        if ($session = $this->getSession()) :
            $hidden_fields .= Field::Hidden(['name' => 'session_' . $this->getForm()->getUid(), 'value' => esc_attr($session)]);
        endif;

        // Liste des boutons
        $buttons_content = '';
        foreach ($this->getForm()->buttons() as $button) :
            $buttons_content .= $button->display();
        endforeach;
        $buttons = Partial::Tag(
            [
                'tag' => 'div',
                'attrs' => ['class' => 'tiFyForm-Buttons'],
                'content' => $buttons_content
            ]
        );

        $this->attributes = compact('wrapper', 'notices', 'content', 'form_before', 'form_after', 'hidden_fields', 'buttons');
    }
}