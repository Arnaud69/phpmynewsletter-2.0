PDO Extended
===========

A PDO extension that will help your developer's life.

Features :
- Several methods like SqlArray(), Sqlrow(), SqlColumn(), SqlValue() that will help you to quickly retrieve a resultset
- You can disconnect, reconnect, pause the connection.
- Bind your values easily, example : 

```php
$Ids = $Cnx->SqlColumn("SELECT Id FROM table WHERE CreationDate BETWEEN ? AND ?", ['2013-01-01', '2013-06-01']);
$Id = $Cnx->SqlValue("SELECT Id FROM table WHERE Id = ?", [35]);
$Id = $Cnx->SqlValue("SELECT Id FROM table WHERE Id = ?", 35); // You don't need an array if you just have 1 value to bind
$Row = $Cnx->SqlRow("SELECT * FROM table WHERE Name LIKE :Name AND Id >= :Id", ['Name' => 'foo%', 'Id' => 22]);
``` 

- Bind, Run and Debug your statements very easily :

```php

$Stmt = $Cnx->Prepare("SELECT Id FROM table WHERE CreationDate BETWEEN ? AND ?");
$Ids = $Stmt->SqlColumn(['2013-01-01', '2013-06-01']); // Fetches Ids in an indexed array
var_dump($Stmt->Debug()->Preview); // SELECT Id FROM table WHERE CreationDate BETWEEN '2013-01-01' AND '2013-06-01'
var_dump($Stmt->Debug()->Duration); // 0.004

``` 

- Create placeholders on the fly :

```php 
$Ids = [2, 5, 8, 24, 26];
try {
    $Rows = $Cnx->SqlArray("SELECT * FROM table WHERE Id IN (".PDOStatementExtended::PlaceHolders($Ids).")", $Ids);
}
catch (StmtException $e) {
    var_dump($e->GetStmt()->queryString); // SELECT * FROM table WHERE Id IN (?, ?, ?, ?, ?)
    var_dump($e->GetStmt()->Preview); // SELECT * FROM table WHERE Id IN (2, 5, 8, 24, 26)
}

// Also works with strings with automatic quote wrapping
$Dates = ['2013-01-01', '2013-06-01'];
try {
    $Rows = $Cnx->SqlArray("SELECT * FROM table WHERE CreationDate IN (".PDOStatementExtended::PlaceHolders($Dates).")", $Dates);
}
catch (StmtException $e) {
    var_dump($e->GetStmt()->queryString); // SELECT * FROM table WHERE CreationDate IN (?, ?)
    var_dump($e->GetStmt()->Preview); // SELECT * FROM table WHERE CreationDate IN ('2013-01-01', '2013-06-01')
}
``` 

- You no longer need to deal with PDO::PARAM_INT, PDO::PARAM_STRING etc. since the PDO type is automatically set depending on the PHP type.

Full Example usage :

