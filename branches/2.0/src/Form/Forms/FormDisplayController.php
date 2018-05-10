<?php

namespace tiFy\Form\Forms;

use Illuminate\Support\Arr;
use tiFy\Field\Field;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Forms\FormItemController;
use tiFy\Partial\Partial;

class FormDisplayController extends AbstractCommonDependency
{
    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [];

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

        $this->parse();
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut à récupérer.
     * @param mixed $defaul Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Rendu de l'affichage.
     *
     * @return string
     */
    public function render()
    {
        $output = "";

        // Court-circuitage post-affichage
        $this->call('form_before_display', [&$output, $this]);

        $output .= $this->get('wrapper');

        // Court-circuitage post-affichage
        $this->call('form_after_display', [&$output, $this]);

        return $output;
    }

    /**
     * Encapsulation du contenu.
     *
     * @return string
     */
    public function wrapper()
    {
        $output = "";

        $output .= $this->get('notices');
        $output .= $this->get('content');

        return $output;
    }

    /**
     * Affichage du formulaire de soumission.
     *
     * @return string
     */
    public function form_content()
    {
        $output = "";
        $output .= $this->get('form_before');

        $output .= $this->header();
        $output .= $this->body();
        $output .= $this->footer();

        $output .= $this->get('form_after');

        return $output;
    }

    /**
     * Affichage du formulaire de soumission.
     *
     * @return string
     */
    public function form()
    {
        $output = "";
        $output .= $this->header();
        $output .= $this->body();
        $output .= $this->footer();

        return $output;
    }

    /**
     * Entête du formulaire.
     *
     * @return string
     */
    public function header()
    {
        $output = "";

        $output .= $this->get('hidden_fields');

        return $output;
    }

    /**
     * Corps de page du formulaire.
     *
     * @return string
     */
    public function body()
    {
        $output = "";

        // Affichage des champs de formulaire
        $output .= "\t\t<div class=\"tiFyForm-Fields\">\n";

        foreach ($this->getFields() as $n => $field) :
            /**
            if (is_null($this->fieldsGroup)) :
            $this->fieldsGroup = $field->get('group');
            $output .= "\t\t\t<div class=\"tiFyForm-FieldsGroup tiFyForm-FieldsGroup--{$this->FieldsGroup}\">";
            elseif ($this->fieldsGroup < $field->getAttr('group')) :
            $this->fieldsGroup = $field->getAttr('group');
            $output .= "\t\t\t</div>";
            $output .= "\t\t\t<div class=\"tiFyForm-FieldsGroup tiFyForm-FieldsGroup--{$this->FieldsGroup}\">";
            endif;
             */
            $output .= $field->display();

            /**
            if (count($this->fields()) - 1 === $n) :
            $output .= "\t\t\t</div>";
            endif;
             */
        endforeach;
        $output .= "\t\t</div>";

        return $output;
    }

    /**
     * Pied de page du formulaire.
     *
     * @return string
     */
    public function footer()
    {
        $output = "";

        $output .= $this->get('buttons');

        return $output;
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
                        'id'    => 'tiFyForm-wrapper--' . $this->getForm()->getUid(),
                        'class' => 'tiFyForm-wrapper'
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
        $notices_args = ['tag' => 'div'];
        if ($this->getHandle()->isSuccessful()) :
            $notices = Partial::Tag(
                [
                    'tag' => 'div',
                    'attrs' => [
                        'class' => 'tiFyForm-notices tiFyForm-notices--success'
                    ],
                    'content' => $this->getNotices()->display('success')
                ]
            );
        elseif($this->getNotices()->has('error')) :
            $notices = Partial::Tag(
                [
                    'tag' => 'div',
                    'attrs' => [
                        'class' => 'tiFyForm-notices tiFyForm-notices--error'
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
                    'id'    => 'tiFyForm-content--' . $this->getForm()->getUid(),
                    'class' => 'tiFyForm-content'
                ];
            endif;
        endif;

        Arr::set($form_attrs, 'method', $this->getForm()->getMethod());

        $action_link = remove_query_arg('success');
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
                'attrs' => ['class' => 'tiFyForm-buttons'],
                'content' => $buttons_content
            ]
        );

        $this->attributes = compact('wrapper', 'notices', 'content', 'form_before', 'form_after', 'hidden_fields', 'buttons');
    }

    /**
     * Récupération de l'affichage du formulaire depuis l'instance.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}