<?php

namespace tiFy\Components\Forms\Addons\Mailer;

use tiFy\Form\Addons\AbstractAddonController;
use tiFy\Components\Addons\Mailer\AdminMailerOptions;
use tiFy\Components\Tools\Mailer\Mailer as tFyLibMailer;
use tiFy\Options\Options;

class Mailer extends AbstractAddonController
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
    public $defaultFormOptions = [
        'admin'              => [
            'confirmation' => true,
            'notification' => true,

        ],
        'confirmation'       => [
            'to' => 'email',
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
     * Liste des options par défaut des champs du formulaire associé.
     * @var array
     */
    protected $defaultFieldOptions = [
        'show' => false,
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultFormOptions['notification']['subject'] = sprintf(
            __('Vous avez une nouvelle demande de contact sur le site %s', 'tify'),
            get_bloginfo('name')
        );
        $this->callbacks['handle_successfully'] = [$this, 'cb_handle_successfully'];
    }

    /**
     * Initialisation de l'addon.
     *
     * @return void
     */
    public function appBoot()
    {
        if ($this->getFormOption('debug')) :
            $this->getForm()->callbacks()->setAddons('handle_submit_request', $this->getName(),
                [$this, 'cb_handle_submit_request']);
        endif;
        $id = @ sanitize_html_class(base64_encode($this->getForm()->getUid()));

        // Bypass
        if ($admin = $this->getFormAttr('admin')) :
            // Définition des attributs de configuration de l'interface d'administration
            $defaults = [
                'form_id'            => $this->getForm()->getId(),
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
            $args = \wp_parse_args($admin, $defaults);

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
        if (!$this->getFormAttr('option_name_prefix')) :
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
    }

    /**
     * Court-circuitage du traitement de la requête du formulaire.
     *
     * @param Handle $handle Classe de rappel de traitement du formulaire.
     *
     * @return void
     */
    public function cb_handle_submit_request($handle)
    {
        switch ($this->getFormAttr('debug')) :
            default:
            case 'confirmation' :
                // Expédition du message de confirmation
                if ($options = $this->getFormAttr('confirmation')) :
                    $options = $this->parseOptions($options, 'confirmation');

                    echo tFyLibMailer::preview($options);

                else :
                    _e('Email de confirmation non configuré', 'tify');
                endif;
                break;
            case 'notification' :
                // Expédition du message de notification
                if ($options = $this->getFormAttr('notification')) :
                    $options = $this->parseOptions($options, 'notification');

                    echo tFyLibMailer::preview($options);
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
     * @param Handle $handle Classe de rappel de traitement du formulaire.
     *
     * @return void
     */
    public function cb_handle_successfully($handle)
    {
        // Expédition du message de confirmation
        if ($options = $this->getFormAttr('confirmation')) :
            $options = $this->parseOptions($options, 'confirmation');

            tFyLibMailer::send($options);
        endif;

        // Expédition du message de notification
        if ($options = $this->getFormAttr('notification')) :
            $options = $this->parseOptions($options, 'notification');

            tFyLibMailer::send($options);
        endif;
    }

    /**
     * Traitement des options de l'email.
     *
     * @param array $options Liste des options a traiter.
     * @param string $context Contexte d'expédition de l'email. notification|confirmation.
     *
     * @return array
     */
    public function parseOptions($options, $context)
    {
        $options = Helpers::parseMergeVars($options, $this->getForm());

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
            foreach ((array)$this->getForm()->fields() as $field) :
                if (!$this->getFieldAttr($field, 'show', false) ||
                    !$field->typeSupport('request') ||
                    in_array($field->getType(), ['password', 'file'])
                ) :
                    continue;
                endif;

                $fields[$field->getSlug()] = [
                    'label' => $field->getLabel(),
                    'value' => $field->getDisplayValue(),
                ];
            endforeach;

            $slug = 'message';
            $name = $context;

            if ($template = $this->getFormAttr('template')) :
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