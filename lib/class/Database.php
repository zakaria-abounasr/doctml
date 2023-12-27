<?php

	class Database {

		public $db;
		public $name = '';
		public $info = [];
		public $username;
		public $password;

		public function __construct() {
		}

		public function dbExists($dbname) {
			if (isset($this->db)) {
				if ($this->name == $dbname) {
					return true;
				}
				else {
					$databases = $this->getDataColumn('show databases');
					if (isset($databases) && in_array($dbname, $databases)) {
						return true;
					}
				}
			}

			else {
				$this->dbConnect($dbname);
				if (!empty($this->name) && ($this->name == $dbname)) {
					return true;
				}
			}
			return false;
		}

		public function connect($dbname) {
			if (!empty($dbname)) {
				if (empty($this->name) || ($this->name != $dbname)) {
					try {
						$host = "127.0.0.1";
						$charset="utf8";
						$username = "infotlxp_zakaria";
						$password = "xP.a812/e@5";
						if (($this->username !== null) && ($this->password !== null)){
							$username=$this->username;
							$password=$this->password;
						}
						
						$this->db = new PDO('mysql:host='.$host.'; dbname='.$dbname.'; charset='.$charset, $username, $password);
						$this->name = $dbname;
						$this->setEmulatePrepare();
						$this->setErrorMode('exception');
					}
					catch (Exception $e) {}
				}
			}
		}
		
		public function dbConnect($dbname) {
			if (!empty($dbname)) {
				if (empty($this->name) || ($this->name != $dbname)) {
					try {
						$host = "127.0.0.1";
						$charset="utf8";
						$username = "infotlxp_zakaria";
						$password = "xP.a812/e@5";
						if (($this->username !== null) && ($this->password !== null)){
							$username=$this->username;
							$password=$this->password;
						}
					
						$this->db = new PDO('mysql:host='.$host.'; dbname='.$dbname.'; charset='.$charset, $username, $password);
						$this->name = $dbname;
						$this->setEmulatePrepare();
						$this->setErrorMode('exception');
					}
					catch (Exception $e) {}
				}
			}
		}

		public function setUser($username, $password) {
			if (!empty($username) && is_string($username) && isset($password)) {
				$this->username = $username;
				$this->password = $password;

			}
		}
	


		public function insert($table, $columns, $values) {
			if (!empty($table) && is_string($table)) {
				if ($this->tableExists($table)) {
					if (!empty($columns)) {
						if (is_string($columns)) {
							$columns_list = explode(', ', $columns);
						}
						else if (is_array($columns)) {
							$columns_list = $columns;
						}
					}
					if (!empty($values)) {
						if (is_string($values) && !empty($columns_list)) {
							$values_list = explode(', ', $values);
						}
						else if (is_array($values)) {
							$values_list = $values;
						}
					}
					if (!empty($values_list) && ((!empty($columns_list)  && (sizeof($columns_list) == sizeof($values_list))) || empty($columns_list))) {
						if (!empty($columns_list)) {
							$i = 0;
							foreach ($columns_list as $c => $column) {
								if ($i != $c) {
									$drop_query = true;
								}
								if (!$this->columnExists($table, $column)) {
									$drop_query = true;
								}
								$i++;
							}
						}
						foreach ($values_list as $value) {
							if (!is_string($value) && !is_int($value) && !empty($value)) {
								$drop_query = true;
							}
							$index[] = '?';
						}
						if (empty($drop_query)) {
							if (empty($columns_list)) {
								$table_columns = $this->getData('show columns from '.$table);
								if (sizeof($table_columns) == sizeof($values_list)) {
									$query = 'insert into '.$table.' values ('.implode(', ', $index).')';
								}
							}
							else {
								$query = 'insert into '.$table.' ('.implode(', ', $columns_list).') values ('.implode(', ', $index).')';
							}
							if (isset($query)) {
								try {
									$prepare = $this->db->prepare($query);
									$prepare->execute($values_list);
									$prepare->closeCursor();
									return true;
								}
								catch (Exception $e) {
									return null;
								}
							}
						}
					}
				}
			}
		}
		
		public function update($table, $columns, $values, $id) {
			if (!empty($table) && is_string($table)) {
				if ($this->tableExists($table)) {
					if (!empty($columns)) {
						if (is_string($columns)) {
							$columns_list = explode(', ', $columns);
						}
						else if (is_array($columns)) {
							$columns_list = $columns;
						}
					}
					if (!empty($values)) {
						if (is_string($values)) {
							$values_list = explode(', ', $values);
						}
						else if (is_array($values)) {
							$values_list = $values;
						}
					}
					if (!empty($values_list) && !empty($columns_list)  && (sizeof($columns_list) == sizeof($values_list)) && isset($id)) {						
						$i = 0;
						foreach ($columns_list as $c => $column) {
							if ($i != $c) {
								$drop_query = true;
							}
							if (!$this->columnExists($table, $column)) {
								$drop_query = true;
							}
							$i++;
						}
						foreach ($values_list as $value) {
							if (!is_string($value) && !is_int($value) && !empty($value)) {
								$drop_query = true;
							}
							$index[] = '?';
						}
						if (empty($drop_query)) {
							$id_column = $this->getIDColumn($table);
							if (!empty($id_column)) {
								if (is_array($id)) {
									$query = 'update '.$table.' set '.implode(' = ?, ', $columns_list).' = ? where '.$id_column.' in ('.implode(', ', $id).')';
								}
								else if (is_string($id) || is_int($id)) {
									$query = 'update '.$table.' set '.implode(' = ?, ', $columns_list).' = ? where '.$id_column.' in ('.$id.')';
								}
							}
							if (isset($query)) {
								try {
									$prepare = $this->db->prepare($query);
									$prepare->execute($values_list);
									$prepare->closeCursor();
									return true;
								}
								catch (Exception $e) {
									return null;
								}
							}
						}
					}
				}
			}
		}
		
		public function insertNewLine($table) {
			if (isset($table) && is_string($table)) {
				if ($this->tableExists($table)) {
					foreach ($this->columns($table) as $column) {
						if (isset($_POST[$column['Field']])) {
							if (is_string($_POST[$column['Field']])) {
								$posted[$column['Field']] = trim($_POST[$column['Field']]);
							}
							else if (is_array($_POST[$column['Field']])) {
								$posted[$column['Field']] = trim(implode(', ', $_POST[$column['Field']]));
							}
						}
					}
					if (!empty($posted)) {
						$query = 'insert into '.$table.' ('.implode(', ', array_keys($posted)).') values ('; // "'.implode('", "', $posted).'")';
						foreach ($posted as $key => $value) {
							if ($value === "") {
								$query .= 'null';
							}
							else {
								$type = $this->getColumnType($table, $key);
								if (in_string($type, 'decimal')) {
									$query .= str_replace([' ', ','], ['', '.'], $value);
								}
								elseif (in_string($type, 'int')) {
									$query .= intval($value);
								}
								else {
									$query .= '"'.str_replace(['\\', '"'], ['', '\"'], $value).'"';
								}
							}
							if ($key != array_key_last($posted)) {
								$query .= ', ';
							}
						}
						$query .= ')';
						try {
							$this->db->query($query);
							$notification = new Item([
								'class' => styles('notification'), 
								'content' => date("H:i:s").' - Enregistrement bien ajouté !!',
								'events' => ['mousemove' => "setTimeout( function() { document.querySelector('.notification').style.display = 'none'; }, '2500')"]
							]);
							foreach ($this->columns($table) as $column) {
								unset ($_POST[$column['Field']]);
							}
							return $notification;
						}
						catch (Exception $e) {
							$notification = new Item([
								'class' => styles('notification_error'),
								'content' => date("H:i:s").' - Erreur d\'ajout du nouvel enregistrement !!<br><br>'.substr($e, 0, 1000).'<br>',
								'events' => ['click' => "this.style.display = 'none';"]
							]);
							unset ($_POST['save_form']);
							unset ($_POST['duplicate']);
							return $notification;
						}
					}
				}
			}
		}
		
		public function updateLine($table, $line) {
			if (isset($table) && is_string($table)) {
				if (isset($line)) {
					if ($this->tableExists($table)) {
						foreach ($this->columns($table) as $column) {
							if (isset($_POST[$column['Field']])) {
								if (is_string($_POST[$column['Field']])) {
									$posted[$column['Field']] = trim($_POST[$column['Field']]);
								}
								else if (is_array($_POST[$column['Field']])) {
									$posted[$column['Field']] = trim(implode(', ', $_POST[$column['Field']]));
								}
							}
						}
						if (!empty($posted)) {
							$id_column = $this->getIDColumn($table);
							$query = 'update '.$table.' set ';
							foreach ($posted as $key => $value) {
								if ($value === "") {
									$query .= $key.' = null';
								}
								else {
									$type = $this->getColumnType($table, $key);
									if (in_string($type, 'decimal')) {
										$query .= $key.' = '.str_replace([' ', ','], ['', '.'], $value);
									}
									elseif (in_string($type, 'int')) {
										$query .= $key.' = '.intval($value);
									}
									else {
										$query .= $key.' = "'.str_replace(['\\', '"'], ['', '\"'], $value).'"';
									}
								}
								if ($key != array_key_last($posted)) {
									$query .= ', ';
								}
							}
							$query .= ' where '.$id_column.' = '.$line;
							try {
								$this->db->query($query);
								$notification = new Item([
									'class' => styles('notification'), 
									'content' => date("H:i:s").' - Modification bien enregistée !!',
									'events' => ['mousemove' => "setTimeout( function() { document.querySelector('.notification').style.display = 'none'; }, '2500')"]
								]);
								foreach ($this->columns($table) as $column) {
									if (isset($_POST[$column['Field']])) {
										unset ($_POST[$column['Field']]);
									}
								}
								return $notification;
							}
							catch (Exception $e) {
								$notification = new Item([
									'class' => styles('notification_error'),
									'content' => date("H:i:s").' - Erreur d\'enregitrement de la modification !!<br><br>'.substr($e, 0, 1000).'<br>',
									'events' => ['click' => "this.style.display = 'none';"]
								]);
								unset ($_POST['save_form']);
								return $notification;
							}
						}
					}
				}
			}
		}
		
		
		public function tables($suffixes) {
			if (isset($this->db)) {
			if (isset($this->tables) && is_array($this->tables)) { $tables = $this->tables; }
			else { $tables = $this->getDataColumn('show tables');
			if (isset($tables) && is_array($tables)) {
			$this->tables = $tables; }}
			if (isset($suffixes) && is_array($suffixes)) {
			foreach ($tables as $table) {
			foreach ($suffixes as $suffix) {
			if (string_starts_by($suffix, $table)) {
			$xtables[] = $table; }}}}
			if (isset($xtables) && is_array($xtables)) { return $xtables; }
			else { return $tables; }}
			return [];
		}

		public function columns($table) {
			if (isset($table)) {
			if (isset($this->db)) {
			if (isset($this->columns[$table])) { return $this->columns[$table]; }
			else if ($this->tableExists($table)) {
			$columns = $this->getDataTable('show columns from '.$table); }
			if (isset($columns) && is_dbl_array($columns)) {
			$this->columns[$table] = $columns; return $columns; }}}
			return [];
		}

		public function tableExists($table) {
			if (isset($table) && in_array($table, $this->tables([]))) {
			return true; }
			return false;
		}

		public function columnExists($table, $column_name) {
			if (!empty($this->columns($table))) {
			foreach ($this->columns($table) as $column) {
			if ($column['Field'] == $column_name) {
			return true; }}}
			return false;
		}

		public function getColumnData($table, $column_name) {
			if (!empty($this->columns($table))) {
			foreach ($this->columns($table) as $column) {
			if ($column['Field'] == $column_name) {
			return $column; }}}
			return [];
		}

		public function getColumnName($table, $column_name) {
			$column = $this->getColumnData($table, $column_name);
			if (!empty($column) && is_array($column)) {
			return $column['Field']; }
			return false;

		}

		public function getColumnType($table, $column_name) {
			$column = $this->getColumnData($table, $column_name);
			if (!empty($column) && is_array($column)) {
			return $column['Type']; }
			return false;
		}

		public function getColumnSize($table, $column_name) {
			$type= $this->getColumnType($table, $column_name);
			if (!empty($type) && (in_string($type, ['int', 'varchar']))) {
				$start = strpos($type, '(')+1;
				$end = strpos($type, ')');
				$lenght = $end-$start;
				$size = substr($type, $start, $lenght);
			}
			if (!empty($size)) { return $size; }
			return false;
		}

		public function getColumnKey($table, $column_name) {
			$column = $this->getColumnData($table, $column_name);
			if (!empty($column) && is_array($column)) {
			return $column['Key']; }
			return false;
		}

		public function getPrimaryColumns($table) {
			foreach ($this->columns($table) as $column) {
			if ($this->getColumnKey($table, $column['Field']) == 'PRI') {
			$primary_columns[] = $column['Field']; }}
			if (isset($primary_columns)) { return $primary_columns;	}
			return [];
		}

		// -----------------------------------------------------------------------------------------------

		public function getForeignTable($table, $column) {
			$query = 'select REFERENCED_TABLE_NAME from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where CONSTRAINT_SCHEMA = "'.$this->name.'" and TABLE_NAME = "'.$table.'" and COLUMN_NAME = "'.$column.'"';
			$parent_table = $this->getDataCase($query);
			if (isset($parent_table)) {
				return $parent_table;
			}
			return false;
		}
		
		public function getForeignField($table, $column) {
			$query = 'select REFERENCED_COLUMN_NAME from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where CONSTRAINT_SCHEMA = "'.$this->name.'" and TABLE_NAME = "'.$table.'" and COLUMN_NAME = "'.$column.'"';
			$parent_field = $this->getDataCase($query);
			if (isset($parent_field)) {
				return $parent_field;
			}
			return false;
		}
		
		public function getParentTables($table) {
			if (isset($table) && is_string($table)) {
				if ($this->tableExists($table)) {
					return $this->getTableInfo($table)['parents'];
				}
			}
			return [];
		}

		public function updateTableInfo($table_name) {
			if (isset($table_name) && is_string($table_name)) {
				if (isset($this->info[$table_name])) {
					unset ($this->info[$table_name]);
				}
				$this->getTableInfo($table_name);
			}
		}
		
		public function getTableInfo($table_name) {
			if (isset($table_name) && is_string($table_name)) {
				if (isset($this->info[$table_name])) {
					return $this->info[$table_name];
				}
				if ($this->tableExists($table_name)) {
					$query_current_table = 'SELECT
					INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.REFERENCED_TABLE_NAME as \'referenced_table\'
				FROM
					INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
				WHERE
					INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.CONSTRAINT_SCHEMA = \''.$this->name.'\'
					AND INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.REFERENCED_TABLE_NAME = \''.$table_name.'\'
				LIMIT 1;';
					
					$query_related_tables = 'SELECT
					INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.TABLE_NAME as \'table\',
					INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.REFERENCED_TABLE_NAME as \'referenced_table\'
				FROM
					INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
				WHERE
					INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.CONSTRAINT_SCHEMA = \''.$this->name.'\'
					AND INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.TABLE_NAME = \''.$table_name.'\';';
					
					$current_table = $this->getDataLine($query_current_table);
					$relatedTables = $this->getDataTable($query_related_tables);
					
					if (!empty($current_table['referenced_table'])) {
						if (!empty($relatedTables)) {
							foreach ($relatedTables as $related_table) {
								if ($related_table['table']) {
									$tableInfo['level'] = 2;
									$tableInfo['parents'][] = $related_table['referenced_table'];
								}
							}
						}
						if (!isset($tableInfo['level'])) {
							$tableInfo['level'] = 1;
						}
					}
					else {
						if (!empty($relatedTables)) {
							foreach ($relatedTables as $related_table) {
								if ($related_table['table']) {
									$tableInfo['level'] = 3;
									$tableInfo['parents'][] = $related_table['referenced_table'];
								}
							}
						}
						if (!isset($tableInfo['level'])) {
							$tableInfo['level'] = 0;
						}
					}
					if (empty($tableInfo['parents'])) {
						$tableInfo['parents'] = [];
					}
					$this->info[$table_name] = $tableInfo;
					return $tableInfo;
				}
			}
		}
		
		public function query($query) {
			$this->db->query($query);
		}

		public function prepare($array) {
			if (is_array($array)) {
				foreach ($array as $index => $string) {
					if ($index == 0) { $query = $string; }
					else { $values[] = $string; }
				}
			}
			if (isset($query)) { $prepare = $this->db->prepare($query); }
			if (isset($prepare)) { $prepare->execute($values); $prepare->closeCursor(); }
		}
		
		public function getData($data) {
			if (!empty($data) && is_string($data)) {
				$query = $data;
				unset ($data);
			}
			else if (!empty($data) && is_array($data)) {
				if (!empty($data[0]) && is_string($data[0])) {
					$query = $data[0];
					array_shift($data);
				}
			}
			if (isset($query) && is_string($query)) {
				if ((string_starts_by($query, 'select') || string_starts_by($query, 'show')) && !in_string($query, 'update') && !in_string($query, 'insert') && !in_string($query, 'delete')) {
					$prepare = $this->db->prepare($query);
				}
			}
			if (isset($prepare)) { 
				if (empty($data)) {
					$prepare->execute();
				}
				else {
					$prepare->execute($data);
				}
				while ($current_result = $prepare->fetch(PDO::FETCH_ASSOC)) {
					$result[] = $current_result;
				}
				$prepare->closeCursor();
			}
			if (isset($result)) {
				if (sizeof($result) > 1) {
					if (sizeof($result[0]) > 1) {
						return $result;
					}
					else if (sizeof($result[0]) == 1) {
						foreach ($result as $r) {
							$new_result[] = reset($r);
						}
						return $new_result;
					}
				}
				else if (sizeof($result) == 1) {
					if (sizeof($result[0]) > 1) {
						return $result[0];
					}
					else if (sizeof($result[0]) == 1) {
						return reset($result[0]);
					}
				}
			}
			
		}
		
		public function getList($query) {
			$q = $this->db->query($query);
			if (in_string(substr($query, 6, strpos($query, 'from')-6).'<br/>', ',')) { $key = true; } else {$key = null; }
			while ($data_line = $q->fetch()) {
				if ($key) {
					$data[$data_line[0]] = $data_line[1];
				} else {
					$data[] = $data_line[0];
				}
			}
			$q->closeCursor();
			if (!isset($data)) {
				$data = [];
			}
			return $data;
		}

		public function getDataCase($query) {
			$data = $this->db->query($query)->fetchColumn();
			if ($data === null) {
				$data = "";
			}
			return $data;
		}

		public function getDataLine($query) {
			$q = $this->db->query($query);
			$data = $q->fetch(PDO::FETCH_ASSOC);
			$q->closeCursor();
			if ($data === null) {
				$data = [];
			}
			return $data;
		}

		public function getDataColumn($query) {
			$q = $this->db->query($query);
			while ($data_line = $q->fetch()) {
				$data[] = $data_line[0];
			}
			$q->closeCursor();
			if (!isset($data) || ($data === null)) {
				$data = [];
			}
			return $data;
		}

		public function getDataTable($query) {
			$q = $this->db->query($query);
			while ($data_line = $q->fetch(PDO::FETCH_ASSOC)) {
				$data[] = $data_line;
			}
			$q->closeCursor();
			if (!isset($data) || ($data === null)) {
				$data = [];
			}
			return $data;
		}

		public function setEmulatePrepare() {
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
		}

		public function unsetEmulatePrepare() {
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}

		public function setErrorMode($mode) {
			if (isset($mode) && ($mode == 'exception')) {
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} else {
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			}
		}
		
		public function setBufferedMode() {
			$this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		}
		
		public function setUnbufferedMode() {
			$this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
		}

		public function setAutoCommit() {
			$this->db->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		}

		public function unsetAutoCommit() {
			$this->db->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
		}

		public function beginTransaction() {
			$this->unsetAutoCommit();
			$this->db->beginTransaction();
		}

		public function commit() {
			$this->db->commit();
			$this->setAutoCommit();
		}

		public function rollback() {
			$this->db->rollBack();
			$this->setAutoCommit();
		}

		// -------------------------------------------------------------------
		// -------------------------------------------------------------------

		public function getIDColumn($table) {
			if (!empty($this->columns($table))) {
				foreach ($this->columns($table) as $column) {
					if ($column['Key'] == 'PRI') { $id_column[] = $column['Field']; }
				}
			}
			if (isset($id_column)) { return implode(', ', $id_column); }
			return null;
		}

		public function getMainColumn($table) {
			if (!empty($this->columns($table))) {
				foreach ($this->columns($table) as $column) {
					if (!in_array($column['Key'], ['PRI', 'MUL'])) { $main_column = $column['Field']; break; }
				}
			}
			if (isset($main_column)) { return $main_column; }
			return null;
		}

		public function insertByForm($table, $form) {
			if ($this->tableExists($table) && ((is_form($form)) || is_data_table($form))) { $this->newInsert($table); $values = $this->setValues($form); }
			if ($this->lastInsertId($table) && isset($values)) { $this->updateTable($table, $values, $this->lastInsertId($table)); }
		}

		public function updateByForm($table, $form, $index) {
			if ($this->tableExists($table) && ((is_form($form)) || is_data_table($form))) { $values = $this->setValues($form); }
			if (isset($values) && isset($index)) {
				$this->updateTable($table, $values, $index); }
		}

		public function deleteRow($table, $control, $index) {
			try { if (is_control($control) && $this->tableExists($table) && isset($index)) {
				if ($control->clicked()) {
					$id_column = $this->getIDColumn($table);
					if (!empty($id_column)) {
						$this->query('delete from '.$table.' where '.$id_column.' = '.$index); }}}}
						catch (exception $e){ }
		}

		public function setValues($form) {
			if ($form->submitted()) {
				foreach ($form->fields() as $field) {
					if (($form->filters() && !in_array($field, $form->filters())) || (empty($form->filters()))) {
						if (isset($_POST[$field->name])) {
							$index = substr($field->name, 0, strpos($field->name, '#'));
							if ($_POST[$field->name] === '') {
								$values[$index] = null;
							} else {
								$values[$index] = $_POST[$field->name];
							}
						}
					}
				}
			}
			if (isset($values)) {
				return $values; }
				return null;
		}

		public function setLastInsertId($table) {
			save('last_id['.$table.']', $this->_db->lastInsertId());
		}

		public function lastInsertId() {
			$lastId = $this->db->query('select last_insert_id()');
			if (isset($lastId)) {
				return $lastId;
			}
			return 'EROOR';
		}

		public function newInsert($table) {
			$id_column = $this->getIDColumn($table);
			if (!empty($id_column)) {
				$this->query('insert into '.$table.' ('.$id_column.') values (null)');
				$this->setLastInsertId($table);
			}
		}

		public function updateTable($table, $values, $id_value) {
			// --------------------------------------------------------------
			$id_column = $this->getIDColumn($table);
			// ----------------------------------------------------------
			if (isset($id_column)) {
				$prepare[] = null;
				foreach ($this->columns($table) as $column) {
					if (array_key_exists($column, $values)) {
						if ($column <> $id_column) {
							$update[] = $column.' = ?';
							$prepare[] = $values[$column];
						}
					}
				}
			}
			// -------------------------------------------------------------------------------------------------------------------------
			if (is_array($update)) { $query = 'update '.$table.' set '.implode(', ', $update).' where '.$id_column.' = '.$id_value; }
			if (isset($query)) { $prepare[0] = $query; }
			if (!empty($prepare[0])) { $this->prepare($prepare); refresh();}
			// ----------------------------------------------------
		}

	}

?>
