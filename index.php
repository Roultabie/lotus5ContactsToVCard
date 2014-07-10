<?php
function toVCard($file)
{
	if (file_exists($file))
	{
	    $data     = file_get_contents($file);
	    $contacts = preg_split('//', $data);
	    array_pop($contacts);
	    if (is_array($contacts)) {
	    	$vCard = '';
	    	foreach ($contacts as $key => $value) {
	    		$element = trim($value);
	    		preg_match('/^FirstName:(.*)$/m', $element, $firstName);
	    		preg_match('/^LastName:(.*)$/m', $element, $lastName);
	    		preg_match('/^JobTitle:(.*)$/m', $element, $fn);
	    		preg_match('/^MailAddress:(.*)$/m', $element, $firstEmail);
	    		preg_match('/^InternetAddress:(.*)$/m', $element, $secondEmail);
	    		preg_match('/^OfficePhoneNumber:(.*)$/m', $element, $officePhone);
	    		preg_match('/^OfficeFAXPhoneNumber:(.*)$/m', $element, $officeFax);
	    		preg_match('/^CellPhoneNumber:(.*)$/m', $element, $cellPhone);
	    		preg_match('/^PhoneNumber:(.*)$/m', $element, $homePhone);
	    		preg_match('/^HomeFAXPhoneNumber:(.*)$/m', $element, $homeFax);
	    		preg_match('/^OfficeStreetAddress: (.*)$/m', $element, $officeStreet);
	    		preg_match('/^OfficeCity:(.*)$/m', $element, $officeCity);
                preg_match('/^OfficeState:(.*)$/m', $element, $officeState);
                preg_match('/^OfficeZIP:(.*)$/m', $element, $officeZip);
                preg_match('/^OfficeCountry:(.*)$/m', $element, $officeCountry);
	    		preg_match('/^StreetAddress:(.*)$/m', $element, $homeStreetAddress);
	    		preg_match('/^City:(.*)$/m', $element, $homeCity);
                preg_match('/^State:(.*)$/m', $element, $homeState);
                preg_match('/^Zip:(.*)$/m',$element, $homeZip);
                preg_match('/^country:(.*)$/m', $element, $homeCountry);

                // Prevent city in ZIP and ZIP in ciy
                if (preg_match('/(\d+)\s*(.+)/', $officeZip[1], $officeZipTemp) || preg_match('/([.+^\d)\s*(\d+)/', $officeZip[1], $officeZipTemp)) {
                    $officeZip[1]  = $officeZipTemp[1];
                    $officeCity[1] = $officeZipTemp[2];
                }
                if (preg_match('/(\d+)\s*(.+)/', $officeCity[1], $officeCityTemp) || preg_match('/(.+^\d))\s*(\d+)/', $officeCity[1], $officeCityTemp)) {
                    $officeZip[1]  = $officeCityTemp[1];
                    $officeCity[1] = $officeCityTemp[2];
                }
                if (preg_match('/(\d+)\s*(.+)/', $homeZip[1], $homeZipTemp) || preg_match('/([.+^\d)\s*(\d+)/', $homeZip[1], $homeZipTemp)) {
                    $homeZip[1]  = $homeZipTemp[1];
                    $homeCity[1] = $homeZipTemp[2];
                }
                if (preg_match('/(\d+)\s*(.+)/', $homeCity[1], $homeCityTemp) || preg_match('/(.+^\d))\s*(\d+)/', $homeCity[1], $homeCityTemp)) {
                    $homeZip[1]  = $homeCityTemp[1];
                    $homeCity[1] = $homeCityTemp[2];
                }



	    		$vCard .= 'BEGIN:VCARD' . PHP_EOL;
	    		$vCard .= 'VERSION:3.0' . PHP_EOL;
	    		$vCard .= 'N:' . trim($lastName[1]) . ';' . trim($firstName[1]) . ';;;' . PHP_EOL;
	    		$vCard .= 'FN:' . trim($firstName[1]) . ' ' . trim($lastName[1]) . PHP_EOL; // and str_replace('FN:' . PHP_EOL, 'FN:Noreply' . PHP_EOL, $vcard)
	    		$vCard .= 'EMAIL;TYPE;INTERNET;TYPE=WORK:' . trim($firstEmail[1]) . PHP_EOL;
	    		$vCard .= 'EMAIL;TYPE;INTERNET;TYPE=HOME:' . trim($secondEmail[1]) . PHP_EOL;
	    		$vCard .= 'TEL;TYPE=WORK:' . trim($officePhone[1]) . PHP_EOL;
	    		$vCard .= 'TEL;TYPE=WORKFAX:' . trim($officeFax[1]) . PHP_EOL;
	    		$vCard .= 'TEL;TYPE=CELL:' . trim($cellPhone[1]) . PHP_EOL;
	    		$vCard .= 'TEL;TYPE=HOME:' . trim($homePhone[1]) . PHP_EOL;
	    		$vCard .= 'TEL;TYPE=HOMEFAX:' . trim($homeFax[1]) . PHP_EOL;
	    		$vCard .= 'ADR;TYPE=WORK:;;' . trim($officeStreet[1]) . ';' . trim($officeCity[1]) . ';' . trim($officeState[1]) . ';' . trim($officeZip[1]) . ';' . trim($officeContry[1]) . PHP_EOL;
	    		$vCard .= 'ADR;TYPE=HOME:;;' . trim($homeStreet[1]) . ';' . trim($homeCity[1]) . ';' . trim($homeState[1]) . ';' . trim($homeZip[1]) . ';' . trim($homeContry[1]) . PHP_EOL;
	    		$vCard .= 'END:VCARD' . PHP_EOL;
	    	}
	    	return utf8_encode(trim($vCard));
	    }
	}
}

