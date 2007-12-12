// -----------------------------------------------------------------------------------
//
//	modalbox v0.1b
//
//  by Arnaud Sellenet - http://demental.info/blog/
//  
//  barely inspired by lightbox.js v2.0.2
//	by Lokesh Dhakar - http://www.huddletogether.com
//	1/9/07
//
//
//	Licensed under the Creative Commons Attribution 2.5 License - http://creativecommons.org/licenses/by/2.5/
//	
// To avoid conflicting with additional functions which are the same as Lightbox.js, 
// you need to include lightbox.js too
//
// Changelog :
// 0.1 First release
// 0.1b (2007-02-08)
//    FIXED : consistency = the resizespeed CSS class is now lowercase just like blocksize
//    FIXED : If two modallinks reside on the same document with one using the default values for blocksize or resizespeed
//            and the other having custom blocksize and resizespeed, the first one would not use the default values anymore
//            after the second one being clicked. This is fixed now.
//    FIXED : renamed the ID of the loading image container to modalLoading, modal close button to bottomModalClose
//            and overlay to modalOverlay in order to avoid conflict with lightbox
//            So now you can have modalboxes and lightboxes in the same document
//    FIXED : loading image now shows up right after the box did, not after the Ajax query being sent.
//    FIXED : switch to 'get' method for sending ajax request
// 0.1c (2007-02-21)
//    FIXED : removed unused, uninitialized variables that caused JS errors in IE6 in some cases
// 0.1d (2007-04-16) :
//            now allows to call initModalbox several times (in case a modal link is created dynamically, after page load).
//            Don't forget to call initModalbox() again when you create a new modal link.
// -----------------------------------------------------------------------------------
/*

	Table of Contents
	-----------------
	Configuration
	Global Variables

	Extending Built-in Objects	
	- Object.extend(Element)
	- Array.prototype.removeDuplicates()
	- Array.prototype.empty()

	Modalbox Class Declaration
	- initialize()
	- start()
	- changeImage()
	- resizeImageContainer()
	- showImage()
	- updateDetails()
	- updateNav()
	- enableKeyboardNav()
	- disableKeyboardNav()
	- keyboardAction()
	- preloadNeighborImages()
	- end()
	
	Miscellaneous Functions
	- getPageScroll()
	- getPageSize()
	- getKey()
	- listenKey()
	- showSelectBoxes()
	- hideSelectBoxes()
	- pause()
	- initLightbox()
	
	Function Calls
	- addLoadEvent(initLightbox)
	
*/
// -----------------------------------------------------------------------------------

//
//	Configuration
//
var fileLoadingImage = "/static/images/indicator.gif";
var fileBottomModalCloseImage = "/sfLightboxPlugin/images/close.gif";

var resizeSpeed = 9;	// controls the speed of the image resizing (1=slowest and 10=fastest)

var borderSize = 10;	//if you adjust the padding in the CSS, you will need to update this variable

// -----------------------------------------------------------------------------------

//
//	Global Variables
//
var imageArray = new Array;
var activeImage;

if(resizeSpeed > 10){ resizeSpeed = 10;}
if(resizeSpeed < 1){ resizeSpeed = 1;}
resizeDuration = (11 - resizeSpeed) * 0.15;

// -----------------------------------------------------------------------------------

//
//	Additional methods for Element added by SU, Couloir
//	- further additions by Lokesh Dhakar (huddletogether.com)
//
Object.extend(Element, {
	getWidth: function(element) {
	   	element = $(element);
	   	return element.offsetWidth; 
	},
	setWidth: function(element,w) {
	   	element = $(element);
    	element.style.width = w +"px";
	},
	setHeight: function(element,h) {
   		element = $(element);
    	element.style.height = h +"px";
	},
	setTop: function(element,t) {
	   	element = $(element);
    	element.style.top = t +"px";
	},
	setSrc: function(element,src) {
    	element = $(element);
    	element.src = src; 
	},
	setHref: function(element,href) {
    	element = $(element);
    	element.href = href; 
	},
	setInnerHTML: function(element,content) {
		element = $(element);
		element.innerHTML = content;
	}
});

