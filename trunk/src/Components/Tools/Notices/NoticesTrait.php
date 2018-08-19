<?php

namespace tiFy\Components\Tools\Notices;

use tiFy\Kernel\Tools;

/**
 * Trait NoticesTrait
 * @package tiFy\Components\Tools\Notices
 *
 * @method string noticesAdd(string $type, string $message = '', array $datas = [])
 * @method array noticesAll(string $type)
 * @method array noticesGetDatas(string $type)
 * @method array noticesGetMessages(string $type)
 * @method bool noticesHasType(string $type)
 * @method array noticesQuery(string $type, array $query_args = [])
 * @method void noticesSetType(string $type)
 * @method void noticesSetTypes(array $types = ['error', 'warning', 'info', 'success'])
 */
trait NoticesTrait
{
    public function __call($name, $arguments)
    {
        if (preg_match('#^notices(.*)#', $name, $matches)) :
            $method = lcfirst($matches[1]);
            if (method_exists(Tools::Notices(), $method)) :
                return call_user_func_array([Tools::Notices(), $method], $arguments);
            endif;
        endif;
    }
}
