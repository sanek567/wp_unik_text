<?php
include_once('admin_button.php'); 
require_once WP_UNIK_TEXT . '/lib/wpUnikTextYouBase_class.php';

$addSynNull = "";
if(isset($_POST['AddSyn'])){
    if($_POST['synonims_dict']){
    $addSyn = new WpUnikTextYouBase();
    $addSynNull = $addSyn->wpUnikTextAddSyn($_POST['synonims_dict']);
    }else{
        $addSynNull =  '<h2 style="color: red;">' . __('You have not entered data!', 'wp_unik_text') . '</h2>';
    }
}
if(isset($_POST['deleteSyn'])){
    $addSyn = new WpUnikTextYouBase();
    $addSynNull = $addSyn->wpUnitTextDeleteSyn();
}
?>
<h1 class="wpUnikTextTitle"><?php esc_html_e('Wp Unik Text synonymizer settings', 'wp_unik_text'); ?></h1>
<?php settings_errors(); ?>
<h2> <?php esc_html_e('Wp Unik Text plugin allows you to synonymize text automatically', 'wp_unik_text'); ?></h2>
<div class='uniktextVozmognosti'><b> <?php esc_html_e('Possibilities:', 'wp_unik_text'); ?></b></div>
<ul>
    <li class='uniktextvozmognostiLi'> <?php esc_html_e('Using your synonym database', 'wp_unik_text'); ?> </li>
    <li class='uniktextvozmognostiLi'> <?php esc_html_e('Connecting a synonymizer', 'wp_unik_text'); ?> <a href='https://unik-text.ru/?ref=217' target='_blank'> UNIK-TEXT.RU</a></li>
    <li class='uniktextvozmognostiLi'> <?php esc_html_e('Connecting a synonymizer', 'wp_unik_text'); ?> <a href='https://textorobot.ru/index.php?option=com_billing&partnername=sanek567' target='_blank'>TEXTOROBOT.RU</a></li>
</ul>
<br />
<form method="post" action="">
    <div>
        <input type="submit" class="wpuniktextButton" style="<?php echo $buttonAllSetting ?>" name="allSetting" value="<?php esc_html_e('General settings', 'wp_unik_text'); ?>" />
        <input type="submit" class="wpuniktextButton" style="<?php echo $buttonYouBase ?>" name="yourBazeSetting" value="<?php esc_html_e('Own synonym database', 'wp_unik_text'); ?>" />
        <input type="submit" class="wpuniktextButton" style="<?php echo $buttonUniktextBase ?>" name="uniktextSetting" value="UNIK-TEXT.RU" />
        <input type="submit" class="wpuniktextButton" style="<?php echo $buttonTextorobotBase ?>" name="textorobotSetting" value="TEXTOROBOT.RU" />
    </div>
</form>
<form method="post" action="options.php">
    <div class="wpuniktextSettings" <?php echo $displayNoneAllSettings; ?>>
        <?php
        settings_fields('all_settings');
        do_settings_sections('all_uniktext_settings');
        submit_button();
        ?>
    </div>
    <div class="wpuniktextSettings" <?php echo $displayNoneYouBase; ?>>
        <?php
        do_settings_sections('uniktext_settings_you_base');
        submit_button();
        ?>
    </div>
    <div class="wpuniktextSettings" <?php echo $displayNoneUniktextBase; ?>>
        <?php
        do_settings_sections('uniktext_settings_uniktext_base');
        submit_button();
        ?>
    </div>
    <div class="wpuniktextSettings" <?php echo $displayNoneTextorobotBase; ?>>
        <?php
        do_settings_sections('uniktext_settings_textorobot_base');
        submit_button();
        ?>
</form>
</div>
<div class="wpuniktextSettings" <?php echo $displayNoneYouBase; ?>>
<h3><?php esc_html_e('Synonymizer Dictionary', 'wp_unik_text'); ?></h3>

<?php
        global $wpdb;
        $wpuniktextYouBaseTable = $this->wpuniktext_get_syn_table();
        $unik_total_synonyms = $wpdb->get_var("SELECT COUNT(*) FROM $wpuniktextYouBaseTable"); ?>
<p><?php esc_html_e('Add a dictionary of synonyms (add a large dictionary in parts)', 'wp_unik_text'); ?></p>
<p<b><?php esc_html_e('format:', 'wp_unik_text'); ?></b> <?php esc_html_e('word|synonym1,synonym2,synonym3', 'wp_unik_text'); ?><br /></p>
<p><?php esc_html_e('Total words in the database:', 'wp_unik_text'); ?> <b><?php echo $unik_total_synonyms; ?></b><br /></p>
<?php echo $addSynNull ?>
<form method="post" action="">
    <input class="wpuniktextButton" type="submit" name="deleteSyn" value="<?php esc_html_e('Clear dictionary database', 'wp_unik_text'); ?>" /><br/>
    <textarea cols=110 rows=12 name="synonims_dict"></textarea><br/>
    <input class="wpuniktextButton" type="submit" name="AddSyn" value="<?php esc_html_e('Add synonyms', 'wp_unik_text'); ?>" />
</form>
</div>
