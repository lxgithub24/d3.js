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
				$phparray = array();
				$phparray["name"] = $input;//为数组添加祖先节点的name属性
				$phparray["children"] = array();//为数组添加祖先节点的children属性
				$sql = "select * from province where fatherid=(select id from province where value='$_POST[input]')";	//SQL语句
				$query = mysql_query($sql);	//执行SQL语句
				$i = 0;
				$firstGeneration = array();
				while($row=mysql_fetch_row($query, MYSQL_ASSOC)){//为数组添加祖先节点的子节点
					$firstGeneration[$i]=$row["value"];
					$phparray["children"][$i++]["name"]=$row["value"];
        }
        for($j = 0; $j < $i ; $j++){
        	$sql = "select * from province where fatherid=(select id from province where value='".$firstGeneration[$j]."')";	//SQL语句
					$query = mysql_query($sql);	//执行SQL语句
					$k=0;
					while($row=mysql_fetch_row($query, MYSQL_ASSOC)){//为数组添加祖先节点的子节点
						if($k==0){
							$phparray["children"][$j]["children"]=array();
						}
						$phparray["children"][$j]["children"][$k++]["name"]=$row["value"];
					}
        }
        echo json_encode($phparray);
		}
	}
	else
	{
		echo "<script>alert('提交未成功！'); history.go(-1);</script>";
	}
?>