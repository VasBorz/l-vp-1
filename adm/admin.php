<?php
include_once ('../app/db.class.php');
include_once ('../app/functions.php');

$instance = ConnectDb::getInstance();
$conn = $instance->getConnection();

$sql_sel = 'SELECT o.id ,o.address, o.comment, o.payment, o.callback, u.phone, u.user_name, u.email FROM burger.users AS u LEFT JOIN burger.orders AS o ON u.id = o.id_user';

$foo = queryDb($conn,$sql_sel,['']);
echo '<h1 style="text-align:center;">Order List</h2>';
echo '<div style="display: grid; grid-template-columns: repeat(3,1fr); grid-gap: 30px; color: #fff">';
foreach ($foo as $key => $value){
    echo '<div style="background: #8ac03e; border-radius: 10px; padding: 20px">';
    echo '<strong>Order id:</strong> ' . $value['id'] .'<br>';
    echo '<strong>User Name:</strong> ' . $value['user_name'] .'<br>';
    echo '<strong>Email:</strong> ' . $value['email'] .'<br>';
    echo '<strong>Phone:</strong> ' . $value['phone'] .'<br>';
    echo '<strong>Address:</strong> ' . $value['address'] .'<br>';
    echo '<strong>Order Comment:</strong> ' . $value['comment'] .'<br><br>';
    echo '</div>';
}
echo '<div>';
