<?php declare(strict_types=1);

namespace tiFy\Wordpress\Query;

use tiFy\Wordpress\Contracts\QueryComment as QueryCommentContract;
use tiFy\Wordpress\Contracts\QueryPost as QueryPostContract;
use tiFy\Wordpress\Contracts\QueryUser as QueryUserContract;
use tiFy\Support\ParamsBag;
use tiFy\Support\DateTime;
use WP_Comment;

class QueryComment extends ParamsBag implements QueryCommentContract
{
    /**
     * Instance de commentaire Wordpress.
     * @var WP_Comment
     */
    protected $wp_comment;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Comment $wp_comment Instance de commentaire Wordpress.
     *
     * @return void
     */
    public function __construct(WP_Comment $wp_comment)
    {
        $this->wp_comment = $wp_comment;

        $this->set($this->wp_comment->to_array())->parse();
    }

    /**
     * @inheritdoc
     */
    public static function createFromId(int $comment_id): ?QueryCommentContract
    {
        return (($wp_comment = get_comment($comment_id)) && ($wp_comment instanceof WP_Comment))
            ? new static($wp_comment) : null;
    }

    /**
     * @inheritdoc
     */
    public function getAgent(): string
    {
        return $this->get('comment_agent', '');
    }

    /**
     * @inheritdoc
     */
    public function getAuthor(): string
    {
        return $this->get('comment_author', '');
    }

    /**
     * @inheritdoc
     */
    public function getAuthorEmail(): string
    {
        return $this->get('comment_author_email', '');
    }

    /**
     * @inheritdoc
     */
    public function getAuthorIp(): string
    {
        return $this->get('comment_author_ip', '');
    }

    /**
     * @inheritdoc
     */
    public function getAuthorUrl(): string
    {
        return $this->get('comment_author_url', '');
    }

    /**
     * @inheritdoc
     */
    public function getContent(): string
    {
        return $this->get('comment_content', '');
    }

    /**
     * @inheritdoc
     */
    public function getDate(bool $gmt = false): string
    {
        return $gmt
            ? (string)$this->get('comment_date_gmt', '')
            : (string)$this->get('comment_date', '');
    }

    /**
     * @inheritdoc
     */
    public function getDateTime(bool $gmt = false): DateTime
    {
        return Datetime::createFromTimeString($this->getDate($gmt));
    }

    /**
     * @inheritdoc
     */
    public function getEditLink(): string
    {
        return get_edit_comment_link($this->getId());
    }

    /**
     * @inheritdoc
     */
    public function getId(): int
    {
        return intval($this->get('comment_ID', 0));
    }

    /**
     * @inheritdoc
     */
    public function getMeta(string $meta_key, bool $single = false, $default = null)
    {
        return get_comment_meta($this->getId(), $meta_key, $single) ?: $default;
    }

    /**
     * @inheritdoc
     */
    public function getMetaMulti(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, false, $default);
    }

    /**
     * @inheritdoc
     */
    public function getMetaSingle(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, true, $default);
    }

    /**
     * @inheritdoc
     */
    public function getParent(): ?QueryCommentContract
    {
        return self::createFromId($this->getParentId());
    }

    /**
     * @inheritdoc
     */
    public function getParentId(): int
    {
        return intval($this->get('comment_parent', 0));
    }

    /**
     * @inheritdoc
     */
    public function getPost(): QueryPostContract
    {
        return QueryPost::createFromId($this->getPostId());
    }

    /**
     * @inheritdoc
     */
    public function getPostId(): int
    {
        return intval($this->get('comment_post_ID', 0));
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->get('comment_type', '');
    }

    /**
     * @inheritdoc
     */
    public function getUser(): QueryUserContract
    {
        return QueryUser::createFromId($this->getUserId());
    }

    /**
     * @inheritdoc
     */
    public function getUserId(): int
    {
        return intval($this->get('user_id', 0));
    }

    /**
     * @inheritdoc
     */
    public function getWpComment(): WP_Comment
    {
        return $this->wp_comment;
    }

    /**
     * @inheritdoc
     */
    public function inTypes(array $comment_types): bool
    {
        return in_array($this->getType(), $comment_types);
    }

    /**
     * @inheritdoc
     */
    public function isApproved(): bool
    {
        return $this->get('comment_approved', '') == 1;
    }

    /**
     * @inheritdoc
     */
    public function isSpam(): bool
    {
        return $this->get('comment_approved', '') === 'spam';
    }
}