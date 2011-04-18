/* http://www.sencha.com/forum/showthread.php?125869-Menu-shadow-probolem-in-IE9&p=579336&viewfull=1#post579336 */
if ((typeof Range !== "undefined") && !Range.prototype.createContextualFragment)
{
	Range.prototype.createContextualFragment = function(html)
	{
		var frag = document.createDocumentFragment(), 
		div = document.createElement("div");
		frag.appendChild(div);
		div.outerHTML = html;
		return frag;
	};
}
