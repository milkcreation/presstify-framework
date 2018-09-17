<?php

namespace tiFy\User\SignUp;

use tiFy\Form\Form;
use tiFy\Form\Forms\FormBaseController;
use tiFy\Kernel\Item\AbstractItemController;

class SignUpItemController extends AbstractItemController
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        add_action(
            'tify_form_register',
            function ($formController) {
                /** @var Form $formController */
                return $formController->register(
                    'tiFyCore-userSignUp--' . $this->getName(),
                    $this->all()
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'fields' => [
                [
                    'slug'         => 'login',
                    'label'        => __('Identifiant', 'tify'),
                    'type'         => 'input',
                    'required'     => true,
                    'addons'       => [
                        'user'  => [
                            'userdata' => 'user_login'
                        ]
                    ]
                ],
                [
                    'slug'         => 'email',
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
                [
                    'slug'         => 'password',
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
                [
                    'slug'         => 'confirm',
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
                [
                    'slug'         => 'captcha',
                    'label'        => __('Code de sécurité', 'tify'),
                    'type'         => 'simple-captcha-image',
                    'required'     => true
                ]
            ],
            'addons' => ['user' => true]
        ];
    }

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Affichage du formulaire.
     *
     * @return string
     */
    public function form()
    {
        return app(Form::class)->display('tiFyCore-userSignUp--' . $this->getName());
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