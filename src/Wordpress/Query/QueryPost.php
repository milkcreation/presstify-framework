<?php declare(strict_types=1);

namespace tiFy\Wordpress\Query;

use tiFy\Wordpress\Contracts\QueryPost as QueryPostContract;
use tiFy\Support\ParamsBag;
use WP_Post;
use WP_Query;
use WP_Term_Query;

class QueryPost extends ParamsBag implements QueryPostContract
{
    /**
     * Objet Post Wordpress.
     * @var WP_Post
     */
    protected $wp_post;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Post $wp_post Objet Post Wordpress.
     *
     * @return void
     */
    public function __construct(WP_Post $wp_post)
    {
        $this->wp_post = $wp_post;

        $this->set($this->wp_post->to_array())->parse();
    }

    /**
     * @inheritdoc
     */
    public static function createFromGlobal(): ?QueryPostContract
    {
        global $post;

        return $post instanceof WP_Post ? new static($post) : null;
    }

    /**
     * @inheritdoc
     */
    public static function createFromId($post_id): ?QueryPostContract
    {
        return ($post_id && is_numeric($post_id) && ($wp_post = get_post($post_id)) && ($wp_post instanceof WP_Post))
            ? new static($wp_post) : null;
    }

    /**
     * @inheritdoc
     */
    public static function createFromName(string $post_name): ?QueryPostContract
    {
        return (($wp_post = (new WP_Query)->query(['name' => $post_name, 'post_type' => 'any', 'posts_per_page' => 1]))
            && ($wp_post[0] instanceof WP_Post)) ? new static($wp_post[0]) : null;
    }

    /**
     * @inheritdoc
     */
    public function getAuthorId()
    {
        return (int)$this->get('post_author', 0);
    }

    /**
     * @inheritdoc
     */
    public function getContent($raw = false)
    {
        $content = (string)$this->get('post_content', '');

        if (!$raw) :
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
        endif;

        return $content;
    }

    /**
     * @inheritdoc
     */
    public function getDate($gmt = false)
    {
        return $gmt
            ? (string)$this->get('post_date_gmt', '')
            : (string)$this->get('post_date', '');
    }

    /**
     * @inheritdoc
     */
    public function getEditLink()
    {
        return get_edit_post_link($this->getId());
    }

    /**
     * @inheritdoc
     */
    public function getExcerpt($raw = false)
    {
        if (!$excerpt = (string)$this->get('post_excerpt', '')) :
            $text = $this->get('post_content', '');

            // @see /wp-includes/post-template.php \get_the_excerpt()
            $text = strip_shortcodes($text);
            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);

            $excerpt_length = apply_filters('excerpt_length', 55);
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[&hellip;]');
            $excerpt = wp_trim_words($text, $excerpt_length, $excerpt_more);
        endif;

        return $raw ? $excerpt : ($excerpt ? apply_filters('get_the_excerpt', $excerpt) : '');
    }

    /**
     * @inheritdoc
     */
    public function getGuid()
    {
        return (string)$this->get('guid', '');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return (int)$this->get('ID', 0);
    }

    /**
     * @inheritdoc
     */
    public function getMeta($meta_key, $single = false, $default = null)
    {
        return get_post_meta($this->getId(), $meta_key, $single) ? : $default;
    }

    /**
     * @inheritdoc
     */
    public function getMetaKeys(bool $registered = true): array
    {
        if($registered) {
            return post_type()->post_meta()->keys($this->getType());
        } else {
            return get_post_custom_keys($this->getId()) ? : [];
        }
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
    public function getModified($gmt = false)
    {
        return $gmt
            ? (string)$this->get('post_modified_gmt', '')
            : (string)$this->get('post_modified', '');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getSlug();
    }

    /**
     * @inheritdoc
     */
    public function getParentId()
    {
        return (int)$this->get('post_parent', 0);
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return rtrim(str_replace(home_url(), '', $this->getPermalink()), '/');
    }

    /**
     * @inheritdoc
     */
    public function getPermalink()
    {
        return get_permalink($this->getId());
    }

    /**
     * @inheritdoc
     */
    public function getPost()
    {
        return $this->wp_post;
    }

    /**
     * @inheritdoc
     */
    public function getSlug()
    {
        return (string)$this->get('post_name', '');
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return (string)$this->get('post_status', '');
    }

    /**
     * @inheritdoc
     */
    public function getTerms($taxonomy, $args = [])
    {
        $args['taxonomy'] = $taxonomy;
        $args['object_ids'] = $this->getId();

        return (new WP_Term_Query($args))->terms;
    }

    /**
     * @inheritdoc
     */
    public function getThumbnail($size = 'post-thumbnail', $attrs = [])
    {
        return get_the_post_thumbnail($this->getId(), $size, $attrs);
    }

    /**
     * @inheritdoc
     */
    public function getThumbnailUrl($size = 'post-thumbnail')
    {
        return get_the_post_thumbnail_url($this->getId(), $size);
    }

    /**
     * @inheritdoc
     */
    public function getTitle($raw = false)
    {
        $title = (string)$this->get('post_title', '');

        return $raw ? $title : apply_filters('the_title', $title, $this->getId());
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return (string)$this->get('post_type', '');
    }

    /**
     * @inheritdoc
     */
    public function hasTerm($term, string $taxonomy): bool
    {
        return has_term($term, $taxonomy, $this->getPost());
    }

    /**
     * @inheritdoc
     */
    public function inTypes(array $post_types): bool
    {
        return in_array($this->getType(), $post_types);
    }

    /**
     * @inheritdoc
     */
    public function update($postdata)
    {
        $p = ParamsBag::createFromAttrs($postdata);
        $columns = database()->getConnection()->getSchemaBuilder()->getColumnListing('posts');
        $db = database('posts');

        $update = [];
        foreach($columns as $col) {
            if ($p->has($col)) {
                $update[$col] = $p->get($col);
            }
        }
        if ($update) {
            $db->where(['ID' => $this->getId()])->update($update);
        }

        if ($p->has('meta')) {
            foreach($p->get('meta') as $k => $v) {
                if ($this->canUpdateMeta($k, $v)) {
                    database('postmeta')->updateOrInsert(
                        ['post_id' => $this->getId(), 'meta_key' => $k],
                        ['meta_value' => maybe_serialize($v)]
                    );
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function canUpdateMeta(string $meta_key, $meta_value): bool
    {
        return in_array($meta_key, $this->getMetaKeys());
    }
}