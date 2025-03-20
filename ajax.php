<?php
include_once('php/db.php');
$mode 	= $_POST['mode'];
$uid 	= $_SESSION['User']['ID'];
$db = new DBAccess();

switch ($mode) {
	case 'create_project':
		$arr 	= $db->get_info_arr("tbl_project", "title='" . $_POST['title'] . "' AND user_id='" . $uid . "'");

		if (count($arr) > 0) {
			echo "Project already exist!!!";
		} else {
			$sql = "INSERT INTO tbl_project (user_id, title, descr, data) VALUES (?, ?, ?, ?)";
			$params = [$uid, $_POST['title'], $_POST['descr'], $_POST['data']];

			$result = $db->insert($sql, $params);

			if ($result) {
				echo "Project create successfully.";
			} else {
				echo "Failed to update project.";
			}
		}
		break;

	case 'append_object':

		$id_arr 	= explode(",", $_POST['data']);

		for ($i = 0; $i < count($id_arr); $i++) {
			if ($id_arr[$i] == "") continue;

			$sql  = "INSERT INTO objects (name, user_id, two_obj, three_obj, three_mtl, thumb_img, size, descr) ";
			$sql .= "SELECT name, '" . $uid . "', two_obj, three_obj, three_mtl, thumb_img, size, descr FROM objects ";
			$sql .= "WHERE id = " . $id_arr[$i];

			$db->run_sql($sql);
		}

		break;

	case 'create_object':

		$data_arr 	= $_POST['data'];
		$dir 		= $data_arr[4];

		$dbhost = 'localhost';
		$dbname = 'floor';
		$username = 'root';
		$password = '';

		$conn = new mysqli($dbhost, $username, $password, $dbname);

		$sql  = "INSERT INTO objects SET ";
		$sql .= " user_id='" . $uid . "'";
		$sql .= ", thumb_img='" . $data_arr[0] . "'";
		$sql .= ", two_obj='" . $data_arr[1] . "'";
		$sql .= ", three_obj='" . $data_arr[2] . "'";
		$sql .= ", three_mtl='" . $data_arr[3] . "'";
		$sql .= ", name='" . $data_arr[4] . "'";
		$sql .= ", size='" . $data_arr[5] . "," . $data_arr[6] . "'";

		$conn->query($sql);

		$ret_arr['mode'] 	= 'success';
		$ret_arr['msg'] 	= `Unknown ERROR!`;

		echo json_encode($ret_arr);
		break;

	case "get_projectlist":

		$html 	= "";
		$arr 	= $db->get_info_arr("tbl_project", "user_id='" . $uid . "'");

		echo json_encode($arr);

		break;

	case "update_project":

		$Title = $_POST['title'];
		$arr 	= $db->get_info_arr("tbl_project", "title='" . $Title . "' AND user_id='" . $uid . "'");

		if (count($arr) > 0) {
			echo "Project already exist!!!";
		} else {
			$sql = "INSERT INTO tbl_project (user_id, title, descr, data) VALUES (?, ?, ?, ?)";
			$params = [$uid, $_POST['title'], $_POST['descr'], $_POST['data']];

			$result = $db->insert($sql, $params);

			if ($result) {
				echo "Project save successfully.";
			} else {
				echo "Failed to update project.";
			}
		}
		break;

	case "delete_project":

		$arr  = $db->get_info_arr("objects", "name='" . $_REQUEST['name'] . "' AND user_id=0");

		if (count($arr) > 1) {
			$sql  = "DELETE FROM objects WHERE id=" . $_REQUEST['id'];
		} else {
			$sql  = "UPDATE objects SET";
			$sql .= " user_id = 0";
			$sql .= " WHERE id=" . $_REQUEST['id'];
		}

		$db->run_sql($sql);

		echo "success";

		break;
	default:

		break;
}
