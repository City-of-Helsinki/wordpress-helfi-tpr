<?php

namespace CityOfHelsinki\WordPress\TPR\Cpt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CityOfHelsinki\WordPress\TPR\Api\Units;

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Helsinki_TPR_Search_Table extends \WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );

        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return array
     */
    public function get_columns()
    {
        return array(
            'name_fi' => __('Name', 'helsinki-tpr'),
            'id' => 'ID',
            'short_desc_fi' => __('Description', 'helsinki-tpr'),
            'control' => __('Options', 'helsinki-tpr'),
        );
    }

    /**
     * Define which columns are hidden
     *
     * @return array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        return array( 'title' => array( 'title', false ) );
    }

    /**
     * Get the table data
     *
     * @return array
     */
    private function table_data()
    {
		$query = ! empty( $_GET['query'] )
			? \sanitize_text_field( $_GET['query'] )
			: '';

        return \apply_filters( 'helsinki_tpr_units_search', array(), $query );
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  array $item        Data
     * @param  string $column_name - Current column name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'name_fi':
            case 'short_desc_fi':
                return isset($item->$column_name) ? $item->$column_name : '';
            case 'control':
                return $this->render_row_controls($item->id, $item->name_fi);

            default:
                return print_r( $item, true ) ;
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'name_fi';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }

        $result = strcmp( $a->{$orderby}, $b->{$orderby} );

        if($order === 'asc')
        {
            return $result;
        }

        return -$result;
    }

    private function render_row_controls( $id, $title )
	{
        $unit_post = $this->find_unit_post_by_tpr_id( $id );

		return $unit_post
			? link_to_tpr_edit_post( $unit_post->ID )
			: sprintf(
                '<button class="button button-primary helsinki-tpr-import-button" data-tpr-id="%s" data-tpr-title="%s">%s</button><span class="spinner"></span>',
                \esc_attr( $id ),
                \esc_html( $title ),
                esc_html__( 'Import unit', 'helsinki-tpr' )
            );
    }

	private function find_unit_post_by_tpr_id( $id )
	{
		if ( ! $id ) {
			return null;
		}

		$query = new \WP_Query( array(
            'post_type' => 'helsinki_tpr_unit',
            'meta_key' => 'tpr_id',
            'meta_value' => $id,
			'no_found_rows' => true,
			'posts_per_page' => 1,
        ) );

		return $query->posts ? $query->posts[0] : null;
	}
}
