<?php
class QueryConstructor
{
	protected $stmt_SQL = null;
	

	/**
	 * @param string $table
	 */
	public function delete($table)
	{
		if ( $this->stmt_SQL !== null or !isset($table) )
		{
			return false;
		}
		$this->stmt_SQL = 'DELETE FROM ' . $table;
	}

	/**
	 * @param string $table
	 * @param array $set_columns
	 */
	public function insert($table, array $set_columns)
	{
		if ( $this->stmt_SQL !== null or !isset($table) or empty($set_columns) )
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
		$this->stmt_SQL = $statement;
	}

	/**
	 * @param string $table
	 * @param array $columns
	 */
	public function select($table, array $columns=null)
	{
		if ( $this->stmt_SQL !== null or !isset($table) )
		{
			return false;
		}
		if ( $columns != null )
		{
			$columns = implode(',', $columns)
			$this->stmt_SQL = 'SELECT ' . $columns . ' FROM ' . $table;
		}
		else
		{
			$this->stmt_SQL = 'SELECT * FROM ' . $table;
		}
		$this->stmt_set = true;
	}

	/**
	 * @param string $table
	 * @param array $set_values
	 */
	public function update($table, array $set_values)
	{
		if ( $this->stmt_SQL !== null or !isset($table) or empty($set_values) )
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
		$this->stmt_SQL = $my_statement;
		$this->stmt_set = true;
	}
	
	/**
	 * @param array $where_values
	 * @return string|bool
	 */
	public function where(array $where_values)
	{
		if ( $this->stmt_SQL == null or count($where_values) <> 1 )
		{
			return false;
		}
		$my_statement = null;
		foreach ( $this->stmt as $type => $SQL )
		{
			if ( $this->stmt[$type] !== null )
			{
				$my_statement = $this->stmt[$type];
				break;
			}
		}
		$where_statement = 'WHERE ';
		$column = $where_values[0];
		$value = $where_values[1];
		if ( !is_numeric($value) )
		{
			$value = "'" . $value . "'";
		}
		else
		{
			$value = inval($value);
		}
		$where_statement = $where_statement . $column . '=' . $value;
	}
}
