<?php

/**
 * Intranet
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2015 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      https://wordpress-cost-estimator.zigaform.com
 */
if ( ! defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if ( class_exists('Uiform_Model_Form')) {
    return;
}

/**
 * Model Form class
 *
 * @category  PHP
 * @package   Rocket_form
 * @author    Softdiscover <info@softdiscover.com>
 * @copyright 2013 Softdiscover
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.00
 * @link      https://wordpress-cost-estimator.zigaform.com
 */
class Uiform_Model_Form
{

    private $wpdb = '';
    public $table = '';
    public $tbformtype = '';
    public $tbformfields = '';

    public function __construct()
    {
        global $wpdb;
        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . 'cest_uiform_form';
        $this->tbformtype   = $wpdb->prefix . 'cest_uiform_fields_type';
        $this->tbformfields = $wpdb->prefix . 'cest_uiform_fields';
    }


    /**
     * formsmodel::getListForms()
     * List form estimator
     *
     * @param int $per_page max number of form estimators
     * @param int $segment  Number of pagination
     *
     * @return array
     */
    public function getListFormsFiltered($data)
    {

        $per_page   = $data['per_page'];
        $segment    = $data['segment'];
        $search_txt = $data['search_txt'];
        $orderby    = $data['orderby'];

        $query = sprintf(
            '
			select uf.fmb_id,uf.fmb_data,uf.fmb_name,uf.fmb_html,uf.fmb_html_backend,uf.flag_status,uf.created_date,uf.updated_date,
				uf.fmb_html_css,uf.fmb_default,uf.fmb_skin_status,uf.fmb_skin_data,uf.fmb_skin_type,uf.fmb_data2, uf.fmb_type
			from %s uf
			where uf.flag_status>0 and uf.fmb_type in (0,1)',
            $this->table
        );

        if ( ! empty($search_txt)) {
            $query .= " and  uf.fmb_name like '%" . $search_txt . "%' ";
        }

        $orderby = ( $orderby === 'asc' ) ? 'asc' : 'desc';

        $query .= sprintf(' ORDER BY uf.updated_date %s ', $orderby);

        if ( (int) $per_page > 0) {
            $segment = ( ! empty($segment) ) ? $segment : 0;
            $query  .= sprintf(' limit %s,%s', (int) $segment, (int) $per_page);
        }

        return $this->wpdb->get_results($query);
    }

    /**
     * formsmodel::getListForms()
     * List form estimator
     *
     * @param int $per_page max number of form estimators
     * @param int $segment  Number of pagination
     *
     * @return array
     */
    public function getListTrashFormsFiltered($data)
    {

        $per_page   = $data['per_page'];
        $segment    = $data['segment'];
        $orderby    = $data['orderby'];

        $query = sprintf(
            '
			select uf.fmb_id,uf.fmb_data,uf.fmb_name,uf.fmb_html,uf.fmb_html_backend,uf.flag_status,uf.created_date,uf.updated_date,
				uf.fmb_html_css,uf.fmb_default,uf.fmb_skin_status,uf.fmb_skin_data,uf.fmb_skin_type,uf.fmb_data2
			from %s uf
			where uf.flag_status=0 and uf.fmb_type in (0,1)',
            $this->table
        );

        $orderby = ( $orderby === 'asc' ) ? 'asc' : 'desc';

        $query .= sprintf(' ORDER BY uf.updated_date %s ', $orderby);

        if ( (int) $per_page > 0) {
            $segment = ( ! empty($segment) ) ? $segment : 0;
            $query  .= sprintf(' limit %s,%s', (int) $segment, (int) $per_page);
        }

        return $this->wpdb->get_results($query);
    }

    /**
     * formsmodel::getListForms()
     * List form estimator
     *
     * @param int $per_page max number of form estimators
     * @param int $segment  Number of pagination
     *
     * @return array
     */
    public function getListForms($per_page = '', $segment = '')
    {
        $query = sprintf(
            '
			select uf.fmb_id,uf.fmb_data,uf.fmb_name,uf.fmb_html,uf.fmb_html_backend,uf.flag_status,uf.created_date,uf.updated_date,
				uf.fmb_html_css,uf.fmb_default,uf.fmb_skin_status,uf.fmb_skin_data,uf.fmb_skin_type,uf.fmb_data2
			from %s uf
			where uf.flag_status>0  and uf.fmb_type in (0,1)
			ORDER BY uf.updated_date desc
			',
            $this->table
        );

        if ( (int) $per_page > 0) {
            $segment = ( ! empty($segment) ) ? $segment : 0;
            $query  .= sprintf(' limit %s,%s', (int) $segment, (int) $per_page);
        }

        return $this->wpdb->get_results($query);
    }

    public function getFormById($id)
    {
        $query = sprintf(
            '
            select uf.fmb_id,uf.fmb_data,uf.fmb_name,uf.fmb_html,uf.fmb_html_backend,uf.flag_status,uf.created_date,uf.updated_date, uf.fmb_parent,
                uf.fmb_html_css,uf.fmb_default,uf.fmb_skin_status,uf.fmb_skin_data,uf.fmb_skin_type,uf.fmb_data2,uf.fmb_rec_tpl_html,uf.fmb_rec_tpl_st, uf.fmb_inv_tpl_st, uf.fmb_inv_tpl_html,  uf.fmb_type, uf.fmb_parent
            from %s uf
            where uf.fmb_id=%s  
            ',
            $this->table,
            $id
        );

        return $this->wpdb->get_row($query);
    }

    public function getTitleFormById($id)
    {
        $query = sprintf(
            '
			select uf.fmb_name
			from %s uf
			where uf.fmb_id=%s
			',
            $this->table,
            (int) $id
        );

        return $this->wpdb->get_row($query);
    }

    public function getAvailableFormById($id)
    {
        $query = sprintf(
            '
			select uf.fmb_id,uf.fmb_data,uf.fmb_name,uf.fmb_html,uf.fmb_html_backend,uf.flag_status,uf.created_date,uf.updated_date,
				uf.fmb_html_css,uf.fmb_default,uf.fmb_skin_status,uf.fmb_skin_data,uf.fmb_skin_type,uf.fmb_data2
			from %s uf
			where 
			uf.flag_status=1 and
			uf.fmb_id=%s
			',
            $this->table,
            (int) $id
        );
        return $this->wpdb->get_row($query);
    }
    
    public function getAvailableForms()
    {
        $query = sprintf(
            '
            select uf.fmb_id,uf.fmb_data,uf.fmb_name,uf.fmb_html,uf.fmb_html_backend,uf.flag_status,uf.created_date,uf.updated_date,
                uf.fmb_html_css,uf.fmb_default,uf.fmb_skin_status,uf.fmb_skin_data,uf.fmb_skin_type,uf.fmb_data2
            from %s uf
            where 
            uf.flag_status=1
            ',
            $this->table
        );

        return $this->wpdb->get_results($query);
    }
    
    public function getChildFormByParentId($id)
    {
        $query = sprintf(
            '
            select uf.fmb_id,uf.fmb_data,uf.fmb_name,uf.fmb_html,uf.fmb_html_backend,uf.flag_status,uf.created_date,uf.updated_date,
                uf.fmb_html_css,uf.fmb_default,uf.fmb_skin_status,uf.fmb_skin_data,uf.fmb_skin_type,uf.fmb_data2, uf.fmb_rec_tpl_html, uf.fmb_rec_tpl_st, uf.fmb_type, uf.fmb_parent
            from %s uf
            where 
            uf.flag_status=1 and
            uf.fmb_parent=%s
            ',
            $this->table,
            $id
        );

        return $this->wpdb->get_results($query);
    }
    
    public function getFieldsById($id)
    {
        $query = sprintf(
            '
            select
            	f.fmf_uniqueid,
            	f.fmf_id,
            	coalesce(NULLIF(f.fmf_fieldname, ""), CONCAT(t.fby_name, f.fmf_id)) as fieldname ,
            	f.type_fby_id,
            	f.fmf_data,
            	fm.fmb_id
            from
            	%s f
            join %s t on
            	f.type_fby_id = t.fby_id
            join %s fm on
            	fm.fmb_id = f.form_fmb_id
            where fm.fmb_id = %s
            ',
            $this->tbformfields,
            $this->tbformtype,
            $this->table,
            $id
        );

        return $this->wpdb->get_results($query);
    }
    
    public function getFieldNamesById($id_form)
    {
        $query  = sprintf(
            'select f.fmf_uniqueid,f.fmf_id, fm.fmb_type, coalesce(NULLIF(f.fmf_fieldname,""),CONCAT(t.fby_name,f.fmf_id)) as fieldname 
        from %s f 
        join %s t on f.type_fby_id=t.fby_id 
        join %s fm on fm.fmb_id=f.form_fmb_id
        where fm.fmb_id=%s and t.fby_id in (8,9,10,11,16,18,39,40,41,42)',
            $this->tbformfields,
            $this->tbformtype,
            $this->table,
            (int) $id_form
        );
        return $this->wpdb->get_results($query);
    }
    
    public function getFormById_2($id)
    {
        $query = sprintf(
            '
			select uf.fmb_data2,uf.fmb_name, uf.fmb_type, uf.fmb_rec_tpl_st,  uf.fmb_inv_tpl_st
			from %s uf
			where uf.fmb_id=%s
			',
            $this->table,
            (int) $id
        );

        return $this->wpdb->get_row($query);
    }

    public function CountForms()
    {
        $query = sprintf(
            '
			select COUNT(*) AS counted
			from %s c
			where c.flag_status>0  and c.fmb_type in (0,1)
			ORDER BY c.updated_date desc
			',
            $this->table
        );
        $row   = $this->wpdb->get_row($query);
        if ( isset($row->counted)) {
            return $row->counted;
        } else {
            return 0;
        }
    }

    /*
    * list all and trash forms
    */
    public function ListTotals()
    {
        $query = sprintf(
            '
			SELECT 
			  SUM(CASE WHEN flag_status = 0 THEN 1 ELSE 0 END) AS r_trash,
			  SUM(CASE WHEN flag_status != 0 THEN 1 ELSE 0 END) AS r_all
			FROM %s 
			WHERE fmb_type in (0,1)
			',
            $this->table
        );

        return $this->wpdb->get_row($query);
    }
}
