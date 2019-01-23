<?php
/**
 * @var tiFy\Contracts\View\ViewController $this
 * @var tiFy\Contracts\Wp\PageHookItem $item
 */
?>

<table class="form-table">
    <tbody>
    <?php
    foreach ($this->get('items', []) as $item) : ?>
        <tr>
            <th><?php echo $item->post()->getTitle(); ?></th>
            <td>
                <?php
                wp_dropdown_pages(
                    [
                        'name'             => $item->getOptionName(),
                        'post_type'        => $item->getObjectName(),
                        'selected'         => $item->post()->getId(),
                        'sort_column'      => $item->get('listorder'),
                        'show_option_none' => $item->get('show_option_none'),
                    ]
                );
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>