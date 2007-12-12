/**
 * gwidgets.js
 *
 * Widgets based on Prototype JS library
 *
 * @copyright Gerald Estadieu (c) 2007 All Rights Reserved
 * @authors Gerald Estadieu <gestadieu@gmail.com>
 * @url http://gestadieu.free.fr/gwidgets/
 * @version 0.5
 * @license License: http://creativecommons.org/licenses/GPL/ 
 */
var gWidget = Base.extend({
	Version: '0.5',
	boxWaiting: new Template('<div class="gwidget-waiting"><img src="#{loadingImg}" alt="loading..." /> #{msgWaiting}</div>'),
	boxError: new Template('<div class="gwidget-error">#{msgError}</div>'),
	errorInline: 'The target element id does not seem to exist in this page...',
	linkRefresh: new Template('<a href="#{href}" id="#{id}" class="gwidget-refresh"><img src="#{refreshImg}" alt="Refresh" title="Refresh"/></a>'),
	classXHRLoaded: 'XHRLoaded',
	
	constructor: function() {
		this.options = Object.extend({
			msgError: 'Sorry, it seems there is a communication problem with our server, try later...',
			msgWaiting: 'Please wait while loading...',
			msgClose: 'close',
			pathImg: '/sfgWidgetsPlugin/images/',
			arrowLeft: 'gexpander-arrow-left.png',
			arrowBottom: 'gexpander-arrow-bottom.png',
			loadingImg: 'indicator.gif',
			refreshImg: 'refresh-icon.png',
			toggleEffect: 'appear',
			useRefresh: true,
			scroll: true,
			defaultWidth: '450',
			defaultHeight: '350',
			xMin: 100,
			yMin: 100,
			contentPadding: 20,
			eventOn: 'mouseover',
			eventOff: 'mouseout'	
		},(typeof(gWidget_Options)=='object')?gWidget_Options:{});
		if (!(typeof(Effect)=='object')) this.options.toggleEffect = '';
	},
	
	_XHRUpdater: function(url,content) {
		var xhr = new Ajax.Updater(
			{ success: content},
			url,
			{
				onFailure: function() { 
					$(content).update(this.boxError.evaluate({msgError:this.options.msgError}));
				}.bind(this),
				onLoading: function() { 
					$(content).update(this.boxWaiting.evaluate({loadingImg:this.options.pathImg+this.options.loadingImg,msgWaiting:this.options.msgWaiting}));
				}.bind(this),
				onComplete: function() {
					if (this.options.useRefresh) {
						new Insertion.Top(content,this.linkRefresh.evaluate({href:url,refreshImg:this.options.pathImg+this.options.refreshImg,id:content+'-refresh'})); 
						Event.observe(content + '-refresh','click',this._XHRRefresh.bind(this),false);
					}
					if (typeof(this.loaded) == 'function') { this.loaded(content); }
				}.bind(this)
			}
		);
	},
	
	_XHRRefresh: function(evt) {
		var elt = Event.element(evt); 
		$(elt).up('div').removeClassName(this.classXHRLoaded);		
		if (typeof(this.refresh) == 'function') { this.refresh(elt); }
		Event.stop(evt);
	},
	
	_getUrl: function(elt) {
		if (elt.readAttribute(this.widgetName)) {
			var tpelt = document.createElement('a');
			tpelt.setAttribute('href',elt.readAttribute(this.widgetName));
			elt = tpelt;
		}
		var params = (elt.search)?elt.search.substr(1).toQueryParams():'';
		var container = (eval('params.' + this.widgetName))?eval('params.' + this.widgetName):elt.hash.substr(1);
		var url = {
			url: elt.href, 
			baseUrl: elt.pathname, 
			container: container, 
			params: params,
			isXHR: (elt.href.split('#').first().split('?').first()!=window.location.href.split('#').first().split('?').first())?true:false
		};
		if (this.widgetName == 'gtip' && $(container)) { url.isXHR = false; }
		return url;
	},
	
	viewportDimensions: function(){
		var de = document.documentElement;
		var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
		var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
		return {xWidth: w, yHeight: h};		
	},
	
	pageDimensions: function(){
		if (window.innerHeight && window.scrollMaxY || window.innerWidth && window.scrollMaxX) {  
			yScroll = window.innerHeight + window.scrollMaxY;
		  xScroll = window.innerWidth + window.scrollMaxX;
		  var deff = document.documentElement;
		  var wff = (deff&&deff.clientWidth) || document.body.clientWidth || window.innerWidth || self.innerWidth;
		  var hff = (deff&&deff.clientHeight) || document.body.clientHeight || window.innerHeight || self.innerHeight;
		  xScroll -= (window.innerWidth - wff);
		  yScroll -= (window.innerHeight - hff);
		} else if (document.body.scrollHeight > document.body.offsetHeight || document.body.scrollWidth > document.body.offsetWidth){ // all but Explorer Mac
			yScroll = document.body.scrollHeight;
		  xScroll = document.body.scrollWidth;
		} else { 
			yScroll = document.body.offsetHeight;
		  xScroll = document.body.offsetWidth;
		}
		return {xWidth: xScroll, yHeight: yScroll};
	},
	
	scrollPosition: function(){
		var yScrolltop;
		var xScrollleft;
		if (self.pageYOffset || self.pageXOffset) {
			yScrolltop = self.pageYOffset;
			xScrollleft = self.pageXOffset;
		} else if (document.documentElement && document.documentElement.scrollTop || document.documentElement.scrollLeft ){	 // Explorer 6 Strict
			yScrolltop = document.documentElement.scrollTop;
			xScrollleft = document.documentElement.scrollLeft;
		} else if (document.body) {// all other Explorers
			yScrolltop = document.body.scrollTop;
			xScrollleft = document.body.scrollLeft;
		}
		return {xOffset: xScrollleft, yOffset: yScrolltop};
	}
});

