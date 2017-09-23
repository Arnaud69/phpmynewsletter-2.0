<?php
if (!defined( "_DB_MYSQL_LAYER" )){
    define("_DB_MYSQL_LAYER", 1);
    class Db{
        var $ConnectionID;
        var $DatabaseName;
        var $Result;
        var $Row;

        function DbConnect($host, $user, $passwd, $database){
            $this->ConnectionID = @mssql_connect($host, $user, $passwd);

            if (!$this->ConnectionID)
                return (false);

            if ($database)
                return ($this->DbSelectDatabase($database));

            return (true);
        }
        function DbSelectDatabase($database){
            $this->DatabaseName = $database;

            if ($this->ConnectionID)
                return @mssql_select_db($database, $this->ConnectionID);
            else
                return false;
        }
        function DbQuery($query, $start = '', $limit = ''){
            if ($start != '' || $limit != ''){
                $query .= ' LIMIT '.$start.','.$limit;
            }
            $this->Result = @mssql_query ($query, $this->ConnectionID);
            return ($this->Result);
        }
        function DbNumRows(){
            $count = @mssql_num_rows ($this->Result);
            return ($count);
        }
        function DbNextRow(){
            $this->Row = @mssql_fetch_array ($this->Result);
            return ($this->Row);
        }
	function DbAffectedRows(){
	  return @mssql_rows_affected($this->ConnectionID);
	}
    }
}

?>