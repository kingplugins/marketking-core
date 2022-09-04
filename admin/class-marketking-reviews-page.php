<?php

	class WP_Comments_List_Table_Reviews extends WP_Comments_List_Table {
		public function get_columns() {
	        global $post_id;
	 
	        $columns = array();
	 
	        if ( $this->checkbox ) {
	            $columns['cb'] = '<input type="checkbox" />';
	        }
	 
	        $columns['author']  = esc_html__( 'Customer','marketking-multivendor-marketplace-for-woocommerce' );
	        $columns['comment'] = esc_html_x( 'Review', 'column name','marketking-multivendor-marketplace-for-woocommerce' );
	        $columns['rating'] = esc_html_x( 'Rating', 'column name','marketking-multivendor-marketplace-for-woocommerce' );

	        $columns['vendor'] = esc_html_x( 'Vendor', 'column name','marketking-multivendor-marketplace-for-woocommerce' );
	 
	        if ( ! $post_id ) {
	            /* translators: Column name or table row header. */
	            $columns['response'] = esc_html__( 'Product', 'marketking-multivendor-marketplace-for-woocommerce' );
	        }
	 
	        $columns['date'] = esc_html_x( 'Date', 'column name','marketking-multivendor-marketplace-for-woocommerce' );
	 
	        return $columns;
	    }
	}

?>