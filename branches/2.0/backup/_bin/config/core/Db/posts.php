<?php

return [
    'search'     => [
        'content',
        'title',
    ],
    'col_prefix' => 'post_',
    'meta'       => 'post',
    'install'    => false,
    'columns'    => [
        'status'           => [
            'default' => 'publish',
            'type'    => 'VARCHAR',
            'size'    => 20,
        ],
        'date_gmt'         => [
            'default' => '0000-00-00 00:00:00',
            'type'    => 'DATETIME',
        ],
        'content_filtered' => [
            'type' => 'LONGTEXT',
        ],
        'parent'           => [
            'default'  => 0,
            'type'     => 'BIGINT',
            'unsigned' => true,
            'size'     => 20,
        ],
        'excerpt'          => [
            'type' => 'TEXT',
        ],
        'ping_status'      => [
            'default' => 'open',
            'prefix'  => false,
            'type'    => 'VARCHAR',
            'size'    => 20,
        ],
        'date'             => [
            'default' => '0000-00-00 00:00:00',
            'type'    => 'DATETIME',
        ],
        'menu_order'       => [
            'default' => 0,
            'prefix'  => false,
            'type'    => 'INT',
            'size'    => 11,
        ],
        'guid'             => [
            'default' => '',
            'prefix'  => false,
            'type'    => 'VARCHAR',
            'size'    => 255,
        ],
        'ID'               => [
            'auto_increment' => true,
            'prefix'         => false,
            'type'           => 'BIGINT',
            'unsigned'       => true,
            'size'           => 20,
        ],
        'comment_status'   => [
            'default' => 'open',
            'prefix'  => false,
            'type'    => 'VARCHAR',
            'size'    => 20,
        ],
        'modified_gmt'     => [
            'default' => '0000-00-00 00:00:00',
            'type'    => 'DATETIME',
        ],
        'password'         => [
            'default' => '',
            'type'    => 'VARCHAR',
            'size'    => 20,
        ],
        'name'             => [
            'default' => '',
            'type'    => 'VARCHAR',
            'size'    => 200,
        ],
        'title'            => [
            'type' => 'TEXT',
        ],
        'to_ping'          => [
            'prefix' => false,
            'type'   => 'TEXT',
        ],
        'author'           => [
            'default'  => 0,
            'type'     => 'BIGINT',
            'unsigned' => true,
            'size'     => 20,
        ],
        'modified'         => [
            'default' => '0000-00-00 00:00:00',
            'type'    => 'DATETIME',
        ],
        'content'          => [
            'type' => 'LONGTEXT',
        ],
        'comment_count'    => [
            'default' => 0,
            'prefix'  => false,
            'type'    => 'BIGINT',
            'size'    => 20,
        ],
        'pinged'           => [
            'prefix' => false,
            'type'   => 'TEXT',
        ],
        'type'             => [
            'default' => 'post',
            'type'    => 'VARCHAR',
            'size'    => 20,
        ],
        'mime_type'        => [
            'default' => '',
            'type'    => 'VARCHAR',
            'size'    => 100,
        ],
    ]
];