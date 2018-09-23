<?php

namespace tiFy\User\SignIn;

use Illuminate\Support\Str;
use tiFy\Contracts\User\UserSignInItemInterface;
use tiFy\Field\Field;
use tiFy\Partial\Partial;

class SignInItemController extends SignInHandleController implements UserSignInItemInterface
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
    public function defaults()
    {
        return [
            'form'               => [
                'fields' => [
                    'username', 'password', 'remember', 'submit'
                ],
                'lost_password_link' => true
            ],
            'logout_link'        => [],
            'lost_password_link' => [],
            'roles'              => [],
            'redirect_url'       => site_url('/'),
            'attempt'            => -1,
            'errors_map'         => [],
        ];
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
        return $this->view('form', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formAdditionnalFields()
    {
        return $this->view('form-additionnal_fields', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formAfter()
    {
        return $this->view('form-after', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formBefore()
    {
        return $this->view('form-before', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formBody()
    {
        return $this->view('form-body', $this->all());
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
            $content = "<ol>";
            foreach ($errors as $message) :
                $content .= "<li>{$message}</li>";
            endforeach;
            $content .= "</ol>";
        else :
            $content = reset($errors);
        endif;

        return Partial::Notice(
            [
                'attrs' => [
                    'class' => 'tiFySignIn-FormPart tiFySignIn-FormErrors'
                ],
                'content'  => $content,
                'type'  => 'error'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldPassword()
    {
        return $this->view('form-field_password', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldRemember()
    {
        return $this->view('form-field_remember', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldSubmit()
    {
        return $this->view('form-field_submit', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldUsername()
    {
        return $this->view('form-field_username', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formFooter()
    {
        return $this->view('form-footer', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formHeader()
    {
        return $this->view('form-header', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formHiddenFields()
    {
        return $this->view('form-hidden_fields', $this->all());
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
            $content = "<ol>";
            foreach ($infos as $message) :
                $content .= "<li>{$message}</li>";
            endforeach;
            $content .= "</ol>";
        else :
            $content = reset($infos);
        endif;

        return Partial::Notice(
            [
                'attrs' => [
                    'class' => 'tiFySignIn-FormPart tiFySignIn-FormInfos'
                ],
                'content'  => $content,
                'type'  => 'info'
            ]
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
        return $$this->view('lost-password-link', $this->all());
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

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->form();
    }
}