/**
 * gTab 
 * 
 * @author Gerald Estadieu
 * @version 1.0 2007-02-20
 */
var gTab = gWidget.extend({
	widgetName: 'gtab',
	classHide: 'gtab-hide',
	classActive: 'gtab-active',
	classLoading: 'gtab-loading',
	
	constructor: function(elt) {
		this.elt = elt;
		this.eltController = this.elt + '-controller';
		this.base();
		this.menu = $(this.elt).down('ul').getElementsBySelector('li a'); 
		this.setup();
		this.menu.each(this.observer.bind(this));
		this.show(this.getInitialTab());
	},

	
	setup: function() {
		$(this.elt).down('ul').addClassName(this.widgetName + '-controllers');
		this.menu.each(function(elt){
			var url = this._getUrl(elt);
			$(elt).setAttribute('id',url.container + '-controller');
		}.bind(this));
	},
	
	observer: function(elt) {
		Event.observe(elt,'click',this.activate.bindAsEventListener(this),false);
		this.hide(elt);
	},
	
	activate: function(evt) {
		var elt = Event.findElement(evt, "a");
		elt.blur();
		this.show(elt);
		this.menu.without(elt).each(this.hide.bind(this));
		Event.stop(evt);
	},
	
	hide: function(elt) {
		var url = this._getUrl(elt);
		$(elt,url.container).invoke('addClassName',this.classHide); 
		$(elt,url.container).invoke('removeClassName',this.classActive); 
	},
	
	refresh: function(elt) {
		var elt = elt.up('div');
		var eltController = $(elt).readAttribute('id')+'-controller';
		this.show($($(elt).readAttribute('id')+'-controller'));
	},
	
	loaded: function(elt) {
		$($(elt).readAttribute('id')+'-controller').removeClassName(this.classLoading);
	},
	
	show: function(elt) {
		var url = this._getUrl(elt);
		$(elt,url.container).invoke('removeClassName',this.classHide);
		$(elt,url.container).invoke('addClassName',this.classActive );
	
		if (url.isXHR && !$(url.container).hasClassName(this.classXHRLoaded)) {
			$(elt).addClassName(this.classLoading);
			this._XHRUpdater(url.url,url.container);
			$(url.container).addClassName(this.classXHRLoaded);
		}
 	},
	
	getInitialTab: function() {
		if(document.location.href.match(/#(\w.+)/)) {
			var loc = RegExp.$1;
			var elt = this.menu.find(function(value) { return value.href.match(/#(\w.+)/)[1] == loc; });
			return elt || this.menu.first();
		} else {
			return this.menu.first();
		}
	}
});

/*
 * gExpander
 * Gerald Estadieu
 * version 1.0 2007-02-20
 */
var gExpander = gWidget.extend({
	widgetName: 'gexpander',
	imgController: new Template('<img src="#{pathImg}" class="gexpander-img" />'),
	containerHtml: new Template('<div id="#{id}" style="display:none;"></div>'),
	classController: 'gexpander-controller',
	
	constructor: function(elt) {
		this.base();
		if (elt) { this.observer($(elt)); }
		else { document.getElementsByClassName(this.widgetName).each(this.observer.bind(this));}
	},
	
	observer: function(elt) {
		var url = this._getUrl(elt);
		new Insertion.Top(elt,this.imgController.evaluate({pathImg: this.options.pathImg + this.options.arrowLeft}));
		Event.observe(elt,'click',this.activate.bindAsEventListener(this),false);
		if (url.container && !$(url.container)) {
			new Insertion.After(elt,this.containerHtml.evaluate({id:url.container}));
		}
	},
	
	activate: function(evt) {
		Event.stop(evt);
		var elt = Event.element(evt);
		elt.blur();
		this.show(elt);
		var img = $(elt).down('img.gexpander-img');
		var url = this._getUrl(elt);
		if (!$(url.container).visible() && img) {
			img.src = this.options.pathImg + this.options.arrowBottom; 
		}	else {
			img.src = this.options.pathImg + this.options.arrowLeft;
		}
		if (this.options.toggleEffect) {
			Effect.toggle($(url.container),this.options.toggleEffect,{duration:0.3});
		} else {
			var tp = (!$(url.container).visible())?$(url.container).show():$(url.container).hide();
		}
	},
	
	refresh: function(elt){
		this.show(elt.up('a.gwidget-refresh'));
	},
	
	show: function(elt) {
		var url = this._getUrl(elt);
		if (url.isXHR && !$(url.container).hasClassName(this.classXHRLoaded)) {
			this._XHRUpdater(url.url,url.container);
			$(url.container).addClassName(this.classXHRLoaded);
		}
	}
});

/**
 * gBox
 * Create a simple modal window with inline or ajax content
 *
 */
var gBox = gWidget.extend({
	widgetName: 'gbox',
	boxHtml: '<div id="gbox_loading" style="display:none"></div><iframe id="gbox_frame" style="display:none"></iframe><div id="gbox_overlay" style="display:none"></div><div id="gbox_window" style="display:none"><div id="gbox_window_title"></div><div id="gbox_window_content" class="gBox"></div></div>',
	imgHtml: new Template('<img src="#{url}" width="#{width}" height="#{height}" id="gbox_img" alt="#{alt}"/>'),
	closeHtml: new Template('<span id="gbox_close">#{close}</span>'),
	idOverlay: 'gbox_overlay',
	idWindow: 'gbox_window',
	idLoading: 'gbox_loading',
	idFrame: 'gbox_frame',
	idTitle: 'gbox_window_title',
	idContent: 'gbox_window_content',
	idClose: 'gbox_close',
	idImg: 'gbox_img',
	
	constructor: function(elt){		
		this.base();
		if (!$(this.idOverlay)) this.insertContainer();
		if (elt) { this.observer($(elt)); }
		else { document.getElementsByClassName(this.widgetName).each(this.observer.bind(this));	}
	},
	
	observer: function(elt){
		Event.observe(elt,'click',this.activate.bindAsEventListener(this),false);
		//if (this.options.scroll) { Event.observe(window,'scroll',this.resize.bindAsEventListener(this),false); }
		//if (this.options.resize) { Event.observe(window,'resize',this.resize.bindAsEventListener(this),false); }
	},

	insertContainer: function(){
		new Insertion.Bottom(document.getElementsByTagName('body')[0],this.boxHtml);
		this.hide();
		Event.observe(this.idOverlay,'click',this.hide.bindAsEventListener(this),false);
	},
	
	activate: function(evt){
		Event.stop(evt);
		var elt = Event.element(evt);
		elt = (elt.nodeName!='A')?elt.up('a.'+this.widgetName):elt;
		elt.blur();
		
		this.overlayBox();
		this.loadingBox();
		$(this.idOverlay,this.idLoading).invoke('show');
		
		var url = this._getUrl(elt);
		var imgType = /\.(jpe?g|gif|png)/gi;
		var contentTitle = elt.title || elt.name || elt.caption || '';			
		$(this.idTitle).update(this.closeHtml.evaluate({close: this.options.msgClose}) + contentTitle);
		Event.observe(this.idClose,'click',this.hide.bindAsEventListener(this),false);

		if (url.baseUrl.match(imgType)) { 
			this.showImg(elt);
		}	else {
			this.show(elt);
		}
	},
	
	refresh: function(elt){
		this.show($(elt).up('a.gwidget-refresh'));
	},
	
	hide: function(evt){
		$(this.idOverlay,this.idFrame,this.idWindow,this.idLoading).invoke('hide');
		if (this.inlineRef){
			document.body.appendChild($(this.inlineRef).hide());
			this.inlineRef = false;
		}
		$(this.idContent).update('');
	},
	
	show: function(elt) {
		var urlInfo = this._getUrl(elt);
		var xWidth = (urlInfo.params.width || this.options.defaultWidth);
		var yHeight = (urlInfo.params.height || this.options.defaultHeight);
		var minBox = this.resizeBox({xWidth: xWidth, yHeight: yHeight});
		if (urlInfo.isXHR) {
			this._XHRUpdater(urlInfo.url,this.idContent);
		} else if ($(urlInfo.container)) {
			this.inlineRef = urlInfo.container;
			$(this.idContent).update('').appendChild($(urlInfo.container).show());
		} else {
			$(this.idContent).update(this.boxError.evaluate({msgError:this.errorInline}));
		}
		$(this.idContent).setStyle({overflow: 'auto', 
			width: (Math.min(xWidth,minBox.xWidth)-this.options.contentPadding) + 'px', 
			height: (Math.min(yHeight,minBox.yHeight)-2*this.options.contentPadding-5) + 'px'});
		this.windowBox(minBox).show();
		$(this.idLoading).hide();
	},
	
	showImg: function(elt) { 
		var url = this._getUrl(elt);
		imgPreload = new Image();
    imgPreload.src = url.baseUrl;
		imgPreload.onload = function(){
			var imgDim = this.resizeBox({xWidth:imgPreload.width,yHeight:imgPreload.height});
			$(this.idContent).setStyle({width: imgDim.xWidth + 'px',height: imgDim.yHeight + 'px'});
			$(this.idContent).update(this.imgHtml.evaluate({url:url.baseUrl,width: imgDim.xWidth,height: imgDim.yHeight,alt:'title'}));
			$(this.idLoading).hide();
			this.windowBox({xWidth:imgDim.xWidth+this.options.contentPadding,yHeight:imgDim.yHeight+this.options.contentPadding*2}).show();
			imgPreload= null;
		}.bind(this);
	},
	
	overlayBox: function(){
		var page = this.pageDimensions();
		$(this.idOverlay).setStyle({ width: page.xWidth+'px', height: page.yHeight+'px' });
	},
	
	windowBox: function(dim){
		var view = this.viewportDimensions();
		var scroll = this.scrollPosition();
		$(this.idWindow).setStyle({
			width: (dim.xWidth) + 'px', 
			height: (dim.yHeight) + 'px', 
			left: ((view.xWidth - dim.xWidth)/2 + scroll.xOffset) + 'px' ,
			top: ((view.yHeight - dim.yHeight)/2 + scroll.yOffset) + 'px'
		});
		return $(this.idWindow);
	},
	
	loadingBox: function(){
		var view = this.viewportDimensions();
		var scroll = this.scrollPosition();
		$(this.idLoading).setStyle({
			left: ((view.xWidth - $(this.idLoading).getWidth())/2 + scroll.xOffset) + 'px',
			top: ((view.yHeight - $(this.idLoading).getHeight())/2 + scroll.yOffset) + 'px' 
		});
	},
	
	resizeBox: function(dim){
		var viewport = this.viewportDimensions();
		var x = parseInt(viewport.xWidth - this.options.xMin); var y = parseInt(viewport.yHeight - this.options.yMin);
		var imgWidth = parseInt(dim.xWidth); var imgHeight = parseInt(dim.yHeight);
		if (imgWidth > x) {
			imgHeight = imgHeight * (x/imgWidth);
			imgWidth = x;
			if (imgHeight>y){
				imgWidth = imgWidth * (y/imgHeight);
				imgHeight = y;
			}
		} else if (imgHeight>y) {
				imgWidth = imgWidth*(y/imgHeight);
				imgHeight = y;
				if (imgWidth>x){
					imgHeight = imgHeight*(x/imgWidth);
					imgWidth = x;
				}
		}
		return {xWidth: parseInt(imgWidth),yHeight: parseInt(imgHeight)};
	}
});

/*
 * gTip
 * Gerald Estadieu
 * version 1.0 2007-02-20
 */
var gTip = gWidget.extend({
	widgetName: 'gtip',
	containerHtml: '<div class="gtip-container" style="display:none;"><div class="gtip-title"></div><div class="gtip-content"></div><span class="gtip-arrow">&nbsp;</span></div>',
	
	constructor: function(elt) {
		this.base();
		this.options.useRefresh = false;
		this.options.defaultWidth = 200;
		this.options.autoReload = false;
		if (elt) { this.observer($(elt)); }
		else { document.getElementsByClassName(this.widgetName).each(this.observer.bind(this));}
	},
	
	observer: function(elt) {
		var url = this._getUrl(elt);
		Event.observe(elt,(url.params.eventOn || this.options.eventOn),this.activate.bindAsEventListener(this),false);
		Event.observe(elt,(url.params.eventOff || this.options.eventOff),this.hide.bindAsEventListener(this),false);
		this.setup(elt);
	},
	
	setup: function(elt) {
		new Insertion.After($(elt),this.containerHtml);
		var gtip = $(elt).next('div.gtip-container');
		$(gtip).down('div.gtip-title').update( elt.title || elt.name || elt.caption || '');
		var url = this._getUrl(elt);
		if ($(url.container)) $(gtip).down('div.gtip-content').appendChild($(url.container).show());
	},
	
	activate: function(evt) {
		Event.stop(evt);
		this.hide();
		var elt = Event.element(evt);
		this.show(elt);
	},
	
	show: function(elt) {
		var gtip = $(elt).next('div.gtip-container');
		var url  = this._getUrl(elt);
		var arrow = $(gtip).down('span.gtip-arrow');
		arrow.removeClassName('gtip-arrow-left','gtip-arrow-right');
		
		var viewport = this.viewportDimensions();
		var eltpos   = Position.cumulativeOffset($(elt));
		var xWidth = (url.params.width || this.options.defaultWidth)*1;
		if (viewport.xWidth>(eltpos[0]+$(elt).getWidth()+xWidth)){
			var left = eltpos[0] + $(elt).getWidth() + 15;
			$(arrow).addClassName('gtip-arrow-left');
			$(arrow).setStyle({left:'-10px'});
		} else {
			var left = eltpos[0] - (xWidth*1+15);
			$(arrow).addClassName('gtip-arrow-right');
			$(arrow).setStyle({left:xWidth+'px'});
		}
		$(gtip).setStyle({
			width:xWidth+'px',
			height:((url.params.height)?url.params.height:'')+'px',
			top:(eltpos[1]-5)+'px',
			left:left+'px'}).show();
			/*if (this.options.toggleEffect) {
				Effect.toggle($(gtip),this.options.toggleEffect,{duration:0.3});
			}	else { $(gtip).show(); }
			*/
		//setTimeout(this.hide.bind(this),10);
		if (url.isXHR && !$(elt).hasClassName(this.classXHRLoaded)) {
			this._XHRUpdater(url.url,$(gtip).down('div.gtip-content')); 
			$(elt).addClassName(this.classXHRLoaded);
		} 
	},
	
	hide: function(evt) {
		if (evt) {
			//console.log(evt,Event.element(evt));
			Event.stop(evt);
			var elt = Event.element(evt);
			//if (this.options.toggleEffect) { Effect.toggle($(elt).next('div.gtip-container'),this.options.toggleEffect,{duration:0.3});} else { 
			if ($(elt).next('div.gtip-container')) $(elt).next('div.gtip-container').hide(); //}
		} else {
			document.getElementsByClassName('gtip-container').invoke('hide');
		}
	},
	
	cancel: function(evt) {
		Event.stop(evt);
	}
});

function gWidget_Init(){
	if (typeof(gWidget_Options) == 'undefined' || typeof(gWidget_Options.declarative) == 'undefined') {
		new gBox();
		new gExpander();
		document.getElementsByClassName('gtab').each(function(elt){
			new gTab(elt.readAttribute('id'));
		});
		new gTip();
	}
}

Event.observe(window,'load',gWidget_Init,false);
