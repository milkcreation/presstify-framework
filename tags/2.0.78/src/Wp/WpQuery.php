<?php

namespace tiFy\Wp;

use WP_Query;

class WpQuery
{
    /**
     * Liste des indicateurs de condition permis.
     * @see https://codex.wordpress.org/Conditional_Tags
     * @var array
     */
    protected $ctags = [
        'is_404',
        'is_archive',
        //'is_attachment',
        //'is_author',
        //'is_category',
        'is_date',
        'is_day',
        'is_front_page',
        'is_home',
        'is_month',
        //'is_page',
        'is_paged',
        //'is_post_type_archive',
        'is_search',
        //'is_single',
        //'is_singular'
        //'is_sticky'
        //'is_tag',
        //'is_tax',
        //'is_template',
        'is_time',
        'is_year'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('pre_get_posts', function (WP_Query &$wp_query) {
            if ($wp_query->is_main_query()) :
                foreach(config('wp.query', []) as $ctag => $query_args) :
                    if (in_array($ctag, $this->ctags)) :
                        if (call_user_func([$wp_query, $ctag])) :
                            foreach($query_args as $query_arg => $value) :
                                $wp_query->set($query_arg, $value);
                            endforeach;
                        endif;
                    endif;
                endforeach;
            endif;

            events()->trigger('wp.query', [&$wp_query]);
        });
    }
}