<?php
require_once('Models.php');
class User_roles extends Models
{
	private static $instance = null;
    protected $db;
    private $table;
    public function __construct()
	{
		require_once($this->getDocumentRoot() . '/inc/conn.php');
        $this->db = DB::getInstance();
        $this->table = 'user_roles';
	}

	public function getWhere($where = '', $sortBy = 'name ASC')
	{
		$sql = "SELECT * FROM $this->table WHERE status != 'D'";
		
		if(!empty($where)) {
			$sql .= " $where";
		}

		$sql .= " ORDER BY $sortBy ";

        $rows = $this->db->select($sql);
        return $rows;
	}

    public function getJoinWhere($where = '', $sortBy = 'ur.name ASC', $startFrom = 0, $pageNo = 20, $enableLimit = 'Y')
	{
		// $db = DB::getInstance();
		$sql = "SELECT ur.*, u.first_name, u.last_name
                FROM $this->table ur 
                LEFT JOIN users u ON u.id = ur.created_by 
                WHERE ur.status != 'D'
                ";
		
		if(!empty($where)) {
			$sql .= " $where";
		}

		$sql .= " ORDER BY $sortBy ";

        if($enableLimit == 'Y') {
            $sql .= " LIMIT $startFrom, $pageNo";
        }
	
		$rows = $this->db->select($sql);
        return $rows;
	}

    public function getTotal($where = '', $sortBy = 'ur.name ASC')
	{
        
		$sql = "SELECT COUNT(*) total_count
                FROM user_roles ur 
				LEFT JOIN users u ON u.id = ur.created_by 
                WHERE ur.status != 'D' ";
		
		if(!empty($where)) {
			$sql .= " $where";
		}

		$sql .= " ORDER BY $sortBy ";
		$rows = $this->db->select($sql, 'assoc');
        return $rows['total_count'];
	}

	public function insertData($data)
	{
        $sql = "INSERT INTO $this->table (";
        $sql .= implode(",", array_keys($data)) . ') VALUES ';            
        $sql .= "('" . implode("','", array_values($data)) . "')";
		$this->db->exec($sql);
		return $this->db->lastInsertId($sql);
	}

    public function updateData($data, $where)
	{
        $set = [];
        foreach($data as $key => $value) {
            $set[] = "$key='$value'";
        }
        
        $sql = "UPDATE $this->table SET ". implode(', ', $set);
        $sql .= " WHERE $where";
		return $this->db->exec($sql);
	}

	public function delete($id)
	{
		$sql = "DELETE FROM $this->table WHERE id=" . $id;
		return $this->db->exec($sql);
	}
}