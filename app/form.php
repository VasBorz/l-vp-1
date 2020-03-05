<?php
//DB connection
include('db.class.php');
include ('functions.php');

//Protect to check this file via browser
if(empty($_POST)){
    header('Location: ../index.html?massage=protect');
}

//Check if some fields is empty for example (better to check via JS)
if(empty($_POST['email']) || empty($_POST['phone'])){
    header('Location: ../index.html?massage=emptyFields');
}

$u_address = 'Street: '. $_POST['street'] . 'Home: '.$_POST['home'] . 'A: '.$_POST['part'] . $_POST['appt'] . $_POST['floor'];
$comment = $_POST['comment'];
$payment = $_POST['payment'];
$callback = $_POST['callback'] === 1 ? 1 : 0; //1=true,0=false to make it tinyint(1) in DB

//Get connection
$instance = ConnectDb::getInstance();
$conn = $instance->getConnection();

//---STEP 1---//
//Send query to DB to check if user exists
$sql_sel = 'SELECT * FROM burger.users WHERE email = ?';
$sql_ins_usr = 'INSERT INTO burger.users (user_name, email, phone) values (?, ?, ?)';
$sql_ins_ord = 'INSERT INTO burger.orders (id_user, address, comment, payment, callback) values (?, ?, ?, ?, ?)';
$sql_sel_ord = 'SELECT o.id ,o.address, o.comment, o.payment, o.callback, u.phone, u.user_name, u.email FROM burger.users AS u LEFT JOIN burger.orders AS o ON u.id = o.id_user where o.id_user = ?';
$foo = queryDb($conn,$sql_sel,[$_POST['email']]);

if ( isset($foo) && empty($foo) ){
    queryDb($conn,$sql_ins_usr, [$_POST['name'], $_POST['email'], $_POST['phone']]);
    $foo = queryDb($conn,$sql_sel,[$_POST['email']]);
}

if( !empty($foo) ){
    queryDb($conn, $sql_ins_ord, [$foo[0]['id'], $u_address, $_POST['comment'], $payment, $callback]);
    session_start();
    $_SESSION['user'] = true;

    //---STEP 3---//
    $foo = queryDb($conn,$sql_sel_ord,[$foo[0]['id']]);
    if(count($foo) >= 1){
        $arr = [
            'Order_Number' => 'Заказ № ' . $foo[sizeof($foo)-1]['id'] . PHP_EOL,
            'Address'      => 'Ваш заказ будет доставлен по адресу: '. $foo[sizeof($foo)-1]['address'] . PHP_EOL,
            'Product'      => 'DarkBeefBurger за 500 рублей, 1 шт.' . PHP_EOL,
            'Comment'      => 'Спасибо! Это уже '. count($foo) . ' заказ.' . PHP_EOL,
            'Date'          => 'Date of order: ' . date('Y-m-d',time()) . PHP_EOL
        ];
        if (count($foo) === 1){
           $arr['Order_Number'] = 'This is your first order:№ ' . $foo[sizeof($foo)-1]['id'] . PHP_EOL;
        }
        $file = '../orders/order' . $foo[sizeof($foo)-1]['id'] .'.txt';

        file_put_contents($file, $arr);
    }else{
        echo 'Something wrong with writing orders to db';
    }
    header('Location: ../index.html?massage=successfully');
}else{
    echo 'Something goes wrong, Please check your configuration';
}