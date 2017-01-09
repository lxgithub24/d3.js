<?php
	if(isset($_POST["Submit"]) && $_POST["Submit"] == "查询")
	{
		$input = $_POST["input"];
		if($input == "")
		{
			echo "<script>alert('输入为空！'); history.go(-1);</script>";
		}
		else
		{			
				mysql_connect("localhost","root","liuxiang");	//连接数据库
				mysql_select_db("sikuquanshu");	//选择数据库
				//mysql_query("set names 'latin1'");	//设定字符集
				$sql = "select fatherid from province where fatherid='$_POST[input]'";	//SQL语句
				$query = mysql_query($sql);	//执行SQL语句
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
		echo "<script>alert('提交未成功！'); history.go(-1);</script>";
	}
?>