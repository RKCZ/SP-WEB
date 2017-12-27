<?php
require_once 'C:/Users/kalivoda/vendor/autoload.php';
require_once './controllers/user_control.class.php';
require_once './controllers/content_loader.php';
session_start();

// deklarace promennych sablony
$page_title = "WEB#CON";
$login_info = array();
$content = "Nothing to show here yet, please try again later...";
$top_nav = array(
    array('class' => "home", 'caption' => "Home")
);

// obsluha odeslanych formularu
if($_POST) {
    if(!isset($_SESSION['user']))
        $_SESSION['user'] = new user_control();
    if(isset($_POST["action"]) && $_POST["action"] == "registration") {
        $ok = $_SESSION['user']->registerUser();
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
            $_SESSION['user']->logOut();
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "login") {
        $ok = $_SESSION['user']->logUser();
        if (!empty($ok)) {
            $_SESSION['error'] = $ok;
            $_SESSION['user']->logOut();
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "logout") {
        $_SESSION['user']->logOut();
    }

    header('Location: thanks.php');
    exit();
}

if(isset($_SESSION['user'])) {
    // obsluha zobrazení pro přihlášené uživatele
    // prirad prislusne hodnoty promennym sablony
    $title = "User Content";
    $login_info = array('role' => $_SESSION['user']->getUserRole(),'nickname' => $_SESSION['user']->getUserNick());
    $role = $_SESSION['user']->getUserRole();
    array_push($top_nav, array('class' => "userContent", 'caption' => "User Content"));

}

$content = loadArticles();
$vars = array('page_title' => $page_title,
    'site_logo' => 'WEB#CON',
    'top_nav' => $top_nav,
    'content' => $content,
    'error_msg' => isset($_SESSION['error'])? $_SESSION['error'] : "",
    'login_info' => $login_info
);
$loader = new Twig_Loader_Filesystem('stylesheets');
$twig = new Twig_Environment($loader, array());
echo $twig->render('main.twig', $vars);

$_SESSION['error'] = "";