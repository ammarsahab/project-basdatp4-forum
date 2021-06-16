<?php
if(isset($_POST['Ubah'])){
    echo "Form diterima";
}
?>
<!DOCTYPE HTML>
<head>
	<title>Ubah Status User</title>
<head>
<body>
<header>
	<h3>Ubah Status User <?php echo $user["username"]?></h3>
</header>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
	<fieldset>
		<p>
			<label for="status">User ini adalah: </label>
            <br>
			<input type="checkbox" name="status" value="Facilitator" <?php
            if($isfacil==1){
                echo "checked";
            }
            ?>>
            <label for="facilitator">Facilitator</label>
            <br>
            <input type="checkbox" name="status" value="ASN" <?php
            if($isasn==1){
                echo "checked";
            } 
            ?>>
            <label for="asn">ASN</label>
         </p>
         <p>
            <?php
            if($isasn==1){
                echo "<label for='workplace'>Workplace </label>";
                echo "<select name='workplace' id='workplace'>";
                echo "<option disabled selected>Pilih</option>";
                while($agency=pg_fetch_array($agencies)){
                    echo '<option value="'.$agency['govtagency_id'].'">'.$agency['govtagency_name'].'</option>';
                }
            }
            ?>
         </p>
         <p>
			<input type="submit" value="Ubah" name="Ubah"/>
		</p>
	</fieldset>
</form>
</body>
</html>