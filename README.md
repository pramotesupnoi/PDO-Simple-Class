# PDO-Simple-Class
A Simple PHP database object (PDO) class

## Getting Started
### 1. Include database.php in your project
```
<?php
  include 'database.php';
```

### 2. Create object
```
  $db = new Database();
```

## How to use
### Query
You can query data by use dbQuery function

This function will return array of query result
```
$sql = "SELECT * FROM member";
$data = $db->dbQuery($sql);
```
The result will be
```
Array (
  [0] => Array
    (
      [MemberID] => 1
      [Name] => John Doe
      [BirthDate] => 1991-01-31
      [Phone] => 202-555-0143
      [Email] => john@mail.com
    )
  [1] => Array
    (
      [MemberID] => 2
      [Name] => Jane Doe
      [BirthDate] => 1992-02-01
      [Phone] => 202-555-0131
      [Email] => Jane@mail.com
    )
  [2] => Array
    (
      [MemberID] => 3
      [Name] => David Moore
      [BirthDate] => 1993-03-06
      [Phone] => 202-555-0101
      [Email] => dave_moore@mail.com
    )
)
```


Use parameter to prevent SQL injection
```
$sql = "SELECT * FROM member WHERE MemberID = :id";
$param = array(':id' => 1);
$q = $db->dbQuery($sql, $param);
```
The result will be
```
Array (
  [0] => Array
    (
      [MemberID] => 1
      [Name] => John Doe
      [BirthDate] => 1991-01-31
      [Phone] => 202-555-0143
      [Email] => john@mail.com
    )
)
```


Set fetch style by passing it as a third parameter (Default is PDO::FETCH_ASSOC)
```
$sql = "SELECT Name, BirthDate, Phone, Email FROM member WHERE MemberID = :id";
$param = array(':id' => 1);
$q = $db->dbQuery($sql, $param, PDO::FETCH_COLUMN);
```
The result will be
```
Array (
  [0] => John Doe
)
```


If you want to set fetch style more than one value then you can use array to help you
```
$sql = "SELECT Name, BirthDate, Phone, Email FROM member WHERE MemberID = :id";
$param = array(':id' => 1);
$q = $db->dbQuery($sql, $param, array(PDO::FETCH_COLUMN, 1));
```
The result will be
```
Array (
  [0] => 1991-01-31
)
```

If your sql statement is INSERT, UPDATE OR DELETE function will return number of affected row (Integer)

If there's somethings wrong function will return false (boolean)
