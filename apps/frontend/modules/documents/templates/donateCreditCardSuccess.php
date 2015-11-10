<?php
echo '<form method="post" action="' . sfConfig::get('app_donate_vads_url') . '">';
foreach ($params as $key => $value)
{
  echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
}
?>
  <input type="submit" name="pay" value="Pay" />
</form>
