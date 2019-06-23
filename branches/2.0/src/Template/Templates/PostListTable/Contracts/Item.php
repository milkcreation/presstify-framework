<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable\Contracts;

use tiFy\Template\Templates\ListTable\Contracts\Item as BaseItem;
use tiFy\Wordpress\Contracts\QueryPost;

interface Item extends BaseItem, QueryPost
{

}