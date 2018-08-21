<?php

namespace tiFy\Kernel\Config;

use Illuminate\Support\Arr;
use Symfony\Component\Finder\Finder;
use tiFy\Kernel\Item\AbstractItemController;

class Config extends AbstractItemController
{
    /**
     * Liste des alias.
     * @var array
     */
    protected $aliases = [
        'app'         => \App\App::class,
        'admin-view'  => \tiFy\AdminView\AdminView::class,
        'ajax-action' => \tiFy\AjaxAction\AjaxAction::class,
        'api'         => \tiFy\Api\Api::class,
        'column'      => \tiFy\Column\Column::class,
        'cron'        => \tiFy\Cron\Cron::class,
        'db'          => \tiFy\Db\Db::class,
        'field'       => \tiFy\Field\Field::class,
        'form'        => \tiFy\Form\Form::class,
        'media'       => \tiFy\Media\Media::class,
        'metabox'     => \tiFy\Metabox\Metabox::class,
        'metadata'    => \tiFy\Metadata\Metadata::class,
        'meta-tag'    => \tiFy\MetaTag\MetaTag::class,
        'options'     => \tiFy\Options\Options::class,
        'page-hook'   => \tiFy\PageHook\PageHook::class,
        'partial'     => \tiFy\Partial\Partial::class,
        'post-type'   => \tiFy\PostType\PostType::class,
        'route'       => \tiFy\Route\Route::class,
        'tab-metabox' => \tiFy\TabMetabox\TabMetabox::class,
        'taxonomy'    => \tiFy\Taxonomy\Taxonomy::class,
        'user'        => \tiFy\User\User::class,
        'view'        => \tiFy\View\View::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $finder = (new Finder())->files()->name('/\.php$/')->in(\paths()->getConfigPath());
        foreach ($finder as $file) :
            $key = basename($file->getFilename(), ".{$file->getExtension()}");
            if ($key === 'autoload') :
                continue;
            endif;

            $value = include($file->getRealPath());

            switch($key) :
                default :
                    $this->set($this->getAlias($key), $value);
                    break;
                case 'plugins' :
                    foreach((array)$value as $plugin => $attrs) :
                        $this->set($plugin, $attrs);
                    endforeach;
                    break;
            endswitch;
        endforeach;
    }

    /**
     * Récupération de l'alias de qualification d'un attribut de configuration.
     *
     * @param string $key Nom de qualification original.
     *
     * @return string
     */
    public function getAlias($key)
    {
        return Arr::get($this->getAliases(), $key, $key);
    }

    /**
     * Récupération de la liste des alias.
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }
}