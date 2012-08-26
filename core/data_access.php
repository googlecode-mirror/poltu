<?php
class Data_access {
	var $SQLHandle;
	public function connect($filename) {
		try {
			$this->SQLHandle = new PDO ( "sqlite:" . $filename );
			
		} catch ( PDOException $e ) {
			echo $e->getMessage () . " :: " . $filename;
		}
	}
	public function TableExists() { 
		$result = $this->RunQuery ( "SELECT name FROM sqlite_master WHERE name='routes'" );
		$i = 0;
		foreach ( $result as $Row ) {
			$i ++;
		}
		if ($i == 0) {
			return false;
		}
		return true;
	}
	public function ExeQuery($sql) {
		$this->SQLHandle->exec ( $sql );
		return $this->SQLHandle->commit ();
	}
	public function RunQuery($sql) {
		$Iterator = $this->SQLHandle->query ( $sql );
		return $Iterator;
	}
	public function createTables() {
		echo "please wait...configuring app engine";
	}
}
?>