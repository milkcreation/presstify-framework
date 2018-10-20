<?php

namespace tiFy\User\SignUp;

use tiFy\Contracts\Form\Form;
use tiFy\Contracts\User\UserSignUpItemInterface;
use tiFy\Form\Forms\FormBaseController;
use tiFy\Kernel\Parameters\AbstractParametersBag;

class SignUpItemController extends AbstractParametersBag implements UserSignUpItemInterface
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        add_action(
            'init',
            function () {
                return form()->add('UserSignUp-' . $this->getName(), $this->all());
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->form();
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'fields' => [
                'login' => [
                    'label'        => __('Identifiant', 'tify'),
                    'type'         => 'input',
                    'required'     => true,
                    'addons'       => [
                        'user'  => [
                            'userdata' => 'user_login'
                        ]
                    ]
                ],
                'email' => [
                    'label'        => __('Adresse e-mail', 'tify'),
                    'attrs'        => [
                        'placeholder'  => __('Indiquez votre e-mail', 'tify'),
                    ],
                    'type'         => 'input',
                    'integrity_cb' => 'is_email',
                    'required'     => true,
                    'addons'       => [
                        'user'  => [
                            'userdata' => 'user_email'
                        ]
                    ]
                ],
                'password' => [
                    'label'        => __('Mot de passe', 'tify'),
                    'attrs'        => [
                        'placeholder'  => __('Renseignez un mot de passe', 'tify'),
                    ],
                    'type'         => 'password',
                    'integrity_cb' => 'is_valid_password',
                    'required'     => true,
                    'addons'       => [
                        'user'  => [
                            'userdata' => 'user_pass'
                        ]
                    ]
                ],
                'confirm' => [
                    'label'        => __('Confirmation de mot de passe', 'tify'),
                    'attrs'        => [
                        'placeholder'  => __('Confirmez votre mot de passe', 'tify'),
                    ],
                    'type'         => 'password',
                    'integrity_cb' => [
                        'cb' => 'compare',
                        'args'     => ['password'],
                        'message'    => __(
                            'Les champs "Mot de passe" et "Confirmation de mot de passe" doivent correspondre',
                            'tify'
                        )
                    ],
                    'required'     => true
                ],
                'captcha' => [
                    'label'        => __('Code de sécurité', 'tify'),
                    'type'         => 'captcha',
                    'required'     => true
                ]
            ],
            'addons' => ['user' => true]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function form()
    {
        return form()->get('UserSignUp-' . $this->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}