<?php
/**
 * Created by PhpStorm.
 * User: kalivoda
 * Date: 07.12.2017
 * Time: 0:54
 */

include_once "models/db.php";

$status_text[0] = 'accepted';
$status_text[1] = 'waiting for review';
$status_text[2] = 'reviewer assigned';
$status_text[3] = 'waiting for approval';
$status_text[4] = 'rejected';

function loadArticles() {
    global $status_text;
    $content = array();
    if(isset($_SESSION['user'])) {
        $content = loadUserArticles($_SESSION['user']->getUserRole());
    }
    array_push($content, db::selectAll("SELECT * FROM articles WHERE status = ?", array(0)));
    for ($i = 0; $i < count($content); $i++) {
        foreach ($content[$i] as $key => $value) {
            $content[$i][$key]['author'] = (user_control::getUser($value['author'])[0][1]);
            $content[$i][$key]['status'] = $status_text[$value['status']];
        }
    }
    return $content;
}

function loadUserArticles($role) {
    if($role == 0) {
        // zobrazeni clanku bezneho uzivatele
        $content= array(
            db::selectAll("SELECT * FROM articles WHERE author = ? AND status BETWEEN ? AND ?", array($_SESSION['user']->getUserId(), 1, 3)),
            db::selectAll("SELECT * FROM articles WHERE author = ? AND status = ?", array($_SESSION['user']->getUserId(), 4)),
            db::selectAll("SELECT * FROM articles WHERE author = ? AND status = ?", array($_SESSION['user']->getUserId(), 0))
        );
    } elseif ($role == 1) {
        // zobrazeni recenzenta
        $content = array(
            db::selectAll("SELECT * FROM articles WHERE status = ? AND reviewer = ?", array(2, $_SESSION['user']->getUserId()))
        );
    } else {
        //zobrazeni admina
        $content = array(
            db::selectAll("SELECT * FROM articles WHERE status = 1", array()),
            db::selectAll("SELECT * FROM articles WHERE status = 3", array()),
            db::selectAll("SELECT * FROM articles WHERE status = 2", array()),
            db::selectAll("SELECT * FROM articles WHERE status = 4", array())
        );
    }
    return $content;
}