<?php declare(strict_types=1);

namespace tiFy\Form\Addon\Mailer;

use Closure;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Form\AddonFactory;
use tiFy\Support\Proxy\Mail;
use tiFy\Support\Proxy\Metabox;
use tiFy\Validation\Validator as v;

class Mailer extends AddonFactory
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->form()->events()
            ->listen('handle.validated', function () {
                if ($debug = $this->params('debug')) {
                    $this->form()->events('addon.mailer.email.debug');
                }
            })
            ->listen('handle.successed', function () {
                $this->form()->events('addon.mailer.email.send');
            })
            ->listen('addon.mailer.email.debug', [$this, 'emailDebug'])
            ->listen('addon.mailer.email.send', [$this, 'emailSend']);

        if ($admin = $this->params('admin')) {
            $defaultAdmin = [
                'confirmation' => true,
                'notification' => true,
            ];
            $this->params(['admin' => is_array($admin) ? array_merge($defaultAdmin, $admin) : $defaultAdmin]);
        }

        if ($this->params('admin.confirmation') || $this->params('admin.notification')) {
            $optionNamesBase = $this->params('option_names_base', '') ?: "mail_{$this->form()->name()}";

            Metabox::add("FormAddonMailer-{$this->form()->name()}", [
                'title' => $this->form()->getTitle(),
            ])->setScreen('tify_options@options')->setContext('tab');

            if ($this->params('admin.notification')) {
                $optionName = $optionNamesBase . '_notif';

                register_setting('tify_options@options', $optionName, function ($value) {
                    $sender = $value['sender'] ?? null;

                    if (!empty($sender['email']) && !v::email()->validate($sender['email'])) {
                        add_settings_error("mail_{$this->form()->name()}_notif_sender", 'invalid_email', __(
                            'Adresse de messagerie d\'expédition de la notification non valide.', 'tify'
                        ));
                    }
                    if ($recipients = $value['recipients'] ?? null) {
                        foreach ($recipients as $k => $recip) {
                            if (empty($recip['email'])) {
                                add_settings_error("mail_{$this->form()->name()}_notif_recep{$k}", 'empty_email', __(
                                    'Adresse de messagerie de destination de la notification non renseignée.',
                                    'tify'
                                ));
                            } elseif (!is_email($recip['email'])) {
                                add_settings_error("mail_{$this->form()->name()}_notif_recep{$k}", 'invalid_email', __(
                                    'Adresse de messagerie de destination de la notification non valide.',
                                    'tify'
                                ));
                            }
                        }
                    }
                    return $value;
                });

                Metabox::add("FormAddonMailerNotification-{$this->form()->name()}", [
                    'driver'   => 'mail-config',
                    'name'     => $optionName,
                    'params'   => [
                        'info' => '<span class="dashicons dashicons-info-outline"></span>&nbsp;' .
                            __('Message de notification à destination des gestionnaires de demandes.', 'tify'),
                    ],
                    'parent'   => "FormAddonMailer-{$this->form()->name()}",
                    'position' => 1,
                    'title'    => __('Message(s) de notification(s)', 'tify'),
                ])->setScreen('tify_options@options')->setContext('tab');

                $optionValues = get_option($optionName);
                if ($optionValues !== false) {
                    if (filter_var($optionValues['enabled'], FILTER_VALIDATE_BOOL)) {
                        if (!empty($optionValues['sender']['email'])) {
                            $this->params([
                                'notification.from' => [
                                    $optionValues['sender']['email'],
                                    $optionValues['sender']['name'] ?? '',
                                ],
                            ]);
                        }
                        if (!empty($optionValues['recipients'])) {
                            $this->params([
                                'notification.to' => $optionValues['recipients'],
                            ]);
                        }
                    }
                }
            }

            if ($this->params('admin.confirmation')) {
                $optionName = $optionNamesBase . '_conf';

                register_setting('tify_options@options', $optionName, function ($value) {
                    $sender = $value['sender'] ?? null;

                    if (!empty($sender['email']) && !v::email()->validate($sender['email'])) {
                        add_settings_error("mail_{$this->form()->name()}_notif_sender", 'invalid_email', __(
                            'Adresse de messagerie d\'expédition de la confirmation n\'est pas valide.', 'tify'
                        ));
                    }
                    return $value;
                });

                Metabox::add("FormAddonMailerConfirmation-{$this->form()->name()}", [
                    'driver'   => 'mail-config',
                    'name'     => $optionName,
                    'params'   => [
                        'enabled' => [
                            'recipients' => false,
                        ],
                        'info'    => '<span class="dashicons dashicons-info-outline"></span>&nbsp;' .
                            __('Message de confirmation à destination de l\'emetteur de la demande.', 'tify'),
                    ],
                    'parent'   => "FormAddonMailer-{$this->form()->name()}",
                    'position' => 2,
                    'title'    => __('Message de confirmation', 'tify'),
                ])->setScreen('tify_options@options')->setContext('tab');

                $optionValues = get_option($optionName);
                if ($optionValues !== false) {
                    if (filter_var($optionValues['enabled'], FILTER_VALIDATE_BOOL)) {
                        if (!empty($optionValues['sender']['email'])) {
                            $this->params([
                                'confirmation.from' => [
                                    $optionValues['sender']['email'],
                                    $optionValues['sender']['name'] ?? '',
                                ],
                            ]);
                        }
                    }
                }
            }
        }

        if ($this->form()->field('mail')) {
            $emitter = '%%mail%%';
        } elseif ($this->form()->field('email')) {
            $emitter = '%%email%%';
        } else {
            $emitter = null;
        }

        if ($notification = $this->params('notification')) {
            $defaults = [
                'reply-to' => $emitter
            ];
            $this->params(['notification' => is_array($notification)
                ? array_merge($defaults, $notification) : $defaults
            ]);
        }
        if ($confirmation = $this->params('confirmation')) {
            $defaults = [
                'to' => $emitter
            ];
            $this->params(['confirmation' => is_array($confirmation)
                ? array_merge($defaults, $confirmation) : $defaults
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    public function defaultsParams(): array
    {
        return [
            /**
             * Activation de l'interface d'administration
             * @var bool|string true: Activer tous|false: Désactiver tous|"confirmation"|"notification"
             */
            'admin'             => true,
            /**
             * Activation du mode de debogage
             * @var bool|string true: Activer défaut|false: Désactiver|"confirmation"|"notification".
             * Notification par défaut.
             */
            'debug'             => false,
            /**
             * Paramètres du message de notification.
             * @see \tiFy\Mail\Mailer
             * @var bool|array true: Activer défaut|false: Désactiver
             */
            'notification'      => true,
            /**
             * Paramètres du message de notification.
             * @see \tiFy\Mail\Mailer
             * @var bool|array
             */
            'confirmation'      => true,
            /**
             * Base du nom d'enregistrement de la données en base.
             * @var string|null Automatique si null
             */
            'option_names_base' => null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultsFieldOptions(): array
    {
        return [
            /**
             * Activation de l'affichage dans le mail automatique.
             * @var bool
             */
            'show'  => true,
            /**
             * Intitulé de qualification dans le mail automatique.
             * @var bool
             */
            'label' => function (FactoryField $field) {
                return $field->getTitle();
            },
            /**
             * Valeur dans le mail automatique.
             * @var bool
             */
            'value' => function (FactoryField $field) {
                return $field->getValues();
            },
        ];
    }

    /**
     * Débogguage des emails de confirmation et/ou de notification.
     *
     * @return void
     */
    public function emailDebug()
    {
        switch ($this->params('debug')) {
            default:
            case 'notification' :
                $notification = $this->params('notification');
                if ($notification !== false) {
                    Mail::debug($this->mailParams($notification, 'notification'));
                } else {
                    wp_die(
                        __('Email de notification désactivé.', 'tify'),
                        __('FormAddonMailer - Erreur', 'tify'),
                        500
                    );
                }
                break;
            case 'confirmation' :
                $confirmation = $this->params('confirmation');
                if ($confirmation !== false) {
                    Mail::debug($this->mailParams($confirmation, 'confirmation'));
                } else {
                    wp_die(
                        __('Email de confirmation désactivé.', 'tify'),
                        __('FormAddonMailer - Erreur', 'tify'),
                        500
                    );
                }
                break;
        }
    }

    /**
     * Expédition des emails de confirmation et/ou de notification.
     *
     * @return void
     */
    public function emailSend(): void
    {
        if ($notification = $this->params('notification')) {
            Mail::send($this->mailParams($notification, 'notification'));
        }

        if ($confirmation = $this->params('confirmation')) {
            Mail::send($this->mailParams($confirmation, 'confirmation'));
        }
    }

    /**
     * Traitement des paramètres de configuration de l'email.
     *
     * @param array $params
     * @param string $type notification|confirmation.
     *
     * @return array
     */
    public function mailParams(array $params, string $type)
    {
        $params['subject'] = $params['subject']
            ?? sprintf(__('%1$s - Demande de contact', 'tify'), get_bloginfo('name'));

        $params = array_map([$this->form(), 'fieldTagsValue'], $params);

        $params['to'] = $params['to'] ?? Mail::getDefaults('to');
        $params['from'] = $params['from'] ?? Mail::getDefaults('from');

        $fields = $this->form()->fields()->collect()->filter(function (FactoryField $item) {
            return $item->getAddonOption('mailer', 'show') && $item->supports('request');
        });

        $fields->each(function (FactoryField $item) {
            $mailer_label = $item->getAddonOption('mailer', 'label');
            $item['mailer_label'] = $mailer_label instanceof Closure ? $mailer_label($item) : $mailer_label;

            $mailer_value = $item->getAddonOption('mailer', 'value');
            $item['mailer_value'] = $mailer_value instanceof Closure ? $mailer_value($item) : $mailer_value;
        });

        $form = $this->form();
        $addon = $this;
        $params['data'] = array_merge(
            Mail::config('data', []), compact('addon', 'form', 'fields', 'params'), $params['data'] ?? []
        );

        if (!isset($params['viewer']['override_dir'])) {
            $params['viewer'] = array_merge([
                'override_dir' => "{$this->form()->viewer()->getDirectory()}/addon/mailer/mail/{$type}",
            ], $params['viewer'] ?? []);
        }

        return $params;
    }
}