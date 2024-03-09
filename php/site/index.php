<?php
	// phpinfo();
	require_once 'inner_connect.php';
	if($db)
		require_once 'inner_index.php';
	else
		require_once 'err404.html';
