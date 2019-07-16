<?php
/**
 * Bouton de bascule
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\PartialView $this
 */
?>

<?php
echo partial('tag', [
    'tag'     => 'a',
    'attrs'   => [
        'href'         => '#' . $this->get('attrs.id'),
        'class'        => 'Sidebar-toggle',
        'data-control' => 'sidebar.toggle',
        'data-toggle'  => '#' . $this->get('attrs.id')
    ],
    'content' =>
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 75 75" xml:space="preserve" 
            fill="' . ($this->get('theme') === 'light' ? '#2B2B2B' : '#FFF') . '"
        >
            <g>
                <rect width="75" height="10" x="0" y="0" ry="0"/>
                <rect width="75" height="10" x="0" y="22" ry="0"/>
                <rect width="75" height="10" x="0" y="44" ry="0"/>
            </g>
        </svg>'
]);
