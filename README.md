# PDO-Simple-Class
A Simple PHP database object (PDO) class

## Getting Started
### 1. Include database.php in your project.
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

```
$sql = "SELECT * FROM member";
$data = $db->dbQuery($sql);
```
result will be
```
Array
(
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
