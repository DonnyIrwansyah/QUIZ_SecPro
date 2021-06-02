<?php

include 'functions.php';
$pdo = pdo_connect();
	//start a session
	session_start();

	//create a key for hash_hmac function
    if (empty($_SESSION['key']))
        $_SESSION['key'] = bin2hex(rand(100000,999999));

    //create CSRF token
    $csrf = hash_hmac('sha256', 'this is some string: index.php', $_SESSION['key']);

    //validate token
    if (isset($_POST['submit'])) {
        if (hash_equals($csrf, $_POST['csrf'])) {
            if (!empty($_POST)) {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $title = $_POST['title'];
                // Insert new record into the contacts table
                $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
                $stmt->execute([$name, $email, $phone, $title, $_GET['id']]);
                header("location:index.php");
            }
        } else
            echo 'CSRF Token Failed!';
    }

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    header("location:index.php");
} else {
    die ('No ID specified!');
}

?>