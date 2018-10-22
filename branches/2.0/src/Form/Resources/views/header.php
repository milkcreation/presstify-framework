<?php
/**
 * Entête du formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FormView $this
 */
?>

<?php
    if ($errors) :
        echo partial(
            'notice',
             [
                 'type'    => 'error',
                 'content' => $this->fetch('errors', ['errors' => $this->get('errors', [])])
             ]
        );
    endif;
?>