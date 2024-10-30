<?php
/*
 * WPPC Product catalog Settings Field
 *
 *@return array
*/
class Ppdp_fields_setting {
	public function __construct(){
		add_filter( 'ppdp_settings_fields',array($this,'ppdp_settings_fields'));
	}
	
	protected static $instance = null;
	
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * [ppdp_settings_fields description]
	 * @param  [array] $settings [Setting Array]
	 * @return [array]           [return Setting array]
	 * Create Filed in Wordpress Admin Panel
	 */
	public function ppdp_settings_fields( $settings) {
		
		$settings['pdppaypal_info'] = array(
			'title'					=>  esc_html__( 'Ppdp Setting', 'ppdp' ),
			'description'			=>  '',
			'fields'				=> array(
				array(
                    'id' 			=> 'paypal_live',
                    'title'			=>  esc_html__( 'Paypal Live', 'ppdp' ),
                    'type'			=> 'checkbox',
                ),
				array(
                    'id' 			=> 'buisness_email',
                    'title'			=>  esc_html__( 'Buisness Email', 'ppdp' ),
                    'type'			=> 'text',
					'description'   =>esc_html__('if Business Email is Empty use Admin Email instead of Business Email','ppdp')
                ),
				array(
                    'id' 			=> 'amount',
                    'title'			=>  esc_html__( 'Default Amount', 'ppdp' ),
                    'type'			=> 'text',
                ),
				array(
                    'id' 			=> 'return_page_url',
                    'title'			=>  esc_html__( 'Return Page Url', 'ppdp' ),
                    'type'			=> 'text',
                ),
				array(
                    'id' 			=> 'cancel_page_url',
                    'title'			=>  esc_html__( 'Cancel Page Url', 'ppdp' ),
                    'type'			=> 'text',
                ),
				array(
                    'id' 			=> 'currency_codes',
                    'title'			=>  esc_html__( 'Currency Codes', 'ppdp' ),
                    'type'			=> 'select',
					'options'       => $this->currency_codes(),
					'std'			=> 'USD'
                ),
				array(
                    'id' 			=> 'custom_button',
                    'title'			=>  esc_html__( 'Custom Button', 'ppdp' ),
                    'type'			=> 'checkbox',
                ),
				array(
                    'id' 			=> 'custom_button_class',
                    'title'			=>  esc_html__( 'Custom Button Class', 'ppdp' ),
                    'type'			=> 'text',
                )
			)
		);
		return $settings;
	}
	
	/**
	 * [currency_codes]
	 * @return [array] 
	 * 
	 */
	public function currency_codes(){
		return	array(
			'AUD'=> esc_html__('Australian Dollar','ppdp'),
			'CAD'=> esc_html__('Canadian Dollar','ppdp'),
			'EUR'=> esc_html__('Euro','ppdp'),
			'USD'=> esc_html__('U.S. Dollar','ppdp')
		);
	}
}

Ppdp_fields_setting::instance();



