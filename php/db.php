<?php
require_once("config.php");

session_start();

class DBAccess
{
	private $link;

	public function __construct()
	{
		$this->link = new mysqli('localhost', 'root', '', 'floor');

		if ($this->link->connect_error) {
			die("Connection Failed: " . $this->link->connect_error);
		}
	}

	public function verify_adminlogin($pass)
	{
		$sql = "SELECT COUNT(*) AS count FROM admin WHERE passwd = ?";
		$stmt = $this->link->prepare($sql);
		$stmt->bind_param("s", $pass);
		$stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		return (int)$result['count'];
	}

	public function insert($sql, $params = [])
	{
		try {
			$this->link = new PDO("mysql:host=localhost;dbname=floor", "root", "");
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$stmt = $this->link->prepare($sql);
			$stmt->execute($params);

			return $stmt->rowCount();
			
		} catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
			return false;
		}
	}

	public function update($sql, $params = [])
	{
		try {
			$this->link = new PDO("mysql:host=localhost;dbname=floor", "root", "");
			$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$stmt = $this->link->prepare($sql);
			$stmt->execute($params);

			return $stmt->rowCount();
		} catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
			return false;
		}
	}

	public function get_info_arr($table, $where = "1")
	{
		$sql = "SELECT * FROM `$table` WHERE $where";
		$result = $this->link->query($sql);

		if (!$result) {
			die("Query failed: " . $this->link->error);
		}

		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function run_sql($sql)
	{
		// return $this->link->query($sql);
		$result = $this->link->query($sql);
		if (!$result) {
			die("SQL Error: " . $this->link->error);
		}
		return $result;
	}

	public function mysql_insert_id()
	{
		return $this->link->insert_id;
	}

	public function close()
	{
		$this->link->close();
	}
}
