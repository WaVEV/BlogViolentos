<?php
include "classWord.php";
?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
	<!--	<link rel="stylesheet" type="text/css" href="style.css"> -->
		<title>Diccionario</title>
	</head>
	<body>
		<h1>Diccionario "Blogs Violentos"</h1>
		<form method="POST" action="diccionario.php"  accept-charset="UTF-8" autocomplete="off">
		<?php
		//Conecto a base de datos    
		$link = mysqli_connect('localhost', 'alamaula', 'tontita', 'blogsviolentos');
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		
		mysqli_set_charset($link, "utf8");
		
		$query = 'SELECT * FROM words WHERE modificada="0" LIMIT 10';
		//$query = 'SELECT * FROM words LIMIT 10';
		if ($result = mysqli_query($link, $query)) {
			while ($row = mysqli_fetch_assoc($result)) {
				
				$p = new Word();
				$p->id = $row["id"];
				$p->palabra = $row["palabra"];
				$p->tipo = $row["tipo"];
				
				if ( $p->tipo == "ADJ" )
					$p->parse_sinonimos();
				else if ( $p->tipo == "COM" )
					$p->parse_definicion();
				else continue;
		?>
			<fieldset>
				<legend><strong>Palabra:</strong> <em>"<?echo $p->palabra;?>"</em> <strong>Tipo:</strong> <em>"<?echo $p->tipo?>"</em></legend>
				<p>
					Reemplazo principal<br>
					<select name="r1s">
						<?
						foreach($p->reemplazos as $e) {
						?>
						<option value="<?echo $e?>"><?echo $e?></option>
						<?
						}
						?>
					</select>
					<br>O si no... <input type="text" name="r1i" maxlength="30" size="31">
				</p>
				<p>
					Reemplazo secundario<br>
					<select name="r2">
						<?
						foreach($p->reemplazos as $e) {
						?>
						<option value="<?echo $e?>"><?echo $e?></option>
						<?
						}
						?>
					</select>
					<br>O si no... <input type="text" name="r2i" maxlength="30" size="31">
				</p>
				<p>
					Reemplazo terciario<br>
					<select name="r3">
						<?
						foreach($p->reemplazos as $e) {
						?>
						<option value="<?echo $e?>"><?echo $e?></option>
						<?
						}
						?>
					</select>
					<br>O si no... <input type="text" name="r3i" maxlength="30" size="31">
				</p>
			</fieldset>
			<br>
		<?php	
			}
			mysqli_free_result($result);
		}
		?>
			<p><input type="submit" value="Enviar"></p>
		</form>
	</body>
</html>
		
