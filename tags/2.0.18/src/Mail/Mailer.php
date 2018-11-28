<?php

namespace tiFy\Mail;

use Html2Text\Html2Text;
use Pelago\Emogrifier;
use tiFy\Contracts\Mail\LibraryAdapter;
use tiFy\Contracts\Mail\Mailer as MailerContract;
use tiFy\Contracts\Mail\MailQueue;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Mail\MessageViewController;

final class Mailer extends ParamsBag implements MailerContract
{
    /**
     * Instance du pilote de traitement de mail.
     * @var LibraryAdapter
     */
    protected $lib;

    /**
     * Instance du controleur de gabarits d'affichage.
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'after_setup_theme',
            function () {
                parent::__construct(config('mail', []));
            }
        );
    }

    /**
     * Traitement récursif d'une liste de pièces jointes.
     *
     * @param string|string[]|array $attachments
     *
     * @return array
     */
    private function _parseAttachments($attachments)
    {
        $output = (func_num_args() === 2) ? func_get_arg(1) : [];

        if (is_string($attachments)) :
            if (is_file($attachments)) :
                $output[] = [$attachments];
            endif;
        elseif (is_array($attachments)) :
            foreach ($attachments as $a) :
                if (is_string($a)) :
                    $output = $this->_parseAttachments($c, $output);
                elseif (is_array($a)) :
                    $filename = $a[0] ?? null;

                    if ($filename && is_file($filename)) :
                        $output[] = $a;
                    endif;
                endif;
            endforeach;
        endif;

        return $output;
    }

    /**
     * Traitement récursif d'une liste de contacts.
     *
     * @param string|string[]|array $contacts Liste de contact.
     * {@internal "{email}"|"{name} {email}"|["{email1}", ["{name2} {email2}"]]}
     *
     * @return array
     */
    private function _parseContacts($contacts)
    {
        $output = (func_num_args() === 2) ? func_get_arg(1) : [];

        if (is_string($contacts)) :
            $email = '';
            $name = '';
            $bracket_pos = strpos($contacts, '<');
            if ($bracket_pos !== false) :
                if ($bracket_pos > 0) :
                    $name = substr($contacts, 0, $bracket_pos - 1);
                    $name = str_replace('"', '', $name);
                    $name = trim($name);
                endif;

                $email = substr($contacts, $bracket_pos + 1);
                $email = str_replace('>', '', $email);
                $email = trim($email);
            elseif (!empty($contacts)) :
                $email = $contacts;
            endif;

            if ($email && is_email($email)) :
                $output[] = [$email, $name];
            endif;
        elseif (is_array($contacts)) :
            if ((count($contacts) === 2) &&
                isset($contacts[0]) && isset($contacts[1]) &&
                is_string($contacts[0]) && is_string($contacts[1])
            ) :
                if (is_email($contacts[0]) && !is_email($contacts[1])) :
                    $output[] = array_map('trim', $contacts);
                endif;
            else :
                foreach ($contacts as $c) :
                    if (is_string($c)) :
                        $output = $this->_parseContacts($c, $output);
                    elseif (is_array($c)) :
                        $email = $c[0] ?? null;
                        $name = $c[1] ?? '';

                        if ($email && is_email($email)) :
                            $output[] = [$email, $name];
                        endif;
                    endif;
                endforeach;
            endif;
        endif;

        return $output;
    }

    /**
     * Traitement de remplacement des variables d'environnement.
     *
     * @param string $output
     * @param array Liste des variables d'environnement personnalisées.
     * @param string $regex Format de détection des variables.
     *
     * @return string
     */
    private function _parseMergeVars($output, $vars = [], $regex = '\*\|(.*?)\|\*')
    {
        $vars = array_merge(
            [
                'SITE:URL'         => site_url('/'),
                'SITE:NAME'        => get_bloginfo('name'),
                'SITE:DESCRIPTION' => get_bloginfo('description'),
            ],
            $vars
        );

        $callback = function ($matches) use ($vars) {
            if (!isset($matches[1])) :
                return $matches[0];
            elseif (isset($vars[$matches[1]])) :
                return $vars[$matches[1]];
            endif;

            return $matches[0];
        };

        $output = preg_replace_callback('/' . $regex . '/', $callback, $output);

        return $output;
    }

    /**
     * Traitement des élements texte de composition du message.
     *
     * @param string|string[] $body {body}|[{html_body},{plain_body}]
     * @param string|string[] $header {header}|[{html_header},{plain_header}]
     * @param string|string[] $footer {footer}|[{html_footer},{plain_footer}]
     *
     * @return string
     */
    private function _parseMessage($body, $header = '', $footer = '')
    {
        $header = $this->_parseTextParts($header);
        $footer = $this->_parseTextParts($footer);
        $message = $this->_parseTextParts($body);

        array_walk($message, function (&$item, $key) use ($header, $footer) {
            $item = $header[$key] . $item . $footer[$key];
        });

        return $message;
    }

    /**
     * Traitement des éléments de texte composant le message (body|header|footer).
     *
     * @param string|array $part
     *
     * @return string
     */
    private function _parseTextParts($part)
    {
        if (is_string($part)) :
            $part = [$part, (new Html2Text($part))->getText()];
        elseif (is_array($part)) :
            $html = $part[0] ?? '';
            $text = $part[1] ?? (new Html2Text($html))->getText();

            $part = [$html, $text];
        endif;

        return $part;
    }

