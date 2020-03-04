<?php
//DB connection
include('db.class.php');

//Protect to check this file via browser
if(empty($_POST)){
//    header('../index.html?massage=protect');
}

//Check if some fields is empty for example (better to check via JS)
if(empty($_POST['email']) || empty($_POST['phone'])){
//    header('../index.html?massage=emptyFields');
}

//$u_name = $_POST['name'];
//$u_phone = $_POST['phone'];

$u_address = 'Street: '. $_POST['street'] . 'Home: '.$_POST['home'] . 'A: '.$_POST['part'] . $_POST['appt'] . $_POST['floor'];
$comment = $_POST['comment'];
$payment = $_POST['payment'];
$callback = $_POST['on'] === 'on' ? true : false;

//Get connection
$instance = ConnectDb::getInstance();
$conn = $instance->getConnection();

//Send query to DB to check if user exists
try {
    $rs = $conn->prepare('SELECT * FROM burger.users WHERE email = ?');
    $rs->execute($_POST['email']);
    $foo = $rs->fetchAll();
} catch (Exception $e) {
    die("Oh noes! There's an error in the query!");
}

//
if($foo){
    print_r($foo);
    session_start();
    $_SESSION['user'] = true;
    try {
        $rs = $conn->prepare('INSERT INTO');
        $rs->execute($_POST['email']);
        $foo = $rs->fetchAll();
    } catch (Exception $e) {
        die("Oh noes! There's an error in the query!");
    }

//    header('../index.html?massage=successfully');
}else{

}