<?php
class wpUnikTextHandSyn_class
{

    public function wpUnikTextHandSyn()
    {
        add_action('admin_menu', [$this, 'wpUnikTextTypeMetaBox']);
    }

    public function wpUnikTextTypeMetaBox()
    {
        $arrType = array('page', 'post');
        foreach ($arrType as $type) {
            add_meta_box('wpUnikTextMetaBox', __('Synonymizer wp-unik-text', 'wp_unik_text'), [$this, 'wpUnikTextMetaBox'], $type);
        }
    }

    public function wpUnikTextMetaBox()
    {
?>
        <a id="wpUnikTextSynonimizeEditor" title="<?php __('Synonymize', 'wp_unik_text'); ?> " class="wpUnikTextbutton" href="#"> <?php esc_html_e('SYNONYMIZE', 'wp_unik_text'); ?></a>
        <div id="response"></div>


        <?php
        ?>
        <div><br><br>
            <input value="" name="wpUnikTextRewrittenTitle" id="fieldWpUnikTextRewrittenTitle" type="text">
        </div>
        <div class="wpUnikTextEditor">
        <?php

        $args = array(
            'editor_height' => '400',
            'textarea_rows' => 15,
            'teeny' => true,
            'quicktags' => true,
            'media_buttons' => false,
            'wpautop' => true,
            'tinymce' => array(
                'force_br_newlines' => false,
                'force_p_newlines' => false,
                'forced_root_block' => '',
            )
        );
        wp_editor('', 'wpuniktextEditor', $args);
    
        ?>
        </div>
        <div>
        <a id="wpUnikTextSynonimizeEditorSend" title="<?php __('Send', 'wp_unik_text'); ?>" class="wpUnikTextbuttonGo" href="#"><?php esc_html_e('Send', 'wp_unik_text'); ?></a>
        </div>
<?php
    }
}
