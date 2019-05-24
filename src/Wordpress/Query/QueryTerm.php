<?php declare(strict_types=1);

namespace tiFy\Wordpress\Query;

use tiFy\Wordpress\Contracts\QueryTerm as QueryTermContract;
use tiFy\Support\ParamsBag;
use WP_Term;

class QueryTerm extends ParamsBag implements QueryTermContract
{
    /**
     * Instance de terme de taxonomie Wordpress.
     * @var WP_Term
     */
    protected $wp_term;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Term $wp_term Instance de terme de taxonomie Wordpress.
     *
     * @return void
     */
    public function __construct(WP_Term $wp_term)
    {
        $this->wp_term = $wp_term;

        $this->set($this->wp_term->to_array())->parse();
    }

    /**
     * @inheritDoc
     */
    public static function createFromId(int $term_id): ?QueryTermContract
    {
        return (($wp_term = get_term($term_id)) && ($wp_term instanceof WP_Term))
            ? new static($wp_term) : null;
    }

    /**
     * @inheritDoc
     */
    public static function createFromSlug(string $term_slug, string $taxonomy): ?QueryTermContract
    {
        return (($wp_term = get_term_by('slug', $term_slug, $taxonomy)) && ($wp_term instanceof WP_Term))
            ? new static($wp_term) : null;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return (string)$this->get('description', '');
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return intval($this->get('term_id', 0));
    }

    /**
     * @inheritDoc
     */
    public function getMeta($meta_key, $single = false, $default = null)
    {
        return get_term_meta($this->getId(), $meta_key, $single) ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function getMetaMulti($meta_key, $default = null)
    {
        return $this->getMeta($meta_key, false, $default);
    }

    /**
     * @inheritDoc
     */
    public function getMetaSingle($meta_key, $default = null)
    {
        return $this->getMeta($meta_key, true, $default);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->get('name', '');
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return get_term_link($this->getWpTerm());
    }

    /**
     * @inheritDoc
     */
    public function getSlug(): string
    {
        return (string)$this->get('slug', '');
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomy(): string
    {
        return (string)$this->get('taxonomy', '');
    }

    /**
     * @inheritDoc
     */
    public function getWpTerm(): WP_Term
    {
        return $this->wp_term;
    }
}