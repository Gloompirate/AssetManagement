<?php 
	require_once('softwareLibrary.php');
	require_once('software.php');

	//set,id,option
	if(!isset($_POST['set']) ||  !isset($_POST['id']) || !isset($_POST['option'])) {
		//error handling here
		echo 'Error: something was not supplied';
		die;
	}

	$filepath = 'Data/';
	$softwareLibraryFile = 'swlibrary.txt';
	if (file_exists($filepath . $softwareLibraryFile)) {
		$library = unserialize(file_get_contents($filepath . $softwareLibraryFile));
	}

	function updateSoftware($id, $set, $option) {
		$software = $library->getSoftware($id);
		switch($option) {
			case 'visibility':
				$software->setVisibility($option);
				//need to setup the return stuff
				break;
			case 'type':
				$software->setType($option);
				//need to setup the return stuff
				break;
		}

	}

	<td id="$id-$option">
		<div class="btn-group">
			<button type="button" class="btn btn-xs btn-<?=$softwareVisibility[$j][1];?>"><?=$softwareVisibility[$j][0];?></button>
			<button type="button" class="btn btn-xs dropdown-toggle btn-<?=$softwareVisibility[$j][1];?>" data-toggle="dropdown"><span class="caret"></span></button>
			<ul class="dropdown-menu">
				<?php 
				$options = $softwareVisibility;
				for($k=0; $k<count($options); $k++) { 
					if($options[$k][0] != $options[$j][0]) {
					?>
					<li><a class="set-software" data-option="<?=$options[$k][0];?>" data-id="$id" data-set="$option" href="#"><?=$options[$k][0];?></a></li>
				<?php }
				}?>
			</ul>
		</div>
	</td>





?>