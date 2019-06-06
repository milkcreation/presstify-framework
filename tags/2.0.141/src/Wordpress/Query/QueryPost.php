<?php declare(strict_types=1);

namespace tiFy\Wordpress\Query;

use tiFy\Support\DateTime;
use tiFy\Support\ParamsBag;
use tiFy\Wordpress\Contracts\QueryComment as QueryCommentContract;
use tiFy\Wordpress\Contracts\QueryPost as QueryPostContract;
use tiFy\Wordpress\Contracts\PostBuilder;
use tiFy\Wordpress\Database\Model\Post as Model;
use WP_Post;
use WP_Query;
use WP_Term_Query;
use WP_User;

class QueryPost extends ParamsBag implements QueryPostContract
{
    /**
     * Instance du modèle de base de données associé.
     * @var PostBuilder
     */
    protected $db;

    /**
     * Instance de post Wordpress.
     * @var WP_Post
     */
    protected $wp_post;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Post $wp_post Instance de post Wordpress.
     *
     * @return void
     */
    public function __construct(WP_Post $wp_post)
    {
        $this->wp_post = $wp_post;

        $this->set($this->wp_post->to_array())->parse();
    }

    /**
     * @inheritDoc
     */
    public static function createFromGlobal(): ?QueryPostContract
    {
        global $post;

        return $post instanceof WP_Post ? new static($post) : null;
    }

    /**
     * @inheritDoc
     */
    public static function createFromId($post_id): ?QueryPostContract
    {
        return ($post_id && is_numeric($post_id) && ($wp_post = get_post($post_id)) && ($wp_post instanceof WP_Post))
            ? new static($wp_post) : null;
    }

    /**
     * @inheritDoc
     */
    public static function createFromName(string $post_name): ?QueryPostContract
    {
        return (($wp_post = (new WP_Query())->query(['name' => $post_name, 'post_type' => 'any', 'posts_per_page' => 1]))
            && ($wp_post[0] instanceof WP_Post)) ? new static($wp_post[0]) : null;
    }

    /**
     * @inheritDoc
     */
    public function db(): PostBuilder
    {
        if (!$this->db) {
            $this->db = (new Model())->find($this->getId());
        }

        return $this->db;
    }

    /**
     * @inheritDoc
     */
    public function getAuthorId()
    {
        return intval($this->get('post_author', 0));
    }

    /**
     * @inheritDoc
     */
    public function getClass(array $classes = [], bool $html = true)
    {
        $classes = get_post_class($classes, $this->getId());

        return $html ? 'class="' . join(' ', $classes) . '"' : $classes;
    }

    /**
     * @inheritDoc
     */
    public function getComment(int $id): ?QueryCommentContract
    {
        return ($res = QueryComment::createFromId($id)) && ($res->getPostId() === $this->getId()) ? $res : null;
    }

    /**
     * @inheritDoc
     */
    public function getComments(array $args = []): iterable
    {
        return QueryComments::createFromArgs(array_merge(['post_id' => $this->getId()], $args)) ?: [];
    }

    /**
     * @inheritDoc
     */
    public function getContent(bool $raw = false)
    {
        $content = (string)$this->get('post_content', '');

        if (!$raw) :
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);
        endif;

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function getDate(bool $gmt = false)
    {
        return $gmt
            ? strval($this->get('post_date_gmt', ''))
            : strval($this->get('post_date', ''));
    }

    /**
     * @inheritDoc
     */
    public function getDateTime(bool $gmt = false): DateTime
    {
        return Datetime::createFromTimeString($this->getDate($gmt));
    }

    /**
     * @inheritDoc
     */
    public function getEditLink()
    {
        return get_edit_post_link($this->getId());
    }

    /**
     * @inheritDoc
     */
    public function getExcerpt(bool $raw = false)
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
     * @inheritDoc
     */
    public function getGuid()
    {
        return strval($this->get('guid', ''));
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return intval($this->get('ID', 0));
    }

    /**
     * @inheritDoc
     */
    public function getMeta(string $meta_key, $single = false, $default = null)
    {
        return get_post_meta($this->getId(), $meta_key, $single) ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeys(bool $registered = true): array
    {
        if ($registered) {
            return post_type()->post_meta()->keys($this->getType());
        } else {
            return get_post_custom_keys($this->getId()) ?: [];
        }
    }

    /**
     * @inheritDoc
     */
    public function getMetaMulti(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, false, $default);
    }

