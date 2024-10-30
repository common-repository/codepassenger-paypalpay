<?php 
class Ppdp_Setting {
	
	protected static $instance = null;
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function __construct(){
		add_shortcode('ppdp_donation_button',array($this,'ppdp_donation_button'));
		add_shortcode('ppdp_respons',array($this,'ppdp_respons'));
		add_shortcode('ppdp_cancel',array($this,'ppdp_cancel'));
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
		add_action('admin_post_paypal_from',array($this,'admin_paypal_from'));
		add_action('admin_post_nopriv_paypal_from',array($this,'admin_paypal_from'));
	}
	
	/**
	 * [enqueue_scripts description]
	 * @return [null]
	 */
	public function enqueue_scripts(){
		wp_enqueue_style( 'ppdp-front', plugins_url( '/css/ppdp-front.css', dirname( __FILE__ ) ), array(), '20160615' );
	}
	
	/**
	 * [ppdp_donation_button]
	 * @param  [array] $atts 
	 * @return [markup]
	 * Create Shortcode
	 */
	public function ppdp_donation_button($atts){
		$atts = shortcode_atts(
			array(
				'name' => wp_get_theme(),
				'id' => '',
				'amount' => '',
				'button_class' => '',
				'button_text' => esc_html__('Donation','ppdp'),
			),
			$atts,
			'ppdp_donation_button'
		);		
		return $this->rander_form($atts['name'],$atts['id'],$atts['amount'],$atts);
	}

	/**
	 * [rander_form description]
	 * @param  [strinh] $name   [description]
	 * @param  [int] $id     [description]
	 * @param  [decimal] $amount [description]
	 * @param  array  $atts   [description]
	 * @return [markup]         [description]
	 * Create Form
	 */
	public function rander_form($name,$id,$amount,$atts=array()){
		$amount_show = true;
		if($amount==''){
			$amount = Ppdp_helper::get_pd_text_item('amount');
			if($amount==''){
				$amount_show = false;
			}
		}
		if($name==''){
			$name = wp_get_theme();
		}
		
		$custom_button  = Ppdp_helper::get_pd_option_item('custom_button');
		
		
		$markup = '';
		if($custom_button){
			$class = Ppdp_helper::get_pd_text_item('custom_button_class');
			if($atts['button_class']!=''){
				$class = $atts['button_class'];
			}
			$button = '<input type="submit" name="submit" value="'.$atts['button_text'].'" class="'.$class.'">';
		}else{
			$button = '<input type="image" name="submit"
			src="'.esc_url('https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif','ppdp').'"
			>';
		}
		$markup .= '<form action="'.esc_url(admin_url( 'admin-post.php' )).'" method="post">';
			$markup .= ' <input type="hidden" name="action" value="paypal_from">';
			$markup .= '<input type="hidden" name="cause_name" value="'.$name.'">';
			if($id!=''){
				$markup .= '<input type="hidden" name="cause_number" value="'.$id.'">';
			}
			if($amount_show){
				$markup .= '<input type="hidden" name="cause_amount" value="'.$amount.'">';
			}
			$markup .= $button;
		$markup .= '</form>';
		return $markup;
	}
	
	/**
	 * [admin_paypal_from description]
	 * @return [null] [description]
	 * Handle From Request
	 */
	public function admin_paypal_from(){
		$valid = true;
		$query = array();
		if(isset($_POST['cause_name']) && $_POST['cause_name'] !=''){
			$item_name = $_POST['cause_name'];
		}else{
			$valid = false;
		}
		
		if(!$valid){
			$_SERVER['HTTP_REFERER'];
			header('Location:'.$_SERVER['HTTP_REFERER']);
			exit;
		}
		$query = array_merge($query,$this->paypal_config());
		
		$query['item_name'] = $item_name;
		
		if(isset($_POST['cause_number']) && $_POST['cause_number'] !=''){
			$item_nember = $_POST['cause_number'];
			$query['item_number'] = $item_nember;
		}
		
		if(isset($_POST['cause_amount']) && $_POST['cause_amount'] !=''){
			$query['amount'] = $_POST['cause_amount'];
		}
		$url = $query['url'];
		unset($query['url']);
		$query_string = http_build_query($query);
		header('Location: '.$url.$query_string);
	}
	
	/**
	 * [ppdp_respons]
	 * @return [markup]
	 * Give Payment Transaction Status
	 */
	public function ppdp_respons(){
		if(!empty($_POST)){
			$data = $_POST;
			if(isset($data['payment_status']) && $data['payer_status']!=''){
				$message = $data['first_name'].' Donate '.$data['mc_gross'].' SuccessFully';
				echo '<div class="pd-alert success">'.$message.'</div>';
			}else{
				echo '<div class="pd-alert danger">'.esc_html__('Donation Failed','ppdp').'</div>';
			}
		}
	}
	
	/**
	 * [ppdp_cancel]
	 * @return [markup]
	 */
	public function ppdp_cancel(){
		echo '<div class="pd-alert danger">'.esc_html__('Somthing Wrong','ppdp').'</div>';
	}
	/**
	 * [paypal_config]
	 * @return [array] [description]
	 */
	private function paypal_config(){
		$query = array();
		$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
		$buisness_email  = Ppdp_helper::get_pd_text_item('buisness_email');
		$currency_code  = Ppdp_helper::get_pd_text_item('currency_codes');
		$return = Ppdp_helper::get_pd_text_item('return_page_url');
		$cancel_return  = Ppdp_helper::get_pd_text_item('cancel_page_url');
		$paypal_live  = Ppdp_helper::get_pd_option_item('paypal_live');
		if($paypal_live){
			$url = 'https://www.paypal.com/cgi-bin/webscr?';
		}
		if($buisness_email==''){
			$buisness_email = get_option('admin_email'); 
		}
		if($currency_code==''){
			$currency_code = 'USD';
		}
		if($return==''){
			$return = home_url("/");
		}
		if($cancel_return==''){
			$cancel_return = home_url("/");
		}
		$query['url'] = $url;
		$query['cmd'] = '_donations';
		$query['rm'] = '2';
		$query['business'] = $buisness_email;
		$query['currency_code'] = $currency_code;
		$query['return'] = $return;
		$query['cancel_return'] = $cancel_return;
		return $query;
	}
}
Ppdp_Setting::get_instance();