// -----------------------------------------------------------------------------------

//
//	Extending built-in Array object
//	- array.removeDuplicates()
//	- array.empty()
//
Array.prototype.removeDuplicates = function () {
	for(i = 1; i < this.length; i++){
		if(this[i][0] == this[i-1][0]){
			this.splice(i,1);
		}
	}
}

// -----------------------------------------------------------------------------------

Array.prototype.empty = function () {
	for(i = 0; i <= this.length; i++){
		this.shift();
	}
}

// -----------------------------------------------------------------------------------

//
//	Lightbox Class Declaration
//	- initialize()
//	- start()
//	- changeImage()
//	- resizeImageContainer()
//	- showImage()
//	- updateDetails()
//	- updateNav()
//	- enableKeyboardNav()
//	- disableKeyboardNav()
//	- keyboardNavAction()
//	- preloadNeighborImages()
//	- end()
//
//	Structuring of code inspired by Scott Upton (http://www.uptonic.com/)
//

var sizeX;
var sizeY;
var modalBlockUrl='';
var Modalbox = Class.create();

Modalbox.prototype = {
	
	// initialize()
	// Constructor runs on completion of the DOM loading. Loops through anchor tags looking for 
	// 'lightbox' references and applies onclick events to appropriate links. The 2nd section of
	// the function inserts html at the bottom of the page which is used to display the shadow 
	// overlay and the image container.
	//
	initialize: function() {	
		if (!document.getElementsByTagName){ return; }
		var anchors = document.getElementsByTagName('a');

		// loop through all anchor tags
		for (var i=0; i<anchors.length; i++){
			var anchor = anchors[i];
			
			var relAttribute = String(anchor.getAttribute('rel'));
			
			// use the string.match() method to catch 'lightbox' references in the rel attribute
			if (anchor.getAttribute('href') && (relAttribute.toLowerCase().match('modalbox'))){
				anchor.onclick = function () {myModalbox.start(this); return false;}
			}
		}
    var test=$('modalOverlay');
    if(test!=null) {
      return;
    }
		// The rest of this code inserts html at the bottom of the page that looks similar to this:
		//
		//	<div id="modalOverlay"></div>
		//	<div id="lightbox">
		//		<div id="outerImageContainer">
		//			<div id="imageContainer">
		//				<img id="lightboxImage">
		//				<div style="" id="hoverNav">
		//					<a href="#" id="prevLink"></a>
		//					<a href="#" id="nextLink"></a>
		//				</div>
		//				<div id="modalLoading">
		//					<a href="#" id="modalLoadingLink">
		//						<img src="images/loading.gif">
		//					</a>
		//				</div>
		//			</div>
		//		</div>
		//		<div id="imageDataContainer">
		//			<div id="imageData">
		//				<div id="imageDetails">
		//					<span id="caption"></span>
		//					<span id="numberDisplay"></span>
		//				</div>
		//				<div id="bottomModal">
		//					<a href="#" id="bottomModalClose">
		//						<img src="images/close.gif">
		//					</a>
		//				</div>
		//			</div>
		//		</div>
		//	</div>


		var objBody = document.getElementsByTagName("body").item(0);
		
		var objOverlay = document.createElement("div");
		objOverlay.setAttribute('id','modalOverlay');
		objOverlay.style.display = 'none';
		objOverlay.onclick = function() { myModalbox.end(); return false; }

		objBody.appendChild(objOverlay);
		
		var objModalbox = document.createElement("div");
		objModalbox.setAttribute('id','modalbox');
		objModalbox.style.display = 'none';
		objBody.appendChild(objModalbox);
	
		var objOuterBlockContainer = document.createElement("div");
		objOuterBlockContainer.setAttribute('id','outerBlockContainer');
		objModalbox.appendChild(objOuterBlockContainer);

		var objBlockContainer = document.createElement("div");
		objBlockContainer.setAttribute('id','blockContainer');
		objOuterBlockContainer.appendChild(objBlockContainer);
			
		var objLoading = document.createElement("div");
		objLoading.setAttribute('id','modalLoading');
		objOuterBlockContainer.appendChild(objLoading);
	
		var objLoadingLink = document.createElement("a");
		objLoadingLink.setAttribute('id','modalLoadingLink');
		objLoadingLink.setAttribute('href','#');
		objLoadingLink.onclick = function() { myModalbox.end(); return false; }
		objLoading.appendChild(objLoadingLink);
	
		var objLoadingImage = document.createElement("img");
		objLoadingImage.setAttribute('src', fileLoadingImage);
		objLoadingLink.appendChild(objLoadingImage);

		var objBlockDataContainer = document.createElement("div");
		objBlockDataContainer.setAttribute('id','blockDataContainer');
		objBlockDataContainer.className = 'clearfix';
		objModalbox.appendChild(objBlockDataContainer);

		var objBlockData = document.createElement("div");
		objBlockData.setAttribute('id','blockData');
		objBlockDataContainer.appendChild(objBlockData);
		
		
		
		var objBottomNav = document.createElement("div");
		objBottomNav.setAttribute('id','bottomModal');
		objBlockData.appendChild(objBottomNav);
	
		var objBottomNavCloseLink = document.createElement("a");
		objBottomNavCloseLink.setAttribute('id','bottomModalClose');
		objBottomNavCloseLink.setAttribute('href','#');
		objBottomNavCloseLink.onclick = function() { myModalbox.end(); return false; }
		objBottomNav.appendChild(objBottomNavCloseLink);
	
		var objBottomNavCloseImage = document.createElement("img");
		objBottomNavCloseImage.setAttribute('src', fileBottomNavCloseImage);
		objBottomNavCloseLink.appendChild(objBottomNavCloseImage);
	},
	
	//
	//	start()
	//	Display overlay and lightbox. If image is part of a set, add siblings to imageArray.
	//
	start: function(urlLink) {	
	  sizeX = 400;
    sizeY = 300;
    modalBlockUrl='';
    extractId='';
    resizeSpeed = 9;
    if(resizeSpeed > 10){ resizeSpeed = 10;}
    if(resizeSpeed < 1){ resizeSpeed = 1;}


		hideSelectBoxes();
    // See if we can find a specified width and height
    classes = urlLink.className.split(' ');
		for (j=0;j<classes.length;j++) {
			if (classes[j].indexOf("blocksize_") == 0) {
        expr = /blocksize_(\d+)x(\d+)/;
			  result = expr.exec(classes[j]);
				sizeX = parseInt(result[1]);
				sizeY = parseInt(result[2]);
			} else {
  			if (classes[j].indexOf("resizespeed_") == 0) {
          expr = /resizespeed_(\d+)/;
  			  result = expr.exec(classes[j]);
  				resizeSpeed = parseInt(result[1]);
			  }
			}
		}
    resizeDuration = (11 - resizeSpeed) * 0.15;

		// stretch overlay to fill page and fade in
		var arrayPageSize = getPageSize();
		Element.setHeight('modalOverlay', arrayPageSize[1]);
		new Effect.Appear('modalOverlay', { duration: 0.2, from: 0.0, to: 0.8, queue:'front' });	

		if (!document.getElementsByTagName){ return; }
		var anchors = document.getElementsByTagName('a');
    

		modalBlockUrl = urlLink.getAttribute('href');

		// calculate top offset for the lightbox and display 
		var arrayPageSize = getPageSize();
		var arrayPageScroll = getPageScroll();
		var modalboxTop = arrayPageScroll[1] + (arrayPageSize[3] / 15);

		Element.setTop('modalbox', modalboxTop);
		Element.show('modalbox');
		Element.show('modalLoading');

		this.displayBlock();
	},

	//
	//	changeImage()
	//	Hide most elements and preload image in preparation for resizing image container.
	//
	displayBlock: function() {
		
    myModalbox.resizeBlockContainer(sizeX, sizeY)
	},


		// hide elements during transition
		

	//
	//	resizeBlockContainer()
	//
	resizeBlockContainer: function( width, height) {
		Element.hide('blockDataContainer');
		// get current height and width
		this.wCur = Element.getWidth('outerBlockContainer');
		this.hCur = Element.getHeight('outerBlockContainer');

		// scalars based on change from old to new
		this.xScale = ((width  + (borderSize * 2)) / this.wCur) * 100;
		this.yScale = ((height  + (borderSize * 2)) / this.hCur) * 100;

		// calculate size difference between new and old image, and resize if necessary
		wDiff = (this.wCur - borderSize * 2) - width;
		hDiff = (this.hCur - borderSize * 2) - height;
		if(!( hDiff == 0)){ new Effect.Scale('outerBlockContainer', this.yScale, {scaleX: false, duration: resizeDuration,  scaleContent: false}); }
		if(!( wDiff == 0)){ new Effect.Scale('outerBlockContainer', this.xScale, {scaleY: false, delay: resizeDuration, duration: resizeDuration, scaleContent: false,afterFinish: function(){myModalbox.SendRequest();}}); }
		if((hDiff == 0) && (wDiff == 0)){
			if (navigator.appVersion.indexOf("MSIE")!=-1){ pause(250); } else { pause(100);} 
		}

		Element.setWidth( 'blockDataContainer', width + (borderSize * 2));

	},
  //
  // SendRequest()
  //
	SendRequest : function() {
		new Ajax.Updater('blockContainer', modalBlockUrl, { method:'get',asynchronous:true, evalScripts:true,
		                        onComplete:function(){
                          		myModalbox.showBlock();
		                        }
		                    }      
		                );
	},
	
	//
	//	showImage()
	//	Display image and begin preloading neighbors.
	//
	showBlock: function(){
		new Effect.Appear('blockDataContainer', { from:0,to: 1,duration: 0.2, queue: 'end'});
		Element.hide('modalLoading');

	},


	//
	//	end()
	//
	end: function() {
		Element.hide('modalbox');
		Element.hide('modalLoading');
		new Effect.Fade('modalOverlay', { duration: 0.2});
		showSelectBoxes();
	}
}
//
// getPageScroll()
// Returns array with x,y page scroll values.
// Core code from - quirksmode.org
//
function getPageScroll(){

	var yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}
  Element.setWidth('outerBlockContainer','400');
  $('blockContainer').innerHTML='';
	arrayPageScroll = new Array('',yScroll) 
	return arrayPageScroll;
}

