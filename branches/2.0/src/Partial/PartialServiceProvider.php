<?php

namespace tiFy\Partial;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Partial\PartialFactory;
use tiFy\Partial\Partials\Accordion\Accordion;
use tiFy\Partial\Partials\Breadcrumb\Breadcrumb;
use tiFy\Partial\Partials\CookieNotice\CookieNotice;
use tiFy\Partial\Partials\Dropdown\Dropdown;
use tiFy\Partial\Partials\Holder\Holder;
use tiFy\Partial\Partials\Modal\Modal;
use tiFy\Partial\Partials\Navtabs\Navtabs;
use tiFy\Partial\Partials\Notice\Notice;
use tiFy\Partial\Partials\Pagination\Pagination;
use tiFy\Partial\Partials\Sidebar\Sidebar;
use tiFy\Partial\Partials\Slider\Slider;
use tiFy\Partial\Partials\Spinner\Spinner;
use tiFy\Partial\Partials\Table\Table;
use tiFy\Partial\Partials\Tag\Tag;

class PartialServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'partial',
        'partial.factory',
        'partial.factory.accordion',
        'partial.factory.breadcrumb',
        'partial.factory.cookie-notice',
        'partial.factory.dropdown',
        'partial.factory.holder',
        'partial.factory.modal',
        'partial.factory.navtabs',
        'partial.factory.notice',
        'partial.factory.pagination',
        'partial.factory.sidebar',
        'partial.factory.slider',
        'partial.factory.spinner',
        'partial.factory.table',
        'partial.factory.tag',
        'partial.viewer'
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('partial', function () {
            return new PartialManager();
        });

        $this->registerFactories();
    }

    /**
     * Déclaration des controleurs de gabarit d'affichage.
     *
     * @return void
     */
    public function registerFactories()
    {
        $this->getContainer()->add('partial.factory.accordion', function (?string $id = null, ?array $attrs = null) {
            return new Accordion($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.breadcrumb', function (?string $id = null, ?array $attrs = null) {
            return new Breadcrumb($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.cookie-notice', function (?string $id = null, ?array $attrs = null) {
            return new CookieNotice($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.dropdown', function (?string $id = null, ?array $attrs = null) {
            return new Dropdown($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.holder', function (?string $id = null, ?array $attrs = null) {
            return new Holder($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.modal', function (?string $id = null, ?array $attrs = null) {
            return new Modal($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.navtabs', function (?string $id = null, ?array $attrs = null) {
            return new Navtabs($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.notice', function (?string $id = null, ?array $attrs = null) {
            return new Notice($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.pagination', function (?string $id = null, ?array $attrs = null) {
            return new Pagination($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.sidebar', function (?string $id = null, ?array $attrs = null) {
            return new Sidebar($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.slider', function (?string $id = null, ?array $attrs = null) {
            return new Slider($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.spinner', function (?string $id = null, ?array $attrs = null) {
            return new Spinner($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.table', function (?string $id = null, ?array $attrs = null) {
            return new Table($id, $attrs);
        });

        $this->getContainer()->add('partial.factory.tag', function (?string $id = null, ?array $attrs = null) {
            return new Tag($id, $attrs);
        });

        $this->getContainer()->add('partial.viewer', function(PartialFactory $factory) {
            $alias = class_info($factory)->getKebabName();
            $default_dir = partial()->resourcesDir("/views/{$alias}");
            $override_dir = $factory->get('viewer.override_dir');

            return view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(PartialView::class)
                ->setOverrideDir((($override_dir) && is_dir($override_dir))
                    ? $override_dir
                    : (is_dir($default_dir) ? $default_dir : __DIR__)
                )
                ->set('partial', $factory);
        });
    }
}