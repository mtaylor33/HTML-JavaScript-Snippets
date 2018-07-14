<?php
/**
 * QueryConstructor
 * @author Marcus T. Taylor <mtaylor3121@gmail.com>
 * @copyright 2018
 */
class QueryConstructor
{
  // Stores ORDER BY clause
  protected $clause_order = null;
  
  // Stores WHERE clause
	protected $clause_where = null;
	
  // Stores SQL statement
	protected $stmt_SQL = null;
	

	/**
	 * @return string|bool
	 */
	public function build()
	{
		if ( $this->stmt_SQL === null )
		{
			return false;
		}
		$statement = $this->stmt_SQL;
    // If applicable, add WHERE clause
		if ( $this->clause_where !== null )
		{
			$statement = $statement . $this->clause_where;
		}
    // If applicable, add ORDER BY clause
    if ( $this->clause_order !== null )
    {
      $statement = $statement . $this->clause_order;
    }
		return trim($statement);
	}
	
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
			$table_columns[] = "'" . $column . "'";
		}
		$table_columns = implode(', ', $table_columns);
		$my_column_values = array_values($set_columns);
		$column_values = [];
		foreach ( $my_column_values as $value )
		{
			if ( !is_numeric($value) )
			{
        // To ignore placing quotes for placeholder text
        if ( strpos($value, ':') !== 0 )
        {
          $value = "'" . $value . "'";
        }
			}
			else
			{
				$value = intval($value);
			}
			$column_values[] = $value;
		}
		$column_values = implode(', ', $column_values);
		$statement = sprintf($statement, $table_columns, $column_values);
		$this->stmt_SQL = trim($statement);
	}
  
  /**
   * @param array $columns
   * @param bool $orders
   */
  public function order_by(array $columns, $orders=false)
  {
    // No empty SQL statement
    // Doesn't begin with SELECT statement
		if ( $this->stmt_SQL === null 
			or strpos($this->stmt_SQL, 'SELECT') !== 0 )
		{
			return false;
		}
    if ( empty($columns) )
    {
      return false;
    }
    $statement = ' ORDER BY ';
    if ( is_array($orders) and count($columns) === count($orders) )
    {
      $order_pairs = [];
      for ( $i=0; $i<count($columns); $i++ )
      {
        $column = $columns[$i];
        $order = $orders[$i];
        if ( !in_array($order, ['ASC', 'DESC']) )
        {
          return false;
        }
        $order_pairs[] = $column . ' ' . $order;
      }
      $order_pairs = implode(', ', $order_pairs);
      $statement = $statement . $order_pairs;
      $this->clause_order = $statement;
    }
    
    if ( !is_array($orders) )
    {
      if ( is_string($orders) and !in_array($orders, ['ASC', 'DESC']) or !$orders )
      {
        $orders = 'ASC';
      }
      $columns = implode(', ', $columns);
      $statement = $statement . $columns . ' ' . $orders;
      $this->clause_order = $statement;
    }
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
    $statement = null;
		if ( $columns != null )
		{
			$columns = implode(', ', $columns);
			$statement = 'SELECT ' . $columns . ' FROM ' . $table;
		}
		else
		{
			$statement = 'SELECT * FROM ' . $table;
		}
    $this->stmt_SQL = trim($statement);
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
		$statement = 'UPDATE ' . $table . ' SET ';
		$my_set_values = [];
		foreach ( $set_values as $column => $value )
		{
			if ( !is_numeric($value) )
			{
        // To ignore placing quotes for placeholder text
        if ( strpos($value, ':') !== 0 )
        {
          $value = "'" . $value . "'";
        }
			}
			else
			{
				$value = intval($value);
			}
			$my_set_values[] = $column . '=' . $value;
		}
		$my_set_values = implode(', ', $my_set_values);
		$statement = $statement . $my_set_values;
		$this->stmt_SQL = trim($statement);
	}
	
	/**
	 * @param array $where_values
	 */
	public function where(array $where_clause)
	{
    // No empty SQL statement
    // No less than one WHERE clause
    // Begins with INSERT statement
		if ( $this->stmt_SQL === null 
			or count($where_clause) < 1 
			or strpos($this->stmt_SQL, 'INSERT') === 0 )
		{
			return false;
		}
		$statement = ' WHERE ';
		$my_where_clause = [];
		foreach ( $where_clause as $column => $value )
		{
			if ( !is_numeric($value) )
			{
        // To ignore placing quotes for placeholder text
        if ( strpos($value, ':') !== 0 )
        {
          $value = "'" . $value . "'";
        }
			}
			else
			{
				$value = intval($value);
			}
			$my_where_clause[] = $column . '=' . $value;
		}
		if ( count($my_where_clause) > 1 )
		{
			$my_where_clause = implode(' AND ', $my_where_clause);
		}
		else
		{
			$my_where_clause = $my_where_clause[0];
		}
		$this->clause_where = $statement . $my_where_clause;
	}
}


//$sql = new QueryConstructor();
//$sql->delete('lcnapvg_notices');
//$sql->select('lcnapvg_notices', ['NID', 'title', 'body']);
//$sql->insert('lcnapvg_notices', ['text' => 'Title', 'body' => 'This is some text']);
//$sql->update('lcnapvg_notices', ['text' => ':title', 'body' => ':body']);
//$sql->where(['NID' => ':nid']);
//$sql->order_by(['NID']);
//echo $sql->build();
