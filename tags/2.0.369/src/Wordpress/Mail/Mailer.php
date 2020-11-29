<?php declare(strict_types=1);

namespace tiFy\Wordpress\Mail;

use tiFy\Mail\Mailer as Manager;

class Mailer
{
    /**
     * Instance du gestionnaire de mail
     * @var Manager|null
     */
    protected $manager;

    /**
     * @param Manager $manager
     *
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;

        $admin_email = get_option('admin_email');
        $admin_name = ($user = get_user_by('email', get_option('admin_email'))) ? $user->display_name : '';

        Manager::setDefaults([
            'from'         => [$admin_email, $admin_name],
            'to'           => [$admin_email, $admin_name],
            'subject'      => sprintf(__('Test d\'envoi de mail depuis le site %s', 'tify'), get_bloginfo('blogname')),
            'charset'      => get_bloginfo('charset'),
        ]);

        $this->manager->boot();
    }
}