    /**
     * Traitement de la liste des paramètres de configuration.
     *
     * @param array $params Liste des paramètres de configuration.
     *
     * @return array
     */
    private function _parseParams($params = [])
    {
        $lib = $this->getLib();

        $pieces = [
            'from', 'to', 'replyTo', 'bcc', 'cc', 'attachments',
            'charset', 'encoding', 'content_type',
            'subject', 'footer', 'header', 'body', 'css'
        ];

        $from = $this->_parseContacts($params['from'] ?? $this->get('from'));
        $to = $this->_parseContacts($params['to'] ?? $this->get('to'));
        $replyTo = $this->_parseContacts($params['reply-to'] ?? $this->get('reply-to', []));
        $bcc = $this->_parseContacts($params['bcc'] ?? $this->get('bcc', []));
        $cc = $this->_parseContacts($params['cc'] ?? $this->get('cc', []));
        $attachments = $this->_parseAttachments($params['attachments'] ?? $this->get('attachments', []));
        $charset = $params['charset'] ?? $this->get('charset');
        $encoding = $params['encoding'] ?? $this->get('encoding');
        $content_type = $params['content_type'] ?? $this->get('content_type');
        $subject = $params['subject'] ?? $this->get('subject', '');
        $css = $params['css'] ?? $this->get('css', '');
        $header = $params['header'] ?? $this->get('header', '');
        $footer = $params['footer'] ?? $this->get('footer', '');
        $inline_css = $params['inline_css'] ?? $this->get('inline_css', '');

        call_user_func_array([$lib, 'setFrom'], current($from));

        foreach ($to as $contact) :
            call_user_func_array([$lib, 'addTo'], $contact);
        endforeach;

        foreach ($replyTo as $contact) :
            call_user_func_array([$lib, 'addReplyTo'], $contact);
        endforeach;

        foreach ($bcc as $contact) :
            call_user_func_array([$lib, 'addBcc'], $contact);
        endforeach;

        foreach ($cc as $contact) :
            call_user_func_array([$lib, 'addCc'], $contact);
        endforeach;

        foreach ($attachments as $attachment) :
            call_user_func_array([$lib, 'addAttachment'], $attachment);
        endforeach;

        $lib->setCharset($charset);

        $lib->setEncoding($encoding);

        $lib->setContentType($content_type);

        $lib->setSubject($subject);

        $body = $params['body'] ?? [
            (string)$this->viewer('default', compact($pieces)),
            sprintf(__('Ceci est un test d\'envoi de mail depuis le site %s', 'tify'), get_bloginfo('blogname')) . "\n\n" .
            __('Si ce mail, vous est parvenu c\'est qu\'il vous a été expédié depuis le site : '). "\n" .
            site_url('/'). "\n\n" .
            __('Néanmoins, il pourrait s\'agir d\'une erreur. Si vous n\'êtes pas concerné par cet e-mail, ', 'tify'). "\n" .
            __('vous pouvez prendre contact avec l\'administrateur du site à cette adresse : ', 'tify'). "\n" .
            get_option('admin_email'). "\n\n" .
            __('Merci de votre compréhension', 'tify')
        ];

        $message = $this->_parseMessage($body, $header, $footer);

        $html = (string)$this->viewer('message', array_merge(compact($pieces), ['message' => $message[0]]));
        $html = $inline_css ? (new Emogrifier($html))->emogrify() : $html;
        $plain = $message[1];

        switch($content_type) :
            case 'multipart/alternative' :
                call_user_func([$lib, 'setBody'], $html);
                call_user_func([$lib, 'setAlt'], $plain);
                break;
            case 'text/html' :
                call_user_func([$lib, 'setBody'], $html);
                break;
            case 'text/plain' :
                call_user_func([$lib, 'setBody'], $plain);
                break;
        endswitch;

        return compact($pieces, 'message', 'html', 'plain');
    }

    /**
     * {@inheritdoc}
     */
    public function debug($params = [])
    {
        $params = $this->_parseParams($params);

        echo ($this->getLib()->prepare())
            ? $this->viewer('debug', array_merge($params, ['headers' => $this->getLib()->getHeaders()]))
            : '';
        exit;
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $admin_email = get_option('admin_email');
        $admin_name = ($user = get_user_by('email', get_option('admin_email'))) ? $user->display_name : '';

        return [
            'to'           => [$admin_email, $admin_name],
            'from'         => [$admin_email, $admin_name],
            'reply-to'     => [],
            'bcc'          => [],
            'cc'           => [],
            'attachments'  => [],
            'header'       => '',
            'footer'       => '',
            'subject'      => sprintf(__('Test d\'envoi de mail depuis le site %s', 'tify'), get_bloginfo('blogname')),
            'charset'      => get_bloginfo('charset'),
            'encoding'     => '8bit',
            'content_type' => 'multipart/alternative',
            'inline_css'   => true,
            'vars'         => [],
            'viewer'       => []
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getLib()
    {
        if (!$this->lib) :
            $this->lib = app('mailer.library');
        endif;

        return $this->lib;
    }

    /**
     * {@inheritdoc}
     */
    public function queue($params = [], $date = 'now', $extras = [])
    {
        $params = $this->_parseParams($params);

        if ($res = $this->getLib()->prepare()) :
            $this->lib = null;

            /** @var MailQueue $queue */
            $queue = app('mail.queue');
            return $queue->add($params, $date, $extras);
        endif;

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function send($params = [])
    {
        $this->_parseParams($params);

        if($res = $this->getLib()->send()) :
            $this->lib = null;
        endif;

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) :
            $default_dir = __DIR__ . '/Resources/views';
            $this->viewer = view()
                ->setDirectory($default_dir)
                ->setController(MessageViewController::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : $default_dir
                );
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }
}