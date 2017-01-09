<?php
$userId='{"name":"shandong","children":[{"name":"jinan"},{"name":"qingdao"},{"name":"linyi"},{"name":"dezhou"},{"name":"heze","children":[{"name":"caoxian"},{"name":"dingtao"},{"name":"juye"},{"name":"mudanqu"},{"name":"shanxian"}]}]}';
//echo $userId;
//$userId='{\"name';
?>
<input type="text" name="userId" id="userId" value='<?php echo $userId; ?>' style="display:none;">
 <script>
 	//alert("a");
var userId;
userId=document.getElementById("userId").value;
alert (userId);
</script>
