<?php
if (!defined( "_DB_MYSQL_LAYER" )){
	define("_DB_MYSQL_LAYER", 1);
	class Db{   
		var $ConnectionID;
		var $DatabaseName;
		var $Result;
		var $Row;
		function DbConnect($host, $user, $passwd, $database=''){
			$this->ConnectionID = @mysql_connect($host, $user, $passwd);
			if (!$this->ConnectionID)
				return( false );
			if ($database)
				return( $this->DbSelectDatabase($database) );
			return( true );    
		}
		function DbSelectDatabase($database){
			$this->DatabaseName = $database;
			if ($this->ConnectionID)
				return @mysql_select_db($database, $this->ConnectionID);        
			else
				return false;    
		}
		function DbQuery($query, $start = '', $limit = ''){
			if ($start != '' || $limit != ''){
				$query .= ' LIMIT '.$start.','.$limit;
			}
			$this->Result = @mysql_query($query, $this->ConnectionID);
			return( $this->Result );
		}
		function DbNumRows(){
			$count = @mysql_num_rows($this->Result);
			return( $count );
		}
		function DbNextRow(){
			$this->Row = @mysql_fetch_array($this->Result);
			return( $this->Row );
		}
		function DbError(){
			return mysql_error();
		}
		function DbCreate($db_name){
			if(mysql_query("CREATE DATABASE $db_name")) 
				return 1;
			else 
				return 0;
		}
		function DbAffectedRows(){
			return @mysql_affected_rows($this->ConnectionID);
		}
	}
    function DbError(){
        return mysql_error();
    }
}
?>
