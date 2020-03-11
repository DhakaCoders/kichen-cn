<?php // integration handler

if ( ! class_exists( 'WLM_AUTORESPONDER_SENDFOX' ) ) {
	class WLM_AUTORESPONDER_SENDFOX {
		private $wlm;
		private $ar;
		function SendFox( $that, $ar, $level_id, $email, $unsub = false ) {
			$this->wlm = $that;
			$this->ar  = $ar;

			list( $fname, $lname ) = explode( ' ', $that->ARSender['name'], 2 );

			if ( $unsub ) {
				$this->unsubscribe( $level_id, $email, trim( $fname ), trim( $lname ) );
			} else {
				$this->subscribe( $level_id, $email, trim( $fname ), trim( $lname ) );
			}
		}

		private function subscribe( $level_id, $email, $fname, $lname ) {
			if ( empty( $this->ar['lists'] ) || empty( $this->ar['lists'][ $level_id ] ) ) {
				return;
			}
			$this->api_request(
				'contacts',
				array(
					'first_name' => $fname,
					'last_name'  => $lname,
					'email'      => $email,
					'lists'      => array(
						$this->ar['lists'][ $level_id ],
					),
				)
			);
		}

		private function unsubscribe( $level_id, $email ) {
			// no endpoint for unsubscribe
		}

		private function api_request( $endpoint, $data ) {
			wp_remote_post(
				'https://api.sendfox.com/' . $endpoint,
				array(
					'body'       => $data,
					'headers'    => array(
						'Authorization' => 'Bearer ' . $this->ar['personal_access_token'],
					),
					'user-agent' => 'WishList Member/' . $this->wlm->Version,
					'timeout'    => 1,
					'blocking'   => false,
				)
			);
		}
	}
}
