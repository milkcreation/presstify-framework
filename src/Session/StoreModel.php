<?php declare(strict_types=1);

namespace tiFy\Session;

use tiFy\Database\Model;

class StoreModel extends Model
{
    /**
     * Nom de qualification de la table.
     * @var string
     */
    protected $table = 'tify_session';
}
