<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 * @var int $index
 * @var tiFy\Wordpress\Contracts\QueryPost $item
 */
?>
<li id="MetaboxRelatedPost-item--<?php echo $item->getId(); ?>"
    class="MetaboxRelatedPost-item"
    data-control="metabox.related-post.item"
>
    <?php $this->insert('item-content', compact('item')); ?>

    <?php echo field('hidden', [
        'attrs' => [
            'class' => 'MetaboxRelatedPost-itemValue',
        ],
        'name'  => $this->get('name') . '[]',
        'value' => $item->getId(),
    ]); ?>

    <?php echo field('text', [
        'attrs' => [
            'autocomplete' => 'off',
            'class'        => 'MetaboxRelatedPost-itemOrder',
            'data-control' => 'metabox.related-post.item.order',
            'readonly',
            'size'         => '1',
        ],
        'value' => $this->get('index', 0) +1
    ]); ?>

    <?php echo partial('tag', [
        'tag'   => 'a',
        'attrs' => [
            'class'        => 'MetaboxRelatedPost-itemMetasToggle',
            'href'         => "#MetaboxRelatedPost-item--{$item->getId()}",
            'data-control' => 'metabox.related-post.item.metas-toggle',
        ],
    ]); ?>

    <?php echo partial('tag', [
        'tag'     => 'span',
        'attrs'   => [
            'class'        => 'MetaboxRelatedPost-itemSort',
            'href'         => "#MetaboxRelatedPost-item--{$item->getId()}",
            'data-control' => 'metabox.related-post.item.sort',
        ],
        'content' => '...',
    ]); ?>

    <?php echo partial('tag', [
        'tag'   => 'a',
        'attrs' => [
            'class'        => 'MetaboxRelatedPost-itemButtonRemove ThemeButton--remove',
            'href'         => "#MetaboxRelatedPost-item--{$item->getId()}",
            'data-control' => 'metabox.related-post.item.remove',
        ],
    ]); ?>
</li>