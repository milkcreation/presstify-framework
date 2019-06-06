<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\Meta\PostMeta as CorcelPostmeta;
use Exception;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Postmeta
 * @package tiFy\Wordpress\Database\Model
 *
 * @mixin Builder
 */
class Postmeta extends CorcelPostmeta
{
    /**
     * @return mixed
     */
    public function getValueAttribute()
    {
        try {
            $value = maybe_unserialize($this->meta_value);

            return $value === false && $this->meta_value !== false ?
                $this->meta_value :
                $value;
        } catch (Exception $ex) {
            return $this->meta_value;
        }
    }
}
