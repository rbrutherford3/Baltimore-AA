<?php

require_once '../../lib/credentialsrecaptchav3.php';

class recaptcha {
	
	public static function verify($multiple_submits) {
		if ($multiple_submits) {
			$response_label='token';
		}
		else {
			$response_label='g-recaptcha-response';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			'secret' => RECAPTCHA_SECRET_KEY_V3,
			'response' => $_POST[$response_label],
			'remoteip' => $_SERVER['REMOTE_ADDR']
		]);

		$resp = json_decode(curl_exec($ch));
		curl_close($ch);
		
		if ($resp->success) {
			if ($resp->score < 0.5) {
				die('<script>alert("It seems very likely that you are NOT human, so this ride will come to an abrupt stop");</script>');
			}
		}
		else {
			die('<script>alert("Unable to determine whether or not you are human!  Existentialism in the digital age...");</script>');
		}
		
		return $resp;
	}
	
	public static function javascript() {
		$html = '
		<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>
		<script type="text/javascript" src="../../lib/recaptcha.js"></script>';
		return $html;
	}
	
	public static function tokeninput() {
		return '
				<input type="hidden" name="token" id="token" />';
	}
	
	public static function submitbutton(string $buttonname, string $buttonlabel, string $action, bool $hidden, bool $useGet) {
		$html = '
				<button
					class="g-recaptcha submitbutton" 
					type="submit" 
					name="' . $buttonname . '" 
					id="' . $buttonname . '" 
					data-sitekey="' . RECAPTCHA_SITE_KEY_V3 . '" 
					data-callback="' . ($useGet ? 'onMixedSubmit' : 'onSubmit') . '"
					data-action="' . $action . '"';
		if ($hidden) {
			$html = $html . '
					style="visibility: hidden;">';
		}
		else {
			$html = $html . '>';
		}
		$html = $html . '
					' . $buttonlabel . '
				</button>';
		return $html;
	}
}

?>