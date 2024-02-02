<?php
class WpUnikTextYouBase
{


    public function wpUnikTextYouBaseSinonimizeContentAuto()
    {
        add_filter('content_save_pre', [$this, 'wpUnikTextSinonimizieContentGo'], 0);
    }

    public function wpUnikTextYouBaseSinonimizeTitle()
    {
        add_filter('title_save_pre', [$this, 'wpUnikTextSinonimizieTitleGo'], 0);
    }

    public function wpUnikTextSinonimizieTitleGo($text)
    {
        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $text);
        $text = iconv('windows-1251//IGNORE', 'utf-8', $text);

        $text = trim($text);

        $textOut = $this->wpUnikTextTextSinonimizieGo($text);
        $textOut[0] = htmlspecialchars_decode($textOut[0]);

        return $textOut[0];
    }

    public function wpUnikTextSinonimizieTitleHandGo($text)
    {
        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $text);
        $text = iconv('windows-1251//IGNORE', 'utf-8', $text);

        $text = trim($text);

        $textOut = $this->wpUnikTextTextSinonimizieGo($text);
        $textOut[0] = htmlspecialchars_decode($textOut[0]);

        return $textOut;
    }

    public function wpUnikTextSinonimizieContentGo($text)
    {
        $text = iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $text);
        $text = iconv('windows-1251//IGNORE', 'utf-8', $text);
        $text = trim($text);

        $textOut = $this->wpUnikTextTextSinonimizieGo($text);
        $textOut[0] = htmlspecialchars_decode($textOut[0]);

        $options = get_option('wpuniktext_settings_options');
        if($options['min_proz_you_base'] < $textOut[1]){
        return $textOut[0];
        }else{return $text;}
    }

    public function wpUnikTextTextSinonimizieGo($text)
    {
        $newCountSyn = 0;
        $words = preg_split('/([a-яА-Я\d]+)/is', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $words = array_values($words);
        $countWords = count($words);
        $open_tag = false;
        $new_text = '';
        $textIzmeneniye = '';

        for ($i = 0; $i < $countWords; $i++) {

            if ($this->wpuniktext_is_opened_tag($words[$i])) {
                $new_text .=  htmlspecialchars($words[$i]);
                $open_tag = true;
                continue;
            }

            if ($this->wpuniktext_is_closed_tag($words[$i])) {
                $new_text .=  htmlspecialchars($words[$i]);
                $open_tag = false;
                continue;
            }
            if ($open_tag) { // Если это тег
                $new_text .=  htmlspecialchars($words[$i]);
                continue;
            }

            if ($this->wpuniktext_get_synonym($words[$i])) {

                $zaglavnaya = false;
                if (preg_match('%^\p{Lu}%u', $words[$i])) {
                    $zaglavnaya = true;
                }
                $WordsZaglavnaya = '';
                $WordsZaglavnaya = $this->wpuniktext_get_synonym($words[$i]);

                if ($zaglavnaya === true) {
                    $WordsZaglavnaya = $this->mb_ucfirst($WordsZaglavnaya);
                }
                $new_text .= $WordsZaglavnaya;
                $newCountSyn = $newCountSyn +  mb_strlen($WordsZaglavnaya);
                continue;
            } else {
                $new_text .= $words[$i];
            }
        }

        $countSimvIn = htmlspecialchars_decode($new_text);
        $countSimvIn = strip_tags($countSimvIn);
        $countSimvIn = mb_strlen(preg_replace("/[^a-zа-я\w]/iu", "", $countSimvIn));

        if (round($countSimvIn > 0)) {
            $textIzmeneniye =  round($newCountSyn / ($countSimvIn / 100), 2);
        }

        $textOut[0] = $new_text;
        $textOut[1] = $textIzmeneniye;
        return $textOut;
    }

    public function  wpuniktext_is_opened_tag($str)
    {
        if (strpos($str, '<') !== false)
            return true;
        else
            return false;
    }

    public function  wpuniktext_is_closed_tag($str)
    {
        if (strpos($str, '>') !== false)
            return true;
        else
            return false;
    }

    public function mb_ucfirst($string, $enc = 'UTF-8')
    {
        return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) . mb_substr($string, 1, mb_strlen($string, $enc), $enc);
    }

    function wpuniktext_get_synonym($word)
    {
        $keyword = mb_strtolower($word, "UTF-8");
        global $wpdb;
        $sql = $wpdb->get_results("SELECT syn FROM wp_wpuniktext_youbase WHERE keyword = '$keyword'");
        if ($sql == true) {
            $syns = $sql[0]->syn;
            $words = explode(',', $syns);
            $keysWord = '';
            srand((float) microtime() * 10000000);
            $words = $words[array_rand($words)];
            return $words;
        } else {
            return false;
        }
    }

    public function wpUnitTextDeleteSyn()
    {
        global $wpdb;
        $unik_syn_table = $wpdb->prefix . "wpuniktext_youbase";
        $sql = "TRUNCATE TABLE $unik_syn_table";
        $wpdb->query($sql);
        return '<h2 style="color: red;">' . __("All synonyms have been removed from the database", "wp_unik_text") . '</h2>';
    }

    public function wpUnikTextAddSyn($synonims_dict)
    {
        global $wpdb;
        $unik_syn_table = $wpdb->prefix . "wpuniktext_youbase";
        strip_tags($synonims_dict);
        $pos = strpos($synonims_dict, '|');
        if (strpos($synonims_dict, '|') === false) {
            return '<h2 style="color: red;">' . __("Incorrect data entered!", "wp_unik_text") . '</h2>';
            exit;
        } else {
            $lines = explode("\n", $synonims_dict);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!$line) continue;
                list($key, $syn) = explode("|", $line);
                $sql = "INSERT INTO $unik_syn_table (keyword, syn) VALUES('$key','$syn')";
                $wpdb->query($sql);
            }
            return '<h2 style="color: red;">' . __("Synonyms have been added to the database!", "wp_unik_text") . '</strong></h2>';
        }
    }
}
