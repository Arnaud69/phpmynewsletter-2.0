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
	 * PDOStatementExtended class 
	 * @author Beno!t POLASZEK - 2013
	 */
	 
	Class PDOStatementExtended Extends PDOStatement {
		
		protected	$Keywords		=	Array();
		protected	$BoundValues	=	Array();
		public		$queryString;
		protected	$Preview;
		protected	$Duration;
		protected	$Executed		=	false;
		protected	$ExecCount		=	0;
		
		/**
		 * When bindValue() is called, we store its params
		 */
		public function BindValue($Parameter, $Value, $PDOType = null) {	
			
			# Flush Bound Values if statement has previously been executed
			if ($this->Executed)
				$this->BoundValues	=	Array()		AND		$this->Executed = false;	
				
			$this->BoundValues[]	=	Array('Parameter' => $Parameter, 'Value' => $Value, 'PDOType' => $PDOType);
			parent::BindValue($Parameter, $Value, $PDOType);
			return $this;
		}
		
		/**
		 * Binds several values at once
		 */
		public function BindValues($SqlValues = array()) {
		
			if (empty($SqlValues))
				return $this;
		
			if (!is_array($SqlValues))
				$SqlValues	=	Array($SqlValues);
				
			foreach ($SqlValues AS $Key => $Value)
				if (is_numeric($Key))
					$this   ->  BindValue((int) $Key + 1, $Value, self::PDOType($Value));
				else
					$this   ->  BindValue(':' . $Key, $Value, self::PDOType($Value));
					
			return $this;
		}
		
		/**
		 * Executes query, measures the total time
		 */
		public function Execute($input_parameters = null) {
		
			$Start			=	microtime(true);
			parent::Execute($input_parameters);
			$End			=	microtime(true);
			
			$this->Duration	=	round($End - $Start, 4);			
			$this->Executed	=	true;	
			$this->ExecCount++;	
			
			return $this;
		}
		
		/**
		 * Executes the statement with bounded params
		 * 
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return PDOStatementExtended instance
		 */
		public function Sql($SqlValues = array()) {
			return $this->BindValues($SqlValues)->Execute();
		}
		
		/**
		 * SqlArray executes Query : returns the whole result set
		 *  
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return Array
		 */
		public function SqlArray($SqlValues = array()) {
			return $this->BindValues($SqlValues)->Execute()->FetchAll(PDO::FETCH_ASSOC);
		}
		
		/**
		 * SqlRow executes Query : returns the 1st row of your result set
		 *  
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return Array
		 */
		public function SqlRow($SqlValues = array()) {
			return $this->BindValues($SqlValues)->Execute()->Fetch(PDO::FETCH_ASSOC);
		}
		
		/**
		 * SqlColumn executes Query : returns the 1st column of your result set
		 *  
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return Array
		 */
		public function SqlColumn($SqlValues = array()) {
			return $this->BindValues($SqlValues)->Execute()->FetchAll(PDO::FETCH_COLUMN);
		}
		
		/**
		 * SqlValue executes Query : returns the 1st cell of your result set
		 *  
		 * @param array $SqlValues : Optional PDO Values to bind
		 * @return String
		 */
		public function SqlValue($SqlValues = array()) {
			return $this->BindValues($SqlValues)->Execute()->Fetch(PDO::FETCH_COLUMN);
		}
		
		/**
		 * SqlAssoc executes Query : 
		 * If $DataType == self::TO_STRING : returns an associative array where the 1st column is the key and the 2nd is the value
		 * If $DataType == self::TO_STDCLASS : returns an associative array where the 1st column is the key the others are properties of an anonymous object
		 * If $DataType == self::TO_ARRAY_ASSOC : returns an associative array where the 1st column is the key the others are an associative array
		 * If $DataType == self::TO_ARRAY_INDEX : returns an associative array where the 1st column is the key the others are an indexed array
		 *  
		 * @param array $SqlValues : PDO Values to bind
		 * @param int $DataType : type of data wanted
		 * @return Array
		 */
		public function SqlAssoc($SqlValues = array(), $DataType = self::TO_STRING) {
			$Data	=	$this->BindValues($SqlValues)->Execute()->Fetch(PDO::FETCH_ASSOC);
						
			if ($Data) :			
			
				$Keys	=	array_keys($Data);
				$Result	=	Array();
				
				if ($DataType == PDOExtended::TO_STDCLASS)
					$Result	=	Array($Data[$Keys[0]] => (object) array_slice($Data, 1));
				
				elseif ($DataType == PDOExtended::TO_ARRAY_ASSOC)
					$Result	=	Array($Data[$Keys[0]] => array_slice($Data, 1));
					
				elseif ($DataType == PDOExtended::TO_ARRAY_INDEX)
					$Result	=	Array($Data[$Keys[0]] => array_values(array_slice($Data, 1)));
					
				else // $DataType == PDOExtended::TO_STRING by default
					$Result	=	Array($Data[$Keys[0]] => $Data[$Keys[1]]);
					
				return $Result;
					
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
		 * @param array $SqlValues : PDO Values to bind
		 * @param int $DataType : type of data wanted
		 * @return Array
		 */
		public function SqlMultiAssoc($SqlValues = array(), $DataType = self::TO_STRING) {
			$Data	=	$this->BindValues($SqlValues)->Execute()->FetchAll(PDO::FETCH_ASSOC);
			
			if (array_key_exists(0, $Data)) :			
			
				$Keys	=	array_keys($Data[0]);
				$Result	=	Array();
								
				foreach ($Data AS $Item)
				
					if ($DataType == PDOExtended::TO_STDCLASS)
						$Result[]	=	Array($Item[$Keys[0]] => (object) array_slice($Item, 1));
					
					elseif ($DataType == PDOExtended::TO_ARRAY_ASSOC)
						$Result[]	=	Array($Item[$Keys[0]] => array_slice($Item, 1));
						
					elseif ($DataType == PDOExtended::TO_ARRAY_INDEX)
						$Result[]	=	Array($Item[$Keys[0]] => array_values(array_slice($Item, 1)));
						
					else // $DataType == PDOExtended::TO_STRING by default	
						$Result[]	=	Array($Item[$Keys[0]] => $Item[$Keys[1]]);
					
				return $Result;
			
			else :			
				return $Data;
				
			endif;
			
		}
		
		/**
		 * PDOStatementExtended debug function
		 * 
		 * @return PDOStatementExtended instance
		 * @author Beno!t POLASZEK - Jun 2013
		 */
		public function Debug() {
			
			$this->Keywords	=	Array();
			$this->Preview	=	preg_replace("#\t+#", "\t", $this->queryString);
			
			# Case of question mark placeholders
			if (array_key_exists(0, $this->BoundValues) && $this->BoundValues[0]['Parameter'] === 1) 			
				foreach ($this->BoundValues AS $BoundParam) 									
					$this->Preview	=	preg_replace("/([\?])/", self::DebugValue($BoundParam), $this->Preview, 1);			
			
			# Case of named placeholders
			else 	
				foreach ($this->BoundValues AS $boundValue)
					$this->Keywords[]	=	$boundValue['Parameter'];		
				foreach ($this->Keywords AS $Word) 
					foreach ($this->BoundValues AS $BoundParam) 
						if ($BoundParam['Parameter'] == $Word) 						
							$this->Preview	=	preg_replace("/(".$Word.")/i", self::DebugValue($BoundParam), $this->Preview);
					
			
			return $this;			
		}
		
		/**
		 * String context => query preview
		 */
		public function __toString() {
			return $this->queryString;
		}
		
		/**
		 * Write access on private and protected properties : DENIED
		 */
		public function __set($Key, $Value) {
			return false;
		}
		
		/**
		 * Read access on private and protected properties : TOLERATED
		 */
		public function __get($Key) {
			return $this->$Key;
		}
		
		/**
		 * Add quotes or not for Debug() method
		 */
		private static function DebugValue($BoundParam) {		
			if (in_array($BoundParam['PDOType'], Array(PDO::PARAM_BOOL, PDO::PARAM_INT))) 	
				return (int) $BoundParam['Value'];
				
			elseif ($BoundParam['PDOType'] == PDO::PARAM_NULL) 	
				return 'NULL';
			
			else
				return (string) "'". addslashes($BoundParam['Value']) . "'";
		}
		
		/**
		 * Transforms an indexed array into placeholders
		 * Example : Array(0, 22, 99) ==> '?,?,?'
		 * Usage : "WHERE VALUES IN (". PDOStatementExtended::PlaceHolders($MyArray) .")"
		 * 
		 * @param array $Array
		 * @return string placeholder
		 * @author Beno!t POLASZEK - Jun 2013
		 */
		public static function PlaceHolders($Array = Array()) {
			return implode(',', array_fill(0, count($Array), '?'));
		}		
		
		/**
		 * PDO Automatic type binding 
		 * 
		 * @param mixed var
		 * @return PDO const 
		 */
		public static function PDOType($Var) {
					
			switch (strtolower(gettype($Var))) :
			
				case 'string' :
					return (strtoupper($Var) == 'NULL') ? PDO::PARAM_NULL : PDO::PARAM_STR;
			
				case 'int'  :
				case 'integer'  :
					return PDO::PARAM_INT;
					
				case 'double'   :
				case 'float'    :
					return PDO::PARAM_STR; // No float PDO type at the moment... :(
					
				case 'bool' :
				case 'boolean'  :
					return PDO::PARAM_BOOL;
					
				case 'null' :
					return PDO::PARAM_NULL;
					
				default :
					return PDO::PARAM_STR;
					
			endswitch;
			
		}
	}
