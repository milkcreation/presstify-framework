<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\Meta\CommentMeta as CorcelCommentmeta;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Commentmeta
 * @package tiFy\Wordpress\Database\Model
 *
 * @mixin Builder
 */
class Commentmeta extends CorcelCommentmeta
{

}