// -----------------------------------------------------------------------------------

//
// getPageSize()
// Returns array with page width, height and window width, height
// Core code from - quirksmode.org
// Edit for Firefox by pHaez
//
function getPageSize(){
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}


	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}

// -----------------------------------------------------------------------------------

//
// getKey(key)
// Gets keycode. If 'x' is pressed then it hides the lightbox.
//
function getKey(e){
	if (e == null) { // ie
		keycode = event.keyCode;
	} else { // mozilla
		keycode = e.which;
	}
	key = String.fromCharCode(keycode).toLowerCase();
	
	if(key == 'x'){
	}
}

// -----------------------------------------------------------------------------------

//
// listenKey()
//
function listenKey () {	document.onkeypress = getKey; }
	
// ---------------------------------------------------

function showSelectBoxes(){
	selects = document.getElementsByTagName("select");
	for (i = 0; i != selects.length; i++) {
		selects[i].style.visibility = "visible";
	}
}

// ---------------------------------------------------

function hideSelectBoxes(){
	selects = document.getElementsByTagName("select");
	for (i = 0; i != selects.length; i++) {
		selects[i].style.visibility = "hidden";
	}
}

// ---------------------------------------------------

//
// pause(numberMillis)
// Pauses code execution for specified time. Uses busy code, not good.
// Code from http://www.faqts.com/knowledge_base/view.phtml/aid/1602
//
function pause(numberMillis) {
	var now = new Date();
	var exitTime = now.getTime() + numberMillis;
	while (true) {
		now = new Date();
		if (now.getTime() > exitTime)
			return;
	}
}
function initModalbox() { myModalbox = new Modalbox(); }
Event.observe(window, 'load', initModalbox, false);