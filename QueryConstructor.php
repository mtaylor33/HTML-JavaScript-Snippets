<?php
class QueryConstructor
{
	protected $stmt_delete = null;

	protected $stmt_insert = null;

	protected $stmt_select = null;

	protected $stmt_update = null;

	protected $stmt_where = null;

	protected $stmt_set = false;

	protected $stmt_SQL = null;

	
	public function build()
	{
		$SQL = [];
		$statements = [
			'delete' => $this->stmt_delete,
			'insert' => $this->stmt_insert,
			'select' => $this->stmt_select,
			'update' => $this->stmt_update
		];
		foreach ( $statements as $type => $set_value )
		{
			if ( $statements[$type] !== null )
			{
				$SQL[$type] = $statements[$type];
				break;
			}
		}
		unset($statements);
		if ( array_key_exists('delete', $SQL) 
			or array_key_exists('select', $SQL) 
			or array_key_exists('update', $SQL) )
		{
			// Allow WHERE clause
		}
	}

	/**
	 * @param string $table
	 * @return string|bool
	 */
	public function delete($table)
	{
		if ( $this->stmt_set or !isset($table) )
		{
			return false;
		}
		$this->statement_delete = 'DELETE FROM ' . $table;
		$this->stmt_set = true;
		return $this->statement_delete;
	}

	/**
	 * @param string $table
	 * @param array $set_columns
	 * @return string|bool
	 */
	public function insert($table, array $set_columns)
	{
		if ( $this->stmt_set or !isset($table) or empty($set_columns) )
		{
			return false;
		}
		$statement = 'INSERT INTO ' . $table . ' (%s) VALUES (%s)';
		$my_table_columns = array_keys($set_columns);
		$table_columns = [];
		foreach ( $my_table_columns as $column )
		{
			$table_columns = "'" . $column . "'";
		}
		$table_columns = implode(',', $table_columns);
		$my_column_values = array_key_values($set_columns);
		$column_values = [];
		foreach ( $my_column_values as $value )
		{
			if ( !is_numeric($value) )
			{
				$value = "'" . $value . "'";
			}
			else
			{
				$value = inval($value);
			}
			$column_values[] = $value;
		}
		$column_values = implode(',', $column_values);
		$statement = sprintf($statement, $table_columns, $column_values);
		$this->stmt_insert = $statement;
		$this->stmt_set = true;
		return $this->statement_insert;
	}

	/**
	 * @param string $table
	 * @param array $columns
	 * @return string|bool
	 */
	public function select($table, array $columns=null)
	{
		if ( $this->stmt_set or !isset($table) )
		{
			return false;
		}
		if ( $columns != null )
		{
			$columns = implode(',', $columns)
			$this->stmt_select = 'SELECT ' . $columns . ' FROM ' . $table;
		}
		else
		{
			$this->stmt_select = 'SELECT * FROM ' . $table;
		}
		$this->stmt_set = true;
		return $this->stmt_select;
	}

	/**
	 * @param string $table
	 * @param array $set_values
	 * @return string|bool
	 */
	public function update($table, array $set_values)
	{
		if ( $this->stmt_set or !isset($table) or empty($set_values) )
		{
			return false;
		}
		$my_statement = 'UPDATE ' . $table . ' SET ';
		$my_set_values = [];
		foreach ( $set_values as $column => $value )
		{
			if ( !is_numeric($value) )
			{
				$value = "'" . $value . "'";
			}
			else
			{
				$value = inval($value);
			}
			$my_set_values[] = $column . '=' . $value;
		}
		$my_set_values = implode(',', $my_set_values);
		$my_statement = $my_statement . $my_set_values;
		$this->stmt_update = $my_statement;
		$this->stmt_set = true;
		return $this->stmt_update;
	}
	
	/**
	 * @param array $where_values
	 * @return string|bool
	 */
	public function where(array $where_values)
	{
		if ( !$this->stmt_set )
		{
			return false;
		}
	}
}
