<?php

namespace tiFy\Form\Addon\AjaxSubmit;

use tiFy\Form\AddonController;

class AjaxSubmit extends AddonController
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        return;

        $this->events()
            ->listen('form.display.after', [$this, 'onFormDisplayAfter'], 9999)
            ->listen('request.redirect', [$this, 'onRequestRedirect'], 9999);

        add_action('wp_ajax_tify_forms_ajax_submit', [$this, 'wp_ajax']);
        add_action('wp_ajax_nopriv_tify_forms_ajax_submit', [$this, 'wp_ajax']);
    }

    /**
     * Court-circuitage de l'url de redirection à l'issue du traitement du formulaire.
     *
     * @param string $redirect Url de redirection
     *
     * @return void
     */
    public function onRequestRedirect(&$redirect_url)
    {
        $redirect_url = false;
    }

    /**
     * Mise en file du script de traitement dans le footer du site.
     *
     * @param FormItemController $form Classe de rappel du formulaire.
     *
     * @return
     */
    public function onFormDisplayAfter($form)
    {
        if (defined('DOING_AJAX')) :
            return;
        endif;

        $ID = $form->getName();
        $html_id = '#' . $form->get('form_id');

        $wp_footer = function () use ($ID, $html_id) {
            ?>
            <script type="text/javascript">/* <![CDATA[ */

                // @todo : tester de permettre de desactiver ex: $( document ).off( 'tify_forms.ajax_submit.success', tify_forms_ajax_submit_success ); **/
                var tify_forms_ajax_submit_init = function (e, data, ID) {

                    },

                    tify_forms_ajax_submit_before = function (e, ID) {
                        $(e.target).append('<div class="tiFyForm-Overlay tiFyForm-Overlay--' + ID + '" />');
                    },

                    tify_forms_ajax_submit_response = function (e, resp, ID) {
                        if (resp.data.html !== undefined)
                            $(e.target).empty().html(resp.data.html);
                    },

                    tify_forms_ajax_submit_after = function (e, ID) {

                    };

                jQuery(document).ready(function ($) {
                    // Définition des variables
                    var ID = '<?php echo $ID;?>',
                        $wrapper = $('#tiFyForm-' + ID);

                    // Déclaration des événements
                    /// A l'intialisation des données de la requête Ajax
                    $(document).on('tify_forms.ajax_submit.init', tify_forms_ajax_submit_init);
                    /// Avant le lancement de la requête Ajax
                    $(document).on('tify_forms.ajax_submit.before', tify_forms_ajax_submit_before);
                    /// Au retour de la requête Ajax avec succès
                    $(document).on('tify_forms.ajax_submit.response', tify_forms_ajax_submit_response);
                    /// Après le retour de la requête Ajax
                    $(document).on('tify_forms.ajax_submit.after', tify_forms_ajax_submit_after);

                    // Requête Ajax
                    $(document).on('submit', '<?php echo $html_id;?>', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        // Formatage des données
                        var data = new FormData(this);
                        /// Action Ajax
                        data.append('action', 'tify_forms_ajax_submit');
                        /// Traitement des fichiers
                        $('input[type="file"]', $(this)).each(function (u, v) {
                            if (v.files !== undefined) {
                                data.append($(this).attr('name'), v.files);
                            }
                        });

                        // Evenement de traitement des données de la requête
                        $wrapper.trigger('tify_forms.ajax_submit.init', data, ID);

                        $.ajax({
                            url: tify_ajaxurl,
                            data: data,
                            type: 'POST',
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            cache: false,
                            beforeSend: function () {
                                $wrapper.trigger('tify_forms.ajax_submit.before', ID);
                            },
                            success: function (resp) {
                                $wrapper.trigger('tify_forms.ajax_submit.response', resp, ID);
                            },
                            complete: function () {
                                $wrapper.trigger('tify_forms.ajax_submit.after', ID);
                            }
                        });

                        return false;
                    });
                });

                /*if (jQuery('tiFyForm-<?php echo $ID;?>').length) ;
                    jQuery(document).on('tify_forms.ajax_submit.after', function (e, ID) {
                        onloadCallback_<?php echo $ID;?>();
                    });*/

                /* ]]> */</script><?php
        };
        add_action('wp_footer', $wp_footer, 99);
    }

    /**
     * Traitement de la soumission de formulaire via ajax
     *
     * @return string
     */
    final public function wp_ajax()
    {
        remove_filter(current_filter(), __METHOD__);
        do_action('tify_form_loaded');

        $data = ['html' => $this->getForm()->display()];

        if ($this->form()->request()->hasError()) :
            \wp_send_json_error($data);
        else :
            \wp_send_json_success($data);
        endif;

    }
}