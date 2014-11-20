<?php
require_once 'Mobilpay/Payment/Request/Abstract.php';
require_once 'Mobilpay/Payment/Request/Sms.php';
require_once 'Mobilpay/Payment/Request/Notify.php';

$errorCode 		= 0;
$errorType		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_NONE;
$errorMessage	= '';

if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0)
{
	if(isset($_POST['env_key']) && isset($_POST['data']))
	{
		#calea catre cheia privata
		#cheia privata este generata de mobilpay, accesibil in Admin -> Conturi de comerciant -> Detalii -> Setari securitate
		$privateKeyFilePath = '<path_to_private_key>';
		try
		{
			$objPmReq = Mobilpay_Payment_Request_Abstract::factoryFromEncrypted($_POST['env_key'], $_POST['data'], $privateKeyFilePath);
	    	switch($objPmReq->objPmNotify->action)
	    	{
	        case 'confirmed':
	        	$errorMessage = $objPmReq->objPmNotify->getCrc();
	            break;
	        default:
	        	$errorType		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
	            $errorCode 		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_ACTION;
	            $errorMessage 	= 'mobilpay_refference_action paramaters is invalid';
	            break;
	    	}
		}
		catch(Exception $e)
		{
			$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_TEMPORARY;
			$errorCode		= $e->getCode();
			$errorMessage 	= $e->getMessage();
		}
	}
	else
	{
		$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
		$errorCode		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_PARAMETERS;
		$errorMessage 	= 'mobilpay.ro posted invalid parameters';
	}
}
else 
{
	$errorType 		= Mobilpay_Payment_Request_Abstract::CONFIRM_ERROR_TYPE_PERMANENT;
	$errorCode		= Mobilpay_Payment_Request_Abstract::ERROR_CONFIRM_INVALID_POST_METHOD;
	$errorMessage 	= 'invalid request metod for payment confirmation';
}

header('Content-type: application/xml');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
if($errorCode == 0)
{
	echo "<crc>{$errorMessage}</crc>";
}
else
{
	echo "<crc error_type=\"{$errorType}\" error_code=\"{$errorCode}\">{$errorMessage}</crc>";
}