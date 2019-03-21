<?php
/**
 * Formulaire d'authentification | Entête.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\User\Signin\SigninView $this
 */
?>
<?php echo partial('notice', [
    'attrs'   => [
        'class' => '%s Signin-authInfos'
    ],
    'content' => $this->fetch('auth/infos', ['infos' => $this->getMessages('info')]),
    'type'    => 'info'
]);
?>
<?php echo partial('notice', [
    'attrs'   => [
        'class' => '%s Signin-authErrors'
    ],
    'content' => $this->fetch('auth/errors', ['errors' => $this->getMessages('error')]),
    'type'    => 'error'
]);