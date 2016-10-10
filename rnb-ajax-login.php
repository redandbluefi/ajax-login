<?php
/**
 * Plugin Name:     AJAX Login
 * Description:     This plugin attaches itself to 'login_init' action hook, and allows you to log in using AJAX.
 * Author:          Christian Nikkanen / redandblue
 * Author URI:      http://redandblue.fi
 * Text Domain:     rnb-ajax-login
 * Version:         1.0.1
 *
 */

function rnb_ajax_login() {
  $response = array();

  if(is_user_logged_in()){
    $user = wp_get_current_user();
    $response['type'] = 'error';
    $response['message'] = 'already logged in as ' . $user->user_email;
  } else {
    $credentials = array(
      'user_login' => @$_POST['log'],
      'user_password' => @$_POST['pwd'],
      'remember' => @$_POST['rememberme']
    );

    $login = wp_signon($credentials, apply_filters('rnb_ajax_login_securecookie', true));

    if(!is_wp_error($login)){
      $response['type'] = 'success';
      $response['message'] = 'logged in as ' . $login->user_email;
    } else {
      $response['type'] = 'error';

      if (empty($credentials['user_login']) || empty($credentials['user_password'])) {
        $response['message'] = 'You didn\'t supply username and/or password. POST them as "log" and "pwd".';
      } else {
        $response['message'] = strip_tags($login->get_error_message(), '<a>');
      }

    }
  }

  wp_send_json($response);
}

function rnb_ajax_login_action(){
  if (!empty($_SERVER['HTTP_RESPONSE_TYPE']) && strtolower($_SERVER['HTTP_RESPONSE_TYPE']) === "json") {
    rnb_ajax_login();
  } elseif (!empty($_REQUEST['RESPONSE_TYPE']) && strtolower($_REQUEST['RESPONSE_TYPE'])) {
    rnb_ajax_login();
  } else {
    return;
  }
}

add_action('login_init', 'rnb_ajax_login_action');
