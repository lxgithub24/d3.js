<?php
	$phparray = array();
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
				$sql = "select id from province where value='$_POST[input]'";	//SQL语句
				$query = mysql_query($sql);	//执行SQL语句
				if (mysql_num_rows($query) < 1) {  
  				echo "<script>alert('无此条记录！');</script>";
				}
				else{
					$sql = "select * from province where fatherid=(select id from province where value='$_POST[input]')";	//SQL语句
					$query = mysql_query($sql);	//执行SQL语句
					$leaf_num = 1;//叶结点个数
					$phparray["name"] = $input;//为数组添加祖先节点的name属性
					$phparray["children"] = array();//为数组添加祖先节点的children属性
					$i = 0;
					$firstGeneration = array();
					while($row=mysql_fetch_row($query, MYSQL_ASSOC)){//为数组添加祖先节点的子节点
						$firstGeneration[$i]=$row["value"];
						$phparray["children"][$i++]["name"]=$row["value"];
	        }
	        for($j = 0; $j < $i ; $j++){
	        	$sql = "select * from province where fatherid=(select id from province where value='".$firstGeneration[$j]."')";	//SQL语句
						$query = mysql_query($sql);	//执行SQL语句
						$numofmysqlrows = mysql_num_rows($query);
						if($numofmysqlrows<1){
							$leaf_num++;
						}
						else{
							$leaf_num+=$numofmysqlrows;
						}
						$k=0;
						while($row=mysql_fetch_row($query, MYSQL_ASSOC)){//为数组添加祖先节点的子节点
							if($k==0){
								$phparray["children"][$j]["children"]=array();
							}
							$phparray["children"][$j]["children"][$k++]["name"]=$row["value"];
						}
	        }
	        $phparraybianliang = json_encode($phparray);
	        //echo json_encode($phparray);
	        //echo $phparraybianliang;
	      }
		}
	}
	else
	{
		echo "<script>alert('提交未成功！'); history.go(-1);</script>";
	}
?>

  <style>

  .node circle {
    cursor: pointer;
    fill: #fff;
    stroke: steelblue;
    stroke-width: 2px;
  }

  .node text {
    font-size: 12px;
  }

  .link {
    fill: none;
    stroke: #ccc;
    stroke-width: 1.5px;
  }

  </style>


<input type="text" name="userId" id="JSON" value='<?php echo $phparraybianliang; ?>' style="display:none;">

<input type="text" name="userId" id="LEAFNUM" value='<?php echo $leaf_num; ?>' style="display:none;">

<script src="d3.v3.min.js"></script>
<script>

var width = 600;

var height = 50*(document.getElementById("LEAFNUM").value);

//边界空白
var padding = {left: 80, right:50, top: 20, bottom: 20 };

var svg = d3.select("body")
           .append("svg")
	         .attr("width", width + padding.left + padding.right)
	         .attr("height", height + padding.top + padding.bottom)
           .append("g")
           .attr("transform","translate("+ padding.left + "," + padding.top + ")");

//树状图布局
var tree = d3.layout.tree()
            .size([height, width]);

//对角线生成器
var diagonal = d3.svg.diagonal()
    .projection(function(d) { return [d.y, d.x]; });

var root = JSON.parse(document.getElementById("JSON").value);
//root = JSON.parse(root);  
//alert(root);
//root = JSON.stringify(obj);
//alert(root);
//d3.json("tmp.json",function(error,root){

  //给第一个节点添加初始坐标x0和x1
  root.x0 = height / 2;
  root.y0 = 0;

  //以第一个节点为起始节点，重绘
  redraw(root);

  //重绘函数
  function redraw(source){

    /*
    （1） 计算节点和连线的位置
    */

    //应用布局，计算节点和连线
    var nodes = tree.nodes(root);
    var links = tree.links(nodes);

    //重新计算节点的y坐标
    nodes.forEach(function(d) { d.y = d.depth * 180; });

    /*
    （2） 节点的处理
    */

    //获取节点的update部分
    var nodeUpdate = svg.selectAll(".node")
                        .data(nodes, function(d){ return d.name; });

    //获取节点的enter部分
    var nodeEnter = nodeUpdate.enter();

    //获取节点的exit部分
    var nodeExit = nodeUpdate.exit();

    //1. 节点的 Enter 部分的处理办法
    var enterNodes = nodeEnter.append("g")
                    .attr("class","node")
                    .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
                    .on("click", function(d) { toggle(d); redraw(d); });

    enterNodes.append("circle")
      .attr("r", 0)
      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

    enterNodes.append("text")
        .attr("x", function(d) { return d.children || d._children ? -14 : 14; })
        .attr("dy", ".35em")
        .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
        .text(function(d) { return d.name; })
        .style("fill-opacity", 0);


    //2. 节点的 Update 部分的处理办法
    var updateNodes = nodeUpdate.transition()
                        .duration(500)
                        .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

    updateNodes.select("circle")
      .attr("r", 8)
      .style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });

    updateNodes.select("text")
      .style("fill-opacity", 1);

    //3. 节点的 Exit 部分的处理办法
    var exitNodes = nodeExit.transition()
                      .duration(500)
                      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
                      .remove();

    exitNodes.select("circle")
      .attr("r", 0);

    exitNodes.select("text")
      .style("fill-opacity", 0);

    /*
    （3） 连线的处理
    */

    //获取连线的update部分
    var linkUpdate = svg.selectAll(".link")
                        .data(links, function(d){ return d.target.name; });

    //获取连线的enter部分
    var linkEnter = linkUpdate.enter();

    //获取连线的exit部分
    var linkExit = linkUpdate.exit();

    //1. 连线的 Enter 部分的处理办法
    linkEnter.insert("path",".node")
          .attr("class", "link")
          .attr("d", function(d) {
              var o = {x: source.x0, y: source.y0};
              return diagonal({source: o, target: o});
          })
          .transition()
          .duration(500)
          .attr("d", diagonal);

    //2. 连线的 Update 部分的处理办法
    linkUpdate.transition()
        .duration(500)
        .attr("d", diagonal);

    //3. 连线的 Exit 部分的处理办法
    linkExit.transition()
          .duration(500)
          .attr("d", function(d) {
            var o = {x: source.x, y: source.y};
            return diagonal({source: o, target: o});
          })
          .remove();


    /*
    （4） 将当前的节点坐标保存在变量x0、y0里，以备更新时使用
    */
    nodes.forEach(function(d) {
      d.x0 = d.x;
      d.y0 = d.y;
    });

  }

  //切换开关，d 为被点击的节点
  function toggle(d){
    if(d.children){ //如果有子节点
      d._children = d.children; //将该子节点保存到 _children
      d.children = null;  //将子节点设置为null
    }else{  //如果没有子节点
      d.children = d._children; //从 _children 取回原来的子节点 
      d._children = null; //将 _children 设置为 null
    }
  }

//});

</script>