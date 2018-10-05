<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<ul <?php $this->attrs(); ?>>
    <?php $this->get('previous') ? $this->insert('previous', $this->all()) : false;?>

    <?php $this->get('numbers') ? $this->insert('numbers', $this->all()) : false;?>

    <?php $this->get('next') ? $this->insert('next', $this->all()) : false;?>
</ul>
