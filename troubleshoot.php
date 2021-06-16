<?php 
include('connect.php');
$agencies=pg_prepare("workplaces", 'SELECT govtagency_id, govtagency_name from govtagency');
$agencies=pg_execute("workplaces",array());
while($agency=pg_fetch_array($agencies)){
    echo $agency['govtagency_id'];
}
 ?>
