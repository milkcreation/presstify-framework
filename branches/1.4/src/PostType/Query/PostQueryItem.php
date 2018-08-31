<?php

namespace tiFy\PostType\Query;

use Illuminate\Support\Fluent;
use tiFy\Contracts\PostType\PostQueryItemInterface;

class PostQueryItem extends Fluent implements QueryPostItemInterface
{
    /**
     * Objet Post Wordpress.
     * @var \WP_Post
     */
    protected $object;

    /**
     * CONSTRUCTEUR.
     *
     * @param \WP_Post $wp_post Objet Post Wordpress.
     *
     * @return void
     */
    public function __construct(\WP_Post $wp_post)
    {
        $this->object = $wp_post;

        parent::__construct($this->object->to_array());
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorId()
    {
        return (int)$this->get('post_author', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($raw = false)
    {
        $content = (string)$this->get('post_content', '');

        if (!$raw) :
            $content = \apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
        endif;

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getDate($gmt = false)
    {
        if ($gmt == false) :
            return (string)$this->get('post_date', '');
        else :
            return (string)$this->get('post_date_gmt', '');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditLink()
    {
        return \get_edit_post_link($this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getExcerpt($raw = false)
    {
        $excerpt = (string)$this->get('post_excerpt', '');

        if ($raw) :
            return $excerpt;
        else :
            return \apply_filters('get_the_excerpt', $excerpt, $this->getPost());
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getGuid()
    {
        return (string)$this->get('guid', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int)$this->get('ID', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($meta_key, $single = false, $default = null)
    {
        return get_post_meta($this->getId(), $meta_key, $single) ? : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getModified($gmt = false)
    {
        if ($gmt) :
            return (string)$this->get('post_modified', '');
        else :
            return (string)$this->get('post_modified_gmt', '');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId()
    {
        return (int)$this->get('post_parent', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermalink()
    {
        return \get_permalink($this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getPost()
    {
        return $this->object;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return (string)$this->get('post_name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return (string)$this->get('post_status', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle($raw = false)
    {
        $title = (string)$this->get('post_title', '');

        if ($raw) :
            return $title;
        else :
            return \apply_filters('the_title', $title, $this->getId());
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return (string)$this->get('post_type', '');
    }
}