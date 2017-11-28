<?php

require_once 'C:/Users/roman/vendor/autoload.php';

$page_title = "Homepage ## WEB#CON";
$top_nav = array(
    array('href' => '#NULL', 'caption' => 'link #1'),
    array('href' => '#', 'caption' => 'link #2'),
    array('href' => '#', 'caption' => 'link #3')
    );

$vars = array('page_title' => $page_title, 'site_logo' => 'WEB#CON', 'title' => 'HomePage', 'top_nav' => $top_nav);

$loader = new Twig_Loader_Filesystem('stylesheets');
$twig = new Twig_Environment($loader, array());
echo $twig->render('base.twig', $vars);
