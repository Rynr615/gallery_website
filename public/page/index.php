<?php

include "../database/koneksi.php";

session_start();

//$_SESSION['username'] = $username;

if (!isset($_SESSION['username'])) {
    header("Location: ./user/login.php");
    exit();
}else{

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/output.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1 class="text-3xl font-bold underline">
        Hello world!
    </h1>
</body>
</html>
<?php 
}
?>