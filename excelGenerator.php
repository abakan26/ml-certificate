<?php
error_reporting(1);
require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
require __DIR__ . "/core/Member.php";
require __DIR__ . "/core/FilterMembers.php";

header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . FilterMembers::FILE_NAME);

try {
    FilterMembers::getExcelFile();
    exit();
} catch (Exception $exception) {
    var_dump($exception->getMessage());
}


