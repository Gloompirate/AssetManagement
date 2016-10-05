<?php 
	$configFile = 'config.json';
	if (file_exists($configFile)) {
		$json = file_get_contents($configFile);
	} else die('Error reading config!');
	$config = json_decode($json, true);
	$assetsList = $config['computers'];
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
    				<!-- Nav tabs -->
    				<ul class="nav nav-tabs" role="tablist">
    					<?php for($i = 0; $i<count($assetsList); $i++) {?>
    					<li><a href="#<?=$assetsList[$i][0];?>" role="tab" data-toggle="tab" id="<?=$assetsList[$i][0];?>Tab"><?=$assetsList[$i][0];?></a></li>
						<?php } ?>
    				</ul>
    				<!-- Tab panes -->
    				<div class="tab-content">
    					<?php for($i=0; $i<count($assetsList); $i++) { ?>
    					<div role="tabpanel" class="tab-pane fade in" id="<?=$assetsList[$i][0];?>">
    						<table class="table table-striped table-hover table-condensed" id="<?=$assetsList[$i][0];?>Table">
    							<tr>
    								<th>Name</th>
    								<th>Status</th>
    								<th>IP Address</th>
    								<th>User</th>
    								<th>Location</th>
    								<th>Hardware Details</th>
    								<th>Installed Software</th>
    								<th>Update</th>
    							</tr>
    							<?php for ($j=0; $j<count($assetsList[$i][1]); $j++) { ?>
    								<tr id="<?=$assetsList[$i][1][$j];?>">
    									<td id="<?=$assetsList[$i][1][$j];?>name"><?=$assetsList[$i][1][$j];?></td>
    									<td id="<?=$assetsList[$i][1][$j];?>status">Unknown</td>
    									<td id="<?=$assetsList[$i][1][$j];?>ip">Unknown</td>
    									<td id="<?=$assetsList[$i][1][$j];?>user">Unknown</td>
    									<td id="<?=$assetsList[$i][1][$j];?>location">Unknown</td>
    									<td id="<?=$assetsList[$i][1][$j];?>hardware" >Unknown</td>
    									<td id="<?=$assetsList[$i][1][$j];?>software" >Unknown</td>
    									<td id="<?=$assetsList[$i][1][$j];?>update"><button type="button" class="btn btn-danger software-update-btn" id="<?=$assetsList[$i][1][$j];?>updateButton">Update Software</button></td>
    								</tr>
    							<?php } ?>
    						</table>
    					</div>
    					<?php } ?>
    				</div>
				</div>
			</div>
		</div>
	</body>
</html>

<script type="text/javascript">
$(document).ready(function() {
	var config = <?=$json;?>;
	var assetList = config['computers'];
	var scanned=[];
	for(var i=0; i<assetList.length; i++) {
		scanned.push(0);
	}
	//[index][0] = list [index][1] = assets
	$(".software-update-btn").on("click",function() {
		ajaxGetInstalledSoftware(this.id);
	});
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		for(var i=0; i<assetList.length; i++) {
			if($(e.target).attr('id') == assetList[i][0]+'Tab') {
				if(!scanned[i]){
					scanLocation(assetList[i][1]);
					scanned[i] = 1;
				}
			}
		}
	})
});

function scanLocation(assets) {
	for (i=0; i<assets.length; i++) {
		ajaxGetComputerDetails(assets[i]);
	}
}


