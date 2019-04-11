<?php 
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
$fb = new Facebook\Facebook([
  'app_id' => '478602012170941',
  'app_secret' => 'e4c36af8c84116530e7d400566fb252b',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://facebook-login.inwavethemes.com/fb-callback.php', $permissions);

echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
?>