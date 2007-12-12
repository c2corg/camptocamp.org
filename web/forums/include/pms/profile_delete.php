<?php

	// Delete users private messages
	$db->query('DELETE FROM '.$db->prefix.'messages WHERE owner='.$id) or error('Unable to delete users messages', __FILE__, __LINE__, $db->error());

?>