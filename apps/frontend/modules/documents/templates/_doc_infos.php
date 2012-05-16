<?php use_helper('Date');

$created_at = format_datetime($created_at);
$date = format_datetime(time());
$elapsed_time = round(1000 * $timer->getElapsedTime());

?>
<footer class="doc-infos<?php if (!$version) echo ' no_print'; ?>">
<?php
if ($version)
{
    echo __('Version #%1%, date %2%', array('%1%' => $version, '%2%' => $created_at));
}
?><span class="no_print"><?php
if ($version)
{
    echo ' - ';
}
echo __('Document generated %1% in %2%', array('%1%' => $date, '%2%' => $elapsed_time));
?></span>
</footer>
