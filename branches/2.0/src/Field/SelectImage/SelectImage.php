<?php

namespace tiFy\Field\SelectImage;

use Symfony\Component\Finder\Finder;
use tiFy\Field\Field;
use tiFy\Field\AbstractFieldItem;
use tiFy\Kernel\Tools;
use tiFy\Partial\Partial;

class SelectImage extends AbstractFieldItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'attrs'     => [],
        'name'      => '',
        'value'     => null,
        'directory' => ''
    ];

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('FieldSelectImage');
        \wp_enqueue_script('FieldSelectJs');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'FieldSelectImage',
            assets()->url('field/select-image/css/styles.css'),
            ['FieldSelectJs'],
            180808
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $options = [];
        $finder = (new Finder())->in($this->get('directory'))->depth('== 0')->name('(.ico|.gif|jpe?g|.png|.svg)');
        foreach ($finder as $file) :
            $options[$file->getRelativePathname()] = (string)Partial::Tag(
                [
                    'tag'   => 'img',
                    'attrs' => [
                        'src'   => Tools::File()->imgBase64Src($file->getRealPath()),
                        'title' => $file->getRelativePathname(),
                    ],
                ]
            );
        endforeach;
        $this->set('options', $options);
    }
}