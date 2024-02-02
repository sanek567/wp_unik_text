<?php
class WpUnikTextSettings
{

    public function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'style']); //add css
        add_action('admin_enqueue_scripts', [$this, 'scripts']); //add java
        add_action('admin_menu', [$this, 'addMenuPage']); //add menu
        add_filter('plugin_action_links_' . WP_UNIK_TEXT_LINKS, [$this, 'addPluginSettingLink']);
        add_action('admin_init', [$this, 'settings_init']); //add all settings
        add_action('plugins_loaded',[$this, 'ap_action_init']);
    }

    public function activation()
    {

        global $wpdb;
        $wpuniktextYouBaseTable = $this->wpuniktext_get_syn_table();
        $charset_collate = '';
        if ($wpdb->get_var("SHOW TABLES LIKE '$wpuniktextYouBaseTable'") != $wpuniktextYouBaseTable) {
            $sql = "CREATE TABLE " . $wpuniktextYouBaseTable . " (
                     s_id int(11) NOT NULL auto_increment,
                      keyword varchar(255) NOT NULL default '',
                      syn varchar(255) NOT NULL default '',
                    PRIMARY KEY  (s_id),
                    KEY kkey  (keyword)
                )$charset_collate";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }


        $strConfig = file(ABSPATH . "wp-config.php");

        $configBool = true;
        foreach ($strConfig as $i) {
            $sch = "<?php define('CONCATENATE_SCRIPTS', false); ?>";
            if (strpos($i, $sch) === false) {
            } else {
                $configBool = false;
            }
        }

        if ($configBool === true) {
            $file_gb = ABSPATH . "wp-config.php";
            $file_tmp = ABSPATH . "wp-config_tmp.php";
            $str = "<?php define('CONCATENATE_SCRIPTS', false); ?>";

            if (file_exists($file_tmp)) die("fatal error, call administrator!");
            if (copy($file_gb, $file_tmp)) {
                if ($w = fopen($file_gb, "w")) {
                    flock($w, 2);
                    fwrite($w, $str . "\n");
                    if (!$r = fopen($file_tmp, "r")) die("can't open file");
                    flock($r, 1);
                    while ($str = fgets($r, 10240)) {
                        fputs($w, $str);
                    }
                    flock($r, 3);
                    fclose($r);
                    flock($w, 3);
                    fclose($w);
                    unlink($file_tmp);
                }
            }
        }
    }

    static function deactivation()
    {

        $file_gb = ABSPATH . "wp-config.php";
        $strConfig = file($file_gb);
        $strFile = "";
        foreach ($strConfig as $i) {
            $sch = "<?php define('CONCATENATE_SCRIPTS', false); ?>";

            if (strpos($i, $sch) === false) {
                $strFile = $strFile . $i;
            }
            file_put_contents($file_gb, $strFile);
        }
    }

    public function style() // Add css
    {
        wp_enqueue_style('Style', plugins_url() . '/wp_unik_text/style/wp_unik_text.css', __FILE__);
    }
    public function scripts() // Add css
    {
        wp_enqueue_script('script', plugins_url() . '/wp_unik_text/js/wp_unik_text_java.js', __FILE__);
        $arrSend = array(
            'Changed' => __('Changed:', 'wp_unik_text'),
            'text' => __('text', 'wp_unik_text')
        );
        wp_localize_script('script', 'ArrIn', $arrSend);
    }

    public function addMenuPage() //add admin menu
    {
        add_menu_page(
            esc_html__('UnikText Setting Page', 'wp_unik_text'),
            esc_html__('UnikText', 'wp_unik_text'),
            'manage_options',
            'all_uniktext_settings',
            [$this, 'AdminMenu'],
            'dashicons-media-text', //plugins_url('wp_unik_text/images/icon.png'),
            90
        );

        add_menu_page(
            '',
            '',
            '',
            'uniktext_settings_you_base',
            '',
            '',
            90
        );

        add_menu_page(
            '',
            '',
            '',
            'uniktext_settings_uniktext_base',
            '',
            '',
            90
        );
    }

    public function adminMenu() //add admin menu
    {
        require_once WP_UNIK_TEXT . 'admin/admin.php';
    }

    public function addPluginSettingLink($link)
    {
        $settingsLink = '<a href="admin.php?page=all_uniktext_settings">' . esc_html__('Settings', 'wp_unik_text') . '</a>';
        array_push($link, $settingsLink);
        return $link;
    }

    public function settings_init()
    {
        register_setting('all_settings', 'wpuniktext_settings_options');

        add_settings_section('wpuniktext_settings_section', esc_html__('General settings', 'wp_unik_text'), NULL, 'all_uniktext_settings');
        add_settings_field('avto_hand_sin', esc_html__('Synonymization', 'wp_unik_text'), [$this, 'avto_sin_html'], 'all_uniktext_settings', 'wpuniktext_settings_section');
        add_settings_field('title_sin', esc_html__('Title synonymization', 'wp_unik_text'), [$this, 'title_sin_html'], 'all_uniktext_settings', 'wpuniktext_settings_section');

        add_settings_section('wpuniktext_settings_section_you_base', esc_html__('Own synonym database', 'wp_unik_text'), NULL, 'uniktext_settings_you_base');
        add_settings_field('base_sin', esc_html__('Own synonym database', 'wp_unik_text'), [$this, 'you_base_sin_html'], 'uniktext_settings_you_base', 'wpuniktext_settings_section_you_base');
        add_settings_field('min_proz_you_base', esc_html__('Minimum synonymization percentage', 'wp_unik_text'), [$this, 'min_proz_you_base_html'], 'uniktext_settings_you_base', 'wpuniktext_settings_section_you_base');

        add_settings_section('wpuniktext_settings_uniktext_base', esc_html__('Synonymizer UNIK-TEXT.RU', 'wp_unik_text'), NULL, 'uniktext_settings_uniktext_base');
        add_settings_field('uniktext_base_sin', esc_html__('Synonymizer Unik-text.ru', 'wp_unik_text'), [$this, 'uniktext_sin_html'], 'uniktext_settings_uniktext_base', 'wpuniktext_settings_uniktext_base');
        add_settings_field('uniktext_api', esc_html__('ApiKey key from the site Uni-text.ru', 'wp_unik_text'), [$this, 'uniktext_api_html'], 'uniktext_settings_uniktext_base', 'wpuniktext_settings_uniktext_base');
        add_settings_field('min_proz_uniktext_base', esc_html__('Minimum synonymization percentage', 'wp_unik_text'), [$this, 'min_proz_uniktext_base_html'], 'uniktext_settings_uniktext_base', 'wpuniktext_settings_uniktext_base');

        add_settings_section('wpuniktext_settings_textorobot_base', esc_html__('Synonymizer TEXTOROBOT.RU', 'wp_unik_text'), NULL, 'uniktext_settings_textorobot_base');
        add_settings_field('textorobot_base_sin', esc_html__('Synonymizer Textorobot.ru', 'wp_unik_text'), [$this, 'textorobot_sin_html'], 'uniktext_settings_textorobot_base', 'wpuniktext_settings_textorobot_base');
        add_settings_field('textorobot_api', esc_html__('ApiKey key from the site Textorobot.ru', 'wp_unik_text'), [$this, 'textorobot_api_html'], 'uniktext_settings_textorobot_base', 'wpuniktext_settings_textorobot_base');
        add_settings_field('min_proz_textorobot_base', esc_html__('Minimum synonymization percentage', 'wp_unik_text'), [$this, 'min_proz_textorobot_base_html'], 'uniktext_settings_textorobot_base', 'wpuniktext_settings_textorobot_base');

    }

    public function avto_sin_html()
    {
        $options = get_option('wpuniktext_settings_options');
?>
        <input type="radio" name="wpuniktext_settings_options[avto_hand_sin]" value="avto" <?php echo isset($options['avto_hand_sin']) && $options['avto_hand_sin'] == "avto" ? "checked" : '';
                                                                                            echo !isset($options['avto_hand_sin']) ? 'checked' : ''; ?> /> <span class="avtoSinonimStr"> <?php esc_html_e('Automatic', 'wp_unik_text'); ?> </span> <span class="avtoSinVozm">(<?php esc_html_e('Synonymization occurs automatically during content publication.', 'wp_unik_text'); ?> <b> <?php esc_html_e('Suitable for autoparsing!', 'wp_unik_text'); ?> </b>) </span><br>
        <input type="radio" name="wpuniktext_settings_options[avto_hand_sin]" value="hand" <?php echo isset($options['avto_hand_sin']) && $options['avto_hand_sin'] == "hand" ? "checked" : ''; ?> /> <span class="avtoSinonimStr"> <?php esc_html_e('Manual', 'wp_unik_text'); ?> </span> <span class="avtoSinVozm">(<?php esc_html_e('Using the "Synonymize" button, which is located below the text editor.', 'wp_unik_text'); ?>) </span>
        
    <?php }

    public function title_sin_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <input type="checkbox" name="wpuniktext_settings_options[title_sin]" value="title_sin" <?php echo isset($options['title_sin']) ? "checked" : ''; ?> /> <span class="avtoSinonimStr"> <?php esc_html_e('Synonymize the title', 'wp_unik_text'); ?> </span>
    <?php }


    public function you_base_sin_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <input type="radio" name="wpuniktext_settings_options[base_sin]" value="you_base_sin" <?php echo isset($options['base_sin']) && $options['base_sin'] == "you_base_sin" ? "checked" : '';
                                                                                                echo !isset($options['base_sin']) ? 'checked' : ''; ?> /> <span class="avtoSinonimStr"> <?php esc_html_e('Synonymize using your synonym database.', 'wp_unik_text'); ?> </span>
    <?php }

    public function min_proz_you_base_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <select name="wpuniktext_settings_options[min_proz_you_base]" value="<?php echo isset($options['min_proz_you_base']) ? $options['min_proz_you_base'] : ''; ?>" />
        <?php echo $this->select_prozent('min_proz_you_base'); ?>
        </select> <span class="avtoSinonimStr"> <?php esc_html_e('If the percentage of text changes is less, then the text is published in its original form without synonymization. (Works only in automatic mode!)', 'wp_unik_text'); ?> </span>
    <?php }

    public function uniktext_sin_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <input type="radio" name="wpuniktext_settings_options[base_sin]" value="uniktext_base_sin" <?php echo isset($options['base_sin']) && $options['base_sin'] == "uniktext_base_sin" ? "checked" : ''; ?> /> <span class="avtoSinonimStr"> <?php esc_html_e('Synonymize using UNIK-TEXT.RU.', 'wp_unik_text'); ?> </span>
    <?php }

    public function uniktext_api_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <input type="text" name="wpuniktext_settings_options[uniktext_api]" value="<?php echo isset($options['uniktext_api']) ? $options['uniktext_api'] : '' ?>" /> <span class="avtoSinonimStr"> <?php esc_html_e('For the synonymizer to work, you need to purchase a symbol package at', 'wp_unik_text'); ?> <a href='https://unik-text.ru/?ref=217' target='_blank'> UNIK-TEXT.RU</a> <?php esc_html_e('and get an API key, which must be specified below.', 'wp_unik_text'); ?> </span>
    <?php
    }

    public function min_proz_uniktext_base_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <select name="wpuniktext_settings_options[min_proz_uniktext_base]" value="<?php echo isset($options['min_proz_uniktext_base']) ? $options['min_proz_uniktext_base'] : ''; ?>" />
        <?php echo $this->select_prozent('min_proz_uniktext_base'); ?>
        </select> <span class="avtoSinonimStr"> <?php esc_html_e('If the percentage of text changes is less, then the text is published in its original form without synonymization. (Works only in automatic mode!)', 'wp_unik_text'); ?> </span>
    <?php }

    public function textorobot_sin_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <input type="radio" name="wpuniktext_settings_options[base_sin]" value="textorobot_base_sin" <?php echo isset($options['base_sin']) && $options['base_sin'] == "textorobot_base_sin" ? "checked" : ''; ?> /> <span class="avtoSinonimStr"> <?php esc_html_e('Synonymize using TEXTOROBOT.RU.', 'wp_unik_text'); ?> </span>
    <?php }

    public function textorobot_api_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <input type="text" name="wpuniktext_settings_options[textorobot_api]" value="<?php echo isset($options['textorobot_api']) ? $options['textorobot_api'] : '' ?>" /> <span class="avtoSinonimStr"> <?php esc_html_e('For the synonymizer to work, you need to purchase a symbol package at', 'wp_unik_text'); ?> <a href='https://textorobot.ru/index.php?option=com_billing&partnername=alex1753' target='_blank'> TEXTOROBOT.RU</a> <?php esc_html_e('and get an API key, which must be specified below.', 'wp_unik_text'); ?> </span>
