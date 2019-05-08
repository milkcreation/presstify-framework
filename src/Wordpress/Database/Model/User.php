<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\User as CorcelUser;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class User
 * @package tiFy\Wordpress\Database\Model
 *
 * @mixin Builder
 */
class User extends CorcelUser
{

}
