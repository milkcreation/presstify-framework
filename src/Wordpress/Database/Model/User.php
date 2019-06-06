<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\User as CorcelUser;
use tiFy\Wordpress\Contracts\UserBuilder;

class User extends CorcelUser implements UserBuilder
{
    /**
     * Nom de qualification de la connexion associé.
     * @var string
     */
    protected $connection = 'wp';
}