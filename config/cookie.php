<?php
if(preg_match('/projets/', $_SERVER['SCRIPT_FILENAME']))
{
	if(isset($_GET['zoopla']))
	{
		setcookie(
			'zoopla',
			password_hash('M0T_DE_P4SSE', PASSWORD_ARGON2I),
			time() + (60 * 60 * 24 * 365),
			'/projets/',
			'thisip.pw',
			true, // Envoyé uniquement via HTTPS
			true, // Non accessible via JavaScript
		);

		header('Location: https://thisip.pw/projets/');
		exit();
	}

	else
	{
		if(isset($_COOKIE['zoopla']))
		{
			if(!password_verify('M0T_DE_P4SSE', $_COOKIE['zoopla']))
			{
				header('Location: https://thisip.pw/');
				exit();
			}
		}

		else
		{
			header('Location: https://thisip.pw/');
			exit();
		}
	}
}