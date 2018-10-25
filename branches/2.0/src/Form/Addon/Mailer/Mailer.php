<?php

namespace tiFy\Form\Addon\Mailer;

use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\AddonController;

class Mailer extends AddonController
{
    /**
     * Définition des options de formulaire par défaut
     * @var array {
     * @var bool|array $admin {
     *          Affichage de l'intitulé et de la valeur de saisie du champ dans le corps du mail
     *
     * @var string $id Identifiant qualificatif. Utilisé notamment pour determiner les noms d'enregistrement des options en base de données
     * @var bool $confirmation Activation de l'interface d'administration de l'email de confirmation de reception à destination des utilisateurs
     * @var bool $notification Activation de l'interface d'administration de l'email de notification à destination des administrateurs de site
     *      }
     * @var bool|array $confirmation {
     *          Attributs de configuration d'expédition de l'email de confirmation de reception à destination des utilisateurs
     * @see tFyLibMailer
     *      }
     * @var bool|array $notification {
     *          Attributs de configuration d'expédition de l'email de notification à destination des administrateurs de site
     * @see tFyLibMailer
     *      }
     * @var string $option_name_prefix Prefixe du nom d'enregistrement des options d'expédition de mail
     * @var array $option_names {
     *          Cartographie d'enregistrement de options.
     *      }
     * @var string|array $template {
     *              Chemin relatif du theme courant vers le gabarit du mail
     *
     * @var slug Chemin absolu vers le nom du fichier de gababit (hors extension)
     * @var $name Modifieur de nom de gabarit
     * @var $args Liste d'arguments complémentaires pass"s dans le gabarit
     *      }
     * @var bool|string $debug Affichage du mail au lieu de l'expédition (false|'confirmation'|'notification')
     * }
     */
    public $attributes = [
        'admin'              => [
            'confirmation' => true,
            'notification' => true,

        ],
        'confirmation'       => [
            'to' => '%%email%%',
        ],
        'notification'       => [],
        'option_name_prefix' => '',
        'option_names'       => [
            'confirmation' => '',
            'notification' => '',
            'sender'       => '',
            'recipients'   => '',
        ],
        'template'           => [],
        'debug'              => false,
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        return;

        $this->events()
            ->listen('request.validation', [$this, 'onRequestValidation'])
            ->listen('request.success', [$this, 'onRequestSuccess']);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'notification' => [
                'subject' => sprintf(
                    __('Vous avez une nouvelle demande de contact sur le site %s', 'tify'),
                    get_bloginfo('name')
                )
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defaultsFieldOptions()
    {
        return [
            'show' => false
        ];
    }

    /**
     * Initialisation de l'addon.
     *
     * @return void
     */
    public function appBoot()
    {
        /*
        if ($this->getFormOption('debug')) :
            $this->getForm()->callbacks()->setAddons(
                'handle_submit_request',
                $this->getName(),
                [$this, 'cb_handle_submit_request']
            );
        endif;
        $id = @ sanitize_html_class(base64_encode($this->getForm()->getUid()));

        // Bypass
        if ($admin = $this->getFormOption('admin')) :
            // Définition des attributs de configuration de l'interface d'administration
            $defaults = [
                'form_id'            => $this->getForm()->getName(),
                'confirmation'       => true,
                'notification'       => true,
                'option_name_prefix' => '',
                'option_names'       => [
                    'confirmation' => '',
                    'notification' => '',
                    'sender'       => '',
                    'recipients'   => '',
                ],
            ];
            $args = array_merge($defaults, $admin);

            // Déclaration de l'interface d'administration des options d'expédition de mail
            $this->appServiceGet(Options::class)->registerNode(
                [
                    'id'    => 'tiFyFormMailer_' . $id,
                    'title' => $this->getForm()->getTitle(),
                    'cb'    => AdminMailerOptions::class,
                    'args'  => $args,
                ]
            );
        endif;

        // Définition du prefixe du nom de l'option d'enregistrement en base de données
        if (!$this->getFormOption('option_name_prefix')) :
            $this->setFormAttr('option_name_prefix', 'tiFyFormMailer_' . $id);
        endif;
        $option_name_prefix = $this->getFormAttr('option_name_prefix');

        $option_names = $this->getFormAttr('option_names', []);
        foreach (['confirmation', 'sender', 'notification', 'recipients'] as $option) :
            $option_names[$option] = !empty($option_names[$option]) ? $option_names[$option] : $option_name_prefix . '-' . $option;
        endforeach;

        // Définition des attributs de l'email de confirmation
        $confirmation = $this->getFormAttr('confirmation');
        if (get_option($option_names['confirmation']) === 'off') :
            $this->setFormAttr('confirmation', false);
        elseif ($from = get_option($option_names['sender'])) :
            $confirmation['from'] = $from;
            $this->setFormAttr('confirmation', $confirmation);
        endif;

        // Définition des attributs de l'email de notification
        $notification = $this->getFormAttr('notification');
        if (get_option($option_names['notification']) === 'off') :
            $this->setFormAttr('notification', false);
        elseif ($to = get_option($option_names['recipients'])) :
            $notification['to'] = $to;
            $this->setFormAttr('notification', $notification);
        endif;
        */
    }

    /**
     * Court-circuitage du traitement de la requête du formulaire.
     *
     * @param FactoryRequest $request Instance du contrôleur de traitement de la requête de soumission associée au formulaire.
     *
     * @return void
     */
    public function onRequestValidation(FactoryRequest &$request)
    {
        switch ($this->get('debug')) :
            default:
            case 'confirmation' :
                if ($attrs = $this->get('confirmation', [])) :
                    $attrs = $this->parseEmailAttrs($attrs, 'confirmation');

                    echo tFyLibMailer::preview($attrs);
                else :
                    _e('Email de confirmation non configuré', 'tify');
                endif;
                break;
            case 'notification' :
                if ($attrs = $this->get('notification', [])) :
                    $attrs = $this->parseEmailAttrs($attrs, 'notification');

                    echo tFyLibMailer::preview($attrs);
                    exit;
                else :
                    _e('Email de notification non configuré', 'tify');
                endif;
                break;
        endswitch;
        exit;
    }

    /**
     * Court-circuitage de l'issue d'un traitement de formulaire réussi.
     *
     * @param FactoryRequest $request Instance du contrôleur de traitement de la requête de soumission associée au formulaire.
     *
     * @return void
     */
    public function onRequestSuccess(FactoryRequest &$request)
    {
        // Expédition du message de confirmation
        if ($attrs = $this->get('confirmation', [])) :
            $attrs = $this->parseEmailAttrs($attrs, 'confirmation');

            tFyLibMailer::send($attrs);
        endif;

        // Expédition du message de notification
        if ($attrs = $this->get('notification', [])) :
            $attrs = $this->parseEmailAttrs($attrs, 'notification');

            tFyLibMailer::send($attrs);
        endif;
    }

    /**
     * Traitement des attributs de configuration de l'email.
     *
     * @param array $attrs Liste des options a traiter.
     * @param string $context Contexte d'expédition de l'email. notification|confirmation.
     *
     * @return array
     */
    public function parseEmailAttrs($attrs = [], $context)
    {
        // Définition du sujet du mail
        if (!isset($options['subject'])) :
            $options['subject'] = sprintf(__('Nouvelle demande sur le site %1$s', 'tify'), get_bloginfo('name'));
        endif;

        // Définition du destinataire
        if (!isset($options['to'])) :
            $options['to'] = get_option('admin_email');
        endif;

        // Définition du message
        if (empty($options['message'])) :
            $subject = $options['subject'];

            $form = $this->getForm();
            $fields = [];
            /** @var FieldItemController $field */
            foreach ($this->getForm()->fields() as $field) :
                if (!$this->getFieldOption($field, 'show', false) ||
                    !$field->support('request') ||
                    in_array($field->get('type'), ['password', 'file'])
                ) :
                    continue;
                endif;

                $fields[$field->getSlug()] = [
                    'label' => $field->getTitle(),
                    'value' => $field->getValueDisplay(),
                ];
            endforeach;

            $slug = 'message';
            $name = $context;

            if ($template = $this->getFormOption('template')) :
                if (is_array($template)) :
                    if (isset($template['slug'])) :
                        $slug = $template['slug'];
                    endif;
                    if (isset($template['name'])) :
                        $name = $template['name'];
                    endif;
                else :
                    $slug = (string)$template;
                endif;
            endif;

            $options['message'] = $this->appTemplateRender($slug, compact('context', 'subject', 'fields', 'form'));
        endif;

        return $options;
    }
}