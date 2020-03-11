<?php

if(!class_exists('WishListUtils')) {
	class WishListUtils {
		/**
		 * Reads the content of a URL using Wordpress WP_Http class if possible
		 * @param string|array $url The URL to read. If array, then each entry is checked if the previous entry fails
		 * @param int $timeout (optional) Optional timeout. defaults to 5
		 * @param bool $file_get_contents_fallback (optional) true to fallback to using file_get_contents if WP_Http fails. defaults to false
		 * @return mixed FALSE on Error or the Content of the URL that was read
		 */
		public static function read_url($url, $timeout=null, $file_get_contents_fallback=null, $wget_fallback=null) {
			$urls = (array) $url;
			if (is_null($timeout))
				$timeout = 30;
			if (is_null($file_get_contents_fallback))
				$file_get_contents_fallback = false;
			if (is_null($wget_fallback))
				$wget_fallback = false;

			$x = false;
			foreach ($urls AS $url) {
				if (class_exists('WP_Http')) {
					$http = new WP_Http;
					$req = $http->request($url, array('timeout' => $timeout));
					$x = (is_wp_error($req) OR is_null($req) OR $req === false) ? false : ($req['response']['code'] == '200' ? $req['body'] . '' : false);
				} else {
					$file_get_contents_fallback = true;
				}

				if ($x === false && ini_get('allow_url_fopen') && $file_get_contents_fallback) {
					$x = file_get_contents($url);
				}

				if ($x === false && $wget_fallback) {
					exec('wget -T ' . $timeout . ' -q -O - "' . $url . '"', $output, $error);
					if ($error) {
						$x = false;
					} else {
						$x = trim(implode("\n", $output));
					}
				}

				if ($x !== false) {
					return $x;
				}
			}
			return $x;
		}
		public static function is_url_local($url) {
			$exceptions = array(
				'home.com',
				'localhost.com',
				'work.com'
			);

			$excludeable_domain = array(
				'home',
				'localhost',
				'work'
			);

			$excludeable_tld = array(
				'loc',
				'dev',
				'local',
			);

			$res = parse_url($url);

			// not excludeable
			if($res === false) {
				return false;
			}


			$host = $res['host'];
			if(stripos($host, '.')) {

				$parts = explode('.', $host);
				$tld = $parts[count($parts) - 1];
				$domain = $parts[count($parts) - 2];

				//exception to our rules?
				if(in_array($domain.".".$tld, $exceptions)) {
					return false;
				}

				if(in_array($domain, $excludeable_domain)) {
					return true;
				}

				if(in_array($tld, $excludeable_tld)) {
					return true;
				}
			} else {
				//empty tld
				return true;
			}
			return false;
		}
		/**
		 * Simple obfuscation to garble some text
		 * @param string $string String to obfuscate
		 * @return string Obfucated string
		 */
		public function encrypt($string) {
			$string = serialize($string);
			$hash = md5($string);
			$string = base64_encode($string);
			for ($i = 0; $i < strlen($string); $i++) {
				$c = $string[$i];
				$o = ord($c);
				$o = $o << 1;
				$string[$i] = chr($o);
			}
			return str_rot13(base64_encode($string)) . $hash;
		}
		/**
		 * Simple un-obfuscation to restore garbled text
		 * @param string $string String to un-obfuscate
		 * @return string Un-obfucated string
		 */
		function decrypt($string) {
			/* if $string is not a string then return $string, get it? */
			if (!is_string($string))
				return $string;

			$orig = $string;
			$hash = substr($string, -32);
			$string = base64_decode(str_rot13(substr($string, 0, -32)));
			for ($i = 0; $i < strlen($string); $i++) {
				$c = $string[$i];
				$o = ord($c);
				$o = $o >> 1;
				$string[$i] = chr($o);
			}
			$string = base64_decode($string);

			if (md5($string) == $hash) {
				// call Decrypt again until it can no longer be decrypted
				return $this->decrypt(unserialize($string));
			} else {
				return $orig;
			}
		}
	}
}
