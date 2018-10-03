<?php

namespace tiFy\Layout\Share\AjaxListTable\Params;

use tiFy\Layout\Share\ListTable\Params\ParamsController as ShareListTableParamsController;

class ParamsController extends ShareListTableParamsController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $attrs = array_merge(
            parent::defaults(),
            [
                'columns'      => [
                    'cb',
                    'email' => __('Email', 'theme'),
                ],
                'items'         => [
                    [
                        'email' => 's.wonder@domain.ltd'
                    ],
                    [
                        'email' => 'l.kravitz@domain.ltd'
                    ],
                    [
                        'email' => 'f.mercury@domain.ltd'
                    ]
                ]
            ]
        );

        return $attrs;
    }
}