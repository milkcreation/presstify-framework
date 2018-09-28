<?php

namespace tiFy\User\SignIn;

use Illuminate\Support\Str;
use tiFy\Contracts\User\UserSignInItemInterface;

class SignInItemController extends SignInHandleController implements UserSignInItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->form();
    }

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
        return $this->viewer('form', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formAdditionnalFields()
    {
        return $this->viewer('form-additionnal_fields', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formAfter()
    {
        return $this->viewer('form-after', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formBefore()
    {
        return $this->viewer('form-before', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formBody()
    {
        return $this->viewer('form-body', $this->all());
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

        return partial(
            'notice',
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
        return $this->viewer('form-field_password', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldRemember()
    {
        return $this->viewer('form-field_remember', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldSubmit()
    {
        return $this->viewer('form-field_submit', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formFieldUsername()
    {
        return $this->viewer('form-field_username', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formFooter()
    {
        return $this->viewer('form-footer', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formHeader()
    {
        return $this->viewer('form-header', $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function formHiddenFields()
    {
        return $this->viewer('form-hidden_fields', $this->all());
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

        return partial(
            'notice',
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
        return $this->viewer('lost-password-link', $this->all());
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