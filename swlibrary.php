<?php
include_once('software.php');
include_once('softwarelibrary.php');

$softwareLibraryFile = 'swlibrary.txt';
$configFile = 'config.json';

if (file_exists($configFile)) {
	$json = file_get_contents($configFile);
} else die('Error reading config!');
$config = json_decode($json, true);
$softwareTypes = $config['softwareTypes'];
$softwareVisibility = $config['softwareVisibility'];
	$filepath = 'Data/';
	if (file_exists($filepath . $softwareLibraryFile)) {
		$library = unserialize(file_get_contents($filepath . $softwareLibraryFile));
		$swlibrary = $library->getAllSoftware();
	} else {
		$swlibrary = [];
	}
?>
<!DOCTYPE html>
<html>
	<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
       	<link href="../css/bootstrap.min.css" rel="stylesheet">
        <script src="../js/jquery-2.1.4.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
    	<div class="container-fluid" id="main">
    		<div class="row">
    			<div class="col-sm-offset-1 col-sm-10">
    				<div class="page-header"><h1>IT Asset List</h1></div>
    				<table class="table table-striped table-hover table-condensed">
    					<tr>
    						<th>Name</th>
    						<th>Version</th>
    						<th>Vendor</th>
    						<th>Type</th>
    						<th>Visibility</th>
    					</tr>
    					<?php 
						$keys = array_keys($swlibrary);
    					for($i=0; $i<count($keys); $i++){
    						?>
						<tr id="<?=$keys[$i];?>-row">
							<td id="<?=$keys[$i];?>-name"><?=$swlibrary[$keys[$i]]->getName();?></td>
							<td id="<?=$keys[$i];?>-version"><?=$swlibrary[$keys[$i]]->getVersion();?></td>
							<td id="<?=$keys[$i];?>-vendor"><?=$swlibrary[$keys[$i]]->getVendor();?></td>
							<?php for($j=0; $j<count($softwareTypes); $j++) { 
								if($swlibrary[$keys[$i]]->getType() == $softwareTypes[$j][0]) { ?>
									<td id="<?=$keys[$i];?>-type">
										<div class="btn-group">
											<button type="button" class="btn btn-xs btn-<?=$softwareTypes[$j][1];?>"><?=$softwareTypes[$j][0];?></button>
											<button type="button" class="btn btn-xs dropdown-toggle btn-<?=$softwareTypes[$j][1];?>" data-toggle="dropdown"><span class="caret"></span></button>
											<ul class="dropdown-menu">
												<?php 
												$options = $softwareTypes;
												for($k=0; $k<count($options); $k++) { 
													if($options[$k][0] != $options[$j][0]) {
													?>
													<li><a class="set-software" data-option="<?=$options[$k][0];?>" data-id="<?=$keys[$i];?>" data-set="type" href="#"><?=$options[$k][0];?></a></li>
												<?php }
												}?>
											</ul>
										</div>
									</td>
								<?php }
							} ?>
							<?php for($j=0; $j<count($softwareVisibility); $j++) { 
								if($swlibrary[$keys[$i]]->getVisibility() == $softwareVisibility[$j][0]) { ?>
									<td id="<?=$keys[$i];?>-visibility">
										<div class="btn-group">
											<button type="button" class="btn btn-xs btn-<?=$softwareVisibility[$j][1];?>"><?=$softwareVisibility[$j][0];?></button>
											<button type="button" class="btn btn-xs dropdown-toggle btn-<?=$softwareVisibility[$j][1];?>" data-toggle="dropdown"><span class="caret"></span></button>
											<ul class="dropdown-menu">
												<?php 
												$options = $softwareVisibility;
												for($k=0; $k<count($options); $k++) { 
													if($options[$k][0] != $options[$j][0]) {
													?>
													<li><a class="set-software" data-option="<?=$options[$k][0];?>" data-id="<?=$keys[$i];?>" data-set="visibility" href="#"><?=$options[$k][0];?></a></li>
												<?php }
												}?>
											</ul>
										</div>
									</td>
								<?php }
							} ?>
						</tr>
    					<?php } ?>
    				</table>
    			</div>
    		</div>
    	</div>
    </body>
</html>
<script type="text/javascript">
$(document).ready(function() {
	$(".set-software").on("click",function(){
		var set = $(this).attr('data-set')
		var id = $(this).attr('data-id');
		var option = $(this).attr('data-option');
		$.ajax({
			type        : 'POST',
			url         : 'updateSoftwareLibrary.php',
			data        : {set:set, id:id, option:option},
			dataType    : 'json',
			encode      : true
		})
		.success(function() {
			//get the button group data back from the post form and change the <td>to the new data
		})
		.fail(function() {alert("error")});
	});		
});
</script>