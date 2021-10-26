<?php

if(!apcu_enabled()) {
    die("APCu is not installation.\r\n");
}

//echo '<pre>';
//print_r(apcu_cache_info());
//die;
//die(apcu_fetch('herrywatch:backend:captcha'));

//apcu_store('herrywatch:backend:captcha','d',55);

//die((apcu_fetch('herrywatch:backend:captcha')));

if(isset($_GET['action'])) {
	$act = $_GET['action'];
	$key = $_GET['key'] ?? '';
	if($act == 'delete') {
		apcu_delete($key);
		if(!apcu_fetch($key)) {
			header('Location: /');
		}
	}
}

$info = apcu_cache_info();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>APCu WebUI</title>
    <link href="/assets/bootstrap-5.1.3-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f2f2f2;">
	<div  style="width: 80%;margin: 0 auto;">
		<h1>APCu WebUI</h1>
		<div class="alert alert-info" role="alert">total: <?php echo $info['num_entries']; ?></div>
		<table class="table table-striped table-hover align-middle" style="background:#fff">
		<thead>
		  <tr>
		    <th scope="col" style="width:80px">#</th>
		    <th scope="col" style="width:200px">键</th>
		    <th scope="col">值</th>
		    <th scope="col" style="width:80px"></th>
		  </tr>
		</thead>
		<tbody>
			<?php
				foreach($info['cache_list'] as $i=> $t) {
			?>
		  <tr>
		    <th scope="row" class="align-top" style="width:80px">
		    	<?php
		    		echo $i+1;
		    	?>
		    </th>
		    <td class="align-top" style="width:200px">
		    	<span class="span-popover" data-bs-toggle="popover" title="<?php echo $t['info']; ?>" data-bs-content="dddd">
				  	<?php
				  		echo $t['info'];
				  	?>
		    	</span>
		    </td>
		    <td class="align-top">
		    	<?php
		    		$value = apcu_fetch($t['info']);
		    		if(is_scalar($value)) {
		    			echo $value;
		    		} else {
		    			echo json_encode($value, JSON_UNESCAPED_UNICODE);
		    		}
		    	?>
		    </td>
		    <td style="width:80px">
		    	<a href="?action=delete&key=<?php echo urlencode($t['info']);?>" class="btn btn-danger btn-sm">Delete</a>
		    </td>
		  </tr>
		  <?php } ?>
		  <?php
		  	if($info['num_entries'] === 0) {
		  ?>
		  <tr>
		  	<td colspan=4>暂无数据</td>
		  </tr>
		  <?php } ?>
		</tbody>
  </div>
</table>
</body>
<script src="/assets/bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
new bootstrap.Popover(document.querySelector('.span-popover'), {
  html: true,
  delay: 1000,
  trigger: 'hover',
  container: 'body',
})
</script>
</html>

