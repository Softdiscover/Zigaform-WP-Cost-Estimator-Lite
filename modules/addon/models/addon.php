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
if ( class_exists('Uiform_Model_Addon')) {
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
class Uiform_Model_Addon
{

    private $wpdb = '';
    public $table = '';
    public $tbaddon_details = '';

    public function __construct()
    {
        global $wpdb;
        $this->wpdb            = $wpdb;
        $this->table           = $wpdb->prefix . 'cest_addon';
        $this->tbaddon_details = $wpdb->prefix . 'cest_addon_details';
    }

    public function getRequiredAddonNames()
    {
        return array_keys($this->getRequiredAddons());
    }

    public function isRequiredAddon($addon_name)
    {
        $addons = $this->getRequiredAddons();
        return isset($addons[$addon_name]);
    }

    public function ensureRequiredAddons()
    {
        if (! $this->addonTableExists()) {
            return;
        }

        foreach ($this->getRequiredAddons() as $addon_name => $addon_data) {
            if ($this->existAddon($addon_name)) {
                $update_data = $addon_data;
                unset($update_data['add_name'], $update_data['created_date']);
                $this->wpdb->update(
                    $this->table,
                    $update_data,
                    array(
                        'add_name' => $addon_name,
                    )
                );
            } else {
                $this->wpdb->insert($this->table, $addon_data);
            }
        }
    }

    private function addonTableExists()
    {
        $query = $this->wpdb->prepare('SHOW TABLES LIKE %s', $this->table);
        return (string) $this->wpdb->get_var($query) === $this->table;
    }

    private function getRequiredAddons()
    {
        return array(
            'cost_estimation' => array(
                'add_name'       => 'cost_estimation',
                'add_title'      => 'Cost Estimation',
                'add_info'       => 'Required calculation engine for the cost estimation form builder.',
                'add_system'     => 1,
                'add_hasconfig'  => 0,
                'add_version'    => '1.0',
                'add_icon'       => null,
                'add_installed'  => 1,
                'add_order'      => 99,
                'add_params'     => '{"locked":true}',
                'add_log'        => null,
                'addonscol'      => null,
                'flag_status'    => 1,
                'created_date'   => '2026-06-15 00:00:00',
                'updated_date'   => '2026-06-15 00:00:00',
                'created_ip'     => null,
                'updated_ip'     => null,
                'created_by'     => null,
                'updated_by'     => null,
                'add_xml'        => null,
                'add_load_back'  => 1,
                'add_load_front' => 1,
                'is_field'       => 0,
            ),
        );
    }


    public function getListAddonsByBack()
    {
        $this->ensureRequiredAddons();

        $query = sprintf(
            '
            select c.add_name
                    ,c.add_title
                    ,c.add_system
                    ,c.add_hasconfig
                    ,c.add_version
                    ,c.add_icon
                    ,c.add_installed
                    ,c.add_order
                    ,c.add_params
                    ,c.add_log
                    ,c.addonscol
                    ,c.flag_status
                    ,c.created_date
                    ,c.updated_date
                    ,c.created_ip
                    ,c.updated_ip
                    ,c.created_by
                    ,c.updated_by
                    ,c.add_xml
                    ,c.add_load_back
                    ,c.add_load_front
                    ,c.is_field
            from %s c
            where c.flag_status=1 
            and c.add_load_back=1
            ORDER BY c.add_order desc
            ',
            $this->table
        );

        return $this->wpdb->get_results($query);
    }

    public function getListAddonsByFront()
    {
        $this->ensureRequiredAddons();

        $query = sprintf(
            '
            select c.add_name
                    ,c.add_title
                    ,c.add_info
                    ,c.add_system
                    ,c.add_hasconfig
                    ,c.add_version
                    ,c.add_icon
                    ,c.add_installed
                    ,c.add_order
                    ,c.add_params
                    ,c.add_log
                    ,c.addonscol
                    ,c.flag_status
                    ,c.created_date
                    ,c.updated_date
                    ,c.created_ip
                    ,c.updated_ip
                    ,c.created_by
                    ,c.updated_by
                    ,c.add_xml
                    ,c.add_load_back
                    ,c.add_load_front
                    ,c.is_field
            from %s c
            where c.flag_status=1 
            and c.add_load_front=1
            ORDER BY c.add_order desc
            ',
            $this->table
        );

        return $this->wpdb->get_results($query);
    }

    public function getActiveAddonsNamesOnBack()
    {
        $this->ensureRequiredAddons();

        $query = sprintf(
            '
            select c.add_name
            from %s c
            where c.flag_status=1 
            and c.add_load_back=1
            ORDER BY c.add_order desc
            ',
            $this->table
        );

        $tmp_result = $this->wpdb->get_results($query);

        $result = array();
        foreach ( $tmp_result as $key => $value) {
            $result[] = $value->add_name;
        }

        return $result;
    }


    public function getAddonsNamesOnBackByForm($idform)
    {
        $this->ensureRequiredAddons();

        $query = $this->wpdb->prepare(
            sprintf(
            '
            select c.add_name
            from %s c
	    left join %s ad on ad.add_name = c.add_name
            where c.flag_status=1 and ad.fmb_id=%%d
            and c.add_load_back=1
            ORDER BY c.add_order desc
            ',
            $this->table,
                $this->tbaddon_details
            ),
            (int) $idform
        );

        $tmp_result = $this->wpdb->get_results($query);

        $result = array();
        foreach ( $tmp_result as $key => $value) {
            $result[] = $value->add_name;
        }

        return $result;
    }



    public function getListAddonsBySection($section = '')
    {
        $this->ensureRequiredAddons();

        $query = sprintf(
            '
            select c.add_name
                    ,c.add_title
                    ,c.add_info
                    ,c.add_system
                    ,c.add_hasconfig
                    ,c.add_version
                    ,c.add_icon
                    ,c.add_installed
                    ,c.add_order
                    ,c.add_params
                    ,c.add_log
                    ,c.addonscol
                    ,c.flag_status
                    ,c.created_date
                    ,c.updated_date
                    ,c.created_ip
                    ,c.updated_ip
                    ,c.created_by
                    ,c.updated_by
                    ,c.add_xml
                    ,c.add_load_back
                    ,c.add_load_front
                    ,c.is_field
            from %s c
            where c.flag_status=1 
            and c.add_section="%s"
            ORDER BY c.add_section_order desc
            ',
            $this->table,
            $section
        );

        return $this->wpdb->get_results($query);
    }


    /**
     * addonmodel::getListAddon()
     * List form estimator
     *
     * @param int $per_page max number of form estimators
     * @param int $segment  Number of pagination
     *
     * @return array
     */
    public function getListAddons($per_page = '', $segment = '')
    {
        $this->ensureRequiredAddons();

        $query = sprintf(
            '
            select  c.add_name, c.add_title, c.add_info, c.flag_status, c.add_params,
            extractvalue(c.add_xml, "/params/child::required_wp") as required_wp,
            extractvalue(c.add_xml, "/params/child::required_php") as required_php
            from %s c            
            where c.flag_status>=0
            ORDER BY c.created_date desc
            ',
            $this->table
        );

        if ( (int) $per_page > 0) {
            $segment = ( ! empty($segment) ) ? $segment : 0;
            $query  .= sprintf(' limit %s,%s', (int) $segment, (int) $per_page);
        }
        return $this->wpdb->get_results($query);
    }

    public function existAddon($addon_name)
    {
        $query = sprintf(
            'select 
                COUNT(*) as count
                from %s ad
                where ad.add_name ="%s" ',
            $this->table,
            $addon_name
        );
        $row   = $this->wpdb->get_row($query);
        if ( intval($row->count) > 0) {
            return 1;
        } else {
            return 0;
        }
    }
}