function sendVcardByMail($mailTo, $vCard)
{
	if (!empty($mailTo)) {
            $boundary = md5(rand()); 
            $boundAtt = md5(rand());
		    $fromMail = $mailTo; //Expediteur  
		    $fromName = $GLOBALS['scriptInfo']; 
		    $subject  = 'Votre carnet d\'adresses';      
		    //Génération du séparateur   
		    $header[] = 'From: ' . $fromMail;  
		    $header[] = 'Reply-to: ' . $fromMail;  
		    $header[] = 'X-Priority: 1';  
		    $header[] = 'MIME-Version: 1.0';  
		    $header[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';  
		    //$header[] = ' \n';  
		    $content[] = '--=' . $boundary;  
		    $content[] = 'Content-Type: text/plain; charset="utf-8"';  
		    $content[] = 'Content-Transfer-Encoding:8bit';  
		    //$content[] = '\n';  
		    $content[] = 'Voici votre carnet d\'adresses disponible en pièces jointe';  
		    //$content[] = '\n';  
            $content[] = '--' . $boundary;
            //$content[] = '--' . $boundary;
		    $content[] = 'Content-Transfer-Encoding: base64';  
            $content[] = 'Content-Type: text/vcard; name=vcard.vcf';
            $content[] = 'Content-Disposition: attachment; filename=vcard.vcf;';
		    //$content[] = '\n';  
		    $content[] = chunk_split(base64_encode($vCard)); 
		    $content[] = '--' . $boundary . '--'; 
		    $content[] = '--' . $boundary . '--'; 
			return mail($mailTo, $subject, implode("\r\n", $content), implode("\r\n", $header));
	}
}

if (!empty($_POST) && is_array($_FILES)) {
	if (is_uploaded_file($_FILES['file']['tmp_name'])) {
		$vCard = toVCard($_FILES['file']['tmp_name']);
		//header('Content-Type: text/x-vcard; charset=utf-8');
		//echo $vCard;
		//$temp = tmpfile();
		//$attachedFile = file_put_contents($temp, $vCard);
		$send = sendVcardByMail($_POST['email'], $vCard);
		print_r($send);

	}
	exit();
}
?>
<html>
<meta charset="utf-8">
<head>
	<title>Lotus contacts to vcard</title>
	<style type="text/css">
	body {
		font-size: 1em;
		margin: 30px 0 0 0;
	}
	h1 {
		font-size: 1.5em;
		text-align: center;
	}
	form {
		display: block;
		width: 400px;
		margin: 30px auto 100px auto;
	}
	input {
		width: 100%;
	}
	input[type=submit] {
		width: 100px;
		margin-left: 150px;
	}
	</style>
</head>
<body>
	<p>
		<h1>Lotus contacts to vcard</h1>
	</p>
	<form enctype="multipart/form-data" name="upload contacts" method="post">
		<input type="file" name="file">
		<input type="email" name="email" value="" placeholder="Indiquez votre adresse email">
		<input type="submit" name="submit" value="Générer">
	</form>
</body>
</html>
