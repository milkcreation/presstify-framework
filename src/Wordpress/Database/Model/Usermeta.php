<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\Meta\UserMeta as CorcelUsermeta;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
class Usermeta extends CorcelUsermeta
{
    /**
     * Nom de qualification de la connexion associé.
     * @var string
     */
    protected $connection = 'wp';
}
