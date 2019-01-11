<?php

namespace tiFy\Partial\Partials\Pagination;

class PaginationUrl
{
    /**
     * Url de base.
     * @var string
     */
    protected $baseurl = '';

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($baseurl = null)
    {
        $this->baseurl = $baseurl ?: url_factory(url()->full())->without(['page'])->with(['page' => '%d'])->getDecode();
    }

    /**
     * Récupération du lien vers une page via son numéro.
     *
     * @param int $num Numéro de la page.
     *
     * @return string
     */
    public function page($num)
    {
        return $num ? sprintf($this->baseurl, $num) : $this->baseurl;
    }
}