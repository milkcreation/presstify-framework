<?php

namespace tiFy\Wp\Query;

use tiFy\Contracts\Wp\QueryTerm as QueryTermContract;
use tiFy\Kernel\Params\ParamsBag;
use WP_Term;

class QueryTerm extends ParamsBag implements QueryTermContract
{
    /**
     * Objet Term Wordpress.
     * @var WP_Term
     */
    protected $wp_term;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Term $wp_term Objet terme Wordpress.
     *
     * @return void
     */
    public function __construct(WP_Term $wp_term)
    {
        $this->wp_term = $wp_term;

        parent::__construct($this->wp_term->to_array());
    }

    /**
     * @inheritdoc
     */
    public static function createFromId($term_id)
    {
        return ($term_id && is_numeric($term_id) && ($wp_term = get_term($term_id)) && ($wp_term instanceof WP_Term))
            ? new static($wp_term) : null;
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
     * @inheritdoc
     */
    public function getId()
    {
        return (int)$this->get('term_id', 0);
    }

    /**
     * @inheritdoc
     */
    public function getMeta($meta_key, $single = false, $default = null)
    {
        return get_term_meta($this->getId(), $meta_key, $single) ? : $default;
    }

    /**
     * @inheritdoc
     */
    public function getMetaMulti($meta_key, $default = null)
    {
        return $this->getMeta($meta_key, false, $default);
    }

    /**
     * @inheritdoc
     */
    public function getMetaSingle($meta_key, $default = null)
    {
        return $this->getMeta($meta_key, true, $default);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return (string)$this->get('name', '');
    }

    /**
     * @inheritdoc
     */
    public function getSlug()
    {
        return (string)$this->get('slug', '');
    }

    /**
     * @inheritdoc
     */
    public function getTaxonomy()
    {
        return (string)$this->get('taxonomy', '');
    }

    /**
     * @inheritdoc
     */
    public function getTerm()
    {
        return $this->wp_term;
    }
}