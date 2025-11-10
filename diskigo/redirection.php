<?php
require_once '../config/diskigo_config.php';

if(!empty($_GET['redirect']))
{
	switch ($_GET['redirect'])
	{
		case 'acemagician': // https://www.diskigo.com/amazon-acemagician
			header('Location: https://www.amazon.fr/dp/B0C6Q1B4RN?=&linkCode=ll1&tag=diskigo-21&linkId=f7ef82a20c5028aebae9767b392f136a&language=fr_FR&ref_=as_li_ss_tl');
			break;
		case 'lenovo-legion-pro-5-16irx8': // https://www.diskigo.com/amazon-lenovo-legion-pro-5-16irx8
			header('Location: https://www.amazon.fr/dp/B0C43VDJ46?=&linkCode=ll1&tag=diskigo-21&linkId=6bf0a2d64baf6df23766b6170945c618&language=fr_FR&ref_=as_li_ss_tl');
			break;
		case 'wd-red-pro-24to': // https://www.diskigo.com/amazon-wd-red-pro-24to
			header('Location: https://www.amazon.fr/dp/B0D24TQK3Q?=&linkCode=ll1&tag=diskigo-21&linkId=890088aae90e9b9515ede546620baa14&language=fr_FR&ref_=as_li_ss_tl');
			break;
		case 'arctic-m2-pro': // https://www.diskigo.com/amazon-arctic-m2-pro
			header('Location: https://www.amazon.fr/dp/B0CYSNSSF8?=&linkCode=ll1&tag=diskigo-21&linkId=aad5c5bcaaa30e1f0143042dc125894d&language=fr_FR&ref_=as_li_ss_tl');
			break;
		case 'ssd-crucial-p3-plus-2to': // https://www.diskigo.com/amazon-arctic-m2-pro
			header('Location: https://www.amazon.fr/dp/B0BYW8FLKN?=&linkCode=ll1&tag=diskigo-21&linkId=e464e05149241f4927b34df2cbe1c449&language=fr_FR&ref_=as_li_ss_tl');
			break;
		case 'steelseries-apex-pro-gen-3': // https://www.diskigo.com/amazon-steelseries-apex-pro-gen-3
			header('Location: https://www.amazon.fr/dp/B0DFH9K6G3?th=1&linkCode=ll1&tag=diskigo-21&linkId=43fb9cc4c4e91a21e8d9113ad05f7463&language=fr_FR&ref_=as_li_ss_tl');
			break;
		default:
			header('Location: https://www.diskigo.com/');
			break;
	}
}
else
	header('Location: https://www.diskigo.com/');