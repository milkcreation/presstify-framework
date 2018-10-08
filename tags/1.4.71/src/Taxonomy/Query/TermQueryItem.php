<?php

namespace tiFy\Taxonomy\Query;

use Illuminate\Support\Fluent;
use tiFy\Contracts\Taxonomy\TermQueryItemInterface;

class TermQueryItem extends Fluent implements TermQueryItemInterface
{
    /**
     * Objet Term Wordpress
     * @var \WP_Term
     */
    protected $object;

    /**
     * CONSTRUCTEUR.
     *
     * @param \WP_Term $wp_term
     *
     * @return void
     */
    public function __construct(\WP_Term $wp_term)
    {
        $this->object = $wp_term;

        parent::__construct($this->object->to_array());
    }

    /**
     * Récupération de la description.
     *
     * @return string
     */
    public function getDescription()
    {
        return (string)$this->get('description', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int)$this->get('term_id', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return (string)$this->get('name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return (string)$this->get('slug', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxonomy()
    {
        return (string)$this->get('taxonomy', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getTerm()
    {
        return $this->object;
    }
}