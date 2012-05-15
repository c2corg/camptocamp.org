<?php use_helper('Date');

$date = format_datetime(time());
$elapsed_time = round(1000 * $timer->getElapsedTime());

?>
<footer class="doc-infos"><?php echo __('Document generated %1% in %2%', array('%1%' => $date, '%2%' => $elapsed_time)) ?></footer>
