<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\Term as CorcelTerm;
use tiFy\Database\Concerns\ColumnsAwareTrait;
use tiFy\Wordpress\Contracts\Database\TaxonomyBuilder;

/**
 * @method static TaxonomyBuilder hasMeta(string|array $meta_key, mixed|null $value, string $operator = '=')
 * @method static TaxonomyBuilder hasMetaLike(string $key, string $value)
 */
class Term extends CorcelTerm implements TaxonomyBuilder
{
    use ColumnsAwareTrait;
}
