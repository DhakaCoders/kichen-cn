<?php // integration handler
if ( ! class_exists( 'WLM_INTEGRATION_WOOCOMMERCE' ) ) {
	class WLM_INTEGRATION_WOOCOMMERCE {
		function __construct() {
			add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 3 );
			add_action( 'woocommerce_subscription_status_changed', array( $this, 'subscription_status_changed' ), 10, 3 );
			add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
			add_action( 'untrashed_post', array( $this, 'untrash_post' ), 1000 );
		}

		/**
		 * Removes levels from a member if an order is trashed
		 *
		 * @param int $post_id
		 */
		function trash_post( $post_id ) {
			$order = wc_get_order( $post_id );
			if ( ! $order ) {
				return;
			}
			$this->__remove_levels( $this->__generate_transaction_id( $order ) );
		}

		/**
		 * Restores an order from trash and updates levels accordingly
		 *
		 * @param int $post_id
		 */
		function untrash_post( $post_id ) {
			$order = wc_get_order( $post_id );
			if ( ! $order ) {
				return;
			}
			$function = function_exists( 'wcs_is_subscription' ) && wcs_is_subscription( $order ) ? 'subscription_status_changed' : 'order_status_changed';
			call_user_func( array( $this, $function ), $post_id, 'trash', $order->get_status() );
		}

		/**
		 * Map subscription status to either activate, remove or deactivate
		 * Called by woocommerce_subscription_status_changed action
		 *
		 * @uses WLM_INTEGRATION_WOOCOMMERCE::__status_changed
		 *
		 * @param int    $order_id
		 * @param string $old_status
		 * @param string $new_status
		 */
		function subscription_status_changed( $order_id, $old_status, $new_status ) {
			switch ( $new_status ) {
				case 'active':
					$status = 'activate';
					break;
				case 'cancelled':
					$status = 'deactivate';
					break;
				case 'pending':
				case 'on-hold':
					$status = 'remove';
					break;
				case 'switched':
				case 'pending-cancel':
				case 'expired':
				default:
					$status = '';
			}
			if ( $status ) {
				$this->__status_changed( $order_id, $status );
			}
		}

		/**
		 * Map order status change to either activate, remove or deactivate
		 * Called by woocommerce_order_status_changed action
		 *
		 * @uses WLM_INTEGRATION_WOOCOMMERCE::__status_changed
		 *
		 * @param int    $order_id
		 * @param string $old_status
		 * @param string $new_status
		 */
		function order_status_changed( $order_id, $old_status, $new_status ) {
			switch ( $new_status ) {
				case 'processing':
				case 'completed':
					$status = 'activate';
					break;
				case 'cancelled':
				case 'refunded':
					$status = 'deactivate';
					break;
				case 'pending':
				case 'on-hold':
				case 'failed':
					$status = 'remove';
					break;
				default:
					$status = '';
			}
			if ( $status ) {
				$this->__status_changed( $order_id, $status );
			}
		}

		/**
		 * Updates a member's levels or their status
		 * Creates a new member if one doesn't exist yet
		 * Used info is gathered from the $order_id
		 *
		 * @param int    $order_id
		 * @param string $status
		 */
		private function __status_changed( $order_id, $status ) {
			global $WishListMemberInstance, $wlm_no_cartintegrationterminate;
			$woocommerce_products = $WishListMemberInstance->GetOption( 'woocommerce_products' );

			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return;
			}
			$txnid = $this->__generate_transaction_id( $order );

			switch ( $status ) {
				case 'activate':
					// take care adding of new customer and levels
					$user = $order->get_customer_id();
					if ( ! $user ) {
						$user = get_user_by_email( $order->get_billing_email() );
						if ( ! $user ) {
							$user = array(
								'first_name'       => $order->get_billing_first_name(),
								'last_name'        => $order->get_billing_last_name(),
								'user_email'       => $order->get_billing_email(),
								'user_login'       => $order->get_billing_email(),
								'user_pass'        => wp_generate_password(),
								'SendMailPerLevel' => 1,
							);
						} else {
							$user = $user->ID;
						}
					}
					$levels = array();
					foreach ( $order->get_items() as $item ) {
						$pid = $item->get_product()->id;
						if ( isset( $woocommerce_products[ $pid ] ) && is_array( $woocommerce_products[ $pid ] ) ) {
							$levels = array_merge( $levels, $woocommerce_products[ $pid ] );
						}
					}
					if ( $levels ) {
						$memlevels = array();
						if ( is_int( $user ) ) {
							$memlevels = $WishListMemberInstance->GetMembershipLevels( $user, false, true );
						}
						$levels = array_unique( $levels );
						foreach ( $levels as &$level ) {
							$level = in_array( $level, $memlevels ) ? false : array( $level, $txnid );
						}
						unset( $level );
						$levels = array( 'Levels' => array_diff( $levels, array( false ) ) );
						if ( is_array( $user ) ) {
							$x = wlmapi_add_member( $user + $levels );
						} else {
							wlmapi_update_member( $user, array( 'SendMailPerLevel' => 1 ) + $levels );
						}
					}

					$old                             = $wlm_no_cartintegrationterminate;
					$wlm_no_cartintegrationterminate = true;
					$_POST['sctxnid']                = $txnid;

					$WishListMemberInstance->ShoppingCartReactivate();
					$wlm_no_cartintegrationterminate = $old;
					break;
				case 'deactivate':
					$old                             = $wlm_no_cartintegrationterminate;
					$wlm_no_cartintegrationterminate = true;
					$_POST['sctxnid']                = $txnid;

					$WishListMemberInstance->ShoppingCartDeactivate();
					$wlm_no_cartintegrationterminate = $old;
					break;
				case 'remove':
					$this->__remove_levels( $txnid );
					break;
			}
		}

		/**
		 * Removes levels from a member based on transaction ID
		 *
		 * @param string $txnid
		 */
		private function __remove_levels( $txnid ) {
			global $WishListMemberInstance;
			$user_id = $WishListMemberInstance->GetUserIDFromTxnID( $txnid );
			if ( $user_id ) {
				$levels = $WishListMemberInstance->GetMembershipLevelsTxnIDs( $user_id, $txnid );
				if ( $levels ) {
					wlmapi_update_member( $user_id, array( 'RemoveLevels' => array_keys( $levels ) ) );
				}
			}

		}

		/**
		 * Generates transaction id from order WooCommerce object
		 *
		 * @param WC_Order $order
		 */
		private function __generate_transaction_id( $order ) {
			return 'WooCommerce#' . $order->get_parent_id() . '-' . $order->get_order_number();
		}
	}
	new WLM_INTEGRATION_WOOCOMMERCE();
}
