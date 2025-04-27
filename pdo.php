<?php
$pdo = new PDO('mysql:host=192.168.0.27;port=3306;dbname=study', 
   'william', 'nuggets-ruft');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


?>