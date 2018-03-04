<?php
if (!defined('ABSPATH')) {exit('No direct script access allowed');}
ob_start();
?>
  <?php include('formhtml_common_css.php');?>
<?php include('formhtml_common_css1.php');?>
<?php include('formhtml_addon_css.php');?>
<?php
$cntACmp = ob_get_contents();
 /* remove comments */
$cntACmp = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $cntACmp);
 /* remove tabs, spaces, newlines, etc. */
$cntACmp = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $cntACmp);
ob_end_clean();
echo $cntACmp;
?>