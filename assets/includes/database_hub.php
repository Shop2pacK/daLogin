<?php 

    // DB Connect, SELECT, INSERT

    require __DIR__ . '/../setup/db.php';

    /* it.. connects */
    function connect() {

        $C = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

        if ($C->connect_error) {

            return false;

        } else {

            return $C;

        }

    }

    /* wrapper for a select statement */
    function sqlSelect($connection, $query, $format = false, ...$vars) {
        $stmt = $connection->prepare($query);
        if($format) {
            $stmt->bind_param($format, ...$vars);            
        }
        if($stmt->execute()) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }
        $stmt->close();
        return false;
    }

    /* wrapper for an insert statement */
    function sqlInsert($C, $query, $format = false, ...$vars) {
        $stmt = $C->prepare($query);
        if($format) {
            $stmt->bind_param($format, ...$vars);            
        }
        if($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return -1;
    }

    /* wrapper for an update statement */
    function sqlUpdate($C, $query, $format = false, ...$vars) {
		$stmt = $C->prepare($query);
		if($format) {
			$stmt->bind_param($format, ...$vars);
		}
		if($stmt->execute()) {
			$stmt->close();
			return true;
		}
		$stmt->close();
		return false;
	}

    /* wrapper for a delete statement */
    function sqlDelete($C, $query, $format = false, ...$vars) {
        $stmt = $C->prepare($query);
        if($format) {
            $stmt->bind_param($format, ...$vars);            
        }
        if($stmt->execute()) {
            $ar = $stmt->affected_rows;
            $stmt->close();
            return $ar;
        }
        $stmt->close();
        return -1;
    }

?>