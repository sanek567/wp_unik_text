<?php

class UniktextApi
{
    public function uniktextSinonimizeContentAuto()
    {
        add_filter('content_save_pre', [$this, 'uniktextApiContent'], 0);
        if(doing_filter('content_save_pre')){
            die;
        }
    }

    public function uniktextSinonimizeTitleAuto(){
        add_filter('title_save_pre', [$this, 'uniktextApiTitleGo'], 0);
        if(doing_filter('title_save_pre')){
            die;
        }
    }

    public function uniktextApiTitleGo($text)
    {
        if(doing_filter('title_save_pre')){
            sleep(3);
        }
        $j = $this->uniktextApiPost($text);
        if($j['synonymizedText'] != ''){
        return $j['synonymizedText'];
        }else{
            return $text;
        }
    }
    public function uniktextApiContent($text)
    {
        if(doing_filter('content_save_pre')){
            sleep(3);
        }
        $j = $this->uniktextApiPost($text);

        $options = get_option('wpuniktext_settings_options');
        if($j['success'] === true && $options['min_proz_uniktext_base'] < $j['synonymizedPrezent'] && $options['uniktext_api'] === $j['apiKey']){
            return $j['synonymizedText'];
            } else {     
            return $text;
            }
    }

    public function uniktextApiPost($text)
    {
        $url = "https://unik-text.ru/sin/api/";
        $apiKey = '';
        $options = get_option('wpuniktext_settings_options');
        if (isset($options['uniktext_api'])){
        $apiKey = $options['uniktext_api'];
        }
        $optionsArr = array(
            'apiKey' => $apiKey,
            'text'   => $text
        );

        $httpQuery = http_build_query($optionsArr);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $httpQuery);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);

        $result = curl_exec($ch);

        $j = json_decode($result, false);
        $j = (array)$j;
        curl_close($ch);

        return $j;
    }
}
