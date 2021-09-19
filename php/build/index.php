<!--
# php+pgsql+redis project - BACKEND + FRONTEND PAGES
# @maintainer G.Gatto 2021 - www.garanet.net
# repo from https://github.com/garanet/k8s-php-pgsql-redis
# Tested on a MacOsx with Docker + Kuberneters (Docker-Desktop)
-->
<!DOCTYPE html><html lang="en">
<head>
<meta HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta HTTP-EQUIV="Expires" CONTENT="-1">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>
<body>

<?php
# Test the REDIS connection
$redis = new Redis();
$redis->connect('redis-service', 6379);
$redis->auth('redis-password');
$key = 'employees';
$source = '<h3>Data from PostgreSQL Server</h3><p>After 3 seconds the page will show the REDIS cache';
# Check if REDIS as the dump already
if (!$redis->get($key)) {
	# Redis empty, then load data from PostgreSQL (secrets are clear but could use the .env variables)
    $servername = "pgsql-service";
	$username = "testdbuser";
	$password = "testdbuserpassword";
	$dbname = "testdb";
	$port = "5432";
	# Test the PostgreSQL connection
    $pdo = new PDO('pgsql:host=' . $servername . '; dbname=' . $dbname, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	# Run the query
    $sql  = "SELECT * FROM employees";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
       $employees[] = $row;
    }
	# Filling the REDIS
    $redis->set($key, serialize($employees));
    $redis->expire($key, 10);
} else {
     $source = '<h3>Data from REDIS Server</h3>';
     $employees = unserialize($redis->get($key));
}
echo $source . '<br>';

# Show the Frontend Part
?>
 <table id="employee_grid" class="table" width="100%" cellspacing="0">
	<thead>
		<tr>
			<th>Number</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Birth Date</th>
			<th>Gender</th>
			<th>Hired on</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($employees as $key => $emp) :?>
		<tr>
			<td><?php echo $emp['emp_no'] ?></td>
			<td><?php echo $emp['first_name'] ?></td>
			<td><?php echo $emp['last_name'] ?></td>
			<td><?php echo $emp['birth_date'] ?></td>
			<td><?php echo $emp['gender'] ?></td>
			<td><?php echo $emp['hire_date'] ?></td>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>
<!-- Reload once the page got the data from PGSQL -->
<script>
function reloadIt() {
    if (window.location.href.substr(-2) !== "?r") {
        window.location = window.location.href + "?r";
    }
}
setTimeout('reloadIt()', 3000)();
</script>
</body>
</html>