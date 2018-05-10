<?php

namespace tiFy\Librairies\Notices;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait NoticesAwareTrait
{
    /**
     * Liste des types de notifications permis. error|warning|info|success.
     * @var array
     */
    protected $_noticesTypes = ['error', 'warning', 'info', 'success'];

    /**
     * Liste des notifications déclarées.
     * @var array
     */
    protected $_notices = [];

    /**
     * Définition des types de notification.
     *
     * @param array $types Liste des types de notification permis. error|warning|info|success par défaut.
     *
     * @return void
     */
    public function noticesSetTypes($types = ['error', 'warning', 'info', 'success'])
    {
        $this->_noticesTypes = (array)$types;
    }

    /**
     * Ajout d'un type de notification permis.
     *
     * @param string $type Type de notification permis.
     *
     * @return void
     */
    public function noticesSetType($type)
    {
        if (!$this->noticesHasType($type)) :
            array_push($type, $this->_noticesTypes);
        endif;
    }

    /**
     * Vérification d'existance d'un type de notification.
     *
     * @param string $type Identifiant de qualification du type à vérifier.
     *
     * @return bool
     */
    public function noticesHasType($type)
    {
        return in_array($type, $this->_noticeTypes);
    }

    /**
     * Ajout d'un message de notification.
     *
     * @param string $type Type de notification.
     * @param string $message Message de notification.
     * @param array $datas Liste des données embarquées associées.
     *
     * @return string
     */
    public function noticesAdd($type, $message = '', $datas = [])
    {
        if (!$this->noticesHasType($type)) :
            return '';
        endif;

        if (!isset($this->_notices[$type])) :
            $this->_notices[$type] = [];
        endif;

        $id = Str::random();
        $this->_notices[$type][$id] = compact('message', 'datas');

        return $id;
    }

    /**
     * Récupération de la liste des notifications par type.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function noticesAll($type)
    {
        return Arr::get($this->_notices, $type, []);
    }

    /**
     * Récupération des messages de notification par type.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function noticesGetMessages($type)
    {
        $messages = [];
        if ($notices = $this->noticesAll($type)) :
            foreach ($notices as $id => $attrs) :
                $messages[$id] = $attrs['message'];
            endforeach;
        endif;

        return $messages;
    }

    /**
     * Récupération des données embarquées associée à une notification.
     *
     * @param string $type Type de notification.
     *
     * @return array
     */
    public function noticesGetDatas($type)
    {
        $datas = [];
        if ($notices = $this->noticesAll($type)) :
            foreach ($notices as $id => $attrs) :
                $datas[$id] = $attrs['datas'];
            endforeach;
        endif;

        return $datas;
    }

    /**
     * Récupération de notification selon une liste d'arguments.
     *
     * @param string $type Type de notification.
     * @param array $query_args Liste d'arguments de données.
     *
     * @return array
     */
    public function noticesQuery($code = 'error', $query_args = [])
    {
        $results = [];
        if (!$notices = $this->noticesAll($type)) :
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
}
