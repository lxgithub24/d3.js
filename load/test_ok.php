<?php
	if(isset($_POST["Submit"]) && $_POST["Submit"] == "��ѯ")
	{
		$input = $_POST["input"];
		if($input == "")
		{
			echo "<script>alert('����Ϊ�գ�'); history.go(-1);</script>";
		}
		else
		{			
				mysql_connect("localhost","root","liuxiang");	//�������ݿ�
				mysql_select_db("sikuquanshu");	//ѡ�����ݿ�
				//mysql_query("set names 'latin1'");	//�趨�ַ���
				$sql = "select fatherid from province where fatherid='$_POST[input]'";	//SQL���
				$query = mysql_query($sql);	//ִ��SQL���
				$result=array();
				while($row=mysql_fetch_row($query, MYSQL_ASSOC)){
					//echo json_encode($row);
        	array_push($result, $row);
        }
        echo $result[0];
        //echo json_encode($result);
		}
	}
	else
	{
		echo "<script>alert('�ύδ�ɹ���'); history.go(-1);</script>";
	}
?>