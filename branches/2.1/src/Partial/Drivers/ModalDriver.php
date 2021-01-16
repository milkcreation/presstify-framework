<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use Closure;
use tiFy\Partial\PartialDriver;
use tiFy\Partial\PartialDriverInterface;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Request;

class ModalDriver extends PartialDriver implements ModalDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * @var bool|string|array $ajax Activation du chargement du contenu Ajax ou Contenu a charger ou liste des
             * attributs de récupération Ajax
             */
            'ajax'           => false,
            /**
             * @var bool $ajax_cacheable Conservation en cache du contenu récupéré via Ajax
             */
            'ajax_cacheable' => true,
            /**
             * @var bool|string $animated Activation de l'animation.
             */
            'animated'       => true,
            /**
             * @var bool|array $backdrop
             */
            'backdrop'       => [
                'close' => false,
            ],
            /**
             *
             */
            'classes'        => [],
            /**
             *
             */
            'close'          => true,
            /**
             * @var bool|string|array|callable $content
             */
            'content'        => [
                /**
                 * @var bool|string|callable $body Affichage du corps de la fenêtre. Chaine de caractère à
                 * afficher|booléen pour activer désactiver ou fonction/méthode d'affichage.
                 */
                'body'    => true,
                /**
                 * @var bool|string|callable $header Affichage de l'entête de la fenêtre. Chaine de caractère à
                 * afficher|booléen pour activer désactiver ou fonction/méthode d'affichage.
                 */
                'header'  => true,
                /**
                 * @var bool|string|callable $footer Affichage d'un bouton fermeture externe. Chaine de caractère à
                 * afficher|booléen pour activer désactiver ou fonction/méthode d'affichage.
                 */
                'footer'  => true,
                'spinner' => true,
            ],
            /**
             * @var array $options Liste des options d'affichage.
             */
            'options'        => [],
            /**
             * @var string $size Taille d'affichage de la fenêtre de dialogue sm|lg|xl|full|flex.
             */
            'size'           => '',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        $this->set([
           'attrs.data-id'      => $this->get('attrs.id', $this->getId())
        ]);
        return parent::parseParams();
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultClasses = [
            'bkclose' => 'modal-backdrop-close ThemeButton--close',
            'body'    => 'modal-body',
            'close'   => 'modal-close ThemeButton--close',
            'content' => 'modal-content',
            'dialog'  => 'modal-dialog',
            'footer'  => 'modal-footer',
            'header'  => 'modal-header',
            'spinner' => 'modal-spinner',
        ];
        foreach ($defaultClasses as $k => $v) {
            $this->set(["classes.{$k}" => sprintf($this->get("classes.{$k}", '%s'), $v)]);
        }

        $this->set([
            'attrs.data-control' => $this->get('attrs.data-control', 'modal'),
            'attrs.data-id'      => $this->get('attrs.id', $this->getId()),
            'size'               => in_array($this->get('size'), ['sm', 'lg', 'xl', 'full', 'flex'])
                ? 'modal-' . $this->get('size') : '',
        ]);

        $options = array_merge([
            'backdrop' => true,
            'keyboard' => true,
            'focus'    => true,
            'show'     => true,
        ], $this->pull('options'));

        foreach (['backdrop', 'keyboard', 'focus', 'show'] as $key) {
            switch ($key) {
                case 'backdrop' :
                    $value = ($options[$key] === 'static') ? 'static' : ($options[$key] ? 'true' : 'false');
                    break;
                default :
                    $value = $options[$key] ? 'true' : 'false';
                    break;
            }
            $this->set("attrs.data-{$key}", $value);
        }

        if ($bkClose = $this->get('backdrop.close', true)) {
            if ($bkClose instanceof Closure) {
                $this->set('backdrop.close', (string)$bkClose($this->all()));
            } elseif (is_string($bkClose)) {
                $this->set('backdrop.close', $bkClose);
            } else {
                $this->set('backdrop.close', (string)$this->view('backdrop-close', $this->all()));
            }
        }

        if ($close = $this->get('close', true)) {
            if ($close instanceof Closure) {
                $this->set('close', (string)$close($this->all()));
            } elseif (is_string($close)) {
                $this->set('close', $close);
            } else {
                $this->set('close', (string)$this->view('close', $this->all()));
            }
        }

        if ($content = $this->get('content')) {
            if ($content instanceof Closure) {
                $this->set('content', (string)$content($this->all()));
            } elseif (is_string($content)) {
                $this->set('content', $content);
            } else {
                foreach (['body', 'footer', 'header', 'spinner'] as $item) {
                    if (${$item} = $this->get("content.{$item}", true)) {
                        if (${$item} instanceof Closure) {
                            $this->set("content.{$item}", (string)${$item}($this->all()));
                        } elseif (is_string(${$item})) {
                            $this->set("content.{$item}", ${$item});
                        } else {
                            $this->set("content.{$item}", (string)$this->view("content-{$item}", $this->all()));
                        }
                    }
                }
            }
        } else {
            $this->get('content', (string)$this->view('content', $this->all()));
        }

        $this->set([
            'attrs.data-options' => [
                'animated'       => $this->get('animated'),
                'backdrop-close' => !!$this->get('backdrop.close'),
                'body'           => !!$this->get('content.body'),
                'classes'        => $this->get('classes', []),
                'close'          => !!$this->get('close'),
                'footer'         => !!$this->get('content.footer'),
                'header'         => !!$this->get('content.header'),
                'spinner'        => !!$this->get('content.spinner'),
                'size'           => $this->get('size'),
            ],
        ]);

        if ($ajax = $this->get('ajax', false)) {
            $defaultAjax = [
                'data'     => [],
                'dataType' => 'json',
                'method'   => 'post',
                'url'      => $this->partialManager()->getXhrRouteUrl('modal'),
            ];

            $this->set([
                'attrs.data-options.ajax'           => is_array($ajax)
                    ? array_merge($defaultAjax, $ajax) : $defaultAjax,
                'attrs.data-options.ajax_cacheable' => !!$this->get('ajax_cacheable'),
            ]);

            if (!$this->get('attrs.data-options.ajax.data.viewer')) {
                $this->set('attrs.data-options.ajax.data.viewer', $this->get('viewer', []));
            }
        }

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function trigger($attrs = []): string
    {
        $params = (new ParamsBag())->set(array_merge([
            'tag'     => 'a',
            'attrs'   => [],
            'content' => '',
            // Liste des options passées à la modale
            'options' => [],
        ], $attrs));

        if (($params->get('tag') === 'a') && !$params->has('attrs.href')) {
            $params->set('attrs.href', "#{$this->get('attrs.data-id')}");
        }

        $params->set([
            'attrs.data-control' => 'modal.trigger',
            'attrs.data-target'  => "{$this->get('attrs.data-id')}",
        ]);

        if ($options = $params->pull('options')) {
            $params->set('attrs.data-options', $options);
        }

        return $this->view('trigger', $params->all());
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->partialManager()->resources("/views/modal");
    }

    /**
     * @inheritdoc
     */
    public function xhrResponse(...$args): array
    {
        $viewer = Request::input('viewer', []);

        foreach ($viewer as $key => $value) {
            $this->view()->params([$key => $value]);
        }
        return [
            'success' => true,
            'data'    => $this->view('ajax-content'),
        ];
    }
}