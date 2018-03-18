<?php

if (!defined( "_DB_PGSQL_LAYER" ))
{
  define("_DB_PGSQL_LAYER", 1);
  
  class Db
    {   
      var $ConnectionID;
      var $DatabaseName;
      var $Result;
      var $Row;
      
      function DbConnect($host, $user, $passwd, $database='')
	{
	  $this->ConnectionID = @pg_connect("host=".$host." port=5432 dbname=".$database." user=".$user." password=".$passwd);
	  if(!$this->ConnectionID)
	  	return false;
	  if (pg_connection_status($this->ConnectionID) != "0")
	    return( false );

	  /*if ($database)
	    return( $this->DbSelectDatabase($database) );*/
	  
	  return( true );	
	}
      /*
      function DbSelectDatabase($database) 
	{
	  $this->DatabaseName = $database;
	  
	  if ($this->ConnectionID)
	    return @pg_select_db($database, $this->ConnectionID);		
	  else
	    return false;	
	}
      */
      function DbQuery($query, $start = '', $limit = '')
	{
	  if ($start != '' || $limit != '')
	    {
	      $query .= ' LIMIT '.$start.','.$limit;
	    }
	  
	  $this->Result = @pg_query($this->ConnectionID , $query);
	  return( $this->Result );
        }
      
      function DbNumRows()
	{
	  $count = @pg_num_rows($this->Result);
	  return( $count );
        }
      
      function DbNextRow()
	{
	  $this->Row = @pg_fetch_array($this->Result);
	  return( $this->Row );
        }

      function DbError()
	{
	  if( pg_connection_status($this->ConnectionID) != 0){
	  return tr("ERROR_DBCONNECT_3");
	  }
	  return pg_last_error($this->ConnectionID);
	}

      
      function DbCreate($db_name)
	{
	  if(pg_query("CREATE DATABASE $db_name")) return 1;
	  else return 0;
	}
      

      function DbAffectedRows()
	{
	  return @pg_affected_rows($this->Result);
	}

    }

}

?>