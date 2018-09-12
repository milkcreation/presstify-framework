<?php

namespace tiFy\User\Query;

use tiFy\Contracts\User\UserQueryInterface;
use tiFy\User\Query\UserQueryCollection;
use tiFy\User\Query\UserQueryItem;

class UserQuery implements UserQueryInterface
{
    /**
     * Role(s) utilisateur Wordpress.
     * @var string|array
     */
    protected $objectName = [];

    /**
     * Controleur de données d'un élément.
     * @var string
     */
    protected $itemController = UserQueryItem::class;

    /**
     * Controleur de données d'une liste d'éléments.
     * @var string
     */
    protected $collectionController = UserQueryCollection::class;

    /**
     * {@inheritdoc}
     */
    public function getCollection($query_args = [])
    {
        $user_query = new \WP_User_Query($query_args);

        if ($user_query->get_total()) :
            $items = array_map([$this, 'getItem'], $user_query->get_results());
        else :
            $items = [];
        endif;

        return $this->resolveCollection($items);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id = null)
    {
        if (!$id) :
            $user = \wp_get_current_user();
        elseif (is_numeric($id) && $id > 0) :
            $user = get_userdata($id);
        elseif (is_string($id)) :
            return $this->getBy(null, $id);
        else :
            $user = $id;
        endif;

        if (!$user instanceof \WP_User) :
            return null;
        endif;

        if ($this->getObjectName() && !array_intersect($user->roles, (array) $this->getObjectName())) :
            return null;
        endif;

        $alias = 'user.query.user.' . $user->ID;
        if (!app()->has($alias)) :
            app()->singleton(
                $alias,
                function() use ($user) {
                    return $this->resolveItem($user);
                }
            );
        endif;

        return app()->resolve($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemBy($key = 'login', $value)
    {
        $args = [
            'search' => $value,
            'number' => 1
        ];

        switch($key) :
            default :
            case 'user_login' :
            case 'login':
                $args['search_columns'] = ['user_login'];
                break;
            case 'user_email' :
            case 'email' :
                $args['search_columns'] = ['user_email'];
                break;
        endswitch;

        $user_query = new \WP_User_Query($args);
        if ($users = $user_query->get_results()) :
            return $this->get(reset($users));
        endif;

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCollection($items)
    {
        $concrete = $this->collectionController;

        return new $concrete($items);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveItem($wp_user)
    {
        $concrete = $this->itemController;

        return new $concrete($wp_user);
    }
}

