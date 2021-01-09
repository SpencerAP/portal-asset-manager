<?php

$action = $_POST['ACTION'] ?? null;
if ($action === 'authorize') {
	authorize();
}

?>
<br>

<form method="POST">
<input name="ACTION" type="hidden" value="authorize" />
<input type="submit" value="Authorize with PhotoShelter" />
</form>
