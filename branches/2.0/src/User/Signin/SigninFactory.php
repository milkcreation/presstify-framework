<?php

namespace tiFy\User\Signin;

use tiFy\Contracts\Kernel\Notices as NoticesContract;
use tiFy\Contracts\User\SigninFactory as SigninFactoryContract;
use tiFy\Contracts\View\ViewController;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Kernel\Notices\Notices;
use tiFy\Support\Arr;
use tiFy\Support\ParamsBag;

class SigninFactory extends ParamsBag implements SigninFactoryContract
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du gestionnaire de message de notification.
     * @var NoticesContract
     */
    protected $notices;

    /**
     * Instance du gestionnaire de gabarits d'affichage.
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du controleur.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs)
    {
        $this->name = $name;

        $this->set($attrs)->parse();

        $this->boot();
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return (string)$this->authForm();
    }

    /**
     * @inheritdoc
     */
    public function authForm()
    {
        return $this->viewer('auth/form', $this->all());
    }

    /**
     * @inheritdoc
     */
    public function addNotice($type, $message = '', $code = null, $datas = []): SigninFactoryContract
    {
        if ($code && $this->has("notices.{$code}")) {
            $message = $this->get("notices.{$code}");
        }

        $this->notices()->add($type, $message, $datas);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {

    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'auth'               => [],
            'logout_link'        => [],
            'lost_password_link' => [],
            'roles'              => [],
            'redirect_url'       => '',
            'attempt'            => -1,
            'notices'            => []
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAuthRedirectUrl(?string $redirect_url = null): string
    {
        if (!$redirect_url) {
            $redirect_url = $this->get('redirect_url', '');
        }

        return $redirect_url;
    }

    /**
     * @inheritdoc
     */
    public function getLogoutRedirectUrl(?string $redirect_url = null): string
    {
        if (!$redirect_url) {
            $redirect_url = $this->get('redirect_url', '');
        }

        return $redirect_url;
    }

    /**
     * @inheritdoc
     */
    public function getLogoutUrl(?string $redirect_url = null): string
    {
        $args = [];
        if ($redirect_url) {
            $args['redirect_to'] = $redirect_url;
        }
        $args['action'] = 'logout';
        $args['signin'] = $this->getName();

        $logout_url = add_query_arg($args, $this->get('redirect_url', site_url('wp-login.php', 'login')));
        $logout_url = wp_nonce_url($logout_url, 'signin-logout-' . $this->getName());

        return $logout_url;
    }

    /**
     * @inheritdoc
     */
    public function getMessages(string $type): array
    {
        return $this->notices()->getMessages($type);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getRoles(): array
    {
        return Arr::wrap($this->get('roles', []));
    }

    /**
     * @inheritdoc
     */
    public function handle()
    {
        if (!$signin = request()->get('signin', false)) {
            return null;
        }

        if ($signin !== $this->getName()) {
            return null;
        }

        switch ($action = request()->get('action', 'login')) {
            default :
                break;
            case 'login' :
                events()->trigger('user.signin.handle.login', [$this]);
                break;
            case 'logout' :
                events()->trigger('user.signin.handle.logout', [$this]);
                break;
        }
    }

    /**
     * @inheritdoc
     */
    public function hasRole($role): bool
    {
        if (!$roles =$this->getRoles()) {
            return true;
        } else {
            return !!array_intersect(Arr::wrap($role), $roles);
        }
    }

    /**
     * @inheritdoc
     */
    public function logoutLink($attrs = [])
    {
        $attrs = array_merge($this->get('logout_link', []), $attrs);
        $url = $this->getLogoutUrl($attrs['redirect']);

        return "<a href=\"{$url}\" title=\"{$attrs['title']}\" class=\"{$attrs['class']}\">{$attrs['text']}</a>";
    }

    /**
     * @inheritdoc
     */
    public function lostpasswordLink()
    {
        return $this->viewer('lostpassword-link', $this->all());
    }

    /**
     * @inheritdoc
     */
    public function notices(): NoticesContract
    {
        if (is_null($this->notices)) {
            $this->notices = new Notices();
        }

        return $this->notices;
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        $this->set('name', $this->getName());

        $this->set('auth.attrs.name', "Signin-auth--{$this->getName()}");
        $this->set('auth.attrs.method', 'post');
        $this->set('auth.attrs.action', '');

        if (!$this->has('auth.attrs.id')) {
            $this->set('auth.attrs.id', 'Signin-auth--' . $this->getName());
        }

        $this->set('auth.attrs.class', sprintf($this->get('auth.attrs.class', '%s'), 'Signin-auth'));

        if ($fields = $this->get('auth.fields', ['username', 'password', 'remember', 'submit'])) {
            foreach ($fields as $name => $attrs) {
                if (is_numeric($name)) {
                    $name = $attrs;
                    $attrs = [];
                }
                if (in_array($name, ['username', 'password', 'remember', 'submit'])) {
                    switch ($name) {
                        case 'username' :
                            $attrs = array_merge([
                                'label' => __('Identifiant', 'tify'),
                                'attrs' => [
                                    'id'           => 'Signin-authFieldUsername--' . $this->getName(),
                                    'placeholder'  => __('Renseignez votre identifiant', 'tify'),
                                    'size'         => 20,
                                    'autocomplete' => 'username',
                                ],
                            ], $attrs);

                            $attrs['name'] = 'log';

                            if ($label = Arr::get($attrs, 'label', [])) :
                                if (is_string($label)) {
                                    $attrs['label'] = ['content' => $label];
                                }
                                $attrs['label'] = array_merge([
                                    'attrs' => [
                                        'for'   => $attrs['attrs']['id'] ?? '',
                                        'class' => 'Signin-authFieldLabel Signin-authFieldLabel--username'
                                    ]
                                ], $attrs['label']);
                            endif;
                            break;

                        case 'password' :
                            $attrs = array_merge([
                                'label' => __('Mot de passe', 'tify'),
                                'attrs' => [
                                    'id'           => 'Signin-authFieldPassword--' . $this->getName(),
                                    'placeholder'  => __('Renseignez votre mot de passe', 'tify'),
                                    'size'         => 20,
                                    'autocomplete' => 'current-password',
                                ],
                            ], $attrs);

                            $attrs['name'] = 'pwd';

                            if ($label = Arr::get($attrs, 'label', [])) {
                                if (is_string($label)) {
                                    $attrs['label'] = ['content' => $label];
                                }
                                $attrs['label'] = array_merge([
                                    'attrs' => [
                                        'for'   => $attrs['attrs']['id'] ?? '',
                                        'class' => 'Signin-authFieldLabel Signin-authFieldLabel--password'
                                    ]
                                ], $attrs['label']);
                            }
                            break;

                        case 'remember' :
                            $attrs = array_merge([
                                'label' => __('Se souvenir de moi', 'tify'),
                                'attrs' => [
                                    'id'    => 'Signin-authFieldRemember--' . $this->getName(),
                                    'value' => 0,
                                ],
                            ], $attrs);

                            $attrs['name'] = 'remember';

                            if ($label = Arr::get($attrs, 'label', [])) {
                                if (is_string($label)) {
                                    $attrs['label'] = ['content' => $label];
                                }
                                $attrs['label'] = array_merge([
                                    'attrs' => [
                                        'for'   => $attrs['attrs']['id'] ?? '',
                                        'class' => 'Signin-authFieldLabel Signin-authFieldLabel--remember'
                                    ]
                                ], $attrs['label']);
                            }
                            break;

                        case 'submit' :
                            $attrs = array_merge([
                                'attrs' => [
                                    'id'    => 'Signin-authFieldSubmit--' . $this->getName(),
                                ],
                                'content' => __('Se connecter', 'tify'),
                                'type' => 'submit'
                            ], $attrs);

                            $attrs['name'] = 'submit';

                            break;
                    }
                }
                $this->set("auth.fields.{$name}", $attrs);
            }
        }
        $this->set('logout_link', array_merge([
            'redirect' => '',
            'text'     => __('Déconnection', 'tify'),
            'class'    => 'Signin-logoutLink',
            'title'    => __('se déconnecter', 'tify'),
        ], $this->get('logout_link', [])));

        $this->set('lost_password_link', array_merge([
            'redirect' => '',
            'content'  => __('Mot de passe oublié', 'tify'),
        ], $this->get('lost_password_link', [])));
    }

    /**
     * Instance du gestionnaire des gabarits d'affichage ou instance d'un gabarit d'affichage.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'intance du controleur principal.}
     * {@internal Sinon récupére le gabarit d'affichage et passe les variables en argument.}
     *
     * @param null|string view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewController|ViewEngine
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) {
            $this->viewer = view()->setDirectory(dirname(__DIR__) . '/Resources/views/signin')
                ->setController(SigninView::class)
                ->set('signin', $this);
        }

        if (func_num_args() === 0) {
            return $this->viewer;
        }

        return $this->viewer->make($view, $data);
    }
}