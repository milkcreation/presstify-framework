<?php declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Partials\CookieNotice;

use tiFy\Contracts\Partial\PartialFactory as BasePartialFactoryContract;
use tiFy\Partial\Partials\CookieNotice\CookieNotice as BaseCookieNotice;
use tiFy\Wordpress\Contracts\Partial\PartialFactory as PartialFactoryContract;

class CookieNotice extends BaseCookieNotice implements PartialFactoryContract
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        add_action('init', function () {
            add_action(
                'wp_ajax_tify_partial_cookie_notice',
                [$this, 'xhrSetCookie']
            );

            add_action(
                'wp_ajax_nopriv_tify_partial_cookie_notice',
                [$this, 'xhrSetCookie']
            );

            wp_register_script(
                'PartialCookieNotice',
                asset()->url('partial/cookie-notice/js/scripts.js'),
                ['PartialNotice'],
                170626,
                true
            );
        });
    }

    /**
     * {@inheritDoc}
     *
     * @return array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var string|callable $content Texte du message de notification. défaut 'Lorem ipsum dolor site amet'.
     *      @var bool $dismiss Affichage du bouton de masquage de la notification.
     *      @var string $type Type de notification info|warning|success|error. défaut info.
     *      @var array $accept Attribut du lien de validation et de création du cookie.
     *      @var string $cookie_name Nom de qualification du cookie.
     *      @var bool|string $cookie_hash Activation ou valeur d'un hashage pour le nom de qualification du cookie.
     *      @var int $cookie_expire Expiration du cookie. Exprimé en secondes.
     *      @var string $ajax_action Action ajax de création du cookie.
     *      @var string $ajax_nonce Chaine de sécurisation CSRF.
     * }
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'ajax_action'   => 'tify_partial_cookie_notice',
            'ajax_nonce'    => '',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function enqueue(): PartialFactoryContract
    {
        wp_enqueue_style('PartialNotice');
        wp_enqueue_script('PartialCookieNotice');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parse(): BasePartialFactoryContract
    {
        parent::parse();

        if(!$this->get('ajax_nonce')) {
            $this->set('ajax_nonce', wp_create_nonce('tiFyPartial-CookieNotice'));
        }

        $this->set([
            'attrs.data-options.ajax_action' => $this->get('ajax_action'),
            'attrs.data-options.ajax_nonce' => $this->get('ajax_nonce')
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrSetCookie()
    {
        check_ajax_referer('tiFyPartial-CookieNotice');

        parent::xhrSetCookie();
    }
}