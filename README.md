# PDO-Simple-Class
A Simple PHP database object (PDO) class

## Getting Started
### 1. Set database configuration in database.config.ini
```
; example configuration file
[database]
dbtype = "mysql"
charset = "utf8"
host = "localhost"
uname = "root"
pwd = ""
dbname = "test_pdo"
emulate = "true"

[database_log]
dir = "db-log/"
name_prefix = "log_"
name_suffix = ""
ext = "json"
enable = "true"
```

### 2. Include database.php in your project
```
<?php
  include 'pdo-database.php';
```

### 3. Create object
```
  $db = new DatabaseConnection();
```

## How to use
The examples will use member table

#### member table 
| MemberID | Name | BirthDate | Phone | Email
|:-----------:|:------------:|:------------:|:------------:|:------------:|
| 1       |        John Doe |     1991-01-31    | 202-555-0143 | john@mail.com
| 2       |        Jane Doe  |     1992-02-01    | 202-555-0131 | jane@mail.com
| 3       |        David Moor  |     1993-03-06    | 202-555-0101 | dave_moore@mail.com

### Query
You can query data and fetching it by use dbQuery function
The function will return array of query result
```
$sql = "SELECT * FROM member";
$data = $db->dbQuery($sql);
```
The result will be
```
Array (
  [0] => Array (
      [MemberID] => 1
      [Name] => John Doe
      [BirthDate] => 1991-01-31
      [Phone] => 202-555-0143
      [Email] => john@mail.com
    )
  [1] => Array (
      [MemberID] => 2
      [Name] => Jane Doe
      [BirthDate] => 1992-02-01
      [Phone] => 202-555-0131
      [Email] => jane@mail.com
    )
  [2] => Array (
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
$data = $db->dbQuery($sql, $param);
```
The result will be
```
Array (
  [0] => Array (
      [MemberID] => 1
      [Name] => John Doe
      [BirthDate] => 1991-01-31
      [Phone] => 202-555-0143
      [Email] => john@mail.com
    )
)
```

You can set pdo fetch style by passing it as a third parameter (Default is PDO::FETCH_ASSOC)
```
$sql = "SELECT Name, BirthDate, Phone, Email FROM member WHERE MemberID = :id";
$param = array(':id' => 1);
$data = $db->dbQuery($sql, $param, PDO::FETCH_COLUMN);
```
The result will be
```
Array (
  [0] => John Doe
)
```

If you want to set pdo fetch style more than one value then you can simply use array
```
$sql = "SELECT Name, BirthDate, Phone, Email FROM member WHERE MemberID = :id";
$param = array(':id' => 1);
$data = $db->dbQuery($sql, $param, array(PDO::FETCH_COLUMN, 1));
```
The result will be
```
Array (
  [0] => 1991-01-31
)
```

If your sql statement is INSERT, UPDATE OR DELETE the function will return number of affected row (Integer)
If there's somethings wrong the function will return false (boolean)

### Get ID of last insert data
Use lastInsertID function to get the Index of last insert data
```
$id = $db->lastInsertID();
```
The result will be
```
3
```

### Get only one records
Use dbRow function to get only one data recoreds (Result will be null if not found any data)
```
$sql = "SELECT * FROM member WHERE MemberID = 1";
$data = $db->dbRow($sql);
```
The result will be
```
Array (
  [MemberID] => 1
  [Name] => John Doe
  [BirthDate] => 1991-01-31
  [Phone] => 202-555-0143
  [Email] => john@mail.com
)
```

### Get only specific field
Use dbColumn function to get only field you want (Result will be null if not found any data)
```
$sql = "SELECT Name, Phone FROM member ";
$data = $db->dbColumn($sql);
```
The result will be
```
Array (
  [0] => John Doe
  [1] => Jane Doe
  [2] => David Moore
)
```

### Count data
If you only want to count the data row you can use dbCount function
The function will return number of sql result (Integer) or return false (boolean) if execute fail
```
$sql = "SELECT COUNT(*) FROM member";
$count = $db->dbCount($sql);
```
The result will be
```
3
```
