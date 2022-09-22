<?php

namespace CityOfHelsinki\WordPress\TPR\SearchTable;

use CityOfHelsinki\WordPress\TPR\Cpt as Config;
use CityOfHelsinki\WordPress\TPR\Api\Units;

function render_table() {
    $searchTable = new Helsinki_TPR_Search_Table();
    $searchTable->prepare_items();
    $searchTable->display();
}

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Helsinki_TPR_Search_Table extends \WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
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
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'name_fi'       => __('Name', 'helsinki-tpr'),
            'id'          => 'ID',
            'short_desc_fi' => __('Description', 'helsinki-tpr'),
            'control'        => __('Options', 'helsinki-tpr'),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('title' => array('title', false));
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        $data = array();

        $query = $_GET['query'] ?? 0;

        if ($query) {
            $units = Units::search_units( $query );
            $data = $units;
        }

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
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
     * @return Mixed
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

    private function render_row_controls($id, $title) {
        $args = array(
            'post_type' => 'helsinki_tpr_unit',
            'meta_key' => 'tpr_id',
            'meta_value' => $id,
        );
        $query = new \WP_Query($args);

        if ($query->found_posts > 0) {
            return Config\link_to_tpr_edit_post($query->posts[0]->ID);
        }
        else {
            return sprintf(
                '<button class="button button-primary helsinki-tpr-import-button" data-tpr-id="%s" data-tpr-title="%s">%s</button><span class="spinner"></span>',
                $id,
                $title,
                __('Import unit', 'helsinki-tpr')
            );
        }

    }
}
?>