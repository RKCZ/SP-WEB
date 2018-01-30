<?php
/**
 * Created by PhpStorm.
 * User: kalivoda
 * Date: 07.12.2017
 * Time: 0:54
 */

include_once ROOT . "/models/db.php";

// příspěvek byl scvhválen a je zobrazen mezi veřejnými příspěvky
$status_text[0] = 'accepted';
// nový článek, čeká se na přidělení recenzentů
$status_text[1] = 'waiting for review';
// byl přidělen recenzent
$status_text[2] = 'reviewer assigned';
// recenze je hotova, čeká se na schválení administrátorem
$status_text[3] = 'waiting for approval';
// článek byl administrátorem odmítnut
$status_text[4] = 'rejected';

function getUsers() {
    //seznam uživatelů
    return array(
        db::selectAll("SELECT * FROM users WHERE role = ?", array(0)),  //běžní uživatelé
        db::selectAll("SELECT * FROM users WHERE role = ?", array(1))   //recenzenti
    );
}

function getArticles() {
    global $status_text;
    $content = array();
    if(isset($_SESSION['user'])) {
        $content = getUserArticles($_SESSION['user']->getUserRole());
    }
    array_push($content, db::selectAll("SELECT * FROM articles WHERE status = ?", array(0)));
    for ($i = 0; $i < count($content); $i++) {
        foreach ($content[$i] as $key => $value) {
            $content[$i][$key]['author_FK'] = (user_control::getUser($value['author_FK'])[0][1]);
            $content[$i][$key]['status'] = $status_text[$value['status']];
        }
    }
    return $content;
}

