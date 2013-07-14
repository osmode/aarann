<?php

function db_connect() {
   $result = new mysqli('localhost', 'username', 'password12345', 'databaseName');
   if (!$result) {
     throw new Exception('Could not connect to database server');
   } else {
     return $result;
   }
}


?>
