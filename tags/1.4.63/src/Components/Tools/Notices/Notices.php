<?php

namespace tiFy\Components\Tools\Notices;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Notices
{
    /**
     * Liste des types de notifications permis. error|warning|info|success.
     * @var array
     */
    protected $types = ['error', 'warning', 'info', 'success'];

    /**
     * Liste des notifications déclarées.
     * @var array
     */
    protected $items = [];

    /**
     * Ajout d'un message de notification.
     *
     * @param string $type Type de notification.
     * @param string $message Message de notification.
     * @param array $datas Liste des données embarquées associées.
     *
     * @return string
     */
    public function add($type, $message = '', $datas = [])
    {
        if (!$this->hasType($type)) :
            return '';
        endif;

        if (!isset($this->items[$type])) :
            $this->items[$type] = [];
        endif;

        $id = Str::random();
        $this->items[$type][$id] = compact('message', 'datas');

        return $id;
    }

    /**
     * Récupération de la liste des notifications par type.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function all($type)
    {
        return Arr::get($this->items, $type, []);
    }

    /**
     * Récupération des données embarquées associée à une notification.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function getDatas($type)
    {
        $datas = [];
        if ($notices = $this->all($type)) :
            foreach ($notices as $id => $attrs) :
                $datas[$id] = $attrs['datas'];
            endforeach;
        endif;

        return $datas;
    }

    /**
     * Récupération des messages de notification par type.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function getMessages($type)
    {
        $messages = [];
        if ($notices = $this->all($type)) :
            foreach ($notices as $id => $attrs) :
                $messages[$id] = $attrs['message'];
            endforeach;
        endif;

        return $messages;
    }

    /**
     * Vérification d'existance d'un type de notification.
     *
     * @param string $type Identifiant de qualification du type à vérifier.
     *
     * @return bool
     */
    public function hasType($type)
    {
        return in_array($type, $this->types);
    }

    /**
     * Récupération de notification selon une liste d'arguments.
     *
     * @param string $type Type de notification.
     * @param array $query_args Liste d'arguments de données.
     *
     * @return array
     */
    public function query($type = 'error', $query_args = [])
    {
        $results = [];
        if (!$notices = $this->all($type)) :
            return $results;
        endif;

        foreach ($notices as $id => $attrs) :
            $exists = @array_intersect($attrs['datas'], $query_args);

            if ($exists !== $query_args) :
                continue;
            endif;

            $results[$id] = $attrs;
        endforeach;

        return $results;
    }

    /**
     * Ajout d'un type de notification permis.
     *
     * @param string $type Type de notification permis.
     *
     * @return void
     */
    public function setType($type)
    {
        if (!$this->hasType($type)) :
            array_push($type, $this->types);
        endif;
    }

    /**
     * Définition des types de notification.
     *
     * @param array $types Liste des types de notification permis. error|warning|info|success par défaut.
     *
     * @return void
     */
    public function setTypes($types = ['error', 'warning', 'info', 'success'])
    {
        $this->types = (array)$types;
    }
}
