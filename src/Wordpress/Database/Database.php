<?php

namespace tiFy\Wordpress\Database;

use tiFy\Contracts\Database\Database as DatabaseManager;
use tiFy\Wordpress\Contracts\Database as DatabaseContract;

class Database implements DatabaseContract
{
    /**
     * Instance du controleur de gestion des bases de données.
     * @var DatabaseManager
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param DatabaseManager $manager Instance du controleur des bases de données.
     *
     * @return void
     */
    public function __construct(DatabaseManager $manager)
    {
        $this->manager = $manager;
    }
}