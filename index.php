<?php
define('ROOT', dirname(__DIR__) . '/htdocs'); // points to project root

require_once ROOT . '/vendor/autoload.php';
require_once ROOT . '/controllers/user_control.class.php';
require_once ROOT . '/controllers/content_manager.php';
session_start();

// deklarace promennych sablony
$page_title = "WEB#CON";
$login_info = array();
$articles = "Nothing to show here yet, please try again later...";
$users = "";
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

    if(isset($_POST["action"]) && $_POST["action"] == "postCreate") {
        $ok = addPost();
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "postEdit") {
        $ok = editPost();
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "assign") {
        $ok = assignReviewer($_POST['article'], $_POST['user']);
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "review") {
        $ok = review($_POST['article'], $_SESSION['user']);
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "change-role") {
        if (isset($_POST["promoteToAdmin"]))
            $ok = changeUserRole(2, $_POST["user"]);
        elseif (isset($_POST["promoteToReviewer"]))
            $ok = changeUserRole(1, $_POST["user"]);
        elseif (isset($_POST["demoteToUser"]))
            $ok = changeUserRole(0, $_POST["user"]);
        elseif (isset($_POST["deleteUser"]))
            $ok = deleteUser($_POST["user"]);
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "postDelete") {
        $ok = deleteArticle($_POST["article"]);
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "postAccept") {
        $ok = changeStatus($_POST["article"], 0);
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
        }
    }

    if(isset($_POST["action"]) && $_POST["action"] == "postReject") {
        $ok = changeStatus($_POST["article"], 4);
        if(!empty($ok)) {
            $_SESSION['error'] = $ok;
        }
    }

    header('Location: thanks.php');
    exit();
}

if(isset($_SESSION['user'])) {
    // obsluha zobrazení pro přihlášené uživatele
    $title = "User Content";
    $login_info = array('role' => $_SESSION['user']->getUserRole(),'nickname' => $_SESSION['user']->getUserNick());
    array_push($top_nav, array('class' => "userContent", 'caption' => "User Content"));
    if ($_SESSION['user']->getUserRole() == 2) {
        $users = getUsers();
        array_push($top_nav, array('class' => "users", 'caption' => "Manage users"));
    }

}

// ziskani clanku z databaze
$articles = getArticles();

// pripraveni promennych pro twig
$vars = array('page_title' => $page_title,
    'site_logo' => 'WEB#CON',
    'top_nav' => $top_nav,
    'content' => $articles,
    'users' => $users,
    'error_msg' => isset($_SESSION['error'])? $_SESSION['error'] : "",
    'login_info' => $login_info
);

// konfigurace twigu
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array('debug' => true));
$twig->addExtension(new Twig_Extension_Debug());
try {
    echo $twig->render('base.twig', $vars);
} catch (Twig_Error_Loader $e) {
    echo 'something went wrong and we could not load twig templates (Loading error)<br>';
    echo $e->getMessage() . '<br>';
    echo $e->getFile() . ': ' . $e->getLine();
} catch (Twig_Error_Runtime $e) {
    echo 'something went wrong and we could not load twig templates (Runtime error)<br>';
    echo $e->getMessage() . '<br>';
    echo $e->getFile() . ': ' . $e->getLine();
} catch (Twig_Error_Syntax $e) {
    echo 'something went wrong and we could not load twig templates (Bad syntax)<br>';
    echo $e->getMessage() . '<br>';
    echo $e->getFile() . ': ' . $e->getLine();
}

$_SESSION['error'] = "";