function ajaxGetComputerDetails(name) {
	var url = 'assetRequest.php?name='+name;
	$.getJSON(url, function(computer) {
		if(computer.status) {
			$('#'+ computer.name).addClass("success");
			$('#'+ computer.name + 'status').text("Online");
			$('#'+ computer.name + 'user').text(computer.lastUser);
		} else {
			$('#'+ computer.name).addClass("danger");
			$('#'+ computer.name + 'status').text("Offline");
		}	
		$('#'+ computer.name + 'ip').text(computer.ipAddress);
		if (computer.totalMemory) {
		//html required for the hardware collapse list
		var hardwarePanel = '<div class="panel-group" role="tablist" style="margin-bottom:0px;"> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="'+computer.name+'hardwarecollapseHeading"> <h4 class="panel-title"> <a href="#'+computer.name+'hardwarecollapseGroup" class="" role="button" data-toggle="collapse"> System Details </a> </h4> </div><div class="panel-collapse collapse" role="tabpanel" id="'+computer.name+'hardwarecollapseGroup" style="height: 0px;"><ul class="list-group"> ';
		//add each item returned in the JSON to the list
		hardwarePanel += '<li class="list-group-item">OS Name:'+computer.osName+'</li>';
		hardwarePanel += '<li class="list-group-item">OS Version:'+computer.osVersion+'</li>';
		hardwarePanel += '<li class="list-group-item">OS Country:'+computer.osCountryCode+'</li>';
		hardwarePanel += '<li class="list-group-item">Serial Number:'+computer.serialNumber+'</li>';
		hardwarePanel += '<li class="list-group-item">Make:'+computer.manufacturer+'</li>';
		hardwarePanel += '<li class="list-group-item">Model:'+computer.model+'</li>';
		hardwarePanel += '<li class="list-group-item">BIOS Version:'+computer.biosVersion+'</li>';
		hardwarePanel += '<li class="list-group-item">Processors:'+computer.numberProcessors+'</li>';
		hardwarePanel += '<li class="list-group-item">RAM:'+computer.totalMemory.charAt(0)+' GB</li>';
		//finish the html for the list
		hardwarePanel += '</ul></div> </div> </div>';
		//get rid of the placeholder text and show the list
		$('#'+computer.name+'hardware').text("");
		$('#'+computer.name+'hardware').append(hardwarePanel);
		}
		if (Object.keys(computer.installedSoftware).length > 0) {
			//html required for the software collapse list
			var softwarePanel = '<div class="panel-group" role="tablist" style="margin-bottom:0px;"> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="'+computer.name+'softwarecollapseHeading"> <h4 class="panel-title"> <a href="#'+computer.name+'softwarecollapseGroup" class="" role="button" data-toggle="collapse"> Software List </a> </h4> </div><div class="panel-collapse collapse" role="tabpanel" id="'+computer.name+'softwarecollapseGroup" style="height: 0px;"><ul class="list-group"> ';
			//add each item returned in the JSON to the list
			var keys = Object.keys(computer.installedSoftware);
			for(i=0; i<keys.length; i++) {
				console.log(keys[i]);
				softwarePanel += '<li class="list-group-item">'+computer.installedSoftware[keys[i]].name+'</li>';
			}
			//finish the html for the list
			softwarePanel += '</ul></div> </div> </div>';
			//get rid of the placeholder text and show the list
			$('#'+computer.name+'software').text("");
			$('#'+computer.name+'software').append(softwarePanel);
		}
	});
}
function ajaxGetInstalledSoftware(name) {
	var hostname = name.substr(0, name.length -12);
	var url = 'assetRequest.php?software=y&name='+hostname;
	$('#'+hostname+'updateButton').addClass("disabled");
	$('#'+hostname+'updateButton').text("Updating");
	$('#'+hostname+'updateButton').removeClass("btn-danger");
	$('#'+hostname+'updateButton').addClass("btn-warning");
	$.getJSON(url, function(computer) {
		if(computer) {
			$('#'+computer.name+'updateButton').addClass("disabled");
			$('#'+computer.name+'updateButton').removeClass("btn-warning");
			$('#'+computer.name+'updateButton').addClass("btn-success");
			$('#'+computer.name+'updateButton').text("Updated");
			//html required for the collapse list
			var softwarePanel = '<div class="panel-group" role="tablist"> <div class="panel panel-default"> <div class="panel-heading" role="tab" id="'+computer.name+'softwarecollapseHeading"> <h4 class="panel-title"> <a href="#'+computer.name+'softwarecollapseGroup" class="" role="button" data-toggle="collapse"> Software List </a> </h4> </div><div class="panel-collapse collapse" role="tabpanel" id="'+computer.name+'softwarecollapseGroup" style="height: 0px;"><ul class="list-group"> ';
			//add each item returned in the JSON to the list
			var keys = Object.keys(computer.installedSoftware);
			for(i=0; i<keys.length; i++) {
				softwarePanel += '<li class="list-group-item">'+computer.installedSoftware[keys[i]].name+'</li>';
			}
			//finish the html for the list
			softwarePanel += '</ul></div> </div> </div>';
			//get rid of the placeholder text and show the list
			$('#'+computer.name+'software').text("");
			$('#'+computer.name+'software').append(softwarePanel);
		}
		
	});
}
</script>

