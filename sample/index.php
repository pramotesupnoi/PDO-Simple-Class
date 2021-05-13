<?php
    require "../src/pdo-database.php";

    $db = new DatabaseConnection();

    echo "<pre>";
    // INSERT EXAMPLE
    // $sql = "INSERT INTO tb_member (Name, BirthDate, Phone, Email) VALUES (:nm, :bd, :pn, :em)";
    // $params = array(':nm' => "John Doe", ':bd' => "1991-01-31", ':pn' => "202-555-0143", ':em' => "john@mail.com");
    // $exc = $db->dbQuery($sql, $params);
    // var_dump($exc);
    echo "<br>";

    // SELECT EXAMPLE
    $sql = "SELECT * from tb_member";
    $exc = $db->dbQuery($sql);
    print_r($exc);
    
    echo "</pre>";

?>