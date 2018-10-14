<?php

namespace tiFy\Wp;

use tiFy\Contracts\Wp\Ctags;

class WpCtags implements Ctags
{
    /**
     * Liste des indicateurs de condition permis.
     * @see https://codex.wordpress.org/Conditional_Tags
     * @var array
     */
    protected $items = [
        '404'               => 'is_404',
        'archive'           => 'is_archive',
        'attachment'        => 'is_attachment',
        'author'            => 'is_author',
        'category'          => 'is_category',
        'date'              => 'is_date',
        'day'               => 'is_day',
        'front'             => 'is_front_page',
        'home'              => 'is_home',
        'month'             => 'is_month',
        'page'              => 'is_page',
        'paged'             => 'is_paged',
        'post_type_archive' => 'is_post_type_archive',
        'search'            => 'is_search',
        'single'            => 'is_single',
        'singular'          => 'is_singular',
        'sticky'            => 'is_sticky',
        'tag'               => 'is_tag',
        'tax'               => 'is_tax',
        'template'          => 'is_template',
        'time'              => 'is_time',
        'year'              => 'is_year'
    ];

    /**
     * {@inheritdoc}
     */
    public function is($ctags = null)
    {
        if (is_null($ctags)) :
            return false;
        elseif (preg_match('#^([\w]+)@wp$#', $ctags, $matches)) :
            $ctags = $matches[1];
        endif;

        if (!isset($this->items[$ctags])) :
            return false;
        else :
            return call_user_func($this->items[$ctags]);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (is_404()) :
            return '404';
        elseif (is_search()) :
            return 'search';
        elseif (is_front_page()) :
            return 'front';
        elseif (is_home()) :
            return 'home';
        elseif (is_post_type_archive()) :
            return 'post_type_archive';
        elseif (is_tax()) :
            return 'tax';
        elseif (is_attachment()) :
            return 'attachment';
        elseif (is_single()) :
            return 'single';
        elseif (is_page()) :
            return 'page';
        elseif (is_singular()) :
            return 'singular';
        elseif (is_category()) :
            return 'category';
        elseif (is_tag()) :
            return 'tag';
        elseif (is_author()) :
            return 'author';
        elseif (is_date()) :
            return 'date';
        elseif (is_archive()) :
            return 'archive';
        else :
            return null;
        endif;
    }
}