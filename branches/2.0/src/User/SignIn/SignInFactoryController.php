<?php

namespace tiFy\User\SignIn;

use Illuminate\Support\Str;
use tiFy\Field\Field;
use tiFy\Components\Tools\Notices\NoticesAwareTrait;
use tiFy\Partial\Partial;

class SignInFactoryController extends SignInHandleController implements SignInControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $username, $password)
    {
        if (!is_wp_error($user) && ($roles = $this->getRoles()) && !array_intersect($user->roles, $this->getRoles())) :
            $user = new \WP_Error('role_not_allowed');
        endif;

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormRedirect($redirect_url = '', $user)
    {
        if (!$redirect_url) :
            $redirect_url = $this->get('redirect_url', admin_url());
        endif;

        return $redirect_url;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoutRedirect($redirect_url = '', $user)
    {
        return $redirect_url;
    }

    /**
     * {@inheritdoc}
     */
    public function form()
    {
        $name = $this->get('form.name', "tiFySignIn-Form--{$this->getName()}");
        $id = $this->get('form.id', "tiFySignIn-Form--{$this->getName()}");
        $class = $this->get('form.class', 'tiFySignIn-Form');

        $output  = "";

        $output .= $this->formBefore();

        $output .= "<form name=\"{$name}\" id=\"{$id}\" class=\"{$class}\" action=\"\" method=\"post\">";

        // Champs cachés (requis)
        $output .= (string)Field::Hidden(
            [
                'name' => 'tiFySignIn',
                'value' => $this->getName()
            ]
        );
        $output .= (string)Field::Hidden(
            [
                'name' => '_wpnonce',
                'value' => \wp_create_nonce('tiFySignIn-in-' . $this->getName())
            ]
        );

        // Entête du formulaire
        $output .= $this->formHeader();

        // Corps du formulaire (champs de saisie)
        $output .= $this->formBody();

        // Pied du formulaire
        $output .= $this->formFooter();

        // Fermeture du formulaire
        $output .= "</form>";

        // post-affichage du formulaire
        $output .= $this->formAfter();

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function formAdditionnalFields()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function formAfter()
    {
        return  '';
    }

    /**
     * {@inheritdoc}
     */
    public function formBefore()
    {
        return  '';
    }

    /**
     * {@inheritdoc}
     */
    public function formBody()
    {
        $output = "";

        // Champs cachés
        $output .= $this->formHiddenFields();

        // Champs requis
        foreach (['username', 'password'] as $field_name) :
            $output .= call_user_func([$this, 'formField' . Str::studly($field_name)]);
        endforeach;

        // Champs de formulaire additionnels
        $output .= $this->formAdditionnalFields();

        // Mémorisation des informations de connection
        $output .= $this->formFieldRemember();

        // Bouton de soumission filtré
        $output .= $this->formFieldSubmit();

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function formErrors()
    {
        if(!$errors = $this->getErrors()) :
            return '';
        endif;

        if (count($errors)>1) :
            $text = "<ol>";
            foreach ($errors as $message) :
                $text .= "<li>{$message}</li>";
            endforeach;
            $text .= "</ol>";
        else :
            $text = reset($errors);
        endif;

        return Partial::Notice(
            [
                'class' => "tiFySignIn-FormPart tiFySignIn-FormErrors",
                'text'  => $text,
                'type'  => 'error'
            ],
            false
        );
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldPassword()
    {
        if(!$attrs = $this->getFieldAttrs('password', false)) :
            return '';
        endif;

        $output  = "";
        $output .= "<p class=\"tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--password\">";

        if ($attrs['label']) :
            $label = [];
            $label['content'] = $attrs['label'];
            $label['attrs'] = [];
            if (isset($attrs['attrs']['id'])) :
                $label['attrs']['for'] = $attrs['attrs']['id'];
            endif;
            $label['attrs']['class'] = 'tiFySignIn-FormFieldLabel tiFySignIn-FormFieldLabel--password';
            $output .= Field::Label($label);
        endif;

        $output .= Field::Password($attrs);
        $output .= "</p>";

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldRemember()
    {
        if(!$attrs = $this->getFieldAttrs('remember', false)) :
            return '';
        endif;

        $output  = "";
        $output .= "<p class=\"tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--remember\">";

        $output .= Field::Checkbox($attrs);

        if ($attrs['label']) :
            $label = [];
            $label['content'] = $attrs['label'];
            $label['attrs'] = [];
            if (isset($attrs['attrs']['id'])) :
                $label['attrs']['for'] = $attrs['attrs']['id'];
            endif;
            $label['attrs']['class'] = 'tiFySignIn-FormFieldLabel tiFySignIn-FormFieldLabel--remember';
            $output .= Field::Label($label);
        endif;

        $output .= "</p>";

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldSubmit()
    {
        if(!$attrs = $this->getFieldAttrs('submit', false)) :
            return '';
        endif;

        $output  = "";
        $output .= "<p class=\"tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--submit\">";
        $output .= Field::Submit($attrs);
        $output .= "</p>";

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldUsername()
    {
        if(!$attrs = $this->getFieldAttrs('username', false)) :
            return '';
        endif;

        $output  = "";
        $output .= "<p class=\"tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--username\">";

        if ($attrs['label']) :
            $label = [];
            $label['content'] = $attrs['label'];
            $label['attrs'] = [];
            if (isset($attrs['attrs']['id'])) :
                $label['attrs']['for'] = $attrs['attrs']['id'];
            endif;
            $label['attrs']['class'] = 'tiFySignIn-FormFieldLabel tiFySignIn-FormFieldLabel--username';
            $output .= Field::Label($label);
        endif;

        $output .= Field::Text($attrs);
        $output .= "</p>";

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function formFooter()
    {
        return $this->lostpasswordLink();
    }

    /**
     * {@inheritdoc}
     */
    public function formHeader()
    {
        return $this->formErrors();
    }

    /**
     * {@inheritdoc}
     */
    public function formHiddenFields()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function formInfos()
    {
        if(!$infos = $this->noticesGetMessages('info')) :
            return '';
        endif;

        if (count($infos)>1) :
            $text = "<ol>";
            foreach ($infos as $message) :
                $text .= "<li>{$message}</li>";
            endforeach;
            $text .= "</ol>";
        else :
            $text = reset($infos);
        endif;

        return Partial::Notice(
            [
                'class' => "tiFySignIn-FormPart tiFySignIn-FormInfos",
                'text'  => $text,
                'type'  => 'info'
            ],
            false
        );
    }

    /**
     * {@inheritdoc}
     */
    public function logoutLink($attrs = [])
    {
        $attrs = array_merge(
            $this->get('logout_link', []),
            $attrs
        );
        $url = $this->getLogoutUrl($attrs['redirect']);

        return "<a href=\"{$url}\" title=\"{$attrs['title']}\" class=\"{$attrs['class']}\">{$attrs['text']}</a>";
    }

    /**
     * {@inheritdoc}
     */
    public function lostpasswordLink()
    {
        $attrs = $this->get('lost_password_link');

        $output =   "<a href=\"" . \wp_lostpassword_url($attrs['redirect']) ."\"" .
            " title=\"" . __( 'Récupération de mot de passe perdu', 'tify' ) . "\"" .
            " class=\"tiFySignIn-LostPasswordLink\">" .
            $attrs['text'] .
            "</a>";

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function onLoginSuccess($user_login, $user)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess()
    {
        return;
    }
}