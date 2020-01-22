<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/PipLabs/pip-checkout-woocommerce/
 * @since      1.0.0
 *
 * @package    Pip_Payment_Gateway
 * @subpackage Pip_Payment_Gateway/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Pip_Payment_Gateway
 * @subpackage Pip_Payment_Gateway/includes
 * @author     Pip Labs <dev@pip.cash>
 */
add_action( 'plugins_loaded', 'wc_pip_payment_gateway', 11 );
function wc_pip_payment_gateway(){


		class Woocommerce_Pip_Payment_Gateway  extends WC_Payment_Gateway{

			public function __construct() {
				
				 	$this->id                 = 'pip-checkout';
					$this->icon               = apply_filters('woocommerce_pip_icon', '');
					$this->has_fields         = true;
					$this->method_title       = __( 'Pip Checkout', 'pip-payment-gateway' );
					$this->method_description = __( 'Allows Payment with Pip Gateway', 'pip-payment-gateway' );
				  
					// Load the settings.
					$this->init_form_fields();
					$this->init_settings();
				  
					// Define user set variables
					$this->title        = $this->get_option( 'title' );
					$this->description  = $this->get_option( 'description' );
					$this->instructions = $this->get_option( 'instructions', $this->description );
				  
					// Actions
					
				  $this->initHooks();


			}

			public function initHooks(){
               	global $current_tab;
               	
				
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ),99999 );
				add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ),99,1 );
				// Customer Emails
				add_action( 'woocommerce_email_after_order_table', array( $this, 'email_instructions' ), 10, 3 );
				add_action("woocommerce_sections_".$current_tab,array($this,"show_wraning_messages"));
				add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this,"show_pip_api_response"), 10, 1 );
			}
			public function payment_fields() {
				$description = $this->get_description();
				if ( $description ) {
					echo wpautop( wptexturize( $description ) ); // @codingStandardsIgnoreLine.
				}

				$settings = $this->settings;
				 if($settings['veil_address'] == '' && $settings['btc_address'] == '' && $settings['dbg_address'] ==''){
                   
					$message = __( 'Merchant has not configured their receiving address.', 'sample-text-domain' );
					printf( '<ul class="woocommerce-error" style="padding:0 14px"><li>%1$s</li></ul>', esc_html( $message ) );
				 }	

				
			}

			public function show_pip_api_response($order){
              $pip_response= json_decode(get_post_meta($order->get_id(),'pip-response',true));
               if(empty($pip_response)) return ;
                echo '</div><div class="order_data_column" ><h3> Pay With Cryptocurrecy</h3>';
                foreach($pip_response as $row):  ?>
                                   <p><strong>
									Send Exactly:</strong> <br/><?php echo $row->amount.' '.strtoupper($row->ticker);?>
								</p>
								<p><strong>
									To Address:</strong> <br/><?php echo $this->get_merchant_key($row->ticker);?>
								</p>
								
                            	<?php endforeach; 
             
			}

			public function show_wraning_messages(){
				  if(isset($_GET['section'])  && $_GET['section'] == 'pip-checkout'){
                      $settings = $this->settings;
                      if($settings['enabled'] == 'yes'){
                      	  if($settings['veil_address'] == '' && $settings['btc_address'] == '' && $settings['dbg_address'] ==''){
                      	  	$class = 'notice notice-error';
						    	$message = __( 'Merchant has not configured their receiving address.', 'sample-text-domain' );
						 
						    	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
                      	  }
                      }
				  }
			}



			
			/**
				 * Initialize Gateway Settings Form Fields
				 */
			public function init_form_fields() {
			       
					$this->form_fields = apply_filters( 'wc_pip_payment_gateway_form_fields', array(
				  
						'enabled' => array(
							'title'   => __( 'Enable Pip Checkout', 'pip-payment-gateway' ),
							'type'    => 'checkbox',
							'label'   => __( 'Enable Pip Checkout', 'pip-payment-gateway' ),
							'default' => 'yes'
						),
						
						'title' => array(
							'title'       => __( 'Title', 'pip-payment-gateway' ),
							'type'        => 'text',
							'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'pip-payment-gateway' ),
							'default'     => __( 'Pip Checkout', 'pip-payment-gateway' ),
							'desc_tip'    => true,
						),
						
						'description' => array(
							'title'       => __( 'Description', 'pip-payment-gateway' ),
							'type'        => 'textarea',
							'description' => __( 'Pay With Cryptocurrency, Your order will not be shipped unless you pay the exact amount.', 'pip-payment-gateway' ),
							'default'     => __( 'Pay With Cryptocurrency, Your order will not be shipped unless you pay the exact amount' ),
							'desc_tip'    => true,
						),
						

						'veil_address' => array(
							'title'       => __( 'VEIL Address', 'pip-payment-gateway' ),
							'type'        => 'text',
							'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'pip-payment-gateway' ),
							'default'     => __( '', 'pip-payment-gateway' ),
							'desc_tip'    => true,
						),

						'btc_address' => array(
							'title'       => __( 'BTC Address', 'pip-payment-gateway' ),
							'type'        => 'text',
							'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'pip-payment-gateway' ),
							'default'     => __( '', 'pip-payment-gateway' ),
							'desc_tip'    => true,
						),
						'dbg_address' => array(
							'title'       => __( 'DBG Address', 'pip-payment-gateway' ),
							'type'        => 'text',
							'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'pip-payment-gateway' ),
							'default'     => __( '', 'pip-payment-gateway' ),
							'desc_tip'    => true,
						),
						
						
					) );
				}
			
			
				/**
				 * Output for the order received page.
				 */
				public function thankyou_page($order_id ) {
					    $pip_response= json_decode(get_post_meta($order_id,'pip-response',true));
                         if(empty($pip_response)) return ;
                         ?>
                         <section class='pip-checkout-section'>
                           <div class="">
                            	<h2 class='woocommerce-column__title'>Pay with cryptocurrency.</h2>
                            	<span>Your order will not be shipped unless you pay the exact amount.</span>
                            	
                            </div> 
                            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
                            	<?php foreach($pip_response as $row):  ?>
                                    <li class="woocommerce-order-overview__order order">
									Send Exactly: <br/><strong><?php echo $row->amount.' '.strtoupper($row->ticker);?></strong>
								</li>
								<li class="woocommerce-order-overview__order order">
									To Address: <br/><strong><?php echo $this->get_merchant_key($row->ticker);?></strong>
								</li>
                            	<?php endforeach; ?>
                            	
                            </ul> 	
                          </section>  
                         <?php
						 
					
				}

				public function get_merchant_key($ticker){
					    $settings = $this->settings;

					   if($ticker == "btc"){
                         return $settings['btc_address'];
					   }
					   else if($ticker == "dgb"){
                         return $settings['dbg_address']; 
					   }
					   else if($ticker == "veil"){
                            return $settings['veil_address'];
					   }
					   return '';
				}
			
			
				/**
				 * Add content to the WC emails.
				 *
				 * @access public
				 * @param WC_Order $order
				 * @param bool $sent_to_admin
				 * @param bool $plain_text
				 */
				public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

					if ( $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
						$pip_response= json_decode(get_post_meta($order->get_id(),'pip-response',true));
                         if(empty($pip_response)) return ;
						?>
						
                           <div style="margin-bottom: 40px;">
                            	<h2 class='woocommerce-column__title'>Pay with cryptocurrency.</h2>
                            	<span>Your order will not be shipped unless you pay the exact amount.</span>

                           
                            <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
								<tbody>

                            	<?php foreach($pip_response as $row):  ?>
                            		<tr>
                                    	<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">Send Exactly: </td>
                                    	<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"> <strong><?php echo $row->amount.' '.strtoupper($row->ticker);?></strong> </td>
                                    </tr>
                                    <tr>
                                    	<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">To Address: </td>
                                    	<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"> <strong><?php echo $this->get_merchant_key($row->ticker);?></strong> </td>
                                    </tr>	
								
								
                            	<?php endforeach; ?>
                            	</tbody>
                            	
                            </table> 	
                           </div>   
						<?php
					}
				}

				public function process_api_call($order_id){
					$order = wc_get_order( $order_id );
					$settings = $this->settings;
					$wallet_address = array_filter(array("btc"=>$settings['btc_address'],"veil"=>$settings['veil_address'],"dgb"=>$settings['dbg_address']));

					$data=array("amount"=>$order->get_total(),
								"walletAddresses"=>$wallet_address,
								"currency"=>strtolower(get_woocommerce_currency()),
								"orderId"=>"WC".$order_id,

								);
                    
                   
                    $request = wp_remote_post("https://api.pip.cash/v1/quotes", array(
					    'body'    => json_encode($data),
					    'method'      => 'POST',
    					'data_format' => 'body',
					    'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
					    
					) );
					$response = json_decode( wp_remote_retrieve_body( $request ), true );
                   
					
					if (200 != wp_remote_retrieve_response_code( $request ) ){
						return array("status"=>false,"message"=>"Merchant does not have valid receiving address configured");
					}
					$response = json_decode( wp_remote_retrieve_body( $request ), true );
					if(!empty($response)){
						return array("status"=>true,"message"=>"succcess","resp"=>$response);
						
					}
					return array("status"=>false,"message"=>"Merchant does not have valid receiving address configured");
                    
					 
				}
			
			
				/**
				 * Process the payment and return the result
				 *
				 * @param int $order_id
				 * @return array
				 */
				public function process_payment( $order_id ) {
					
			        $response= $this->process_api_call($order_id);
                    
			        if(isset($response['status']) && $response['status'] == true ) {
			        	  $order = wc_get_order( $order_id );
			        	$resp=$response['resp'];
			        	update_post_meta($order_id,"pip-response",json_encode($resp));  
                        
						// Mark as on-hold (we're awaiting the payment)
						$order->update_status( 'on-hold', __( 'Awaiting Cryptocurrecy payment Order status changed from payment pending to on hold', 'pip-payment-gateway' ) );
						
						// Reduce stock levels
						$order->reduce_order_stock();
						
						// Remove cart
						WC()->cart->empty_cart();
						
						// Return thankyou redirect
						return array(
							'result' 	=> 'success',
							'redirect'	=> $this->get_return_url( $order )
						);

			        }
			        else{
			        	     throw new Exception($response['message']);
			        	     return false;
			        }
			        

			        throw new Exception("Merchant does not have valid receiving address configured");
			        
					return false ;
				}

		}
}
