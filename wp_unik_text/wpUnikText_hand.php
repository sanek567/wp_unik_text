<?php
require_once 'lib/wpUnikTextYouBase_class.php';
require_once 'lib/uniktextApi_class.php';
require_once 'lib/textorobotApi_class.php';
require_once($_SERVER['DOCUMENT_ROOT'] . $folder . '/wp-config.php');

$options = get_option('wpuniktext_settings_options');
if (isset($options['base_sin']) && $options['base_sin'] === 'you_base_sin') {
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input"));

    $text = new WpUnikTextYouBase();
    $inpTitle = htmlspecialchars_decode($data->title);
    $inpContent = htmlspecialchars_decode($data->content);
    if (isset($options['title_sin']) && $options['title_sin'] === "title_sin") {
        $inpTitle = $text->wpUnikTextSinonimizieTitleGo($inpTitle);
    }
    $inpContent = $text->wpUnikTextSinonimizieTitleHandGo($inpContent);
    $contentArr = [
        'title' => $inpTitle,
        'content' => $inpContent[0],
        'prozent' => $inpContent[1]
    ];

    $inp = json_encode($contentArr, JSON_UNESCAPED_UNICODE);
    print_r($inp);
}

if (isset($options['base_sin']) && $options['base_sin'] === 'uniktext_base_sin') {
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input"));

    $text = new UniktextApi();
    $inpTitle = htmlspecialchars_decode($data->title);
    $inpContent = htmlspecialchars_decode($data->content);
    if (isset($options['title_sin']) && $options['title_sin'] === "title_sin") {
        $inpTitle = $text->uniktextApiPost($inpTitle);
    }
    $inpContent = $text->uniktextApiPost($inpContent);
    $contentArr = [
        'title' => $inpTitle['synonymizedText'],
        'content' => $inpContent['synonymizedText'],
        'prozent' => $inpContent['synonymizedPrezent']
    ];

    $inp = json_encode($contentArr, JSON_UNESCAPED_UNICODE);
    print_r($inp);
}

if (isset($options['base_sin']) && $options['base_sin'] === 'textorobot_base_sin') {
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input"));

    $text = new TextorobotApi();
    $inpTitle = htmlspecialchars_decode($data->title);
    $inpContent = htmlspecialchars_decode($data->content);
    if (isset($options['title_sin']) && $options['title_sin'] === "title_sin") {
        $inpTitle = $text->synonymizeText($inpTitle);
    }
    $inpContent = $text->synonymizeText($inpContent);
    $contentArr = [
        'title' => $inpTitle->processedText,
        'content' => $inpContent->processedText,
        'prozent' => $inpContent->synonymPercentage
    ];

    $inp = json_encode($contentArr, JSON_UNESCAPED_UNICODE);
    print_r($inp);
}
