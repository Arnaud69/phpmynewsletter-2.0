<?php
if (!defined( "_DB_MYSQL_LAYER" )){
    define("_DB_MYSQL_LAYER", 1);
    class Db{   
        var $ConnectionID;
        var $DatabaseName;
        var $Result;
        var $Row;
        function DbConnect($host, $user, $passwd, $database=''){
            $this->ConnectionID = @mysqli_connect($host, $user, $passwd,$database);
            if (!$this->ConnectionID){
                return false ;
            } else {
                return true;
            }
        }
        function DbQuery($query, $start = '', $limit = ''){
            if ($start != '' || $limit != ''){
                $query .= ' LIMIT '.$start.','.$limit;
            }
            $this->Result = @mysqli_query($query, $this->ConnectionID);
            return( $this->Result );
        }
        function DbNumRows(){
            $count = @mysqli_num_rows($this->Result);
            return( $count );
        }
        function DbNextRow(){
            $this->Row = @mysqli_fetch_array($this->Result);
            return( $this->Row );
        }
        function DbError(){
            return mysqli_error();
        }
        function DbCreate($db_name){
            if(mysqli_query("CREATE DATABASE $db_name")) 
                return 1;
            else 
                return 0;
        }
        function DbAffectedRows(){
            return @mysqli_affected_rows($this->ConnectionID);
        }
    }
    function DbError(){
        return mysqli_error();
    }
}
?>
