<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="author" content="NETOPIA" />
<meta http-equiv="copyright" content="(c)NETOPIA" />
<meta http-equiv="rating" content="general" />
<meta http-equiv="distribution" content="general" />
<link href="http://www.mobilpay.ro/assets/themes/public/css/screen.css" media="screen, projection" rel="stylesheet" type="text/css" />
<link href="http://www.mobilpay.ro/assets/common/img/favicon.ico" rel="shortcut icon" />
<link href="http://www.mobilpay.ro/assets/themes/public/css/print.css" media="print" rel="stylesheet" type="text/css" />
<link href="http://www.mobilpay.ro/assets/themes/public/css/mp.css" media="screen" rel="stylesheet" type="text/css" />
<link href="http://www.mobilpay.ro/assets/themes/public/css/ui.css" media="screen" rel="stylesheet" type="text/css" />
<!--[if IE]> <link href="http://www.mobilpay.ro/assets/themes/public/css/ie.css" media="screen, projection" rel="stylesheet" type="text/css" /><![endif]-->
</head>
<body >
<div class="wrapper">
	<div class="page">
		<div class="pagetop clearfix">		
			<div class="container clearfix">
					<div class="header">   
						<div class="logo">
						  <h1 class="top bottom">
							<a href="/"><span>MobilPay</span></a>
						  </h1>
						</div>
						<div class="menu">
							<a href="#" id="m-1"><span>Cum functioneaza</span></a>      
							<a href="#" id="m-2"><span>Cat costa</span></a>      
							<a href="#" id="m-3"><span>Demo</span></a>      
							<a href="#" id="m-4"><span>Inregistrare</span></a>      
							<a href="#" id="m-5"><span>FAQ</span></a>      
							<a href="#" id="m-6"><span>Presa</span></a>
							<a href="#" id="m-7"><span>Contact</span></a>
						</div>
					</div>
			
				<!-- content begin -->
				<div class="span-4">
	<img src="assets/themes/public/img/demo.png"/>
</div>
<?php
require_once 'Mobilpay/Payment/Request/Abstract.php';
require_once 'Mobilpay/Payment/Request/Sms.php';

#adresa catre care se face redirectarea la plata (test/productie)
$paymentUrl = 'http://sandboxsecure.mobilpay.ro';
//$paymentUrl = 'https://secure.mobilpay.ro';

#calea catre certificatul public 
#certificatul este generat de mobilpay, accesibil in Admin -> Conturi de comerciant -> Detalii -> Setari securitate
$x509FilePath = '<path_to_x509_certificate>';

try
{
	$objPmReqSms 				= new Mobilpay_Payment_Request_Sms();
	#merchant account signature - generated by mobilpay.ro for every merchant account
	#semnatura contului de comerciant - mergi pe www.mobilpay.ro Admin -> Conturi de comerciant -> Detalii -> Setari securitate
	$objPmReqSms->signature 	= 'XXXX-XXXX-XXXX-XXXX-XXXX';
	#service/product identificator generated by mobilpay.ro for every service/product
	#semnatura contului de comerciant - mergi pe www.mobilpay.ro Admin -> Conturi de comerciant -> Produse si servicii -> Semnul plus
	$objPmReqSms->service 		= '000000-000000-000000';
	#supply return_url and/or confirm_url only if you want to overwrite the ones configured for the service/product when it was created
	#if you don't want to supply a different return/confirm URL, just let it null
	$objPmReqSms->returnUrl 	= null; //sau $objPmReqSms->returnUrl = '<new return url>';
	$objPmReqSms->confirmUrl 	= null; //sau $objPmReqSms->confirmUrl = '<new confirm url>';
	#you should assign here the transaction ID registered by your application for this commercial operation
	#order_id should be unique for a merchant account
	srand((double) microtime() * 1000000);
	$objPmReqSms->orderId 		= md5(uniqid(rand()));
	$objPmReqSms->encrypt($x509FilePath);
}
catch(Exception $e)
{
}
?>
<div class="span-15 prepend-1">
<h3>Exemplu de implementare plata prin SMS</h3>
<?php if(!($e instanceof Exception)):?>
<p>
	<strong>Ai ales sa cumperi <i>Exemplu de implementare prin SMS</i></strong>
	<br/>
	<strong>Cost 0.05 Euro + TVA</strong>
	<br/>
	<strong>Plata va fi realizata prin portalul de plati securizat mobilPay.ro</strong>
	<br/>
	<form action="<?php echo $paymentUrl;?>" method="post" name="frmPaymentRedirect">
		<input type="hidden" name="env_key" value="<?php echo $objPmReqSms->getEnvKey();?>" />
		<input type="hidden" name="data" value="<?php echo $objPmReqSms->getEncData();?>" />
		<input type="hidden" name="cipher" value="<?php echo $objPmReqSms->getCipher();?>" /> 
		<input type="hidden" name="iv" value="<?php echo $objPmReqSms->getIv();?>" /> 
		<input type="image" src="images/12792_mobilpay-96x30.gif" />
	</form>
</p>
<?php else:?>
<p><strong><?php echo $e->getMessage();?></strong></p>
<?php endif;?>
<br/>
<br/>
</body>
</html>
