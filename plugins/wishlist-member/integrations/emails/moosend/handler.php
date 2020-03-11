<?php // integration handler

if ( ! class_exists( 'WLM_AUTORESPONDER_MOOSEND' ) ) {
	class WLM_AUTORESPONDER_MOOSEND {
		private $wlm;
		private $ar;
		function Moosend( $that, $ar, $level_id, $email, $unsub = false ) {
			$this->wlm = $that;
			$this->ar  = $ar;

			$name = trim( $that->ARSender['name'] );

			if ( $unsub ) {
				$this->unsubscribe( $level_id, $email );
			} else {
				$this->subscribe( $level_id, $email, $name );
			}
		}

		private function subscribe( $level_id, $email, $name ) {
			if ( empty( $this->ar['lists'] ) || empty( $this->ar['lists'][ $level_id ] ) ) {
				return;
			}

			$this->api_request(
				sprintf( 'subscribers/%s/subscribe.json', $this->ar['lists'][ $level_id ] ),
				array(
					'Name'  => $name,
					'Email' => $email,
				)
			);
		}

		private function unsubscribe( $level_id, $email ) {
			if ( empty( $this->ar['lists'] ) || empty( $this->ar['lists'][ $level_id ] ) ) {
				return;
			}
			if ( empty( $this->ar['unsubscribe'] ) || empty( $this->ar['unsubscribe'][ $level_id ] ) ) {
				return;
			}

			$this->api_request(
				sprintf( 'subscribers/%s/unsubscribe.json', $this->ar['lists'][ $level_id ] ),
				array(
					'Email' => $email,
				)
			);
		}

		private function api_request( $endpoint, $data ) {
			$base = 'https://api.moosend.com/v3/';
			$url  = add_query_arg( 'apikey', $this->ar['api_key'], $base . $endpoint );
			wp_remote_post(
				$url,
				array(
					'body'       => json_encode( $data ),
					'headers'    => array(
						'Content-Type' => 'application/json',
						'Accept'       => 'application/json',
					),
					'user-agent' => 'WishList Member/' . $this->wlm->Version,
					'timeout'    => 1,
					'blocking'   => false,
				)
			);
		}
	}
}