    /**
     * @inheritDoc
     */
    public function getMetaSingle(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, true, $default);
    }

    /**
     * @inheritDoc
     */
    public function getModified(bool $gmt = false)
    {
        return $gmt
            ? strval($this->get('post_modified_gmt', ''))
            : strval($this->get('post_modified', ''));
    }

    /**
     * @inheritDoc
     */
    public function getModifiedDateTime(bool $gmt = false): DateTime
    {
        return Datetime::createFromTimeString($this->getModified($gmt));
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getSlug();
    }

    /**
     * @inheritDoc
     */
    public function getParentId()
    {
        return (int)$this->get('post_parent', 0);
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return rtrim(str_replace(home_url(), '', $this->getPermalink()), '/');
    }

    /**
     * @inheritDoc
     */
    public function getPermalink()
    {
        return get_permalink($this->getId());
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated
     */
    public function getPost()
    {
        return $this->getWpPost();
    }

    /**
     * @inheritDoc
     */
    public function getSlug()
    {
        return (string)$this->get('post_name', '');
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return (string)$this->get('post_status', '');
    }

    /**
     * @inheritDoc
     */
    public function getTerms($taxonomy, $args = [])
    {
        $args['taxonomy'] = $taxonomy;
        $args['object_ids'] = $this->getId();

        return (new WP_Term_Query($args))->terms;
    }

    /**
     * @inheritDoc
     */
    public function getThumbnail($size = 'post-thumbnail', $attrs = [])
    {
        return get_the_post_thumbnail($this->getId(), $size, $attrs);
    }

    /**
     * @inheritDoc
     */
    public function getThumbnailUrl($size = 'post-thumbnail')
    {
        return get_the_post_thumbnail_url($this->getId(), $size);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(bool $raw = false)
    {
        $title = (string)$this->get('post_title', '');

        return $raw ? $title : apply_filters('the_title', $title, $this->getId());
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return (string)$this->get('post_type', '');
    }

    /**
     * @inheritDoc
     */
    public function getWpPost()
    {
        return $this->wp_post;
    }

    /**
     * @inheritDoc
     */
    public function hasTerm($term, string $taxonomy): bool
    {
        return has_term($term, $taxonomy, $this->getWpPost());
    }

    /**
     * @inheritDoc
     */
    public function save($postdata): void
    {
        $p = ParamsBag::createFromAttrs($postdata);
        $columns =  $this->db()->getConnection()->getSchemaBuilder()->getColumnListing($this->db()->getTable());

        $update = [];
        foreach ($columns as $col) {
            if ($p->has($col)) {
                $update[$col] = $p->get($col);
            }
        }

        if ($update) {
            $this->db()->where(['ID' => $this->getId()])->update($update);
        }

        if ($p->has('meta')) {
            $this->saveMeta($p->get('meta'));
        }
    }

    /**
     * @inheritDoc
     */
    public function saveComment(string $content, array $commentdata = [], ?WP_User $wp_user = null): int
    {
        $user = $wp_user ? new QueryUser($wp_user) : QueryUser::createFromGlobal();

        $commentdata = array_merge([
            'comment_ID'           => 0,
            'comment_post_ID'      => $this->getId(),
            'comment_author'       => $user->getDisplayName(),
            'comment_author_email' => $user->getEmail(),
            'comment_author_url'   => $user->getUrl(),
            'comment_author_IP'    => request()->ip(),
            'comment_content'      => $content,
            'comment_agent'        => request()->userAgent(),
            'comment_type'         => '',
            'comment_parent'       => 0,
            'comment_approved'     => 1,
            'user_id'              => $user->getId(),
            'meta'                 => []
        ], $commentdata);

        if ($comment_id = wp_insert_comment($commentdata)) {
            foreach ($commentdata['meta'] as $k => $v) {
                add_comment_meta($comment_id, $k, $v);
            }
            return $comment_id;
        } else {
            return 0;
        }
    }

    /**
     * @inheritDoc
     */
    public function saveMeta($key, $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $k => $v) {
            $this->db()->saveMeta($k, $v);
        }
    }

    /**
     * @inheritDoc
     */
    public function typeIn(array $post_types): bool
    {
        return in_array($this->getType(), $post_types);
    }
}