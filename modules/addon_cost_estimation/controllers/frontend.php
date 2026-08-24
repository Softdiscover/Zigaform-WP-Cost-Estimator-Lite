<?php

if ( ! defined('ABSPATH')) {
    exit('No direct script access allowed');
}
if ( class_exists('zfaddn_cost_estimation_front')) {
    return;
}

class zfaddn_cost_estimation_front extends Uiform_Base_Module
{
    const VERSION = '0.1';

    public $local_controllers = array();
    public $local_actions = array();
    public $js_actions = array();

    protected function __construct()
    {
    }

    public function add_controllers()
    {
        return array();
    }

    public function register_hook_callbacks()
    {
    }

    public function init()
    {
    }

    public function activate($network_wide)
    {
        return true;
    }

    public function deactivate()
    {
        return true;
    }

    public function upgrade($db_version = 0)
    {
        return true;
    }

    protected function is_valid($property = 'all')
    {
        return true;
    }
}

