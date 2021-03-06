<?php 

$callerName = $_POST['callerName'];
$contactNo = $_POST['contactNo'];
$locationofIncident = $_POST['locationofIncident'];
$typeofIncident = $_POST['typeofIncident'];
$descriptionofIncident = $_POST['descriptionofIncident'];

$cars = [];


require_once 'db.php';
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$sql = "SELECT patrolcar.patrolcar_id, patrolcar_status.patrolcar_status_desc, patrolcar_status.patrolcar_status_id FROM patrolcar JOIN patrolcar_status ON patrolcar.patrolcar_status_id=patrolcar_status.patrolcar_status_id";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
	
	$id = $row['patrolcar_id'];
	$status = $row['patrolcar_status_desc'];
	$statusId = $row['patrolcar_status_id'];
	$car = ["id" => $id, "status" => $status, "StatusId" => $statusId];


	array_push($cars, $car);
}

$btnDispatchClicked = isset($_POST["btnDispatch"]);
$btnProcessCallClicked = isset($_POST["btnProcessCall"]);

if ($btnDispatchClicked == false && $btnProcessCallClicked == false) {
	header("Location: logcall.php?message=error");
}

if ($btnDispatchClicked == true) {
	$insertIncidentSuccess = FALSE;
	$patrolcarDispatched = $_POST["cbCarSelection"];
	$numofPatrolcarDispatched = count($patrolcarDispatched);
	$incidentStatus = 0;		

	if ($numofPatrolcarDispatched > 0) {
		$incidentStatus = "2";
	} else {
		$incidentStatus = "1";
	}

	$sql = "INSERT INTO incident (caller_name, phone_number, incident_type_id, incident_location, incident_desc, incident_status_id) VALUES ('" . $callerName . "', '" . $contactNo . "', '" . $typeofIncident . "', '" . $locationofIncident . "', '" . $descriptionofIncident . "', '" . $incidentStatus. "')";

	$insertIncidentSuccess = $conn -> query($sql);

	if ($insertIncidentSuccess === FALSE) {
		echo "Error: " . $sql . "<br>" . $conn -> error;
	}

	$incidentId = mysqli_insert_id($conn);
	$updateSuccess = FALSE;
	$insertDispatchSuccess = FALSE;

	for ($i=0; $i <$numofPatrolcarDispatched; $i++) { 
		$carId = $patrolcarDispatched[$i];

		$sql = "UPDATE patrolcar SET patrolcar_status_id='1' WHERE patrolcar_id='".$carId."'";
		$updateSuccess = $conn -> query($sql);
		if ($updateSuccess === FALSE) {
			echo "Error: " . $sql . "<br>" . $conn -> error;
		}

		$sql = "INSERT INTO dispatch (incident_id, patrolcar_id, time_dispatched) VALUES ($incidentId, '". $carId ."', NOW())";
		$insertDispatchSuccess = $conn -> query($sql);
		if ($insertDispatchSuccess === FALSE) {
			echo "Error: " . $sql . "<br>" . $conn -> error;
		}
	}

	$conn -> close();

	if ($insertIncidentSuccess === true && $updateSuccess === true && $insertDispatchSuccess === true) 
	{
		echo "<script> window.location.href = 'logcall.php?message=success&carID=$carId'; alert('Patrol car $carId status has been Dispatched');</script>";
	}


}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Dispatch</title>
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
</head>
<body>

	<div class="container" style="width:80%">

		<?php require_once 'nav.php'; ?>

		<section style="margin-top: 20px">
			<form action="dispatch.php" method="post">

				
				<div class="form-group row">
					<label for="callerName" class="col-sm-4col-form-label"> Caller's Name</label>
					<div class="col-sm-8">
						<?php echo $callerName; ?>
						<input type="hidden" name="callerName" id="callerName" value="<?php echo $callerName; ?>">
					</div>
				</div>


				<div class="form-group row">
					<label for="contactNo" class="col-sm-4col-form-label">Contact Number</label>
					<div class="col-sm-8">
						<?php echo $contactNo; ?>
						<input type="hidden" name="contactNo" id="contactNo" value="<?php echo $contactNo;?>">
					</div>
				</div>


				<div class="form-group row">
					<label for="locationofIncident" class="col-sm-4col-form-label">Location of Incident</label>
					<div class="col-sm-8">
						<?php echo $locationofIncident; ?>
						<input type="hidden" name="locationofIncident" id="locationofIncident" value="<?php echo $locationofIncident;?>">
					</div>
				</div>


				<div class="form-group row">
					<label for="typeofIncident" class="col-sm-4col-form-label">Type of Incident</label>
					<div class="col-sm-8">
						<?php 
						$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
						$sql = "SELECT incident_type_desc FROM incident_type WHERE incident_id = '" . $typeofIncident . "'";
						$result = $conn -> query($sql);
						while ($row = $result -> fetch_assoc()) {
							$desc = $row['incident_type_desc'];
							echo $desc;
						}
						$conn->close();
						?>
						<input type="hidden" name="typeofIncident" id="typeofIncident" value="<?php echo $typeofIncident;?>">
					</div>
				</div>


				<div class="form-group row">
					<label for="descriptionofIncident" class="col-sm-4col-form-label">Description of Incident</label>
					<div class="col-sm-8">
						<?php echo $descriptionofIncident; ?>
						<input type="hidden" name="descriptionofIncident" id="descriptionofIncident" value="<?php echo $descriptionofIncident;?>">
					</div>
				</div>


				<div class="form-group row">
					<label for="patrolCars" class="col-sm-4col-form-label">Choose Patrol Car(s)</label>
					<div class="col-sm-8">
						<table class="table table-striped">
							<tbody>
								<tr>
									<th scope="col">Car's Number</th>
									<th scope="col">Car's Status</th>
									<th scope="col"></th>
								</tr>
								<?php
									for ($i=0;$i<count($cars);$i++) { 
										$car = $cars[$i];
										echo '<tr>';
										echo '<td>'.$car['id'].'</td>';
										echo '<td>'.$car['status'].'</td>';
										echo '<td>';
										if ($car['StatusId'] > 1) {
											echo '<input type="checkbox" name="cbCarSelection[]" value="'.$car['id'].'">';
										}
										echo '</td>';
										echo '</tr>';
									}
								?>
							</tbody>
						</table>
					</div>
				</div>


				<div class="form-group row">
					<div class="col-sm-4"></div>
					<div class="col-sm-8" style="text-align: center">
						<input type="submit" name="btnDispatch" class="btn btn-primary" value="Dispatch">
					</div>
				</div>


			</form>

		</section>


		<footer class="page-footer font-small blue pt-4 footer-copyright text-center py-3">
			&copy;2021 Copyright
			<a href="www.ite.edu.sg">ITE</a>
		</footer>
		<script type="text/javascript" src="js/jquery-3.5.0.min.js"></script>
		<script type="text/javascript" src="js/popper.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.js"></script>
	</div>
</body>
</html>