<?php
    }

    public function min_proz_textorobot_base_html()
    {
        $options = get_option('wpuniktext_settings_options');
    ?>
        <select name="wpuniktext_settings_options[min_proz_textrobot_base]" value="<?php echo isset($options['min_proz_textrobot_base']) ? $options['min_proz_textrobot_base'] : ''; ?>" />
        <?php echo $this->select_prozent('min_proz_textrobot_base'); ?>
        </select> <span class="avtoSinonimStr"> <?php esc_html_e('If the percentage of text changes is less, then the text is published in its original form without synonymization. (Works only in automatic mode!)', 'wp_unik_text'); ?> </span>
<?php
    }

    function wpuniktext_get_syn_table()
    { //Получает таблицу с синонимами
        global $wpdb;
        return $wpdb->prefix . "wpuniktext_youbase";
    }

    public function select_prozent($minProz)
    {
        $options = get_option('wpuniktext_settings_options');
        $optionSelect = '';
        for ($i = 0; $i <= 100; $i++) {
            $select = "";
            if ($options["$minProz"] == $i) {
                $select = 'selected="selected"';
            } else {
                $select = '';
            }
            $optionSelect .= '<option ' . $select . 'value="' . $i . '">' . $i . '</option>';
        }
        return $optionSelect;
    }

    public function ap_action_init()
    {
    // Локализация
    $loadTextUrl = basename('wp_unik_text');
    load_plugin_textdomain('wp_unik_text', false, $loadTextUrl . '/lang/' );
    }
}
