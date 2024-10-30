<?php 
class Ppdp_helper {
		/**
		 * [get_pd_option_item]
		 * @param  [string] $field_name 
		 * @return [string]             
		 */
		public static function get_pd_option_item($field_name){
		$option_name = PPDP_PREFIX.$field_name;
		$value = get_option( $option_name );
		if( $value && 'on' == $value ):
			return $value;
		else:
			return false;
		endif;
	
	}
	
	/**
	 * [get_pd_text_item]
	 * @param  [string] $field_name 
	 * @return [string]            
	 */
	public static function get_pd_text_item($field_name){
		
		$option_name = PPDP_PREFIX.$field_name;
		$value = get_option( $option_name );
		if( $value ):
			return $value;
		else:
			return false;
		endif;
	
	}
}