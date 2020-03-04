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
//1=true,0=false to make it tinyint(1) in DB
$callback = $_POST['callback'] === 1 ? 1 : 0;

//Get connection
$instance = ConnectDb::getInstance();
$conn = $instance->getConnection();


//---STEP 1---//
//Send query to DB to check if user exists
try {
    $rs = $conn->prepare('SELECT * FROM burger.users WHERE email = ?');
    $rs->execute([$_POST['email']]);
    $foo = $rs->fetchAll();
} catch (Exception $e) {
    die("Oh noes! There's an error in the query!");
}

if ( isset($foo) && empty($foo) ){
    try {
        $rs = $conn->prepare('INSERT INTO burger.users (user_name, email, phone) values (?,?,?)');
        $rs->execute([$_POST['name'],$_POST['email'],$_POST['phone']]);
        $rs = $conn->prepare('INSERT INTO burger.orders (id_user, address, comment, payment, callback) values (?,?,?,?,?)');
        $rs->execute([$foo[0]['id'],$u_address,$_POST['comment'],$payment,$callback]);
        session_start();
        $_SESSION['user'] = true;
    } catch (Exception $e) {
        die("Oh noes! There's an error in the query!");
    }
}else if( !empty($foo) ){
    session_start();
    $_SESSION['user'] = true;
    try {
        $rs = $conn->prepare('INSERT INTO burger.orders (id_user, address, comment, payment, callback) values (?,?,?,?,?)');
        $rs->execute([$foo[0]['id'],$u_address,$_POST['comment'],$payment,$callback]);
    } catch (Exception $e) {
        die("Oh noes! There's an error in the query!");
    }

    //---STEP 3---//
    try {
        $rs = $conn->prepare('SELECT orders.id ,orders.address, orders.comment, orders.payment, orders.callback, users.phone, users.user_name, users.email FROM burger.users LEFT JOIN burger.orders ON users.id = orders.id_user where orders.id_user = ?');
        $rs->execute([$foo[0]['id']]);
        $foo = $rs->fetchAll();
    } catch (Exception $e) {
        die("Oh noes! There's an error in the query!");
    }
    echo '<pre>';
    print_r($foo);
    echo '</pre>';
    if (count($foo) === 1){
        echo 'This is your first order: ' . $foo['id'];
    }
    if(count($foo) > 1){
        $arr = [
            'Order_Number' => "Заказ № " . $foo[sizeof($foo)-1]['id'] . PHP_EOL,
            'Address'      => 'Ваш заказ будет доставлен по адресу: '. $foo[sizeof($foo)-1]['address'] . PHP_EOL,
            'Product'      => 'DarkBeefBurger за 500 рублей, 1 шт.' . PHP_EOL,
            'Comment'      => 'Спасибо! Это уже '. count($foo) . ' заказ' . PHP_EOL,
            'Date'          => date('y-m-d',time()) . PHP_EOL
        ];
        $file = '../orders/order' . $foo[sizeof($foo)-1]['id'] .'.txt';
        file_put_contents($file, $arr);
        echo  'test';
    }
//    header('../index.html?massage=successfully');
}else{
    echo 'Something goes wrong, Please check your configuration';
}




//function insertDb($conn,array $arr, array $arr2,string $table) {
//        $rs = $conn->prepare("'INSERT INTO $table ($arr) values (?,?,?,?,?)'");
//        $rs->execute([$arr2]);
//}