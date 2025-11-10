<?php
require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if(!empty($_GET['ip']))
{
	if(!empty($_GET['ip']) AND (isIPv4($_GET['ip']) OR isIPv6($_GET['ip'])))
	{
		$ip = secuChars($_GET['ip']);

		$fichierCache = $_SERVER['DOCUMENT_ROOT'].'assets/cache/ip/ports-'.$ip;
		if(!file_exists($fichierCache) OR (filemtime($fichierCache) < strtotime('-20 minutes')))
		{
			$descriptor_spec = [
				0 => ['pipe', 'r'], // stdin est un pipe que le processus va lire
				1 => ['pipe', 'w'], // stdout est un pipe que le processus va écrire
				2 => ['pipe', 'w'] // stderr est un pipe que le processus va écrire
			];

			$process = proc_open('python3 ports-'.(isIPv6($ip) ? 'v6' : 'v4').'.py '.escapeshellarg($ip), $descriptor_spec, $pipes);

			if(is_resource($process))
			{
				fclose($pipes[0]);
				$output = stream_get_contents($pipes[1]);

				$stream = fopen('php://temp', 'r+');
				$content = stream_get_contents($stream);
				fclose($stream);

				fclose($pipes[1]);
				$error_output = stream_get_contents($pipes[2]);
				fclose($pipes[2]);
				$return_value = proc_close($process);

				$content = preg_match_all('/Port (?P<port>\d{2,4})\|(?P<service>[\w\s]+) is (?P<statut>closed|open|filtered: timed out)/is', $output, $m);

				if(!empty($m['service']))
				{
					foreach($m['service'] as $cle => $valeur) {
						$port		= (!empty($m['port'][$cle]) AND mb_strlen($m['port'][$cle]) > 1 AND mb_strlen($m['port'][$cle]) <= 4)	? (int) $m['port'][$cle]		: null;
						$statut		= (!empty($m['statut'][$cle]) AND in_array($m['statut'][$cle], ['closed', 'open', 'filtered'], true))	? (string) $m['statut'][$cle]	: null;
						$service	= (!empty($m['service'][$cle]) AND in_array($m['service'][$cle], ['FTP', 'SSH', 'Telnet', 'SMTP', 'DNS', 'HTTP', 'POP3', 'NETBIOS', 'IMAP', 'HTTPS', 'SMB', 'MSSQL', 'ORACLE', 'MySQL', 'Remote Desktop'], true)) ? $m['service'][$cle] : null;

						$donnees[] = [
							'port'		=> $port,
							'service'	=> $service,
							'statut'	=> $statut
						];
					}
				}

				$donnnees = json_encode($donnees);

				cache($fichierCache, $donnnees);

				echo $donnnees;
			}
		}

		else
			echo (file_exists($fichierCache) AND filesize($fichierCache) > 0) ? file_get_contents($fichierCache) : null;
	}

	else
		echo json_encode(['error' => 'L’adresse IP est incorrecte']);
}

else
	echo json_encode(['error' => 'L’adresse IP est vide']);