<?php
//$inc=PROJECT_DIR."is".DS."functions".DS."get_new_docname.php";
//return include ($inc);
if ($date=='') {
    $date=$this->dates->F_date("", 1);
}
$month=substr($date, 3, 2);
$year=substr($date, 8, 2);
$lastday=$this->dates->days_in_month($month, $year);
$sql="select count(*) from documents where date>='01.$month.$year' and date<='$lastday.$month.$year';-- LD:$lastday, M:$month, Y:$year";
//$out.= "<br>$sql";
$cntr=$this->db->GetVal($sql);
if ($cntr>0) {
    $sql="select name from documents where date>='01.$month.$year' and date<='$lastday.$month.$year' order by name desc limit 1";
    //$out.= "<br>$sql";
    $name=$this->db->GetVal($sql); //01.01.2009
    $cntr=substr($name, 6)*1+1;//01-34-6789
} else {
    $cntr=1;
}
    //$cntr=2347;
    $name=$year."-".$month."-".sprintf("%04s", $cntr);
    return $name;