function getUserArticles($role) {
    if($role == 0) {
        // zobrazeni clanku bezneho uzivatele
        $content= array(
            //v přijímacím řízení
            db::selectAll("SELECT * FROM articles WHERE author_FK = ? AND status BETWEEN ? AND ?", array($_SESSION['user']->getUserId(), 1, 3)),
            //zamítnuté
            db::selectAll("SELECT a.*, AVG(r.art_val), AVG(r.grammar), AVG(r.theme) FROM articles a JOIN reviews r ON a.articles_id = r.articles_FK
              WHERE a.author_FK = ? AND a.status = ? GROUP BY a.articles_id", array($_SESSION['user']->getUserId(), 4)),
            //přijaté
            db::selectAll("SELECT a.*, AVG(r.art_val), AVG(r.grammar), AVG(r.theme) FROM articles a JOIN reviews r ON a.articles_id = r.articles_FK
              WHERE a.author_FK = ? AND a.status = ? GROUP BY a.articles_id", array($_SESSION['user']->getUserId(), 0))
        );
    } elseif ($role == 1) {
        // zobrazeni recenzenta
        $content = array(
            //nové články k ohodnocení
            db::selectAll("SELECT a.* FROM articles a JOIN reviews r ON a.articles_id = r.articles_FK
              WHERE r.reviewer_FK = ? AND a.status BETWEEN  ? AND ? AND r.created = ?", array($_SESSION['user']->getUserId(), 2, 3, 0)),
            // ohodnocené články, zatím nebyly adminem rozhodnuty
            db::selectAll("SELECT a.* FROM articles a JOIN reviews r ON a.articles_id = r.articles_FK
              WHERE r.reviewer_FK = ? AND a.status BETWEEN  ? AND ? AND r.created = ?", array($_SESSION['user']->getUserId(), 2, 3, 1))
        );
    } else {
        //zobrazeni admina
        $content = array(
            //nové články
            db::selectAll("SELECT * FROM articles WHERE status = ?", array(1)),
            //čekají na schválení
            db::selectAll("SELECT a.*, AVG(r.art_val), AVG(r.grammar), AVG(r.theme) FROM articles a JOIN reviews r ON a.articles_id = r.articles_FK
              WHERE a.status = ? GROUP BY a.articles_id", array(3)),
            //právě rezenzovány
            db::selectAll("SELECT a.*, AVG(r.art_val), AVG(r.grammar), AVG(r.theme) FROM articles a JOIN reviews r ON a.articles_id = r.articles_FK
              WHERE a.status = ? GROUP BY a.articles_id", array(2)),
            //odmítnuté
            db::selectAll("SELECT a.*, AVG(r.art_val), AVG(r.grammar), AVG(r.theme) FROM articles a JOIN reviews r ON a.articles_id = r.articles_FK
              WHERE a.status = ? GROUP BY a.articles_id", array(4))
        );
    }
    return $content;
}

function assignReviewer($article, $user) {
    try {
        db::insert('reviews', array('articles_FK' => $article, 'reviewer_FK' => $user));
        db::updateRecord("UPDATE articles a JOIN reviews r ON a.articles_id = r.articles_FK SET a.status = ? WHERE r.articles_FK = ?", array(2, $article));
        return "Reviewer #" . $user . " was assigned to article #" . $article . ".";
    } catch (PDOException $e) {
        return "ERROR: That user is already reviewing this article";
    }
}

function addPost() {
    if ($_FILES['content']['error'] !== UPLOAD_ERR_OK) {
        return "ERROR: Upload failed with error " . $_FILES['content']['error'];
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['content']['tmp_name']);
    switch ($mime) {
        case 'application/pdf':
            break;
        default:
            return "ERROR: Unknown/not permitted file type";
    }
    $file = $_SESSION['user']->getUserNick()."_".$_FILES['content']['name'];        //nový název souboru
    $file_loc = $_FILES['content']['tmp_name'];
    $folder="uploads/";
    if (file_exists($folder.$file))
        return "ERROR: File with this name already exists!";
    move_uploaded_file($file_loc,$folder.$file);
    db::insert('articles', array('abstract' => $_POST['abstract'], 'content' => $file, 'author_FK' => $_SESSION['user']->getUserId(), 'status' => 1));
    return "Post was successfully  created.";
}

function editPost() {
    if(is_uploaded_file($_FILES['content']['tmp_name'])) {
        if ($_FILES['content']['error'] !== UPLOAD_ERR_OK) {
            return "ERROR: Upload failed with error " . $_FILES['content']['error'];
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['content']['tmp_name']);
        switch ($mime) {
            case 'application/pdf':
                break;
            default:
                return "ERROR: Unknown/not permitted file type";
        }
        $oldFile = db::selectOne("SELECT content FROM articles WHERE articles_id = ?", array($_POST['article']));
        unlink($oldFile);
        $file = $_SESSION['user']->getUserNick() . "_" . $_FILES['content']['name'];        //nový název souboru
        $file_loc = $_FILES['content']['tmp_name'];
        $folder = "uploads/";
        if (file_exists($folder . $file))
            return "ERROR: File with this name already exists!";
        move_uploaded_file($file_loc, $folder . $file);
        db::updateRecord("UPDATE articles SET abstract = ?, content = ? WHERE articles_id = ?", array($_POST['abstract'], $file, $_POST['article']));
    } else {
        db::updateRecord("UPDATE articles SET abstract = ? WHERE articles_id = ?", array($_POST['abstract'], $_POST['article']));
    }
    return "Post #" . $_POST['article'] . " was changed.";
}

function deleteArticle($id) {
    db::updateRecord("DELETE FROM articles WHERE articles_id = ?", array($id));
    return "Post #" . $id . " was deleted.";
}

function deleteUser($user) {
    db::updateRecord("DELETE FROM users WHERE users_id = ?", array($user));
    return "user #" . $user . " was deleted.";
}

function changeUserRole($role, $user) {
    db::updateRecord("UPDATE users SET role = ? WHERE users_id = ?", array($role, $user));
    return "User role changed";
}

function review($article, $reviewer) {
    db::updateRecord("UPDATE reviews SET grammar = ?, art_val = ?, theme = ? WHERE articles_FK = ? AND reviewer_FK = ?",
        array($_POST['grammar'], $_POST['art_value'], $_POST['theme'], $article, $reviewer->getUserId()));
    $created = db::selectOne("SELECT created FROM reviews WHERE articles_FK = ? AND reviewer_FK = ?", array($article, $reviewer->getUserId()));
    if ($created[0] == 0) {
        db::updateRecord("UPDATE articles SET reviewed_by = reviewed_by + 1 WHERE articles_id = ?", array($article));
        db::updateRecord("UPDATE reviews SET created = ? WHERE articles_FK = ? AND reviewer_FK = ?",
            array(1 , $article, $reviewer->getUserId()));
    }
    $num_reviews = db::selectOne("SELECT reviewed_by FROM articles WHERE articles_id = ?", array($article));
    if ($num_reviews[0] >= 3) {
        changeStatus($article, 3);
    }
    return "Review for article #" . $article . " was created.";
}

function changeStatus($article, $status) {
    db::updateRecord("UPDATE articles SET status = ? WHERE articles_id = ?", array($status, $article));
    return "Post #" . $article . ": status changed to " . $status . ".";
}