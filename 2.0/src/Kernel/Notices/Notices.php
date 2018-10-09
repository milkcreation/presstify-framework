<?php

namespace tiFy\Kernel\Notices;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\Contracts\Kernel\NoticesInterface;

class Notices implements NoticesInterface
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function all($type)
    {
        return Arr::get($this->items, $type, []);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($type)
    {
        return in_array($type, $this->types);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function setType($type)
    {
        if (!$this->hasType($type)) :
            array_push($type, $this->types);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function setTypes($types = ['error', 'warning', 'info', 'success'])
    {
        $this->types = (array)$types;
    }
}
