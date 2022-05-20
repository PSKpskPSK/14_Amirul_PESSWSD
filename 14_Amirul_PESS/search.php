<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Search Patrol Car</title>
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
</head>
<body>

	<div class="container" style="width: 80%;">

		<?php require_once 'nav.php'; ?>

		<section>
			<form action="update.php" method="POST">
				<div class="form-group row">
					<label for="patrolCarId" class="col-sm-4col-form-label">
						Enter Patrol Car's Number
					</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="patrolCarId" id="patrolCarId">
					</div>
					<div class="col-sm-2">
						<input type="submit" class="btn btn-primary" name="btnSearch" value="Search">
					</div>
				</div>
			</form>
		</section>
		<footer class="page-footer font-small blue pt-4 footer-copyright text-center py-3">&copy; 2020 Copyright <a href="www.ite.edu.sg">ITE</a></footer>
	</div>
	<script type="text/javascript" src="js/jquery-3.5.0.min.js"></script>
	<script type="text/javascript" src="js/popper.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
</body>
</html>