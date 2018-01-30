<?php
/**
 * Created by PhpStorm.
 * User: kalivoda
 * Date: 07.12.2017
 * Time: 11:10
 */
/*
 * pomocna stranka, presmerovava zpet na index.php po zpracovani odeslaneho formulare
 */
echo 'Please wait, we are redirecting you to target page....';

header('Location: index.php');
exit();