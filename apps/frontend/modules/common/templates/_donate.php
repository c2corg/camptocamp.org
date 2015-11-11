<aside id="donate" style="display:none">

<span>La mise en place de la nouvelle version de c2c nécessite un financement supplémentaire de 110.000 € (<a href="http://www.pre-prod.dev.camptocamp.org/articles/526958/fr/nouvelle-version-v6-de-camptocamp-financement-participatif">explications</a>). Nous faisons donc appel à vous, à votre générosité, pour boucler le projet. Si chacun de vous donne 2 €, la levée de fonds sera bouclée en 2 semaine! Camptocamp.org, c'est votre site, une mine d'infos pour passionnés des cimes, entièrement gratuit, géré par des bénévoles (association). Vous trouvez Camptocamp utile ? Alors aidez nous à le faire vivre !</span>

<div>
  <img class="donate-image" />
  <span class="people"></span>, <span class="role"></span>
</div>
<div>
  <span class="presentation"></span>
</div>

<button class="donate-notnow">X</button>
<button class="donate-notnow">Me le rappeler plus tard</button>
<button class="donate-never">Non merci</button>
<button class="donate-change">Autre bannière</button>

<?php
use_helper('Link');
if (sfConfig::get('app_production') != 1)
{
    $url = '/donate';
}
else
{
    // on prod, force https
    $url = str_replace('http://', 'https://', url_for('@homepage', true)) . 'donate';
}
?>
<button onclick="location.href='<?php echo $url ?>?amount=10'">10€</button>
<button onclick="location.href='<?php echo $url ?>?amount=30'">30€</button>
<button onclick="location.href='<?php echo $url ?>?amount=50'">50€</button>
<button onclick="location.href='<?php echo $url ?>?amount=100'">100€</button>
<button onclick="location.href='<?php echo $url ?>?amount=500'">500€</button>
<button onclick="location.href='<?php echo $url ?>'">Autre montant</button>

</aside>
