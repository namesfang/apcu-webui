<?php

if(!apcu_enabled()) {
    die("APCu is not installation.\r\n");
}

$k = '';
if(isset($_GET['keyword'])) {
	$k = trim($_GET['keyword']);
}

if(isset($_GET['action'])) {
	$act = $_GET['action'];
	$key = $_GET['key'] ?? '';
	if($act == 'delete') {
		apcu_delete($key);
		if(!apcu_fetch($key)) {
			header('Location: /'.($k?'?keyword='.$k:''));
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
	<style>
	body {
		background:#f2f2f2;
	}
	
	.el-container {
		width: 1200px;
		height: auto;
		margin: 0 auto;
	}
	
	.el-container header {
		width: 1200px;
		height: 78px;
	}
	
	.el-container header h1 {
		width: 240px;
		height: 78px;
		line-height: 78px;
		font-size: 30px;
		margin: 0;
		float: left;
	}
	
	.el-container header form {
		width: 400px;
		margin: 20px 0;
		float: left;
	}
	
	.el-container .alert {
		margin-bottom: 20px;
	}
	
	.el-container table .el-w80 {
		width: 80px;
	}
	
	.el-container table .el-w200 {
		width: 200px;
		word-break: break-all;
	}
	
	.el-container table .el-w400 {
		width: 400px;
		word-break: break-all;
	}
	
	.el-container table .el-wauto {
		word-break: break-all;
	}
	
	.el-container table td em {
		font-style: normal;
		color: red;
	}
	.el-container main {
		background: #fff;
	}
	
	.el-totop,
	.el-totop:hover,
	.el-totop:visited,
	.el-totop:active{
		width: 46px;
		height: 46px;
		line-height: 46px;
		text-align: center;
		text-decoration: none;
		font-size:20px;
		color: #fff;
		background: #0d6efd;
		border-radius: 50%;
		position: fixed;
		bottom: 10%;
		left: 50%;
		margin-left: 620px;
	}
	</style>
</head>
<body>
	<div class="el-container">
		<header>
			<h1>APCu WebUI</h1>
			<form class="input-group mb-3">
			  <input name="keyword" value="<?php echo $k; ?>" type="text" class="form-control" autocomplete="off" placeholder="关键字 支持*匹配">
			  <button class="btn btn-primary" type="submit">检索</button>
			</form>
		</header>
		<div class="alert alert-info" role="alert">total: <?php echo $info['num_entries']; ?></div>
		<main>
			<table class="table table-striped table-hover align-middle">
				<thead>
				  <tr>
					<th scope="col">#</th>
					<th scope="col">Key</th>
					<th scope="col">TTL</th>
					<th scope="col">Type</th>
					<th scope="col">Value</th>
					<th scope="col"></th>
				  </tr>
				</thead>
				<tbody>
					<?php
						$f = substr($k, -1) === '*';
						$l = substr($k, 0, 1) === '*';
						$k = trim($k, '*');
						$s = 0;
						foreach($info['cache_list'] as $i=> $t) {
							$key = $t['info'];
							if($k) {
								if($f) {
									if(0 !== strpos($t['info'], $k)) {
										continue;
									}
								} else if($l) {
									if(substr($t['info'], -strlen($k)) !== $key) {
										continue;
									}
								} else {
									if(strpos($t['info'], $k) === false) {
										continue;
									}
								}
								$key = str_replace($k, "<em>{$k}</em>", $key);
							}
							$value = apcu_fetch($t['info']);
							$detail = apcu_key_info($t['info']);
							$s++;
					?>
				  <tr>
					<td scope="row" class="align-top">
						<div class="el-w80">
							<?php
								echo $s;
							?>
						</div>
					</td>
					<td class="align-top">
						<div class="el-w200">
							<?php
								echo $key;
							?>
						</div>
					</td>
					<td class="align-top">
						<div class="el-w80">
							<?php
								echo $detail['ttl'];
							?>
						</div>
					</td>
					<td class="align-top">
						<div class="el-w80">
							<?php
								echo gettype($value);
							?>
						</div>
					</td>
					<td class="align-top">
						<div class="el-wauto">
						<?php
							if(is_scalar($value)) {
								echo is_bool($value) ? ($value?'true':'false') : $value;
							} else {
								print_r($value);
							}
						?>
						</div>
					</td>
					<td>
						<div class="el-w80">
							<a href="?action=delete&keyword=<?php echo $k;?>&key=<?php echo urlencode($t['info']);?>" class="btn btn-danger btn-sm">Delete</a>
						</div>
					</td>
				  </tr>
				  <?php } ?>
				  <?php
					if($s === 0) {
				  ?>
				  <tr>
					<td colspan=6>暂无数据</td>
				  </tr>
				  <?php } ?>
				</tbody>
			</table>
		</main>
	</div>
	<a href="#top" class="el-totop">↑</a>
</body>
</html>

