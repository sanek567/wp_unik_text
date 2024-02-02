<?php

$buttonAllSetting = 'background-color: rgb(109, 104, 216);color:aliceblue;';
$buttonYouBase = '';
$buttonUniktextBase = '';
$buttonTextorobotBase = '';
$displayNoneAllSettings = '';
$displayNoneYouBase = 'style="display: none;"';
$displayNoneUniktextBase = 'style="display: none;"';
$displayNoneTextorobotBase = 'style="display: none;"';
if (isset($_POST['allSetting'])) {
    $buttonAllSetting = 'background-color: rgb(109, 104, 216);color:aliceblue;';
    $buttonYouBase = '';
    $buttonUniktextBase = '';
    $buttonTextorobotBase = '';
    $displayNoneAllSettings = '';
    $displayNoneYouBase = 'style="display: none;"';
    $displayNoneUniktextBase = 'style="display: none;"';
    $displayNoneTextorobotBase = 'style="display: none;"';
}
if (isset($_POST['yourBazeSetting']) || isset($_POST['AddSyn']) || isset($_POST['deleteSyn'])) {
    $buttonAllSetting = '';
    $buttonYouBase = 'background-color: rgb(109, 104, 216);color:aliceblue;';
    $buttonUniktextBase = '';
    $buttonTextorobotBase = '';
    $displayNoneAllSettings = 'style="display: none;"';
    $displayNoneYouBase = '';
    $displayNoneUniktextBase = 'style="display: none;"';
    $displayNoneTextorobotBase = 'style="display: none;"';
}
if (isset($_POST['uniktextSetting']) || isset($_POST['testApiUniktext'])) {
    $buttonAllSetting = '';
    $buttonYouBase = '';
    $buttonUniktextBase = 'background-color: rgb(109, 104, 216);color:aliceblue;';
    $buttonTextorobotBase = '';
    $displayNoneAllSettings = 'style="display: none;"';
    $displayNoneYouBase = 'style="display: none;"';
    $displayNoneUniktextBase = '';
    $displayNoneTextorobotBase = 'style="display: none;"';
}
if (isset($_POST['textorobotSetting'])) {
    $buttonAllSetting = '';
    $buttonYouBase = '';
    $buttonUniktextBase = '';
    $buttonTextorobotBase = 'background-color: rgb(109, 104, 216);color:aliceblue;';
    $displayNoneAllSettings = 'style="display: none;"';
    $displayNoneYouBase = 'style="display: none;"';
    $displayNoneUniktextBase = 'style="display: none;"';
    $displayNoneTextorobotBase = '';
}

