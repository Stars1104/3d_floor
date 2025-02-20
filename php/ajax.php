<?php
	include_once('db.php');

	$mode 	= $_POST['mode'];

	$db 	= new DBAccess();

	$uid 	= $_SESSION['User']['ID'];

	switch ($mode)
	{
		case 'create_project':
			$arr 	= $db->get_info_arr("tbl_project","title='".$_POST['title']."'");

			if(count($arr) > 0) 
			{
				echo "-1";
				return;
			}

			$sql  = "INSERT INTO tbl_project SET ";
			$sql .= " user_id='".$uid."'";
			$sql .= ", title='".$_POST['title']."'";
			$sql .= ", descr='".$_POST['descr']."'";
			$sql .= ", data='".$_POST['data']."'";

			$db->run_sql($sql);

			echo mysql_insert_id();
			break;

		case 'create_object':
			
			$data_arr 	= explode(",", $_POST['data']);
			$dir 		= $data_arr[4];
			$mtls 		= explode(";",$data_arr[7]);

			echo "<pre>";
			var_dump($_POST['data']);
			echo "</pre>"

			if(file_exists("../objs/".$dir))
			{
				echo "{mode:'error',msg:'The name is already exist, Please input another name!'}";
				return;
			}


			$dbhost = 'localhost';
			$dbname = 'floor';
			$username = 'root';
			$password = '';

			$conn = new mysqli($dbhost, $username, $password, $dbname);

			echo $data_arr[0]

			// $sql = "INSERT INTO users (user_id, thumb_img, two_obj,three_obj, three_mtl, name, size)
			// VALUES ($uid, $data_arr[0], $data_arr[1], $data_arr[2],$data_arr[3], $data_arr[4], $data_arr[5].$data_arr[6])";

			// $conn->query($sql)

			// outUV_trunk.jpg,outUV_trunk_bump.jpg,7m_WASHINGTONIA PALM_tree.obj,7m_WASHINGTONIA PALM_tree.mtl,WASHINGTONIA PALM LEAF.jpg,BNB,5,5

			// $sql  = "INSERT INTO objects SET ";
			// $sql .= " user_id='".$uid."'";
			// $sql .= ", thumb_img='".$data_arr[0]."'";
			// $sql .= ", two_obj='".$data_arr[1]."'";
			// $sql .= ", three_obj='".$data_arr[2]."'";
			// $sql .= ", three_mtl='".$data_arr[3]."'";
			// $sql .= ", name='".$data_arr[4]."'";
			// $sql .= ", size='".$data_arr[5].",".$data_arr[6]."'";



			// $db->run_sql($sql);

			// mkdir("../objs/".$dir);
			
			// rename("../tmp/".$data_arr[0],"../objs/".$dir."/".$data_arr[0]);
			// rename("../tmp/".$data_arr[1],"../objs/".$dir."/".$data_arr[1]);
			// rename("../tmp/".$data_arr[2],"../objs/".$dir."/".$data_arr[2]);
			// rename("../tmp/".$data_arr[3],"../objs/".$dir."/".$data_arr[3]);

			// for($i = 0; $i < count($mtls); $i++)		
			// {
			// 	if($mtls[$i] == "") 
			// 		continue;

			// 	rename("../tmp/".$mtls[$i],"../objs/".$dir."/".$mtls[$i]);
			// }

			// echo "{mode:'success',msg:'".mysql_insert_id()."'}";
			// break;

		case "get_projectlist":

			$html 	= "";

			$arr 	= $db->get_info_arr("tbl_project","user_id='".$uid."'");
			$proID 	= $_POST['proID'];


			for($i = 0; $i < count($arr); $i++)
			{
				if($proID && $proID == $arr[$i]['id'])
					$html .= "<option value='".$arr[$i]['id']."' descr='".$arr[$i]['descr']."' data='".$arr[$i]['data']."' selected>".$arr[$i]['title']."</option>";
				else
					$html .= "<option value='".$arr[$i]['id']."' descr='".$arr[$i]['descr']."' data='".$arr[$i]['data']."'>".$arr[$i]['title']."</option>";
			}



			echo $html;

		break;


		case "update_project":

			$sql  = "UPDATE tbl_project SET";

			$sql .= " data='".$_POST['data']."'";

			$sql .= " WHERE id=".$_POST['proID'];

			$db->run_sql($sql);

		break;

		

		default:

			# code...

			break;

	}

?>