<?php
/**
 * Created by PhpStorm.
 * User: kalivoda
 * Date: 07.12.2017
 * Time: 0:54
 */

include_once "models/db.php";

$status_text[0] = 'waiting for review';
$status_text[1] = 'accepted';
$status_text[2] = 'reviewer assigned';
$status_text[3] = 'waiting for approval';
$status_text[4] = 'rejected';

function loadArticles() {
    global $status_text;
    if(isset($_SESSION['user'])) {
        $content = loadUserArticles($_SESSION['user']->getUserRole());
    } else {
        $content = db::selectAll("SELECT * FROM articles WHERE status = 1", array());
    }
    foreach ($content as $key => $value) {
        $content[$key]['author'] = (user_control::getUser($value['author'])[0][1]);
        $content[$key]['status'] = $status_text[$value['status']];
    }
    return $content;
}

function loadUserArticles($role) {
    if($role == 0) {
        // zobrazeni clanku bezneho uzivatele
        $content = db::selectAll("SELECT * FROM articles WHERE author = ? ", array($_SESSION['user']->getUserId()));
        return $content;
    } elseif ($role == 1) {
        // zobrazeni recenzenta
        $content = db::selectAll("SELECT * FROM articles WHERE status = ? AND reviewer = ?", array(2, $_SESSION['user']->getUserId()));
        return $content;
    } else {
        //zobrazeni admina
        $content = db::selectAll("SELECT * FROM articles WHERE status = 0", array());
        return $content;
    }
}