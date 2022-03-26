<?php
$starttime = microtime(true);
include("config.php");
if (!isset($_POST['submit'])) {
	$result = $mysqli->query('select word from entries ORDER BY RAND() limit 1');
	if ($result->num_rows >= 1) {
		$sugest = $result->fetch_row()[0];
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Wordle Solver</title>
	<meta charset="utf-8">
	<meta name="description" content="Wordle Solver by UserGhost411">
	<meta name="keywords" content="Wordle Solver, Worl Scramble, Word List">
	<meta name="author" content="UserGhost411">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
</head>

<body>

	<div class="container" style="margin-top:40px;">
		<h2>Wordle Solver</h2>
		<form action="" method="POST" style='margin-bottom:20px'>
			<div class="form-group">
				<label for="email">correct char (use <b>!{char}</b> when char on incorrect order,example: <b>!a</b>):</label>
				<div class="row">
					<div class="col-md-1 col-2 offset-1 offset-md-0">
						<input type="text" class="form-control" maxlength="2" id="cor" placeholder="<?= (isset($sugest[0]) ? $sugest[0] : '') ?>" name="cor[]" value="<?= (isset($_POST['cor'][0]) ? $_POST['cor'][0] : '') ?>">
					</div>
					<div class="col-md-1 col-2">
						<input type="text" class="form-control" maxlength="2" id="cor" placeholder="<?= (isset($sugest[1]) ? $sugest[1] : '') ?>" name="cor[]" value="<?= (isset($_POST['cor'][1]) ? $_POST['cor'][1] : '') ?>">
					</div>
					<div class="col-md-1 col-2">
						<input type="text" class="form-control" maxlength="2" id="cor" placeholder="<?= (isset($sugest[2]) ? $sugest[2] : '') ?>" name="cor[]" value="<?= (isset($_POST['cor'][2]) ? $_POST['cor'][2] : '') ?>">
					</div>
					<div class="col-md-1 col-2">
						<input type="text" class="form-control" maxlength="2" id="cor" placeholder="<?= (isset($sugest[3]) ? $sugest[3] : '') ?>" name="cor[]" value="<?= (isset($_POST['cor'][3]) ? $_POST['cor'][3] : '') ?>">
					</div>
					<div class="col-md-1 col-2">
						<input type="text" class="form-control" maxlength="2" id="cor" placeholder="<?= (isset($sugest[4]) ? $sugest[4] : '') ?>" name="cor[]" value="<?= (isset($_POST['cor'][4]) ? $_POST['cor'][4] : '') ?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="pwd">incorrent char:</label>
				<input type="text" class="form-control" id="incor" placeholder="Enter incorect char" name="incor" value="<?= (isset($_POST['incor']) ? $_POST['incor'] : '') ?>">
			</div>

			<button type="submit" name="submit" value="<?= md5(date("d/mY H")) ?>" class="btn btn-sm btn-primary">Search</button>
			<button type="button" name="reset" class="btn btn-sm btn-warning" onclick="$(':input[type=text]').val('');">Reset</button>
			<a href="./dataset" class="btn btn-sm btn-outline-danger float-right">Add Wordlist</a>
		</form>

		<?php
		if (isset($_POST['submit']) && $_POST['submit'] == md5(date("d/mY H"))) {	
			$incorraw = str_split($_POST['incor']);
			$corraw = ($_POST['cor']);
			if((preg_match("/^[a-zA-Z]+$/", join("",$incorraw)) == 1 || $_POST['incor']=="") && (preg_match("/^[a-zA-Z\!]+$/", join("",$corraw)) == 1)){
				$incor = [];
				$cor = [];
				$order = [];
				foreach ($incorraw as $val) {
					if (preg_match('/[a-z]/', $val,$val)==0) continue;
					$incor[] = "and not INSTR(word,'".$val[0]."')";
				}
				foreach ($corraw as $val) {
					if (preg_match('/[a-z]/', $val,$val)==0) continue;
					$cor[] = "and INSTR(word,'".$val[0]."')";
				}
				foreach ($corraw as $key => $val) {
					if (strpos($val, "!") === false) {
						if (preg_match('/[a-z]/', $val,$val)==0) continue;
						$order[] = "and SUBSTR(word, ".($key + 1).", 1)='".$val[0]."'";
					} else {
						if (preg_match('/[a-z]/', $val,$val)==0) continue;
						$order[] = "and not SUBSTR(word, ".($key + 1).", 1)='".$val[0]."'";
					}
				}
				$ret = [];
				$sql = "SELECT id,word FROM entries WHERE LENGTH(word)=5<br><br>" . join(" ", $cor) . "<br><br>" . join(" ", $order) . "<br><br>" . join(" ", $incor) . "<br><br>and word REGEXP  '^[A-z]+$' limit 40";
				//echo "<hr>$sql<hr>";
				if ($result = $mysqli->query(str_replace("<br>", " ", $sql))) {
					foreach ($result->fetch_all() as $val) {
						$ret[] = $val[1];
					}
					$result->free_result();
					$mysqli->close();
					echo "<table class='table table-hover table-sm table-striped'>";
					for ($x = 0; $x <= 9; $x++) {
						$a = 10;
						$b = 20;
						$c = 30;
						echo "<tr>
				  <td><small>" . ($x + 1) . "</small>. " . (isset($ret[$x]) ? $ret[$x] : '') . "</td>
				  <td><small>" . ($x + 1 + $a) . "</small>. " . (isset($ret[$x + $a]) ? $ret[$x + $a] : '') . "</td>
				  <td><small>" . ($x + 1 + $b) . "</small>. " . (isset($ret[$x + $b]) ? $ret[$x + $b] : '') . "</td>
				  <td><small>" . ($x + 1 + $c) . "</small>. " . (isset($ret[$x + $c]) ? $ret[$x + $c] : '') . "</td>
				  </tr>";
					}
					echo "</table>";
				}
			}else{
				if(join("",$corraw)==""){
					echo '<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>No Input Found!</div>';
				}else{
					echo '<div class="alert alert-danger alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>Invalid Character Found!</div>';
				}
				
			}
		
		}
		$endtime = microtime(true);
		?>
		<hr>
		<div class='row'>
			<div class='col-6'><small><span class="d-none d-sm-inline" >Page </span>Generated in <?= round(($endtime - $starttime) * 1000, 3) ?> ms</small></div>
			<div class='col-6'><small class='float-right'><span class="d-none d-sm-inline">Wordle Solver copyright </span>&copy; <a target='_blank' href='https://github.com/UserGhost411/Wordle-Solver'>UserGhost411</a></small></div>
		</div><br><br>
	</div>
</body>

</html>