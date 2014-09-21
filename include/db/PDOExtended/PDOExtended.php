<?php
	/**
	 * MIT License (MIT)
	 * 
	 * Copyright (c) 2013 Beno!t POLASZEK
	 * 
	 * Permission is hereby granted, free of charge, to any person obtaining a copy of
	 * this software and associated documentation files (the "Software"), to deal in
	 * the Software without restriction, including without limitation the rights to
	 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
	 * the Software, and to permit persons to whom the Software is furnished to do so,
	 * subject to the following conditions:
	 * 
	 * The above copyright notice and this permission notice shall be included in all
	 * copies or substantial portions of the Software.
	 * 
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
	 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
	 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
	 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
	 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	 * 
	 * PDOExtended class 
	 * @author Beno!t POLASZEK - 2013
	 */
	require_once		__DIR__	.	'/PDOStatementExtended.php';
	require_once		__DIR__	.	'/StmtException.php';
	Class PDOExtended {		
		protected		$PDO;
		protected		$Dsn;
		protected		$Username;
		protected		$Password;
		protected		$DriversOptions;
		protected		$IsConnected		=	false;
		protected		$IsPaused			=	false;
		const			TO_ARRAY_ASSOC		=	1;
		const			TO_ARRAY_INDEX		=	2;
		const			TO_STRING			=	3;
		const			TO_STDCLASS			=	4;
		/**
		 * Constructor
		 * @link http://php.net/manual/en/pdo.construct.php 
		 * @access public
		 * @author Beno!t POLASZEK - 2013
		 */
		public function __construct() {
			# Catching constructor arguments
			$Args				=	func_get_args();
			# Setting properties
			$this	->	Dsn		=	(array_key_exists(0, $Args)) ? $Args[0] : null;
			$this	->	Username	=	(array_key_exists(1, $Args)) ? $Args[1] : null;
			$this	->	Password	=	(array_key_exists(2, $Args)) ? $Args[2] : null;
			$this	->	DriversOptions	=	(array_key_exists(3, $Args)) ? $Args[3] : null;
			# Creating PDO instance into $this->PDO
			$Class				=	new \ReflectionClass('\PDO');
			$this	->	PDO		=	$Class->NewInstanceArgs($Args);
			$this	->	PDO		->	SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this	->	PDO		->	SetAttribute(PDO::ATTR_STATEMENT_CLASS, Array(__NAMESPACE__ . '\PDOStatementExtended'));	
			# Checking PDO connection
			$this	->	IsConnected	=	$this->Ping();
			$this	->	IsPaused	=	false;
		}
		/**
		 * Constructor alias - useful for chaining
		 * Example : $Status = PDOExtended::NewInstance('mysql:host=localhost', 'user', 'password')->SqlMultiAssoc("SHOW GLOBAL STATUS");
		 */
		public static function NewInstance() {		
			$CurrentClass	=	new \ReflectionClass(get_called_class());
			return $CurrentClass->NewInstanceArgs(func_get_args());
		}
		/**
		 * Destructor : disconnection
		 */
		public function __destruct() {
			$this->Disconnect();
		}
		/**
		 * Magic Shortcut to PDO object methods
		 * @access public
		 * @author Beno!t POLASZEK - 2013
		 */
		public function __call($Name, array $Args) {
			# If the connection was paused, we have to reconnect
			!$this->IsPaused OR $this->Reconnect();
			if (!($this->PDO instanceof \PDO))
				throw new \PDOException("PDO Connection isn't active.");
			return call_user_func_array(array($this->PDO, $Name), $Args);
		}
		/**
		 * Checks if connection is active
		 * 
		 * @return bool true / false
		 * @access public
		 * @author Beno!t POLASZEK - 2013
		 */
		public function Ping() {
			if ($this->IsPaused)
				return false;
			try {
				return (bool) $this->Query("SELECT 1+1");
			}
			catch (Exception $e) {
				return false;
			}
		}
		/**
		 * Closes the current connection
		 * Next calls (query, prepare, etc) will throw an exception unless you use Reconnect() method
		 * 
		 * @return PDOExtended instance
		 * @access public
		 * @author Beno!t POLASZEK - 2013
		 */
		public function Disconnect() {
			$this	->	PDO				=	null;
			$this	->	IsConnected		=	false;
			return $this;
		}
		/**
		 * Pauses the current connection (disconnects temporarily)
		 * The connexion will be closed but reopened at the next call (query, prepare, sql etc)
		 * 
		 * @return PDOExtended instance
		 * @access public
		 * @author Beno!t POLASZEK - 2013
		 */
		public function Pause() {
			$this	->	Disconnect();
			$this	->	IsPaused	=	true;
			return $this;
		}
		/**
		 * Re-opens the connection with the same dsn / username / passwd etc
		 * 
		 * @return PDOExtended instance
		 * @access public
		 * @author Beno!t POLASZEK - 2013
		 */
		public function Reconnect() {
			self::__construct($this->Dsn, $this->Username, $this->Password, $this->DriversOptions);
			return $this;
		}
		/**
		 * Prepares a SQL Statement
		 * 
		 * @param string SqlString : SQL query
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @param array $DriversOptions
		 * @return PDOStatementExtended Stmt 
		 * @access public
		 * @author Beno!t POLASZEK - 2013 
		 */
		public function Prepare($SqlString, $SqlValues = array(), $DriversOptions = array()) {
			# If the connection was paused, we have to reconnect
			!$this->IsPaused OR $this->Reconnect();
			if (!($this->PDO instanceof \PDO))
				throw new \PDOException("PDO Connection isn't active.");
			# The SQL Query becomes a SQL Statement
			$Stmt	=	$this->PDO->Prepare($SqlString, $DriversOptions);
			if (empty($SqlValues))
				return $Stmt;
			# If values have been provided, let's bind them
			else
				$Stmt->BindValues($SqlValues);
			return $Stmt;
		}
		/**
		 * Prepares a SQL Statement and executes it 
		 * 
		 * @param mixed $SqlString : SQL Query (String or instanceof PDOStatement)
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @param array $DriversOptions
		 * @return PDOStatementExtended Stmt (executed)
		 * @access public
		 */
		public function Sql($SqlString, $SqlValues = array(), $DriversOptions = array()) {
			# If the connection was paused, we have to reconnect
			!$this->IsPaused OR $this->Reconnect();
			if (!($this->PDO instanceof \PDO))
				throw new \PDOException("PDO Connection isn't active.");
			# If SqlString isn't a PDOStatement yet
			$Stmt	=	($SqlString instanceof PDOStatement) ? $SqlString : $this->Prepare($SqlString, $SqlValues, $DriversOptions);
			# If values have been provided, let's bind them
			if (!empty($SqlValues))
				$Stmt->BindValues($SqlValues);
			# Execution
			try {
				$Stmt->Execute();
			}
			# Custom PDO Exception, allowing query preview
			catch (PDOException $PDOException) {
				throw new StmtException((string) $PDOException->GetMessage(), $PDOException->GetCode(), $PDOException, $Stmt->Debug());
			}
			# The statement is executed. You can now use Fetch() and FetchAll() methods.
			return $Stmt;			
		}
		/**
		 * SqlArray executes Query : returns the whole result set
		 *  
		 * @param mixed $SqlString : SQL Query (String or instanceof PDOStatement)
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return Array
		 */
		public function SqlArray($SqlString, $SqlValues = array()) {
			return $this->Sql($SqlString, $SqlValues)->FetchAll(PDO::FETCH_ASSOC);
		}
		/**
		 * SqlRow executes Query : returns the 1st row of your result set
		 *  
		 * @param mixed $SqlString : SQL Query (String or instanceof PDOStatement)
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return Array
		 */
		public function SqlRow($SqlString, $SqlValues = array()) {
			return $this->Sql($SqlString, $SqlValues)->Fetch(PDO::FETCH_ASSOC);
		}
		/**
		 * SqlColumn executes Query : returns the 1st column of your result set
		 *  
		 * @param mixed $SqlString : SQL Query (String or instanceof PDOStatement)
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return Array
		 */
		public function SqlColumn($SqlString, $SqlValues = array()) {
			return $this->Sql($SqlString, $SqlValues)->FetchAll(PDO::FETCH_COLUMN);
		}
		/**
		 * SqlValue executes Query : returns the 1st cell of your result set
		 *  
		 * @param mixed $SqlString : SQL Query (String or instanceof PDOStatement)
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return String
		 */
		public function SqlValue($SqlString, $SqlValues = array()) {
			return $this->Sql($SqlString, $SqlValues)->Fetch(PDO::FETCH_COLUMN);
		}
		/**
		 * SqlAssoc executes Query :
		 * If $DataType == self::TO_STRING : returns an associative array where the 1st column is the key and the 2nd is the value
		 * If $DataType == self::TO_STDCLASS : returns an associative array where the 1st column is the key the others are properties of an anonymous object
		 * If $DataType == self::TO_ARRAY_ASSOC : returns an associative array where the 1st column is the key the others are an associative array
		 * If $DataType == self::TO_ARRAY_INDEX : returns an associative array where the 1st column is the key the others are an indexed array
		 *  
		 * @param mixed $SqlString : SQL Query (String or instanceof PDOStatement)
		 * @param array $SqlValues : PDO Values to bind
		 * @param int $DataType : type of data wanted
		 * @return Array
		 */
		public function SqlAssoc($SqlString, $SqlValues = array(), $DataType = self::TO_STRING) {
			$Data	=	$this->Sql($SqlString, $SqlValues)->Fetch(PDO::FETCH_ASSOC);			
			if ($Data) :			
				$Keys	=	array_keys($Data);
				$Result	=	Array();
				if ($DataType == self::TO_STDCLASS)
					$Result	=	Array($Data[$Keys[0]] => (object) array_slice($Data, 1));
				elseif ($DataType == self::TO_ARRAY_ASSOC)
					$Result	=	Array($Data[$Keys[0]] => array_slice($Data, 1));
				elseif ($DataType == self::TO_ARRAY_INDEX)
					$Result	=	Array($Data[$Keys[0]] => array_values(array_slice($Data, 1)));
				else // $DataType == self::TO_STRING by default	
					$Result	=	Array($Data[$Keys[0]] => $Data[$Keys[1]]);
				return $Result;
			else :			
				return $Data;	
			endif;	
		}
		/**
		 * SqlMultiAssoc executes Query : 
		 * If $DataType == self::TO_STRING : returns an associative array where the 1st column is the key and the 2nd is the value
		 * If $DataType == self::TO_STDCLASS : returns an associative array where the 1st column is the key the others are properties of an anonymous object
		 * If $DataType == self::TO_ARRAY_ASSOC : returns an associative array where the 1st column is the key the others are an associative array
		 * If $DataType == self::TO_ARRAY_INDEX : returns an associative array where the 1st column is the key the others are an indexed array
		 *  
		 * @param mixed $SqlString : SQL Query as a string or a PDOStatementExtended
		 * @param array $SqlValues : PDO Values to bind
		 * @param int $DataType : type of data wanted
		 * @return Array
		 */
		public function SqlMultiAssoc($SqlString, $SqlValues = array(), $DataType = self::TO_STRING) {
			$Data	=	$this->Sql($SqlString, $SqlValues)->FetchAll(PDO::FETCH_ASSOC);
			if (array_key_exists(0, $Data)) :			
				$Keys	=	array_keys($Data[0]);
				$Result	=	Array();
				foreach ($Data AS $Item)
					if ($DataType == self::TO_STDCLASS)
						$Result[]	=	Array($Item[$Keys[0]] => (object) array_slice($Item, 1));
					elseif ($DataType == self::TO_ARRAY_ASSOC)
						$Result[]	=	Array($Item[$Keys[0]] => array_slice($Item, 1));
					elseif ($DataType == self::TO_ARRAY_INDEX)
						$Result[]	=	Array($Item[$Keys[0]] => array_values(array_slice($Item, 1)));
					else // $DataType == self::TO_STRING by default	
						$Result[]	=	Array($Item[$Keys[0]] => $Item[$Keys[1]]);
				return $Result;
			else :			
				return $Data;
			endif;
		}
		/**
		 * Prevents from XSS injection
		 * 
		 * @param mixed $Input
		 * @return string
		 * @access public
		 */
		public static function CleanInput($Input, $ScriptTags = true, $StyleTags = true, $MultiLineComments = true) {
			$RemovePatterns = Array();			
			(bool) $ScriptTags AND $RemovePatterns[] = '@<script[^>]*?>.*?</script>@si';	// Strip out javascript
			(bool) $StyleTags  AND $RemovePatterns[] = '@<style[^>]*?>.*?</style>@siU';	// Strip style tags properly
			(bool) $MultiLineComments AND $RemovePatterns[]	= '@<![\s\S]*?--[ \t\n\r]*>@';	// Strip multi-line comments
			return preg_replace($RemovePatterns, null, $Input);
		}				
	}