<?php declare(strict_types=1);

namespace tiFy\Partial\Partials\PdfPreview;

use tiFy\Partial\PartialFactory;

class PdfPreview extends PartialFactory
{
    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'src'     => 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/examples/learning/helloworld.pdf',
            'view'    => [
                'attrs' => [
                    'class' => 'PdfPreview-view'
                ]
            ],
            'prev'    => [
                'tag'     => 'a',
                'attrs'   => [
                    'href'  => '#',
                    'class' => 'PdfPreview-nav PdfPreview-nav--prev',
                ],
                'content' => __('Préc.', 'tify')
            ],
            'next'    => [
                'tag'   => 'a',
                'attrs' => [
                    'href'  => '#',
                    'class' => 'PdfPreview-nav PdfPreview-nav--next',
                ],
                'content' => __('Suiv.', 'tify')
            ],
            'page' => [
                'attrs' => [
                    'class' => 'PdfPreview-page'
                ]
            ],
            'num' => [
                'tag'   => 'span',
                'attrs' => [
                    'class' => 'PdfPreview-pageNum',
                ]
            ],
            'total'   => [
                'tag'   => 'span',
                'attrs' => [
                    'class' => 'PdfPreview-pageTotal',
                ]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function display()
    {
        return (string)$this->viewer('pdf-preview', $this->all());
    }

    /**
     * @inheritDoc
     */
    public function parse()
    {
        $defaults = $this->defaults();
        foreach(['view', 'prev', 'next', 'page', 'num', 'total'] as $el) {
            $this->set("{$el}", array_merge($defaults[$el], $this->get($el, [])));
            $this->set("{$el}.attrs.class", sprintf(
                $this->get("{$el}.attrs.class", []), $defaults[$el]['attrs']['class']
            ));
        }

        parent::parse();

        $this->set([
            'attrs.data-control'         => 'pdf-preview',
            'attrs.data-src'             => $this->get('src'),
            'view.tag'                   => 'canvas',
            'view.attrs.data-control'    => 'pdf-preview.view',
            'prev.attrs.data-control'    => 'pdf-preview.nav.prev',
            'next.attrs.data-control'    => 'pdf-preview.nav.next',
            'page.attrs.data-control'    => 'pdf-preview.page',
            'page.attrs.aria-visible'    => false,
            'num.attrs.data-control'     => 'pdf-preview.page.num',
            'total.attrs.data-control'   => 'pdf-preview.page.total',
        ]);
    }
}