```php

	require_once		'PDOExtended/PDOExtended.php';

	define('PDO_DSN', 'mysql:host=localhost;dbname=test');
    define('PDO_USERNAME', 'root');
    define('PDO_PASSWORD', null);   
    
    // Normal call
    $Cnx    =   new PDOExtended(PDO_DSN, PDO_USERNAME, PDO_PASSWORD);
    
    // Singleton call
	class db {
		use PDOExtendedSingleton;
	}	
    db::Cnx(PDO_DSN, PDO_USERNAME, PDO_PASSWORD);
	$Tables	=	db::Cnx()->SqlColumn("SHOW TABLES");
    
    // Let's create our working table...
    $Cnx->Sql("CREATE TABLE IF NOT EXISTS
                            TVSeries (
                                Id TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
                                Name VARCHAR(255),
                                Channel VARCHAR(40),
                                PRIMARY KEY (Id),
                                UNIQUE (Name)
                            ) ENGINE=InnoDb"); 
    // Or db::Cnx()->Sql(...) when using the singleton mode
                            
    // Example of insert with a prepared statement
    $InsertStmt =   $Cnx->Prepare("INSERT IGNORE INTO TVSeries SET Name = ?");          
    $TVSeries   =   Array('Games Of Thrones', 'The Big Bang Theory', 'Dexter'); 
    
    // For each Series, we play the insert statement with a different value
    foreach ($TVSeries AS $Series)
        $InsertStmt->Sql($Series); 
		// $Series is mapped to the "Name" bound value - since it's a string, it'll be bound as PDO::PARAM_STR 
		// If $Series was an integer, it'd be bound as PDO::PARAM_INT automatically
        
    // The expected parameter may also be an array, in case you have more values to bind
    $InsertStmt =   $Cnx->Prepare("INSERT IGNORE INTO TVSeries SET Name = :Name");       
    foreach ($TVSeries AS $Series)
        $InsertStmt->Sql(Array('Name' => $Series));
        
	// Now, let's update the previous rows with the Channel info.
    $Channels    =   Array('HBO', 'CBS', 'ShowTime');
    
    // You're not obliged to prepare your statement before executing them. It's the Sql() method's job.
    $Cnx->Sql("UPDATE TVSeries SET Channel = ? WHERE Name = ?", Array($Channels[0], $TVSeries[0]));
    $Cnx->Sql("UPDATE TVSeries SET Channel = :Channel WHERE Name LIKE :Name", Array('Channel' => $Channels[1], 'Name' => $TVSeries[1]));
    $Cnx->Sql("UPDATE TVSeries SET Channel = :Channel WHERE Id = :Id", Array('Channel' => $Channels[2], 'Id' => 3)); // Id sera typé automatiquement en PDO::PARAM_INT car 2 est un entier
        
	// Now, let's easily retrieve some infos.
		
    // All the table into a multidimensionnal array.
    var_dump($Cnx->SqlArray("SELECT * FROM TVSeries"));
    
    // Juste the Big bang theory row
    var_dump($Cnx->SqlRow("SELECT * FROM TVSeries WHERE Id = ?", 2));
    
    // A list of series
    var_dump($Cnx->SqlColumn("SELECT Name FROM TVSeries WHERE Channel IN (". PDOStatementExtended::PlaceHolders($Channels) .")", $Channels)); // La fonction PlaceHolders produira "IN (?, ?, ?)";
    
    // What's the channel of Dexter ?
    var_dump($Cnx->SqlValue("SELECT Channel FROM TVSeries WHERE Name LIKE :Name", Array('Name' => 'Dexter')));
    
    // Another way to request it
    var_dump($Cnx->SqlValue("SELECT Channel FROM TVSeries WHERE Name LIKE ?", Array('Dexter')));
    
    // Another way to request it
    var_dump($Cnx->SqlValue("SELECT Channel FROM TVSeries WHERE Name LIKE ?", 'Dexter'));
    
    // Association key => value
    var_dump($Cnx->SqlAssoc("SELECT Channel, Name FROM TVSeries WHERE Id = ?", 3));
    
    // Association key => associative array
    var_dump($Cnx->SqlAssoc("SELECT Channel, Id, Name FROM TVSeries WHERE Id = ?", 3, PDOExtended::TO_ARRAY_ASSOC));
    
    // Association key => indexed array
    var_dump($Cnx->SqlAssoc("SELECT Channel, Id, Name FROM TVSeries WHERE Id = ?", 3, PDOExtended::TO_ARRAY_INDEX));
    
    // Association key => stdClass
    var_dump($Cnx->SqlAssoc("SELECT Channel, Id, Name FROM TVSeries WHERE Id = ?", 3, PDOExtended::TO_STDCLASS));
    
    // Association key => value, multiline version (array of keys => values)
    var_dump($Cnx->SqlMultiAssoc("SELECT Channel, Name FROM TVSeries WHERE Id IN (?, ?)", Array(1, 2)));
    
    // Association key => associative array, multiline version (array of keys => associative arrays)
    var_dump($Cnx->SqlMultiAssoc("SELECT Channel, Id, Name FROM TVSeries WHERE Id IN (?, ?)", Array(1, 2), PDOExtended::TO_ARRAY_ASSOC));
    
    // Association key => indexed array, multiline version (array of keys => indexed arrays)
    var_dump($Cnx->SqlMultiAssoc("SELECT Channel, Id, Name FROM TVSeries WHERE Id IN (?, ?)", Array(1, 2), PDOExtended::TO_ARRAY_INDEX));
    
    // Association key => stdClass, multiline version (array of keys => stdClasses)
    var_dump($Cnx->SqlMultiAssoc("SELECT Channel, Id, Name FROM TVSeries WHERE Id IN (?, ?)", Array(1, 2), PDOExtended::TO_STDCLASS));
    
    // You can also invoke all theses methods from a PDOStatementExtended object
    var_dump($Cnx->Prepare("SELECT Channel, Id, Name FROM TVSeries WHERE Id IN (?, ?)")->SqlMultiAssoc(Array(2, 3), PDOExtended::TO_STDCLASS));
    
    // How long the query has taken ?
    $Stmt   =   $Cnx->Prepare("SELECT * FROM TVSeries WHERE Id = :Id OR Name LIKE :Name");
    $Res    =   $Stmt->SqlArray(Array('Id' => 1, 'Name' => 'Dexter'));   
    var_dump($Stmt->Duration);
    
    // What was the real query played ?
    var_dump($Stmt->Debug()->Preview);
    
    // You can disconnect : every call afterwards will result in a PDO Exception until you invoke the Reconnect() method
    $Cnx->Disconnect();
    
    // When disconnected, you can just call Reconnect without having to specify the dsn / username / password again
    $Cnx->Reconnect();
	
	// You can also Pause the connection. It actually disconnects from MySQl, but on the next call (Sql, Query, SqlArray etc) the Reconnect() method will automatically be called so you never have a "not connected" exception thrown
    $Cnx->Pause();
	
	// Why doing this ? When you have a big treatment to do (parsing a big xml for instance), this prevents from getting "sleep connections" issues
    
    // The ping() method tells you wether or not you're effectively connected to MySQl
    var_dump($Cnx->Ping());
    
    // Every other PDO method is available... $Cnx->Query() ou $Cnx->SetAttribute(), etc.
