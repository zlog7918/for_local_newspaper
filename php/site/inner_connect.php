<?php
	try {
		$db=new PDO(
			"pgsql:host=postgres;port={$_ENV['POSTGRES_PORT']};dbname={$_ENV['POSTGRES_DB']}",
			$_ENV['POSTGRES_USER'],
			$_ENV['POSTGRES_PASSWORD'],
			[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
		);
	} catch (PDOException $e) {
		unset($e);
		$db=false;
	}
	