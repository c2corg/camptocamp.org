/*
  Geoportal.js -- IGN France Geoportal Map Viewer Library

  Copyright 2007-2010 IGN France, released under the BSD license.
  Please see http://api.ign.fr/geoportail/api/doc/webmaster/license.html
  Please see http://api.ign.fr/geoportail/api/doc/fr/webmaster/licence.html
  for the full text of the license.

  The full source of Geoportal API can be downloaded there :
  http://api.ign.fr/geoportail/api/doc/fr/developpeur/download.html
 */
/*--------------------------------------------------------------------------*/
/*
  Contains rewritting of http://hexmen.com/blog/2007/03/printf-sprintf/

  This code is unrestricted: you are free to use it however you like.
 */
/*--------------------------------------------------------------------------*/
/*
  Contains portions of Sarissa -- http://dev.abiss.gr/sarissa
     
  Sarissa is an ECMAScript library acting as a cross-browser wrapper for native XML APIs.
  The library supports Gecko based browsers like Mozilla and Firefox,
  Internet Explorer (5.5+ with MSXML3.0+), Konqueror, Safari and Opera
  @version 0.9.9.4
  @author: Copyright 2004-2008 Emmanouil Batsis, mailto: mbatsis at users full stop sourceforge full stop net

  Sarissa is free software distributed under Apache Software License 2.0 or higher (see <a href="asl.txt">asl.txt</a>).

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY 
  KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
  WARRANTIES OF MERCHANTABILITY,FITNESS FOR A PARTICULAR PURPOSE 
  AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
  OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
  SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
  */
/*--------------------------------------------------------------------------*/
/*
  proj4js.js -- Javascript reprojection library.

  Authors:      Mike Adair madairATdmsolutions.ca
                Richard Greenwood richATgreenwoodmap.com
                Didier Richard didier.richardATign.fr
                Stephen Irons
  License:      LGPL as per: http://www.gnu.org/copyleft/lesser.html
                Note: This program is an almost direct port of the C library
                Proj4.
*/
/*--------------------------------------------------------------------------*/
/*
  Contains OpenLayers.js -- OpenLayers Map Viewer Library

  Copyright 2005-2007 MetaCarta, Inc., released under the BSD license.
  Please see http://svn.openlayers.org/trunk/openlayers/release-license.txt
  for the full text of the license.

  Includes compressed code under the following licenses:

  (For uncompressed versions of the code used please see the
  OpenLayers SVN repository: <http://openlayers.org/>)
*/
/*--------------------------------------------------------------------------*/
/* Contains portions of Prototype.js:
 *
 * Prototype JavaScript framework, version 1.4.0
 *  (c) 2005 Sam Stephenson <sam@conio.net>
 *
 *  Prototype is freely distributable under the terms of an MIT-style license.
 *  For details, see the Prototype web site: http://prototype.conio.net/
 *
/*--------------------------------------------------------------------------*/
/**  
*  
*  Contains portions of Rico <http://openrico.org/>
* 
*  Copyright 2005 Sabre Airline Solutions  
*  
*  Licensed under the Apache License, Version 2.0 (the "License"); you
*  may not use this file except in compliance with the License. You
*  may obtain a copy of the License at
*  
*         http://www.apache.org/licenses/LICENSE-2.0  
*  
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
*  implied. See the License for the specific language governing
*  permissions and limitations under the License. 
*
**/
/*--------------------------------------------------------------------------*/
/*
    Contains XMLHttpRequest of <http://code.google.com/p/xmlhttprequest>

Onderwerp: Re: XMLHttpRequest.js license
Van:       "Sergey Ilinsky" <sergey@ilinsky.com>
Datum:     Wo, 21 april, 2010 10:45 am
Aan:       bartvde@osgis.nl
--------------------------------------------------------------------------
I authorize OpenLayers to get the latest version of XMLHttpRequest.js
library and use it under Apache license (Just replace the license note in
the file). Is the written confirmation sufficient?

Sergey/

        http://www.apache.org/licenses/LICENSE-2.0
 */
/*--------------------------------------------------------------------------*/
/*! SWFObject v2.2 <http://code.google.com/p/swfobject/>
    is released under the MIT License
<http://www.opensource.org/licenses/mit-license.php>
*/
/*--------------------------------------------------------------------------*/
Proj4js={defaultDatum:"WGS84",transform:function(d,b,a){if(!d.readyToUse){this.reportError("Proj4js initialization for:"+d.srsCode+" not yet complete");
return a
}if(!b.readyToUse){this.reportError("Proj4js initialization for:"+b.srsCode+" not yet complete");
return a
}if((d.srsProjNumber=="900913"&&b.datumCode!="WGS84"&&!b.datum_params)||(b.srsProjNumber=="900913"&&d.datumCode!="WGS84"&&!d.datum_params)){var c=Proj4js.WGS84;
this.transform(d,c,a);
d=c
}if(d.axis!="enu"){this.adjust_axis(d,false,a)
}if(d.projName=="longlat"){a.x*=Proj4js.common.D2R;
a.y*=Proj4js.common.D2R
}else{if(d.to_meter){a.x*=d.to_meter;
a.y*=d.to_meter
}d.inverse(a)
}if(d.from_greenwich){a.x+=d.from_greenwich
}a=this.datum_transform(d.datum,b.datum,a);
if(b.from_greenwich){a.x-=b.from_greenwich
}if(b.projName=="longlat"){a.x*=Proj4js.common.R2D;
a.y*=Proj4js.common.R2D
}else{b.forward(a);
if(b.to_meter){a.x/=b.to_meter;
a.y/=b.to_meter
}}if(b.axis!="enu"){this.adjust_axis(b,true,a)
}return a
},datum_transform:function(c,b,a){if(c.compare_datums(b)){return a
}if(c.datum_type==Proj4js.common.PJD_NODATUM||b.datum_type==Proj4js.common.PJD_NODATUM){return a
}if(c.es!=b.es||c.a!=b.a||c.datum_type==Proj4js.common.PJD_3PARAM||c.datum_type==Proj4js.common.PJD_7PARAM||b.datum_type==Proj4js.common.PJD_3PARAM||b.datum_type==Proj4js.common.PJD_7PARAM){c.geodetic_to_geocentric(a);
if(c.datum_type==Proj4js.common.PJD_3PARAM||c.datum_type==Proj4js.common.PJD_7PARAM){c.geocentric_to_wgs84(a)
}if(b.datum_type==Proj4js.common.PJD_3PARAM||b.datum_type==Proj4js.common.PJD_7PARAM){b.geocentric_from_wgs84(a)
}b.geocentric_to_geodetic(a)
}return a
},adjust_axis:function(e,g,d){var c=d.x,j=d.y,a=d.z||0;
var f,h;
for(var b=0;
b<3;
b++){if(g&&b==2&&d.z===undefined){continue
}if(b==0){f=c;
h="x"
}else{if(b==1){f=j;
h="y"
}else{f=a;
h="z"
}}switch(e.axis[b]){case"e":d[h]=f;
break;
case"w":d[h]=-f;
break;
case"n":d[h]=f;
break;
case"s":d[h]=-f;
break;
case"u":if(d[h]!==undefined){d.z=f
}break;
case"d":if(d[h]!==undefined){d.z=-f
}break;
default:alert("ERROR: unknow axis ("+e.axis[b]+") - check definition of "+e.projName);
return null
}}return d
},reportError:function(a){},extend:function(a,d){a=a||{};
if(d){for(var c in d){var b=d[c];
if(b!==undefined){a[c]=b
}}}return a
},Class:function(){var b=function(){this.initialize.apply(this,arguments)
};
var a={};
var d;
for(var c=0;
c<arguments.length;
++c){if(typeof arguments[c]=="function"){d=arguments[c].prototype
}else{d=arguments[c]
}Proj4js.extend(a,d)
}b.prototype=a;
return b
},bind:function(c,b){var a=Array.prototype.slice.apply(arguments,[2]);
return function(){var d=a.concat(Array.prototype.slice.apply(arguments,[0]));
return c.apply(b,d)
}
},scriptName:"proj4js.js",defsLookupService:"http://spatialreference.org/ref",libPath:null,getScriptLocation:function(){if(this.libPath){return this.libPath
}var e=this.scriptName;
var d=e.length;
var a=document.getElementsByTagName("script");
for(var c=0;
c<a.length;
c++){var f=a[c].getAttribute("src");
if(f){var b=f.lastIndexOf(e);
if((b>-1)&&(b+d==f.length)){this.libPath=f.slice(0,-d);
break
}}}return this.libPath||""
},loadScript:function(d,e,c,a){var b=document.createElement("script");
b.defer=false;
b.type="text/javascript";
b.id=d;
b.src=d;
b.onload=e;
b.onerror=c;
b.loadCheck=a;
if(/MSIE/.test(navigator.userAgent)){b.onreadystatechange=this.checkReadyState
}document.getElementsByTagName("head")[0].appendChild(b)
},checkReadyState:function(){if(this.readyState=="loaded"){if(!this.loadCheck()){this.onerror()
}else{this.onload()
}}}};
Proj4js.Proj=Proj4js.Class({readyToUse:false,title:null,projName:null,units:null,datum:null,x0:0,y0:0,localCS:false,queue:null,initialize:function(c,d){this.srsCodeInput=c;
this.queue=[];
if(d){this.queue.push(d)
}if((c.indexOf("GEOGCS")>=0)||(c.indexOf("GEOCCS")>=0)||(c.indexOf("PROJCS")>=0)||(c.indexOf("LOCAL_CS")>=0)){this.parseWKT(c);
this.deriveConstants();
this.loadProjCode(this.projName);
return
}if(c.indexOf("urn:")==0){var a=c.split(":");
if((a[1]=="ogc"||a[1]=="x-ogc")&&(a[2]=="def")&&(a[3]=="crs")){c=a[4]+":"+a[a.length-1]
}}else{if(c.indexOf("http://")==0){var b=c.split("#");
if(b[0].match(/epsg.org/)){c="EPSG:"+b[1]
}else{if(b[0].match(/RIG.xml/)){c="IGNF:"+b[1]
}}}}this.srsCode=c.toUpperCase();
if(this.srsCode.indexOf("EPSG")==0){this.srsCode=this.srsCode;
this.srsAuth="epsg";
this.srsProjNumber=this.srsCode.substring(5)
}else{if(this.srsCode.indexOf("IGNF")==0){this.srsCode=this.srsCode;
this.srsAuth="IGNF";
this.srsProjNumber=this.srsCode.substring(5)
}else{if(this.srsCode.indexOf("CRS")==0){this.srsCode=this.srsCode;
this.srsAuth="CRS";
this.srsProjNumber=this.srsCode.substring(4)
}else{this.srsAuth="";
this.srsProjNumber=this.srsCode
}}}this.loadProjDefinition()
},loadProjDefinition:function(){if(Proj4js.defs[this.srsCode]){this.defsLoaded();
return
}var a=Proj4js.getScriptLocation()+"defs/"+this.srsAuth.toUpperCase()+this.srsProjNumber+".js";
Proj4js.loadScript(a,Proj4js.bind(this.defsLoaded,this),Proj4js.bind(this.loadFromService,this),Proj4js.bind(this.checkDefsLoaded,this))
},loadFromService:function(){var a=Proj4js.defsLookupService+"/"+this.srsAuth+"/"+this.srsProjNumber+"/proj4js/";
Proj4js.loadScript(a,Proj4js.bind(this.defsLoaded,this),Proj4js.bind(this.defsFailed,this),Proj4js.bind(this.checkDefsLoaded,this))
},defsLoaded:function(){this.parseDefs();
this.loadProjCode(this.projName)
},checkDefsLoaded:function(){if(Proj4js.defs[this.srsCode]){return true
}else{return false
}},defsFailed:function(){Proj4js.reportError("failed to load projection definition for: "+this.srsCode);
Proj4js.defs[this.srsCode]=Proj4js.defs.WGS84;
this.defsLoaded()
},loadProjCode:function(b){if(Proj4js.Proj[b]){this.initTransforms();
return
}var a=Proj4js.getScriptLocation()+"projCode/"+b+".js";
Proj4js.loadScript(a,Proj4js.bind(this.loadProjCodeSuccess,this,b),Proj4js.bind(this.loadProjCodeFailure,this,b),Proj4js.bind(this.checkCodeLoaded,this,b))
},loadProjCodeSuccess:function(a){if(Proj4js.Proj[a].dependsOn){this.loadProjCode(Proj4js.Proj[a].dependsOn)
}else{this.initTransforms()
}},loadProjCodeFailure:function(a){Proj4js.reportError("failed to find projection file for: "+a)
},checkCodeLoaded:function(a){if(Proj4js.Proj[a]){return true
}else{return false
}},initTransforms:function(){Proj4js.extend(this,Proj4js.Proj[this.projName]);
this.init();
this.readyToUse=true;
if(this.queue){var a;
while((a=this.queue.shift())){a.call(this,this)
}}},wktRE:/^(\w+)\[(.*)\]$/,parseWKT:function(k){var h=k.match(this.wktRE);
if(!h){return
}var m=h[1];
var l=h[2];
var g=l.split(",");
var o;
if(m.toUpperCase()=="TOWGS84"){o=m
}else{o=g.shift()
}o=o.replace(/^\"/,"");
o=o.replace(/\"$/,"");
var n=new Array();
var b=0;
var f="";
for(var e=0;
e<g.length;
++e){var c=g[e];
for(var d=0;
d<c.length;
++d){if(c.charAt(d)=="["){++b
}if(c.charAt(d)=="]"){--b
}}f+=c;
if(b===0){n.push(f);
f=""
}else{f+=","
}}switch(m){case"LOCAL_CS":this.projName="identity";
this.localCS=true;
this.srsCode=o;
break;
case"GEOGCS":this.projName="longlat";
this.geocsCode=o;
if(!this.srsCode){this.srsCode=o
}break;
case"PROJCS":this.srsCode=o;
break;
case"GEOCCS":break;
case"PROJECTION":this.projName=Proj4js.wktProjections[o];
break;
case"DATUM":this.datumName=o;
break;
case"LOCAL_DATUM":this.datumCode="none";
break;
case"SPHEROID":this.ellps=o;
this.a=parseFloat(n.shift());
this.rf=parseFloat(n.shift());
break;
case"PRIMEM":this.from_greenwich=parseFloat(n.shift());
break;
case"UNIT":this.units=o;
this.unitsPerMeter=parseFloat(n.shift());
break;
case"PARAMETER":var a=o.toLowerCase();
var p=parseFloat(n.shift());
switch(a){case"false_easting":this.x0=p;
break;
case"false_northing":this.y0=p;
break;
case"scale_factor":this.k0=p;
break;
case"central_meridian":this.long0=p*Proj4js.common.D2R;
break;
case"latitude_of_origin":this.lat0=p*Proj4js.common.D2R;
break;
case"more_here":break;
default:break
}break;
case"TOWGS84":this.datum_params=n;
break;
case"AXIS":var a=o.toLowerCase();
var p=n.shift();
switch(p){case"EAST":p="e";
break;
case"WEST":p="w";
break;
case"NORTH":p="n";
break;
case"SOUTH":p="s";
break;
case"UP":p="u";
break;
case"DOWN":p="d";
break;
case"OTHER":default:p=" ";
break
}if(!this.axis){this.axis="enu"
}switch(a){case"x":this.axis=p+this.axis.substr(1,2);
break;
case"y":this.axis=this.axis.substr(0,1)+p+this.axis.substr(2,1);
break;
case"z":this.axis=this.axis.substr(0,2)+p;
break;
default:break
}case"MORE_HERE":break;
default:break
}for(var e=0;
e<n.length;
++e){this.parseWKT(n[e])
}},parseDefs:function(){this.defData=Proj4js.defs[this.srsCode];
var e,b;
if(!this.defData){return
}var a=this.defData.split("+");
for(var f=0;
f<a.length;
f++){var d=a[f].split("=");
e=d[0].toLowerCase();
b=d[1];
switch(e.replace(/\s/gi,"")){case"":break;
case"title":this.title=b;
break;
case"proj":this.projName=b.replace(/\s/gi,"");
break;
case"units":this.units=b.replace(/\s/gi,"");
break;
case"datum":this.datumCode=b.replace(/\s/gi,"");
break;
case"nadgrids":this.nadgrids=b.replace(/\s/gi,"");
break;
case"ellps":this.ellps=b.replace(/\s/gi,"");
break;
case"a":this.a=parseFloat(b);
break;
case"b":this.b=parseFloat(b);
break;
case"rf":this.rf=parseFloat(b);
break;
case"lat_0":this.lat0=b*Proj4js.common.D2R;
break;
case"lat_1":this.lat1=b*Proj4js.common.D2R;
break;
case"lat_2":this.lat2=b*Proj4js.common.D2R;
break;
case"lat_ts":this.lat_ts=b*Proj4js.common.D2R;
break;
case"lon_0":this.long0=b*Proj4js.common.D2R;
break;
case"alpha":this.alpha=parseFloat(b)*Proj4js.common.D2R;
break;
case"lonc":this.longc=b*Proj4js.common.D2R;
break;
case"x_0":this.x0=parseFloat(b);
break;
case"y_0":this.y0=parseFloat(b);
break;
case"k_0":this.k0=parseFloat(b);
break;
case"k":this.k0=parseFloat(b);
break;
case"r_a":this.R_A=true;
break;
case"zone":this.zone=parseInt(b);
break;
case"south":this.utmSouth=true;
break;
case"towgs84":this.datum_params=b.split(",");
break;
case"to_meter":this.to_meter=parseFloat(b);
break;
case"from_greenwich":this.from_greenwich=b*Proj4js.common.D2R;
break;
case"pm":b=b.replace(/\s/gi,"");
this.from_greenwich=Proj4js.PrimeMeridian[b]?Proj4js.PrimeMeridian[b]:parseFloat(b);
this.from_greenwich*=Proj4js.common.D2R;
break;
case"axis":b=b.replace(/\s/gi,"");
var c="ewnsud";
if(b.length==3&&c.indexOf(b.substr(0,1))!=-1&&c.indexOf(b.substr(1,1))!=-1&&c.indexOf(b.substr(2,1))!=-1){this.axis=b
}break;
case"no_defs":break;
default:}}this.deriveConstants()
},deriveConstants:function(){if(this.nadgrids=="@null"){this.datumCode="none"
}if(this.datumCode&&this.datumCode!="none"){var a=Proj4js.Datum[this.datumCode];
if(a){this.datum_params=a.towgs84?a.towgs84.split(","):null;
this.ellps=a.ellipse;
this.datumName=a.datumName?a.datumName:this.datumCode
}}if(!this.a){var b=Proj4js.Ellipsoid[this.ellps]?Proj4js.Ellipsoid[this.ellps]:Proj4js.Ellipsoid.WGS84;
Proj4js.extend(this,b)
}if(this.rf&&!this.b){this.b=(1-1/this.rf)*this.a
}if(this.rf===0||Math.abs(this.a-this.b)<Proj4js.common.EPSLN){this.sphere=true;
this.b=this.a
}this.a2=this.a*this.a;
this.b2=this.b*this.b;
this.es=(this.a2-this.b2)/this.a2;
this.e=Math.sqrt(this.es);
if(this.R_A){this.a*=1-this.es*(Proj4js.common.SIXTH+this.es*(Proj4js.common.RA4+this.es*Proj4js.common.RA6));
this.a2=this.a*this.a;
this.b2=this.b*this.b;
this.es=0
}this.ep2=(this.a2-this.b2)/this.b2;
if(!this.k0){this.k0=1
}if(!this.axis){this.axis="enu"
}this.datum=new Proj4js.datum(this)
}});
Proj4js.Proj.longlat={init:function(){},forward:function(a){return a
},inverse:function(a){return a
}};
Proj4js.Proj.identity=Proj4js.Proj.longlat;
Proj4js.defs={WGS84:"+title=long/lat:WGS84 +proj=longlat +ellps=WGS84 +datum=WGS84 +units=degrees","EPSG:4326":"+title=long/lat:WGS84 +proj=longlat +a=6378137.0 +b=6356752.31424518 +ellps=WGS84 +datum=WGS84 +units=degrees","EPSG:4269":"+title=long/lat:NAD83 +proj=longlat +a=6378137.0 +b=6356752.31414036 +ellps=GRS80 +datum=NAD83 +units=degrees","EPSG:3857":"+title= Google Mercator +proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +no_defs"};
Proj4js.defs["EPSG:3785"]=Proj4js.defs["EPSG:3857"];
Proj4js.defs.GOOGLE=Proj4js.defs["EPSG:3857"];
Proj4js.defs["EPSG:900913"]=Proj4js.defs["EPSG:3857"];
Proj4js.defs["EPSG:102113"]=Proj4js.defs["EPSG:3857"];
Proj4js.common={PI:3.141592653589793,HALF_PI:1.5707963267948966,TWO_PI:6.283185307179586,FORTPI:0.7853981633974483,R2D:57.29577951308232,D2R:0.017453292519943295,SEC_TO_RAD:0.00000484813681109536,EPSLN:1e-10,MAX_ITER:20,COS_67P5:0.3826834323650898,AD_C:1.0026,PJD_UNKNOWN:0,PJD_3PARAM:1,PJD_7PARAM:2,PJD_GRIDSHIFT:3,PJD_WGS84:4,PJD_NODATUM:5,SRS_WGS84_SEMIMAJOR:6378137,SIXTH:0.16666666666666666,RA4:0.04722222222222222,RA6:0.022156084656084655,RV4:0.06944444444444445,RV6:0.04243827160493827,msfnz:function(c,b,d){var a=c*b;
return d/(Math.sqrt(1-a*a))
},tsfnz:function(e,d,c){var a=e*c;
var b=0.5*e;
a=Math.pow(((1-a)/(1+a)),b);
return(Math.tan(0.5*(this.HALF_PI-d))/a)
},phi2z:function(g,f){var e=0.5*g;
var a,b;
var d=this.HALF_PI-2*Math.atan(f);
for(var c=0;
c<=15;
c++){a=g*Math.sin(d);
b=this.HALF_PI-2*Math.atan(f*(Math.pow(((1-a)/(1+a)),e)))-d;
d+=b;
if(Math.abs(b)<=1e-10){return d
}}alert("phi2z has NoConvergence");
return(-9999)
},qsfnz:function(c,b){var a;
if(c>1e-7){a=c*b;
return((1-c*c)*(b/(1-a*a)-(0.5/c)*Math.log((1-a)/(1+a))))
}else{return(2*b)
}},asinz:function(a){if(Math.abs(a)>1){a=(a>1)?1:-1
}return Math.asin(a)
},e0fn:function(a){return(1-0.25*a*(1+a/16*(3+1.25*a)))
},e1fn:function(a){return(0.375*a*(1+0.25*a*(1+0.46875*a)))
},e2fn:function(a){return(0.05859375*a*a*(1+0.75*a))
},e3fn:function(a){return(a*a*a*(35/3072))
},mlfn:function(e,d,c,b,a){return(e*a-d*Math.sin(2*a)+c*Math.sin(4*a)-b*Math.sin(6*a))
},srat:function(a,b){return(Math.pow((1-a)/(1+a),b))
},sign:function(a){if(a<0){return(-1)
}else{return(1)
}},adjust_lon:function(a){a=(Math.abs(a)<this.PI)?a:(a-(this.sign(a)*this.TWO_PI));
return a
},adjust_lat:function(a){a=(Math.abs(a)<this.HALF_PI)?a:(a-(this.sign(a)*this.PI));
return a
},latiso:function(d,c,b){if(Math.abs(c)>this.HALF_PI){return +Number.NaN
}if(c==this.HALF_PI){return Number.POSITIVE_INFINITY
}if(c==-1*this.HALF_PI){return -1*Number.POSITIVE_INFINITY
}var a=d*b;
return Math.log(Math.tan((this.HALF_PI+c)/2))+d*Math.log((1-a)/(1+a))/2
},fL:function(b,a){return 2*Math.atan(b*Math.exp(a))-this.HALF_PI
},invlatiso:function(e,c){var b=this.fL(1,c);
var d=0;
var a=0;
do{d=b;
a=e*Math.sin(d);
b=this.fL(Math.exp(e*Math.log((1+a)/(1-a))/2),c)
}while(Math.abs(b-d)>1e-12);
return b
},sinh:function(a){var b=Math.exp(a);
b=(b-1/b)/2;
return b
},cosh:function(a){var b=Math.exp(a);
b=(b+1/b)/2;
return b
},tanh:function(a){var b=Math.exp(a);
b=(b-1/b)/(b+1/b);
return b
},asinh:function(a){var b=(a>=0?1:-1);
return b*(Math.log(Math.abs(a)+Math.sqrt(a*a+1)))
},acosh:function(a){return 2*Math.log(Math.sqrt((a+1)/2)+Math.sqrt((a-1)/2))
},atanh:function(a){return Math.log((a-1)/(a+1))/2
},gN:function(b,f,d){var c=f*d;
return b/Math.sqrt(1-c*c)
}};
Proj4js.datum=Proj4js.Class({initialize:function(b){this.datum_type=Proj4js.common.PJD_WGS84;
if(b.datumCode&&b.datumCode=="none"){this.datum_type=Proj4js.common.PJD_NODATUM
}if(b&&b.datum_params){for(var a=0;
a<b.datum_params.length;
a++){b.datum_params[a]=parseFloat(b.datum_params[a])
}if(b.datum_params[0]!=0||b.datum_params[1]!=0||b.datum_params[2]!=0){this.datum_type=Proj4js.common.PJD_3PARAM
}if(b.datum_params.length>3){if(b.datum_params[3]!=0||b.datum_params[4]!=0||b.datum_params[5]!=0||b.datum_params[6]!=0){this.datum_type=Proj4js.common.PJD_7PARAM;
b.datum_params[3]*=Proj4js.common.SEC_TO_RAD;
b.datum_params[4]*=Proj4js.common.SEC_TO_RAD;
b.datum_params[5]*=Proj4js.common.SEC_TO_RAD;
b.datum_params[6]=(b.datum_params[6]/1000000)+1
}}}if(b){this.a=b.a;
this.b=b.b;
this.es=b.es;
this.ep2=b.ep2;
this.datum_params=b.datum_params
}},compare_datums:function(a){if(this.datum_type!=a.datum_type){return false
}else{if(this.a!=a.a||Math.abs(this.es-a.es)>5e-11){return false
}else{if(this.datum_type==Proj4js.common.PJD_3PARAM){return(this.datum_params[0]==a.datum_params[0]&&this.datum_params[1]==a.datum_params[1]&&this.datum_params[2]==a.datum_params[2])
}else{if(this.datum_type==Proj4js.common.PJD_7PARAM){return(this.datum_params[0]==a.datum_params[0]&&this.datum_params[1]==a.datum_params[1]&&this.datum_params[2]==a.datum_params[2]&&this.datum_params[3]==a.datum_params[3]&&this.datum_params[4]==a.datum_params[4]&&this.datum_params[5]==a.datum_params[5]&&this.datum_params[6]==a.datum_params[6])
}else{if(this.datum_type==Proj4js.common.PJD_GRIDSHIFT||a.datum_type==Proj4js.common.PJD_GRIDSHIFT){alert("ERROR: Grid shift transformations are not implemented.");
return false
}else{return true
}}}}}},geodetic_to_geocentric:function(c){var l=c.x;
var h=c.y;
var d=c.z?c.z:0;
var e;
var b;
var a;
var j=0;
var k;
var i;
var g;
var f;
if(h<-Proj4js.common.HALF_PI&&h>-1.001*Proj4js.common.HALF_PI){h=-Proj4js.common.HALF_PI
}else{if(h>Proj4js.common.HALF_PI&&h<1.001*Proj4js.common.HALF_PI){h=Proj4js.common.HALF_PI
}else{if((h<-Proj4js.common.HALF_PI)||(h>Proj4js.common.HALF_PI)){Proj4js.reportError("geocent:lat out of range:"+h);
return null
}}}if(l>Proj4js.common.PI){l-=(2*Proj4js.common.PI)
}i=Math.sin(h);
f=Math.cos(h);
g=i*i;
k=this.a/(Math.sqrt(1-this.es*g));
e=(k+d)*f*Math.cos(l);
b=(k+d)*f*Math.sin(l);
a=((k*(1-this.es))+d)*i;
c.x=e;
c.y=b;
c.z=a;
return j
},geocentric_to_geodetic:function(t){var y=1e-12;
var u=(y*y);
var f=30;
var l;
var h;
var a;
var n;
var b;
var m;
var k;
var x;
var w;
var j;
var r;
var q;
var e;
var v;
var g=t.x;
var d=t.y;
var c=t.z?t.z:0;
var i;
var s;
var o;
e=false;
l=Math.sqrt(g*g+d*d);
h=Math.sqrt(g*g+d*d+c*c);
if(l/this.a<y){e=true;
i=0;
if(h/this.a<y){s=Proj4js.common.HALF_PI;
o=-this.b;
return
}}else{i=Math.atan2(d,g)
}a=c/h;
n=l/h;
b=1/Math.sqrt(1-this.es*(2-this.es)*n*n);
x=n*(1-this.es)*b;
w=a*b;
v=0;
do{v++;
k=this.a/Math.sqrt(1-this.es*w*w);
o=l*x+c*w-k*(1-this.es*w*w);
m=this.es*k/(k+o);
b=1/Math.sqrt(1-m*(2-m)*n*n);
j=n*(1-m)*b;
r=a*b;
q=r*x-j*w;
x=j;
w=r
}while(q*q>u&&v<f);
s=Math.atan(r/Math.abs(j));
t.x=i;
t.y=s;
t.z=o;
return t
},geocentric_to_geodetic_noniter:function(s){var d=s.x;
var c=s.y;
var a=s.z?s.z:0;
var g;
var r;
var l;
var e;
var n;
var q;
var m;
var j;
var h;
var i;
var v;
var f;
var u;
var t;
var o;
var k;
var b;
d=parseFloat(d);
c=parseFloat(c);
a=parseFloat(a);
b=false;
if(d!=0){g=Math.atan2(c,d)
}else{if(c>0){g=Proj4js.common.HALF_PI
}else{if(c<0){g=-Proj4js.common.HALF_PI
}else{b=true;
g=0;
if(a>0){r=Proj4js.common.HALF_PI
}else{if(a<0){r=-Proj4js.common.HALF_PI
}else{r=Proj4js.common.HALF_PI;
l=-this.b;
return
}}}}}n=d*d+c*c;
e=Math.sqrt(n);
q=a*Proj4js.common.AD_C;
j=Math.sqrt(q*q+n);
i=q/j;
f=e/j;
v=i*i*i;
m=a+this.b*this.ep2*v;
k=e-this.a*this.es*f*f*f;
h=Math.sqrt(m*m+k*k);
u=m/h;
t=k/h;
o=this.a/Math.sqrt(1-this.es*u*u);
if(t>=Proj4js.common.COS_67P5){l=e/t-o
}else{if(t<=-Proj4js.common.COS_67P5){l=e/-t-o
}else{l=a/u+o*(this.es-1)
}}if(b==false){r=Math.atan(u/t)
}s.x=g;
s.y=r;
s.z=l;
return s
},geocentric_to_wgs84:function(b){if(this.datum_type==Proj4js.common.PJD_3PARAM){b.x+=this.datum_params[0];
b.y+=this.datum_params[1];
b.z+=this.datum_params[2]
}else{if(this.datum_type==Proj4js.common.PJD_7PARAM){var f=this.datum_params[0];
var d=this.datum_params[1];
var i=this.datum_params[2];
var e=this.datum_params[3];
var j=this.datum_params[4];
var h=this.datum_params[5];
var g=this.datum_params[6];
var c=g*(b.x-h*b.y+j*b.z)+f;
var a=g*(h*b.x+b.y-e*b.z)+d;
var k=g*(-j*b.x+e*b.y+b.z)+i;
b.x=c;
b.y=a;
b.z=k
}}},geocentric_from_wgs84:function(c){if(this.datum_type==Proj4js.common.PJD_3PARAM){c.x-=this.datum_params[0];
c.y-=this.datum_params[1];
c.z-=this.datum_params[2]
}else{if(this.datum_type==Proj4js.common.PJD_7PARAM){var g=this.datum_params[0];
var d=this.datum_params[1];
var j=this.datum_params[2];
var f=this.datum_params[3];
var k=this.datum_params[4];
var i=this.datum_params[5];
var h=this.datum_params[6];
var e=(c.x-g)/h;
var b=(c.y-d)/h;
var a=(c.z-j)/h;
c.x=e+i*b-k*a;
c.y=-i*e+b+f*a;
c.z=k*e-f*b+a
}}}});
Proj4js.Point=Proj4js.Class({initialize:function(a,d,c){if(typeof a=="object"){this.x=a[0];
this.y=a[1];
this.z=a[2]||0
}else{if(typeof a=="string"&&typeof d=="undefined"){var b=a.split(",");
this.x=parseFloat(b[0]);
this.y=parseFloat(b[1]);
this.z=parseFloat(b[2])||0
}else{this.x=a;
this.y=d;
this.z=c||0
}}},clone:function(){return new Proj4js.Point(this.x,this.y,this.z)
},toString:function(){return("x="+this.x+",y="+this.y)
},toShortString:function(){return(this.x+", "+this.y)
}});
Proj4js.PrimeMeridian={greenwich:0,lisbon:-9.131906111111,paris:2.337229166667,bogota:-74.080916666667,madrid:-3.687938888889,rome:12.452333333333,bern:7.439583333333,jakarta:106.807719444444,ferro:-17.666666666667,brussels:4.367975,stockholm:18.058277777778,athens:23.7163375,oslo:10.722916666667};
Proj4js.Ellipsoid={MERIT:{a:6378137,rf:298.257,ellipseName:"MERIT 1983"},SGS85:{a:6378136,rf:298.257,ellipseName:"Soviet Geodetic System 85"},GRS80:{a:6378137,rf:298.257222101,ellipseName:"GRS 1980(IUGG, 1980)"},IAU76:{a:6378140,rf:298.257,ellipseName:"IAU 1976"},airy:{a:6377563.396,b:6356256.91,ellipseName:"Airy 1830"},"APL4.":{a:6378137,rf:298.25,ellipseName:"Appl. Physics. 1965"},NWL9D:{a:6378145,rf:298.25,ellipseName:"Naval Weapons Lab., 1965"},mod_airy:{a:6377340.189,b:6356034.446,ellipseName:"Modified Airy"},andrae:{a:6377104.43,rf:300,ellipseName:"Andrae 1876 (Den., Iclnd.)"},aust_SA:{a:6378160,rf:298.25,ellipseName:"Australian Natl & S. Amer. 1969"},GRS67:{a:6378160,rf:298.247167427,ellipseName:"GRS 67(IUGG 1967)"},bessel:{a:6377397.155,rf:299.1528128,ellipseName:"Bessel 1841"},bess_nam:{a:6377483.865,rf:299.1528128,ellipseName:"Bessel 1841 (Namibia)"},clrk66:{a:6378206.4,b:6356583.8,ellipseName:"Clarke 1866"},clrk80:{a:6378249.145,rf:293.4663,ellipseName:"Clarke 1880 mod."},CPM:{a:6375738.7,rf:334.29,ellipseName:"Comm. des Poids et Mesures 1799"},delmbr:{a:6376428,rf:311.5,ellipseName:"Delambre 1810 (Belgium)"},engelis:{a:6378136.05,rf:298.2566,ellipseName:"Engelis 1985"},evrst30:{a:6377276.345,rf:300.8017,ellipseName:"Everest 1830"},evrst48:{a:6377304.063,rf:300.8017,ellipseName:"Everest 1948"},evrst56:{a:6377301.243,rf:300.8017,ellipseName:"Everest 1956"},evrst69:{a:6377295.664,rf:300.8017,ellipseName:"Everest 1969"},evrstSS:{a:6377298.556,rf:300.8017,ellipseName:"Everest (Sabah & Sarawak)"},fschr60:{a:6378166,rf:298.3,ellipseName:"Fischer (Mercury Datum) 1960"},fschr60m:{a:6378155,rf:298.3,ellipseName:"Fischer 1960"},fschr68:{a:6378150,rf:298.3,ellipseName:"Fischer 1968"},helmert:{a:6378200,rf:298.3,ellipseName:"Helmert 1906"},hough:{a:6378270,rf:297,ellipseName:"Hough"},intl:{a:6378388,rf:297,ellipseName:"International 1909 (Hayford)"},kaula:{a:6378163,rf:298.24,ellipseName:"Kaula 1961"},lerch:{a:6378139,rf:298.257,ellipseName:"Lerch 1979"},mprts:{a:6397300,rf:191,ellipseName:"Maupertius 1738"},new_intl:{a:6378157.5,b:6356772.2,ellipseName:"New International 1967"},plessis:{a:6376523,rf:6355863,ellipseName:"Plessis 1817 (France)"},krass:{a:6378245,rf:298.3,ellipseName:"Krassovsky, 1942"},SEasia:{a:6378155,b:6356773.3205,ellipseName:"Southeast Asia"},walbeck:{a:6376896,b:6355834.8467,ellipseName:"Walbeck"},WGS60:{a:6378165,rf:298.3,ellipseName:"WGS 60"},WGS66:{a:6378145,rf:298.25,ellipseName:"WGS 66"},WGS72:{a:6378135,rf:298.26,ellipseName:"WGS 72"},WGS84:{a:6378137,rf:298.257223563,ellipseName:"WGS 84"},sphere:{a:6370997,b:6370997,ellipseName:"Normal Sphere (r=6370997)"}};
Proj4js.Datum={WGS84:{towgs84:"0,0,0",ellipse:"WGS84",datumName:"WGS84"},GGRS87:{towgs84:"-199.87,74.79,246.62",ellipse:"GRS80",datumName:"Greek_Geodetic_Reference_System_1987"},NAD83:{towgs84:"0,0,0",ellipse:"GRS80",datumName:"North_American_Datum_1983"},NAD27:{nadgrids:"@conus,@alaska,@ntv2_0.gsb,@ntv1_can.dat",ellipse:"clrk66",datumName:"North_American_Datum_1927"},potsdam:{towgs84:"606.0,23.0,413.0",ellipse:"bessel",datumName:"Potsdam Rauenberg 1950 DHDN"},carthage:{towgs84:"-263.0,6.0,431.0",ellipse:"clark80",datumName:"Carthage 1934 Tunisia"},hermannskogel:{towgs84:"653.0,-212.0,449.0",ellipse:"bessel",datumName:"Hermannskogel"},ire65:{towgs84:"482.530,-130.596,564.557,-1.042,-0.214,-0.631,8.15",ellipse:"mod_airy",datumName:"Ireland 1965"},nzgd49:{towgs84:"59.47,-5.04,187.44,0.47,-0.1,1.024,-4.5993",ellipse:"intl",datumName:"New Zealand Geodetic Datum 1949"},OSGB36:{towgs84:"446.448,-125.157,542.060,0.1502,0.2470,0.8421,-20.4894",ellipse:"airy",datumName:"Airy 1830"}};
Proj4js.WGS84=new Proj4js.Proj("WGS84");
Proj4js.Datum.OSB36=Proj4js.Datum.OSGB36;
Proj4js.wktProjections={"Lambert Tangential Conformal Conic Projection":"lcc",Mercator:"merc","Popular Visualisation Pseudo Mercator":"merc",Mercator_1SP:"merc",Transverse_Mercator:"tmerc","Transverse Mercator":"tmerc","Lambert Azimuthal Equal Area":"laea","Universal Transverse Mercator System":"utm"};
Proj4js.ProxyHost="";
Proj4js.ProxyHostFQDN=null;
Proj4js.getFQDNForUrl=function(a){if(a){var b=a.match(/^[a-z]+:\/\/([^\/]+)\/?/i);
if(b){return b[1]
}return window.location.host
}return null
};
Proj4js.setProxyUrl=function(a){Proj4js.ProxyHost=a;
Proj4js.ProxyHostFQDN=Proj4js.getFQDNForUrl(a)
};
Proj4js.Try=function(){var d=null;
for(var c=0,a=arguments.length;
c<a;
c++){var b=arguments[c];
try{d=b();
break
}catch(f){}}return d
};
Proj4js.loadScript=function(b,d,g,i){var e={loaded:false,onload:d,onfail:g,loadCheck:i,transport:Proj4js.Try(function(){return new XMLHttpRequest()
},function(){return new ActiveXObject("Msxml2.XMLHTTP")
},function(){return new ActiveXObject("Microsoft.XMLHTTP")
})||null};
if(!e.transport){return
}if(e.transport.overrideMimeType){e.transport.overrideMimeType("text/xml")
}var f="_tick_="+new Date().getTime();
b+=(b.indexOf("?")+1?"&":"?")+f;
if(Proj4js.ProxyHost){if(b.indexOf(Proj4js.ProxyHost)!=0){if(b.search(/^[a-z]+:\/\//i)!=-1){var h=b.match(/^[a-z]+:\/\/([^\/]*)\/?/i);
if(h){h=h[1]
}if(Proj4js.ProxyHostFQDN!=h){b=Proj4js.ProxyHost+encodeURIComponent(b)
}}}}e.transport.open("GET",b,false);
e.transport.onreadystatechange=Proj4js.bind(Proj4js.onStateChange,Proj4js,e);
var c={"X-Requested-With":"XMLHttpRequest",Accept:"text/javascript, text/html, application/xml, text/xml, */*",Proj4js:true};
for(var a in c){e.transport.setRequestHeader(a,c[a])
}e.transport.send(null);
if(e.transport.overrideMimeType){Proj4js.onStateChange(e)
}};
Proj4js.onStateChange=function(request){if(request.transport.readyState>1&&!(request.transport.readyState==4&&request.loaded)){var state=0;
try{state=request.transport.status||0
}catch(e){state=0
}var success=state==0||(state>=200&&state<300);
if(request.transport.readyState==4){request.loaded=true;
if(success){eval(request.transport.responseText);
if(request.loadCheck&&!request.loadCheck()){if(request.onfail){request.onfail()
}}else{if(request.onload){request.onload()
}}}else{if(request.onfail){request.onfail()
}}request.transport.onreadystatechange=function(){}
}}};
Proj4js.checkReadyState=function(){};
Proj4js.defs["IGNF:STPL69GEO"]="+title=Saint-Paul 1969 +proj=longlat +towgs84=225.571,-346.608,-46.567,0,0,0,0 +a=6378388.0000 +rf=297.0000000000000 +units=m +no_defs";
Proj4js.Proj.mill={init:function(){},forward:function(d){var e=d.x;
var c=d.y;
var b=Proj4js.common.adjust_lon(e-this.long0);
var a=this.x0+this.a*b;
var f=this.y0+this.a*Math.log(Math.tan((Proj4js.common.PI/4)+(c/2.5)))*1.25;
d.x=a;
d.y=f;
return d
},inverse:function(b){b.x-=this.x0;
b.y-=this.y0;
var c=Proj4js.common.adjust_lon(this.long0+b.x/this.a);
var a=2.5*(Math.atan(Math.exp(0.8*b.y/this.a))-Proj4js.common.PI/4);
b.x=c;
b.y=a;
return b
}};
Proj4js.defs["EPSG:310642901"]="+title=Geoportail - Monde +proj=mill +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lon_0=0.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:WGS84G"]="+title=World Geodetic System 1984 +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.Proj.lcc={init:function(){if(!this.lat2){this.lat2=this.lat0
}if(!this.k0){this.k0=1
}if(Math.abs(this.lat1+this.lat2)<Proj4js.common.EPSLN){Proj4js.reportError("lcc:init: Equal Latitudes");
return
}var j=this.b/this.a;
this.e=Math.sqrt(1-j*j);
var g=Math.sin(this.lat1);
var e=Math.cos(this.lat1);
var i=Proj4js.common.msfnz(this.e,g,e);
var b=Proj4js.common.tsfnz(this.e,this.lat1,g);
var f=Math.sin(this.lat2);
var d=Math.cos(this.lat2);
var h=Proj4js.common.msfnz(this.e,f,d);
var a=Proj4js.common.tsfnz(this.e,this.lat2,f);
var c=Proj4js.common.tsfnz(this.e,this.lat0,Math.sin(this.lat0));
if(Math.abs(this.lat1-this.lat2)>Proj4js.common.EPSLN){this.ns=Math.log(i/h)/Math.log(b/a)
}else{this.ns=g
}this.f0=i/(this.ns*Math.pow(b,this.ns));
this.rh=this.a*this.f0*Math.pow(c,this.ns);
if(!this.title){this.title="Lambert Conformal Conic"
}},forward:function(e){var f=e.x;
var d=e.y;
if(Math.abs(2*Math.abs(d)-Proj4js.common.PI)<=Proj4js.common.EPSLN){d=Proj4js.common.sign(d)*(Proj4js.common.HALF_PI-2*Proj4js.common.EPSLN)
}var a=Math.abs(Math.abs(d)-Proj4js.common.HALF_PI);
var c,g;
if(a>Proj4js.common.EPSLN){c=Proj4js.common.tsfnz(this.e,d,Math.sin(d));
g=this.a*this.f0*Math.pow(c,this.ns)
}else{a=d*this.ns;
if(a<=0){Proj4js.reportError("lcc:forward: No Projection");
return null
}g=0
}var b=this.ns*Proj4js.common.adjust_lon(f-this.long0);
e.x=this.k0*(g*Math.sin(b))+this.x0;
e.y=this.k0*(this.rh-g*Math.cos(b))+this.y0;
return e
},inverse:function(b){var e,c,f;
var g,a;
var i=(b.x-this.x0)/this.k0;
var h=(this.rh-(b.y-this.y0)/this.k0);
if(this.ns>0){e=Math.sqrt(i*i+h*h);
c=1
}else{e=-Math.sqrt(i*i+h*h);
c=-1
}var d=0;
if(e!=0){d=Math.atan2((c*i),(c*h))
}if((e!=0)||(this.ns>0)){c=1/this.ns;
f=Math.pow((e/(this.a*this.f0)),c);
g=Proj4js.common.phi2z(this.e,f);
if(g==-9999){return null
}}else{g=-Proj4js.common.HALF_PI
}a=Proj4js.common.adjust_lon(d/this.ns+this.long0);
b.x=a;
b.y=g;
return b
}};
Proj4js.defs["EPSG:2154"]="+title=RGF93 / Lambert-93 +proj=lcc +lat_1=49 +lat_2=44 +lat_0=46.5 +lon_0=3 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs ";
Proj4js.Proj.eqc={init:function(){if(!this.x0){this.x0=0
}if(!this.y0){this.y0=0
}if(!this.lat0){this.lat0=0
}if(!this.long0){this.long0=0
}if(!this.lat_ts){this.lat_ts=0
}if(!this.title){this.title="Equidistant Cylindrical (Plate Carre)"
}this.rc=Math.cos(this.lat_ts)
},forward:function(d){var e=d.x;
var c=d.y;
var b=Proj4js.common.adjust_lon(e-this.long0);
var a=Proj4js.common.adjust_lat(c-this.lat0);
d.x=this.x0+(this.a*b*this.rc);
d.y=this.y0+(this.a*a);
return d
},inverse:function(b){var a=b.x;
var c=b.y;
b.x=Proj4js.common.adjust_lon(this.long0+((a-this.x0)/(this.a*this.rc)));
b.y=Proj4js.common.adjust_lat(this.lat0+((c-this.y0)/(this.a)));
return b
}};
Proj4js.defs["IGNF:GEOPORTALANF"]="+title=Geoportail - Antilles francaises +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=15.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGNCGEO"]="+title=Reseau Geodesique de Nouvelle-Caledonie +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:LAMB93"]="+title=Lambert 93 +proj=lcc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=46.500000000 +lon_0=3.000000000 +lat_1=44.000000000 +lat_2=49.000000000 +x_0=700000.000 +y_0=6600000.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALKER"]="+title=Geoportail - Kerguelen +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-49.500000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.Proj.utm={dependsOn:"tmerc",init:function(){if(!this.zone){Proj4js.reportError("utm:init: zone must be specified for UTM");
return
}this.lat0=0;
this.long0=((6*Math.abs(this.zone))-183)*Proj4js.common.D2R;
this.x0=500000;
this.y0=this.utmSouth?10000000:0;
this.k0=0.9996;
Proj4js.Proj.tmerc.init.apply(this);
this.forward=Proj4js.Proj.tmerc.forward;
this.inverse=Proj4js.Proj.tmerc.inverse
}};
Proj4js.defs["EPSG:3296"]="+title=RGPF / UTM zone 5S +proj=utm +zone=5 +south +ellps=GRS80 +units=m +no_defs ";
Proj4js.Proj.tmerc={init:function(){this.e0=Proj4js.common.e0fn(this.es);
this.e1=Proj4js.common.e1fn(this.es);
this.e2=Proj4js.common.e2fn(this.es);
this.e3=Proj4js.common.e3fn(this.es);
this.ml0=this.a*Proj4js.common.mlfn(this.e0,this.e1,this.e2,this.e3,this.lat0)
},forward:function(d){var a=d.x;
var m=d.y;
var g=Proj4js.common.adjust_lon(a-this.long0);
var e;
var u,r;
var s=Math.sin(m);
var k=Math.cos(m);
if(this.sphere){var q=k*Math.sin(g);
if((Math.abs(Math.abs(q)-1))<1e-10){Proj4js.reportError("tmerc:forward: Point projects into infinity");
return(93)
}else{u=0.5*this.a*this.k0*Math.log((1+q)/(1-q));
e=Math.acos(k*Math.cos(g)/Math.sqrt(1-q*q));
if(m<0){e=-e
}r=this.a*this.k0*(e-this.lat0)
}}else{var j=k*g;
var i=Math.pow(j,2);
var l=this.ep2*Math.pow(k,2);
var o=Math.tan(m);
var v=Math.pow(o,2);
e=1-this.es*Math.pow(s,2);
var f=this.a/Math.sqrt(e);
var h=this.a*Proj4js.common.mlfn(this.e0,this.e1,this.e2,this.e3,m);
u=this.k0*f*j*(1+i/6*(1-v+l+i/20*(5-18*v+Math.pow(v,2)+72*l-58*this.ep2)))+this.x0;
r=this.k0*(h-this.ml0+f*o*(i*(0.5+i/24*(5-v+9*l+4*Math.pow(l,2)+i/30*(61-58*v+Math.pow(v,2)+600*l-330*this.ep2)))))+this.y0
}d.x=u;
d.y=r;
return d
},inverse:function(A){var k,e;
var J;
var C;
var o=6;
var m,j;
if(this.sphere){var F=Math.exp(A.x/(this.a*this.k0));
var E=0.5*(F-1/F);
var H=this.lat0+A.y/(this.a*this.k0);
var D=Math.cos(H);
k=Math.sqrt((1-D*D)/(1+E*E));
m=Proj4js.common.asinz(k);
if(H<0){m=-m
}if((E==0)&&(D==0)){j=this.long0
}else{j=Proj4js.common.adjust_lon(Math.atan2(E,D)+this.long0)
}}else{var s=A.x-this.x0;
var q=A.y-this.y0;
k=(this.ml0+q/this.k0)/this.a;
e=k;
for(C=0;
true;
C++){J=((k+this.e1*Math.sin(2*e)-this.e2*Math.sin(4*e)+this.e3*Math.sin(6*e))/this.e0)-e;
e+=J;
if(Math.abs(J)<=Proj4js.common.EPSLN){break
}if(C>=o){Proj4js.reportError("tmerc:inverse: Latitude failed to converge");
return(95)
}}if(Math.abs(e)<Proj4js.common.HALF_PI){var b=Math.sin(e);
var K=Math.cos(e);
var u=Math.tan(e);
var I=this.ep2*Math.pow(K,2);
var l=Math.pow(I,2);
var v=Math.pow(u,2);
var a=Math.pow(v,2);
k=1-this.es*Math.pow(b,2);
var B=this.a/Math.sqrt(k);
var w=B*(1-this.es)/k;
var G=s/(B*this.k0);
var z=Math.pow(G,2);
m=e-(B*u*z/w)*(0.5-z/24*(5+3*v+10*I-4*l-9*this.ep2-z/30*(61+90*v+298*I+45*a-252*this.ep2-3*l)));
j=Proj4js.common.adjust_lon(this.long0+(G*(1-z/6*(1+2*v+I-z/20*(5-2*I+28*v-3*l+8*this.ep2+24*a)))/K))
}else{m=Proj4js.common.HALF_PI*Proj4js.common.sign(q);
j=this.long0
}}A.x=j;
A.y=m;
return A
}};
Proj4js.defs["IGNF:RGNCUTM58S"]="+title=Reseau Geodesique de Nouvelle-Caledonie - UTM fuseau 58 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=165.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["EPSG:2975"]="+title=RGR92 / UTM zone 40S +proj=utm +zone=40 +south +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs ";
Proj4js.defs["EPSG:4559"]="+proj=utm +zone=20 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs ";
Proj4js.defs["IGNF:RGM04UTM38S"]="+title=UTM fuseau 38 Sud (Reseau Geodesique de Mayotte 2004) +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=45.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:ETRS89LCC"]="+title=ETRS89 Lambert Conformal Conic +proj=lcc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=52.000000000 +lon_0=9.999999995 +lat_1=35.000000000 +lat_2=65.000000000 +x_0=4000000.000 +y_0=2800000.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGNCUTM57S"]="+title=Reseau Geodesique de Nouvelle-Caledonie - UTM fuseau 57 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=159.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["EPSG:4624"]="+title=RGFG95 +proj=longlat +ellps=GRS80 +towgs84=2,2,-2,0,0,0,0 +no_defs ";
Proj4js.defs["IGNF:GEOPORTALPYF"]="+title=Geoportail - Polynesie francaise +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-15.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:4640"]="+title=RRAF 1991 +proj=longlat +ellps=WGS84 +towgs84=0,0,0,0,0,0,0 +no_defs ";
Proj4js.Proj.laea={S_POLE:1,N_POLE:2,EQUIT:3,OBLIQ:4,init:function(){var a=Math.abs(this.lat0);
if(Math.abs(a-Proj4js.common.HALF_PI)<Proj4js.common.EPSLN){this.mode=this.lat0<0?this.S_POLE:this.N_POLE
}else{if(Math.abs(a)<Proj4js.common.EPSLN){this.mode=this.EQUIT
}else{this.mode=this.OBLIQ
}}if(this.es>0){var b;
this.qp=Proj4js.common.qsfnz(this.e,1);
this.mmf=0.5/(1-this.es);
this.apa=this.authset(this.es);
switch(this.mode){case this.N_POLE:case this.S_POLE:this.dd=1;
break;
case this.EQUIT:this.rq=Math.sqrt(0.5*this.qp);
this.dd=1/this.rq;
this.xmf=1;
this.ymf=0.5*this.qp;
break;
case this.OBLIQ:this.rq=Math.sqrt(0.5*this.qp);
b=Math.sin(this.lat0);
this.sinb1=Proj4js.common.qsfnz(this.e,b)/this.qp;
this.cosb1=Math.sqrt(1-this.sinb1*this.sinb1);
this.dd=Math.cos(this.lat0)/(Math.sqrt(1-this.es*b*b)*this.rq*this.cosb1);
this.ymf=(this.xmf=this.rq)/this.dd;
this.xmf*=this.dd;
break
}}else{if(this.mode==this.OBLIQ){this.sinph0=Math.sin(this.lat0);
this.cosph0=Math.cos(this.lat0)
}}},forward:function(e){var m,k;
var n=e.x;
var i=e.y;
n=Proj4js.common.adjust_lon(n-this.long0);
if(this.sphere){var l,g,c;
c=Math.sin(i);
g=Math.cos(i);
l=Math.cos(n);
switch(this.mode){case this.OBLIQ:case this.EQUIT:k=(this.mode==this.EQUIT)?1+g*l:1+this.sinph0*c+this.cosph0*g*l;
if(k<=Proj4js.common.EPSLN){Proj4js.reportError("laea:fwd:y less than eps");
return null
}k=Math.sqrt(2/k);
m=k*g*Math.sin(n);
k*=(this.mode==this.EQUIT)?c:this.cosph0*c-this.sinph0*g*l;
break;
case this.N_POLE:l=-l;
case this.S_POLE:if(Math.abs(i+this.phi0)<Proj4js.common.EPSLN){Proj4js.reportError("laea:fwd:phi < eps");
return null
}k=Proj4js.common.FORTPI-i*0.5;
k=2*((this.mode==this.S_POLE)?Math.cos(k):Math.sin(k));
m=k*Math.sin(n);
k*=l;
break
}}else{var l,h,c,d,f=0,a=0,j=0;
l=Math.cos(n);
h=Math.sin(n);
c=Math.sin(i);
d=Proj4js.common.qsfnz(this.e,c);
if(this.mode==this.OBLIQ||this.mode==this.EQUIT){f=d/this.qp;
a=Math.sqrt(1-f*f)
}switch(this.mode){case this.OBLIQ:j=1+this.sinb1*f+this.cosb1*a*l;
break;
case this.EQUIT:j=1+a*l;
break;
case this.N_POLE:j=Proj4js.common.HALF_PI+i;
d=this.qp-d;
break;
case this.S_POLE:j=i-Proj4js.common.HALF_PI;
d=this.qp+d;
break
}if(Math.abs(j)<Proj4js.common.EPSLN){Proj4js.reportError("laea:fwd:b < eps");
return null
}switch(this.mode){case this.OBLIQ:case this.EQUIT:j=Math.sqrt(2/j);
if(this.mode==this.OBLIQ){k=this.ymf*j*(this.cosb1*f-this.sinb1*a*l)
}else{k=(j=Math.sqrt(2/(1+a*l)))*f*this.ymf
}m=this.xmf*j*a*h;
break;
case this.N_POLE:case this.S_POLE:if(d>=0){m=(j=Math.sqrt(d))*h;
k=l*((this.mode==this.S_POLE)?j:-j)
}else{m=k=0
}break
}}e.x=this.a*m+this.x0;
e.y=this.a*k+this.y0;
return e
},inverse:function(b){b.x-=this.x0;
b.y-=this.y0;
var h=b.x/this.a;
var f=b.y/this.a;
var l,e;
if(this.sphere){var m=0,j,c=0;
j=Math.sqrt(h*h+f*f);
var e=j*0.5;
if(e>1){Proj4js.reportError("laea:Inv:DataError");
return null
}e=2*Math.asin(e);
if(this.mode==this.OBLIQ||this.mode==this.EQUIT){c=Math.sin(e);
m=Math.cos(e)
}switch(this.mode){case this.EQUIT:e=(Math.abs(j)<=Proj4js.common.EPSLN)?0:Math.asin(f*c/j);
h*=c;
f=m*j;
break;
case this.OBLIQ:e=(Math.abs(j)<=Proj4js.common.EPSLN)?this.phi0:Math.asin(m*sinph0+f*c*cosph0/j);
h*=c*cosph0;
f=(m-Math.sin(e)*sinph0)*j;
break;
case this.N_POLE:f=-f;
e=Proj4js.common.HALF_PI-e;
break;
case this.S_POLE:e-=Proj4js.common.HALF_PI;
break
}l=(f==0&&(this.mode==this.EQUIT||this.mode==this.OBLIQ))?0:Math.atan2(h,f)
}else{var i,d,a,g,k=0;
switch(this.mode){case this.EQUIT:case this.OBLIQ:h/=this.dd;
f*=this.dd;
g=Math.sqrt(h*h+f*f);
if(g<Proj4js.common.EPSLN){b.x=0;
b.y=this.phi0;
return b
}d=2*Math.asin(0.5*g/this.rq);
i=Math.cos(d);
h*=(d=Math.sin(d));
if(this.mode==this.OBLIQ){k=i*this.sinb1+f*d*this.cosb1/g;
a=this.qp*k;
f=g*this.cosb1*i-f*this.sinb1*d
}else{k=f*d/g;
a=this.qp*k;
f=g*i
}break;
case this.N_POLE:f=-f;
case this.S_POLE:a=(h*h+f*f);
if(!a){b.x=0;
b.y=this.phi0;
return b
}k=1-a/this.qp;
if(this.mode==this.S_POLE){k=-k
}break
}l=Math.atan2(h,f);
e=this.authlat(Math.asin(k),this.apa)
}b.x=Proj4js.common.adjust_lon(this.long0+l);
b.y=e;
return b
},P00:0.3333333333333333,P01:0.17222222222222222,P02:0.10257936507936508,P10:0.06388888888888888,P11:0.0664021164021164,P20:0.016415012942191543,authset:function(b){var a;
var c=new Array();
c[0]=b*this.P00;
a=b*b;
c[0]+=a*this.P01;
c[1]=a*this.P10;
a*=b;
c[0]+=a*this.P02;
c[1]+=a*this.P11;
c[2]=a*this.P20;
return c
},authlat:function(b,c){var a=b+b;
return(b+c[0]*Math.sin(a)+c[1]*Math.sin(a+a)+c[2]*Math.sin(a+a+a))
}};
Proj4js.defs["IGNF:ETRS89LAEA"]="+title=ETRS89 Lambert Azimutal Equal Area +proj=laea +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=52.000000000 +lon_0=10.000000000 +x_0=4321000.000 +y_0=3210000.000 +units=m +no_defs";
Proj4js.defs["EPSG:32743"]="+proj=utm +zone=43 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["IGNF:UTM20W84GUAD"]="+title=World Geodetic System 1984 UTM fuseau 20 Nord-Guadeloupe +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-63.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:32739"]="+proj=utm +zone=39 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:32707"]="+proj=utm +zone=7 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:32757"]="+proj=utm +zone=57 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:3297"]="+title=RGPF / UTM zone 6S +proj=utm +zone=6 +south +ellps=GRS80 +units=m +no_defs ";
Proj4js.defs["EPSG:310642801"]="+title=Geoportail - Crozet +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-46.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:310024802"]="+title=Geoportail - France metropolitaine +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=46.500000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM42SW84"]="+title=World Geodetic System 1984 UTM fuseau 42 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=69.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALASP"]="+title=Geoportail - Amsterdam et Saint-Paul +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-38.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:4171"]="+title=RGF93 +proj=longlat +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +no_defs ";
Proj4js.Proj.stere={ssfn_:function(a,c,b){c*=b;
return(Math.tan(0.5*(Proj4js.common.HALF_PI+a))*Math.pow((1-c)/(1+c),0.5*b))
},TOL:1e-8,NITER:8,CONV:1e-10,S_POLE:0,N_POLE:1,OBLIQ:2,EQUIT:3,init:function(){this.phits=this.lat_ts?this.lat_ts:Proj4js.common.HALF_PI;
var a=Math.abs(this.lat0);
if((Math.abs(a)-Proj4js.common.HALF_PI)<Proj4js.common.EPSLN){this.mode=this.lat0<0?this.S_POLE:this.N_POLE
}else{this.mode=a>Proj4js.common.EPSLN?this.OBLIQ:this.EQUIT
}this.phits=Math.abs(this.phits);
if(this.es){var b;
switch(this.mode){case this.N_POLE:case this.S_POLE:if(Math.abs(this.phits-Proj4js.common.HALF_PI)<Proj4js.common.EPSLN){this.akm1=2*this.k0/Math.sqrt(Math.pow(1+this.e,1+this.e)*Math.pow(1-this.e,1-this.e))
}else{a=Math.sin(this.phits);
this.akm1=Math.cos(this.phits)/Proj4js.common.tsfnz(this.e,this.phits,a);
a*=this.e;
this.akm1/=Math.sqrt(1-a*a)
}break;
case this.EQUIT:this.akm1=2*this.k0;
break;
case this.OBLIQ:a=Math.sin(this.lat0);
b=2*Math.atan(this.ssfn_(this.lat0,a,this.e))-Proj4js.common.HALF_PI;
a*=this.e;
this.akm1=2*this.k0*Math.cos(this.lat0)/Math.sqrt(1-a*a);
this.sinX1=Math.sin(b);
this.cosX1=Math.cos(b);
break
}}else{switch(this.mode){case this.OBLIQ:this.sinph0=Math.sin(this.lat0);
this.cosph0=Math.cos(this.lat0);
case this.EQUIT:this.akm1=2*this.k0;
break;
case this.S_POLE:case this.N_POLE:this.akm1=Math.abs(this.phits-Proj4js.common.HALF_PI)>=Proj4js.common.EPSLN?Math.cos(this.phits)/Math.tan(Proj4js.common.FORTPI-0.5*this.phits):2*this.k0;
break
}}},forward:function(c){var b=c.x;
b=Proj4js.common.adjust_lon(b-this.long0);
var i=c.y;
var m,k;
if(this.sphere){var a,f,l,g;
a=Math.sin(i);
f=Math.cos(i);
l=Math.cos(b);
g=Math.sin(b);
switch(this.mode){case this.EQUIT:k=1+f*l;
if(k<=Proj4js.common.EPSLN){F_ERROR
}k=this.akm1/k;
m=k*f*g;
k*=a;
break;
case this.OBLIQ:k=1+this.sinph0*a+this.cosph0*f*l;
if(k<=Proj4js.common.EPSLN){F_ERROR
}k=this.akm1/k;
m=k*f*g;
k*=this.cosph0*a-this.sinph0*f*l;
break;
case this.N_POLE:l=-l;
i=-i;
case this.S_POLE:if(Math.abs(i-Proj4js.common.HALF_PI)<this.TOL){F_ERROR
}k=this.akm1*Math.tan(Proj4js.common.FORTPI+0.5*i);
m=g*k;
k*=l;
break
}}else{l=Math.cos(b);
g=Math.sin(b);
a=Math.sin(i);
var j,h;
if(this.mode==this.OBLIQ||this.mode==this.EQUIT){var e=2*Math.atan(this.ssfn_(i,a,this.e));
j=Math.sin(e-Proj4js.common.HALF_PI);
h=Math.cos(e)
}switch(this.mode){case this.OBLIQ:var d=this.akm1/(this.cosX1*(1+this.sinX1*j+this.cosX1*h*l));
k=d*(this.cosX1*j-this.sinX1*h*l);
m=d*h;
break;
case this.EQUIT:var d=2*this.akm1/(1+h*l);
k=d*j;
m=d*h;
break;
case this.S_POLE:i=-i;
l=-l;
a=-a;
case this.N_POLE:m=this.akm1*Proj4js.common.tsfnz(this.e,i,a);
k=-m*l;
break
}m=m*g
}c.x=m*this.a+this.x0;
c.y=k*this.a+this.y0;
return c
},inverse:function(d){var r=(d.x-this.x0)/this.a;
var n=(d.y-this.y0)/this.a;
var b,m;
var h,a,q=0,e=0,o,g=0,k=0;
var j;
if(this.sphere){var l,s,f,t;
s=Math.sqrt(r*r+n*n);
l=2*Math.atan(s/this.akm1);
f=Math.sin(l);
t=Math.cos(l);
b=0;
switch(this.mode){case this.EQUIT:if(Math.abs(s)<=Proj4js.common.EPSLN){m=0
}else{m=Math.asin(n*f/s)
}if(t!=0||r!=0){b=Math.atan2(r*f,t*s)
}break;
case this.OBLIQ:if(Math.abs(s)<=Proj4js.common.EPSLN){m=this.phi0
}else{m=Math.asin(t*sinph0+n*f*cosph0/s)
}l=t-sinph0*Math.sin(m);
if(l!=0||r!=0){b=Math.atan2(r*f*cosph0,l*s)
}break;
case this.N_POLE:n=-n;
case this.S_POLE:if(Math.abs(s)<=Proj4js.common.EPSLN){m=this.phi0
}else{m=Math.asin(this.mode==this.S_POLE?-t:t)
}b=(r==0&&n==0)?0:Math.atan2(r,n);
break
}d.x=Proj4js.common.adjust_lon(b+this.long0);
d.y=m
}else{o=Math.sqrt(r*r+n*n);
switch(this.mode){case this.OBLIQ:case this.EQUIT:q=2*Math.atan2(o*this.cosX1,this.akm1);
h=Math.cos(q);
a=Math.sin(q);
if(o==0){e=Math.asin(h*this.sinX1)
}else{e=Math.asin(h*this.sinX1+(n*a*this.cosX1/o))
}q=Math.tan(0.5*(Proj4js.common.HALF_PI+e));
r*=a;
n=o*this.cosX1*h-n*this.sinX1*a;
k=Proj4js.common.HALF_PI;
g=0.5*this.e;
break;
case this.N_POLE:n=-n;
case this.S_POLE:q=-o/this.akm1;
e=Proj4js.common.HALF_PI-2*Math.atan(q);
k=-Proj4js.common.HALF_PI;
g=-0.5*this.e;
break
}for(j=this.NITER;
j--;
e=m){a=this.e*Math.sin(e);
m=2*Math.atan(q*Math.pow((1+a)/(1-a),g))-k;
if(Math.abs(e-m)<this.CONV){if(this.mode==this.S_POLE){m=-m
}b=(r==0&&n==0)?0:Math.atan2(r,n);
d.x=Proj4js.common.adjust_lon(b+this.long0);
d.y=m;
return d
}}}}};
Proj4js.defs["IGNF:GEOPORTALGUF"]="+title=Geoportail - Guyane +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=4.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM22RGFG95"]="+title=RGFG95 UTM fuseau 22 Nord-Guyane +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-51.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM01SW84"]="+title=World Geodetic System 1984 UTM fuseau 01 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-177.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGPFGEO"]="+title=RGPF (Reseau Geodesique de Polynesie Francaise) +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["EPSG:32759"]="+proj=utm +zone=59 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["IGNF:RGSPM06U21"]="+title=Saint-Pierre-et-Miquelon (2006) UTM Fuseau 21 Nord +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-57.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:3034"]="+title=ETRS89 / ETRS-LCC +proj=lcc +lat_1=35 +lat_2=65 +lat_0=52 +lon_0=10 +x_0=4000000 +y_0=2800000 +ellps=GRS80 +units=m +no_defs ";
Proj4js.defs["IGNF:RGPFUTM5S"]="+title=RGPF - UTM fuseau 5 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-153.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["EPSG:2989"]="+title=RRAF 1991 / UTM zone 20N +proj=utm +zone=20 +ellps=WGS84 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs ";
Proj4js.defs["EPSG:3298"]="+title=RGPF / UTM zone 7S +proj=utm +zone=7 +south +ellps=GRS80 +units=m +no_defs ";
Proj4js.defs["EPSG:4471"]="+proj=utm +zone=38 +south +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs ";
Proj4js.defs["EPSG:4749"]="+title=RGNC91-93 +proj=longlat +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +no_defs ";
Proj4js.defs["IGNF:RGNCUTM59S"]="+title=Reseau Geodesique de Nouvelle-Caledonie - UTM fuseau 59 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=171.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["EPSG:310702807"]="+title=Geoportail - Mayotte +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-12.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:32622"]="+proj=utm +zone=22 +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:2972"]="+title=RGFG95 / UTM zone 22N +proj=utm +zone=22 +ellps=GRS80 +towgs84=2,2,-2,0,0,0,0 +units=m +no_defs ";
Proj4js.defs["EPSG:2986"]="+title=Terre Adelie 1950 +proj=stere +towgs84=324.9120,153.2820,172.0260 +a=6378388.0000 +rf=297.0000000000000 +lat_0=-90.000000000 +lon_0=140.000000000 +lat_ts=-67.000000000 +k=0.96027295 +x_0=300000.000 +y_0=-2299363.482 +units=m +no_defs";
Proj4js.defs["EPSG:32738"]="+proj=utm +zone=38 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["IGNF:GEOPORTALWLF"]="+title=Geoportail - Wallis et Futuna +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-14.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:310915814"]="+title=Geoportail - Antilles francaises +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=15.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:4258"]="+title=ETRS89 +proj=longlat +ellps=GRS80 +no_defs ";
Proj4js.defs["EPSG:310642812"]="+title=Geoportail - Kerguelen +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-49.500000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:4627"]="+title=RGR92 +proj=longlat +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +no_defs ";
Proj4js.defs["EPSG:310642810"]="+title=Geoportail - Wallis et Futuna +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-14.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGFG95GEO"]="+title=Reseau geodesique francais de Guyane 1995 +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:RGPFUTM6S"]="+title=RGPF - UTM fuseau 6 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-147.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["EPSG:4467"]="+proj=utm +zone=21 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs ";
Proj4js.defs["IGNF:RGTAAF07G"]="+title=Reseau Geodesique des TAAF (2007) (dms) +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:AMST63UTM43S"]="+title=Amsterdam 1963 UTM fuseau 43 Sud +proj=tmerc +towgs84=109.753,-528.133,-362.244,0,0,0,0 +a=6378388.0000 +rf=297.0000000000000 +lat_0=0.000000000 +lon_0=75.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:ETRS89GEO"]="+title=ETRS89 geographiques (dms) +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["EPSG:310032811"]="+title=Geoportail - Polynesie francaise +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-15.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:4687"]="+proj=longlat +ellps=GRS80 +no_defs ";
Proj4js.defs["EPSG:32742"]="+proj=utm +zone=42 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:310486805"]="+title=Geoportail - Guyane +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=4.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALREU"]="+title=Geoportail - Reunion et dependances +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-21.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:32706"]="+proj=utm +zone=6 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:32740"]="+proj=utm +zone=40 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:2969"]="+title=Fort Marigot / UTM zone 20N +proj=utm +zone=20 +ellps=intl +towgs84=137,248,-430,0,0,0,0 +units=m +no_defs ";
Proj4js.defs["EPSG:32758"]="+proj=utm +zone=58 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["IGNF:GEOPORTALCRZ"]="+title=Geoportail - Crozet +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-46.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGM04GEO"]="+title=RGM04 (Reseau Geodesique de Mayotte 2004) +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:MILLER"]="+title=Geoportail - Monde +proj=mill +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lon_0=0.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:32606"]="+proj=utm +zone=6 +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["IGNF:RGPFUTM7S"]="+title=RGPF - UTM fuseau 7 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-141.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:STPL69UTM43S"]="+title=Saint-Paul 1969 UTM fuseau 43 Sud +proj=tmerc +towgs84=225.571,-346.608,-46.567,0,0,0,0 +a=6378388.0000 +rf=297.0000000000000 +lat_0=0.000000000 +lon_0=75.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["EPSG:310706808"]="+title=Geoportail - Saint-Pierre et Miquelon +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=47.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:32662"]="+title=WGS 84 / Plate Carree +proj=eqc +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["IGNF:UTM20W84MART"]="+title=World Geodetic System 1984 UTM fuseau 20 Nord-Martinique +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-63.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALMYT"]="+title=Geoportail - Mayotte +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-12.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGR92UTM40S"]="+title=RGR92 UTM fuseau 40 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=57.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGSPM06GEO"]="+title=Saint-Pierre-et-Miquelon (2006) +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.Proj.merc={init:function(){if(this.lat_ts){if(this.sphere){this.k0=Math.cos(this.lat_ts)
}else{this.k0=Proj4js.common.msfnz(this.es,Math.sin(this.lat_ts),Math.cos(this.lat_ts))
}}},forward:function(e){var f=e.x;
var d=e.y;
if(d*Proj4js.common.R2D>90&&d*Proj4js.common.R2D<-90&&f*Proj4js.common.R2D>180&&f*Proj4js.common.R2D<-180){Proj4js.reportError("merc:forward: llInputOutOfRange: "+f+" : "+d);
return null
}var a,g;
if(Math.abs(Math.abs(d)-Proj4js.common.HALF_PI)<=Proj4js.common.EPSLN){Proj4js.reportError("merc:forward: ll2mAtPoles");
return null
}else{if(this.sphere){a=this.x0+this.a*this.k0*Proj4js.common.adjust_lon(f-this.long0);
g=this.y0+this.a*this.k0*Math.log(Math.tan(Proj4js.common.FORTPI+0.5*d))
}else{var c=Math.sin(d);
var b=Proj4js.common.tsfnz(this.e,d,c);
a=this.x0+this.a*this.k0*Proj4js.common.adjust_lon(f-this.long0);
g=this.y0-this.a*this.k0*Math.log(b)
}e.x=a;
e.y=g;
return e
}},inverse:function(d){var a=d.x-this.x0;
var f=d.y-this.y0;
var e,c;
if(this.sphere){c=Proj4js.common.HALF_PI-2*Math.atan(Math.exp(-f/this.a*this.k0))
}else{var b=Math.exp(-f/(this.a*this.k0));
c=Proj4js.common.phi2z(this.e,b);
if(c==-9999){Proj4js.reportError("merc:inverse: lat = -9999");
return null
}}e=Proj4js.common.adjust_lon(this.long0+a/(this.a*this.k0));
d.x=e;
d.y=c;
return d
}};
Proj4js.defs["IGNF:TERA50G"]="+title=Pointe Geologie - Perroud 1950 +proj=longlat +towgs84=324.9120,153.2820,172.0260 +a=6378388.0000 +rf=297.0000000000000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALSPM"]="+title=Geoportail - Saint-Pierre et Miquelon +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=47.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:310547809"]="+title=Geoportail - Nouvelle-Caledonie +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-22.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:AMST63GEO"]="+title=Amsterdam 1963 +proj=longlat +towgs84=109.753,-528.133,-362.244,0,0,0,0 +a=6378388.0000 +rf=297.0000000000000 +units=m +no_defs";
Proj4js.defs["IGNF:RGR92GEO"]="+title=Reseau geodesique de la Reunion 1992 +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["EPSG:3035"]="+title=ETRS89 / ETRS-LAEA +proj=laea +lat_0=52 +lon_0=10 +x_0=4321000 +y_0=3210000 +ellps=GRS80 +units=m +no_defs ";
Proj4js.defs["EPSG:310642813"]="+title=Geoportail - Amsterdam et Saint-Paul +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-38.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM43SW84"]="+title=World Geodetic System 1984 UTM fuseau 43 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=75.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:WGS84RRAFGEO"]="+title=Reseau de reference des Antilles francaises (1988-1991) +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:TERA50STEREO"]="+title=Terre Adelie 1950 +proj=stere +towgs84=324.9120,153.2820,172.0260 +a=6378388.0000 +rf=297.0000000000000 +lat_0=-90.000000000 +lon_0=140.000000000 +lat_ts=-67 +k=0.96027295 +x_0=300000.000 +y_0=-2299363.482 +units=m +no_defs";
Proj4js.defs["EPSG:32705"]="+proj=utm +zone=5 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:32620"]="+proj=utm +zone=20 +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["EPSG:32701"]="+proj=utm +zone=1 +south +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["IGNF:GEOPORTALNCL"]="+title=Geoportail - Nouvelle-Caledonie +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-22.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["CRS:84"]="+title=WGS 84 longitude-latitude +proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs ";
Proj4js.defs["EPSG:310700806"]="+title=Geoportail - Reunion et dependances +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-21.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGF93G"]="+title=Reseau geodesique francais 1993 +proj=longlat +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALFXX"]="+title=Geoportail - France metropolitaine +proj=eqc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=46.500000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM39SW84"]="+title=World Geodetic System 1984 UTM fuseau 39 Sud +proj=tmerc +nadgrids=null +wktext +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=51.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
window.OpenLayers=window.OpenLayers||{};
OpenLayers._document=null;
OpenLayers.getDoc=function(){return OpenLayers._document||document
};
OpenLayers.setDoc=function(a){OpenLayers._document=a
};
if(!OpenLayers.Class){alert("OpenLayers.Class is mandatory")
}if(typeof(OpenLayers.inherit)!=="function"){OpenLayers.overload=function(P,F){var pProtoInitialize=typeof(F.initialize)=="function"?P.prototype.initialize:null;
OpenLayers.Util.extend(P.prototype,F);
if(pProtoInitialize!==null){for(var pn in P){if(typeof(P[pn])=="function"&&P[pn].prototype.initialize===pProtoInitialize){var f={};
f=eval('{"initialize":'+F.initialize.toString()+"}");
P[pn]=OpenLayers.overload(P[pn],f)
}}}return P
}
}else{OpenLayers.overload=function(c,b){if(typeof(b.initialize)==="function"&&c===c.prototype.initialize){var a=c.prototype;
var d=OpenLayers.Util.extend({},c);
c=b.initialize;
c.prototype=a;
OpenLayers.Util.extend(c,d)
}OpenLayers.Util.extend(c.prototype,b);
return c
}
}if(OpenLayers.Bounds){OpenLayers.Bounds=OpenLayers.overload(OpenLayers.Bounds,{transform:function(r,h,v){if(!v){var l=OpenLayers.Projection.transform({x:this.left,y:this.bottom},r,h);
var g=OpenLayers.Projection.transform({x:this.right,y:this.top},r,h);
this.left=l.x<g.x?l.x:g.x;
this.bottom=l.y<g.y?l.y:g.y;
this.right=g.x>l.x?g.x:l.x;
this.top=g.y>l.y?g.y:l.y;
return this
}var w=h.getProjName()=="longlat"?0.000028:1;
var b,d,x,n;
var c=1;
for(var t=0;
t<7;
t++){var m=(this.right-this.left)/(1*c);
var k=(this.top-this.bottom)/(1*c);
var o;
var a,u,f,y;
var z=[],e=0;
for(var s=0;
s<c;
s++){z[e++]={x:this.left+s*m,y:this.bottom};
z[e++]={x:this.right,y:this.bottom+s*k};
z[e++]={x:this.right-s*m,y:this.top};
z[e++]={x:this.left,y:this.top-s*k}
}z=OpenLayers.Projection.transform(z,r,h);
if(a==undefined){a=f=z[0].x;
u=y=z[0].y
}for(var q=0;
q<e;
q++){o=z[q];
if(o.x<a){a=o.x
}if(o.y<u){u=o.y
}if(o.x>f){f=o.x
}if(o.y>y){y=o.y
}}z=null;
if(b!=undefined&&Math.abs(a-b)<w&&Math.abs(u-d)<w&&Math.abs(f-x)<w&&Math.abs(y-n)<w){this.left=a;
this.bottom=u;
this.right=f;
this.top=y;
return this
}b=a;
d=u;
x=f;
n=y;
c*=2
}this.left=b;
this.bottom=d;
this.right=x;
this.top=n;
return this
}})
}if(OpenLayers.LonLat){OpenLayers.LonLat=OpenLayers.overload(OpenLayers.LonLat,{equals:function(e,d){var c=false;
if(!d){d=0.000001
}if(e!=null){var b=(!isNaN(this.lon)&&!isNaN(e.lon))?Math.abs(this.lon-e.lon):1;
var a=(!isNaN(this.lat)&&!isNaN(e.lat))?Math.abs(this.lat-e.lat):1;
c=((b<=d&&a<=d)||(isNaN(this.lon)&&isNaN(this.lat)&&isNaN(e.lon)&&isNaN(e.lat)))
}return c
}})
}OpenLayers.Lang=OpenLayers.Lang||{};
OpenLayers.Lang.en=OpenLayers.Lang.en||{};
OpenLayers.Lang.en["no.proj.implementation.found"]="No implementation for Projection handling found";
OpenLayers.Lang.en["unknown.crs"]="Unknown CRS : ${crs}";
OpenLayers.Lang.en.dd="degrees";
OpenLayers.Lang.de=OpenLayers.Lang.de||OpenLayers.Util.applyDefaults({},OpenLayers.Lang.en);
OpenLayers.Lang.de["no.proj.implementation.found"]="Keine umsetzung für projektions-handling gefunden";
OpenLayers.Lang.de["unknown.crs"]="Unknown CRS : ${crs}";
OpenLayers.Lang.de.dd="grad";
OpenLayers.Lang.es=OpenLayers.Lang.es||OpenLayers.Util.applyDefaults({},OpenLayers.Lang.en);
OpenLayers.Lang.es.W="Oe";
OpenLayers.Lang.es.E="Or";
OpenLayers.Lang.es["no.proj.implementation.found"]="No aplicación para el manejo de proyección encontrado";
OpenLayers.Lang.es["unknown.crs"]="Unknown CRS : ${crs}";
OpenLayers.Lang.es.dd="grados";
OpenLayers.Lang.fr=OpenLayers.Lang.fr||OpenLayers.Util.applyDefaults({},OpenLayers.Lang.en);
OpenLayers.Lang.fr["no.proj.implementation.found"]="Aucune implémentation d'un gestionnaire de projections n'a été chargé";
OpenLayers.Lang.fr["unknown.crs"]="CRS inconnu : ${crs}";
OpenLayers.Lang.fr.dd="degrés";
OpenLayers.Lang.it=OpenLayers.Lang.it||OpenLayers.Util.applyDefaults({},OpenLayers.Lang.en);
OpenLayers.Lang.it.W="O";
OpenLayers.Lang.it.E="E";
OpenLayers.Lang.it.N="N";
OpenLayers.Lang.it.S="S";
OpenLayers.Lang.it["no.proj.implementation.found"]="No di attuazione per la gestione di proiezione trovato";
OpenLayers.Lang.it["unknown.crs"]="Unknown CRS : ${crs}";
OpenLayers.Lang.it.dd="gradi";
if(OpenLayers.Util){OpenLayers.Util.isArray=function(b){return(Object.prototype.toString.call(b)==="[object Array]")
};
OpenLayers.Util.extend(OpenLayers.INCHES_PER_UNIT,{deg:OpenLayers.INCHES_PER_UNIT.dd,degre:OpenLayers.INCHES_PER_UNIT.dd,degree:OpenLayers.INCHES_PER_UNIT.dd,rad:OpenLayers.INCHES_PER_UNIT.dd*0.017453292519943295,gon:OpenLayers.INCHES_PER_UNIT.dd*1.1111111111111112,meters:OpenLayers.INCHES_PER_UNIT.m,meter:OpenLayers.INCHES_PER_UNIT.m,metres:OpenLayers.INCHES_PER_UNIT.m,metre:OpenLayers.INCHES_PER_UNIT.m});
OpenLayers.Util.getResolutionFromScale=function(d,a){var b;
if(d){if(a==null||OpenLayers.INCHES_PER_UNIT[a]==undefined){a="degrees"
}var c=OpenLayers.Util.normalizeScale(d);
b=1/(c*OpenLayers.INCHES_PER_UNIT[a]*OpenLayers.DOTS_PER_INCH)
}return b
};
OpenLayers.Util.getScaleFromResolution=function(b,a){if(a==null||OpenLayers.INCHES_PER_UNIT[a]==undefined){a="degrees"
}var c=b*OpenLayers.INCHES_PER_UNIT[a]*OpenLayers.DOTS_PER_INCH;
return c
};
OpenLayers.Util.rad=function(a){return a*0.017453292519943295
};
OpenLayers.Util.deg=function(a){return a*57.29577951308232
};
OpenLayers.Util.gon=function(a){return a*1.1111111111111112
};
OpenLayers.Util.distVincenty=function(g,e,i){if(i==undefined||!(i instanceof OpenLayers.Projection)){i=OpenLayers.Projection.CRS84
}var M=i.getProperty("semi_major")||6378137,K=i.getProperty("semi_minor")||6356752.3142,G=i.getProperty("inverse_flattening")||298.257223563;
G=1/G;
var n=OpenLayers.Util.rad(e.lon-g.lon);
var J=Math.atan((1-G)*Math.tan(OpenLayers.Util.rad(g.lat)));
var I=Math.atan((1-G)*Math.tan(OpenLayers.Util.rad(e.lat)));
var m=Math.sin(J),j=Math.cos(J);
var l=Math.sin(I),h=Math.cos(I);
var r=n,o=2*Math.PI;
var q=20;
while(Math.abs(r-o)>1e-12&&--q>0){var z=Math.sin(r),c=Math.cos(r);
var N=Math.sqrt((h*z)*(h*z)+(j*l-m*h*c)*(j*l-m*h*c));
if(N==0){return 0
}var E=m*l+j*h*c;
var y=Math.atan2(N,E);
var k=Math.asin(j*h*z/N);
var F=Math.cos(k)*Math.cos(k);
var p=E-2*m*l/F;
var v=G/16*F*(4+G*(4-3*F));
o=r;
r=n+(1-v)*G*Math.sin(k)*(y+v*N*(p+v*E*(-1+2*p*p)))
}if(q==0){return NaN
}var u=F*(M*M-K*K)/(K*K);
var x=1+u/16384*(4096+u*(-768+u*(320-175*u)));
var w=u/1024*(256+u*(-128+u*(74-47*u)));
var D=w*N*(p+w/4*(E*(-1+2*p*p)-w/6*p*(-3+4*N*N)*(-3+4*p*p)));
var t=K*x*(y-D);
var H=t.toFixed(3)/1000;
return H
}
}if(OpenLayers.Control){OpenLayers.UI=OpenLayers.UI||OpenLayers.Class({initialize:function(a){}})
}if(OpenLayers.Feature&&OpenLayers.Feature.Vector){OpenLayers.Feature.Vector=OpenLayers.overload(OpenLayers.Feature.Vector,{destroyPopup:function(){OpenLayers.Feature.prototype.destroyPopup.apply(this,arguments)
}})
}if(OpenLayers.Rule){OpenLayers.Rule=OpenLayers.overload(OpenLayers.Rule,{clone:function(){var b=OpenLayers.Util.extend({},this);
if(this.symbolizers){var a=this.symbolizers.length;
b.symbolizers=new Array(a);
for(var d=0;
d<a;
++d){b.symbolizers[d]=this.symbolizers[d].clone()
}}else{b.symbolizer={};
var f,e;
for(var c in this.symbolizer){f=this.symbolizer[c];
e=typeof f;
if(e==="object"){b.symbolizer[c]=OpenLayers.Util.extend({},f)
}else{if(e==="string"){b.symbolizer[c]=f
}}}}b.filter=this.filter&&this.filter.clone();
b.context=typeof this.context==="function"?this.context:this.context&&OpenLayers.Util.extend({},this.context);
return new OpenLayers.Rule(b)
}})
}if(OpenLayers.Format&&OpenLayers.Format.XML){OpenLayers.Format.XML=OpenLayers.overload(OpenLayers.Format.XML,{write:function(b){var c;
if(b.xml!=undefined){c=b.xml
}else{var a=new XMLSerializer();
if(b.nodeType==1){var d=OpenLayers.getDoc().implementation.createDocument("","",null);
if(d.importNode){b=d.importNode(b,true)
}d.appendChild(b);
c=a.serializeToString(d)
}else{c=a.serializeToString(b)
}}return c
},setAttributeNS:function(d,c,a,e){if(e==null||e==undefined){e=""
}if(d.setAttributeNS){d.setAttributeNS(c,a,e)
}else{if(this.xmldom){if(c){var b=d.ownerDocument.createNode(2,a,c);
b.nodeValue=e;
d.setAttributeNode(b)
}else{d.setAttribute(a,e)
}}else{throw OpenLayers.i18n("xml.setattributens")
}}},writeNode:function(a,f,d){var e,c;
var b=a.indexOf(":");
if(b>0){e=a.substring(0,b);
c=a.substring(b+1)
}else{if(d){e=this.namespaceAlias[d.namespaceURI]
}else{e=this.defaultPrefix
}c=a
}var g=this.writers[e][c].apply(this,[f]);
if(d&&g){d.appendChild(g)
}return g
}})
}if(OpenLayers.Format&&OpenLayers.Format.XML&&OpenLayers.Format.XML.VersionedOGC){OpenLayers.Format.XML.VersionedOGC=OpenLayers.Class(OpenLayers.Format.XML.VersionedOGC,{read:function(d,c){if(typeof d=="string"){d=OpenLayers.Format.XML.prototype.read.apply(this,[d])
}var b=d.nodeType==9?d.documentElement:d;
var a=this.getVersion(b);
this.parser=this.getParser(a);
var f=this.parser.read(b,c);
if(f){if(this.errorProperty!==null&&f[this.errorProperty]===undefined){var e=new OpenLayers.Format.OGCExceptionReport();
f.error=e.read(b)
}if(!(OpenLayers.Util.isArray(f))){f.version=a
}}return f
}})
}if(OpenLayers.Geometry&&OpenLayers.Geometry.Point){OpenLayers.Geometry.Point=OpenLayers.overload(OpenLayers.Geometry.Point,{transform:function(b,a){OpenLayers.Projection.transform(this,b,a);
return this
}})
}if(OpenLayers.Geometry&&OpenLayers.Geometry.MultiPoint){OpenLayers.Geometry.MultiPoint=OpenLayers.overload(OpenLayers.Geometry.MultiPoint,{transform:function(b,a){OpenLayers.Projection.transform(this.components,b,a);
if(b&&a){this.bounds=null
}return this
}})
}if(OpenLayers.Geometry&&OpenLayers.Geometry.Curve){OpenLayers.Geometry.Curve=OpenLayers.overload(OpenLayers.Geometry.Curve,{transform:OpenLayers.Geometry.MultiPoint.prototype.transform,getGeodesicLength:function(b){var e=this;
var c=b||OpenLayers.Projection.CRS84;
if(b){if(!OpenLayers.Projection.CRS84.equals(b)){e=this.clone().transform(b,OpenLayers.Projection.CRS84);
c=OpenLayers.Projection.CRS84
}}var f=0;
if(e.components&&(e.components.length>1)){var h,g;
for(var d=1,a=e.components.length;
d<a;
d++){h=e.components[d-1];
g=e.components[d];
f+=OpenLayers.Util.distVincenty({lon:h.x,lat:h.y},{lon:g.x,lat:g.y},c)
}}return f*1000
}})
}if(OpenLayers.Geometry&&OpenLayers.Geometry.LineString){OpenLayers.Geometry.LineString=OpenLayers.overload(OpenLayers.Geometry.LineString,{transform:OpenLayers.Geometry.MultiPoint.prototype.transform,getGeodesicLength:OpenLayers.Geometry.Curve.prototype.getGeodesicLength})
}if(OpenLayers.Geometry&&OpenLayers.Geometry.LinearRing){OpenLayers.Geometry.LinearRing=OpenLayers.overload(OpenLayers.Geometry.LinearRing,{getGeodesicArea:function(e){var c=this;
var g=e||OpenLayers.Projection.CRS84;
if(e){if(!OpenLayers.Projection.CRS84.equals(e)){c=this.clone().transform(e,g);
g=OpenLayers.Projection.CRS84
}}var b=0;
var f=c.components&&c.components.length;
var h=g.getProperty("semi_major")||6378137;
if(f>2){var k,j;
for(var d=0;
d<f-1;
d++){k=c.components[d];
j=c.components[d+1];
b+=OpenLayers.Util.rad(j.x-k.x)*(2+Math.sin(OpenLayers.Util.rad(k.y))+Math.sin(OpenLayers.Util.rad(j.y)))
}b=b*h*h/2
}return b
},containsPoint:function(m){var s=OpenLayers.Number.limitSigDigs;
var l=14;
var k=s(m.x,l);
var j=s(m.y,l);
function r(w,t,v,i,u){return(w-u)*((i-t)/(u-v))+i
}var a=this.components.length-1;
var g,f,q,d,o,b,e,c;
var h=0;
for(var n=0;
n<a;
++n){g=this.components[n];
q=s(g.x,l);
d=s(g.y,l);
f=this.components[n+1];
o=s(f.x,l);
b=s(f.y,l);
if(d==b){if(j==d){if(q<=o&&(k>=q&&k<=o)||q>=o&&(k<=q&&k>=o)){h=-1;
break
}}continue
}e=s(r(j,q,d,o,b),l);
if(e==k){if(d<b&&(j>=d&&j<=b)||d>b&&(j<=d&&j>=b)){h=-1;
break
}}if(e<=k){continue
}if(q!=o&&(e<Math.min(q,o)||e>Math.max(q,o))){continue
}if(d<b&&(j>=d&&j<b)||d>b&&(j<d&&j>=b)){++h
}}var p=(h==-1)?1:!!(h&1);
return p
}})
}if(OpenLayers.Event){OpenLayers.Event.stopObservingElement=function(a){var b=OpenLayers.Util.getElement(a);
if(b){var c=b._eventCacheID;
if(c){this._removeElementObservers(OpenLayers.Event.observers[c])
}}}
}if(OpenLayers.Projection){OpenLayers.Projection=OpenLayers.overload(OpenLayers.Projection,{domainOfValidity:null,initialize:function(f,c){OpenLayers.Util.extend(this,c);
this.projCode=f;
this.options=OpenLayers.Util.extend({},c);
this.aliases=OpenLayers.Util.extend({},this.options.aliases);
if(window.Proj4js){this.proj=null;
try{this.proj=new Proj4js.Proj(f)
}catch(d){throw OpenLayers.i18n("unknown.crs",{crs:f})
}}if(this.proj==null){throw OpenLayers.i18n("no.proj.implementation.found")
}if(f=="EPSG:4326"||f=="CRS:84"||f=="IGNF:WGS84G"||f=="WGS84"){this.domainOfValidity=new OpenLayers.Bounds(-180,-90,180,90)
}else{if(f=="EPSG:3857"||f=="EPSG:900913"||f=="EPSG:102113"||f=="GOOGLE"){this.domainOfValidity=new OpenLayers.Bounds(-180,-85.05113,180,85.05113)
}else{if(this.isUTMZoneProjection()){var b=-180,a=-90,e=180,g=90;
if(this.getProjName()=="utm"){e=this.getProperty("zone")*6-180;
b=e-6;
g=84;
a=0;
if(this.getProperty("south")===true){g=0;
a=-80
}}else{if(this.getProjName()=="stere"){if(this.getProperty("standard_parallel_1")>0){a=84
}else{g=-80
}}}this.domainOfValidity=new OpenLayers.Bounds(b,a,e,g)
}}}},getCode:function(){if(window.Proj4js&&(this.proj instanceof Proj4js.Proj)){return this.proj.srsCode
}return this.projCode
},getUnits:function(){if(window.Proj4js&&(this.proj instanceof Proj4js.Proj)){return this.proj.units||(this.proj.projName=="longlat"?"dd":"m")
}return null
},clone:function(){if(this.proj==null){return null
}var a=new OpenLayers.Projection(this.projCode,this.options);
a.aliases=OpenLayers.Util.extend({},this.aliases);
return a
},getProjName:function(){if(window.Proj4js&&(this.proj instanceof Proj4js.Proj)){return this.proj.projName
}return null
},getTitle:function(){var a=OpenLayers.i18n(this.projCode);
if(a==this.projCode){a=this.getProperty("title");
if(a==null){a=this.projCode
}}return a
},getDatum:function(){if(window.Proj4js&&(this.proj instanceof Proj4js.Proj)){return this.proj.datum
}return null
},getProperty:function(a){if(a==undefined){return null
}if(window.Proj4js&&(this.proj instanceof Proj4js.Proj)){switch(a){case"projcs":a="projName";
break;
case"datum":a="datumCode";
break;
case"spheroid":a="ellps";
break;
case"nadgrids":a="nagrids";
break;
case"semi_major":a="a";
break;
case"semi_minor":a="b";
break;
case"inverse_flattening":a="rf";
break;
case"standard_parallel_1":a=(this.getProjName().match(/t?merc|eqc|stere|utm/)?"lat_ts":"lat1");
break;
case"standard_parallel_2":a="lat2";
break;
case"latitude_of_center":case"latitude_of_origin":a="lat0";
break;
case"longitude_of_center":case"central_meridian":a="long0";
break;
case"false_easting":a="x0";
break;
case"false_northing":a="y0";
break;
case"scale_factor":a="k0";
break;
case"south":a="utmSouth";
break;
case"towgs84":a="datum_params";
break;
case"primem":a="from_greenwich";
break;
default:break
}return this.proj[a]
}return null
},equals:function(b){var a=false;
if(this.proj&&b){var c=b instanceof OpenLayers.Projection?b.getCode():b;
if(this.getCode()==c){a=true
}else{a=this.isAliasOf(b)
}}return a
},isAliasOf:function(j){if(!this.proj||!j){return false
}var c=this.getCode(),b=j instanceof OpenLayers.Projection?j.getCode():j;
if(this.aliases[b]===true){return true
}if(this.aliases[b]===false){return false
}var h=false,g=false;
for(var e in OpenLayers.Projection.WKALIASES){if(OpenLayers.Projection.WKALIASES.hasOwnProperty(e)){var m=OpenLayers.Projection.WKALIASES[e];
for(var f=0,d=m.length;
f<d&&!(h&&g);
f++){if(c==m[f]){h=true;
continue
}if(b==m[f]){g=true;
continue
}}if(h||g){break
}}}this.aliases[b]=(h&&g);
return this.aliases[b]
},isWebMercator:function(){var a=this.getCode();
switch(a){case"GOOGLE":case"EPSG:3857":case"EPSG:102113":case"EPSG:900913":return true;
default:return false
}},isCompatibleWith:function(c){if(!this.proj||!c){return false
}var g;
try{g=c instanceof OpenLayers.Projection?c:new OpenLayers.Projection(c)
}catch(f){OpenLayers.Console.error(f.message);
return false
}var b=false;
if(this.equals(g)){b=true
}else{try{var a=this.getProjName(),d=g.getProjName();
if((a=="longlat"||a=="eqc"||(a=="merc"&&this.isWebMercator()))&&(d=="longlat"||d=="eqc"||(d=="merc"&&g.isWebMercator()))){b=true;
if((a!="merc"&&d!="merc")&&this.getDatum()&&g.getDatum()){b=this.getDatum().compare_datums(g.getDatum())
}if(b&&this.domainOfValidity&&g.domainOfValidity){b=this.domainOfValidity.intersectsBounds(g.domainOfValidity,true)
}}else{}}catch(f){}}if(!(g==c)){g.destroy();
g=null
}return b
},isUTMZoneProjection:function(){if(this.proj==null){return false
}if(this.getProjName()=="utm"&&this.getProperty("zone")!=null){return true
}if(this.getProjName()=="stere"&&this.getProperty("central_meridian")==0&&this.getProperty("latitude_of_origin")===this.getProperty("standard_parallel_1")&&Math.abs(this.getProperty("latitude_of_origin"))==1.57079632679){return true
}return false
},isAxisInverted:function(){if(window.Proj4js&&(this.proj instanceof Proj4js.Proj)){if(this.axisInverted===undefined){this.axisInverted=OpenLayers.Projection.INVERTED_AXIS[this.proj.srsCode]===1
}return this.axisInverted
}return false
},destroy:function(){if(this.proj){delete this.proj
}if(this.projCode){delete this.projCode
}if(this.domainOfValidity){delete this.domainOfValidity
}delete this.options;
delete this.aliases
}});
OpenLayers.Projection.WKALIASES={WGS84G:["WGS84","EPSG:4326","CRS:84","IGNF:WGS84G","IGNF:WGS84RRAFGEO","IGNF:RGF93G","IGNF:RGFG95GEO","IGNF:RGM04GEO","IGNF:RGNCGEO","IGNF:RGPFGEO","IGNF:RGR92GEO","IGNF:RGSPM06GEO","EPSG:4171","EPSG:4624","EPSG:4627","EPSG:4640","EPSG:4687","EPSG:4749","EPSG:4258"],LAMB93:["IGNF:LAMB93","EPSG:2154"],LAMBE:["IGNF:LAMBE","EPSG:27572","EPSG:27582"],UTM39SW84:["IGNF:UTM39SW84","EPSG:32739"],UTM20W84GUAD:["IGNF:UTM20W84GUAD","EPSG:2969","EPSG:4559","EPSG:32620"],UTM22RGFG95:["IGNF:UTM22RGFG95","EPSG:2972","EPSG:32622"],UTM42SW84:["IGNF:UTM42SW84","EPSG:32742"],UTM20W84MART:["IGNF:UTM20W84MART","EPSG:2989","EPSG:4559","EPSG:32620"],RGM04UTM38S:["IGNF:RGM04UTM38S","EPSG:4471","EPSG:32738"],RGNCUTM57S:["IGNF:RGNCUTM57S","EPSG:32757"],RGNCUTM58S:["IGNF:RGNCUTM58S","EPSG:32758"],RGNCUTM59S:["IGNF:RGNCUTM59S","EPSG:32759"],RGPFUTM5S:["IGNF:RGPFUTM5S","EPSG:3296","EPSG:32705"],RGPFUTM6S:["IGNF:RGPFUTM6S","EPSG:3297","EPSG:32706"],RGPFUTM7S:["IGNF:RGPFUTM7S","EPSG:3298","EPSG:32707"],RGR92UTM40S:["IGNF:RGR92UTM40S","EPSG:2975","EPSG:32740"],UTM43SW84:["IGNF:UTM43SW84","EPSG:32743"],RGSPM06U21:["IGNF:RGSPM06U21","EPSG:4467","EPSG:32606"],UTM01SW84:["IGNF:UTM01SW84","EPSG:32701"],GOOGLE:["EPSG:3857","EPSG:900913","EPSG:102113"],GEOPORTALFXX:["IGNF:GEOPORTALFXX","EPSG:310024802"],GEOPORTALANF:["IGNF:GEOPORTALANF","EPSG:310915814"],GEOPORTALGUF:["IGNF:GEOPORTALGUF","EPSG:310486805"],GEOPORTALREU:["IGNF:GEOPORTALREU","EPSG:310700806"],GEOPORTALMYT:["IGNF:GEOPORTALMYT","EPSG:310702807"],GEOPORTALSPM:["IGNF:GEOPORTALSPM","EPSG:310706808"],GEOPORTALNCL:["IGNF:GEOPORTALNCL","EPSG:310547809"],GEOPORTALWLF:["IGNF:GEOPORTALWLF","EPSG:310642810"],GEOPORTALPYF:["IGNF:GEOPORTALPYF","EPSG:310032811"],GEOPORTALKER:["IGNF:GEOPORTALKER","EPSG:310642812"],GEOPORTALCRZ:["IGNF:GEOPORTALCRZ","EPSG:310642801"],GEOPORTALASP:["IGNF:GEOPORTALASP","EPSG:310642813"],TERA50STEREO:["IGNF:TERA50STEREO","EPSG:2986"],MILLER:["IGNF:MILLER","EPSG:310642901"]};
OpenLayers.Projection.INVERTED_AXIS={"EPSG:2036":1,"EPSG:2044":1,"EPSG:2045":1,"EPSG:2065":1,"EPSG:2081":1,"EPSG:2082":1,"EPSG:2083":1,"EPSG:2085":1,"EPSG:2086":1,"EPSG:2091":1,"EPSG:2092":1,"EPSG:2093":1,"EPSG:2096":1,"EPSG:2097":1,"EPSG:2098":1,"EPSG:2105":1,"EPSG:2106":1,"EPSG:2107":1,"EPSG:2108":1,"EPSG:2109":1,"EPSG:2110":1,"EPSG:2111":1,"EPSG:2112":1,"EPSG:2113":1,"EPSG:2114":1,"EPSG:2115":1,"EPSG:2116":1,"EPSG:2117":1,"EPSG:2118":1,"EPSG:2119":1,"EPSG:2120":1,"EPSG:2121":1,"EPSG:2122":1,"EPSG:2123":1,"EPSG:2124":1,"EPSG:2125":1,"EPSG:2126":1,"EPSG:2127":1,"EPSG:2128":1,"EPSG:2129":1,"EPSG:2130":1,"EPSG:2131":1,"EPSG:2132":1,"EPSG:2166":1,"EPSG:2167":1,"EPSG:2168":1,"EPSG:2169":1,"EPSG:2170":1,"EPSG:2171":1,"EPSG:2172":1,"EPSG:2173":1,"EPSG:2174":1,"EPSG:2175":1,"EPSG:2176":1,"EPSG:2177":1,"EPSG:2178":1,"EPSG:2179":1,"EPSG:2180":1,"EPSG:2193":1,"EPSG:2199":1,"EPSG:2200":1,"EPSG:2206":1,"EPSG:2207":1,"EPSG:2208":1,"EPSG:2209":1,"EPSG:2210":1,"EPSG:2211":1,"EPSG:2212":1,"EPSG:2319":1,"EPSG:2320":1,"EPSG:2321":1,"EPSG:2322":1,"EPSG:2323":1,"EPSG:2324":1,"EPSG:2325":1,"EPSG:2326":1,"EPSG:2327":1,"EPSG:2328":1,"EPSG:2329":1,"EPSG:2330":1,"EPSG:2331":1,"EPSG:2332":1,"EPSG:2333":1,"EPSG:2334":1,"EPSG:2335":1,"EPSG:2336":1,"EPSG:2337":1,"EPSG:2338":1,"EPSG:2339":1,"EPSG:2340":1,"EPSG:2341":1,"EPSG:2342":1,"EPSG:2343":1,"EPSG:2344":1,"EPSG:2345":1,"EPSG:2346":1,"EPSG:2347":1,"EPSG:2348":1,"EPSG:2349":1,"EPSG:2350":1,"EPSG:2351":1,"EPSG:2352":1,"EPSG:2353":1,"EPSG:2354":1,"EPSG:2355":1,"EPSG:2356":1,"EPSG:2357":1,"EPSG:2358":1,"EPSG:2359":1,"EPSG:2360":1,"EPSG:2361":1,"EPSG:2362":1,"EPSG:2363":1,"EPSG:2364":1,"EPSG:2365":1,"EPSG:2366":1,"EPSG:2367":1,"EPSG:2368":1,"EPSG:2369":1,"EPSG:2370":1,"EPSG:2371":1,"EPSG:2372":1,"EPSG:2373":1,"EPSG:2374":1,"EPSG:2375":1,"EPSG:2376":1,"EPSG:2377":1,"EPSG:2378":1,"EPSG:2379":1,"EPSG:2380":1,"EPSG:2381":1,"EPSG:2382":1,"EPSG:2383":1,"EPSG:2384":1,"EPSG:2385":1,"EPSG:2386":1,"EPSG:2387":1,"EPSG:2388":1,"EPSG:2389":1,"EPSG:2390":1,"EPSG:2391":1,"EPSG:2392":1,"EPSG:2393":1,"EPSG:2394":1,"EPSG:2395":1,"EPSG:2396":1,"EPSG:2397":1,"EPSG:2398":1,"EPSG:2399":1,"EPSG:2400":1,"EPSG:2401":1,"EPSG:2402":1,"EPSG:2403":1,"EPSG:2404":1,"EPSG:2405":1,"EPSG:2406":1,"EPSG:2407":1,"EPSG:2408":1,"EPSG:2409":1,"EPSG:2410":1,"EPSG:2411":1,"EPSG:2412":1,"EPSG:2413":1,"EPSG:2414":1,"EPSG:2415":1,"EPSG:2416":1,"EPSG:2417":1,"EPSG:2418":1,"EPSG:2419":1,"EPSG:2420":1,"EPSG:2421":1,"EPSG:2422":1,"EPSG:2423":1,"EPSG:2424":1,"EPSG:2425":1,"EPSG:2426":1,"EPSG:2427":1,"EPSG:2428":1,"EPSG:2429":1,"EPSG:2430":1,"EPSG:2431":1,"EPSG:2432":1,"EPSG:2433":1,"EPSG:2434":1,"EPSG:2435":1,"EPSG:2436":1,"EPSG:2437":1,"EPSG:2438":1,"EPSG:2439":1,"EPSG:2440":1,"EPSG:2441":1,"EPSG:2442":1,"EPSG:2443":1,"EPSG:2444":1,"EPSG:2445":1,"EPSG:2446":1,"EPSG:2447":1,"EPSG:2448":1,"EPSG:2449":1,"EPSG:2450":1,"EPSG:2451":1,"EPSG:2452":1,"EPSG:2453":1,"EPSG:2454":1,"EPSG:2455":1,"EPSG:2456":1,"EPSG:2457":1,"EPSG:2458":1,"EPSG:2459":1,"EPSG:2460":1,"EPSG:2461":1,"EPSG:2462":1,"EPSG:2463":1,"EPSG:2464":1,"EPSG:2465":1,"EPSG:2466":1,"EPSG:2467":1,"EPSG:2468":1,"EPSG:2469":1,"EPSG:2470":1,"EPSG:2471":1,"EPSG:2472":1,"EPSG:2473":1,"EPSG:2474":1,"EPSG:2475":1,"EPSG:2476":1,"EPSG:2477":1,"EPSG:2478":1,"EPSG:2479":1,"EPSG:2480":1,"EPSG:2481":1,"EPSG:2482":1,"EPSG:2483":1,"EPSG:2484":1,"EPSG:2485":1,"EPSG:2486":1,"EPSG:2487":1,"EPSG:2488":1,"EPSG:2489":1,"EPSG:2490":1,"EPSG:2491":1,"EPSG:2492":1,"EPSG:2493":1,"EPSG:2494":1,"EPSG:2495":1,"EPSG:2496":1,"EPSG:2497":1,"EPSG:2498":1,"EPSG:2499":1,"EPSG:2500":1,"EPSG:2501":1,"EPSG:2502":1,"EPSG:2503":1,"EPSG:2504":1,"EPSG:2505":1,"EPSG:2506":1,"EPSG:2507":1,"EPSG:2508":1,"EPSG:2509":1,"EPSG:2510":1,"EPSG:2511":1,"EPSG:2512":1,"EPSG:2513":1,"EPSG:2514":1,"EPSG:2515":1,"EPSG:2516":1,"EPSG:2517":1,"EPSG:2518":1,"EPSG:2519":1,"EPSG:2520":1,"EPSG:2521":1,"EPSG:2522":1,"EPSG:2523":1,"EPSG:2524":1,"EPSG:2525":1,"EPSG:2526":1,"EPSG:2527":1,"EPSG:2528":1,"EPSG:2529":1,"EPSG:2530":1,"EPSG:2531":1,"EPSG:2532":1,"EPSG:2533":1,"EPSG:2534":1,"EPSG:2535":1,"EPSG:2536":1,"EPSG:2537":1,"EPSG:2538":1,"EPSG:2539":1,"EPSG:2540":1,"EPSG:2541":1,"EPSG:2542":1,"EPSG:2543":1,"EPSG:2544":1,"EPSG:2545":1,"EPSG:2546":1,"EPSG:2547":1,"EPSG:2548":1,"EPSG:2549":1,"EPSG:2551":1,"EPSG:2552":1,"EPSG:2553":1,"EPSG:2554":1,"EPSG:2555":1,"EPSG:2556":1,"EPSG:2557":1,"EPSG:2558":1,"EPSG:2559":1,"EPSG:2560":1,"EPSG:2561":1,"EPSG:2562":1,"EPSG:2563":1,"EPSG:2564":1,"EPSG:2565":1,"EPSG:2566":1,"EPSG:2567":1,"EPSG:2568":1,"EPSG:2569":1,"EPSG:2570":1,"EPSG:2571":1,"EPSG:2572":1,"EPSG:2573":1,"EPSG:2574":1,"EPSG:2575":1,"EPSG:2576":1,"EPSG:2577":1,"EPSG:2578":1,"EPSG:2579":1,"EPSG:2580":1,"EPSG:2581":1,"EPSG:2582":1,"EPSG:2583":1,"EPSG:2584":1,"EPSG:2585":1,"EPSG:2586":1,"EPSG:2587":1,"EPSG:2588":1,"EPSG:2589":1,"EPSG:2590":1,"EPSG:2591":1,"EPSG:2592":1,"EPSG:2593":1,"EPSG:2594":1,"EPSG:2595":1,"EPSG:2596":1,"EPSG:2597":1,"EPSG:2598":1,"EPSG:2599":1,"EPSG:2600":1,"EPSG:2601":1,"EPSG:2602":1,"EPSG:2603":1,"EPSG:2604":1,"EPSG:2605":1,"EPSG:2606":1,"EPSG:2607":1,"EPSG:2608":1,"EPSG:2609":1,"EPSG:2610":1,"EPSG:2611":1,"EPSG:2612":1,"EPSG:2613":1,"EPSG:2614":1,"EPSG:2615":1,"EPSG:2616":1,"EPSG:2617":1,"EPSG:2618":1,"EPSG:2619":1,"EPSG:2620":1,"EPSG:2621":1,"EPSG:2622":1,"EPSG:2623":1,"EPSG:2624":1,"EPSG:2625":1,"EPSG:2626":1,"EPSG:2627":1,"EPSG:2628":1,"EPSG:2629":1,"EPSG:2630":1,"EPSG:2631":1,"EPSG:2632":1,"EPSG:2633":1,"EPSG:2634":1,"EPSG:2635":1,"EPSG:2636":1,"EPSG:2637":1,"EPSG:2638":1,"EPSG:2639":1,"EPSG:2640":1,"EPSG:2641":1,"EPSG:2642":1,"EPSG:2643":1,"EPSG:2644":1,"EPSG:2645":1,"EPSG:2646":1,"EPSG:2647":1,"EPSG:2648":1,"EPSG:2649":1,"EPSG:2650":1,"EPSG:2651":1,"EPSG:2652":1,"EPSG:2653":1,"EPSG:2654":1,"EPSG:2655":1,"EPSG:2656":1,"EPSG:2657":1,"EPSG:2658":1,"EPSG:2659":1,"EPSG:2660":1,"EPSG:2661":1,"EPSG:2662":1,"EPSG:2663":1,"EPSG:2664":1,"EPSG:2665":1,"EPSG:2666":1,"EPSG:2667":1,"EPSG:2668":1,"EPSG:2669":1,"EPSG:2670":1,"EPSG:2671":1,"EPSG:2672":1,"EPSG:2673":1,"EPSG:2674":1,"EPSG:2675":1,"EPSG:2676":1,"EPSG:2677":1,"EPSG:2678":1,"EPSG:2679":1,"EPSG:2680":1,"EPSG:2681":1,"EPSG:2682":1,"EPSG:2683":1,"EPSG:2684":1,"EPSG:2685":1,"EPSG:2686":1,"EPSG:2687":1,"EPSG:2688":1,"EPSG:2689":1,"EPSG:2690":1,"EPSG:2691":1,"EPSG:2692":1,"EPSG:2693":1,"EPSG:2694":1,"EPSG:2695":1,"EPSG:2696":1,"EPSG:2697":1,"EPSG:2698":1,"EPSG:2699":1,"EPSG:2700":1,"EPSG:2701":1,"EPSG:2702":1,"EPSG:2703":1,"EPSG:2704":1,"EPSG:2705":1,"EPSG:2706":1,"EPSG:2707":1,"EPSG:2708":1,"EPSG:2709":1,"EPSG:2710":1,"EPSG:2711":1,"EPSG:2712":1,"EPSG:2713":1,"EPSG:2714":1,"EPSG:2715":1,"EPSG:2716":1,"EPSG:2717":1,"EPSG:2718":1,"EPSG:2719":1,"EPSG:2720":1,"EPSG:2721":1,"EPSG:2722":1,"EPSG:2723":1,"EPSG:2724":1,"EPSG:2725":1,"EPSG:2726":1,"EPSG:2727":1,"EPSG:2728":1,"EPSG:2729":1,"EPSG:2730":1,"EPSG:2731":1,"EPSG:2732":1,"EPSG:2733":1,"EPSG:2734":1,"EPSG:2735":1,"EPSG:2738":1,"EPSG:2739":1,"EPSG:2740":1,"EPSG:2741":1,"EPSG:2742":1,"EPSG:2743":1,"EPSG:2744":1,"EPSG:2745":1,"EPSG:2746":1,"EPSG:2747":1,"EPSG:2748":1,"EPSG:2749":1,"EPSG:2750":1,"EPSG:2751":1,"EPSG:2752":1,"EPSG:2753":1,"EPSG:2754":1,"EPSG:2755":1,"EPSG:2756":1,"EPSG:2757":1,"EPSG:2758":1,"EPSG:2935":1,"EPSG:2936":1,"EPSG:2937":1,"EPSG:2938":1,"EPSG:2939":1,"EPSG:2940":1,"EPSG:2941":1,"EPSG:2953":1,"EPSG:2963":1,"EPSG:3006":1,"EPSG:3007":1,"EPSG:3008":1,"EPSG:3009":1,"EPSG:3010":1,"EPSG:3011":1,"EPSG:3012":1,"EPSG:3013":1,"EPSG:3014":1,"EPSG:3015":1,"EPSG:3016":1,"EPSG:3017":1,"EPSG:3018":1,"EPSG:3019":1,"EPSG:3020":1,"EPSG:3021":1,"EPSG:3022":1,"EPSG:3023":1,"EPSG:3024":1,"EPSG:3025":1,"EPSG:3026":1,"EPSG:3027":1,"EPSG:3028":1,"EPSG:3029":1,"EPSG:3030":1,"EPSG:3034":1,"EPSG:3035":1,"EPSG:3038":1,"EPSG:3039":1,"EPSG:3040":1,"EPSG:3041":1,"EPSG:3042":1,"EPSG:3043":1,"EPSG:3044":1,"EPSG:3045":1,"EPSG:3046":1,"EPSG:3047":1,"EPSG:3048":1,"EPSG:3049":1,"EPSG:3050":1,"EPSG:3051":1,"EPSG:3058":1,"EPSG:3059":1,"EPSG:3068":1,"EPSG:3114":1,"EPSG:3115":1,"EPSG:3116":1,"EPSG:3117":1,"EPSG:3118":1,"EPSG:3120":1,"EPSG:3126":1,"EPSG:3127":1,"EPSG:3128":1,"EPSG:3129":1,"EPSG:3130":1,"EPSG:3131":1,"EPSG:3132":1,"EPSG:3133":1,"EPSG:3134":1,"EPSG:3135":1,"EPSG:3136":1,"EPSG:3137":1,"EPSG:3138":1,"EPSG:3139":1,"EPSG:3140":1,"EPSG:3146":1,"EPSG:3147":1,"EPSG:3150":1,"EPSG:3151":1,"EPSG:3152":1,"EPSG:3300":1,"EPSG:3301":1,"EPSG:3328":1,"EPSG:3329":1,"EPSG:3330":1,"EPSG:3331":1,"EPSG:3332":1,"EPSG:3333":1,"EPSG:3334":1,"EPSG:3335":1,"EPSG:3346":1,"EPSG:3350":1,"EPSG:3351":1,"EPSG:3352":1,"EPSG:3366":1,"EPSG:3386":1,"EPSG:3387":1,"EPSG:3388":1,"EPSG:3389":1,"EPSG:3390":1,"EPSG:3396":1,"EPSG:3397":1,"EPSG:3398":1,"EPSG:3399":1,"EPSG:3407":1,"EPSG:3414":1,"EPSG:3416":1,"EPSG:3764":1,"EPSG:3788":1,"EPSG:3789":1,"EPSG:3790":1,"EPSG:3791":1,"EPSG:3793":1,"EPSG:3795":1,"EPSG:3796":1,"EPSG:3819":1,"EPSG:3821":1,"EPSG:3823":1,"EPSG:3824":1,"EPSG:3833":1,"EPSG:3834":1,"EPSG:3835":1,"EPSG:3836":1,"EPSG:3837":1,"EPSG:3838":1,"EPSG:3839":1,"EPSG:3840":1,"EPSG:3841":1,"EPSG:3842":1,"EPSG:3843":1,"EPSG:3844":1,"EPSG:3845":1,"EPSG:3846":1,"EPSG:3847":1,"EPSG:3848":1,"EPSG:3849":1,"EPSG:3850":1,"EPSG:3851":1,"EPSG:3852":1,"EPSG:3854":1,"EPSG:3873":1,"EPSG:3874":1,"EPSG:3875":1,"EPSG:3876":1,"EPSG:3877":1,"EPSG:3878":1,"EPSG:3879":1,"EPSG:3880":1,"EPSG:3881":1,"EPSG:3882":1,"EPSG:3883":1,"EPSG:3884":1,"EPSG:3885":1,"EPSG:3888":1,"EPSG:3889":1,"EPSG:3906":1,"EPSG:3907":1,"EPSG:3908":1,"EPSG:3909":1,"EPSG:3910":1,"EPSG:3911":1,"EPSG:4001":1,"EPSG:4002":1,"EPSG:4003":1,"EPSG:4004":1,"EPSG:4005":1,"EPSG:4006":1,"EPSG:4007":1,"EPSG:4008":1,"EPSG:4009":1,"EPSG:4010":1,"EPSG:4011":1,"EPSG:4012":1,"EPSG:4013":1,"EPSG:4014":1,"EPSG:4015":1,"EPSG:4016":1,"EPSG:4017":1,"EPSG:4018":1,"EPSG:4019":1,"EPSG:4020":1,"EPSG:4021":1,"EPSG:4022":1,"EPSG:4023":1,"EPSG:4024":1,"EPSG:4025":1,"EPSG:4026":1,"EPSG:4027":1,"EPSG:4028":1,"EPSG:4029":1,"EPSG:4030":1,"EPSG:4031":1,"EPSG:4032":1,"EPSG:4033":1,"EPSG:4034":1,"EPSG:4035":1,"EPSG:4036":1,"EPSG:4037":1,"EPSG:4038":1,"EPSG:4040":1,"EPSG:4041":1,"EPSG:4042":1,"EPSG:4043":1,"EPSG:4044":1,"EPSG:4045":1,"EPSG:4046":1,"EPSG:4047":1,"EPSG:4052":1,"EPSG:4053":1,"EPSG:4054":1,"EPSG:4055":1,"EPSG:4074":1,"EPSG:4075":1,"EPSG:4080":1,"EPSG:4081":1,"EPSG:4120":1,"EPSG:4121":1,"EPSG:4122":1,"EPSG:4123":1,"EPSG:4124":1,"EPSG:4125":1,"EPSG:4126":1,"EPSG:4127":1,"EPSG:4128":1,"EPSG:4129":1,"EPSG:4130":1,"EPSG:4131":1,"EPSG:4132":1,"EPSG:4133":1,"EPSG:4134":1,"EPSG:4135":1,"EPSG:4136":1,"EPSG:4137":1,"EPSG:4138":1,"EPSG:4139":1,"EPSG:4140":1,"EPSG:4141":1,"EPSG:4142":1,"EPSG:4143":1,"EPSG:4144":1,"EPSG:4145":1,"EPSG:4146":1,"EPSG:4147":1,"EPSG:4148":1,"EPSG:4149":1,"EPSG:4150":1,"EPSG:4151":1,"EPSG:4152":1,"EPSG:4153":1,"EPSG:4154":1,"EPSG:4155":1,"EPSG:4156":1,"EPSG:4157":1,"EPSG:4158":1,"EPSG:4159":1,"EPSG:4160":1,"EPSG:4161":1,"EPSG:4162":1,"EPSG:4163":1,"EPSG:4164":1,"EPSG:4165":1,"EPSG:4166":1,"EPSG:4167":1,"EPSG:4168":1,"EPSG:4169":1,"EPSG:4170":1,"EPSG:4171":1,"EPSG:4172":1,"EPSG:4173":1,"EPSG:4174":1,"EPSG:4175":1,"EPSG:4176":1,"EPSG:4178":1,"EPSG:4179":1,"EPSG:4180":1,"EPSG:4181":1,"EPSG:4182":1,"EPSG:4183":1,"EPSG:4184":1,"EPSG:4185":1,"EPSG:4188":1,"EPSG:4189":1,"EPSG:4190":1,"EPSG:4191":1,"EPSG:4192":1,"EPSG:4193":1,"EPSG:4194":1,"EPSG:4195":1,"EPSG:4196":1,"EPSG:4197":1,"EPSG:4198":1,"EPSG:4199":1,"EPSG:4200":1,"EPSG:4201":1,"EPSG:4202":1,"EPSG:4203":1,"EPSG:4204":1,"EPSG:4205":1,"EPSG:4206":1,"EPSG:4207":1,"EPSG:4208":1,"EPSG:4209":1,"EPSG:4210":1,"EPSG:4211":1,"EPSG:4212":1,"EPSG:4213":1,"EPSG:4214":1,"EPSG:4215":1,"EPSG:4216":1,"EPSG:4218":1,"EPSG:4219":1,"EPSG:4220":1,"EPSG:4221":1,"EPSG:4222":1,"EPSG:4223":1,"EPSG:4224":1,"EPSG:4225":1,"EPSG:4226":1,"EPSG:4227":1,"EPSG:4228":1,"EPSG:4229":1,"EPSG:4230":1,"EPSG:4231":1,"EPSG:4232":1,"EPSG:4233":1,"EPSG:4234":1,"EPSG:4235":1,"EPSG:4236":1,"EPSG:4237":1,"EPSG:4238":1,"EPSG:4239":1,"EPSG:4240":1,"EPSG:4241":1,"EPSG:4242":1,"EPSG:4243":1,"EPSG:4244":1,"EPSG:4245":1,"EPSG:4246":1,"EPSG:4247":1,"EPSG:4248":1,"EPSG:4249":1,"EPSG:4250":1,"EPSG:4251":1,"EPSG:4252":1,"EPSG:4253":1,"EPSG:4254":1,"EPSG:4255":1,"EPSG:4256":1,"EPSG:4257":1,"EPSG:4258":1,"EPSG:4259":1,"EPSG:4260":1,"EPSG:4261":1,"EPSG:4262":1,"EPSG:4263":1,"EPSG:4264":1,"EPSG:4265":1,"EPSG:4266":1,"EPSG:4267":1,"EPSG:4268":1,"EPSG:4269":1,"EPSG:4270":1,"EPSG:4271":1,"EPSG:4272":1,"EPSG:4273":1,"EPSG:4274":1,"EPSG:4275":1,"EPSG:4276":1,"EPSG:4277":1,"EPSG:4278":1,"EPSG:4279":1,"EPSG:4280":1,"EPSG:4281":1,"EPSG:4282":1,"EPSG:4283":1,"EPSG:4284":1,"EPSG:4285":1,"EPSG:4286":1,"EPSG:4287":1,"EPSG:4288":1,"EPSG:4289":1,"EPSG:4291":1,"EPSG:4292":1,"EPSG:4293":1,"EPSG:4294":1,"EPSG:4295":1,"EPSG:4296":1,"EPSG:4297":1,"EPSG:4298":1,"EPSG:4299":1,"EPSG:4300":1,"EPSG:4301":1,"EPSG:4302":1,"EPSG:4303":1,"EPSG:4304":1,"EPSG:4306":1,"EPSG:4307":1,"EPSG:4308":1,"EPSG:4309":1,"EPSG:4310":1,"EPSG:4311":1,"EPSG:4312":1,"EPSG:4313":1,"EPSG:4314":1,"EPSG:4315":1,"EPSG:4316":1,"EPSG:4317":1,"EPSG:4318":1,"EPSG:4319":1,"EPSG:4322":1,"EPSG:4324":1,"EPSG:4326":1,"EPSG:4327":1,"EPSG:4329":1,"EPSG:4339":1,"EPSG:4341":1,"EPSG:4343":1,"EPSG:4345":1,"EPSG:4347":1,"EPSG:4349":1,"EPSG:4351":1,"EPSG:4353":1,"EPSG:4355":1,"EPSG:4357":1,"EPSG:4359":1,"EPSG:4361":1,"EPSG:4363":1,"EPSG:4365":1,"EPSG:4367":1,"EPSG:4369":1,"EPSG:4371":1,"EPSG:4373":1,"EPSG:4375":1,"EPSG:4377":1,"EPSG:4379":1,"EPSG:4381":1,"EPSG:4383":1,"EPSG:4386":1,"EPSG:4388":1,"EPSG:4417":1,"EPSG:4434":1,"EPSG:4463":1,"EPSG:4466":1,"EPSG:4469":1,"EPSG:4470":1,"EPSG:4472":1,"EPSG:4475":1,"EPSG:4480":1,"EPSG:4482":1,"EPSG:4483":1,"EPSG:4490":1,"EPSG:4491":1,"EPSG:4492":1,"EPSG:4493":1,"EPSG:4494":1,"EPSG:4495":1,"EPSG:4496":1,"EPSG:4497":1,"EPSG:4498":1,"EPSG:4499":1,"EPSG:4500":1,"EPSG:4501":1,"EPSG:4502":1,"EPSG:4503":1,"EPSG:4504":1,"EPSG:4505":1,"EPSG:4506":1,"EPSG:4507":1,"EPSG:4508":1,"EPSG:4509":1,"EPSG:4510":1,"EPSG:4511":1,"EPSG:4512":1,"EPSG:4513":1,"EPSG:4514":1,"EPSG:4515":1,"EPSG:4516":1,"EPSG:4517":1,"EPSG:4518":1,"EPSG:4519":1,"EPSG:4520":1,"EPSG:4521":1,"EPSG:4522":1,"EPSG:4523":1,"EPSG:4524":1,"EPSG:4525":1,"EPSG:4526":1,"EPSG:4527":1,"EPSG:4528":1,"EPSG:4529":1,"EPSG:4530":1,"EPSG:4531":1,"EPSG:4532":1,"EPSG:4533":1,"EPSG:4534":1,"EPSG:4535":1,"EPSG:4536":1,"EPSG:4537":1,"EPSG:4538":1,"EPSG:4539":1,"EPSG:4540":1,"EPSG:4541":1,"EPSG:4542":1,"EPSG:4543":1,"EPSG:4544":1,"EPSG:4545":1,"EPSG:4546":1,"EPSG:4547":1,"EPSG:4548":1,"EPSG:4549":1,"EPSG:4550":1,"EPSG:4551":1,"EPSG:4552":1,"EPSG:4553":1,"EPSG:4554":1,"EPSG:4555":1,"EPSG:4557":1,"EPSG:4558":1,"EPSG:4568":1,"EPSG:4569":1,"EPSG:4570":1,"EPSG:4571":1,"EPSG:4572":1,"EPSG:4573":1,"EPSG:4574":1,"EPSG:4575":1,"EPSG:4576":1,"EPSG:4577":1,"EPSG:4578":1,"EPSG:4579":1,"EPSG:4580":1,"EPSG:4581":1,"EPSG:4582":1,"EPSG:4583":1,"EPSG:4584":1,"EPSG:4585":1,"EPSG:4586":1,"EPSG:4587":1,"EPSG:4588":1,"EPSG:4589":1,"EPSG:4600":1,"EPSG:4601":1,"EPSG:4602":1,"EPSG:4603":1,"EPSG:4604":1,"EPSG:4605":1,"EPSG:4606":1,"EPSG:4607":1,"EPSG:4608":1,"EPSG:4609":1,"EPSG:4610":1,"EPSG:4611":1,"EPSG:4612":1,"EPSG:4613":1,"EPSG:4614":1,"EPSG:4615":1,"EPSG:4616":1,"EPSG:4617":1,"EPSG:4618":1,"EPSG:4619":1,"EPSG:4620":1,"EPSG:4621":1,"EPSG:4622":1,"EPSG:4623":1,"EPSG:4624":1,"EPSG:4625":1,"EPSG:4626":1,"EPSG:4627":1,"EPSG:4628":1,"EPSG:4629":1,"EPSG:4630":1,"EPSG:4631":1,"EPSG:4632":1,"EPSG:4633":1,"EPSG:4634":1,"EPSG:4635":1,"EPSG:4636":1,"EPSG:4637":1,"EPSG:4638":1,"EPSG:4639":1,"EPSG:4640":1,"EPSG:4641":1,"EPSG:4642":1,"EPSG:4643":1,"EPSG:4644":1,"EPSG:4645":1,"EPSG:4646":1,"EPSG:4652":1,"EPSG:4653":1,"EPSG:4654":1,"EPSG:4655":1,"EPSG:4656":1,"EPSG:4657":1,"EPSG:4658":1,"EPSG:4659":1,"EPSG:4660":1,"EPSG:4661":1,"EPSG:4662":1,"EPSG:4663":1,"EPSG:4664":1,"EPSG:4665":1,"EPSG:4666":1,"EPSG:4667":1,"EPSG:4668":1,"EPSG:4669":1,"EPSG:4670":1,"EPSG:4671":1,"EPSG:4672":1,"EPSG:4673":1,"EPSG:4674":1,"EPSG:4675":1,"EPSG:4676":1,"EPSG:4677":1,"EPSG:4678":1,"EPSG:4679":1,"EPSG:4680":1,"EPSG:4681":1,"EPSG:4682":1,"EPSG:4683":1,"EPSG:4684":1,"EPSG:4685":1,"EPSG:4686":1,"EPSG:4687":1,"EPSG:4688":1,"EPSG:4689":1,"EPSG:4690":1,"EPSG:4691":1,"EPSG:4692":1,"EPSG:4693":1,"EPSG:4694":1,"EPSG:4695":1,"EPSG:4696":1,"EPSG:4697":1,"EPSG:4698":1,"EPSG:4699":1,"EPSG:4700":1,"EPSG:4701":1,"EPSG:4702":1,"EPSG:4703":1,"EPSG:4704":1,"EPSG:4705":1,"EPSG:4706":1,"EPSG:4707":1,"EPSG:4708":1,"EPSG:4709":1,"EPSG:4710":1,"EPSG:4711":1,"EPSG:4712":1,"EPSG:4713":1,"EPSG:4714":1,"EPSG:4715":1,"EPSG:4716":1,"EPSG:4717":1,"EPSG:4718":1,"EPSG:4719":1,"EPSG:4720":1,"EPSG:4721":1,"EPSG:4722":1,"EPSG:4723":1,"EPSG:4724":1,"EPSG:4725":1,"EPSG:4726":1,"EPSG:4727":1,"EPSG:4728":1,"EPSG:4729":1,"EPSG:4730":1,"EPSG:4731":1,"EPSG:4732":1,"EPSG:4733":1,"EPSG:4734":1,"EPSG:4735":1,"EPSG:4736":1,"EPSG:4737":1,"EPSG:4738":1,"EPSG:4739":1,"EPSG:4740":1,"EPSG:4741":1,"EPSG:4742":1,"EPSG:4743":1,"EPSG:4744":1,"EPSG:4745":1,"EPSG:4746":1,"EPSG:4747":1,"EPSG:4748":1,"EPSG:4749":1,"EPSG:4750":1,"EPSG:4751":1,"EPSG:4752":1,"EPSG:4753":1,"EPSG:4754":1,"EPSG:4755":1,"EPSG:4756":1,"EPSG:4757":1,"EPSG:4758":1,"EPSG:4759":1,"EPSG:4760":1,"EPSG:4761":1,"EPSG:4762":1,"EPSG:4763":1,"EPSG:4764":1,"EPSG:4765":1,"EPSG:4766":1,"EPSG:4767":1,"EPSG:4768":1,"EPSG:4769":1,"EPSG:4770":1,"EPSG:4771":1,"EPSG:4772":1,"EPSG:4773":1,"EPSG:4774":1,"EPSG:4775":1,"EPSG:4776":1,"EPSG:4777":1,"EPSG:4778":1,"EPSG:4779":1,"EPSG:4780":1,"EPSG:4781":1,"EPSG:4782":1,"EPSG:4783":1,"EPSG:4784":1,"EPSG:4785":1,"EPSG:4786":1,"EPSG:4787":1,"EPSG:4788":1,"EPSG:4789":1,"EPSG:4790":1,"EPSG:4791":1,"EPSG:4792":1,"EPSG:4793":1,"EPSG:4794":1,"EPSG:4795":1,"EPSG:4796":1,"EPSG:4797":1,"EPSG:4798":1,"EPSG:4799":1,"EPSG:4800":1,"EPSG:4801":1,"EPSG:4802":1,"EPSG:4803":1,"EPSG:4804":1,"EPSG:4805":1,"EPSG:4806":1,"EPSG:4807":1,"EPSG:4808":1,"EPSG:4809":1,"EPSG:4810":1,"EPSG:4811":1,"EPSG:4812":1,"EPSG:4813":1,"EPSG:4814":1,"EPSG:4815":1,"EPSG:4816":1,"EPSG:4817":1,"EPSG:4818":1,"EPSG:4819":1,"EPSG:4820":1,"EPSG:4821":1,"EPSG:4822":1,"EPSG:4823":1,"EPSG:4824":1,"EPSG:4839":1,"EPSG:4855":1,"EPSG:4856":1,"EPSG:4857":1,"EPSG:4858":1,"EPSG:4859":1,"EPSG:4860":1,"EPSG:4861":1,"EPSG:4862":1,"EPSG:4863":1,"EPSG:4864":1,"EPSG:4865":1,"EPSG:4866":1,"EPSG:4867":1,"EPSG:4868":1,"EPSG:4869":1,"EPSG:4870":1,"EPSG:4871":1,"EPSG:4872":1,"EPSG:4873":1,"EPSG:4874":1,"EPSG:4875":1,"EPSG:4876":1,"EPSG:4877":1,"EPSG:4878":1,"EPSG:4879":1,"EPSG:4880":1,"EPSG:4883":1,"EPSG:4885":1,"EPSG:4887":1,"EPSG:4889":1,"EPSG:4891":1,"EPSG:4893":1,"EPSG:4895":1,"EPSG:4898":1,"EPSG:4900":1,"EPSG:4901":1,"EPSG:4902":1,"EPSG:4903":1,"EPSG:4904":1,"EPSG:4907":1,"EPSG:4909":1,"EPSG:4921":1,"EPSG:4923":1,"EPSG:4925":1,"EPSG:4927":1,"EPSG:4929":1,"EPSG:4931":1,"EPSG:4933":1,"EPSG:4935":1,"EPSG:4937":1,"EPSG:4939":1,"EPSG:4941":1,"EPSG:4943":1,"EPSG:4945":1,"EPSG:4947":1,"EPSG:4949":1,"EPSG:4951":1,"EPSG:4953":1,"EPSG:4955":1,"EPSG:4957":1,"EPSG:4959":1,"EPSG:4961":1,"EPSG:4963":1,"EPSG:4965":1,"EPSG:4967":1,"EPSG:4969":1,"EPSG:4971":1,"EPSG:4973":1,"EPSG:4975":1,"EPSG:4977":1,"EPSG:4979":1,"EPSG:4981":1,"EPSG:4983":1,"EPSG:4985":1,"EPSG:4987":1,"EPSG:4989":1,"EPSG:4991":1,"EPSG:4993":1,"EPSG:4995":1,"EPSG:4997":1,"EPSG:4999":1,"EPSG:5012":1,"EPSG:5013":1,"EPSG:5017":1,"EPSG:5048":1,"EPSG:5105":1,"EPSG:5106":1,"EPSG:5107":1,"EPSG:5108":1,"EPSG:5109":1,"EPSG:5110":1,"EPSG:5111":1,"EPSG:5112":1,"EPSG:5113":1,"EPSG:5114":1,"EPSG:5115":1,"EPSG:5116":1,"EPSG:5117":1,"EPSG:5118":1,"EPSG:5119":1,"EPSG:5120":1,"EPSG:5121":1,"EPSG:5122":1,"EPSG:5123":1,"EPSG:5124":1,"EPSG:5125":1,"EPSG:5126":1,"EPSG:5127":1,"EPSG:5128":1,"EPSG:5129":1,"EPSG:5130":1,"EPSG:5132":1,"EPSG:5167":1,"EPSG:5168":1,"EPSG:5169":1,"EPSG:5170":1,"EPSG:5171":1,"EPSG:5172":1,"EPSG:5173":1,"EPSG:5174":1,"EPSG:5175":1,"EPSG:5176":1,"EPSG:5177":1,"EPSG:5178":1,"EPSG:5179":1,"EPSG:5180":1,"EPSG:5181":1,"EPSG:5182":1,"EPSG:5183":1,"EPSG:5184":1,"EPSG:5185":1,"EPSG:5186":1,"EPSG:5187":1,"EPSG:5188":1,"EPSG:5224":1,"EPSG:5228":1,"EPSG:5229":1,"EPSG:5233":1,"EPSG:5245":1,"EPSG:5246":1,"EPSG:5251":1,"EPSG:5252":1,"EPSG:5253":1,"EPSG:5254":1,"EPSG:5255":1,"EPSG:5256":1,"EPSG:5257":1,"EPSG:5258":1,"EPSG:5259":1,"EPSG:5263":1,"EPSG:5264":1,"EPSG:5269":1,"EPSG:5270":1,"EPSG:5271":1,"EPSG:5272":1,"EPSG:5273":1,"EPSG:5274":1,"EPSG:5275":1,"EPSG:5801":1,"EPSG:5802":1,"EPSG:5803":1,"EPSG:5804":1,"EPSG:5808":1,"EPSG:5809":1,"EPSG:5810":1,"EPSG:5811":1,"EPSG:5812":1,"EPSG:5813":1,"EPSG:5814":1,"EPSG:5815":1,"EPSG:5816":1,"EPSG:20004":1,"EPSG:20005":1,"EPSG:20006":1,"EPSG:20007":1,"EPSG:20008":1,"EPSG:20009":1,"EPSG:20010":1,"EPSG:20011":1,"EPSG:20012":1,"EPSG:20013":1,"EPSG:20014":1,"EPSG:20015":1,"EPSG:20016":1,"EPSG:20017":1,"EPSG:20018":1,"EPSG:20019":1,"EPSG:20020":1,"EPSG:20021":1,"EPSG:20022":1,"EPSG:20023":1,"EPSG:20024":1,"EPSG:20025":1,"EPSG:20026":1,"EPSG:20027":1,"EPSG:20028":1,"EPSG:20029":1,"EPSG:20030":1,"EPSG:20031":1,"EPSG:20032":1,"EPSG:20064":1,"EPSG:20065":1,"EPSG:20066":1,"EPSG:20067":1,"EPSG:20068":1,"EPSG:20069":1,"EPSG:20070":1,"EPSG:20071":1,"EPSG:20072":1,"EPSG:20073":1,"EPSG:20074":1,"EPSG:20075":1,"EPSG:20076":1,"EPSG:20077":1,"EPSG:20078":1,"EPSG:20079":1,"EPSG:20080":1,"EPSG:20081":1,"EPSG:20082":1,"EPSG:20083":1,"EPSG:20084":1,"EPSG:20085":1,"EPSG:20086":1,"EPSG:20087":1,"EPSG:20088":1,"EPSG:20089":1,"EPSG:20090":1,"EPSG:20091":1,"EPSG:20092":1,"EPSG:21413":1,"EPSG:21414":1,"EPSG:21415":1,"EPSG:21416":1,"EPSG:21417":1,"EPSG:21418":1,"EPSG:21419":1,"EPSG:21420":1,"EPSG:21421":1,"EPSG:21422":1,"EPSG:21423":1,"EPSG:21453":1,"EPSG:21454":1,"EPSG:21455":1,"EPSG:21456":1,"EPSG:21457":1,"EPSG:21458":1,"EPSG:21459":1,"EPSG:21460":1,"EPSG:21461":1,"EPSG:21462":1,"EPSG:21463":1,"EPSG:21473":1,"EPSG:21474":1,"EPSG:21475":1,"EPSG:21476":1,"EPSG:21477":1,"EPSG:21478":1,"EPSG:21479":1,"EPSG:21480":1,"EPSG:21481":1,"EPSG:21482":1,"EPSG:21483":1,"EPSG:21896":1,"EPSG:21897":1,"EPSG:21898":1,"EPSG:21899":1,"EPSG:22171":1,"EPSG:22172":1,"EPSG:22173":1,"EPSG:22174":1,"EPSG:22175":1,"EPSG:22176":1,"EPSG:22177":1,"EPSG:22181":1,"EPSG:22182":1,"EPSG:22183":1,"EPSG:22184":1,"EPSG:22185":1,"EPSG:22186":1,"EPSG:22187":1,"EPSG:22191":1,"EPSG:22192":1,"EPSG:22193":1,"EPSG:22194":1,"EPSG:22195":1,"EPSG:22196":1,"EPSG:22197":1,"EPSG:25884":1,"EPSG:27205":1,"EPSG:27206":1,"EPSG:27207":1,"EPSG:27208":1,"EPSG:27209":1,"EPSG:27210":1,"EPSG:27211":1,"EPSG:27212":1,"EPSG:27213":1,"EPSG:27214":1,"EPSG:27215":1,"EPSG:27216":1,"EPSG:27217":1,"EPSG:27218":1,"EPSG:27219":1,"EPSG:27220":1,"EPSG:27221":1,"EPSG:27222":1,"EPSG:27223":1,"EPSG:27224":1,"EPSG:27225":1,"EPSG:27226":1,"EPSG:27227":1,"EPSG:27228":1,"EPSG:27229":1,"EPSG:27230":1,"EPSG:27231":1,"EPSG:27232":1,"EPSG:27391":1,"EPSG:27392":1,"EPSG:27393":1,"EPSG:27394":1,"EPSG:27395":1,"EPSG:27396":1,"EPSG:27397":1,"EPSG:27398":1,"EPSG:27492":1,"EPSG:28402":1,"EPSG:28403":1,"EPSG:28404":1,"EPSG:28405":1,"EPSG:28406":1,"EPSG:28407":1,"EPSG:28408":1,"EPSG:28409":1,"EPSG:28410":1,"EPSG:28411":1,"EPSG:28412":1,"EPSG:28413":1,"EPSG:28414":1,"EPSG:28415":1,"EPSG:28416":1,"EPSG:28417":1,"EPSG:28418":1,"EPSG:28419":1,"EPSG:28420":1,"EPSG:28421":1,"EPSG:28422":1,"EPSG:28423":1,"EPSG:28424":1,"EPSG:28425":1,"EPSG:28426":1,"EPSG:28427":1,"EPSG:28428":1,"EPSG:28429":1,"EPSG:28430":1,"EPSG:28431":1,"EPSG:28432":1,"EPSG:28462":1,"EPSG:28463":1,"EPSG:28464":1,"EPSG:28465":1,"EPSG:28466":1,"EPSG:28467":1,"EPSG:28468":1,"EPSG:28469":1,"EPSG:28470":1,"EPSG:28471":1,"EPSG:28472":1,"EPSG:28473":1,"EPSG:28474":1,"EPSG:28475":1,"EPSG:28476":1,"EPSG:28477":1,"EPSG:28478":1,"EPSG:28479":1,"EPSG:28480":1,"EPSG:28481":1,"EPSG:28482":1,"EPSG:28483":1,"EPSG:28484":1,"EPSG:28485":1,"EPSG:28486":1,"EPSG:28487":1,"EPSG:28488":1,"EPSG:28489":1,"EPSG:28490":1,"EPSG:28491":1,"EPSG:28492":1,"EPSG:29701":1,"EPSG:29702":1,"EPSG:30161":1,"EPSG:30162":1,"EPSG:30163":1,"EPSG:30164":1,"EPSG:30165":1,"EPSG:30166":1,"EPSG:30167":1,"EPSG:30168":1,"EPSG:30169":1,"EPSG:30170":1,"EPSG:30171":1,"EPSG:30172":1,"EPSG:30173":1,"EPSG:30174":1,"EPSG:30175":1,"EPSG:30176":1,"EPSG:30177":1,"EPSG:30178":1,"EPSG:30179":1,"EPSG:30800":1,"EPSG:31251":1,"EPSG:31252":1,"EPSG:31253":1,"EPSG:31254":1,"EPSG:31255":1,"EPSG:31256":1,"EPSG:31257":1,"EPSG:31258":1,"EPSG:31259":1,"EPSG:31275":1,"EPSG:31276":1,"EPSG:31277":1,"EPSG:31278":1,"EPSG:31279":1,"EPSG:31281":1,"EPSG:31282":1,"EPSG:31283":1,"EPSG:31284":1,"EPSG:31285":1,"EPSG:31286":1,"EPSG:31287":1,"EPSG:31288":1,"EPSG:31289":1,"EPSG:31290":1,"EPSG:31466":1,"EPSG:31467":1,"EPSG:31468":1,"EPSG:31469":1,"EPSG:31700":1};
OpenLayers.Projection.getUTMZone=function(b){var a=""+(Math.floor(b/6)+31);
return a
};
OpenLayers.Projection.getMGRSZone=function(a,e){var c=["C","D","E","F","G","H","J","K","L","M","N","P","Q","R","S","T","U","V","W"];
var b;
if(e.lat<-80){if(e.lon<0){b="A"
}else{b="B"
}return b
}if(e.lat>84){if(e.lon<0){b="Y"
}else{b="Z"
}return b
}if(e.lat>72){if(e.lon<0||e.lon>42){b=a+"X"
}else{if(e.lon<=9){b="31X"
}else{if(e.lon<=21){b="33X"
}else{if(e.lon<=33){b="35X"
}else{b="37X"
}}}}return b
}var d=Math.abs(parseInt((e.lat+80)/8,10));
b=a+c[d];
if(b=="31V"&&e.lon>3){b="32V"
}return b
};
OpenLayers.Projection.transform=function(d,e,b){if(e&&b){if(window.Proj4js&&(e.proj instanceof Proj4js.Proj)&&(b.proj instanceof Proj4js.Proj)){if(!e.equals(b)){if(OpenLayers.Util.isArray(d)){for(var c=0,a=d.length;
c<a;
c++){d[c]=Proj4js.transform(e.proj,b.proj,d[c]);
if(d[c] instanceof OpenLayers.Geometry){d[c].bounds=null
}}}else{d=Proj4js.transform(e.proj,b.proj,d);
if(d instanceof OpenLayers.Geometry){d.bounds=null
}}}}else{if(typeof(e.getCode)=="function"){e=e.getCode()
}if(typeof(b.getCode)=="function"){b=b.getCode()
}if(OpenLayers.Projection.transforms[e]&&OpenLayers.Projection.transforms[e][b]){if(OpenLayers.Util.isArray(d)){for(var c=0,a=d.length;
c<a;
c++){OpenLayers.Projection.transforms[e][b](d[c]);
if(d[c] instanceof OpenLayers.Geometry){d[c].bounds=null
}}}else{OpenLayers.Projection.transforms[e][b](d);
if(d instanceof OpenLayers.Geometry){d.bounds=null
}}}}}return d
};
OpenLayers.Projection.CRS84=new OpenLayers.Projection("WGS84");
OpenLayers.Projection.PseudoMercator=new OpenLayers.Projection("EPSG:3857")
}if(OpenLayers.Map){OpenLayers.Map=OpenLayers.overload(OpenLayers.Map,{removeLayer:function(c,e){if(e==null){e=true
}if(c.isFixed){this.viewPortDiv.removeChild(c.div)
}else{if(this.layerContainerDiv.childNodes.length>0){this.layerContainerDiv.removeChild(c.div)
}}OpenLayers.Util.removeItem(this.layers,c);
c.removeMap(this);
c.map=null;
if(this.baseLayer==c){this.baseLayer=null;
if(e){for(var b=0,a=this.layers.length;
b<a;
b++){var d=this.layers[b];
if(d.isBaseLayer||this.allOverlays){this.setBaseLayer(d);
break
}}}}this.resetLayersZIndex();
this.events.triggerEvent("removelayer",{layer:c});
c.events.triggerEvent("removed",{map:this,layer:c})
},isValidZoomLevel:function(c){var a=(c!=null);
try{a=a&&(c>=(this.getRestrictedMinZoom()||0))
}catch(b){a=a&&(c>=0)
}a=a&&(c<this.getNumZoomLevels());
if(this.minZoomLevel!=undefined){a=a&&(c>=this.minZoomLevel)
}if(this.maxZoomLevel!=undefined){a=a&&(c<=this.maxZoomLevel)
}return a
},getProjection:function(){var a=null;
if(this.baseLayer!=null){a=this.baseLayer.projection
}else{a=this.projection
}if(a&&typeof(a)=="string"){a=new OpenLayers.Projection(a)
}return a?a:null
},getProjectionObject:function(){return this.getProjection()
},getDisplayProjection:function(){var a=null;
if(this.displayProjection){a=this.displayProjection
}else{a=this.getProjection()
}return a
},getMaxExtent:function(b){var a=null;
if(b&&b.restricted&&this.restrictedExtent){a=this.restrictedExtent
}else{if(this.baseLayer!=null){a=this.baseLayer.maxExtent
}}if(!a){this.maxExtent=new OpenLayers.Bounds(-180,-90,180,90);
this.maxExtent.transform(OpenLayers.Projection.CRS84,this.getProjection(),true);
a=this.maxExtent
}return a
}})
}if(OpenLayers.Layer){OpenLayers.Layer=OpenLayers.overload(OpenLayers.Layer,{savedStates:{},initialize:function(b,a){this.addOptions(a);
this.name=b;
if(this.id==null){this.id=OpenLayers.Util.createUniqueID(this.CLASS_NAME+"_");
this.div=OpenLayers.Util.createDiv(this.id);
this.div.style.width="100%";
this.div.style.height="100%";
this.div.dir="ltr";
this.events=new OpenLayers.Events(this,this.div,this.EVENT_TYPES);
if(this.eventListeners instanceof Object){this.events.on(this.eventListeners)
}}if(this.wrapDateLine){this.displayOutsideMaxExtent=true
}this.savedStates={};
if(this.GeoRM){if(!this.events){this.events=new OpenLayers.Events(this,this.div,this.EVENT_TYPES)
}this.events.register("loadstart",this,this.updateGeoRM);
this.events.register("move",this,this.updateGeoRM);
this.events.register("moveend",this,this.updateGeoRM)
}},destroy:function(a){if(a==null){a=true
}if(this.map!=null){this.map.removeLayer(this,a)
}this.projection=null;
this.map=null;
this.name=null;
this.div=null;
this.options=null;
this.savedStates=null;
if(this.GeoRM){this.events.unregister("moveend",this,this.updateGeoRM);
this.events.unregister("move",this,this.updateGeoRM);
this.events.unregister("loadstart",this,this.updateGeoRM)
}if(this.events){if(this.eventListeners){this.events.un(this.eventListeners)
}this.events.destroy()
}this.eventListeners=null;
this.events=null
},clone:function(a){if(a==null){a=new OpenLayers.Layer(this.name,this.getOptions())
}OpenLayers.Util.applyDefaults(a,this);
a.map=null;
a.savedStates={};
return a
},updateGeoRM:function(){return(this.GeoRM&&this.GeoRM.getToken()!=null||this.isBaseLayer)
},getNativeProjection:function(){if(this.isBaseLayer){this.projection=this.projection||this.nativeProjection
}if(!this.projection&&this.map){this.projection=this.map.getProjection()
}if(this.projection&&typeof(this.projection)=="string"){this.projection=new OpenLayers.Projection(this.projection)
}return this.projection
}})
}if(OpenLayers.Popup){OpenLayers.Popup=OpenLayers.overload(OpenLayers.Popup,{updateSize:function(){var e="<div class='"+this.contentDisplayClass+"'>"+this.contentDiv.innerHTML+"</div>";
var h=(this.map)?this.map.div:document.body;
var i=OpenLayers.Util.getRenderedDimensions(e,null,{displayClass:this.displayClass,containerElement:h});
var g=this.getSafeContentSize(i);
var f=null;
if(g.equals(i)){f=i
}else{var b=new OpenLayers.Size();
b.w=(g.w<i.w)?g.w:null;
b.h=(g.h<i.h)?g.h:null;
if(b.w&&b.h){f=g
}else{var d=OpenLayers.Util.getRenderedDimensions(e,b,{displayClass:this.contentDisplayClass,containerElement:h});
var c=OpenLayers.Element.getStyle(this.contentDiv,"overflow");
if((c!="hidden")&&(d.equals(g))){var a=OpenLayers.Util.getScrollbarWidth();
if(b.w){d.h+=a
}else{d.w+=a
}}f=this.getSafeContentSize(d)
}}this.setSize(f)
}})
}if(OpenLayers.Layer&&OpenLayers.Layer.HTTPRequest){OpenLayers.Layer.HTTPRequest=OpenLayers.overload(OpenLayers.Layer.HTTPRequest,{mergeNewParams:function(b){this.params=OpenLayers.Util.extend(this.params,b);
if(this.GeoRM){OpenLayers.Util.extend(this.params,this.GeoRM.token);
if(this.GeoRM.transport=="referrer"){OpenLayers.Util.extend(this.params,Geoportal.GeoRMHandler.getCookieReferrer((this.map?this.map.div:null),true))
}}var a=this.redraw();
if(this.map!=null){this.map.events.triggerEvent("changelayer",{layer:this,property:"params"})
}return a
},getFullRequestString:function(g,d){var b=d||this.url;
var f=OpenLayers.Util.extend({},this.params);
f=OpenLayers.Util.extend(f,g);
if(this.GeoRM){OpenLayers.Util.extend(f,this.GeoRM.token);
if(this.GeoRM.transport=="referrer"){OpenLayers.Util.extend(this.params,Geoportal.GeoRMHandler.getCookieReferrer((this.map?this.map.div:null),true))
}}var e=OpenLayers.Util.getParameterString(f);
if(OpenLayers.Util.isArray(b)){b=this.selectUrl(e,b)
}var a=OpenLayers.Util.upperCaseObject(OpenLayers.Util.getParameters(b));
for(var c in f){if(c.toUpperCase() in a){delete f[c]
}}e=OpenLayers.Util.getParameterString(f);
return OpenLayers.Util.urlAppend(b,e)
}})
}if(OpenLayers.Layer&&OpenLayers.Layer.Grid){OpenLayers.Layer.Grid=OpenLayers.overload(OpenLayers.Layer.Grid,{mergeNewParams:OpenLayers.Layer.HTTPRequest.prototype.mergeNewParams,getFullRequestString:OpenLayers.Layer.HTTPRequest.prototype.getFullRequestString,getTileBounds:function(d){var c,f,e,b;
if(this.resample){c=this.nativeMaxExtent;
f=this.nativeResolution;
e=f*this.nativeTileSize.w;
b=f*this.nativeTileSize.h
}else{c=this.maxExtent;
f=this.getResolution();
e=f*this.tileSize.w;
b=f*this.tileSize.h
}var h=this.getLonLatFromViewPortPx(d);
if(this.resample){h.transform(this.map.getProjection(),this.getNativeProjection())
}var a=c.left+(e*Math.floor((h.lon-c.left)/e));
var g=c.bottom+(b*Math.floor((h.lat-c.bottom)/b));
return new OpenLayers.Bounds(a,g,a+e,g+b)
},getTileOrigin:function(){var b=this.tileOrigin;
if(!b){var c=this.getMaxExtent();
var a=({tl:["left","top"],tr:["right","top"],bl:["left","bottom"],br:["right","bottom"]})[this.tileOriginCorner];
b=new OpenLayers.LonLat(c?c[a[0]]:0,c?c[a[1]]:0)
}return b
}})
}if(OpenLayers.Tile){OpenLayers.Tile=OpenLayers.overload(OpenLayers.Tile,{initialize:function(e,a,f,c,d,b){this.layer=e;
this.position=a.clone();
this.bounds=f.clone();
this.url=c;
this.size=d.clone();
this.id=OpenLayers.Util.createUniqueID("Tile_");
this.events=new OpenLayers.Events(this,null,this.EVENT_TYPES);
OpenLayers.Util.extend(this,b);
if(e.GeoRM){this.events.register("reload",this,this.updateGeoRM);
this.events.register("loadstart",this,this.updateGeoRM)
}},destroy:function(){if(this.layer.GeoRM){this.events.unregister("reload",this,this.updateGeoRM);
this.events.unregister("loadstart",this,this.updateGeoRM)
}this.layer=null;
this.bounds=null;
this.size=null;
this.position=null;
this.events.destroy();
this.events=null
},updateGeoRM:function(){return(this.layer.GeoRM.getToken()!=null)
},draw:function(){var a=this.layer.resample?this.layer.nativeMaxExtent:this.layer.maxExtent;
var b=(a&&this.bounds.intersectsBounds(a,false));
this.shouldDraw=(b||this.layer.displayOutsideMaxExtent);
this.clear();
return this.shouldDraw
}})
}if(OpenLayers.Layer&&OpenLayers.Layer.Vector){OpenLayers.Layer.Vector=OpenLayers.overload(OpenLayers.Layer.Vector,{addFeatures:function(b,k){if(!(OpenLayers.Util.isArray(b))){b=[b]
}var h=!k||!k.silent;
if(h){var a={features:b};
var g=this.events.triggerEvent("beforefeaturesadded",a);
if(g===false){return
}b=a.features
}var d=[];
for(var c=0,f=b.length;
c<f;
c++){if(c!=(b.length-1)){this.renderer.locked=true
}else{this.renderer.locked=false
}var j=b[c];
if(!(j instanceof OpenLayers.Feature.Vector)){continue
}if(this.geometryType&&!(j.geometry instanceof this.geometryType)){var e=OpenLayers.i18n("componentShouldBe",{geomType:this.geometryType.prototype.CLASS_NAME});
throw e
}j.layer=this;
if(!j.style&&this.style){j.style=OpenLayers.Util.extend({},this.style)
}if(h){if(this.events.triggerEvent("beforefeatureadded",{feature:j})===false){continue
}this.preFeatureInsert(j)
}d.push(j);
this.features.push(j);
this.drawFeature(j);
if(h){this.events.triggerEvent("featureadded",{feature:j});
this.onFeatureInsert(j)
}}if(h){this.events.triggerEvent("featuresadded",{features:d})
}}})
}if(OpenLayers.Layer&&OpenLayers.Layer.WMS){OpenLayers.Layer.WMS=OpenLayers.overload(OpenLayers.Layer.WMS,{getFullRequestString:function(e,c){var b=this.getNativeProjection();
var a="SRS";
if(parseFloat(this.params.VERSION)>=1.3){a="CRS";
if(typeof(this.layerLimit)=="number"){var d=this.params.LAYERS.split(",");
if(this.layerLimit<d.length){this.params.LAYERS=d.slice(0,this.layerLimit).join(",");
OpenLayers.Console.warn("["+d.slice(this.layerLimit).join(",")+"]")
}}}this.params[a]=(b==null)?"none":b.getCode();
return OpenLayers.Layer.Grid.prototype.getFullRequestString.apply(this,arguments)
},getURL:function(c){var f=c.clone();
f=this.adjustBounds(f);
f.transform(this.map.getProjection(),this.getNativeProjection(),true);
var d=this.getImageSize();
var g={};
var b=this.reverseAxisOrder();
g.BBOX=this.encodeBBOX?f.toBBOX(null,b):f.toArray(b);
g.WIDTH=d.w;
g.HEIGHT=d.h;
var a=this.getFullRequestString(g);
return a
},getDataExtent:function(){return this.maxExtent
}})
}if(OpenLayers.Layer&&OpenLayers.Layer.Vector&&OpenLayers.Layer.Vector.RootContainer){OpenLayers.Layer.Vector.RootContainer=OpenLayers.overload(OpenLayers.Layer.Vector.RootContainer,{setMap:function(a){OpenLayers.Layer.Vector.prototype.setMap.apply(this,arguments);
this.collectRoots();
a.events.on({changelayer:this.handleChangeLayer,removelayer:this.handleRemoveLayer,scope:this})
},removeMap:function(a){a.events.un({changelayer:this.handleChangeLayer,removelayer:this.handleRemoveLayer,scope:this});
this.resetRoots();
OpenLayers.Layer.Vector.prototype.removeMap.apply(this,arguments)
},handleRemoveLayer:function(a){var c=a.layer;
for(var b=0;
b<this.layers.length;
b++){if(c==this.layers[b]){this.layers.splice(b,1);
this.renderer.eraseFeatures(c.features);
return
}}}})
}if(OpenLayers.Protocol&&OpenLayers.Protocol.Script){OpenLayers.Protocol.Script=OpenLayers.overload(OpenLayers.Protocol.Script,{createRequest:function(c,e,g){var f=OpenLayers.Protocol.Script.register(g);
var b="OpenLayers.Protocol.Script.registry.regId"+f;
e=OpenLayers.Util.extend({},e);
e[this.callbackKey]=this.callbackPrefix+b;
c=OpenLayers.Util.urlAppend(c,OpenLayers.Util.getParameterString(e));
var a=document.createElement("script");
a.type="text/javascript";
a.src=c;
a.id="OpenLayers_Protocol_Script_"+f;
this.pendingRequests[a.id]=a;
var d=document.getElementsByTagName("head")[0];
d.appendChild(a);
return a
}});
(function(){var b=OpenLayers.Protocol.Script;
var a=0;
b.registry={};
b.register=function(d){var c=++a;
b.registry["regId"+c]=function(){b.unregister(c);
d.apply(this,arguments)
};
return c
};
b.unregister=function(c){delete b.registry["regId"+c]
}
})()
}if(OpenLayers.Layer&&OpenLayers.Layer.GML){OpenLayers.Layer.GML=OpenLayers.overload(OpenLayers.Layer.GML,{addFeatures:OpenLayers.Layer.Vector.prototype.addFeatures,clone:function(a){if(a==null){a=new OpenLayers.Layer.GML(this.name,this.url,this.getOptions())
}a=OpenLayers.Layer.Vector.prototype.clone.apply(this,[a]);
a.savedStates={};
return a
}})
}if(OpenLayers.Layer&&OpenLayers.Layer.XYZ){OpenLayers.Layer.XYZ=OpenLayers.overload(OpenLayers.Layer.XYZ,{getFullRequestString:OpenLayers.Layer.HTTPRequest.prototype.getFullRequestString})
}if(OpenLayers.Popup&&OpenLayers.Popup.Framed){OpenLayers.Popup.Framed=OpenLayers.overload(OpenLayers.Popup.Framed,{destroy:function(){this.imageSrc=null;
this.imageSize=null;
this.isAlphaImage=null;
this.fixedRelativePosition=false;
this.positionBlocks=null;
if(this.blocks){for(var a=0;
a<this.blocks.length;
a++){var b=this.blocks[a];
if(b.image){b.div.removeChild(b.image)
}b.image=null;
if(b.div){this.groupDiv.removeChild(b.div)
}b.div=null
}this.blocks=null
}OpenLayers.Popup.Anchored.prototype.destroy.apply(this,arguments)
}})
}if(OpenLayers.Control&&OpenLayers.Control.KeyboardDefaults){OpenLayers.Control.KeyboardDefaults=OpenLayers.overload(OpenLayers.Control.KeyboardDefaults,{defaultKeyPress:function(a){var c=true;
switch(a.keyCode){case OpenLayers.Event.KEY_LEFT:this.map.pan(-this.slideFactor,0);
break;
case OpenLayers.Event.KEY_RIGHT:this.map.pan(this.slideFactor,0);
break;
case OpenLayers.Event.KEY_UP:this.map.pan(0,-this.slideFactor);
break;
case OpenLayers.Event.KEY_DOWN:this.map.pan(0,this.slideFactor);
break;
case 33:var b=this.map.getSize();
this.map.pan(0,-0.75*b.h);
break;
case 34:var b=this.map.getSize();
this.map.pan(0,0.75*b.h);
break;
case 35:var b=this.map.getSize();
this.map.pan(0.75*b.w,0);
break;
case 36:var b=this.map.getSize();
this.map.pan(-0.75*b.w,0);
break;
case 43:case 61:case 187:case 107:this.map.zoomIn();
break;
case 45:case 54:case 109:case 189:case 95:this.map.zoomOut();
break;
default:c=false;
break
}if(c===true){OpenLayers.Event.stop(a)
}}})
}var Geoportal={VERSION_NUMBER:"Geoportal 1.3 Min; publicationDate=2011-07-05",singleFile:true,_getScriptLocation:(function(){var f="";
var c=new RegExp("(^|(.*?\\/))(GeoportalMin?.js)(\\?|$)");
var b=document.documentElement.getElementsByTagName("script");
for(var e=0,a=b.length;
e<a;
e++){var g=b[e].getAttribute("src");
if(g){var d=g.match(c);
if(d){f=d[1];
break
}}}return(function(){return f
})
})()};
Geoportal.Lang={add:function(b){for(var a in b){if(b.hasOwnProperty(a)){for(var c in b[a]){if(b[a].hasOwnProperty(c)){var d=Geoportal.Lang[c];
if(!d){d={}
}d[a]=b[a][c]
}}}}},translate:function(b,a){var d=Geoportal.Lang[OpenLayers.Lang.getCode()];
var c=d[b];
if(!c){c=OpenLayers.Lang.translate(b)
}if(a){c=OpenLayers.String.format(c,a)
}return c
}};
Geoportal.i18n=Geoportal.Lang.translate;
OpenLayers.i18n=Geoportal.Lang.translate;
Geoportal.Lang.en={ATF:"French Southern Territories",FXX:"France mainland",GLP:"Guadeloupe",GUF:"French Guiana",MTQ:"Martinique",MYT:"Mayotte",NCL:"New Caledonia",PYF:"French Polynesia",REU:"Réunion",SPM:"Saint Pierre and Miquelon",WLF:"Wallis and Futuna",ANF:"French Antilles",ASP:"Saint Paul and Amsterdam",CRZ:"Crozet",EUE:"Europe",KER:"Kerguelen",SBA:"Saint Barthélémy",SMA:"Saint Martin",WLD:"The world","GEOGRAPHICALGRIDSYSTEMS.MAPS":"IGN Maps","GEOGRAPHICALGRIDSYSTEMS.MAPS.description":"Maps are extracted from IGN's SCAN databases : World, Europe, SCAN 1 000®, SCAN 500®, SCAN Régional®, SCAN 200®, SCAN Départemental®, SCAN 100®, SCAN 50®, SCAN 25®.","ORTHOIMAGERY.ORTHOPHOTOS":"Orthoimagery","ORTHOIMAGERY.ORTHOPHOTOS.description":"Aerial photographies combine the geometrical precision of maps with richness of photographies, between 15 and 50 cm resolution, and satelite images, between 10 and 20 m resolution.","ELEVATION.SLOPES":"Slops - Elevation","ELEVATION.SLOPES.description":"Digital terrain models were derived from BD ALTI ® data that describes the  French territory with contour lines. The equidistance of the contour lines can range from 5 to 40m. Original data has been acquired from IGN maps at 1: 25 000, at 1: 50 000 and from aerial photographs at 1: 20 000, 1: 30 000 and 1: 60 000 by stereophotogrammetry.","ELEVATION.LEVEL0":"Sea level 0 - Elevation","ELEVATION.LEVEL0.description":"The sea 0 level is part of LITTO3D®, a joint production from SHOM and IGN.","CADASTRALPARCELS.PARCELS":"Cadastral parcels","CADASTRALPARCELS.PARCELS.description":"The digital cadastral information is georeferenced and seamless throughout the French territory. It was carried out from the assembly of the digital cadastral scanned sheets.","HYDROGRAPHY.HYDROGRAPHY":"Hydrography","HYDROGRAPHY.HYDROGRAPHY.description":"Hydrography network is derived from the assembly of datasets from BD TOPO®, BD CARTHAGE®, EuroRegionalMap and EuroGlobalMap databases.","TRANSPORTNETWORKS.ROADS":"Road transport networks - Transport networks","TRANSPORTNETWORKS.ROADS.description":"The roads network is derived from the assembly of datasets from BD TOPO®, BD CARTO®, EuroRegionalMap and EuroGlobalMap databases.","TRANSPORTNETWORKS.RAILWAYS":"Rail transport networks - Transport networks","TRANSPORTNETWORKS.RAILWAYS.description":"The railways network is derived from the assembly of datasets from BD TOPO®, BD CARTO®, EuroRegionalMap and EuroGlobalMap databases.","TRANSPORTNETWORKS.RUNWAYS":"Air transport networks - Transport networks","TRANSPORTNETWORKS.RUNWAYS.description":"Airport runways are derived from the assembly of datasets from BD TOPO® and BD CARTO®.","BUILDINGS.BUILDINGS":"Buildings","BUILDINGS.BUILDINGS.description":"Buildings and urban areas are derived from the assembly of datasets from BD TOPO®, BD CARTO®, EuroRegionalMap and EuroGlobalMap databases.","UTILITYANDGOVERNMENTALSERVICES.ALL":"Utility and governmental services","UTILITYANDGOVERNMENTALSERVICES.ALL.description":"Energy networks are derived from the assembly of datasets from BD TOPO® and BD CARTO®.","ADMINISTRATIVEUNITS.BOUNDARIES":"Administrative units","ADMINISTRATIVEUNITS.BOUNDARIES.description":"French administrative units are derived from the assembly of datasets from BD TOPO®, BD CARTO®, EuroRegionalMap and EuroBoundaryMap databases.","LANDCOVER.CORINELANDCOVER":"Corine Land cover","LANDCOVER.CORINELANDCOVER.description":"CORINE Land Cover (2006)","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS":"Coastal maps","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS.description":"Coastal maps","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS":"Type 1900 topographical maps","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS.description":"Maps at 1: 50 000 (after the first edition of the IGN Map Library)","GEOGRAPHICALGRIDSYSTEMS.CASSINI":"Cassini's maps","GEOGRAPHICALGRIDSYSTEMS.CASSINI.description":"Cassini's maps","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000":"Coastal ortho-imagery (2000)","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000.description":"Ortho-photography of the coast of the North Sea, English Channel and Atlantic.","LANDUSE.AGRICULTURE2007":"Agricultural parcels (2007)","LANDUSE.AGRICULTURE2007.description":"This layer displays the islets anonymized Registry Parcel Graph (RPG) and their main crop group reported in 2007 by farmers to benefit from CAP subsidies.","LANDUSE.AGRICULTURE2008":"Agricultural parcels (2008)","LANDUSE.AGRICULTURE2008.description":"This layer displays the islets anonymized Registry Parcel Graph (RPG) and their main crop group reported in 2008 by farmers to benefit from CAP subsidies.","LANDUSE.AGRICULTURE2009":"Agricultural parcels (2009)","LANDUSE.AGRICULTURE2009.description":"This layer displays the islets anonymized Registry Parcel Graph (RPG) and their main crop group reported in 2009 by farmers to benefit from CAP subsidies.","LANDUSE.AGRICULTURE2010":"Agricultural parcels (2010)","LANDUSE.AGRICULTURE2010.description":"This layer displays the islets anonymized Registry Parcel Graph (RPG) and their main crop group reported in 2010 by farmers to benefit from CAP subsidies.","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE":"Geneva State","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE.description":"Ortho-photography, 10 cm resolution (2010).","ORTHOIMAGERY.ORTHOPHOTOS2000-2005":"Orthoimagery (2000-2005)","ORTHOIMAGERY.ORTHOPHOTOS2000-2005.description":"1st national coverage (2000-2005) of ortho-imagery with 50 cm resolution.","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER":"FranceRaster®","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER.description":"FranceRaster® is a serie of georeferenced images covering France mainland and overseas territories. It is produced with the vector database of the IGN best suited to each scale with a uniform symbology. According to the scales, FranceRaster ®, allows viewing the themes roads and rail, frame, hydrography, vegetation, addresses, direction of travel, names ...","NATURALRISKZONES.1910FLOODEDWATERSHEDS":"Seine (PHEC)","NATURALRISKZONES.1910FLOODEDWATERSHEDS.description":"This map layer represents the known highest water (PHEC) on the river basin of the Seine, ie geographical areas flooded by the biggest known flood and documented on each river.","NATURALRISKZONES.1910FLOODEDCELLARS":"Flooded cellars (1910)","NATURALRISKZONES.1910FLOODEDCELLARS.description":"Map of flooded cellars during the Seine's flood of 1910. This map is based on testimonies.","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40":"État-Major Maps (1:40 000)","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40.description":"This layer is formed by the assembly of the 978 original drawings of the map of Etat Major established in the nineteenth century. These hand-written and colored surveys, at the 1: 40 000, were established between 1825 and 1866.","LANDCOVER.FORESTINVENTORY.V1":"Forest inventory (v1)","LANDCOVER.FORESTINVENTORY.V1.description":"Forest inventory (1987-2004)","LANDCOVER.FORESTINVENTORY.V2":"Forest inventory (v2)","LANDCOVER.FORESTINVENTORY.V2.description":"Forest inventory (2005+)","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS":"Map of administrative divisions","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS.description":"Map of administrative divisions","TOPONYMS.ALL":"Search by location","TOPONYMS.ALL.description":"Uses the database BD Nyme®.","ADDRESSES.CROSSINGS":"Search by address","ADDRESSES.CROSSINGS.description":"Uses the database Routes Adresses®.","GEOGRAPHICALNAMES.NAMES":"Geographical names","GEOGRAPHICALNAMES.NAMES.description":"Geographical names","div.not.found":"${id} not found in the document : check you have '${id}' set.","proxy.not.set":"Missing proxy setting : may cause problems when getting contract configuration.","cookies.not.enabled":"Cookies are disabled. Please, enable them.","geoRM.getConfig":"Impossible to get information for key '${key}' - check your connection","geoRM.getToken":"Impossible to get the token for key '${key}' - check your connection","geoRM.failed":"Failed to get a valid token for key '${key}'","geoRM.forbidden":"API key is missing or ${layer} not supported by the application API's contract.","url.error":"Error when loading '${url}' ${msg}","GPX.version":"Unhandled GPX version ${gpxVersion}","XLS.version":"Unhandled XLS version ${xlsVersion} for OpenLS Core service ${coreService}","Not.conformal.XLS":"Location Service XML is missing : ${part}","olControlMeasurePath.title":"Distance measurement","olControlMeasurePolygon.title":"Surface measurement","waiting.measurement":"Waiting for digitizing ...","length.measurement":"Length","area.measurement":"Area","gpControlLayerSwitcher.label":"Layers","gpLayer.metadataURL":"More information about this layer ...","gpLayer.dataURL":"Access to download page","gpControlPanelToggle.closed":"Show toolbar","gpControlPanelToggle.opened":"Hide toolbar","gpControlRemoveLayer.title":"Remove layer","gpControlLayerOpacity.title":"Opacity slider","gpControlZoomToLayerMaxExtent.title":"Zoom to layer's extent","gpControlMousePosition.longitude":"Longitude","gpControlMousePosition.latitude":"Latitude","gpControlMousePosition.easting":"Easting","gpControlMousePosition.northing":"Northing","gpControlMousePosition.north":"N","gpControlMousePosition.south":"S","gpControlMousePosition.east":"E","gpControlMousePosition.west":"W","gpControlMousePosition.sexa":"sexagecimal degrees","gpControlMousePosition.deg":"decimal degrees","gpControlMousePosition.gon":"decimal grades","gpControlMousePosition.rad":"decimal radians","gpControlMousePosition.km":"kilometers","gpControlMousePosition.m":"meters","gpControlMousePosition.cm":"centimeters","gpControlMousePosition.utmZone":"Zone","gpControlToolBox.label":"Toolbox","gpControlZoomBar.world":"World","gpControlZoomBar.state":"State","gpControlZoomBar.country":"Country","gpControlZoomBar.town":"Town","gpControlZoomBar.street":"Street","gpControlZoomBar.house":"House","gpControlEditingToolbar.drawpoint":"Draw point","gpControlEditingToolbar.drawline":"Draw line","gpControlEditingToolbar.drawpolygon":"Draw polygon","gpControlEditingToolbar.dragfeature":"Drag feature","gpControlEditingToolbar.modifyfeature":"Modify feature","gpControlEditingToolbar.deletefeature":"Delete feature","gpControlEditingToolbar.selectfeature":"Select feature","gpControlEditingToolbar.navigation":"Navigate","gpControlAddImageLayer.title":"Add image layer ...","gpControlAddImageLayer.layerUrl":"URL : ","gpControlAddImageLayer.layerUrl.help":"base address of the service","gpControlAddImageLayer.layerType":"Type : ","gpControlAddImageLayer.layerType.help":"pick up a value in the select","gpControlAddImageLayer.layerType.WMS":"Web Map Service","gpControlAddImageLayer.layerType.WMTS":"Web Map Tile Service","gpControlAddImageLayer.layerType.WMSC":"OSGeO WMS-C","gpControlAddImageLayer.button.add":"Add Layer","gpControlAddImageLayer.button.cancel":"Cancel","wms.caps.no.compatible.srs":"No compatible layer found","ogc.caps.unknown.service":"${serviceType} is not a ${expectedType}","gpControlAddVectorLayer.title":"Add vector layer ...","gpControlAddVectorLayer.layerName.help":"ex : my layer","gpControlAddVectorLayer.layerType":"Type : ","gpControlAddVectorLayer.layerType.help":"pick up a value in the select","gpControlAddVectorLayer.layerType.Point":"point","gpControlAddVectorLayer.layerType.LineString":"linestring","gpControlAddVectorLayer.layerType.Polygon":"polygon","gpControlAddVectorLayer.layerType.KML":"KML resource","gpControlAddVectorLayer.layerType.GPX":"GPX resource","gpControlAddVectorLayer.layerType.OSM":"OSM resource","gpControlAddVectorLayer.layerType.GeoRSS":"GeoRSS resource","gpControlAddVectorLayer.layerType.WFS":"WFS resource","gpControlAddVectorLayer.layerUrl":"URL : ","gpControlAddVectorLayer.layerUrl.help":"either local or remote","gpControlAddVectorLayer.layerFreeHand":"freehand ? ","gpControlAddVectorLayer.layerFreeHand.help":"allow free hand drawing","gpControlAddVectorLayer.button.add":"Add Layer","gpControlAddVectorLayer.button.cancel":"Cancel","gpControlAddVectorLayer.layerName":"Name : ","gpControlAddVectorLayer.layerContent":"Contents","gpControlAddVectorLayer.layerContent.help":"Copy / Paste Data","gpControlAddVectorLayer.layerUrlSwitch":"By URL / Content","gpControlAddVectorLayer.layerUrlSwitch.help":"Provide remote URL or the content data","wfs.caps.no.feature.found":"No features found","wfs.caps.unsupported.version":"Unsupported WFS version ${version}","gpControlLocationUtilityService.geonames.title":"Search a location","gpControlLocationUtilityService.geocode.title":"Search an address","gpControlLocationUtilityService.reverse.geocode.title":"Search addresses around a location","gpControlLocationUtilityServiceGeoNames.title":"Search a location :","gpControlLocationUtilityServiceGeoNames.name":"Location : ","gpControlLocationUtilityServiceGeoNames.name.help":"ex : Saint-Mandé","gpControlLocationUtilityServiceGeoNames.button.cancel":"Cancel","gpControlLocationUtilityServiceGeoNames.button.search":"Search","gpControlLocationUtilityServiceGeocode.title":"Search a place :","gpControlLocationUtilityServiceGeocode.address":"Street : ","gpControlLocationUtilityServiceGeocode.address.help":"ex : 73, avenue de Paris","gpControlLocationUtilityServiceGeocode.municipality":"Municipality : ","gpControlLocationUtilityServiceGeocode.municipality.help":"ex : Saint-Mandé","gpControlLocationUtilityServiceGeocode.postalcode":"Postal code : ","gpControlLocationUtilityServiceGeocode.postalcode.help":"ex : 94165","gpControlLocationUtilityServiceGeocode.name":"Place: ","gpControlLocationUtilityServiceGeocode.name.help":"e.g.: Saint-Mandé or 94165","gpControlLocationUtilityServiceGeocode.button.cancel":"Cancel","gpControlLocationUtilityServiceGeocode.button.search":"Search","gpControlLocationUtilityServiceGeocode.matchType.city":"Location in the city","gpControlLocationUtilityServiceGeocode.matchType.street":"Location in the street","gpControlLocationUtilityServiceGeocode.matchType.number":"Location at the exact address number","gpControlLocationUtilityServiceGeocode.matchType.enhanced":"Location interpolated between two addresses","gpControlLocationUtilityServiceReverseGeocode.title":"Search places around :","gpControlLocationUtilityServiceReverseGeocode.longitude":"Longitude : ","gpControlLocationUtilityServiceReverseGeocode.longitude.help":"ex : dd.mmss in geographic coordinates","gpControlLocationUtilityServiceReverseGeocode.latitude":"Latitude : ","gpControlLocationUtilityServiceReverseGeocode.latitude.help":"ex : dd.mmss in geographic coordinates","gpControlLocationUtilityServiceReverseGeocode.button.cancel":"Cancel","gpControlLocationUtilityServiceReverseGeocode.button.search":"Search","gpControlCSW.title":"Search in the geocatalogue","gpControlCSW.cswTitle":"Title : ","gpControlCSW.cswTitle.help":" ","gpControlCSW.cswKeyWords":"KeyWords : ","gpControlCSW.cswKeyWords.help":" ","gpControlCSW.cswKeyWords.NoKeyWords":" ","gpControlCSW.cswKeyWords.Addresses":"Addresses","gpControlCSW.cswKeyWords.AdministrativeUnits":"Administrative units","gpControlCSW.cswKeyWords.Agricultural":"Agricultural and aquaculture facilities","gpControlCSW.cswKeyWords.RegulationZones":"Area management/restriction/regulation zones and reporting units","gpControlCSW.cswKeyWords.Atmospheric":"Atmospheric conditions","gpControlCSW.cswKeyWords.BioGeographical":"Bio-geographical regions","gpControlCSW.cswKeyWords.Buildings":"Buildings","gpControlCSW.cswKeyWords.Cadastral":"Cadastral parcels","gpControlCSW.cswKeyWords.CoordinateSystems":"Coordinate reference systems","gpControlCSW.cswKeyWords.Elevation":"Elevation","gpControlCSW.cswKeyWords.Energy":"Energy resources","gpControlCSW.cswKeyWords.EnvironmentalFacilities":"Environmental monitoring facilities","gpControlCSW.cswKeyWords.GeographicalSystems":"Geographical grid systems","gpControlCSW.cswKeyWords.GeographicalNames":"Geographical names","gpControlCSW.cswKeyWords.Geology":"Geology","gpControlCSW.cswKeyWords.Habitats":"Habitats and biotopes","gpControlCSW.cswKeyWords.HumanHealth":"Human health and safety","gpControlCSW.cswKeyWords.Hydrography":"Hydrography","gpControlCSW.cswKeyWords.LandCover":"Land cover","gpControlCSW.cswKeyWords.LandUse":"Land use","gpControlCSW.cswKeyWords.Meteorological":"Meteorological geographical features","gpControlCSW.cswKeyWords.Mineral":"Mineral resources","gpControlCSW.cswKeyWords.NaturalRiskZones":"Natural risk zones","gpControlCSW.cswKeyWords.Oceanographic":"Oceanographic geographical features","gpControlCSW.cswKeyWords.Orthoimagery":"Orthoimagery","gpControlCSW.cswKeyWords.Population":"Population distribution — Demography","gpControlCSW.cswKeyWords.Production":"Production and industrial facilities","gpControlCSW.cswKeyWords.ProtectedSites":"Protected sites","gpControlCSW.cswKeyWords.SeaRegions":"Sea regions","gpControlCSW.cswKeyWords.Soil":"Soil","gpControlCSW.cswKeyWords.SpeciesDistribution":"Species distribution","gpControlCSW.cswKeyWords.StatisticalUnits":"Statistical units","gpControlCSW.cswKeyWords.TransportNetworks":"Transport networks","gpControlCSW.cswKeyWords.UtilityServices":"Utility and governmental services","gpControlCSW.cswOrganism":"Organism : ","gpControlCSW.cswOrganism.help":" ","gpControlCSW.button.cancel":"Cancel","gpControlCSW.button.search":"Search","gpControlCSW.cswBBOX":"BBOX","gpControlCSW.cswNoBBOX":"World extent","gpControlCSW.cswNoBBOX.help":" ","gpControlCSW.cswCurrentBBOX":"Current extent","gpControlCSW.cswCurrentBBOX.help":" ","gpControlCSW.cswPersonnalBBOX":"Select an extent","gpControlCSW.cswPersonnalBBOX.help":" ","gpControlPageManager.button.previous":"<","gpControlPageManager.button.next":">","azimuth.measurement":"Azimuth","gpControlMeasureAzimuth.title":"Azimuth measurement","gpControlMeasureAzimuth.azimuth":"Azimuth","gpControlMeasureAzimuth.azimuth.help":"an angular measurement in a spherical coordinate system","gpControlMeasureAzimuth.distance":"Distance","gpControlMeasureAzimuth.distance.help":"Length","gpControlPrintMap.title":"Map's printing preview","gpControlPrintMap.comments":"Your notes or comments","approx.scale":"Approximate scale: 1:","approx.center":"Geographical coordinates of the center of the map","gpControlPrintMap.print.forbidden":"All rights reserved","gpControlPrintMap.print":"Print",gpControlInformationMini:"Click to get informations panel visible","OpenLayers.Control.WMSGetFeatureInfo.title":"Objects identification","gpControlAddAttributeToLayer.title":"Add / Remove Attributes","gpControlAddAttributeToLayer.attName":"Name","gpControlAddAttributeToLayer.attName.help":"name of the attribute to add","gpControlAddAttributeToLayer.attDefaultValue":"Default","gpControlAddAttributeToLayer.attDefaultValue.help":"default value attribute","gpControlAddAttributeToLayer.attList":"Attributes","gpControlAddAttributeToLayer.attList.help":"being present","gpControlAddAttributeToLayer.button.cancel":"Finish","gpControlAddAttributeToLayer.button.addatt":"Add","gpControlAddAttributeToLayer.button.delatt":"Remove","gpControlAddAttributeToLayer.emptyName":"The name is empty","gpControlAddAttributeToLayer.existingName":"${name} already exists","gpControlLayerStyling.title":"Edit mapping","gpControlLayerStyling.color":"Color lines and filling","gpControlLayerStyling.color.help":"click to open the color palette, click outside to close","gpControlLayerStyling.size":"Size","gpControlLayerStyling.size.help":"click to open the panel to change sizes","gpControlLayerStyling.style":"Representation","gpControlLayerStyling.style.help":"click to open the panel for changing styles","gpControlLayerStyling.rotation":"Angle of rotation (in degree)","gpControlLayerStyling.rotation.help":"click to choose an angle between 0 and 360°","gpControlLayerStyling.externalgraphic":"URL of the icon","gpControlLayerStyling.externalgraphic.help":"enter the URL of the icon - nothing for none","gpControlLayerStyling.button.cancel":"Cancel","gpControlLayerStyling.button.changestyle":"Change","gpControlLayerStyling.emptyColor":"The color is empty (#XXXXXX expected)","gpControlSaveLayer.title":"Save Layer","gpControlSaveLayer.format":"Save Format","gpControlSaveLayer.gml":"GML","gpControlSaveLayer.kml":"KML","gpControlSaveLayer.gpx":"GPX tracks","gpControlSaveLayer.osm":"OpenStreetMap XML Export","gpControlSaveLayer.gxt":"Géoconcept Export","gpControlSaveLayer.format.help":"format to export the data","gpControlSaveLayer.proj":"Reference system of coordinates","gpControlSaveLayer.proj.help":"export coordinates","gpControlSaveLayer.pretty":"Improved display","gpControlSaveLayer.pretty.help":"to make it readable data export","gpControlSaveLayer.button.cancel":"Cancel","gpControlSaveLayer.button.save":"Save","gpControlSaveLayer.noData":"Die Syer is empty, no data to save","lus.not.match":"No match found","csw.not.match":"No metadata record found","geocoded.address.popup.title":"Address","geocoded.address.popup.postalCode":"Postal code","geocoded.address.popup.places":"Places",CountrySubdivision:"Country subdivision",CountrySecondarySubdivision:"Country secondary subdivision",Municipality:"Municipality",MunicipalitySubdivision:"Municipality subdivision",TOS:"Terms of service","utm.zone":"UTM","*":""};
Geoportal.Lang.fr={ATF:"Terres australes françaises",FXX:"France métropolitaine",GLP:"Guadeloupe",GUF:"Guyane française",MTQ:"Martinique",MYT:"Mayotte",NCL:"Nouvelle-Calédonie",PYF:"Polynésie française",REU:"Île de la Réunion",SPM:"Saint-Pierre et Miquelon",WLF:"Wallis et Futuna",ANF:"Antilles françaises",ASP:"Saint Paul et Amsterdam",CRZ:"Crozet",EUE:"Europe",KER:"Kerguelen",SBA:"Saint Barthélémy",SMA:"Saint Martin",WLD:"Le Monde","GEOGRAPHICALGRIDSYSTEMS.MAPS":"Cartes IGN","GEOGRAPHICALGRIDSYSTEMS.MAPS.description":"Les cartes sont issues des bases de données SCAN de l'IGN : Monde, Europe Politique, SCAN 1 000®, SCAN 500®, SCAN Régional®, SCAN 200®, SCAN Départemental®, SCAN 100®, SCAN 50®, SCAN 25®.","ORTHOIMAGERY.ORTHOPHOTOS":"Ortho-imagerie","ORTHOIMAGERY.ORTHOPHOTOS.description":"Les photographies aériennes allient la précision géométrique de la carte à la richesse de la photographie, résolution entre 50 et 15 cm ou de l'images satellites, résolution entre 10 m et 20m.","ELEVATION.SLOPES":"Teintes hypso. - Altitude","ELEVATION.SLOPES.description":"Les modèles numériques de terrain sont issus de données BD ALTI® que décrit le territoire français par des courbes de niveau. L’équidistance des courbes peut aller de 5 à 40m. Les données initiales ont été saisies sur des cartes IGN au 1 : 25 000, au 1 : 50 000 et à partir d’une restitution issue de prises de vue aériennes au 1 : 20 000, 1 : 30 000 et 1 : 60 000.","ELEVATION.LEVEL0":"Trait de côte - Altitude","ELEVATION.LEVEL0.description":"Le 0 des mers est issu de LITTO3D®, une production réalisée en commun entre le SHOM et l'IGN.","CADASTRALPARCELS.PARCELS":"Parcelles cadastrales","CADASTRALPARCELS.PARCELS.description":"L'information cadastrale numérique est géoréférencée et continue sur l'ensemble du territoire français. Elle a été réalisée à partir de l'assemblage du plan cadastral dématérialisé.","HYDROGRAPHY.HYDROGRAPHY":"Hydrographie","HYDROGRAPHY.HYDROGRAPHY.description":"L'hydrographie terrestre est issue de l'assemblage de données BD TOPO®, BD CARTHAGE®, EuroRegionalMap et EuroGlobalMap.","TRANSPORTNETWORKS.ROADS":"Réseaux routiers - Réseaux de transport","TRANSPORTNETWORKS.ROADS.description":"Le réseau routier est issu de l'assemblage de données BD TOPO®, BD CARTO®, EuroRegionalMap et EuroGlobalMap.","TRANSPORTNETWORKS.RAILWAYS":"Réseaux ferroviaires - Réseaux de transport","TRANSPORTNETWORKS.RAILWAYS.description":"Le réseau ferroviaire est issu de l'assemblage de données BD TOPO®, BD CARTO®, EuroRegionalMap et EuroGlobalMap.","TRANSPORTNETWORKS.RUNWAYS":"Réseaux aériens - Réseaux de transport","TRANSPORTNETWORKS.RUNWAYS.description":"Les pistes des aéroports et aérodromes sont issus de l'assemblage de données BD TOPO® et BD CARTO®.","BUILDINGS.BUILDINGS":"Bâtiments","BUILDINGS.BUILDINGS.description":"Les bâtiments et zones construites sont issus de l'assemblage de données BD TOPO®, BD CARTO®, EuroRegionalMap et EuroGlobalMap.","UTILITYANDGOVERNMENTALSERVICES.ALL":"Services d'utilité publique et services publics","UTILITYANDGOVERNMENTALSERVICES.ALL.description":"Les divers réseaux de transports d'énergie sont issus de l'assemblage de données BD TOPO® et BD CARTO®.","ADMINISTRATIVEUNITS.BOUNDARIES":"Unités administratives","ADMINISTRATIVEUNITS.BOUNDARIES.description":"Les unités administratives de la France sont issus de l'assemblage de données BD TOPO®, BD CARTO®, EuroRegionalMap et EuroBoundaryMap.","LANDCOVER.CORINELANDCOVER":"Corine LC","LANDCOVER.CORINELANDCOVER.description":"CORINE Land Cover (2006)","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS":"Cartes du littoral","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS.description":"Cartes du littoral","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS":"Cartes topographiques type 1900","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS.description":"Cartes au 1 : 50 000 (1ère édition issue de la Cartothèque de l'IGN)","GEOGRAPHICALGRIDSYSTEMS.CASSINI":"Cartes de Cassini","GEOGRAPHICALGRIDSYSTEMS.CASSINI.description":"Cartes de Cassini","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000":"Ortho-imagerie du littoral (2000)","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000.description":"Ortho-photographie des côtes de la Mer du nord, de la Manche et de l'Atlantique.","LANDUSE.AGRICULTURE2007":"Îlots de culture (2007)","LANDUSE.AGRICULTURE2007.description":"Cette couche affiche les îlots anonymisés du Registre Parcellaire Graphique (RPG) et leur groupe de cultures principal déclarés en 2007 par les exploitants agricoles pour bénéficier des aides PAC.","LANDUSE.AGRICULTURE2008":"Îlots de culture (2008)","LANDUSE.AGRICULTURE2008.description":"Cette couche affiche les îlots anonymisés du Registre Parcellaire Graphique (RPG) et leur groupe de cultures principal déclarés en 2008 par les exploitants agricoles pour bénéficier des aides PAC.","LANDUSE.AGRICULTURE2009":"Îlots de culture (2009)","LANDUSE.AGRICULTURE2009.description":"Cette couche affiche les îlots anonymisés du Registre Parcellaire Graphique (RPG) et leur groupe de cultures principal déclarés en 2009 par les exploitants agricoles pour bénéficier des aides PAC.","LANDUSE.AGRICULTURE2010":"Îlots de culture (2010)","LANDUSE.AGRICULTURE2010.description":"Cette couche affiche les îlots anonymisés du Registre Parcellaire Graphique (RPG) et leur groupe de cultures principal déclarés en 2010 par les exploitants agricoles pour bénéficier des aides PAC.","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE":"Canton de Genève","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE.description":"Ortho-photographie, 10 cm de résolution (2010).","ORTHOIMAGERY.ORTHOPHOTOS2000-2005":"Ortho-photographie (2000-2005)","ORTHOIMAGERY.ORTHOPHOTOS2000-2005.description":"Première couverture France entière d'ortho-photographies à 50 cm de résolution.","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER":"FranceRaster®","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER.description":"FranceRaster® est une série d´images géoréférencées couvrant la France Métropolitaine et les DOM. De cartographie homogène, elle est produite avec les bases de données vecteur de l´IGN les plus adaptées à chaque échelle. Selon les échelles, FranceRaster®; permet la visualisation des thèmes réseau routier et ferré, bâti, hydrographie, végétation, adresses, sens de circulation, toponymie...","NATURALRISKZONES.1910FLOODEDWATERSHEDS":"Seine (PHEC)","NATURALRISKZONES.1910FLOODEDWATERSHEDS.description":"Cette couche de la carte représente le plus haut des eaux connues (PHEC) sur le bassin de la Seine, c'est à dire des zones géographiques inondées par les plus grandes crues connues et documentées sur chaque rivière.","NATURALRISKZONES.1910FLOODEDCELLARS":"Caves inondées (1910)","NATURALRISKZONES.1910FLOODEDCELLARS.description":"Carte des caves inondées lors des crues de la Seine de 1910. Cette carte est basée sur des témoignages.","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40":"Cartes État-Major (1/40 000)","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40.description":"Cette couche est formée par l'assemblage des 978 dessins originaux de la carte d'État-Major créé au XIXe siècle. Ces enquêtes écrites à la main et colorées, au 1: 40 000, ont été établies entre 1825 et 1866.","LANDCOVER.FORESTINVENTORY.V1":"Inventaire forestier (v1)","LANDCOVER.FORESTINVENTORY.V1.description":"Inventaire forestier (1987-2004)","LANDCOVER.FORESTINVENTORY.V2":"Inventaire forestier (v2)","LANDCOVER.FORESTINVENTORY.V2.description":"Inventaire forestier (2005+)","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS":"Divisions administratives","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS.description":"Carte des divisions administratives","TOPONYMS.ALL":"Moteur de recherche par lieux","TOPONYMS.ALL.description":"Utilise la base de données BD Nyme®.","ADDRESSES.CROSSINGS":"Moteur de recherche par adresses","ADDRESSES.CROSSINGS.description":"Utilise la base de données Routes Adresses®.","GEOGRAPHICALNAMES.NAMES":"Désignations géographiques","GEOGRAPHICALNAMES.NAMES.description":"Toponymes","div.not.found":"${id} non trouvé dans le document : vérifier que '${id}' existe bien.","proxy.not.set":"Pas de configuration du proxy : l'obtention des informations sur le contrat pourrait échouer.","cookies.not.enabled":"Les cookies sont désactivés. Activer les s'il vous plaît.","geoRM.getConfig":"Impossible d'obtenir les informations relatives à la clef '${key}' - Vérifier votre connexion.","geoRM.getToken":"Impossible d'obtenir le jeton associé à la clef '${key}' - Vérifier votre connexion.","geoRM.failed":"Impossible d'obtenir un jeton valide pour la clef '${key}'","geoRM.forbidden":"Clé API manquante ou ${layer} pas pris en charge par le contrat de l'application API.","url.error":"Erreur au chargement de '${url}' ${msg}","GPX.version":"Version ${gpxVersion} GPX non supportée","XLS.version":"Version ${xlsVersion} XLS non supportée pour le service ${coreService}","Not.conformal.XLS":"${part} est manquant dans l'XML","olControlMeasurePath.title":"Mesure de distance","olControlMeasurePolygon.title":"Mesure de surface","waiting.measurement":"En attente de saisie ...","length.measurement":"Longueur","area.measurement":"Surface","gpControlLayerSwitcher.label":"Couches","gpLayer.metadataURL":"Plus d'information sur cette couche ...","gpLayer.dataURL":"Accès au service de téléchargement","gpControlPanelToggle.closed":"Afficher la barre d'outils","gpControlPanelToggle.opened":"Masquer la barre d'outils","gpControlRemoveLayer.title":"Retirer la couche","gpControlLayerOpacity.title":"Règle de transparence","gpControlZoomToLayerMaxExtent.title":"Zoomer sur l'emprise de la couche","gpControlMousePosition.longitude":"Longitude","gpControlMousePosition.latitude":"Latitude","gpControlMousePosition.easting":"Abscisse","gpControlMousePosition.northing":"Ordonnée","gpControlMousePosition.north":"N","gpControlMousePosition.south":"S","gpControlMousePosition.east":"E","gpControlMousePosition.west":"O","gpControlMousePosition.sexa":"degrés sexagésimaux","gpControlMousePosition.deg":"degrés décimaux","gpControlMousePosition.gon":"grades","gpControlMousePosition.rad":"radians","gpControlMousePosition.km":"kilomètres","gpControlMousePosition.m":"mètres","gpControlMousePosition.cm":"centimètres","gpControlMousePosition.utmZone":"Zone","gpControlToolBox.label":"Outils","gpControlZoomBar.world":"Monde","gpControlZoomBar.state":"Pays","gpControlZoomBar.country":"Dépt.","gpControlZoomBar.town":"Ville","gpControlZoomBar.street":"Rue","gpControlZoomBar.house":"Maison","gpControlEditingToolbar.drawpoint":"Dessiner un point","gpControlEditingToolbar.drawline":"Dessiner une ligne","gpControlEditingToolbar.drawpolygon":"Dessiner un polygone","gpControlEditingToolbar.dragfeature":"Déplacer un objet","gpControlEditingToolbar.modifyfeature":"Modifier un objet","gpControlEditingToolbar.deletefeature":"Détruire un objet","gpControlEditingToolbar.selectfeature":"Sélectionner un objet","gpControlEditingToolbar.navigation":"Naviguer","gpControlAddImageLayer.title":"Ajouter une couche image ...","gpControlAddImageLayer.layerUrl":"URL : ","gpControlAddImageLayer.layerUrl.help":"adresse de base du service","gpControlAddImageLayer.layerType":"Type : ","gpControlAddImageLayer.layerType.help":"choisissez une valeur dans la liste","gpControlAddImageLayer.layerType.WMS":"Web Map Service","gpControlAddImageLayer.layerType.WMTS":"Web Map Tile Service","gpControlAddImageLayer.layerType.WMSC":"OSGeO WMS-C","gpControlAddImageLayer.button.add":"Ajouter","gpControlAddImageLayer.button.cancel":"Annuler","wms.caps.no.compatible.srs":"Pas de couche compatible trouvée","ogc.caps.unknown.service":"${serviceType} ne semble pas être un ${expectedType}","gpControlAddVectorLayer.title":"Ajouter une couche vectorielle ...","gpControlAddVectorLayer.layerName":"Nom : ","gpControlAddVectorLayer.layerName.help":"ex : ma couche","gpControlAddVectorLayer.layerType":"Type : ","gpControlAddVectorLayer.layerType.help":"choisissez une valeur dans la liste","gpControlAddVectorLayer.layerType.Point":"ponctuelle","gpControlAddVectorLayer.layerType.LineString":"linéaire","gpControlAddVectorLayer.layerType.Polygon":"surfacique","gpControlAddVectorLayer.layerType.KML":"ressource KML","gpControlAddVectorLayer.layerType.GPX":"ressource GPX","gpControlAddVectorLayer.layerType.OSM":"ressource OSM","gpControlAddVectorLayer.layerType.GeoRSS":"ressource GeoRSS","gpControlAddVectorLayer.layerType.WFS":"ressource WFS","gpControlAddVectorLayer.layerUrl":"URL : ","gpControlAddVectorLayer.layerUrl.help":"local ou à distance","gpControlAddVectorLayer.layerFreeHand":"Dessin à main levée ? ","gpControlAddVectorLayer.layerFreeHand.help":"permet le dessin à main levée","gpControlAddVectorLayer.button.add":"Ajouter","gpControlAddVectorLayer.button.cancel":"Annuler","gpControlAddVectorLayer.layerContent":"Contenu","gpControlAddVectorLayer.layerContent.help":"Copier / Coller les données","gpControlAddVectorLayer.layerUrlSwitch":"Par URL / Contenu","gpControlAddVectorLayer.layerUrlSwitch.help":"Fournir l'URL distante ou le contenu des données","wfs.caps.no.feature.found":"Aucun objet trouvé","wfs.caps.unsupported.version":"Version ${version} de WFS non supportée","gpControlLocationUtilityService.geonames.title":"Rechercher un lieu","gpControlLocationUtilityService.geocode.title":"Rechercher une adresse","gpControlLocationUtilityService.reverse.geocode.title":"Rechercher les adresses autour d'un point","gpControlLocationUtilityServiceGeoNames.title":"Rechercher un lieu :","gpControlLocationUtilityServiceGeoNames.name":"Lieu : ","gpControlLocationUtilityServiceGeoNames.name.help":"ex : Saint-Mandé","gpControlLocationUtilityServiceGeoNames.button.cancel":"Annuler","gpControlLocationUtilityServiceGeoNames.button.search":"Rechercher","gpControlLocationUtilityServiceGeocode.title":"Chercher une adresse :","gpControlLocationUtilityServiceGeocode.address":"Rue : ","gpControlLocationUtilityServiceGeocode.address.help":"ex : 73, avenue de Paris","gpControlLocationUtilityServiceGeocode.municipality":"Ville : ","gpControlLocationUtilityServiceGeocode.municipality.help":"ex : Saint-Mandé","gpControlLocationUtilityServiceGeocode.postalcode":"Code postal : ","gpControlLocationUtilityServiceGeocode.postalcode.help":"ex : 94165","gpControlLocationUtilityServiceGeocode.name":"Lieu : ","gpControlLocationUtilityServiceGeocode.name.help":"ex : Saint-Mandé ou 94165","gpControlLocationUtilityServiceGeocode.button.cancel":"Annuler","gpControlLocationUtilityServiceGeocode.button.search":"Rechercher","gpControlLocationUtilityServiceGeocode.matchType.city":"Localisation à la ville","gpControlLocationUtilityServiceGeocode.matchType.street":"Localisation à la rue","gpControlLocationUtilityServiceGeocode.matchType.number":"Localisation au numéro adresse exact","gpControlLocationUtilityServiceGeocode.matchType.enhanced":"Localisation interpolée entre deux adresses","gpControlLocationUtilityServiceReverseGeocode.title":"Chercher des lieux à proximité :","gpControlLocationUtilityServiceReverseGeocode.longitude":"Longitude : ","gpControlLocationUtilityServiceReverseGeocode.longitude.help":"ex : dd.mmss en coordonnées géographiques","gpControlLocationUtilityServiceReverseGeocode.latitude":"Latitude : ","gpControlLocationUtilityServiceReverseGeocode.latitude.help":"ex : dd.mmss en coordonnées géographiques","gpControlLocationUtilityServiceReverseGeocode.button.cancel":"Annuler","gpControlLocationUtilityServiceReverseGeocode.button.search":"Rechercher","gpControlCSW.title":"Rechercher dans le géocatalogue","gpControlCSW.cswTitle":"Titre : ","gpControlCSW.cswTitle.help":" ","gpControlCSW.cswKeyWords":"Mot-clés : ","gpControlCSW.cswKeyWords.help":" ","gpControlCSW.cswKeyWords.NoKeyWords":" ","gpControlCSW.cswKeyWords.Addresses":"Adresses","gpControlCSW.cswKeyWords.AdministrativeUnits":"Unités administratives","gpControlCSW.cswKeyWords.Agricultural":"Installations agricoles et aquacoles","gpControlCSW.cswKeyWords.RegulationZones":"Zones de gestion, de restriction ou de réglementation et unités de déclaration","gpControlCSW.cswKeyWords.Atmospheric":"Conditions atmosphériques","gpControlCSW.cswKeyWords.BioGeographical":"Régions biogéographiques","gpControlCSW.cswKeyWords.Buildings":"Bâtiments","gpControlCSW.cswKeyWords.Cadastral":"Parcelles cadastrales","gpControlCSW.cswKeyWords.CoordinateSystems":"Référentiels de coordonnées","gpControlCSW.cswKeyWords.Elevation":"Altitude","gpControlCSW.cswKeyWords.Energy":"Sources d'énergie","gpControlCSW.cswKeyWords.EnvironmentalFacilities":"Installations de suivi environnemental","gpControlCSW.cswKeyWords.GeographicalSystems":"Systèmes de maillage géographique","gpControlCSW.cswKeyWords.GeographicalNames":"Dénominations géographiques","gpControlCSW.cswKeyWords.Geology":"Géologie","gpControlCSW.cswKeyWords.Habitats":"Habitats et biotopes","gpControlCSW.cswKeyWords.HumanHealth":"Santé et sécurité des personnes","gpControlCSW.cswKeyWords.Hydrography":"Hydrographie","gpControlCSW.cswKeyWords.LandCover":"Occupation des terres","gpControlCSW.cswKeyWords.LandUse":"Usage des sols","gpControlCSW.cswKeyWords.Meteorological":"Caractéristiques géographiques météorologiques","gpControlCSW.cswKeyWords.Mineral":"Ressources minérales","gpControlCSW.cswKeyWords.NaturalRiskZones":"Zones à risque naturel","gpControlCSW.cswKeyWords.Oceanographic":"Caractéristiques géographiques océanographiques","gpControlCSW.cswKeyWords.Orthoimagery":"Ortho-imagerie","gpControlCSW.cswKeyWords.Population":"Répartition de la population — Démographie","gpControlCSW.cswKeyWords.Production":"Lieux de production et sites industriels","gpControlCSW.cswKeyWords.ProtectedSites":"Sites protégés","gpControlCSW.cswKeyWords.SeaRegions":"Régions maritimes","gpControlCSW.cswKeyWords.Soil":"Sols","gpControlCSW.cswKeyWords.SpeciesDistribution":"Répartition des espèces","gpControlCSW.cswKeyWords.StatisticalUnits":"Unités statistiques","gpControlCSW.cswKeyWords.TransportNetworks":"Réseaux de transport","gpControlCSW.cswKeyWords.UtilityServices":"Services d'utilité publique et services publics","gpControlCSW.cswOrganism":"Organisme : ","gpControlCSW.cswOrganism.help":" ","gpControlCSW.button.cancel":"Annuler","gpControlCSW.button.search":"Rechercher","gpControlCSW.cswBBOX":"Emprise : ","gpControlCSW.cswNoBBOX":"Mondiale","gpControlCSW.cswNoBBOX.help":" ","gpControlCSW.cswCurrentBBOX":"Emprise courante","gpControlCSW.cswCurrentBBOX.help":" ","gpControlCSW.cswPersonnalBBOX":"Sélectionner une emprise","gpControlCSW.cswPersonnalBBOX.help":" ","gpControlPageManager.button.previous":"<","gpControlPageManager.button.next":">","azimuth.measurement":"Azimuth","gpControlMeasureAzimuth.title":"Mesure d'azimuth","gpControlMeasureAzimuth.azimuth":"Azimuth","gpControlMeasureAzimuth.azimuth.help":"angle horizontal entre la direction d'un objet et le nord géographique","gpControlMeasureAzimuth.distance":"Distance","gpControlMeasureAzimuth.distance.help":"Longueur","gpControlPrintMap.title":"Aperçu de la carte avant impression","gpControlPrintMap.comments":"Vos notes ou commentaires","approx.scale":"Échelle approximative: 1:","approx.center":"Coordonnées géographiques du centre de la carte","gpControlPrintMap.print.forbidden":"Tous droits réservés","gpControlPrintMap.print":"Imprimer",gpControlInformationMini:"Cliquer pour rendre visible les informations","OpenLayers.Control.WMSGetFeatureInfo.title":"Identifier les objets","gpControlAddAttributeToLayer.title":"Ajouter / Retirer des attributs","gpControlAddAttributeToLayer.attName":"Nom","gpControlAddAttributeToLayer.attName.help":"nom de l'attribut à ajouter","gpControlAddAttributeToLayer.attDefaultValue":"Valeur par défaut","gpControlAddAttributeToLayer.attDefaultValue.help":"valeur par défaut de l'attribut","gpControlAddAttributeToLayer.attList":"Attributs","gpControlAddAttributeToLayer.attList.help":"présents actuellement","gpControlAddAttributeToLayer.button.cancel":"Terminer","gpControlAddAttributeToLayer.button.addatt":"Ajouter","gpControlAddAttributeToLayer.button.delatt":"Retirer","gpControlAddAttributeToLayer.emptyName":"Le nom est vide","gpControlAddAttributeToLayer.existingName":"${name} existe déjà","gpControlLayerStyling.title":"Modifier la représentation cartographique","gpControlLayerStyling.color":"Couleur des traits et remplissage","gpControlLayerStyling.color.help":"Clic pour ouvrir le panneau de couleurs, Clic en dehors pour le fermer","gpControlLayerStyling.size":"Taille","gpControlLayerStyling.size.help":"Clic pour ouvrir le panneau de changement des tailles","gpControlLayerStyling.style":"Représentation","gpControlLayerStyling.style.help":"Clic pour ouvrir le panneau de changement des styles","gpControlLayerStyling.rotation":"Angle de rotation (en degré) ","gpControlLayerStyling.rotation.help":"Clic pour choisir un angle entre 0 et 360°","gpControlLayerStyling.externalgraphic":"URL du pictogramme","gpControlLayerStyling.externalgraphic.help":"Indiquer l'URL du pictogramme - vide pour aucun","gpControlLayerStyling.button.cancel":"Annuler","gpControlLayerStyling.button.changestyle":"Modifier","gpControlLayerStyling.emptyColor":"La couleur est vide (#XXXXXX attendu)","gpControlSaveLayer.title":"Sauvegarder la couche","gpControlSaveLayer.format":"Format de sauvegarde","gpControlSaveLayer.gml":"GML","gpControlSaveLayer.kml":"KML","gpControlSaveLayer.gpx":"Traces GPX","gpControlSaveLayer.osm":"OpenStreetMap XML Export","gpControlSaveLayer.gxt":"Géoconcept Export","gpControlSaveLayer.format.help":"format pour exporter les données","gpControlSaveLayer.proj":"Système de référence de coordonnées","gpControlSaveLayer.proj.help":"coordonnées pour l'export","gpControlSaveLayer.pretty":"Affichage amélioré","gpControlSaveLayer.pretty.help":"pour rendre lisible l'export des données","gpControlSaveLayer.button.cancel":"Annuler","gpControlSaveLayer.button.save":"Sauvegarder","gpControlSaveLayer.noData":"La couche est vide, aucune données à sauvegarder","lus.not.match":"Pas de correspondance trouvée","csw.not.match":"Pas de fiche de métadonnées trouvée","geocoded.address.popup.title":"Adresse","geocoded.address.popup.postalCode":"Code postal","geocoded.address.popup.places":"Lieux",CountrySubdivision:"Département",CountrySecondarySubdivision:"Commune",Municipality:"Ville",MunicipalitySubdivision:"Quartier",TOS:"Conditions générales d'utilisation","utm.zone":"UTM","*":""};
Geoportal.Lang.de={ATF:"Französisch Südliche Territorien",FXX:"Frankreich Festland",GLP:"Guadeloupe",GUF:"Französisch-Guayana",MTQ:"Martinique",MYT:"Mayotte",NCL:"Neukaledonien",PYF:"Französisch-Polynesien",REU:"Réunion",SPM:"Saint Pierre und Miquelon",WLF:"Wallis und Futuna",ANF:"Französisch Antillen",ASP:"Saint Paul und Amsterdam",CRZ:"Crozet",EUE:"Europa",KER:"Kerguelen",SBA:"Saint Barthélémy",SMA:"Saint Martin",WLD:"Die Welt","GEOGRAPHICALGRIDSYSTEMS.MAPS":"IGN Karten","GEOGRAPHICALGRIDSYSTEMS.MAPS.description":"Die karten stammen aus den SCAN-datenbanken des IGN: Welt, Politisches Europa, SCAN 1000®, SCAN 500®, SCAN Régional®, SCAN 200®, SCAN Départemental®, SCAN 100®, SCAN 50®, SCAN 25®.","ORTHOIMAGERY.ORTHOPHOTOS":"Orthofotografie","ORTHOIMAGERY.ORTHOPHOTOS.description":"Die Luftaufnahmen verbinden die präzision der geometrie mit der detailreichhaltigkeit der photographie, auflösung zwischen 50 und 15 cm, oder der satellitenbilder, auflösung zwischen 10 m und 20 m.","ELEVATION.SLOPES":"Hypsometrische farben - Höhe","ELEVATION.SLOPES.description":"Die digitalen geländemodelle stammen aus den BD ALTI® daten, die das französische staatsgebiet mit seinen höhenlinien darstellt. Die abstandsgleichheit der linien reicht von 5 bis 40 m. Die ausgangsdaten wurden auf IGN-Karten im Maßstab von 1 : 25.000,  1 : 50.000 und, ausgehend von einer wiedergabe der luftaufnahmen, im Maßstab von 1 : 20.000, 1 : 30.000 und 1 : 60.000 erfaßt.","ELEVATION.LEVEL0":"Küstenlinie - Höhe","ELEVATION.LEVEL0.description":"Die 0 des Meeres stammt aus LITTO3D®, eine gemeinsame produktion des SHOM und des IGN.","CADASTRALPARCELS.PARCELS":"Katasterparzellen (Flurstücke/Grundstücke)","CADASTRALPARCELS.PARCELS.description":"Die digitale katasterinformation ist für das gesamte französische staatsgebiet durchgehend und georeferenziert. Sie wurde ausgehend von der zusammenstellung des entmaterialisierten katasterplans hergestellt.","HYDROGRAPHY.HYDROGRAPHY":"Gewässernetz","HYDROGRAPHY.HYDROGRAPHY.description":"Die hydrographie der erde wurde ausgehend von der zusammenstellung der daten des BD TOPO®, BD CARTHAGE®, EuroRegionalMal und EuroGlobalMap erstellt.","TRANSPORTNETWORKS.ROADS":"Straßennetz - Verkehrsnetze","TRANSPORTNETWORKS.ROADS.description":"Das straßennetz wurde ausgehend von der zusammenstellung der daten des BD TOPO®, BD CARTO®, EuroRegionalMap und EuroGlobalMap erstellt.","TRANSPORTNETWORKS.RAILWAYS":"Schienennetz - Verkehrsnetze","TRANSPORTNETWORKS.RAILWAYS.description":"Das schienennetz wurde ausgehend von der zusammenstellung der daten des BD TOPO®, BD CARTO®, EuroRegionalMap und EuroGlobalMap erstellt.","TRANSPORTNETWORKS.RUNWAYS":"Luftverkehrnetz - Verkehrsnetze","TRANSPORTNETWORKS.RUNWAYS.description":"Die flughäfen und flugplätze wurden ausgehend von der zusammenstellung der daten des BD TOPO® und BD CARTO® erfaßt.","BUILDINGS.BUILDINGS":"Gebäude","BUILDINGS.BUILDINGS.description":"Die gebäude und bebaute gebiete wurden ausgehend von der zusammenfügung der daten des BD TOPO®, BD CARTO®, EuroRegionalMap und EuroGlobalMap erfaßt.","UTILITYANDGOVERNMENTALSERVICES.ALL":"Versorgungswirtschaft und staatliche Dienste","UTILITYANDGOVERNMENTALSERVICES.ALL.description":"Die verschiedenen energietransportnetze wurden ausgehend von der zusammenstellung der daten des BD TOPO® und BD CARTO® erfaßt.","ADMINISTRATIVEUNITS.BOUNDARIES":"Verwaltungseinheiten","ADMINISTRATIVEUNITS.BOUNDARIES.description":"Die verwaltungseinheiten frankreichs wurden ausgehend von der zusammenstellung der daten des BD TOPO®, BD CARTO®, EuroRegionalMap und EuroBoundaryMap erfaßt.","LANDCOVER.CORINELANDCOVER":"Corine LC","LANDCOVER.CORINELANDCOVER.description":"CORINE Land Cover (2006)","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS":"Coastal Karten","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS.description":"Coastal Karten","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS":"topographischen Typ 1900 Karten","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS.description":"Karten bei 1: 50 000 (nach der ersten Ausgabe der IGN Karte Library)","GEOGRAPHICALGRIDSYSTEMS.CASSINI":"Cassini-Karten","GEOGRAPHICALGRIDSYSTEMS.CASSINI.description":"Cassini-Karten","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000":"Ortho-Fotografie der Küste (2000)","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000.description":"Ortho-Fotografie von der Küste der Nordsee, Ärmelkanal und Atlantik","LANDUSE.AGRICULTURE2007":"Landwirtschaftlicher Parzellen (2007)","LANDUSE.AGRICULTURE2007.description":"Diese Schicht zeigt das Inselchen anonymisierter Registrierung Parcel Graph (RPG) und ihre Haupternte Gruppe berichtet im Jahr 2007 durch die Landwirte aus GAP-Subventionen profitieren.","LANDUSE.AGRICULTURE2008":"Landwirtschaftlicher Parzellen (2008)","LANDUSE.AGRICULTURE2008.description":"Diese Schicht zeigt das Inselchen anonymisierter Registrierung Parcel Graph (RPG) und ihre Haupternte Gruppe berichtet im Jahr 2008 durch die Landwirte aus GAP-Subventionen profitieren.","LANDUSE.AGRICULTURE2009":"Landwirtschaftlicher Parzellen (2009)","LANDUSE.AGRICULTURE2009.description":"Diese Schicht zeigt das Inselchen anonymisierter Registrierung Parcel Graph (RPG) und ihre Haupternte Gruppe berichtet im Jahr 2009 durch die Landwirte aus GAP-Subventionen profitieren.","LANDUSE.AGRICULTURE2010":"Landwirtschaftlicher Parzellen (2010)","LANDUSE.AGRICULTURE2010.description":"Diese Schicht zeigt das Inselchen anonymisierter Registrierung Parcel Graph (RPG) und ihre Haupternte Gruppe berichtet im Jahr 2010 durch die Landwirte aus GAP-Subventionen profitieren.","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE":"Genfer Staatsrat","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE.description":"Ortho-Fotografie, 10 cm Auflösung (2010).","ORTHOIMAGERY.ORTHOPHOTOS2000-2005":"Orthofotografie (2000-2005)","ORTHOIMAGERY.ORTHOPHOTOS2000-2005.description":"1. National Berichterstattung (2000-2005) von Ortho-Bilder mit 50 cm Auflösung.","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER":"FranceRaster®","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER.description":"FranceRaster® ist eine Serie von georeferenzierten Bildern für Frankreich Festland und Übersee-Territorien. Es ist mit dem Vektor-Datenbank der IGN am besten jede Skala mit einer einheitlichen Symbolik geeignet hergestellt. Nach den Maßstäben, FranceRaster ®, ermöglicht die Anzeige der Themen Straßen-und Schienenverkehr, Rahmen, Gewässerkunde, Vegetation, Adressen, Fahrtrichtung, Namen ...","NATURALRISKZONES.1910FLOODEDWATERSHEDS":"Seine (PHEC)","NATURALRISKZONES.1910FLOODEDWATERSHEDS.description":"Diese Karte Schicht stellt die bekannten höchsten Wasserstand (PHEC) auf das Einzugsgebiet der Seine, dh Gebiete mit den größten bekannten Hochwasser überflutet und dokumentiert auf jedem Fluss.","NATURALRISKZONES.1910FLOODEDCELLARS":"Überflutete Keller (1910)","NATURALRISKZONES.1910FLOODEDCELLARS.description":"Karte von überschwemmten Kellern während der Seine Flut von 1910. Diese Karte basiert auf Zeugenaussagen basieren.","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40":"État-Major-Karte (1:40 000)","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40.description":"Diese Schicht wird durch die Montage von dem 978 Original-Zeichnungen von der Karte von état-major gebildet gegründet im neunzehnten Jahrhundert. Diese handschriftliche und farbige Erhebungen, bei der 1: 40 000 wurden zwischen 1825 und 1866 errichtet.","LANDCOVER.FORESTINVENTORY.V1":"Waldinventur (v1)","LANDCOVER.FORESTINVENTORY.V1.description":"Waldinventur (1987-2004)","LANDCOVER.FORESTINVENTORY.V2":"Waldinventur (v2)","LANDCOVER.FORESTINVENTORY.V2.description":"Waldinventur (2005+)","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS":"Karte von Verwaltungsabteilungen","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS.description":"Karte von Verwaltungsabteilungen","TOPONYMS.ALL":"Suche nach Standort","TOPONYMS.ALL.description":"Verwendet die Datenbank BD Nyme®.","ADDRESSES.CROSSINGS":"Suche nach Adresse","ADDRESSES.CROSSINGS.description":"Verwendet die Datenbank Routes Adresses®.","GEOGRAPHICALNAMES.NAMES":"Geographische Namen","GEOGRAPHICALNAMES.NAMES.description":"Geographische Namen","div.not.found":"${id} konnte nicht in dem dokument gefunden werden: Prüfen sie, ob ${id} auch wirklich existiert.","proxy.not.set":"Keine Proxy-konfiguration: Informationen zu dem vertrag könnten möglicherweise nicht abgerufen werden.","cookies.not.enabled":"Cookies sind deaktiviert. Bitte aktivieren.","geoRM.getConfig":"Die informationen zum schlüßel '${key}' können nicht abgerufen werden - überprüfen sie ihre verbindung.","geoRM.getToken":"Die datei zum schlüßel '${key}' kann nicht abgerufen werden - überprüfen sie ihre verbindung.","geoRM.failed":"Kein gültiges zeichen erhalten zum schlüßel '${key}'","geoRM.forbidden":"API-Schlüssel fehlt oder ${layer} nicht durch die Anwendung von API Vertrag unterstützt.","url.error":"Fehler beim Laden '${url}' ${msg}","GPX.version":"Version ${gpxVersion} GPX nicht gestützt.","XLS.version":"Version ${xlsVersion} XLS wird vom service ${coreService} nicht gestützt.","Not.conformal.XLS":"${part} fehlt im XML","olControlMeasurePath.title":"Entfernungsmeßung","olControlMeasurePolygon.title":"Flüchenmeßung","waiting.measurement":"In erwartung der eingabe ...","length.measurement":"Länge","area.measurement":"Fläche","gpControlLayerSwitcher.label":"Schichten","gpLayer.metadataURL":"Mehr informationen zu dieser schicht...","gpLayer.dataURL":"Zugang zum download-service","gpControlPanelToggle.closed":"Die tool-leiste anzeigen","gpControlPanelToggle.opened":"Die tool-leiste ausblenden","gpControlRemoveLayer.title":"Die schicht entfernen","gpControlLayerOpacity.title":"Die transparenz einstellen","gpControlZoomToLayerMaxExtent.title":"Auf die bodenfläche der schicht zoomen","gpControlMousePosition.longitude":"Längengrad","gpControlMousePosition.latitude":"Breitengrad","gpControlMousePosition.easting":"Absziße","gpControlMousePosition.northing":"Ordinate","gpControlMousePosition.north":"N","gpControlMousePosition.south":"S","gpControlMousePosition.east":"O","gpControlMousePosition.west":"W","gpControlMousePosition.sexa":"sexagecimal grades","gpControlMousePosition.deg":"dezimale grades","gpControlMousePosition.gon":"grad","gpControlMousePosition.rad":"radians","gpControlMousePosition.km":"kilometers","gpControlMousePosition.m":"meters","gpControlMousePosition.cm":"zentimeters","gpControlMousePosition.utmZone":"Zone","gpControlToolBox.label":"Tools","gpControlZoomBar.world":"Welt","gpControlZoomBar.state":"Staat","gpControlZoomBar.country":"Land","gpControlZoomBar.town":"Stadt","gpControlZoomBar.street":"Straße","gpControlZoomBar.house":"Haus","gpControlEditingToolbar.drawpoint":"Einen punkt platzieren","gpControlEditingToolbar.drawline":"Eine linie zeichnen","gpControlEditingToolbar.drawpolygon":"Ein polygon zeichnen","gpControlEditingToolbar.dragfeature":"Ein objekt verschieben","gpControlEditingToolbar.modifyfeature":"Ein objekt verändern","gpControlEditingToolbar.deletefeature":"Ein objekt vernichten","gpControlEditingToolbar.selectfeature":"Ein objekt auswählen","gpControlEditingToolbar.navigation":"Navigieren","gpControlAddImageLayer.title":"Eine bild-schicht hinzufügen","gpControlAddImageLayer.layerUrl":"URL : ","gpControlAddImageLayer.layerUrl.help":"basis-adresse des dienstes","gpControlAddImageLayer.layerType":"Typ : ","gpControlAddImageLayer.layerType.help":"Wählen sie einen wert der liste","gpControlAddImageLayer.layerType.WMS":"Web Map Service","gpControlAddImageLayer.layerType.WMTS":"Web Map Tile Service","gpControlAddImageLayer.layerType.WMSC":"OSGeO WMS-C","gpControlAddImageLayer.button.add":"Hinzufügen","gpControlAddImageLayer.button.cancel":"Abbrechen","wms.caps.no.compatible.srs":"Nicht kompatibel schicht gefunden","ogc.caps.unknown.service":"${serviceType} ist kein ${expectedType}","gpControlAddVectorLayer.title":"Eine vektorielle schicht hinzufügen","gpControlAddVectorLayer.layerName":"Name : ","gpControlAddVectorLayer.layerName.help":"z.B.: meine schicht","gpControlAddVectorLayer.layerType":"Typ : ","gpControlAddVectorLayer.layerType.help":"Wählen sie einen wert der liste","gpControlAddVectorLayer.layerType.Point":"punktuell","gpControlAddVectorLayer.layerType.LineString":"linear","gpControlAddVectorLayer.layerType.Polygon":"flächenbezogen","gpControlAddVectorLayer.layerType.KML":"KML resource","gpControlAddVectorLayer.layerType.GPX":"GPX resource","gpControlAddVectorLayer.layerType.OSM":"OSM resource","gpControlAddVectorLayer.layerType.GeoRSS":"GeoRSS resource","gpControlAddVectorLayer.layerType.WFS":"WFS resource","gpControlAddVectorLayer.layerUrl":"URL : ","gpControlAddVectorLayer.layerUrl.help":"vor ort oder aus entfernung","gpControlAddVectorLayer.layerFreeHand":"freihandzeichnen ? ","gpControlAddVectorLayer.layerFreeHand.help":"ermöglicht das freihandzeichnen","gpControlAddVectorLayer.button.add":"Hinzufügen","gpControlAddVectorLayer.button.cancel":"Abbrechen","gpControlAddVectorLayer.layerContent":"Inhalt","gpControlAddVectorLayer.layerContent.help":"Copy / Paste Data","gpControlAddVectorLayer.layerUrlSwitch":"Durch URL / Content","gpControlAddVectorLayer.layerUrlSwitch.help":"Sie stellen eine URL oder den Inhalt von Daten","wfs.caps.no.feature.found":"Keine objekte gefunden","wfs.caps.unsupported.version":"WFS version ${version} nicht unterstützt","gpControlLocationUtilityService.geonames.title":"Einen ort suchen","gpControlLocationUtilityService.geocode.title":"Eine adreße suchen","gpControlLocationUtilityService.reverse.geocode.title":"Die Adreßen um einen punkt herum suchen","gpControlLocationUtilityServiceGeoNames.title":"Einen ort suchen :","gpControlLocationUtilityServiceGeoNames.name":"Ort : ","gpControlLocationUtilityServiceGeoNames.name.help":"z.B. : Saint-Mandé","gpControlLocationUtilityServiceGeoNames.button.cancel":"Abbrechen","gpControlLocationUtilityServiceGeoNames.button.search":"Suchen","gpControlLocationUtilityServiceGeocode.title":"Eine Adreße suchen :","gpControlLocationUtilityServiceGeocode.address":"Straße : ","gpControlLocationUtilityServiceGeocode.address.help":"ex : 73, avenue de Paris","gpControlLocationUtilityServiceGeocode.municipality":"Stadt : ","gpControlLocationUtilityServiceGeocode.municipality.help":"z.B. : Saint-Mandé","gpControlLocationUtilityServiceGeocode.postalcode":"Postleitzahl:","gpControlLocationUtilityServiceGeocode.postalcode.help":"z.B. : 94165","gpControlLocationUtilityServiceGeocode.name":"Ort:","gpControlLocationUtilityServiceGeocode.name.help":"z.B.: Saint-Mandé oder 94165","gpControlLocationUtilityServiceGeocode.button.cancel":"Abbrechen","gpControlLocationUtilityServiceGeocode.button.search":"Suchen","gpControlLocationUtilityServiceGeocode.matchType.city":"Lage in der Stadt","gpControlLocationUtilityServiceGeocode.matchType.street":"Lage auf der Straße","gpControlLocationUtilityServiceGeocode.matchType.number":"Lage in der genauen Anschrift Zahl","gpControlLocationUtilityServiceGeocode.matchType.enhanced":"Lage zwischen zwei Adressen interpoliert","gpControlLocationUtilityServiceReverseGeocode.title":"Orte in der nähe suchen:","gpControlLocationUtilityServiceReverseGeocode.longitude":"Längengrad : ","gpControlLocationUtilityServiceReverseGeocode.longitude.help":"z.B. : dd.mmss in in geographischen koordinaten","gpControlLocationUtilityServiceReverseGeocode.latitude":"Breitengrad : ","gpControlLocationUtilityServiceReverseGeocode.latitude.help":"z.B. : dd.mmss in geographischen koordinaten","gpControlLocationUtilityServiceReverseGeocode.button.cancel":"Abbrechen","gpControlLocationUtilityServiceReverseGeocode.button.search":"Suchen","gpControlCSW.title":"Rechercher dans le géocatalogue","gpControlCSW.cswTitle":"Titre : ","gpControlCSW.cswTitle.help":" ","gpControlCSW.cswKeyWords":"Keywords : ","gpControlCSW.cswKeyWords.help":" ","gpControlCSW.cswKeyWords.NoKeyWords":" ","gpControlCSW.cswKeyWords.Addresses":"Adressen","gpControlCSW.cswKeyWords.AdministrativeUnits":"Verwaltungseinheiten","gpControlCSW.cswKeyWords.Agricultural":"Landwirtschaftliche Anlagen und Aquakulturanlagen","gpControlCSW.cswKeyWords.RegulationZones":"Bewirtschaftungsgebiete/Schutzgebiete/geregelte Gebiete und Berichterstattungseinheiten","gpControlCSW.cswKeyWords.Atmospheric":"Atmosphärische Bedingungen","gpControlCSW.cswKeyWords.BioGeographical":"Biogeografische Regionen","gpControlCSW.cswKeyWords.Buildings":"Gebäude","gpControlCSW.cswKeyWords.Cadastral":"Flurstücke/Grundstücke (Katasterparzellen)","gpControlCSW.cswKeyWords.CoordinateSystems":"Koordinatenreferenzsysteme","gpControlCSW.cswKeyWords.Elevation":"Höhe","gpControlCSW.cswKeyWords.Energy":"Energiequellen","gpControlCSW.cswKeyWords.EnvironmentalFacilities":"Umweltüberwachung","gpControlCSW.cswKeyWords.GeographicalSystems":"Geografische Gittersysteme","gpControlCSW.cswKeyWords.GeographicalNames":"Geografische Bezeichnungen","gpControlCSW.cswKeyWords.Geology":"Geologie","gpControlCSW.cswKeyWords.Habitats":"Lebensräume und Biotope","gpControlCSW.cswKeyWords.HumanHealth":"Gesundheit und Sicherheit","gpControlCSW.cswKeyWords.Hydrography":"Gewässernetz","gpControlCSW.cswKeyWords.LandCover":"Bodenbedeckung","gpControlCSW.cswKeyWords.LandUse":"Bodennutzung","gpControlCSW.cswKeyWords.Meteorological":"Meteorologisch-geografische Kennwerte","gpControlCSW.cswKeyWords.Mineral":"Mineralische Bodenschätze","gpControlCSW.cswKeyWords.NaturalRiskZones":"Gebiete mit naturbedingten Risiken","gpControlCSW.cswKeyWords.Oceanographic":"Ozeanografisch-geografische Kennwerte","gpControlCSW.cswKeyWords.Orthoimagery":"Orthofotografie","gpControlCSW.cswKeyWords.Population":"Verteilung der Bevölkerung — Demografie","gpControlCSW.cswKeyWords.Production":"Produktions- und Industrieanlagen","gpControlCSW.cswKeyWords.ProtectedSites":"Schutzgebiete","gpControlCSW.cswKeyWords.SeaRegions":"Meeresregionen","gpControlCSW.cswKeyWords.Soil":"Boden","gpControlCSW.cswKeyWords.SpeciesDistribution":"Verteilung der Arten","gpControlCSW.cswKeyWords.StatisticalUnits":"Statistische Einheiten","gpControlCSW.cswKeyWords.TransportNetworks":"Verkehrsnetze","gpControlCSW.cswKeyWords.UtilityServices":"Versorgungswirtschaft und staatliche Dienste","gpControlCSW.cswOrganism":"Organisme : ","gpControlCSW.cswOrganism.help":" ","gpControlCSW.button.cancel":"Abbrechen","gpControlCSW.button.search":"Suchen","gpControlCSW.cswBBOX":"BBOX","gpControlCSW.cswNoBBOX":"Mondiale","gpControlCSW.cswNoBBOX.help":" ","gpControlCSW.cswCurrentBBOX":"Emprise courante","gpControlCSW.cswCurrentBBOX.help":" ","gpControlCSW.cswPersonnalBBOX":"Sélectionner une emprise","gpControlCSW.cswPersonnalBBOX.help":" ","gpControlPageManager.button.previous":"<","gpControlPageManager.button.next":">","azimuth.measurement":"Azimuth","gpControlMeasureAzimuth.title":"Azimuth messung","gpControlMeasureAzimuth.azimuth":"Azimuth","gpControlMeasureAzimuth.azimuth.help":"im Uhrzeigersinn gemessenen Winkel zwischen geografisch-Nord (Nordpol) und einer beliebigen Richtung auf der Erdoberfläche","gpControlMeasureAzimuth.distance":"Länge","gpControlMeasureAzimuth.distance.help":"Länge","gpControlPrintMap.title":"Druck-Vorschau anzeigen","gpControlPrintMap.comments":"Ihre Anmerkungen oder Kommentare","approx.scale":"Ungefähren Maßstab: 1:","approx.center":"Geographische Koordinaten der Mitte der Karte","gpControlPrintMap.print.forbidden":"All rights reserved","gpControlPrintMap.print":"Drucken",gpControlInformationMini:"Klicken Sie auf Informationen Virenwarnung sichtbar","OpenLayers.Control.WMSGetFeatureInfo.title":"Identifikation von Objekten","gpControlAddAttributeToLayer.title":"Hinzufügen / Entfernen von Attributen","gpControlAddAttributeToLayer.attName":"Name","gpControlAddAttributeToLayer.attName.help":"name des hinzuzufügenden Attributs","gpControlAddAttributeToLayer.attDefaultValue":"Default","gpControlAddAttributeToLayer.attDefaultValue.help":"default-Wert Attribut","gpControlAddAttributeToLayer.attList":"Attribute","gpControlAddAttributeToLayer.attList.help":"präsent sein","gpControlAddAttributeToLayer.button.cancel":"Finish","gpControlAddAttributeToLayer.button.addatt":"Add","gpControlAddAttributeToLayer.button.delatt":"Remove","gpControlAddAttributeToLayer.emptyName":"Der Name ist leer","gpControlAddAttributeToLayer.existingName":"${name} bereits vorhanden","gpControlLayerStyling.title":"Edit mapping","gpControlLayerStyling.color":"Color Linien und Füllung","gpControlLayerStyling.color.help":"klicken Sie auf die Farbpalette zu öffnen, klicken Sie außerhalb beendet werden","gpControlLayerStyling.size":"Size","gpControlLayerStyling.size.help":"klicken, um das Panel zu Größen ändern zu eröffnen","gpControlLayerStyling.style":"Vertretung","gpControlLayerStyling.style.help":"klicken, um das Fenster zum Ändern Stile offen","gpControlLayerStyling.rotation":"Drehwinkel (in Grad) ","gpControlLayerStyling.rotation.help":"klicken Sie auf einen Winkel zwischen 0 und 360°","gpControlLayerStyling.externalgraphic":"URL des Symbols","gpControlLayerStyling.externalgraphic.help":"geben Sie die URL der Ikone - nichts für","gpControlLayerStyling.button.cancel":"Cancel","gpControlLayerStyling.button.changestyle":"Change","gpControlLayerStyling.emptyColor":"Die Farbe ist leer (#XXXXXX erwartet)","gpControlSaveLayer.title":"Save Layer","gpControlSaveLayer.format":"Save Format","gpControlSaveLayer.gml":"GML","gpControlSaveLayer.kml":"KML","gpControlSaveLayer.gpx":"Traces GPX","gpControlSaveLayer.osm":"OpenStreetMap XML Export","gpControlSaveLayer.gxt":"Géoconcept Export","gpControlSaveLayer.format.help":"format die Daten exportieren","gpControlSaveLayer.proj":"Reference System der Koordinaten","gpControlSaveLayer.proj.help":"für den Export Koordinaten","gpControlSaveLayer.pretty":"Verbesserte Anzeige","gpControlSaveLayer.pretty.help":"um es lesbaren Daten exportieren","gpControlSaveLayer.button.cancel":"Cancel","gpControlSaveLayer.button.save":"Speichern","gpControlSaveLayer.noData":"Die Schicht ist leer, um keine Daten zu speichern","lus.not.match":"Es konnte keine übereinstimmung gefunden werden","csw.not.match":"keine Metadaten Datensatz gefunden","geocoded.address.popup.title":"Adreße","geocoded.address.popup.postalCode":"Postleitzahl","geocoded.address.popup.places":"Orte",CountrySubdivision:"Departement",CountrySecondarySubdivision:"Gemeinde",Municipality:"Stadt",MunicipalitySubdivision:"Stadtteil",TOS:"Allgemeine geschäftsbedingungen","utm.zone":"UTM","*":""};
Geoportal.Lang.es={ATF:"Tierras australes francés",FXX:"Francia continental",GLP:"Guadalupe",GUF:"Francés de Guayana",MTQ:"Martinica",MYT:"Mayotte",NCL:"Nueva Caledonia",PYF:"Polinesia francés",REU:"La Reunión",SPM:"San Pedro y Miquelón",WLF:"Wallis y Futuna",ANF:"Francés Antillas",ASP:"Saint Paul y Amsterdam",CRZ:"Crozet",EUE:"Europa",KER:"Kerguelen",SBA:"Saint Barthélémy",SMA:"Saint Martin",WLD:"El mundo","GEOGRAPHICALGRIDSYSTEMS.MAPS":"IGN Mapas","GEOGRAPHICALGRIDSYSTEMS.MAPS.description":"Los mapas se derivan de las bases de datos SCAN del IGN: Mundo, Europa politica databases : World, Europe, SCAN 1 000®, SCAN 500®, SCAN Régional®, SCAN 200®, SCAN Départemental®, SCAN 100®, SCAN 50®, SCAN 25®.","ORTHOIMAGERY.ORTHOPHOTOS":"Ortoimágenes","ORTHOIMAGERY.ORTHOPHOTOS.description":"Las fotografias aéreas combinan la precisión geométrica del mapa y la riqueza de la fotografia, una resolución entre 50 cm y 15 cm o, con imágenes de satélite, una resolución entre 10 m y 20 m.","ELEVATION.SLOPES":"Tintas hipsométricas - Elevaciones","ELEVATION.SLOPES.description":"Los modelos digitales del terreno se elaboran con información de la base de datos BD ALTI®, que describe el territorio francés mediante curvas de nivel. La equidistancia de las curvas varia de 5 m a 40 m. Los datos iniciales se han tomado de mapas del IGN a escalas 1:25 000, 1:50 000 y a partir de una restitución elaborada mediante tomas de vistas aéreas a escalas 1:20 000, 1:30 000 y 1:60 000.","ELEVATION.LEVEL0":"Linea de costa - Elevaciones","ELEVATION.LEVEL0.description":"La cota 0 (cero) de los mares se obtiene de LITTO3D®, una producción realizada en común entre el SHOM y el IGN.","CADASTRALPARCELS.PARCELS":"Parcelas catastrales","CADASTRALPARCELS.PARCELS.description":"La información catastral digital está georreferenciada y es continua sobre el conjunto del territorio francés. Se ha elaborado a partir del montaje del plan catastral desmaterializado.","HYDROGRAPHY.HYDROGRAPHY":"Hidrografia","HYDROGRAPHY.HYDROGRAPHY.description":"La hidrografia terrestre se obtiene de la combinación de datos de las bases de datos BD TOPO® y BD CARTHAGE® y del EuroRegionalMap y el EuroGlobalMap.","TRANSPORTNETWORKS.ROADS":"Red de Carreteras - Redes de transporte","TRANSPORTNETWORKS.ROADS.description":"La red de carreteras se obtiene de la combinación de datos de las bases de datos BD TOPO® y BD CARTO® y del EuroRegionalMap y el EuroGlobalMap.","TRANSPORTNETWORKS.RAILWAYS":"Red ferroviaria - Redes de transporte","TRANSPORTNETWORKS.RAILWAYS.description":"La red ferroviaria se obtiene de la combinación de datos de las bases de datos BD TOPO® y CARTO® y del EuroRegionalMap y el EuroGlobalMap.","TRANSPORTNETWORKS.RUNWAYS":"Red de aire - Redes de transporte","TRANSPORTNETWORKS.RUNWAYS.description":"Las pistas de los aeropuertos y aeródromos se obtienen de la combinación de datos de las bases de datos BD TOPO® y BD CARTO®.","BUILDINGS.BUILDINGS":"Edificios","BUILDINGS.BUILDINGS.description":"Los edificios y las zonas construidas se obtienen de la combinación de datos de las bases de datos BD TOPO® y BD CARTO® y del EuroRegionalMap y el EuroGlobalMap.","UTILITYANDGOVERNMENTALSERVICES.ALL":"Servicios de utilidad pública y estatales","UTILITYANDGOVERNMENTALSERVICES.ALL.description":"Las diversas redes de transporte de energia se obtienen de la combinación de datos de las bases de datos BD TOPO® y BD CARTO®.","ADMINISTRATIVEUNITS.BOUNDARIES":"Unidades administrativas","ADMINISTRATIVEUNITS.BOUNDARIES.description":"Las unidades administrativas de Francia se obtienen de la combinación de datos de las bases de datos BD TOPO® y BD CARTO® y del EuroRegionalMap y el EuroBoundaryMap.","LANDCOVER.CORINELANDCOVER":"Corine LC","LANDCOVER.CORINELANDCOVER.description":"CORINE Land Cover (2006)","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS":"Costeras mapas","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS.description":"Costeras mapas","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS":"mapas topográficos del tipo 1900","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS.description":"Mapas a escala 1: 50 000 (después de la primera edición de la Biblioteca Mapa IGN)","GEOGRAPHICALGRIDSYSTEMS.CASSINI":"Cassini mapas","GEOGRAPHICALGRIDSYSTEMS.CASSINI.description":"Cassini mapas","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000":"orto-fotografías de la costa (2000)","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000.description":"Orto-fotografía de la costa del Mar del Norte, el Canal Inglés y el Atlántico.","LANDUSE.AGRICULTURE2007":"Las parcelas agrícolas (2007)","LANDUSE.AGRICULTURE2007.description":"Esta capa muestra los islotes anónimos registro de paquetes gráfico (RPG) y su grupo de cultivos principales reportados en 2007 por los agricultores para beneficiarse de ayudas de la PAC.","LANDUSE.AGRICULTURE2008":"Las parcelas agrícolas (2008)","LANDUSE.AGRICULTURE2008.description":"Esta capa muestra los islotes anónimos registro de paquetes gráfico (RPG) y su grupo de cultivos principales reportados en 2008 por los agricultores para beneficiarse de ayudas de la PAC.","LANDUSE.AGRICULTURE2009":"Las parcelas agrícolas (2009)","LANDUSE.AGRICULTURE2009.description":"Esta capa muestra los islotes anónimos registro de paquetes gráfico (RPG) y su grupo de cultivos principales reportados en 2009 por los agricultores para beneficiarse de ayudas de la PAC.","LANDUSE.AGRICULTURE2010":"Las parcelas agrícolas (2010)","LANDUSE.AGRICULTURE2010.description":"Esta capa muestra los islotes anónimos registro de paquetes gráfico (RPG) y su grupo de cultivos principales reportados en 2010 por los agricultores para beneficiarse de ayudas de la PAC.","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE":"Del Estado de Ginebra","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE.description":"Orto-fotografías, 10 cm de resolución (2010).","ORTHOIMAGERY.ORTHOPHOTOS2000-2005":"Orto-fotografías (2000-2005)","ORTHOIMAGERY.ORTHOPHOTOS2000-2005.description":"Primera cobertura nacional (2000-2005) de orto-imágenes con una resolución de 50 cm.","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER":"FranceRaster®","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER.description":"FranceRaster® es una serie de imágenes georreferenciadas que abarcan el territorio continental de Francia y el extranjero. Se produce con la base de datos vectorial del IGN que mejor se adapte a cada escala con una simbología uniforme. De acuerdo con las escalas, FranceRaster ®, permite la visualización de las carreteras y el ferrocarril temas, el marco, hidrografía, vegetación, electrónico, dirección de viaje, nombres ...","NATURALRISKZONES.1910FLOODEDWATERSHEDS":"Seine (PHEC)","NATURALRISKZONES.1910FLOODEDWATERSHEDS.description":"Esta capa de mapa representa el agua más alta conocida (PHEC) en la cuenca del Sena, es decir, áreas geográficas inundados por la inundación más grande conocido y documentado en cada río.","NATURALRISKZONES.1910FLOODEDCELLARS":"Inundados sótanos (1910)","NATURALRISKZONES.1910FLOODEDCELLARS.description":"Mapa de sótanos inundados durante la inundación del Sena y de 1910. Este mapa se basa en testimonios.","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40":"Mapas de État-Major (1:40 000)","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40.description":"Esta capa se forma por la asamblea de los 978 dibujos originales del mapa de Estado Mayor de la establecida en el siglo XIX. Estas encuestas escritas a mano y coloreado, a la 1: 40 000, se establecieron entre 1825 y 1866.","LANDCOVER.FORESTINVENTORY.V1":"Inventario forestal (v1)","LANDCOVER.FORESTINVENTORY.V1.description":"Inventario forestal (1987-2004)","LANDCOVER.FORESTINVENTORY.V2":"Inventario forestal (v2)","LANDCOVER.FORESTINVENTORY.V2.description":"Inventario forestal (2005+)","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS":"Mapa de las divisiones administrativas","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS.description":"Mapa de las divisiones administrativas","TOPONYMS.ALL":"Búsqueda por ubicación","TOPONYMS.ALL.description":"Utiliza la base de datos BD Nyme®.","ADDRESSES.CROSSINGS":"Buscar por dirección","ADDRESSES.CROSSINGS.description":"Utiliza la base de datos Routes Adresses®.","GEOGRAPHICALNAMES.NAMES":"Los nombres geográficos","GEOGRAPHICALNAMES.NAMES.description":"Los nombres geográficos","div.not.found":"${id} no se encontró en el documento: verifique si '${id}' existe.","proxy.not.set":"No se ha configurado un proxy: la obtención de información sobre el contrato podria fallar.","cookies.not.enabled":"Las cookies son discapacitados. Por favor, les permiten.","geoRM.getConfig":"No es posible obtener información relativa a la clave '${key}' - Verifique su conexión.","geoRM.getToken":"No es posible obtener la ficha asociada a la clave '${key}' - Verifique su conexión.","geoRM.failed":"No pudo obtener una muestra válida relativa a la clave '${key}'","geoRM.forbidden":"Clave de la API no se encuentra o no ${layer} con el apoyo de contrato de la aplicación de la API.","url.error":"Error al cargar '${url}' ${msg}","GPX.version":"La versión ${gpxVersion} de GPX no es compatible","XLS.version":"La versión ${xlsVersion} de XLS no es compatible con el servicio ${coreService}","Not.conformal.XLS":"${part} falta en el XML","olControlMeasurePath.title":"Medida de distancia","olControlMeasurePolygon.title":"Medida de superficie","waiting.measurement":"A la espera de la selección...","length.measurement":"Longitud","area.measurement":"Superficie","gpControlLayerSwitcher.label":"Capas","gpLayer.metadataURL":"Más información sobre esta capa...","gpLayer.dataURL":"Acceso al servicio de descargas","gpControlPanelToggle.closed":"Mostrar la barra de herramientas","gpControlPanelToggle.opened":"Ocultar la barra de herramientas","gpControlRemoveLayer.title":"Quitar la capa","gpControlLayerOpacity.title":"Regla de transparencia","gpControlZoomToLayerMaxExtent.title":"Ampliar el área de la capa","gpControlMousePosition.longitude":"Longitud","gpControlMousePosition.latitude":"Latitud","gpControlMousePosition.easting":"Abscisa","gpControlMousePosition.northing":"Ordenada","gpControlMousePosition.north":"N","gpControlMousePosition.south":"S","gpControlMousePosition.east":"Or","gpControlMousePosition.west":"Oe","gpControlMousePosition.sexa":"grados sexagesimales","gpControlMousePosition.deg":"grados decimales","gpControlMousePosition.gon":"grados","gpControlMousePosition.rad":"radianes","gpControlMousePosition.km":"kilómetros","gpControlMousePosition.m":"metros","gpControlMousePosition.cm":"centimetros","gpControlMousePosition.utmZone":"Zona","gpControlToolBox.label":"Herra.","gpControlZoomBar.world":"Mundo","gpControlZoomBar.state":"Pais","gpControlZoomBar.country":"Provin.","gpControlZoomBar.town":"Ciudad","gpControlZoomBar.street":"Calle","gpControlZoomBar.house":"Casa","gpControlEditingToolbar.drawpoint":"Situar un punto","gpControlEditingToolbar.drawline":"Dibujar una linea","gpControlEditingToolbar.drawpolygon":"Dibujar un poligono","gpControlEditingToolbar.dragfeature":"Desplazar un objeto","gpControlEditingToolbar.modifyfeature":"Modificar un objeto","gpControlEditingToolbar.deletefeature":"Destruir un objeto","gpControlEditingToolbar.selectfeature":"Seleccionar un objeto","gpControlEditingToolbar.navigation":"Navegar","gpControlAddImageLayer.title":"Añadir una capa de imagen...","gpControlAddImageLayer.layerUrl":"URL : ","gpControlAddImageLayer.layerUrl.help":"Dirección base del servicio","gpControlAddImageLayer.layerType":"Tipo : ","gpControlAddImageLayer.layerType.help":"elija un valor de la lista","gpControlAddImageLayer.layerType.WMS":"Web Map Service","gpControlAddImageLayer.layerType.WMTS":"Web Map Tile Service","gpControlAddImageLayer.layerType.WMSC":"OSGeO WMS-C","gpControlAddImageLayer.button.add":"Añadir","gpControlAddImageLayer.button.cancel":"Cancelar","wms.caps.no.compatible.srs":"No compatible encontrado capa","ogc.caps.unknown.service":"${serviceType} no es un ${expectedType}","gpControlAddVectorLayer.title":"Añadir una capa vectorial...","gpControlAddVectorLayer.layerName":"Nombre : ","gpControlAddVectorLayer.layerName.help":"ejemplo : mi capa","gpControlAddVectorLayer.layerType":"Tipo : ","gpControlAddVectorLayer.layerType.help":"elija un valor de la lista","gpControlAddVectorLayer.layerType.Point":"puntual","gpControlAddVectorLayer.layerType.LineString":"lineal","gpControlAddVectorLayer.layerType.Polygon":"superficial","gpControlAddVectorLayer.layerType.KML":"recurso KML","gpControlAddVectorLayer.layerType.GPX":"recurso GPX","gpControlAddVectorLayer.layerType.OSM":"recurso OSM","gpControlAddVectorLayer.layerType.GeoRSS":"recurso GeoRSS","gpControlAddVectorLayer.layerType.WFS":"recurso WFS","gpControlAddVectorLayer.layerUrl":"URL : ","gpControlAddVectorLayer.layerUrl.help":"local o a distancia","gpControlAddVectorLayer.layerFreeHand":"Dibujo a mano alzada ? ","gpControlAddVectorLayer.layerFreeHand.help":"permite el dibujo a mano alzada","gpControlAddVectorLayer.button.add":"Añadir","gpControlAddVectorLayer.button.cancel":"Cancelar","gpControlAddVectorLayer.layerContent":"Contenido","gpControlAddVectorLayer.layerContent.help":"Copiar / Pegar datos","gpControlAddVectorLayer.layerUrlSwitch":"Por URL / contenidos","gpControlAddVectorLayer.layerUrlSwitch.help":"Proporcione el URL remoto o los datos contenidos","wfs.caps.no.feature.found":"No objetos encontrados","wfs.caps.unsupported.version":"WFS versión ${version} no es compatible","gpControlLocationUtilityService.geonames.title":"Buscar un lugar","gpControlLocationUtilityService.geocode.title":"Buscar una dirección","gpControlLocationUtilityService.reverse.geocode.title":"Buscar las direcciones en torno a un punto","gpControlLocationUtilityServiceGeoNames.title":"Buscar un lugar:","gpControlLocationUtilityServiceGeoNames.name":"Lugar: ","gpControlLocationUtilityServiceGeoNames.name.help":"ejemplo: Saint-Mandé","gpControlLocationUtilityServiceGeoNames.button.cancel":"Cancelar","gpControlLocationUtilityServiceGeoNames.button.search":"Buscar","gpControlLocationUtilityServiceGeocode.title":"Buscar una dirección:","gpControlLocationUtilityServiceGeocode.address":"Calle: ","gpControlLocationUtilityServiceGeocode.address.help":"ejemplo: 73, avenue de Paris","gpControlLocationUtilityServiceGeocode.municipality":"Localidad:","gpControlLocationUtilityServiceGeocode.municipality.help":"ejemplo: Saint-Mandé","gpControlLocationUtilityServiceGeocode.postalcode":"Código postal: ","gpControlLocationUtilityServiceGeocode.postalcode.help":"ejemplo: 94165","gpControlLocationUtilityServiceGeocode.name":"Lugar:","gpControlLocationUtilityServiceGeocode.name.help":"ejemplo: Saint-Mandé o 94165","gpControlLocationUtilityServiceGeocode.button.cancel":"Cancelar","gpControlLocationUtilityServiceGeocode.button.search":"Buscar","gpControlLocationUtilityServiceGeocode.matchType.city":"Ubicación en la ciudad","gpControlLocationUtilityServiceGeocode.matchType.street":"Ubicación en la calle","gpControlLocationUtilityServiceGeocode.matchType.number":"Ubicación en la dirección exacta","gpControlLocationUtilityServiceGeocode.matchType.enhanced":"Ubicación interpolado entre dos direcciones","gpControlLocationUtilityServiceReverseGeocode.longitude":"Longitud: ","gpControlLocationUtilityServiceReverseGeocode.longitude.help":"ejemplo: dd.mmss en coordenadas geográficas","gpControlLocationUtilityServiceReverseGeocode.latitude":"Latitud: ","gpControlLocationUtilityServiceReverseGeocode.latitude.help":"ejemplo: dd.mmss en coordenadas geográficas","gpControlLocationUtilityServiceReverseGeocode.title":"Buscar lugares próximos:","gpControlLocationUtilityServiceReverseGeocode.button.cancel":"Cancelar","gpControlLocationUtilityServiceReverseGeocode.button.search":"Buscar","gpControlCSW.title":"Buscar en el geocatálogo","gpControlCSW.cswTitle":"Título : ","gpControlCSW.cswTitle.help":" ","gpControlCSW.cswKeyWords":"Palabras clave : ","gpControlCSW.cswKeyWords.help":" ","gpControlCSW.cswKeyWords.NoKeyWords":" ","gpControlCSW.cswKeyWords.Addresses":"Direcciones","gpControlCSW.cswKeyWords.AdministrativeUnits":"Unidades administrativas","gpControlCSW.cswKeyWords.Agricultural":"Instalaciones agrícolas y de acuicultura","gpControlCSW.cswKeyWords.RegulationZones":"Zonas sujetas a ordenación, a restricciones o reglamentaciones y unidades de notificación","gpControlCSW.cswKeyWords.Atmospheric":"Condiciones atmosféricas","gpControlCSW.cswKeyWords.BioGeographical":"Regiones biogeográficas","gpControlCSW.cswKeyWords.Buildings":"Edificios","gpControlCSW.cswKeyWords.Cadastral":"Parcelas catastrales","gpControlCSW.cswKeyWords.CoordinateSystems":"Sistemas de coordenadas de referencia","gpControlCSW.cswKeyWords.Elevation":"Elevaciones","gpControlCSW.cswKeyWords.Energy":"Recursos energéticos","gpControlCSW.cswKeyWords.EnvironmentalFacilities":"Instalaciones de observación del medio ambiente","gpControlCSW.cswKeyWords.GeographicalSystems":"Sistema de cuadrículas geográficas","gpControlCSW.cswKeyWords.GeographicalNames":"Nombres geográficos","gpControlCSW.cswKeyWords.Geology":"Geología","gpControlCSW.cswKeyWords.Habitats":"Hábitats y biotopos","gpControlCSW.cswKeyWords.HumanHealth":"Salud y seguridad humanas","gpControlCSW.cswKeyWords.Hydrography":"Hidrografía","gpControlCSW.cswKeyWords.LandCover":"Cubierta terrestre","gpControlCSW.cswKeyWords.LandUse":"Uso del suelo","gpControlCSW.cswKeyWords.Meteorological":"Aspectos geográficos de carácter meteorológico","gpControlCSW.cswKeyWords.Mineral":"Recursos minerales","gpControlCSW.cswKeyWords.NaturalRiskZones":"Zonas de riesgos naturales","gpControlCSW.cswKeyWords.Oceanographic":"Rasgos geográficos oceanográficos","gpControlCSW.cswKeyWords.Orthoimagery":"Ortoimágenes","gpControlCSW.cswKeyWords.Population":"Distribución de la población — Demografía","gpControlCSW.cswKeyWords.Production":"Instalaciones de producción e industriales","gpControlCSW.cswKeyWords.ProtectedSites":"Lugares protegidos","gpControlCSW.cswKeyWords.SeaRegions":"Regiones marinas","gpControlCSW.cswKeyWords.Soil":"Suelo","gpControlCSW.cswKeyWords.SpeciesDistribution":"Distribución de las especies","gpControlCSW.cswKeyWords.StatisticalUnits":"Unidades estadísticas","gpControlCSW.cswKeyWords.TransportNetworks":"Redes de transporte","gpControlCSW.cswKeyWords.UtilityServices":"Servicios de utilidad pública y estatales","gpControlCSW.cswOrganism":"Organismo : ","gpControlCSW.cswOrganism.help":" ","gpControlCSW.button.cancel":"Cancelar","gpControlCSW.button.search":"Buscar","gpControlCSW.cswBBOX":"BBOX","gpControlCSW.cswNoBBOX":"Mondiale","gpControlCSW.cswNoBBOX.help":" ","gpControlCSW.cswCurrentBBOX":"Emprise courante","gpControlCSW.cswCurrentBBOX.help":" ","gpControlCSW.cswPersonnalBBOX":"Sélectionner une emprise","gpControlCSW.cswPersonnalBBOX.help":" ","gpControlPageManager.button.previous":"<","gpControlPageManager.button.next":">","azimuth.measurement":"Acimut","gpControlMeasureAzimuth.title":"Medición de acimut","gpControlMeasureAzimuth.azimuth":"Acimut","gpControlMeasureAzimuth.azimuth.help":"el ángulo de una dirección contado en el sentido de las agujas del reloj a partir del norte geográfico","gpControlMeasureAzimuth.distance":"Longitud","gpControlMeasureAzimuth.distance.help":"Longitud","gpControlPrintMap.title":"Vista previa de impresión del mapa","gpControlPrintMap.comments":"Tus notas o comentarios","approx.scale":"Escala aproximada: 1:","approx.center":"Coordenadas geográficas del centro del mapa","gpControlPrintMap.print.forbidden":"Todos los derechos reservados","gpControlPrintMap.print":"Imprimir",gpControlInformationMini:"Haga clic para obtener la información del panel visible","OpenLayers.Control.WMSGetFeatureInfo.title":"La identificación de los objetos","gpControlAddAttributeToLayer.title":"Agregar / Quitar atributos","gpControlAddAttributeToLayer.attName":"Nombre","gpControlAddAttributeToLayer.attName.help":"nombre del atributo que se agregue","gpControlAddAttributeToLayer.attDefaultValue":"Default","gpControlAddAttributeToLayer.attDefaultValue.help":"default atributo de valor","gpControlAddAttributeToLayer.attList":"Atributos","gpControlAddAttributeToLayer.attList.help":"estar presente","gpControlAddAttributeToLayer.button.cancel":"Finalizar","gpControlAddAttributeToLayer.button.addatt":"Agregar","gpControlAddAttributeToLayer.button.delatt":"Eliminar","gpControlAddAttributeToLayer.emptyName":"El nombre está vacía","gpControlAddAttributeToLayer.existingName":"${name} ya existe","gpControlLayerStyling.title":"Edición de la cartografía","gpControlLayerStyling.color":"Color y líneas de llenado","gpControlLayerStyling.color.help":"haga clic para abrir la paleta de colores, haga clic fuera a cerrar","gpControlLayerStyling.size":"Tamaño","gpControlLayerStyling.size.help":"haga clic para abrir el panel para cambiar los tamaños","gpControlLayerStyling.style":"Representación","gpControlLayerStyling.style.help":"haga clic para abrir el panel para el cambio de estilos","gpControlLayerStyling.rotation":"El ángulo de rotación (en grados) ","gpControlLayerStyling.rotation.help":"haga clic para seleccionar un ángulo de entre 0 y 360°","gpControlLayerStyling.externalgraphic":"URL del icono","gpControlLayerStyling.externalgraphic.help":"introducir la URL del icono - nada","gpControlLayerStyling.button.cancel":"Cancelar","gpControlLayerStyling.button.changestyle":"Cambio","gpControlLayerStyling.emptyColor":"El color está vacío (#XXXXXX espera)","gpControlSaveLayer.title":"Guardar la capa","gpControlSaveLayer.format":"Guardar Formato","gpControlSaveLayer.gml":"GML","gpControlSaveLayer.kml":"KML","gpControlSaveLayer.gpx":"Huellas GPX","gpControlSaveLayer.osm":"OpenStreetMap XML Export","gpControlSaveLayer.gxt":"Géoconcept Export","gpControlSaveLayer.format.help":"formato para exportar los datos","gpControlSaveLayer.proj":"Sistema de referencia de coordenadas","gpControlSaveLayer.proj.help":"coordenadas para la exportación","gpControlSaveLayer.pretty":"Mejora de la visualización","gpControlSaveLayer.pretty.help":"para hacer la exportación de datos legible","gpControlSaveLayer.button.cancel":"Cancelar","gpControlSaveLayer.button.save":"Guardar","gpControlSaveLayer.noData":"La capa está vacía, no hay datos para ahorrar","lus.not.match":"No se ha encontrado ninguna correspondencia","csw.not.match":"No encontró registro de metadatos","geocoded.address.popup.title":"Dirección","geocoded.address.popup.postalCode":"Código postal","geocoded.address.popup.places":"Lugares",CountrySubdivision:"Departamento",CountrySecondarySubdivision:"Municipio",Municipality:"Localidad",MunicipalitySubdivision:"Barrio",TOS:"Condiciones del servicio","utm.zone":"UTM","*":""};
Geoportal.Lang.it={ATF:"Territori Francesi del Sud",FXX:"Francia continentale",GLP:"Guadalupa",GUF:"Guiana francese",MTQ:"Martinica",MYT:"Mayotte",NCL:"Nuova Caledonia",PYF:"Polinesia Francese",REU:"Riunione",SPM:"Saint Pierre e Miquelon",WLF:"Wallis e Futuna",ANF:"Antille francesi",ASP:"San Paolo e Amsterdam",CRZ:"Crozet",EUE:"Europa",KER:"Kerguelen",SBA:"Saint Barthélémy",SMA:"Saint Martin",WLD:"Il mondo","GEOGRAPHICALGRIDSYSTEMS.MAPS":"IGN Mappe","GEOGRAPHICALGRIDSYSTEMS.MAPS.description":"Le carte sono ricavate dalla banca dati SCAN dell'IGN : Mondo, Europa Politica, SCAN 1 000®, SCAN 500®, SCAN Régional®, SCAN 200®, SCAN Départemental®, SCAN 100®, SCAN 50®, SCAN 25®.","ORTHOIMAGERY.ORTHOPHOTOS":"Orto immagini","ORTHOIMAGERY.ORTHOPHOTOS.description":"Le fotografie aeree sposano la precisione geometrica della carta alla richezza di una foto con una risoluzione compresa tra 50 e 15 cm o di immagini satellitarie con una risoluzione compresa tra 10 e 20 m.","ELEVATION.SLOPES":"Colori ipsometrici - Elevazione","ELEVATION.SLOPES.description":"I modelli numerici del terreno sono ricavati da dati della banca dati BD ALTI® che descrive il territorio francese con delle curve di livello. L'equidistanza delle curve pueden andare da 5 a 40 m. I dati iniziali sono stati impostati su delle carte IGN alla scala di 1:25 000 o 1:50 000, a partire da una restituzione ricavata da riprese aeree alla scala di 1:20 000, 1:30 000 o 1:60 000.","ELEVATION.LEVEL0":"Tratto costiero - Elevazione","ELEVATION.LEVEL0.description":"Lo 0 dei mari è ricavato dal datbase LITTO3D®, prodotto realizzato in comune dallo SHOM e dall'IGN.","CADASTRALPARCELS.PARCELS":"Parcelle catastali","CADASTRALPARCELS.PARCELS.description":"Le informazioni catastali numeriche comportano i riferimenti geografici, continuano sull'insieme del territorio francese e sono state ricavate a partire da un assemblaggio dei piani catastali smaterializzati.","HYDROGRAPHY.HYDROGRAPHY":"Idrografia","HYDROGRAPHY.HYDROGRAPHY.description":"L'idrografia terrestre è ricavata a partire da un assemblaggio dei dati dei database BD TOPO®, BD CARTHAGE®, EuroRegionalMap e EuroGlobalMap.","TRANSPORTNETWORKS.ROADS":"Rete stradale - Reti di trasporto","TRANSPORTNETWORKS.ROADS.description":"La rete stradale è ricavata a partire da un assemblaggio dei dati dei database BD TOPO®, BD CARTO®, EuroRegionalMap e EuroGlobalMap.","TRANSPORTNETWORKS.RAILWAYS":"Rete ferroviaria - Reti di trasporto","TRANSPORTNETWORKS.RAILWAYS.description":"La rete ferroviaria è ricavata a partire da un assemblaggio dei dati dei database BD TOPO®, BD CARTO®, EuroRegionalMap e EuroGlobalMap.","TRANSPORTNETWORKS.RUNWAYS":"Aria di rete - Reti di trasporto","TRANSPORTNETWORKS.RUNWAYS.description":"Le piste degli aeroporti e aerodromi sono ricavate a partire da un assemblaggio dei dati dei database BD TOPO® e BD CARTO®","BUILDINGS.BUILDINGS":"Edifici","BUILDINGS.BUILDINGS.description":"Gli edifici e le zone costruite sono ricavati a partire da un assemblaggio dei dati dei database BD TOPO®, BD CARTO®, EuroRegionalMap e EuroGlobalMap.","UTILITYANDGOVERNMENTALSERVICES.ALL":"Servizi di pubblica utilità e servizi amministrativi","UTILITYANDGOVERNMENTALSERVICES.ALL.description":"Le diverse reti di trasporto dell'energia sono ricavate a partire da un assemblaggio dei dati dei database BD TOPO® e BD CARTO®.","ADMINISTRATIVEUNITS.BOUNDARIES":"Unità amministrative","ADMINISTRATIVEUNITS.BOUNDARIES.description":"Le unitè amministrative della Francia sono ricavate a partire da un assemblaggio dei dati dei database BD TOPO®, BD CARTO®, EuroRegionalMap e EuroBoundaryMap.","LANDCOVER.CORINELANDCOVER":"Corine LC","LANDCOVER.CORINELANDCOVER.description":"CORINE Land Cover (2006)","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS":"Coastal mappe","GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS.description":"Coastal mappe","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS":"Topografiche del tipo 1900 mappe","GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS.description":"Mappe a 1: 50 000 (prima edizione dopo la Biblioteca Mappa di IGN).","GEOGRAPHICALGRIDSYSTEMS.CASSINI":"Cassini mappe","GEOGRAPHICALGRIDSYSTEMS.CASSINI.description":"Cassini mappe","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000":"Ortofoto della costa (2000)","ORTHOIMAGERY.ORTHOPHOTOS.COAST2000.description":"Ortho-fotografia della costa del Mare del Nord, Canale della Manica e dell'Atlantico.","LANDUSE.AGRICULTURE2007":"parcelle agricole (2007)","LANDUSE.AGRICULTURE2007.description":"Questo strato mostra gli isolotti anonimi Registro Parcel grafico (RPG) e il loro gruppo di colture principali segnalato nel 2007 da parte degli agricoltori di beneficiare di sovvenzioni PAC.","LANDUSE.AGRICULTURE2008":"parcelle agricole (2008)","LANDUSE.AGRICULTURE2008.description":"Questo strato mostra gli isolotti anonimi Registro Parcel grafico (RPG) e il loro gruppo di colture principali segnalato nel 2008 da parte degli agricoltori di beneficiare di sovvenzioni PAC.","LANDUSE.AGRICULTURE2009":"parcelle agricole (2009)","LANDUSE.AGRICULTURE2009.description":"Questo strato mostra gli isolotti anonimi Registro Parcel grafico (RPG) e il loro gruppo di colture principali segnalato nel 2009 da parte degli agricoltori di beneficiare di sovvenzioni PAC.","LANDUSE.AGRICULTURE2010":"parcelle agricole (2010)","LANDUSE.AGRICULTURE2010.description":"Questo strato mostra gli isolotti anonimi Registro Parcel grafico (RPG) e il loro gruppo di colture principali segnalato nel 2010 da parte degli agricoltori di beneficiare di sovvenzioni PAC.","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE":"Stato di Ginevra","ORTHOIMAGERY.ORTHOPHOTOS.GENEVE.description":"Orto-fotografías, 10 cm de resolución (2010).","ORTHOIMAGERY.ORTHOPHOTOS2000-2005":"Orto-fotografías (2000-2005)","ORTHOIMAGERY.ORTHOPHOTOS2000-2005.description":"Prima copertura nazionale (2000-2005) di orto-immagini con risoluzione di 50 centimetri.","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER":"FranceRaster®","GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER.description":"FranceRaster® è una serie di immagini georeferenziate che copre la Francia continentale e territori d'oltremare. E 'prodotto con il database vettoriale del IGN più adatta a ogni scala con una simbologia uniforme. Secondo le scale, FranceRaster ®, consente di visualizzare le strade tematiche e la ferrovia, telaio, idrografia, vegetazione, indirizzi, direzione di marcia, nomi ...","NATURALRISKZONES.1910FLOODEDWATERSHEDS":"Seine (PHEC)","NATURALRISKZONES.1910FLOODEDWATERSHEDS.description":"Questo strato mappa rappresenta l'acqua più alto noto (PHEC) sul bacino della Senna, ovvero zone geografiche allagati a causa della più grande alluvione noti e documentati su ogni fiume.","NATURALRISKZONES.1910FLOODEDCELLARS":"Cantine, seminterrati (1910)","NATURALRISKZONES.1910FLOODEDCELLARS.description":"Mappa di cantine allagate durante inondazione della Senna del 1910. Questa mappa è basata su testimonianze.","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40":"État-Major mappe (1:40 000)","GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40.description":"Questo strato è costituito dall'assemblea dei 978 disegni originali della mappa di stato maggiore stabilita nel diciannovesimo secolo. Queste indagini scritta a mano e colorati, a 1: 40 000, sono stati istituiti tra il 1825 e il 1866.","LANDCOVER.FORESTINVENTORY.V1":"Inventario Forestale (v1)","LANDCOVER.FORESTINVENTORY.V1.description":"Inventario Forestale (1987-2004)","LANDCOVER.FORESTINVENTORY.V2":"Inventario Forestale (v2)","LANDCOVER.FORESTINVENTORY.V2.description":"Inventario Forestale (2005+)","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS":"Mappa delle divisioni amministrative","GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS.description":"Mappa delle divisioni amministrative","TOPONYMS.ALL":"Cerca per località","TOPONYMS.ALL.description":"Utilizza il database di BD Nyme®.","ADDRESSES.CROSSINGS":"Cerca per indirizzo","ADDRESSES.CROSSINGS.description":"Utilizza il database Routes Adresses®.","GEOGRAPHICALNAMES.NAMES":"Geografica nomi","GEOGRAPHICALNAMES.NAMES.description":"Geografica nomi","div.not.found":"${id} non trovato nel documento : verificare l'esistenza reale di '${id}'.","proxy.not.set":"Nessuna configurazione per il proxy : è possibile che l'ottenimento delle informazioni richieste sul contratto fallisca.","cookies.not.enabled":"Cookie sono disabilitati. Per favore, consentire loro.","geoRM.getConfig":"Impossibile di ottenere le informazioni relative alla chiave '${key}' - Verificare la propria connessione.","geoRM.getToken":"Impossibile di ottenere il gettone associato alla chiave '${key}' - Verificare la propria connessione.","geoRM.failed":"Impossibile di ottenere uno token valido relative alla chiave '${key}'","geoRM.forbidden":"API key è mancante o non supportato dal ${layer} contratto l'applicazione dell'API.","url.error":"Errore durante il caricamento '${url}' ${msg}","GPX.version":"Versione ${gpxVersion} GPX non supportata","XLS.version":"Versione ${xlsVersion} XLS non supportata per il servizio ${coreService}","Not.conformal.XLS":"${part} non è presente nello XML","olControlMeasurePath.title":"Misura di distanza","olControlMeasurePolygon.title":"Misura di superficie","waiting.measurement":"In attesa di input...","length.measurement":"Lunghezza","area.measurement":"Surfacie","gpControlLayerSwitcher.label":"Strati","gpLayer.metadataURL":"Maggiori informazioni su questo strato...","gpLayer.dataURL":"Accesso al servizio di download","gpControlPanelToggle.closed":"Visualizzare la barra degli attrezzi","gpControlPanelToggle.opened":"Mascherare la barra degli attrezzi","gpControlRemoveLayer.title":"Ritirare lo strato","gpControlLayerOpacity.title":"Regola di trasparenza","gpControlZoomToLayerMaxExtent.title":"Zoom sul campo di applicazione dello strato","gpControlMousePosition.longitude":"Longitudine","gpControlMousePosition.latitude":"Latitudine","gpControlMousePosition.easting":"Ascissa","gpControlMousePosition.northing":"Ordinata","gpControlMousePosition.north":"N","gpControlMousePosition.south":"S","gpControlMousePosition.east":"E","gpControlMousePosition.west":"O","gpControlMousePosition.sexa":"gradi sessagesimali","gpControlMousePosition.deg":"gradi decimali","gpControlMousePosition.gon":"gradi","gpControlMousePosition.rad":"radianti","gpControlMousePosition.km":"chilometri","gpControlMousePosition.m":"metri","gpControlMousePosition.cm":"centimetri","gpControlMousePosition.utmZone":"Zona","gpControlToolBox.label":"Attrezzi","gpControlZoomBar.world":"Mondo","gpControlZoomBar.state":"Stato","gpControlZoomBar.country":"Paese","gpControlZoomBar.town":"Città","gpControlZoomBar.street":"Via","gpControlZoomBar.house":"Casa","gpControlEditingToolbar.drawpoint":"Inserire un punto","gpControlEditingToolbar.drawline":"Disegnare una linea","gpControlEditingToolbar.drawpolygon":"Disegnare un poligono","gpControlEditingToolbar.dragfeature":"Spostare un oggetto","gpControlEditingToolbar.modifyfeature":"Modificare un oggetto","gpControlEditingToolbar.deletefeature":"Distruggere un oggetto","gpControlEditingToolbar.selectfeature":"Selezionare un oggetto","gpControlEditingToolbar.navigation":"Navigare","gpControlAddImageLayer.title":"Aggiungere uno strato immagine...","gpControlAddImageLayer.layerUrl":"URL : ","gpControlAddImageLayer.layerUrl.help":"indirizzo di base del servizio","gpControlAddImageLayer.layerType":"Tipo :","gpControlAddImageLayer.layerType.help":"Scegliere un valore nella lista","gpControlAddImageLayer.layerType.WMS":"Web Map Service","gpControlAddImageLayer.layerType.WMTS":"Web Map Tile Service","gpControlAddImageLayer.layerType.WMSC":"OSGeO WMS-C","gpControlAddImageLayer.button.add":"Aggiungere","gpControlAddImageLayer.button.cancel":"Cancel","wms.caps.no.compatible.srs":"Non compatibile strato trovato","ogc.caps.unknown.service":"${serviceType} non è un ${expectedType}","gpControlAddVectorLayer.title":"Annullare","gpControlAddVectorLayer.layerName":"Nome :","gpControlAddVectorLayer.layerName.help":"Ad esempio : il mio strato","gpControlAddVectorLayer.layerType":"Tipo :","gpControlAddVectorLayer.layerType.help":"Scegliere un valore nella lista","gpControlAddVectorLayer.layerType.Point":"puntuale","gpControlAddVectorLayer.layerType.LineString":"lineare","gpControlAddVectorLayer.layerType.Polygon":"superficiale","gpControlAddVectorLayer.layerType.KML":"risorsa KML","gpControlAddVectorLayer.layerType.GPX":"risorsa GPX","gpControlAddVectorLayer.layerType.OSM":"risorsa OSM","gpControlAddVectorLayer.layerType.GeoRSS":"risorsa GeoRSS","gpControlAddVectorLayer.layerType.WFS":"risorsa WFS","gpControlAddVectorLayer.layerUrl":"URL : ","gpControlAddVectorLayer.layerUrl.help":"locale o a distanza","gpControlAddVectorLayer.layerFreeHand":"a mano libera ? ","gpControlAddVectorLayer.layerFreeHand.help":"permette il disegno a mano libera","gpControlAddVectorLayer.button.add":"Aggiungere","gpControlAddVectorLayer.button.cancel":"Annullare","gpControlAddVectorLayer.layerContent":"Contenuto","gpControlAddVectorLayer.layerContent.help":"Copia / Incolla dati","gpControlAddVectorLayer.layerUrlSwitch":"Da URL / Content","gpControlAddVectorLayer.layerUrlSwitch.help":"Fornire URL remoto o che i dati contenuti","wfs.caps.no.feature.found":"Nessun oggetto trovato","wfs.caps.unsupported.version":"WFS versione ${version} non supportata","gpControlLocationUtilityService.geonames.title":"Ricercare una località","gpControlLocationUtilityService.geocode.title":"Ricercare un indirizzo","gpControlLocationUtilityService.reverse.geocode.title":"Ricercare gli indirizzi intorno a un punto","gpControlLocationUtilityServiceGeoNames.title":"Ricercare una località :","gpControlLocationUtilityServiceGeoNames.name":"Località :","gpControlLocationUtilityServiceGeoNames.name.help":"Ad esempio : Saint-Mandé","gpControlLocationUtilityServiceGeoNames.button.cancel":"Annullare","gpControlLocationUtilityServiceGeoNames.button.search":"Ricercare","gpControlLocationUtilityServiceGeocode.title":"Ricercare un indirizzo","gpControlLocationUtilityServiceGeocode.address":"Via :","gpControlLocationUtilityServiceGeocode.address.help":"Ad esempio : 73, avenue de Paris","gpControlLocationUtilityServiceGeocode.municipality":"Città :","gpControlLocationUtilityServiceGeocode.municipality.help":"Ad esempio : Saint-Mandé","gpControlLocationUtilityServiceGeocode.postalcode":"Codice di avviamento postale : ","gpControlLocationUtilityServiceGeocode.postalcode.help":"Ad esempio : 94165","gpControlLocationUtilityServiceGeocode.name":"Località :","gpControlLocationUtilityServiceGeocode.name.help":"Ad esempio : Saint-Mandé ou 94165","gpControlLocationUtilityServiceGeocode.button.cancel":"Annullare","gpControlLocationUtilityServiceGeocode.button.search":"Ricercare","gpControlLocationUtilityServiceGeocode.matchType.city":"Posizione nella città","gpControlLocationUtilityServiceGeocode.matchType.street":"Posizione in strada","gpControlLocationUtilityServiceGeocode.matchType.number":"Posizione nel numero esatto indirizzo","gpControlLocationUtilityServiceGeocode.matchType.enhanced":"Posizione interpolata tra due indirizzi","gpControlLocationUtilityServiceReverse.title":"Ricercare dei luoghi nelle vicinanze","gpControlLocationUtilityServiceReverse.longitude":"Longitudine :","gpControlLocationUtilityServiceReverse.longitude.help":"Ad esempio : dd.mmss in coordinate geografiche","gpControlLocationUtilityServiceReverse.latitude":"Latitudine","gpControlLocationUtilityServiceReverse.latitude.help":"Ad esempio : dd.mmss in coordinate geografiche","gpControlLocationUtilityServiceReverse.button.cancel":"Annullare","gpControlLocationUtilityServiceReverse.button.search":"Ricercare","gpControlCSW.title":"Cercare nel geocatalogo","gpControlCSW.cswTitle":"Titolo : ","gpControlCSW.cswTitle.help":" ","gpControlCSW.cswKeyWords":"Descrittore : ","gpControlCSW.cswKeyWords.help":" ","gpControlCSW.cswKeyWords.NoKeyWords":" ","gpControlCSW.cswKeyWords.Addresses":"Indirizzi","gpControlCSW.cswKeyWords.AdministrativeUnits":"Unità amministrative","gpControlCSW.cswKeyWords.Agricultural":"Impianti agricoli e di acquacoltura","gpControlCSW.cswKeyWords.RegulationZones":"Zone sottoposte a gestione/limitazioni/regolamentazione e unità con obbligo di comunicare dati","gpControlCSW.cswKeyWords.Atmospheric":"Condizioni atmosferiche","gpControlCSW.cswKeyWords.BioGeographical":"Regioni biogeografiche","gpControlCSW.cswKeyWords.Buildings":"Edifici","gpControlCSW.cswKeyWords.Cadastral":"Parcelle catastali","gpControlCSW.cswKeyWords.CoordinateSystems":"Sistemi di coordinate","gpControlCSW.cswKeyWords.Elevation":"Elevazione","gpControlCSW.cswKeyWords.Energy":"Risorse energetiche","gpControlCSW.cswKeyWords.EnvironmentalFacilities":"Impianti di monitoraggio ambientale","gpControlCSW.cswKeyWords.GeographicalSystems":"Sistemi di griglie geografiche","gpControlCSW.cswKeyWords.GeographicalNames":"Nomi geografici","gpControlCSW.cswKeyWords.Geology":"Geologia","gpControlCSW.cswKeyWords.Habitats":"Habitat e biotopi","gpControlCSW.cswKeyWords.HumanHealth":"Salute umana e sicurezza","gpControlCSW.cswKeyWords.Hydrography":"Idrografia","gpControlCSW.cswKeyWords.LandCover":"Copertura del suolo","gpControlCSW.cswKeyWords.LandUse":"Utilizzo del territorio","gpControlCSW.cswKeyWords.Meteorological":"Elementi geografici meteorologici","gpControlCSW.cswKeyWords.Mineral":"Risorse minerarie","gpControlCSW.cswKeyWords.NaturalRiskZones":"Zone a rischio naturale","gpControlCSW.cswKeyWords.Oceanographic":"Elementi geografici oceanografici","gpControlCSW.cswKeyWords.Orthoimagery":"Orto immagini","gpControlCSW.cswKeyWords.Population":"Distribuzione della popolazione — Demografia","gpControlCSW.cswKeyWords.Production":"Produzione e impianti industriali","gpControlCSW.cswKeyWords.ProtectedSites":"Siti protetti","gpControlCSW.cswKeyWords.SeaRegions":"Regioni marine","gpControlCSW.cswKeyWords.Soil":"Suolo","gpControlCSW.cswKeyWords.SpeciesDistribution":"Distribuzione delle specie","gpControlCSW.cswKeyWords.StatisticalUnits":"Unità statistiche","gpControlCSW.cswKeyWords.TransportNetworks":"Reti di trasporto","gpControlCSW.cswKeyWords.UtilityServices":"Servizi di pubblica utilità e servizi amministrativi","gpControlCSW.cswOrganism":"Organismo : ","gpControlCSW.cswOrganism.help":" ","gpControlCSW.button.cancel":"Annullare","gpControlCSW.button.search":"Ricercare","gpControlCSW.cswBBOX":"Emprise : ","gpControlCSW.cswNoBBOX":"Mondiale","gpControlCSW.cswNoBBOX.help":" ","gpControlCSW.cswCurrentBBOX":"Emprise courante","gpControlCSW.cswCurrentBBOX.help":" ","gpControlCSW.cswPersonnalBBOX":"Sélectionner une emprise","gpControlCSW.cswPersonnalBBOX.help":" ","gpControlPageManager.button.previous":"<","gpControlPageManager.button.next":">","azimuth.measurement":"Azimuth","gpControlMeasureAzimuth.title":"Misura di azimuth","gpControlMeasureAzimuth.azimuth":"Azimuth","gpControlMeasureAzimuth.azimuth.help":"indica un angolo tra un punto e un piano di riferimento","gpControlMeasureAzimuth.distance":"Lunghezza","gpControlMeasureAzimuth.distance.help":"Lunghezza","gpControlPrintMap.title":"Anteprima di stampa della mappa","gpControlPrintMap.comments":"Le note o commenti","approx.scale":"Dimensioni approssimative:","approx.center":"Coordinate geografiche del centro della mappa","gpControlPrintMap.print.forbidden":"Tutti i diritti riservati","gpControlPrintMap.print":"Stampa",gpControlInformationMini:"Fare clic per ottenere informazioni pannello visibile","OpenLayers.Control.WMSGetFeatureInfo.title":"Identificare gli oggetti","gpControlAddAttributeToLayer.title":"Aggiungi / Rimuovi attributi","gpControlAddAttributeToLayer.attName":"Nome","gpControlAddAttributeToLayer.attName.help":"nome dell'attributo da aggiungere","gpControlAddAttributeToLayer.attDefaultValue":"Default","gpControlAddAttributeToLayer.attDefaultValue.help":"default value","gpControlAddAttributeToLayer.attList":"Attributi","gpControlAddAttributeToLayer.attList.help":"essere presente","gpControlAddAttributeToLayer.button.cancel":"Fine","gpControlAddAttributeToLayer.button.addatt":"Aggiungi","gpControlAddAttributeToLayer.button.delatt":"Rimuovi","gpControlAddAttributeToLayer.emptyName":"Il nome è vuoto","gpControlAddAttributeToLayer.existingName":"${name} esiste già","gpControlLayerStyling.title":"Modifica mappatura","gpControlLayerStyling.color":"Linee di colore e riempimento","gpControlLayerStyling.color.help":"fare clic per aprire la tavolozza dei colori, fare clic all'esterno per chiudere","gpControlLayerStyling.size":"Size","gpControlLayerStyling.size.help":"fare clic per aprire il pannello per modificare le dimensioni","gpControlLayerStyling.style":"Rappresentazione","gpControlLayerStyling.style.help":"fare clic per aprire il pannello per la modifica degli stili","gpControlLayerStyling.rotation":"Angolo di rotazione (in gradi) ","gpControlLayerStyling.rotation.help":"fare clic per scegliere un angolo compreso tra 0 e 360°","gpControlLayerStyling.externalgraphic":"URL dell'icona","gpControlLayerStyling.externalgraphic.help":"inserire l'URL dell'icona - niente per","gpControlLayerStyling.button.cancel":"Annulla","gpControlLayerStyling.button.changestyle":"Change","gpControlLayerStyling.emptyColor":"Il colore è vuota (#XXXXXX previsto)","gpControlSaveLayer.title":"Salva mappa","gpControlSaveLayer.format":"Formato di salvataggio","gpControlSaveLayer.gml":"GML","gpControlSaveLayer.kml":"KML","gpControlSaveLayer.gpx":"Le tracce GPX","gpControlSaveLayer.osm":"OpenStreetMap XML Export","gpControlSaveLayer.gxt":"Géoconcept Export","gpControlSaveLayer.format.help":"formato per esportare i dati","gpControlSaveLayer.proj":"Sistema di coordinate di riferimento","gpControlSaveLayer.proj.help":"coordinate per l'esportazione","gpControlSaveLayer.pretty":"Migliorata la visualizzazione","gpControlSaveLayer.pretty.help":"per rendere più leggibile l'esportazione dei dati","gpControlSaveLayer.button.cancel":"Annulla","gpControlSaveLayer.button.save":"Salva","gpControlSaveLayer.noData":"Il livello è vuoto, non dati da salvare","lus.not.match":"Non trovata nessuna corrispondenza","csw.not.match":"Trovato alcuna traccia dei metadati","geocoded.address.popup.title":"Indirizzo","geocoded.address.popup.postalCode":"Codice di avviamento postale :","geocoded.address.popup.places":"Località",CountrySubdivision:"Provincia",CountrySecondarySubdivision:"Comune",Municipality:"Città",MunicipalitySubdivision:"Quartiere",TOS:"Termini di servizio","utm.zone":"UTM","*":""};
Geoportal.Util={_imagesLocation:null,getImagesLocation:function(){if(!Geoportal.Util._imagesLocation){Geoportal.Util._imagesLocation=Geoportal._getScriptLocation()+"theme/geoportal/img/"
}return Geoportal.Util._imagesLocation
},setTheme:function(a){if(!a){a="geoportal"
}Geoportal.Util._imagesLocation=Geoportal._getScriptLocation()+"theme/"+a+"/img/"
},convertToPixels:function(e,a,g){if(!e){return undefined
}if(a==undefined){a=false
}if(/px$/.test(e)){return parseInt(e)
}var f=OpenLayers.getDoc();
var d=f.createElement("div");
d.style.display="";
d.style.visibility="hidden";
d.style.position="absolute";
d.style.lineHeight="0";
if(!g){g=f.body
}if(/%$/.test(e)){g=g.parentNode||g;
d.style[a?"width":"height"]=e
}else{d.style.borderStyle="solid";
if(a){d.style.borderBottomHeight="0";
d.style.borderTopHeight=e
}else{d.style.borderBottomWidth="0";
d.style.borderTopWidth=e
}}g.appendChild(d);
var b=a?d.offsetWidth:d.offsetHeight;
g.removeChild(d);
return b
},getComputedStyle:function(g,i,b){var h=OpenLayers.getDoc();
var a;
if(g.currentStyle){a=g.currentStyle[OpenLayers.String.camelize(i)]
}else{if(h.defaultView.getComputedStyle){var f=h.defaultView.getComputedStyle(g,null);
a=f.getPropertyValue(i)
}else{a=null
}}var e=/(em|ex|pt|%)$/;
var c=/(width)/i;
a=b?a?e.test(a)?this.convertToPixels(a,c.test(a),g.parentNode):parseInt(a)||0:0:a;
return a
},loadJS:function(a,c,j,h){c=c||a;
var d=OpenLayers.Util.getElement(c);
if(d!=null){return[d,false]
}var o=OpenLayers.getDoc();
var b=o.getElementsByTagName("script"),e,k;
var g,f;
for(g=0,f=b.length;
g<f;
++g){if(OpenLayers.Util.isEquivalentUrl(b.item(g).src,a)){b.item(g).setAttribute("id",c);
return[b.item(g),false]
}}b=o.getElementsByTagName("head");
var m=b.length>0?b[0]:o.body;
var p=j&&j!=""?OpenLayers.Util.getElement(j):null;
d=o.createElement("script");
d.setAttribute("type","text/javascript");
d.setAttribute("src",a);
d.setAttribute("charset","UTF-8");
d.setAttribute("id",c);
if(h!=undefined){d.onload=function(){if(d.readyState&&d.readyState!="loaded"&&d.readyState!="complete"){return
}d.onreadystatechange=d.onload=null;
h()
};
if(navigator.appName!="Opera"){d.onreadystatechange=d.onload
}}if(p!=null){var p=OpenLayers.Util.getElement(j);
OpenLayers.Element.insertAfter(d,p)
}else{b=m.childNodes;
k=null;
for(g=b.length-1;
g>=0;
g--){e=b[g];
if(e.nodeType!=1){continue
}switch(e.tagName.toLowerCase()){case"script":if(e.getAttribute("type").toLowerCase()=="text/javascript"){k=e
}break;
default:break
}if(k!=null){break
}}if(k==null){m.appendChild(d)
}else{OpenLayers.Element.insertAfter(d,k)
}}return[d,true]
},loadCSS:function(a,c,g){c=c||a;
var h=OpenLayers.Util.getElement(c);
if(h!=null){return[h,false]
}var m=OpenLayers.getDoc();
var b=m.getElementsByTagName("link"),d,j;
var f,e;
for(f=0,e=b.length;
f<e;
++f){if(OpenLayers.Util.isEquivalentUrl(b.item(f).href,a)){b.item(f).setAttribute("id",c);
return[b.item(f),false]
}}b=m.getElementsByTagName("head");
var k=b.length>0?b[0]:m.body;
h=m.createElement("link");
h.setAttribute("rel","stylesheet");
h.setAttribute("type","text/css");
h.setAttribute("href",a);
h.setAttribute("id",c);
if(g==""){k.appendChild(h);
return[h,true]
}var o=g?OpenLayers.Util.getElement(g):null;
if(o!=null){OpenLayers.Element.insertAfter(h,o);
return[h,true]
}b=k.childNodes;
j=null;
for(f=0,e=b.length;
f<e;
f++){d=b[f];
if(d.nodeType!=1){continue
}switch(d.tagName.toLowerCase()){case"link":if(d.getAttribute("rel").toLowerCase()=="stylesheet"||d.getAttribute("type").toLowerCase()=="text/css"){j=d
}break;
case"style":j=d;
break;
default:break
}if(j!=null){break
}}if(j==null){k.appendChild(h)
}else{j.parentNode.insertBefore(h,j)
}return[h,true]
},cleanContent:function(b){var a=b.replace(/[\r\n]?/gi,"");
a=a.replace(/<[\/]?html[^>]*>/gi,"");
a=a.replace(/<head[^>]*>.*<\/head>/gi,"");
a=a.replace(/<[\/]?body[^>]*>/gi,"");
a=a.replace(/<script[^>]*>.*<\/script>/gi,"");
return a
},getMaxDimensions:function(){var a=0,b=0;
var c=OpenLayers.getDoc();
if(c.innerHeight>b){a=c.innerWidth;
b=c.innerHeight
}if(c.documentElement&&c.documentElement.clientHeight>b){a=c.documentElement.clientWidth;
b=c.documentElement.clientHeight
}if(c.body&&c.body.clientHeight>b){a=c.body.clientWidth;
b=c.body.clientHeight
}return new OpenLayers.Size(a,b)
},getCSSRule:function(j){j=j.toLowerCase();
if(document.styleSheets){for(var f=0,b=document.styleSheets.length;
f<b;
f++){var d=document.styleSheets[f];
var c=[];
for(var h in {rules:"",imports:"",cssRules:""}){try{c=d[h];
if(c!=undefined){for(var m=0,k=c.length;
m<k;
m++){var a=c[m];
if(a&&a.selectorText&&a.selectorText.toLowerCase()==j){return a
}}}break
}catch(g){}}}}return null
},dmsToDeg:function(i){if(!i){return Number.NaN
}var h=i.match(/(^\s?-)|(\s?[SW]\s?$)/)!=null?-1:1;
i=i.replace(/(^\s?-)|(\s?[NSEW]\s?)$/,"");
i=i.replace(/\s/g,"");
var e=i.match(/(\d{1,3})[.,°d]?(\d{0,2})['′]?(\d{0,2})[.,]?(\d{0,})(?:["″]|[']{2})?/);
if(e==null){return Number.NaN
}var f=(e[1]?e[1]:"0.0")*1;
var a=(e[2]?e[2]:"0.0")*1;
var b=(e[3]?e[3]:"0.0")*1;
var c=(e[4]?("0."+e[4]):"0.0")*1;
var g=(f+(a/60)+(b/3600)+(c/3600))*h;
return g
},degToDMS:function(e,c,a,o){var l=Math.abs(e);
var f=Math.round(l+0.5)-1;
var i=60*(l-f);
var h=Math.round(i+0.5)-1;
i=60*(i-h);
var n=Math.round(i+0.5)-1;
if(a===undefined||a<0){a=1
}var g=Math.pow(10,a);
var m=g*(i-n);
m=Math.round(m+0.5)-1;
if(m>=g){n=n+1;
m=0
}if(n==60){h=h+1;
n=0
}if(h==60){f=f+1;
h=0
}var d="";
if(c&&!o&&(OpenLayers.Util.isArray(c))&&c.length==2){d=" "+(e>0?c[0]:c[1])
}else{if(e<0){f=-1*f
}}var j="%4d° %02d' %02d",b='.%0*d"';
if(o){j=o;
b="%0*d"
}var p=OpenLayers.String.sprintf(j,f,h,n)+(a>0?OpenLayers.String.sprintf(b,a,m):(o?'"':""))+d;
return p
}};
OpenLayers.Util.onImageLoadError=function(){if(this.src.match(/^http:\/\/[abc]\.[a-z]+\.openstreetmap\.org\//)){this.src="http://openstreetmap.org/openlayers/img/404.png"
}else{if(this.src.match(/^http:\/\/[def]\.tah\.openstreetmap\.org\//)){}else{this._attempts=(this._attempts)?(this._attempts+1):1;
if(this._attempts<=OpenLayers.IMAGE_RELOAD_ATTEMPTS){var d=this.urls;
if(d&&OpenLayers.Util.isArray(d)&&d.length>1){var e=this.src.toString();
var c,a;
for(a=0;
c=d[a];
a++){if(e.indexOf(c)!=-1){break
}}var f=Math.floor(d.length*Math.random());
var b=d[f];
a=0;
while(b==c&&a++<4){f=Math.floor(d.length*Math.random());
b=d[f]
}this.src=e.replace(c,b)
}else{var e=this.src.toString().replace(/&?_tick_=\d+/,"");
e+=(e.indexOf("?")+1>0?"&":"?")+"_tick_="+new Date().getTime();
this.src=e
}}else{if(this.layer.onLoadError){this.src=this.layer.onLoadError()
}else{if(this.src.match(/^http:\/\/[a-z0-9-]+\.ign\.fr\//)){if(this.src.match(/TRANSPARENT=true/i)){this.src=OpenLayers.Util.getImagesLocation()+"blank.gif"
}else{this.src=Geoportal.Util.getImagesLocation()+"nodata.jpg"
}}else{OpenLayers.Element.addClass(this,"olImageLoadError")
}}}this.style.display=""
}}};
Geoportal.Cookies={_get:function(f){var g=OpenLayers.getDoc().cookie.split(";");
var j={name:f,value:null,path:"",domain:"",ttl:0,secure:false};
for(var e=0,a=g.length;
e<a;
e++){var d=g[e].split("=");
var h=d[0];
var b=d[1];
if(OpenLayers.String.trim(h)===f){j.value=decodeURIComponent(b);
break
}}return(j.value===null?null:j)
},get:function(b,a){var d=Geoportal.Cookies._get(OpenLayers.String.trim(b));
return(d?d.value:a)
},expireDateToHours:function(d){var b=new Date(d);
if(isNaN(b)){return -1
}var c=new Date();
var a=(b.getTime()-c.getTime())/(60*60*1000);
return a
},set:function(i,e,b,j,d,a){if(i==null){return
}var h=OpenLayers.String.trim(typeof(i)=="object"?i.name:i);
var f=Geoportal.Cookies._get(h)||{name:"",value:null,ttl:0,path:"",domain:"",secure:false};
var g;
if(typeof(i)=="string"){g={name:h,value:typeof(e)==="undefined"?f.value||"":e,ttl:typeof(b)==="undefined"?f.ttl||"":b,path:f.path||j||"",domain:f.domain||d||"",secure:f.secure||a||false}
}else{g={name:h,value:typeof(i.value)==="undefined"?f.value||"":i.value,ttl:typeof(i.ttl)==="undefined"?f.ttl||"":i.ttl,path:f.path||i.path||"",domain:f.domain||i.domain||"",secure:f.secure||i.secure||false}
}OpenLayers.getDoc().cookie=Geoportal.Cookies.toString(g)
},remove:function(a){Geoportal.Cookies.set(a,"",-1)
},toString:function(e,b){var a=[];
a.push(e.name+"="+encodeURIComponent(""+e.value));
if(e.path){a.push("path="+e.path)
}if(b===true){a.push("domain="+(!e.domain?location.hostname:e.domain))
}if(e.ttl&&!isNaN(e.ttl)){if(e.ttl<1){a.push("expires=Thu, 01-Jan-1970 00:00:01 GMT")
}else{a.push("max-age="+e.ttl*60*60)
}}if(e.secure){a.push("secure")
}var d=a.join("; ");
return d
},cookiesEnabled:function(){var a={name:"cookieEnabled",value:"1"};
Geoportal.Cookies.set(a.name,a.value);
var d=Geoportal.Cookies.get(a.name);
var b=!(d===undefined||d!=a.value);
Geoportal.Cookies.remove(a.name);
return b
}};
Geoportal.GeoRMHandler={};
OpenLayers=OpenLayers||{};
if(!OpenLayers.getDoc){OpenLayers.getDoc=function(){return OpenLayers._document||document
}
}OpenLayers.Util=OpenLayers.Util||{};
if(!OpenLayers.Util.extend){OpenLayers.Util.extend=function(a,e){a=a||{};
if(e){for(var d in e){var c=e[d];
if(c!==undefined){a[d]=c
}}var b=typeof window.Event=="function"&&e instanceof window.Event;
if(!b&&e.hasOwnProperty&&e.hasOwnProperty("toString")){a.toString=e.toString
}}return a
}
}if(!OpenLayers.Util.getElement){OpenLayers.Util.getElement=function(){var d=[];
for(var c=0,a=arguments.length;
c<a;
c++){var b=arguments[c];
if(typeof b=="string"){b=document.getElementById(b)
}if(arguments.length==1){return b
}d.push(b)
}return d
}
}if(!OpenLayers.Util.applyDefaults){OpenLayers.Util.applyDefaults=function(d,c){d=d||{};
var b=typeof window.Event=="function"&&c instanceof window.Event;
for(var a in c){if(d[a]===undefined||(!b&&c.hasOwnProperty&&c.hasOwnProperty(a)&&!d.hasOwnProperty(a))){d[a]=c[a]
}}if(!b&&c&&c.hasOwnProperty&&c.hasOwnProperty("toString")&&!d.hasOwnProperty("toString")){d.toString=c.toString
}return d
}
}if(!OpenLayers.Util.urlAppend){OpenLayers.Util.urlAppend=function(a,b){var d=a;
if(b){var c=(a+" ").split(/[?&]/);
d+=(c.pop()===" "?b:c.length?"&"+b:"?"+b)
}return d
}
}Geoportal.GeoRMHandler.Updater=function(c,b,a,d){OpenLayers.Util.extend(this,d);
this.maps=[];
this.tgts=[];
this.scripts=[];
this.domHeads=[];
this.GeoRMKey=c;
this.lastUpdate=0;
this.serverUrl=b||Geoportal.GeoRMHandler.GEORM_SERVER_URL;
if(this.serverUrl.charAt(this.serverUrl.length-1)!="/"){this.serverUrl+="/"
}if(a){this.ttl=1000*a
}this.queryUrl=this.serverUrl+"getToken?key="+this.GeoRMKey+"&output=json&callback=Geoportal.GeoRMHandler.U"+this.GeoRMKey+".callback&";
if(OpenLayers.Events){this.events=new OpenLayers.Events(this,null,this.EVENT_TYPES)
}if(this.eventListeners instanceof Object){this.eventListeners=[]
}this.addOptions(d);
if(OpenLayers.Event){OpenLayers.Event.observe(window,"unload",this.destroy)
}};
Geoportal.GeoRMHandler.Updater.prototype={GeoRMKey:null,serverUrl:null,ttl:600000,token:null,maps:null,tgts:null,queryUrl:null,lastUpdate:0,status:0,domHeads:null,scripts:null,reload:false,EVENT_TYPES:["tokenupdatestart","tokenupdateend","tokenloaded"],events:null,onBeforeUpdateToken:function(){},onUpdateTokenFailure:function(){},onUpdateTokenSuccess:function(){},onTokenLoaded:function(a){if(a&&a.setCenter){a.setCenter(a.getCenter(),a.getZoom(),false,true)
}},initialize:function(c,b,a,d){OpenLayers.Util.extend(this,d);
this.maps=[];
this.tgts=[];
this.scripts=[];
this.domHeads=[];
this.GeoRMKey=c;
this.lastUpdate=0;
this.serverUrl=b||Geoportal.GeoRMHandler.GEORM_SERVER_URL;
if(this.serverUrl.charAt(this.serverUrl.length-1)!="/"){this.serverUrl+="/"
}if(a){this.ttl=1000*a
}this.queryUrl=this.serverUrl+"getToken?key="+this.GeoRMKey+"&output=json&callback=Geoportal.GeoRMHandler.U"+this.GeoRMKey+".callback&";
if(OpenLayers.Events){this.events=new OpenLayers.Events(this,null,this.EVENT_TYPES)
}if(this.eventListeners instanceof Object){this.eventListeners=[]
}this.addOptions(d);
if(OpenLayers.Event){OpenLayers.Event.observe(window,"unload",this.destroy)
}},addOptions:function(a){if(a){if(a.eventListeners&&a.eventListeners instanceof Object){if(!this.eventListeners){this.eventListeners=[]
}this.eventListeners.push(a.eventListeners);
if(this.events){this.events.on(a.eventListeners)
}}}},addMap:function(e){for(var c=0,a=this.maps.length;
c<a;
c++){if(this.maps[c]===e){return
}}this.maps.push(e);
var d=(e?e.div.ownerDocument:OpenLayers.getDoc());
for(var c=0,a=this.tgts.length;
c<a;
c++){if(this.tgts[c]===d){return
}}this.tgts.push(d);
var b=(d.getElementsByTagName("head").length?d.getElementsByTagName("head")[0]:d.body);
this.domHeads.push(b);
this.getToken()
},destroy:function(){if(OpenLayers.Event){OpenLayers.Event.stopObserving(window,"unload",this.destroy)
}if(this.events){if(this.eventListeners){for(var b=0,a=this.eventListeners.length;
b<a;
b++){this.events.un(this.eventListeners[b])
}this.eventListeners=null
}this.events.destroy();
this.events=null
}if(this.GeoRMKey){this.GeoRMKey=null
}if(this.serverUrl){this.serverUrl=null
}if(this.token){this.token=null
}if(this.maps){this.maps=null
}if(this.tgts){this.tgts=null
}if(this.scripts){this.scripts=null
}if(this.domHeads){this.domHeads=null
}if(this.queryUrl){this.queryUrl=null
}},getToken:function(){if(this.domHeads.length==0){return null
}var a=(new Date()).getTime();
var b=(!this.token)||(this.lastUpdate+this.ttl<a);
if(this.lastUpdate+this.ttl/2<a){if(this.status==0){this.lastUpdate=a;
this.updateToken()
}}if(b&&this.status>=0){this.token=null;
this.reload=true;
return null
}return this.token
},updateToken:function(){this.onBeforeUpdateToken();
if(this.events&&this.events.triggerEvent("updatetokenstart")===false){return
}for(var g=0,b=this.scripts.length;
g<b;
g++){var c=this.scripts[g];
if(c){try{c.parentNode.removeChild(c)
}catch(j){}this.scripts[g]=null
}}this.status++;
if(this.status>=10){this.status=0;
this.onUpdateTokenFailure();
if(this.events&&this.events.triggerEvent("updatetokenstop")!==false){if(OpenLayers.Console){OpenLayers.Console.error(OpenLayers.i18n("geoRM.failed",{key:this.GeoRMKey}))
}}return
}var d=this.queryUrl;
for(var f in this.token){d+=f+"="+this.token[f]+"&"
}for(var g=0,b=this.domHeads.length;
g<b;
g++){if(this.domHeads[g]==null){continue
}try{var c=this.domHeads[g].ownerDocument.createElement("script");
c.setAttribute("type","text/javascript");
var h=d;
if(this.transport=="referrer"){if(this.referrer){h+="cookie=referrer,"+encodeURIComponent(this.referrer)
}else{h+=Geoportal.GeoRMHandler.getCookieReferrer(this.domHeads[g])
}h+="&"
}c.setAttribute("src",h);
this.domHeads[g].appendChild(c);
this.scripts[g]=c;
break
}catch(j){this.domHeads[g]=null
}}if(this.timeout){window.clearTimeout(this.timeout)
}var a=this.status*this.ttl/10;
this.timeout=window.setTimeout("Geoportal.GeoRMHandler.U"+this.GeoRMKey+" && Geoportal.GeoRMHandler.U"+this.GeoRMKey+".updateToken()",a)
},callback:function(c){this.onUpdateTokenSuccess();
if(this.events&&this.events.triggerEvent("updatetokenend")===false){return
}if(c==null){if(OpenLayers.Console){OpenLayers.Console.error(OpenLayers.i18n("geoRM.getToken",{key:this.GeoRMKey}))
}}else{if(this.status>0){this.token=c;
if(this.timeout){window.clearTimeout(this.timeout);
this.timeout=null
}this.status=-1;
if(this.reload){for(var b=0,a=this.maps.length;
b<a;
b++){if((this.events&&this.events.triggerEvent("tokenloaded")!==false)||!this.events){this.onTokenLoaded(this.maps[b])
}}this.reload=false
}this.status=0
}}},CLASS_NAME:"Geoportal.GeoRMHandler.Updater"};
Geoportal.GeoRMHandler.getCookieReferrer=function(f,i){var c=i===true?{}:"";
if(Geoportal.Cookies.cookiesEnabled()){var b=Geoportal.Cookies.get(Geoportal.GeoRMHandler.GEORM_REFERRER_COOKIENAME);
if(b===undefined){var h=f||OpenLayers.getDoc();
h=h.ownerDocument||h;
var a=h.defaultView||h.parentWindow;
var g=a.opener;
try{if(g){b=g&&g.location&&g.location.href
}else{b=h.location.href
}}catch(f){b=h.location.href
}b="referrer,"+encodeURIComponent(b||"http://localhost/")
}c=i===true?{cookie:b}:"cookie="+b
}else{if(OpenLayers.Console){OpenLayers.Console.warn(OpenLayers.i18n("cookies.not.enabled"))
}}return c
};
Geoportal.GeoRMHandler.getConfig=function(k,j,c,n){if(!k){return 0
}if(typeof(k)=="string"){k=[k]
}if(k.length==0){return 0
}n=n||{};
var b=function(l){if(!l){return null
}var i=/\W*function\s+([\w\$]+)\(/.exec(l);
if(!i){return null
}return i[1]
};
var f=!j?"Geoportal.GeoRMHandler.getContract":typeof(j)=="string"?j:b(j);
if(!f){return 0
}var h=OpenLayers.getDoc();
var g=(h.getElementsByTagName("head").length?h.getElementsByTagName("head")[0]:h.body);
for(var e=0,d=k.length;
e<d;
e++){var m=OpenLayers.Util.getElement("__"+k[e]+"__");
if(m&&m.parentNode){m.parentNode.removeChild(m)
}m=h.createElement("script");
m.id="__"+k[e]+"__";
m.setAttribute("type","text/javascript");
var a=(c||Geoportal.GeoRMHandler.GEORM_SERVER_URL)+"getConfig?key="+k[e]+"&output=json&callback="+f+"&";
m.setAttribute("src",a);
g.appendChild(m)
}if(!j){if(window.gGEOPORTALRIGHTSMANAGEMENT===undefined){gGEOPORTALRIGHTSMANAGEMENT={}
}OpenLayers.Util.extend(gGEOPORTALRIGHTSMANAGEMENT,{pending:0,apiKey:[],services:{}});
OpenLayers.Util.extend(gGEOPORTALRIGHTSMANAGEMENT,n);
gGEOPORTALRIGHTSMANAGEMENT.pending+=k.length
}return k.length
};
Geoportal.GeoRMHandler.getContract=function(e){if(gGEOPORTALRIGHTSMANAGEMENT.pending>0){gGEOPORTALRIGHTSMANAGEMENT.pending--;
if(e.error){OpenLayers.Console.warn(e.error)
}else{gGEOPORTALRIGHTSMANAGEMENT.apiKey.push(e.key);
gGEOPORTALRIGHTSMANAGEMENT[e.key]={tokenServer:{url:e.service,ttl:e.tokenTimeOut},tokenTimeOut:e.tokenTimeOut,bounds:e.boundingBox?[e.boundingBox.minx,e.boundingBox.miny,e.boundingBox.maxx,e.boundingBox.maxy]:[-180,-90,180,90],allowedGeoportalLayers:new Array(e.resources.length),resources:{}};
for(var b=0,a=e.resources.length;
b<a;
b++){var d=e.resources[b];
gGEOPORTALRIGHTSMANAGEMENT[e.key].allowedGeoportalLayers[b]=d.name+":"+d.type;
gGEOPORTALRIGHTSMANAGEMENT[e.key].resources[d.name+":"+d.type]=OpenLayers.Util.extend({},d);
if(gGEOPORTALRIGHTSMANAGEMENT.services[d.url]===undefined){if(gGEOPORTALRIGHTSMANAGEMENT.services[d.url]===undefined){gGEOPORTALRIGHTSMANAGEMENT.services[d.url]={id:"__"+d.url.replace(/[^a-z0-9.-]/gi,"_")+"__",type:d.type,caps:null}
}}}var c=OpenLayers.Util.getElement("__"+e.key+"__");
if(c&&c.parentNode){c.parentNode.removeChild(c)
}}if(gGEOPORTALRIGHTSMANAGEMENT.pending==0&&typeof(gGEOPORTALRIGHTSMANAGEMENT.onContractsComplete)==="function"){gGEOPORTALRIGHTSMANAGEMENT.onContractsComplete()
}}return gGEOPORTALRIGHTSMANAGEMENT
};
Geoportal.GeoRMHandler.getServicesCapabilities=function(i,j,d,m){if(window.gGEOPORTALRIGHTSMANAGEMENT===undefined){gGEOPORTALRIGHTSMANAGEMENT={}
}if(!i){if(!gGEOPORTALRIGHTSMANAGEMENT.services){return null
}i=gGEOPORTALRIGHTSMANAGEMENT.services
}m=m||{};
OpenLayers.Util.applyDefaults(m,gGEOPORTALRIGHTSMANAGEMENT.capabilities);
var b=function(o){if(!o){return null
}var n=/\W*function\s+([\w\$]+)\(/.exec(o);
if(!n){return null
}return n[1]
};
var f=!j?(m.callback?m.callback:"Geoportal.GeoRMHandler.getCapabilities"):(typeof(j)=="string"?j:b(j));
if(!f){return null
}for(var k in i){var c=i[k];
var g=c.type;
switch(c.type){case"WFS":break;
case"WMTS":break;
case"WMSC":g="WMS";
break;
case"WMS":break;
default:c.caps={};
continue
}var h=OpenLayers.getDoc();
var e=(h.getElementsByTagName("head").length?h.getElementsByTagName("head")[0]:h.body);
var l=OpenLayers.Util.getElement(c.id);
if(l&&l.parentNode){l.parentNode.removeChild(l)
}l=h.createElement("script");
l.id=c.id;
l.setAttribute("type","text/javascript");
var a=d||m.proxy||Geoportal.JSON_PROXY_URL;
a=OpenLayers.Util.urlAppend(a,"url="+encodeURIComponent(OpenLayers.Util.urlAppend(k,"SERVICE="+g+"&REQUEST=GetCapabilities&"))+"&callback="+f+"&");
l.setAttribute("src",a);
e.appendChild(l)
}if(!j){if(m.onCapabilitiesComplete){gGEOPORTALRIGHTSMANAGEMENT.onCapabilitiesComplete=m.onCapabilitiesComplete
}}return i
};
Geoportal.GeoRMHandler.getCapabilities=function(h){if(!h){h={}
}if(!h.http){h.http={}
}if(!h.http.code){h.http.code=400
}if(!h.http.url){h.http.url="http://localhost/?"
}if(!h.xml){h.xml=""
}var m=h.http.url.split("?")[0];
var c=gGEOPORTALRIGHTSMANAGEMENT.services[m];
if(h.http.code!=200){OpenLayers.Console.warn(OpenLayers.i18n("url.error",{url:h.http.url,msg:""}))
}else{if(c){var j=OpenLayers.parseXMLString(h.xml);
var d=null;
switch(c.type){case"WFS":d=OpenLayers.Format?OpenLayers.Format.WFSCapabilities:null;
break;
case"WMTS":d=OpenLayers.Format?OpenLayers.Format.WMTSCapabilities:null;
break;
case"WMSC":case"WMS":d=OpenLayers.Format?OpenLayers.Format.WMSCapabilities:null;
break;
default:break
}if(d){var a=new d();
var f=null;
try{f=a.read(j)
}catch(k){OpenLayers.Console.warn("url.error",{url:h.http.url,msg:""})
}finally{if(f&&f.exceptions){var b="";
for(var g=0,e=f.exceptions.length;
g<e;
g++){b+=f.exceptions[g]+"\n"
}OpenLayers.Console.warn("url.error",{url:h.http.url,msg:b})
}else{c.caps=f
}}}var n=OpenLayers.Util.getElement(c.id);
if(n&&n.parentNode){n.parentNode.removeChild(n)
}}}if(c&&!c.caps){c.caps={}
}for(var c in gGEOPORTALRIGHTSMANAGEMENT.services){if(gGEOPORTALRIGHTSMANAGEMENT.services[c].caps===null){return
}}if(typeof(gGEOPORTALRIGHTSMANAGEMENT.onCapabilitiesComplete)==="function"){gGEOPORTALRIGHTSMANAGEMENT.onCapabilitiesComplete()
}};
Geoportal.GeoRMHandler.addKey=function(c,b,a,g,d){var f=OpenLayers.getDoc();
var e=(f.defaultView||f.parentWindow).Geoportal.GeoRMHandler;
if(!e["U"+c]){e["U"+c]=new Geoportal.GeoRMHandler.Updater(c,b,a,d);
e["U"+c].getToken()
}else{e["U"+c].addOptions(d)
}e["U"+c].addMap(g);
return e["U"+c]
};
Geoportal.GeoRMHandler.GEORM_REFERRER_COOKIENAME="__rfrrric__";
Geoportal.GeoRMHandler.GEORM_SERVER_URL="http://jeton-api.ign.fr/";
Geoportal.JSON_PROXY_URL="http://api.ign.fr/geoportail/api/xmlproxy?output=json";
Geoportal.Layer=OpenLayers.Class(OpenLayers.Layer,{clone:function(a){if(a==null){a=new Geoportal.Layer(this.name,this.getOptions())
}a=OpenLayers.Layer.prototype.clone.apply(this,[a]);
return a
},CLASS_NAME:"Geoportal.Layer"});
Geoportal.Layer.onPreAddLayer=function(a){if(a==null){return
}if(a.layer==null){return
}if(a.layer.isBaseLayer){return
}if(a.layer.getCompatibleProjection(this)!==null){if(!a.layer.savedStates[this.id]){a.layer.savedStates[this.id]={}
}a.layer.savedStates[this.id].visibility=!!a.layer.visibility;
if(a.layer.opacity!=undefined){a.layer.savedStates[this.id].opacity=a.layer.opacity
}}};
Geoportal.Layer.Grid=OpenLayers.Class(OpenLayers.Layer.Grid,{nativeTileOrigin:null,nativeTileSize:null,nativeResolutions:null,nativeMaxExtent:null,resample:false,initialize:function(c,b,d,a){a=a||{};
if(a.gridOrigin){a.tileOrigin=a.gridOrigin.clone();
delete a.gridOrigin
}OpenLayers.Layer.Grid.prototype.initialize.apply(this,arguments);
if(!this.maxExtent&&this.nativeMaxExtent){this.maxExtent=this.nativeMaxExtent.clone()
}if(!this.nativeMaxExtent&&this.maxExtent){this.nativeMaxExtent=this.maxExtent.clone()
}this.tileOrigin=this.getTileOrigin();
this.nativeTileOrigin=this.tileOrigin.clone();
this.nativeTileSize=this.nativeTileSize||new OpenLayers.Size(OpenLayers.Map.TILE_WIDTH,OpenLayers.Map.TILE_HEIGHT);
this.tileSize=this.tileSize?this.tileSize.clone():new OpenLayers.Size(OpenLayers.Map.TILE_WIDTH,OpenLayers.Map.TILE_HEIGHT)
},setMap:function(a){OpenLayers.Layer.Grid.prototype.setMap.apply(this,arguments);
if(!this.nativeMaxExtent&&this.maxExtent){this.nativeMaxExtent=this.maxExtent.clone().transform(this.map.getProjection(),this.getNativeProjection())
}this.tileOrigin.transform(this.getNativeProjection(),this.map.getProjection())
},destroy:function(){this.tileOrigin=null;
this.nativeTileOrigin=null;
this.nativeTileSize=null;
this.nativeResolutions=null;
this.nativeMaxExtent=null;
this.resample=false;
OpenLayers.Layer.Grid.prototype.destroy.apply(this,arguments)
},clone:function(a){if(a==null){a=new Geoportal.Layer.Grid(this.name,this.url,this.params,this.options)
}a=OpenLayers.Layer.HTTPRequest.prototype.clone.apply(this,[a]);
if(this.tileSize!=null){a.tileSize=this.tileSize.clone()
}if(this.tileOrigin!=null){a.tileOrigin=this.tileOrigin.clone()
}if(this.nativeTileOrigin!=null){a.nativeTileOrigin=this.nativeTileOrigin.clone()
}if(this.nativeTileSize!=null){a.nativeTileSize=this.nativeTileSize.clone()
}if(this.nativeResolutions!=null){a.nativeResolutions=this.nativeResolutions.slice(0)
}a.grid=[];
return a
},moveTo:function(d,a,e){OpenLayers.Layer.HTTPRequest.prototype.moveTo.apply(this,arguments);
d=d||this.map.getExtent();
if(d!=null){var c=!this.grid.length||a;
var b=this.getTilesBounds();
if(this.resample){b.transform(this.getNativeProjection(),this.map.getProjection(),true)
}if(this.singleTile){if(c||(!e&&!b.containsBounds(d))){this.initSingleTile(d)
}}else{if(c||!b.containsBounds(d,true)){this.initGriddedTiles(d)
}else{if(this.timerId!=null){window.clearTimeout(this.timerId)
}this._bounds=d;
this.timerId=window.setTimeout(this._moveGriddedTiles,this.tileLoadingDelay)
}}if(!c&&this.resample&&this.getVisibility()){if(this.forceRedrawTimer){window.clearTimeout(this.forceRedrawTimer)
}this.forceRedrawTimer=window.setTimeout(OpenLayers.Function.bind(function(){this.moveTo(d,true,e);
this.forceRedrawTimer=null
},this),500)
}}},updateTileSize:function(a){this.resample=false;
var j=this.getNativeProjection(),b=this.map.getProjection();
var c=this.map.getResolution();
this.nativeResolution=c;
if(!(j.isCompatibleWith(b))){return
}var h=new OpenLayers.LonLat(1,1);
h.transform(j,b);
if(this.nativeResolutions){var g=0;
for(var d=Math.max(0,this.minZoomLevel),e=Math.min(this.nativeResolutions.length,this.maxZoomLevel+1);
d<e;
d++){var f=this.nativeResolutions[d]*h.lon/c;
if(f>1){f=1/f
}if(f>g){g=f;
this.nativeResolution=this.nativeResolutions[d]
}}}this.resample=(((this.nativeResolution/c)*h.lat!=1)||((this.nativeResolution/c)*h.lon!=1))
},getTileOrigin:function(){var a=this.tileOrigin||new OpenLayers.LonLat(0,0);
return a
},initGriddedTiles:function(E){this.updateTileSize(E);
var c=this.map.getSize();
var J=this.nativeResolution*this.nativeTileSize.w;
var O=this.nativeResolution*this.nativeTileSize.h;
var n=this.map.getProjection(),P=this.getNativeProjection();
var b=E.clone().transform(n,P,true);
var d={lon:this.nativeTileOrigin.lon+Math.floor((b.left-this.nativeTileOrigin.lon)/J)*J,lat:0};
var o={lon:0,lat:this.nativeTileOrigin.lat+Math.floor((b.bottom-this.nativeTileOrigin.lat)/O)*O};
var N={lon:this.nativeTileOrigin.lon+Math.ceil((b.right-this.nativeTileOrigin.lon)/J)*J,lat:0};
var Q={lon:0,lat:this.nativeTileOrigin.lat+Math.ceil((b.top-this.nativeTileOrigin.lat)/O)*O};
var z=Math.round((N.lon-d.lon)/J);
var e=Math.round((Q.lat-o.lat)/O);
var R=Math.max(1,2*this.buffer);
if(R>1){d.lon-=this.buffer*J;
N.lon+=this.buffer*J;
o.lat-=this.buffer*O;
Q.lat+=this.buffer*O
}else{d.lon-=J;
N.lon+=J;
o.lat-=O;
Q.lat+=O;
R++
}z+=R;
e+=R;
var M=this.map.getResolution();
var u=this.nativeResolution*this.nativeTileSize.w;
var S=d.lon;
var a=this.nativeResolution*this.nativeTileSize.h;
var t=Q.lat-a;
var v=(new OpenLayers.LonLat(d.lon,Q.lat)).transform(P,n);
var B=this.map.getViewPortPxFromLonLat(v);
var L=B.x;
var K=B.y;
var s=L;
var C=S;
var m=0;
var q=parseInt(this.map.layerContainerDiv.style.left);
var A=parseInt(this.map.layerContainerDiv.style.top);
var D=new OpenLayers.LonLat(0,0);
var G=new OpenLayers.LonLat(0,0);
do{var p=this.grid[m++];
if(!p){p=[];
this.grid.push(p)
}S=C;
L=s;
var T=0;
do{var F=new OpenLayers.Bounds(S,t,S+u,t+a);
var I=L;
I-=q;
var H=K;
H-=A;
var l=Math.round(I);
var k=Math.round(H);
var i=new OpenLayers.Pixel(l,k);
var f=p[T++];
D.lon=F.right,D.lat=F.top;
D.transform(P,n);
G.lon=F.left,G.lat=F.bottom;
G.transform(P,n);
var j=Math.round((D.lon-G.lon)/M);
var r=Math.round((D.lat-G.lat)/M);
var g=new OpenLayers.Size(j,r);
if(!f){f=this.addTile(F,i,g);
this.addTileMonitoringHooks(f);
p.push(f)
}else{f.moveTo(F,i,false);
f.setSize(g)
}S+=u;
L+=j
}while(T<z);
t-=a;
K+=r
}while(m<e);
this.spiralTileLoad()
},addTile:function(c,a,b){return new Geoportal.Tile.Image(this,a,c,null,b,this.tileOptions)
},CLASS_NAME:"Geoportal.Layer.Grid"});
Geoportal.UI=OpenLayers.Class(OpenLayers.UI,{setComponent:function(a){this.component=a;
if(!this.displayClass){this.displayClass=a.CLASS_NAME.replace("Geoportal.","gp").replace(/\./g,"")
}},CLASS_NAME:"Geoportal.UI"});
Geoportal.Control=OpenLayers.Class(OpenLayers.Control,{uis:["Geoportal.UI"],CLASS_NAME:"Geoportal.Control"});
if(OpenLayers.UI&&!OpenLayers.UI.Panel){Geoportal.Control=OpenLayers.overload(Geoportal.Control,{initialize:function(a){this.displayClass=this.CLASS_NAME.replace("Geoportal.","gp").replace(/\./g,"");
OpenLayers.Util.extend(this,a);
this.events=new OpenLayers.Events(this,null,this.EVENT_TYPES);
if(this.eventListeners instanceof Object){this.events.on(this.eventListeners)
}if(this.id==null){this.id=OpenLayers.Util.createUniqueID(this.CLASS_NAME+"_")
}}})
}Geoportal.Control.selectFeature=function(a){if(a){if(!a.popup){a.createPopup()
}if(a.layer&&a.layer.map&&a.popup){a.layer.map.addPopup(a.popup)
}}};
Geoportal.Control.renderFeatureAttributes=function(c,d){if(!c){return["",""]
}if(!d){d=[]
}d.unshift("styleUrl");
var e='<table border="1" cellspacing="0" cellpadding="0">';
var b="";
for(var a in c.attributes){if(c.attributes.hasOwnProperty(a)&&(OpenLayers.Array.filter(d,function(f){return new RegExp("^"+a+"$","i").test(f)
})).length==0){if(a.toLowerCase()=="name"){if(c.attributes[a]&&typeof(c.attributes[a])=="object"){b=c.attributes[a].value
}else{b=c.attributes[a]
}b=b||""
}e+='<tr><td class="gpAttName">'+a+'</td><td class="gpAttValue">'+(c.attributes[a]&&typeof(c.attributes[a])=="object"?c.attributes[a].value:c.attributes[a])||"</td></tr>"
}}e+="</table>";
return[b,e]
};
Geoportal.Control.hoverFeature=function(b){if(b){if(!b.popup){var a=Geoportal.Control.renderFeatureAttributes(b);
if(!b.popupClass){b.popupClass=b.layer&&b.layer.formatOptions&&b.layer.formatOptions.popupClass?b.layer.formatOptions.popupClass:typeof(OpenLayers.Popup)!="undefined"&&typeof(OpenLayers.Popup.FramedCloud)!="undefined"?OpenLayers.Popup.FramedCloud:null
}if(b.popupClass){b.popup=new b.popupClass("chicken",b.geometry.getBounds().getCenterLonLat(),null,'<div class="gpPopupHead">'+a[0]+'</div><div class="gpPopupBody">'+a[1]+"</div>",null,false);
Geoportal.Popup.completePopup(b.popup,{maxSize:new OpenLayers.Size(300,300),overflow:"auto"})
}else{b.popup=null
}}if(b.layer&&b.layer.map&&b.popup){b.layer.map.addPopup(b.popup,true)
}}};
Geoportal.Control.unselectFeature=function(a){if(a){if(a.popup){a.popup.destroy();
a.popup=null
}}};
Geoportal.Control.mapMouseOut=function(a){if(this.map&&this.map.events){this.map.events.triggerEvent("mapmouseout");
if(a!=null){OpenLayers.Event.stop(a)
}}};
Geoportal.Control.mapMouseOver=function(a){if(this.map&&this.map.events){this.map.events.triggerEvent("mapmouseover");
if(a!=null){OpenLayers.Event.stop(a)
}}};
Geoportal.Control.TermsOfService=OpenLayers.Class(Geoportal.Control,{tosLabel:null,tosURL:null,draw:function(a){Geoportal.Control.prototype.draw.apply(this,arguments);
this.updateTermsOfService();
return this.div
},updateTermsOfService:function(){if(this.div.childNodes.length>0){this.div.removeChild(this.div.childNodes[0])
}if(this.tosLabel==null){this.tosLabel="TOS"
}if(this.tosURL==null){this.tosURL="http://www.ign.fr/partage/api/cgu/licAPI_CGUF.pdf"
}var a=OpenLayers.i18n(this.tosLabel);
var b=this.div.ownerDocument.createElement("a");
b.setAttribute("href",this.tosURL);
b.setAttribute("alt",a);
b.setAttribute("title",a);
b.setAttribute("target","_blank");
b.appendChild(this.div.ownerDocument.createTextNode(a));
this.div.appendChild(b)
},setMap:function(){Geoportal.Control.prototype.setMap.apply(this,arguments);
this.map.events.register("controlvisibilitychanged",this,this.onVisibilityChanged)
},onVisibilityChanged:function(d){if(!d||!d.size){return
}var c=(d.visibility?1:-1);
var a=Geoportal.Util.getComputedStyle(this.div,"bottom",true);
a=a+c*d.size.h;
this.div.style.bottom=a+"px"
},changeLang:function(a){this.updateTermsOfService()
},CLASS_NAME:"Geoportal.Control.TermsOfService"});
Geoportal.Layer.WFS=OpenLayers.Class(OpenLayers.Layer.WFS,{CLASS_NAME:"Geoportal.Layer.WFS"});
Geoportal.Layer.WMTS=OpenLayers.Class(Geoportal.Layer.Grid,{isBaseLayer:false,version:"1.0.0",requestEncoding:"KVP",url:null,layer:null,matrixSet:null,style:null,format:"image/jpeg",tileOrigin:null,tileFullExtent:null,formatSuffix:null,matrixIds:null,dimensions:null,params:null,zoomOffset:0,formatSuffixMap:{"image/png":"png","image/png8":"png","image/png24":"png","image/png32":"png",png:"png","image/jpeg":"jpg","image/jpg":"jpg",jpeg:"jpg",jpg:"jpg"},matrix:null,initialize:function(c){var f={url:true,layer:true,style:true,matrixSet:true};
for(var g in f){if(!(g in c)){throw new Error("Missing property '"+g+"' in layer configuration.")
}}c.params=OpenLayers.Util.upperCaseObject(c.params);
var b=[c.name,c.url,c.params,c];
Geoportal.Layer.Grid.prototype.initialize.apply(this,b);
if(!this.formatSuffix){this.formatSuffix=this.formatSuffixMap[this.format]||this.format.split("/").pop()
}if(this.matrixIds){var a=this.matrixIds.length;
if(a&&typeof this.matrixIds[0]==="string"){var e=this.matrixIds;
this.matrixIds=new Array(a);
for(var d=0;
d<a;
++d){this.matrixIds[d]={identifier:e[d]}
}}}},setMap:function(){Geoportal.Layer.Grid.prototype.setMap.apply(this,arguments);
this.updateMatrixProperties()
},updateMatrixProperties:function(){this.matrix=this.getMatrix();
if(this.matrix){if(this.matrix.topLeftCorner){this.nativeTileOrigin=this.matrix.topLeftCorner;
this.tileOrigin=this.matrix.topLeftCorner.clone().transform(this.getNativeProjection(),this.map.getProjection())
}if(this.matrix.tileWidth&&this.matrix.tileHeight){this.tileSize=new OpenLayers.Size(this.matrix.tileWidth,this.matrix.tileHeight)
}if(!this.tileOrigin){this.tileOrigin=new OpenLayers.LonLat(this.maxExtent.left,this.maxExtent.top);
this.nativeTileOrigin=this.tileOrigin.clone().transform(this.map.getProjection(),this.getNativeProjection())
}if(!this.tileFullExtent){this.tileFullExtent=this.maxExtent
}}},moveTo:function(b,a,c){if(a||!this.matrix){this.updateMatrixProperties()
}return Geoportal.Layer.Grid.prototype.moveTo.apply(this,arguments)
},clone:function(a){if(a==null){a=new Geoportal.Layer.WMTS(this.options)
}a=Geoportal.Layer.Grid.prototype.clone.apply(this,[a]);
return a
},getMatrix:function(){var b;
if(!this.matrixIds||this.matrixIds.length===0){b={identifier:this.map.getZoom()+this.zoomOffset}
}else{if("scaleDenominator" in this.matrixIds[0]){var a=OpenLayers.METERS_PER_INCH*OpenLayers.INCHES_PER_UNIT[this.units]*this.map.getResolution()/0.00028;
var e=Number.POSITIVE_INFINITY;
var f;
for(var c=0,d=this.matrixIds.length;
c<d;
++c){f=Math.abs(1-(this.matrixIds[c].scaleDenominator/a));
if(f<e){e=f;
b=this.matrixIds[c]
}}}else{b=this.matrixIds[this.map.getZoom()+this.zoomOffset]
}}return b
},getTileInfo:function(f){var g,j,e,a,i;
if(this.nativeResolution){g=this.nativeResolution;
j=this.nativeTileSize.w;
e=this.nativeTileSize.h;
a=this.nativeTileOrigin.lon;
i=this.nativeTileOrigin.lat
}else{g=this.map.getResolution();
j=this.tileSize.w;
e=this.tileSize.h;
a=this.tileOrigin.lon;
i=this.tileOrigin.lat
}var d=(f.lon-a)/(g*j);
var c=(i-f.lat)/(g*e);
var b=Math.floor(d);
var k=Math.floor(c);
return{col:b,row:k,i:Math.floor((d-b)*j),j:Math.floor((c-k)*e)}
},getURL:function(d){d=this.adjustBounds(d);
var b="";
if(!this.tileFullExtent||this.tileFullExtent.intersectsBounds(d)){var a=d.getCenterLonLat();
var f=this.getTileInfo(a);
var h=this.matrix.identifier;
if(this.requestEncoding.toUpperCase()==="REST"){var e=this.version+"/"+this.layer+"/"+this.style+"/";
if(this.dimensions){for(var c=0;
c<this.dimensions.length;
c++){if(this.params[this.dimensions[c]]){e=e+this.params[this.dimensions[c]]+"/"
}}}e=e+this.matrixSet+"/"+this.matrix.identifier+"/"+f.row+"/"+f.col+"."+this.formatSuffix;
if(OpenLayers.Util.isArray(this.url)){b=this.selectUrl(e,this.url)
}else{b=this.url
}if(!b.match(/\/$/)){b=b+"/"
}b=b+e
}else{if(this.requestEncoding.toUpperCase()==="KVP"){var g={SERVICE:"WMTS",REQUEST:"GetTile",VERSION:this.version,LAYER:this.layer,STYLE:this.style,TILEMATRIXSET:this.matrixSet,TILEMATRIX:this.matrix.identifier,TILEROW:f.row,TILECOL:f.col,FORMAT:this.format};
b=Geoportal.Layer.Grid.prototype.getFullRequestString.apply(this,[g])
}}}return b
},mergeNewParams:function(a){if(this.requestEncoding.toUpperCase()==="KVP"){return Geoportal.Layer.Grid.prototype.mergeNewParams.apply(this,[OpenLayers.Util.upperCaseObject(a)])
}},CLASS_NAME:"Geoportal.Layer.WMTS"});
Geoportal.Tile=OpenLayers.Class(OpenLayers.Tile,{CLASS_NAME:"Geoportal.Tile"});
Geoportal.Tile.Image=OpenLayers.Class(OpenLayers.Tile.Image,{setSize:function(a){if(this.frame!=null&&(!this.size||!this.size.equals(a))){OpenLayers.Util.modifyDOMElement(this.frame,null,null,a);
this.size=a;
if(this.imgDiv!=null){OpenLayers.Util.modifyDOMElement(this.imgDiv,null,null,a)
}}},resetBackBuffer:function(){this.showTile();
if(this.backBufferTile&&(this.isFirstDraw||!this.layer.numLoadingTiles)){this.isFirstDraw=false;
var a=this.layer.maxExtent;
var b=(a&&this.bounds.intersectsBounds(a,false));
if(b){this.backBufferTile.position=this.position;
this.backBufferTile.bounds=this.bounds;
this.backBufferTile.size=this.size;
this.backBufferTile.imageSize=this.size||this.layer.getImageSize(this.bounds);
this.backBufferTile.imageOffset=this.layer.imageOffset;
this.backBufferTile.resolution=this.layer.getResolution();
this.backBufferTile.renderTile()
}this.backBufferTile.hide()
}},positionImage:function(){if(this.layer==null){return
}OpenLayers.Util.modifyDOMElement(this.frame,null,this.position,this.size);
var a=this.size||this.layer.getImageSize(this.bounds);
if(this.layerAlphaHack){OpenLayers.Util.modifyAlphaImageDiv(this.imgDiv,null,null,a,this.url)
}else{OpenLayers.Util.modifyDOMElement(this.imgDiv,null,null,a);
this.imgDiv.src=this.url
}},initImgDiv:function(){if(this.imgDiv==null){var d=this.layer.imageOffset;
var b=this.size||this.layer.getImageSize(this.bounds);
if(this.layerAlphaHack){this.imgDiv=OpenLayers.Util.createAlphaImageDiv(null,d,b,null,"relative",null,null,null,true)
}else{this.imgDiv=OpenLayers.Util.createImage(null,d,b,null,"relative",null,null,true)
}if(OpenLayers.Util.isArray(this.layer.url)){this.imgDiv.urls=this.layer.url.slice()
}this.imgDiv.className="olTileImage";
this.frame.style.zIndex=this.isBackBuffer?0:1;
this.frame.appendChild(this.imgDiv);
this.layer.div.appendChild(this.frame);
if(this.layer.opacity!=null){OpenLayers.Util.modifyDOMElement(this.imgDiv,null,null,null,null,null,null,this.layer.opacity)
}this.imgDiv.map=this.layer.map;
this.imgDiv.layer=this.layer;
var c=function(){if(this.isLoading){this.isLoading=false;
this.events.triggerEvent("loadend")
}};
if(this.layerAlphaHack){OpenLayers.Event.observe(this.imgDiv.childNodes[0],"load",OpenLayers.Function.bind(c,this))
}else{OpenLayers.Event.observe(this.imgDiv,"load",OpenLayers.Function.bind(c,this))
}var a=function(){if(this.imgDiv._attempts>OpenLayers.IMAGE_RELOAD_ATTEMPTS){c.call(this)
}};
OpenLayers.Event.observe(this.imgDiv,"error",OpenLayers.Function.bind(a,this))
}this.imgDiv.viewRequestID=this.layer.map.viewRequestID
},CLASS_NAME:"Geoportal.Tile.Image"});
Geoportal.Layer.WMSC=OpenLayers.Class(Geoportal.Layer.Grid,{DEFAULT_PARAMS:{service:"WMS",version:"1.1.1",request:"GetMap",styles:"",exceptions:"application/vnd.ogc.se_inimage",format:"image/jpeg"},isBaseLayer:false,initialize:function(d,c,e,b){var a=[];
e=OpenLayers.Util.upperCaseObject(e);
a.push(d,c,e,b);
Geoportal.Layer.Grid.prototype.initialize.apply(this,a);
OpenLayers.Util.applyDefaults(this.params,OpenLayers.Util.upperCaseObject(this.DEFAULT_PARAMS));
if(this.params.TRANSPARENT&&this.params.TRANSPARENT.toString().toLowerCase()=="true"){if((b==null)||(!b.isBaseLayer)){this.isBaseLayer=false
}if(this.params.FORMAT=="image/jpeg"){this.params.FORMAT=OpenLayers.Util.alphaHack()?"image/gif":"image/png"
}}},destroy:function(){Geoportal.Layer.Grid.prototype.destroy.apply(this,arguments)
},clone:function(a){if(a==null){a=new Geoportal.Layer.WMSC(this.name,this.url,this.params,this.options)
}a=Geoportal.Layer.Grid.prototype.clone.apply(this,[a]);
return a
},initResolutions:function(){Geoportal.Layer.Grid.prototype.initResolutions.apply(this,arguments);
if(this.nativeResolutions){var a=Math.max(0,this.minZoomLevel),b=Math.min(this.nativeResolutions.length,this.maxZoomLevel+1);
if(a>b){OpenLayers.Console.error("resolutions inconsistency - check "+this.name+" (deactived)");
this.minZoomLevel=this.map.baseLayer.maxZoomLevel+1;
this.maxZoomLevel=this.minZoomLevel;
this.visibility=false
}}},getURL:function(a){if(this.gutter){a=this.adjustBoundsByGutter(a)
}var b={BBOX:a.toBBOX(),WIDTH:this.nativeTileSize.w,HEIGHT:this.nativeTileSize.h,TILED:true};
return decodeURIComponent(this.getFullRequestString(b))
},mergeNewParams:function(c){var b=OpenLayers.Util.upperCaseObject(c);
var a=[b];
Geoportal.Layer.Grid.prototype.mergeNewParams.apply(this,a)
},getFullRequestString:function(b){var a=this.getNativeProjection()||this.map.getProjection();
this.params.SRS=(a=="none")?null:a;
return Geoportal.Layer.Grid.prototype.getFullRequestString.apply(this,arguments)
},addTile:function(c,a,b){return new Geoportal.Tile.Image(this,a,c,null,b,this.tileOptions)
},getDataExtent:function(){return this.maxExtent
},changeBaseLayer:function(a){if(OpenLayers.Layer.prototype.changeBaseLayer.apply(this,arguments)===false){return false
}if(!this.isBaseLayer){if(this.getCompatibleProjection(a.layer)!=null){if(this.aggregate==undefined){this.displayInLayerSwitcher=true
}if(typeof(this.savedStates[a.layer.id])=="object"){if(this.savedStates[a.layer.id].opacity!=undefined){this.opacity=undefined;
this.setOpacity(this.savedStates[a.layer.id].opacity)
}this.setVisibility(this.savedStates[a.layer.id].visibility)
}else{this.setVisibility(this.getVisibility()&&this.calculateInRange())
}return true
}if(this.getCompatibleProjection(a.baseLayer)!=null){if(!this.savedStates[a.baseLayer.id]){this.savedStates[a.baseLayer.id]={}
}this.savedStates[a.baseLayer.id].visibility=this.getVisibility();
this.savedStates[a.baseLayer.id].opacity=this.opacity
}if(this.aggregate==undefined){this.displayInLayerSwitcher=false
}this.setVisibility(false)
}return true
},getCompatibleProjection:function(c){var b=OpenLayers.Layer.prototype.getCompatibleProjection.apply(this,arguments);
if(!b){return b
}c=c||this.map.baseLayer;
var d=c.getNativeProjection();
var a=this.restrictedExtent||this.maxExtent;
if(!a){return b
}if(!this.map){a=a.clone().transform(b,d)
}if(c.maxExtent.containsBounds(a,true,true)||a.containsBounds(c.maxExtent,true,true)){return b
}return null
},CLASS_NAME:"Geoportal.Layer.WMSC"});
Geoportal.Control.Logo=OpenLayers.Class(Geoportal.Control,{logoPrefix:"http://www.geoportail.fr/legendes/logo_",logoSuffix:".gif",logoSize:null,attributionDiv:null,separator:", ",_isSizeFixed:false,_listeLogos:null,_attributions:null,initialize:function(a){Geoportal.Control.prototype.initialize.apply(this,arguments);
this._isSizeFixed=this.logoSize!=null;
if(!this._isSizeFixed){this.logoSize=new OpenLayers.Size(Geoportal.Control.Logo.WHSizes.normal,Geoportal.Control.Logo.WHSizes.normal)
}else{if(typeof(this.logoSize)=="number"){this.logoSize=new OpenLayers.Size(this.logoSize,this.logoSize)
}}this._listeLogos=[];
this._attributions=[]
},destroy:function(){var b=this.div.childNodes;
for(var a=0;
a<b.length;
a++){this.div.removeChild(b[a])
}this._listeLogos=null;
this._attributions=null;
this.logoSize=null;
this._isSizeFixed=false;
this.map.events.unregister("addlayer",this,this.redraw);
this.map.events.unregister("changelayer",this,this.redraw);
this.map.events.unregister("removelayer",this,this.redraw);
this.map.events.unregister("zoomend",this,this.redraw);
this.map.events.unregister("move",this,this.redraw);
this.map.events.unregister("changebaselayer",this,this.changeBaseLayer);
this.map.events.unregister("controlvisibilitychanged",this,this.onVisibilityChanged);
Geoportal.Control.prototype.destroy.apply(this,arguments)
},draw:function(a){Geoportal.Control.prototype.draw.apply(this,arguments);
return this.div
},redraw:function(){var m,d;
var n=this.div.childNodes;
for(m=0,d=n.length;
m<d;
m++){n[m].style.display="none"
}if(this.attributionDiv){this.attributionDiv.innerHTML=""
}var h=this.map.layers;
var r=this.map.getZoom();
var c;
this._attributions=[];
for(m=0,d=h.length;
m<d;
m++){if(!h[m].getVisibility()||!h[m].inRange){continue
}if(h[m].originators!=null){var p;
for(var f=0,g=h[m].originators.length;
f<g;
f++){p=true;
var c=h[m].originators[f];
if(c.minZoomLevel&&(c.minZoomLevel>r)){p=false
}if(p&&c.maxZoomLevel&&(c.maxZoomLevel<r)){p=false
}if(p&&c.extent){var b=this.map.calculateBounds();
if(b){if(!(OpenLayers.Util.isArray(c.extent))){c.extent=[c.extent]
}var o=false;
for(var e=0,a=c.extent.length;
e<a;
e++){if(b.intersectsBounds(c.extent[e])){o=true;
break
}}p=o
}}if(p){if(!c.logo){c.logo=c.url||"#"
}var q=c.attribution||h[m].attribution||"&copy; "+c.logo.toUpperCase();
this._ajoutLogo(c.logo,c.url,c.pictureUrl,q)
}}}}},_ajoutLogo:function(f,c,e,b){if(this._listeLogos[f]==null){var a=this.div.ownerDocument.createElement("div");
this.div.appendChild(a);
this._listeLogos[f]=a;
var g=OpenLayers.Util.createImage(null,null,this.logoSize,e?e:this.logoPrefix+f+this.logoSuffix,null,null,null,false);
if(c!=null){var d=this.div.ownerDocument.createElement("a");
if(c.match(/^javascript:/)){d.setAttribute("href","#");
d.setAttribute("onclick",c)
}else{d.setAttribute("href",c);
d.setAttribute("target","_blank")
}d.appendChild(g);
a.appendChild(d)
}else{a.appendChild(g)
}}else{this._listeLogos[f].style.display=""
}if(this.attributionDiv){this._attributions.push(b);
this.attributionDiv.innerHTML=this._attributions.join(this.separator)
}},setMap:function(){Geoportal.Control.prototype.setMap.apply(this,arguments);
this.map.events.register("addlayer",this,this.redraw);
this.map.events.register("changelayer",this,this.redraw);
this.map.events.register("removelayer",this,this.redraw);
this.map.events.register("zoomend",this,this.redraw);
this.map.events.register("move",this,this.redraw);
this.map.events.register("changebaselayer",this,this.changeBaseLayer);
this.map.events.register("controlvisibilitychanged",this,this.onVisibilityChanged)
},changeLogoSize:function(l){if(this._isFixedSize){return
}var h=null;
if(typeof(l)=="number"){h=new OpenLayers.Size(l,l)
}else{h=l.clone()
}this.logoSize=h;
var d=this.map.layers;
for(var e=0,k=d.length;
e<k;
e++){var g=d[e];
if(g.originators!=null){for(var b=0,f=g.originators.length;
b<f;
b++){var a=g.originators[b];
if(this._listeLogos[a.logo]!=null){var c=this._listeLogos[a.logo].firstChild;
if(c){if(c.firstChild){c=c.firstChild
}if(c){c.style.width=this.logoSize.w+"px";
c.style.height=this.logoSize.h+"px"
}}}}}}h=null
},changeBaseLayer:function(o){if(!o){return
}if(!(o.layer)){return
}if(!(o.baseLayer)){return
}var m=o.baseLayer.getNativeProjection();
var n=o.layer.getNativeProjection();
var g=this.map.layers;
var b;
for(var h=0,c=g.length;
h<c;
h++){if(g[h].originators!=null){for(var e=0,f=g[h].originators.length;
e<f;
e++){var b=g[h].originators[e];
if(b.extent){if(!(OpenLayers.Util.isArray(b.extent))){b.extent=[b.extent]
}for(var d=0,a=b.extent.length;
d<a;
d++){b.extent[d].transform(m,n,true)
}}}}}},onVisibilityChanged:function(d){if(!d||!d.size){return
}var c=(d.visibility?1:-1);
var a=Geoportal.Util.getComputedStyle(this.div,"bottom",true);
a=a+c*d.size.h;
this.div.style.bottom=a+"px"
},CLASS_NAME:"Geoportal.Control.Logo"});
Geoportal.Control.Logo.WHSizes={normal:50,mini:30};
Geoportal.Control.PermanentLogo=OpenLayers.Class(Geoportal.Control,{permaLogo:null,permaURL:null,initialize:function(a){Geoportal.Control.prototype.initialize.apply(this,arguments);
if(!this.permaLogo){this.permaLogo=Geoportal.Util.getImagesLocation()+"logo_gp.gif"
}if(!this.permaURL){this.permaURL="http://www.geoportail.fr/"
}},destroy:function(){this.map.events.unregister("changebaselayer",this,this.changeBaseLayer);
this.map.events.unregister("preaddlayer",this,this.onGeoportalLayer);
this.map.events.unregister("changelayer",this,this.changeBaseLayer);
Geoportal.Control.prototype.destroy.apply(this,arguments)
},draw:function(b){Geoportal.Control.prototype.draw.apply(this,arguments);
var c=OpenLayers.Util.createImage(null,null,null,this.permaLogo,null,null,null,false);
if(this.permaURL!=null){var a=OpenLayers.getDoc().createElement("a");
a.setAttribute("href",this.permaURL);
a.setAttribute("target","_blank");
a.appendChild(c);
this.div.appendChild(a)
}else{this.div.appendChild(c)
}if(this.hasGeoportalLayers()){this.div.style.display="block"
}else{this.div.style.display="none"
}return this.div
},onGeoportalLayer:function(a){if(!(this.div.style.display=="none")){return
}if(!a){return
}if(!a.layer){return
}var b=a.layer;
if(!b.visibility){return
}if(b.GeoRM){this.div.style.display="block"
}},hasGeoportalLayers:function(){if(!this.map){return false
}for(var c=0,a=this.map.layers.length;
c<a;
c++){var b=this.map.layers[c];
if(!b.visibility){continue
}if(b.GeoRM){return true
}}return(this.map.layers.length==0)
},setMap:function(){Geoportal.Control.prototype.setMap.apply(this,arguments);
this.map.events.register("preaddlayer",this,this.onGeoportalLayer);
this.map.events.register("changebaselayer",this,this.changeBaseLayer);
this.map.events.register("changelayer",this,this.changeBaseLayer);
this.map.events.register("controlvisibilitychanged",this,this.onVisibilityChanged)
},changeBaseLayer:function(a){if(!a){return
}if(a.type=="changelayer"&&a.property!="visibility"){return
}if(this.hasGeoportalLayers()){this.div.style.display="block"
}else{this.div.style.display="none"
}},onVisibilityChanged:function(d){if(!d||!d.size){return
}var c=(d.visibility?1:-1);
var a=this.bottom;
if(!a){a=(d.visibility?Geoportal.Util.getComputedStyle(this.div,"bottom",true):0)
}this.bottom=a+c*d.size.h;
this.div.style.bottom=this.bottom+"px"
},CLASS_NAME:"Geoportal.Control.PermanentLogo"});
Geoportal.Layer.WMS=OpenLayers.Class(OpenLayers.Layer.WMS,{CLASS_NAME:"Geoportal.Layer.WMS"});
Geoportal.Catalogue=function(b,a){if(b){this.map=b
}this.setKeys(a)
};
Geoportal.Catalogue.prototype={map:null,urlServices:{},destroy:function(){if(this.map){this.map=null
}if(this.apiKey){var b;
for(var c=0,a=this.apiKey.length;
c<a;
c++){b=this.apiKey[c];
if(this[b]){this[b]=null
}}this.apiKey=null
}if(this.services){this.services=null
}},_orderLayersStack:function(c){var h=[];
var l;
var f,d=c.length;
var m=new RegExp(/^([^:]+)(:(.+))?$/);
for(f=0;
f<d;
f++){var e=c[f].match(m);
if(e==null){continue
}var j=e[1];
if(j==null){continue
}var b=e[3]||"WMSC";
var g=0;
var a=Geoportal.Catalogue.LAYERNAMES[j];
if(a){g=a.weight||0
}l={layerId:j+":"+b,weight:g};
h.unshift(l)
}h.sort(function(n,i){return i.weight-n.weight
});
d=h.length;
var k=[];
for(f=0;
f<d;
f++){l=h.shift();
k[f]=l.layerId
}return k
},getTerritory:function(d){if(d==undefined){if(this.map){d=this.map.territory||"FXX"
}else{d="FXX"
}}if(Geoportal.Catalogue.TERRITORIES[d]==undefined){if(this.map){d=this.map.territory||"FXX"
}else{d="FXX"
}}for(var b=0,a=Geoportal.Catalogue.TERRITORIES[d].defaultCRS.length;
b<a;
b++){var c=Geoportal.Catalogue.TERRITORIES[d].defaultCRS[b];
if(typeof(c)=="string"){Geoportal.Catalogue.TERRITORIES[d].defaultCRS[b]=new OpenLayers.Projection(c,{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[d].geobbox)})
}}if(typeof(Geoportal.Catalogue.TERRITORIES[d].geoCRS[0])=="string"){Geoportal.Catalogue.TERRITORIES[d].geoCRS[0]=new OpenLayers.Projection(Geoportal.Catalogue.TERRITORIES[d].geoCRS[0],{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[d].geobbox)})
}return d
},findTerritory:function(d){for(var c in Geoportal.Catalogue.TERRITORIES){if(Geoportal.Catalogue.TERRITORIES.hasOwnProperty(c)){var a=Geoportal.Catalogue.TERRITORIES[c];
if(c=="WLD"){continue
}if(!a.geobbox){continue
}var e=OpenLayers.Bounds.fromArray(a.geobbox);
var b=e.containsLonLat(d);
e=null;
if(b){return c
}}}return"WLD"
},getNativeProjection:function(b,a){if(!a){a=Geoportal.Catalogue.TERRITORIES[b].defaultCRS[0]
}if(typeof(a)=="string"){a=new OpenLayers.Projection(a,{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[b].geobbox)})
}return a
},getDisplayProjections:function(j,k,a){if(j==undefined){if(this.map){j=this.map.territory||"FXX"
}else{j="FXX"
}}var l=[];
if(!k){if(!a){l.push(Geoportal.Catalogue.TERRITORIES[j].displayCRS[0])
}else{l=Geoportal.Catalogue.TERRITORIES[j].displayCRS.slice(0)
}}else{l.push(k)
}var b=[];
for(var d=0,f=l.length;
d<f;
d++){var h=l[d];
if(typeof(h)=="string"){try{h=new OpenLayers.Projection(h,{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[j].geobbox)});
b.push(h)
}catch(g){}}else{b.push(h.clone())
}}return b
},getResolutions:function(e,c){var b=null;
var d,a;
if(c.getProjName()!="longlat"){b=[];
for(d=0,a=Geoportal.Catalogue.RESOLUTIONS.length;
d<a;
d++){b[d]=Geoportal.Catalogue.RESOLUTIONS[d]
}return b
}if(c.getProjName()=="longlat"){b=[];
for(d=0,a=Geoportal.Catalogue.RESOLUTIONS.length;
d<a;
d++){var f=new OpenLayers.LonLat(Geoportal.Catalogue.RESOLUTIONS[d],0);
f.transform(Geoportal.Catalogue.TERRITORIES[e].defaultCRS[0],c);
b[d]=f.lon
}return b
}return b
},getCenter:function(c,b){var a=new OpenLayers.LonLat(Geoportal.Catalogue.TERRITORIES[c].geocenter[0],Geoportal.Catalogue.TERRITORIES[c].geocenter[1]);
if(b&&typeof(b)!="string"){a.transform(Geoportal.Catalogue.TERRITORIES[c].geoCRS[0],b)
}return a
},getExtent:function(e,c){var f=null;
if(!e){if(this.apiKey){var b;
for(var d=0,a=this.apiKey.length;
d<a;
d++){b=this.apiKey[d];
if(this[b]&&this[b].bounds){if(!f){f=this[b].bounds.clone()
}else{f.extend(this[b].bounds.clone())
}}}}if(f==null){f=new OpenLayers.Bounds(-180,-90,180,90)
}}else{f=OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[e].geobbox)
}if(c&&typeof(c)!="string"){f.transform(e?Geoportal.Catalogue.TERRITORIES[e].geoCRS[0]:OpenLayers.Projection.CRS84,c,true)
}return f
},getDefaultMinZoom:function(c,a){if(!a){a=this.getNativeProjection(c)
}var d=Geoportal.Catalogue.RESOLUTIONS.length-1;
for(var b in Geoportal.Catalogue.TERRITORIES[c].baseLayers){if(Geoportal.Catalogue.TERRITORIES[c].baseLayers.hasOwnProperty(b)){if(a.isCompatibleWith(b)){if(Geoportal.Catalogue.TERRITORIES[c].baseLayers[b].minZoomLevel<d){d=Geoportal.Catalogue.TERRITORIES[c].baseLayers[b].minZoomLevel
}break
}}}return d==Geoportal.Catalogue.RESOLUTIONS.length-1?0:d
},getDefaultMaxZoom:function(d,b){if(!b){b=this.getNativeProjection(d)
}var a=0;
for(var c in Geoportal.Catalogue.TERRITORIES[d].baseLayers){if(Geoportal.Catalogue.TERRITORIES[d].baseLayers.hasOwnProperty(c)){if(b.isCompatibleWith(c)){if(Geoportal.Catalogue.TERRITORIES[d].baseLayers[c].maxZoomLevel>a){a=Geoportal.Catalogue.TERRITORIES[d].baseLayers[c].maxZoomLevel
}break
}}}return a==0?Geoportal.Catalogue.RESOLUTIONS.length-1:a
},getDefaultZoom:function(c,a){if(!a){a=this.getNativeProjection(c)
}for(var b in Geoportal.Catalogue.TERRITORIES[c].baseLayers){if(Geoportal.Catalogue.TERRITORIES[c].baseLayers.hasOwnProperty(b)){if(a.isCompatibleWith(b)){return Geoportal.Catalogue.TERRITORIES[c].baseLayers[b].defaultZoomLevel
}}}return 5
},setKeys:function(p){if(!p.apiKey){return
}var f={};
for(var o in Geoportal.Catalogue.LAYERNAMES){if(Geoportal.Catalogue.LAYERNAMES[o]&&Geoportal.Catalogue.LAYERNAMES[o].deprecated){f[o+":WMSC"]=Geoportal.Catalogue.LAYERNAMES[o].deprecated+":WMSC"
}}if(!(OpenLayers.Util.isArray(p.apiKey))){p.apiKey=[p.apiKey]
}var c;
for(var e=0,b=p.apiKey.length;
e<b;
e++){c=p.apiKey[e];
if(!c){continue
}if(p[c]!=null){this[c]={tokenServer:p[c].tokenServer,geoRMKey:c,tokenTimeOut:p[c].tokenTimeOut,transport:p[c].transport||p.transport||"json",bounds:p[c].bounds?OpenLayers.Bounds.fromArray(p[c].bounds):new OpenLayers.Bounds(-180,-90,180,90),layers:p[c].resources,allowedGeoportalLayers:p[c].allowedGeoportalLayers};
if(this[c].transport=="referer"){this[c].transport="referrer";
this[c].referrer=p.referrer||"http://localhost/"
}for(var o in f){var g=this[c].layers[o];
if(g){var a=OpenLayers.Util.extend(g,{name:(f[o].split(":"))[0]});
this[c].layers[f[o]]=a;
delete this[c].layers[o];
for(var d=0,h=this[c].allowedGeoportalLayers.length;
d<h;
d++){if(this[c].allowedGeoportalLayers[d]===o){this[c].allowedGeoportalLayers[d]=f[o];
break
}}}}}else{this[c]={tokenServer:"http://localhost/",geoRMKey:c,tokenTimeOut:60000,layers:{},allowedGeoportalLayers:[]}
}}this.apiKey=p.apiKey.slice(0);
if(p.services){this.services={};
for(var e=0,b=this.apiKey.length;
e<b;
e++){var c=this.apiKey[e];
if(!c){continue
}for(var m in this[c].resources){var n=this[c].resources[m].url;
if(p.services[n]&&!this.services[n]){this.services[n]=p.services[n]
}}}}},getAllowedGeoportalLayers:function(c){var e=[];
if(!c){return e
}if(typeof(c)=="string"){c=[c]
}var b=0,a=c.length;
for(;
b<a;
b++){e=e.concat(this[c[b]].allowedGeoportalLayers)
}if(a==1){return e
}var d={};
for(b=0,a=e.length;
b<a;
b++){d[e[b]]=0
}e=[];
for(b in d){e.push(b)
}return e
},getLayerGeoRMKey:function(e,b){if(this.apiKey){var c;
for(var d=0,a=this.apiKey.length;
d<a;
d++){c=this.apiKey[d];
if(this[c]&&(!e||(this[c].bounds&&this[c].bounds.intersectsBounds(this.getExtent(e),true)))){for(var f in this[c].layers){if(f==b||f.match("^"+b+":")){return c
}}}}}return null
},getLayerParameters:function(a,p){if(!Geoportal.Catalogue.TERRITORIES[a]){return null
}if(!p){return null
}var t=p.split(":");
if(t.length==0||t[0].length==0){return null
}if(t.length==1){t.push("WMSC")
}var C=t.pop(),d=t.join(":");
if(Geoportal.Catalogue.LAYERNAMES[d]&&Geoportal.Catalogue.LAYERNAMES[d].deprecated){d=Geoportal.Catalogue.LAYERNAMES[d].deprecated
}var A=[];
if(this.apiKey){var v;
for(var x=0,u=this.apiKey.length;
x<u;
x++){v=this.apiKey[x];
if(this[v]){for(var n in this[v].layers){if(n.match("^"+d+":"+C+"$")){A.push(OpenLayers.Util.extend({},this[v].layers[n]))
}}}}}if(A.length==0){return null
}var f=null;
for(x=0,u=A.length;
x<u;
x++){if(C==A[x].type){f=A[x];
break
}}if(f==null){return null
}var B=Geoportal.Catalogue.LAYERNAMES[f.name];
if(!B){return null
}var o=B.key;
if(!Geoportal.Catalogue.CONFIG[o]){return null
}var q=Geoportal.Catalogue.CONFIG[o][a];
if(!q){return null
}var m={resourceId:f.name+":"+f.type,url:f.url,params:{layers:null,exceptions:"text/xml"},options:{isBaseLayer:false,description:f.name+".description",visibility:false,opacity:1,view:{drop:false,zoomToExtent:false}}};
var r=Geoportal.Catalogue.TERRITORIES[a].defaultCRS.slice(0);
switch(f.type){case"WMS":m.classLayer=Geoportal.Layer.WMS;
m.params=OpenLayers.Util.extend(m.params,{format:"image/png",transparent:true});
m.options=OpenLayers.Util.extend(m.options,{buffer:1,singleTile:true});
r=r.slice(1);
break;
case"WFS":m.classLayer=Geoportal.Layer.WFS;
m.options=OpenLayers.Util.extend(m.options,{});
r=r.slice(1);
break;
case"OPENLS":return null;
default:m.classLayer=Geoportal.Layer.WMSC;
m.options=OpenLayers.Util.extend(m.options,{buffer:1,tileOrigin:new OpenLayers.LonLat(0,0),nativeTileSize:new OpenLayers.Size(256,256),singleTile:false});
break
}if(r.length==0){return null
}m.options.visibility=Geoportal.Catalogue.CONFIG[o].serviceParams.options.visibility||false;
if(Geoportal.Catalogue.CONFIG[o].serviceParams[f.type]){m.params.format=Geoportal.Catalogue.CONFIG[o].serviceParams[f.type].format;
if(Geoportal.Catalogue.CONFIG[o].serviceParams[f.type].transparent){m.params.transparent=true
}}var b={};
b.opacity=q.opacity||Geoportal.Catalogue.CONFIG[o].layerOptions.opacity;
b.originators=[];
for(var x=0,u=q.originators.length;
x<u;
x++){var g=q.originators[x];
b.originators.push(Geoportal.Catalogue.getOriginator(g.id,g.mnzl,g.mxzl))
}b.minZoomLevel=q.minZoomLevel;
b.maxZoomLevel=q.maxZoomLevel;
if(q.bounds){b.maxExtent=OpenLayers.Bounds.fromArray(q.bounds)
}if(q.fileIdentifiers&&q.fileIdentifiers.length>0){b.metadataURL=[];
for(var x=0,u=q.fileIdentifiers.length;
x<u;
x++){var s=q.fileIdentifiers[x];
if(!s.match(/^http:/)){s=Geoportal.Catalogue.CATBASEURL+s
}b.metadataURL.push(s)
}}if(q.dataURL&&q.dataURL.length>0){b.dataURL=[];
for(var x=0,u=q.dataURL.length;
x<u;
x++){var h=q.dataURL[x];
b.dataURL.push(h)
}}if(m){b.name=f.name;
b.projection=null;
for(var x=0,z=r.length;
x<z;
x++){if(typeof(r[x])=="string"){r[x]=new OpenLayers.Projection(r[x],{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[a].geobbox)})
}else{r[x]=new OpenLayers.Projection(r[x].getCode(),{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[a].geobbox)})
}if(this.map&&r[x].equals(this.map.getProjection())){b.projection=new OpenLayers.Projection(r[x].getCode(),{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[a].geobbox)})
}}if(f.type=="WMS"){b.srs={};
for(var x=0,z=r.length;
x<z;
x++){var c=r[x].clone();
b.srs[c]=true
}}if(!b.projection){b.projection=new OpenLayers.Projection(r[0].getCode(),{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[a].geobbox)})
}m.params.layers=f.name;
if(m.classLayer==Geoportal.Layer.WMSC){b.nativeResolutions=Geoportal.Catalogue.RESOLUTIONS.slice(0)
}if(b.maxExtent==undefined){b.maxExtent=this.getExtent(a)
}b.maxExtent.transform(Geoportal.Catalogue.TERRITORIES[a].geoCRS[0],b.projection,true);
if(b.originators){for(var x=0,u=b.originators.length;
x<u;
x++){var e=b.originators[x];
if(e.extent){if(!(OpenLayers.Util.isArray(e.extent))){e.extent=[e.extent]
}for(var w=0,y=e.extent.length;
w<y;
w++){e.extent[w].transform(Geoportal.Catalogue.TERRITORIES[a].geoCRS[0],b.projection,true)
}}}}OpenLayers.Util.extend(m.options,b)
}return m
},CLASS_NAME:"Geoportal.Catalogue"};
Geoportal=Geoportal||{};
Geoportal.Catalogue=Geoportal.Catalogue||{};
Geoportal.Catalogue.RESOLUTIONS=[39135.75,19567.875,9783.9375,4891.96875,2445.984375,2048,1024,512,256,128,64,32,16,8,4,2,1,0.5,0.25,0.125,0.0625];
Geoportal.Catalogue.TERRITORIES={ASP:{geobbox:[76,-40,79,-36],geocenter:[77.571944,-37.796389],defaultCRS:["IGNF:GEOPORTALASP","IGNF:UTM43SW84"],geoCRS:["IGNF:WGS84G"],displayCRS:["CRS:84","IGNF:UTM43SW84"],baseLayers:{"IGNF:GEOPORTALASP":{minZoomLevel:5,maxZoomLevel:15,defaultZoomLevel:13},"EPSG:310642813":{minZoomLevel:5,maxZoomLevel:15,defaultZoomLevel:13},"IGNF:UTM43SW84":{minZoomLevel:5,maxZoomLevel:15,defaultZoomLevel:13}}},ATF:{geobbox:[132.56,-68.62,144.54,-64.03],geocenter:[140.001389,-66.66278],defaultCRS:["IGNF:TERA50STEREO"],geoCRS:["IGNF:WGS84G"],displayCRS:["CRS:84","IGNF:TERA50STEREO"],baseLayers:{"IGNF:TERA50STEREO":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:10},"EPSG:2986":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:10}}},CHE:{},CRZ:{geobbox:[47,-48,55,-44],geocenter:[51.866667,-46.433333],defaultCRS:["IGNF:GEOPORTALCRZ","IGNF:UTM39SW84"],geoCRS:["IGNF:WGS84G"],displayCRS:["CRS:84","IGNF:UTM39SW84"],baseLayers:{"IGNF:GEOPORTALCRZ":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:11},"EPSG:310642801":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:11},"IGNF:UTM39SW84":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:11}}},FXX:{geobbox:[-31.17,27.33,69.03,80.83],geocenter:[2.345274398,48.860832558],defaultCRS:["IGNF:GEOPORTALFXX","IGNF:LAMB93"],geoCRS:["IGNF:RGF93G"],displayCRS:["IGNF:RGF93G","IGNF:ETRS89GEO","IGNF:LAMB93","IGNF:ETRS89LCC","IGNF:ETRS89LAEA"],baseLayers:{"IGNF:GEOPORTALFXX":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:5},"EPSG:310024802":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:5},"IGNF:LAMB93":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:5}}},GLP:{geobbox:[-63.2,15.75,-60,17.5],geocenter:[-61.732777778,15.996111111],defaultCRS:["IGNF:GEOPORTALANF","IGNF:UTM20W84GUAD"],geoCRS:["IGNF:WGS84RRAFGEO"],displayCRS:["IGNF:WGS84RRAFGEO","IGNF:UTM20W84GUAD"],baseLayers:{"IGNF:GEOPORTALANF":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13},"EPSG:310915814":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13},"IGNF:UTM20W84GUAD":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13}}},GUF:{geobbox:[-62.1,-4.3,-46,11.5],geocenter:[-52.305277778,4.932222222],defaultCRS:["IGNF:GEOPORTALGUF","IGNF:UTM22RGFG95"],geoCRS:["IGNF:RGFG95GEO"],displayCRS:["IGNF:RGFG95GEO","IGNF:UTM22RGFG95"],baseLayers:{"IGNF:GEOPORTALGUF":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:10},"EPSG:310486805":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:10},"IGNF:UTM22RGFG95":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:10}}},KER:{geobbox:[62,-53,76,-45],geocenter:[70.215278,-49.354167],defaultCRS:["IGNF:GEOPORTALKER","IGNF:UTM42SW84"],geoCRS:["IGNF:WGS84G"],displayCRS:["IGNF:WGS84G","IGNF:UTM42SW84"],baseLayers:{"IGNF:GEOPORTALKER":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:11},"EPSG:310642812":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:11},"IGNF:UTM42SW84":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:11}}},MTQ:{geobbox:[-64,11.7,-59,15.7],geocenter:[-61.075,14.6],defaultCRS:["IGNF:GEOPORTALANF","IGNF:UTM20W84MART"],geoCRS:["IGNF:WGS84RRAFGEO"],displayCRS:["IGNF:WGS84RRAFGEO","IGNF:UTM20W84MART"],baseLayers:{"IGNF:GEOPORTALANF":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13},"EPSG:310915814":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13},"IGNF:UTM20W84MART":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13}}},MYT:{geobbox:[40,-17.5,56,3],geocenter:[45.228333333,-12.781666667],defaultCRS:["IGNF:GEOPORTALMYT","IGNF:RGM04UTM38S"],geoCRS:["IGNF:RGM04GEO"],displayCRS:["IGNF:RGM04GEO","IGNF:RGM04UTM38S"],baseLayers:{"IGNF:GEOPORTALMYT":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:14},"EPSG:310702807":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:14},"IGNF:RGM04UTM38S":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:14}}},NCL:{geobbox:[160,-24.3,170,-17.1],geocenter:[166.433333,-22.283333],defaultCRS:["IGNF:GEOPORTALNCL","IGNF:RGNCUTM57S","IGNF:RGNCUTM58S","IGNF:RGNCUTM59S"],geoCRS:["IGNF:RGNCGEO"],displayCRS:["IGNF:RGNCGEO","IGNF:RGNCUTM57S","IGNF:RGNCUTM58S","IGNF:RGNCUTM59S"],baseLayers:{"IGNF:GEOPORTALNCL":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:9},"EPSG:310547809":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:9},"IGNF:RGNCUTM57S":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:9},"IGNF:RGNCUTM58S":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:9},"IGNF:RGNCUTM59S":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:9}}},PYF:{geobbox:[-160,-28.2,-108,11],geocenter:[-149.569444,-17.536111],defaultCRS:["IGNF:GEOPORTALPYF","IGNF:RGPFUTM5S","IGNF:RGPFUTM6S","IGNF:RGPFUTM7S"],geoCRS:["IGNF:RGPFGEO"],displayCRS:["IGNF:RGPFGEO","IGNF:RGPFUTM5S","IGNF:RGPFUTM6S","IGNF:RGPFUTM7S"],baseLayers:{"IGNF:GEOPORTALPYF":{minZoomLevel:5,maxZoomLevel:11,defaultZoomLevel:11},"EPSG:310032811":{minZoomLevel:5,maxZoomLevel:11,defaultZoomLevel:11},"IGNF:RGPFUTM5S":{minZoomLevel:5,maxZoomLevel:11,defaultZoomLevel:11},"IGNF:RGPFUTM6S":{minZoomLevel:5,maxZoomLevel:11,defaultZoomLevel:11},"IGNF:RGPFUTM7S":{minZoomLevel:5,maxZoomLevel:11,defaultZoomLevel:11}}},REU:{geobbox:[37.5,-26.2,60,-17.75],geocenter:[55.466666667,-20.875],defaultCRS:["IGNF:GEOPORTALREU","IGNF:RGR92UTM40S"],geoCRS:["IGNF:RGR92GEO"],displayCRS:["IGNF:RGR92GEO","IGNF:RGR92UTM40S"],baseLayers:{"IGNF:GEOPORTALREU":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13},"EPSG:310700806":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13},"IGNF:RGR92UTM40S":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:13}}},SPM:{geobbox:[-60,43.5,-50,52],geocenter:[-56.173611,46.780556],defaultCRS:["IGNF:GEOPORTALSPM","IGNF:RGSPM06U21"],geoCRS:["IGNF:RGSPM06GEO"],displayCRS:["IGNF:RGSPM06GEO","IGNF:RGSPM06U21"],baseLayers:{"IGNF:GEOPORTALSPM":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:14},"EPSG:310706808":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:14},"IGNF:RGSPM06U21":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:14}}},WLD:{geobbox:[-180,-90,180,90],geocenter:[0,0],defaultCRS:["IGNF:MILLER"],geoCRS:[OpenLayers.Projection.CRS84],displayCRS:[OpenLayers.Projection.CRS84],baseLayers:{"IGNF:MILLER":{minZoomLevel:0,maxZoomLevel:4,defaultZoomLevel:0},"EPSG:310642901":{minZoomLevel:0,maxZoomLevel:4,defaultZoomLevel:0}}},WLF:{geobbox:[-178.5,-14.6,-175.8,-12.8],geocenter:[-176.173611,-13.283333],defaultCRS:["IGNF:GEOPORTALWLF","IGNF:UTM01SW84"],geoCRS:["IGNF:WGS84G"],displayCRS:["CRS:84","IGNF:UTM01SW84"],baseLayers:{"IGNF:GEOPORTALWLF":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:14},"EPSG:310642810":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:14},"IGNF:UTM01SW84":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:14}}}};
Geoportal.Catalogue.TERRITORIES.EUE=OpenLayers.Util.extend({},Geoportal.Catalogue.TERRITORIES.FXX);
Geoportal.Catalogue.TERRITORIES.SBA=OpenLayers.Util.extend({},Geoportal.Catalogue.TERRITORIES.GLP);
Geoportal.Catalogue.TERRITORIES.SBA.geobbox=[-63,17.75,-62.7,17.99];
Geoportal.Catalogue.TERRITORIES.SBA.geocenter=[-62.85,17.895833];
Geoportal.Catalogue.TERRITORIES.SMA=OpenLayers.Util.extend({},Geoportal.Catalogue.TERRITORIES.GLP);
Geoportal.Catalogue.TERRITORIES.SMA.geobbox=[-63.19,18,-62.9,18.18];
Geoportal.Catalogue.TERRITORIES.SMA.geocenter=[-63.088888,18.069722];
Geoportal.Catalogue.TERRITORIES.ANF=OpenLayers.Util.extend({},Geoportal.Catalogue.TERRITORIES.GLP);
Geoportal.Catalogue.TERRITORIES.ANF.geobbox=[-64,11.7,-59,18.18];
Geoportal.Catalogue.ORIGINATORS={asp:{url:"http://www.asp-public.fr/"},BNF:{url:"http://www.bnf.fr/"},cartosphere:{url:"http://www.esrifrance.fr/FranceRaster.asp"},cnes:{url:"http://www.cnes.fr"},cnrs:{url:"http://www.cnrs.fr"},DIRENHAUTENORMANDIE:{url:"http://www.haute-normandie.ecologie.gouv.fr/"},DIRENIDF:{url:"http://www.ile-de-france.ecologie.gouv.fr/"},div:{url:"http://www.ville.gouv.fr/",attribution:"CNV"},EHESS:{url:"http://www.ehess.fr/ldh/Themes/Theme_Cassini.htm"},eurogeographics:{url:"http://www.eurogeographics.org/",attribution:"EuroGeographics"},eea:{url:"http://www.stats.environnement.developpement-durable.gouv.fr/",attribution:"SOeS Environnement"},ird:{url:"http://www.cayenne.ird.fr/"},maaprat:{url:"http://agriculture.gouv.fr/"},meeddtl:{url:"http://www.developpement-durable.gouv.fr/"},"partenaires-bdortho":{url:"http://www.ign.fr/institut/72/partenaires/partenaires-institutionnels-de-l-ign.htm",attribution:" ",bounds:[[5.80542659759521,45.6817817687988,7.04488706588745,46.4081611633301],[2.38789987564087,45.2870712280273,3.98563718795776,46.2565994262695],[6.63532686233521,43.480052947998,7.71883344650269,44.3610534667969]]},planetobserver:{url:"http://www.planetobserver.com",attribution:"PlanetObserver"},SEINEENPARTAGE:{url:"http://www.seineenpartage.fr/"},seasguyane:{url:"http://www.seas-guyane.org/",attribution:"SAES Guyane"},shom:{url:"http://www.shom.fr"},sitg:{url:"http://etat.geneve.ch/sitg/accueil.html"},spotimage:{url:"http://www.spotimage.fr",attribution:"SPOT IMAGE"},ign:{url:"http://www.ign.fr",attribution:"Institut national de l'information géographique et forestière"}};
Geoportal.Catalogue.getOriginator=function(d,c,g){if(!d){d="ign"
}var f=Geoportal.Catalogue.ORIGINATORS[d];
if(!f){d="ign";
f=Geoportal.Catalogue.ORIGINATORS[d]
}var e=OpenLayers.Util.extend({logo:d},f);
if(!e.attribution){e.attribution=d.toUpperCase()
}e.attribution=OpenLayers.String.trim(e.attribution);
if(e.attribution.length>0){e.attribution+="&copy; "+e.attribution
}if(!e.url){e.url="#"
}if(e.bounds){var a=e.bounds.length;
e.extent=new Array(a);
for(var b=0;
b<a;
b++){e.extent[b]=OpenLayers.Bounds.fromArray(e.bounds[b])
}delete e.bounds
}if(c&&g){e.minZoomLevel=c;
e.maxZoomLevel=g
}return e
};
Geoportal.Catalogue.CATBASEURL="http://www.geocatalogue.fr/Detail.do?fileIdentifier=";
Geoportal.Catalogue.PROFILES=["geoportail","inspire","edugeo"];
Geoportal.Catalogue.SERVICES={WMSC:{geoportail:["http://wxs.ign.fr/geoportail/wmsc"],inspire:["http://wxs.ign.fr/inspire/wmsc"],edugeo:["http://wxs.ign.fr/edugeo/wmsc"]},WMS:{geoportail:["http://wxs.ign.fr/geoportail/v/wms","http://wxs.ign.fr/geoportail/r/wms"],inspire:["http://wxs.ign.fr/inspire/v/wms","http://wxs.ign.fr/inspire/r/wms"]},WFS:{},OPENSL:{geoportail:["http://wxs.ign.fr/geoportail/ols","http://wxs.ign.fr/geoportail/gazetteer"]},CSW:{geoportail:["http://wxs.ign.fr/geoportail/csw/isoap"]}};
Geoportal.Catalogue.LAYERNAMES={"ORTHOIMAGERY.ORTHOPHOTOS":{key:"ortho",weight:999},"ORTHOIMAGERY.ORTHOPHOTOS2000-2005":{key:"orthov1",weight:998.99},"ORTHOIMAGERY.ORTHOPHOTOS.GENEVE":{key:"orthoSitg",weight:998.9},"GEOGRAPHICALGRIDSYSTEMS.MAPS":{key:"scanmap",weight:998},"GEOGRAPHICALGRIDSYSTEMS.COASTALMAPS":{key:"coastmap",weight:997.9},"GEOGRAPHICALGRIDSYSTEMS.FRANCERASTER":{key:"franceRaster",weight:997.89},"GEOGRAPHICALGRIDSYSTEMS.ETATMAJOR40":{key:"em40",weight:997.2},"GEOGRAPHICALGRIDSYSTEMS.1900TYPEMAPS":{key:"type1900",weight:997.19},"GEOGRAPHICALGRIDSYSTEMS.CASSINI":{key:"cassini",weight:997.1},"GEOGRAPHICALGRIDSYSTEMS.ADMINISTRATIVEUNITS":{key:"scanadm",weight:997.09},"ORTHOIMAGERY.ORTHOPHOTOS.COAST2000":{key:"orthoCoast2000",weight:997},"LANDUSE.AGRICULTURE2010":{key:"rpg2010",weight:996.201},"LANDUSE.AGRICULTURE2009":{key:"rpg2009",weight:996.2009},"LANDUSE.AGRICULTURE2008":{key:"rpg2008",weight:996.2008},"LANDUSE.AGRICULTURE2007":{key:"rpg2007",weight:996.2007},"LANDCOVER.FORESTINVENTORY.V2":{key:"forestV2",weight:996.2005},"LANDCOVER.FORESTINVENTORY.V1":{key:"forestV1",weight:996.1987},"LANDCOVER.CORINELANDCOVER":{key:"clc",weight:996},"ELEVATION.SLOPS":{key:"slopes",weight:989,deprecated:"ELEVATION.SLOPES"},"ELEVATION.SLOPES":{key:"slopes",weight:989},"CADASTRALPARCELS.PARCELS":{key:"bdparcel",weight:979},"CP.CadastralParcel":{key:"bdparcel",weight:979},"NATURALRISKZONES.1910FLOODEDWATERSHEDS":{key:"floodws1910",weight:969.2},"NATURALRISKZONES.1910FLOODEDCELLARS":{key:"floodcl1910",weight:969.1},"HYDROGRAPHY.HYDROGRAPHY":{key:"waterb",weight:969},"HY.PhysicalWaters.Waterbodies":{key:"waterb",weight:969},"TRANSPORTNETWORKS.ROADS":{key:"roadl",weight:899},"TN.RoadTransportNetwork.RoadLink":{key:"roadl",weight:899},"TRANSPORTNETWORKS.RAILWAYS":{key:"raill",weight:898},"TN.RailTransportNetwork.RailwayLink":{key:"raill",weight:898},"TRANSPORTNETWORKS.RUNWAYS":{key:"runwaya",weight:897},"TN.AirTransportNetwork.RunwayArea":{key:"runwaya",weight:897},"BUILDINGS.BUILDINGS":{key:"buildings",weight:799},"UTILITYANDGOVERNMENTALSERVICES.ALL":{key:"utility",weight:699},"ADMINISTRATIVEUNITS.BOUNDARIES":{key:"limadm",weight:599},"AU.AdministrativeBoundary":{key:"limadm",weight:599},"SEAREGIONS.LEVEL0":{key:"level0",weight:499,deprecated:"ELEVATION.LEVEL0"},"ELEVATION.LEVEL0":{key:"level0",weight:499},"GEOGRAPHICALNAMES.NAMES":{key:"toponyms",weight:498.9},"TOPONYMS.ALL":{key:"geonames",weight:-1},"ADDRESSES.CROSSINGS":{key:"routeadr",weight:-2}};
Geoportal.Catalogue.CONFIG={ortho:{serviceParams:{WMSC:{format:"image/jpeg"},options:{visibility:true}},layerOptions:{opacity:1},CRZ:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"planetobserver",mnzl:5,mxzl:12}],bounds:[49.154744,-47.097592,53.392222,-45.331433],fileIdentifiers:[]},FXX:{minZoomLevel:5,maxZoomLevel:18,originators:[{id:"planetobserver",mnzl:5,mxzl:11},{id:"ign",mnzl:12,mxzl:18},{id:"partenaires-bdortho",mnzl:12,mxzl:18}],bounds:[-41.052325,23.548796,82.104649,84.775666],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:18,originators:[{id:"planetobserver",mnzl:5,mxzl:11},{id:"ign",mnzl:12,mxzl:18},{id:"partenaires-bdortho",mnzl:12,mxzl:18}],bounds:[-41.052325,23.548796,82.104649,84.775666],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},ANF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[-63.38672,14.129278,-58.510819,18.839037],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[-63.38672,14.129278,-58.510819,18.839037],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[-63.38672,14.129278,-58.510819,18.839037],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[-63.38672,14.129278,-58.510819,18.839037],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:18,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:18}],bounds:[-61.558257,14.129278,-60.644026,15.012358],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[-66.09764,-4.709759,-42.49134,14.129278],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},KER:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"planetobserver",mnzl:5,mxzl:12}],bounds:[67.986951,-50.629912,71.612922,-48.275032],fileIdentifiers:[]},MYT:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[44.839483,-13.246198,45.441355,-12.363118],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},NCL:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"planetobserver",mnzl:5,mxzl:12}],bounds:[157.468808,-28.258555,172.707725,-14.129278],fileIdentifiers:["GL_PHOTO_NCL.xml"]},PYF:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"planetobserver",mnzl:5,mxzl:12}],bounds:[-160.904751,-28.258555,-107.269834,14.129278],fileIdentifiers:["GL_PHOTO_PYF.xml"]},REU:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[35.313845,-28.258555,60.53802,-14.129278],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},SPM:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[-62.152397,42.387833,-48.340754,56.517111],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]},WLD:{minZoomLevel:0,maxZoomLevel:4,originators:[{id:"planetobserver",mnzl:0,mxzl:4}],bounds:[-179.999961,-72.782842,179.999961,72.782842],fileIdentifiers:[]},WLF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"planetobserver",mnzl:5,mxzl:8},{id:"ign",mnzl:9,mxzl:17}],bounds:[-179.595856,-18.839037,-174.741914,-9.419518],fileIdentifiers:["IGNF_BDORTHOr_2-0.xml"]}},orthov1:{serviceParams:{WMSC:{format:"image/jpeg"},options:{}},layerOptions:{opacity:0.7},FXX:{minZoomLevel:9,maxZoomLevel:17,originators:[{id:"ign",mnzl:9,mxzl:17}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:[]},EUE:{minZoomLevel:9,maxZoomLevel:17,originators:[{id:"ign",mnzl:9,mxzl:17}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:[]}},orthoSitg:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:12,maxZoomLevel:18,originators:[{id:"sitg",mnzl:12,mxzl:18}],bounds:[5.940156,46.118968,6.312204,46.383872],fileIdentifiers:["http://etat.geneve.ch/geoportail/metadataws/Publish/4745.html"]},EUE:{minZoomLevel:12,maxZoomLevel:18,originators:[{id:"sitg",mnzl:12,mxzl:18}],bounds:[5.940156,46.118968,6.312204,46.383872],fileIdentifiers:["http://etat.geneve.ch/geoportail/metadataws/Publish/4745.html"]}},scanmap:{serviceParams:{WMSC:{format:"image/jpeg"},options:{visibility:true}},layerOptions:{opacity:0.3},ASP:{opacity:1,minZoomLevel:5,maxZoomLevel:15,originators:[{id:"ign",mnzl:5,mxzl:15}],bounds:[77.486775,-38.752073,77.602118,-37.788746],fileIdentifiers:["GL_CARTE_ASP.xml"]},ATF:{opacity:1,minZoomLevel:5,maxZoomLevel:13,originators:[{id:"ign",mnzl:5,mxzl:13}],bounds:[137.643312,-66.753143,147.850442,-61.861314],fileIdentifiers:["GL_CARTE_ATF.xml"]},CRZ:{minZoomLevel:5,maxZoomLevel:13,originators:[{id:"ign",mnzl:5,mxzl:13}],bounds:[49.154744,-47.097592,53.392222,-45.331433],fileIdentifiers:["GL_CARTE_CRZ.xml"]},FXX:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-20.526162,32.968315,27.368216,61.22687],fileIdentifiers:["GL_CARTE_FXX.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-20.526162,32.968315,27.368216,61.22687],fileIdentifiers:["GL_CARTE_FXX.xml"]},ANF:{opacity:0.3,minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.38672,14.129278,-58.510819,18.839037],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.38672,14.129278,-58.510819,18.839037],fileIdentifiers:["GL_CARTE_GLP.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.38672,14.129278,-58.510819,18.839037],fileIdentifiers:["GL_CARTE_SBA.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.38672,14.129278,-58.510819,18.839037],fileIdentifiers:["GL_CARTE_SMA.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-61.558257,14.129278,-60.644026,15.012358],fileIdentifiers:["GL_CARTE_MTQ.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-54.763718,1.610769,-51.150381,6.13809],fileIdentifiers:["GL_CARTE_GUF.xml"]},KER:{minZoomLevel:5,maxZoomLevel:13,originators:[{id:"ign",mnzl:5,mxzl:13}],bounds:[67.986951,-50.629912,71.612922,-48.275032],fileIdentifiers:["GL_CARTE_KER.xml"]},MYT:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"ign",mnzl:5,mxzl:15}],bounds:[44.839483,-13.246198,45.441355,-12.363118],fileIdentifiers:["GL_CARTE_MYT.xml"]},NCL:{minZoomLevel:5,maxZoomLevel:13,originators:[{id:"ign",mnzl:5,mxzl:13}],bounds:[162.548447,-23.548796,168.897996,-18.839037],fileIdentifiers:["GL_CARTE_NCL.xml"]},PYF:{minZoomLevel:5,maxZoomLevel:13,originators:[{id:"ign",mnzl:5,mxzl:13}],bounds:[-152.371924,-18.839037,-148.714997,-15.306718],fileIdentifiers:["GL_CARTE_PYF.xml"]},REU:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[54.862581,-21.488277,56.12379,20.605197],fileIdentifiers:["GL_CARTE_REU.xml"]},SPM:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-56.541417,46.508872,-56.109803,47.391952],fileIdentifiers:["GL_CARTE_SPM.xml"]},WLD:{minZoomLevel:0,maxZoomLevel:4,originators:[{id:"ign",mnzl:0,mxzl:4}],bounds:[-179.999961,-72.782842,179.999961,72.782842],fileIdentifiers:[]},WLF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"ign",mnzl:5,mxzl:15}],bounds:[-179.595856,-18.839037,-174.741914,-9.419518],fileIdentifiers:["GL_CARTE_WLF.xml"]}},coastmap:{serviceParams:{WMSC:{format:"image/jpeg"},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:12,maxZoomLevel:15,originators:[{id:"shom",mnzl:12,mxzl:15},{id:"ign",mnzl:12,mxzl:15}],bounds:[-5.345355,41.0632133,9.9423598,51.476197],fileIdentifiers:[]},EUE:{minZoomLevel:12,maxZoomLevel:15,originators:[{id:"shom",mnzl:12,mxzl:15},{id:"ign",mnzl:12,mxzl:15}],bounds:[-5.345355,41.0632133,9.9423598,51.476197],fileIdentifiers:[]},ANF:{minZoomLevel:12,maxZoomLevel:15,originators:[{id:"shom",mnzl:12,mxzl:15},{id:"ign",mnzl:12,mxzl:15}],bounds:[-61.901094,14.780077,-58.577631,16.668132],fileIdentifiers:[]},GLP:{minZoomLevel:12,maxZoomLevel:15,originators:[{id:"shom",mnzl:12,mxzl:15},{id:"ign",mnzl:12,mxzl:15}],bounds:[-61.901094,14.780077,-58.577631,16.668132],fileIdentifiers:[]},SBA:{minZoomLevel:12,maxZoomLevel:15,originators:[{id:"shom",mnzl:12,mxzl:15},{id:"ign",mnzl:12,mxzl:15}],bounds:[-61.901094,14.780077,-58.577631,16.668132],fileIdentifiers:[]},SMA:{minZoomLevel:12,maxZoomLevel:15,originators:[{id:"shom",mnzl:12,mxzl:15},{id:"ign",mnzl:12,mxzl:15}],bounds:[-61.901094,14.780077,-58.577631,16.668132],fileIdentifiers:[]},MTQ:{minZoomLevel:12,maxZoomLevel:15,originators:[{id:"shom",mnzl:12,mxzl:15},{id:"ign",mnzl:12,mxzl:15}],bounds:[-61.367792,14.780077,-58.577631,15.012358],fileIdentifiers:[]}},franceRaster:{serviceParams:{WMSC:{format:"image/jpeg"},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:7,maxZoomLevel:17,originators:[{id:"cartosphere",mnzl:7,mxzl:17},{id:"ign",mnzl:7,mxzl:17}],fileIdentifiers:[]},EUE:{minZoomLevel:7,maxZoomLevel:17,originators:[{id:"cartosphere",mnzl:7,mxzl:17},{id:"ign",mnzl:7,mxzl:17}],fileIdentifiers:[]},ANF:{minZoomLevel:10,maxZoomLevel:17,originators:[{id:"cartosphere",mnzl:10,mxzl:17},{id:"ign",mnzl:10,mxzl:17}],fileIdentifiers:[]},GLP:{minZoomLevel:10,maxZoomLevel:17,originators:[{id:"cartosphere",mnzl:10,mxzl:17},{id:"ign",mnzl:10,mxzl:17}],fileIdentifiers:[]},SBA:{minZoomLevel:10,maxZoomLevel:17,originators:[{id:"cartosphere",mnzl:10,mxzl:17},{id:"ign",mnzl:10,mxzl:17}],fileIdentifiers:[]},SMA:{minZoomLevel:10,maxZoomLevel:17,originators:[{id:"cartosphere",mnzl:10,mxzl:17},{id:"ign",mnzl:10,mxzl:17}],fileIdentifiers:[]},MTQ:{minZoomLevel:10,maxZoomLevel:17,originators:[{id:"cartosphere",mnzl:10,mxzl:17},{id:"ign",mnzl:10,mxzl:17}],fileIdentifiers:[]},REU:{minZoomLevel:10,maxZoomLevel:17,originators:[{id:"cartosphere",mnzl:10,mxzl:17},{id:"ign",mnzl:10,mxzl:17}],fileIdentifiers:[]}},scanadm:{serviceParams:{WMSC:{format:"image/jpeg"},options:{}},layerOptions:{opacity:0.5},FXX:{minZoomLevel:5,maxZoomLevel:13,originators:[{id:"ign",mnzl:5,mxzl:13}],fileIdentifiers:[]},EUE:{minZoomLevel:5,maxZoomLevel:13,originators:[{id:"ign",mnzl:5,mxzl:13}],fileIdentifiers:[]},ANF:{minZoomLevel:7,maxZoomLevel:10,originators:[{id:"ign",mnzl:7,mxzl:10}],fileIdentifiers:[]},GLP:{minZoomLevel:7,maxZoomLevel:10,originators:[{id:"ign",mnzl:7,mxzl:10}],fileIdentifiers:[]},SBA:{minZoomLevel:7,maxZoomLevel:10,originators:[{id:"ign",mnzl:7,mxzl:10}],fileIdentifiers:[]},SMA:{minZoomLevel:7,maxZoomLevel:10,originators:[{id:"ign",mnzl:7,mxzl:10}],fileIdentifiers:[]},MTQ:{minZoomLevel:7,maxZoomLevel:10,originators:[{id:"ign",mnzl:7,mxzl:10}],fileIdentifiers:[]},GUF:{minZoomLevel:7,maxZoomLevel:10,originators:[{id:"ign",mnzl:7,mxzl:10}],fileIdentifiers:[]},REU:{minZoomLevel:7,maxZoomLevel:10,originators:[{id:"ign",mnzl:7,mxzl:10}],fileIdentifiers:[]}},em40:{serviceParams:{WMSC:{format:"image/jpeg"},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],fileIdentifiers:[]},EUE:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],fileIdentifiers:[]}},type1900:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:11,maxZoomLevel:14,originators:[{id:"ign",mnzl:11,mxzl:14}],bounds:[1.710514,48.569392,2.886492,49.158112],fileIdentifiers:[]},EUE:{minZoomLevel:11,maxZoomLevel:14,originators:[{id:"ign",mnzl:11,mxzl:14}],bounds:[1.710514,48.569392,2.886492,49.158112],fileIdentifiers:[]}},cassini:{serviceParams:{WMSC:{format:"image/jpeg"},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:11,maxZoomLevel:14,originators:[{id:"EHESS",mnzl:11,mxzl:14},{id:"cnrs",mnzl:11,mxzl:14},{id:"BNF",mnzl:11,mxzl:14}],bounds:[-5.559169,41.799113,8.124939,50.924272],fileIdentifiers:[]},EUE:{minZoomLevel:11,maxZoomLevel:14,originators:[{id:"EHESS",mnzl:11,mxzl:14},{id:"cnrs",mnzl:11,mxzl:14},{id:"BNF",mnzl:11,mxzl:14}],bounds:[-5.559169,41.799113,8.124939,50.924272],fileIdentifiers:[]}},orthoCoast2000:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:12,maxZoomLevel:17,originators:[{id:"meeddtl",mnzl:12,mxzl:17}],bounds:[-5.51,42.92,2.72,49.67],fileIdentifiers:["d26cf450-3b3a-11dc-9fe0-0015601080cc"]},EUE:{minZoomLevel:12,maxZoomLevel:17,originators:[{id:"meeddtl",mnzl:12,mxzl:17}],bounds:[-5.51,42.92,2.72,49.67],fileIdentifiers:["d26cf450-3b3a-11dc-9fe0-0015601080cc"]}},rpg2010:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]},EUE:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]},ANF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,14.129278,-60.339282,18.250317],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]},GLP:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]},SBA:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]},SMA:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]},MTQ:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-61.558257,14.129278,-60.339282,15.306718],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]},GUF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-54.29449,3.532319,-51.343702,5.887199],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]},REU:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[54.862581,-21.782636,56.12379,-20.605197],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d29"],dataURL:["http://www.asp-public.fr/?q=node/856"]}},rpg2009:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]},EUE:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]},ANF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,14.129278,-60.339282,18.250317],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]},GLP:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]},SBA:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]},SMA:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]},MTQ:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-61.558257,14.129278,-60.339282,15.306718],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]},GUF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-54.29449,3.532319,-51.343702,5.887199],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]},REU:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[54.862581,-21.782636,56.12379,-20.605197],fileIdentifiers:["79bce0b5-a7a9-4fbe-a565-58888e592d24"],dataURL:["http://www.asp-public.fr/?q=node/856"]}},rpg2008:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]},EUE:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]},ANF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,14.129278,-60.339282,18.250317],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]},GLP:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]},SBA:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]},SMA:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]},MTQ:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-61.558257,14.129278,-60.339282,15.306718],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]},GUF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-54.29449,3.532319,-51.343702,5.887199],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]},REU:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[54.862581,-21.782636,56.12379,-20.605197],fileIdentifiers:["54863a7b-feac-4301-8c11-f0af5bbd4052"],dataURL:["http://www.asp-public.fr/?q=node/856"]}},rpg2007:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]},EUE:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]},ANF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,14.129278,-60.339282,18.250317],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]},GLP:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]},SBA:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]},SMA:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]},MTQ:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-61.558257,14.129278,-60.339282,15.306718],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]},GUF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[-54.29449,3.532319,-51.343702,5.887199],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]},REU:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"maaprat",mnzl:5,mxzl:15},{id:"asp",mnzl:5,mxzl:15}],bounds:[54.862581,-21.782636,56.12379,-20.605197],fileIdentifiers:["0656092-c31f-4f44-b7ef-afb9b61df06f"],dataURL:["http://www.asp-public.fr/?q=node/856"]}},forestV2:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:5,maxZoomLevel:10,originators:[{id:"ign",mnzl:5,mxzl:10}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["cc10fbf0-3762-43d5-a5d7-7a5c26c5fde0"],dataURL:["http://www.ifn.fr/spip/?rubrique53"]},EUE:{minZoomLevel:5,maxZoomLevel:10,originators:[{id:"ign",mnzl:5,mxzl:10}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["cc10fbf0-3762-43d5-a5d7-7a5c26c5fde0"],dataURL:["http://www.ifn.fr/spip/?rubrique53"]}},forestV1:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"ign",mnzl:5,mxzl:15}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["b323be92-b828-46ed-ac8f-0480576dbf89"],dataURL:["http://www.ifn.fr/spip/spip.php?rubrique180"]},EUE:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"ign",mnzl:5,mxzl:15}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["b323be92-b828-46ed-ac8f-0480576dbf89"],dataURL:["http://www.ifn.fr/spip/spip.php?rubrique180"]}},clc:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"eea",mnzl:5,mxzl:12},{id:"meeddtl",mnzl:5,mxzl:12}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["d15c51d0-d037-11dd-94bd-001438ebb238"]},EUE:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"eea",mnzl:5,mxzl:12},{id:"meeddtl",mnzl:5,mxzl:12}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["d15c51d0-d037-11dd-94bd-001438ebb238"]}},slopes:{serviceParams:{WMSC:{format:"image/jpeg"},options:{}},layerOptions:{opacity:0.3},FXX:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5323461"]},EUE:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-6.842054,37.678074,13.684108,51.807352],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5323461"]},ANF:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-63.38672,14.129278,-60.339282,18.250317],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-63.38672,15.306718,-60.948769,18.250317],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-61.558257,14.129278,-60.339282,15.306718],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-54.29449,3.532319,-51.343702,5.887199],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},MYT:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[44.538547,-13.540558,45.742292,-12.363118],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},NCL:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[162.548447,-22.960076,167.628086,-19.427757],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},REU:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[54.862581,-21.782636,56.12379,-20.605197],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},SPM:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-56.973031,46.508872,-56.109803,47.686312],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]},WLF:{minZoomLevel:5,maxZoomLevel:12,originators:[{id:"ign",mnzl:5,mxzl:12}],bounds:[-178.382371,-14.717998,-175.9554,-12.951838],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"]}},bdparcel:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:7,maxZoomLevel:18,originators:[{id:"ign",mnzl:7,mxzl:18}],bounds:[-5.184994,41.320778,9.568185,51.108247],fileIdentifiers:["IGNF_BDPARCELLAIREr_1-2_image.xml"]},EUE:{minZoomLevel:7,maxZoomLevel:18,originators:[{id:"ign",mnzl:7,mxzl:18}],bounds:[-5.184994,41.320778,9.568185,51.108247],fileIdentifiers:["IGNF_BDPARCELLAIREr_1-2_image.xml"]},ANF:{minZoomLevel:12,maxZoomLevel:18,originators:[{id:"ign",mnzl:12,mxzl:18}],bounds:[-63.272441,14.239663,-60.605933,18.287112],fileIdentifiers:[]},GLP:{minZoomLevel:12,maxZoomLevel:18,originators:[{id:"ign",mnzl:12,mxzl:18}],bounds:[-63.272441,15.711462,-60.83449,18.287112],fileIdentifiers:["IGNF_BDPARCELLAIREr_1-2_image.xml"]},SBA:{minZoomLevel:12,maxZoomLevel:18,originators:[{id:"ign",mnzl:12,mxzl:18}],bounds:[-63.272441,15.711462,-60.83449,18.287112],fileIdentifiers:["IGNF_BDPARCELLAIREr_1-2_image.xml"]},SMA:{minZoomLevel:12,maxZoomLevel:18,originators:[{id:"ign",mnzl:12,mxzl:18}],bounds:[-63.272441,15.711462,-60.83449,18.287112],fileIdentifiers:["IGNF_BDPARCELLAIREr_1-2_image.xml"]},MTQ:{minZoomLevel:12,maxZoomLevel:18,originators:[{id:"ign",mnzl:12,mxzl:18}],bounds:[-61.253513,14.239663,-60.605933,15.012358],fileIdentifiers:["IGNF_BDPARCELLAIREr_1-2_image.xml"]},REU:{minZoomLevel:12,maxZoomLevel:18,originators:[{id:"ign",mnzl:12,mxzl:18}],bounds:[55.177883,-21.414687,55.8479,-20.862762],fileIdentifiers:["IGNF_BDPARCELLAIREr_1-2_image.xml"]}},floodws1910:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.5},FXX:{minZoomLevel:6,maxZoomLevel:15,originators:[{id:"DIRENIDF",mnzl:6,mxzl:15},{id:"DIRENHAUTENORMANDIE",mnzl:6,mxzl:15},{id:"SEINEENPARTAGE",mnzl:6,mxzl:15}],bounds:[-1.7105135,47.0975924,6.8420541,50.6299118],fileIdentifiers:[]},EUE:{minZoomLevel:6,maxZoomLevel:15,originators:[{id:"DIRENIDF",mnzl:6,mxzl:15},{id:"DIRENHAUTENORMANDIE",mnzl:6,mxzl:15},{id:"SEINEENPARTAGE",mnzl:6,mxzl:15}],bounds:[-1.7105135,47.0975924,6.8420541,50.6299118],fileIdentifiers:[]}},floodcl1910:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.5},FXX:{minZoomLevel:9,maxZoomLevel:15,originators:[{id:"DIRENIDF",mnzl:9,mxzl:15},{id:"DIRENHAUTENORMANDIE",mnzl:9,mxzl:15},{id:"SEINEENPARTAGE",mnzl:9,mxzl:15}],bounds:[-1.7105135,47.0975924,6.8420541,50.6299118],fileIdentifiers:[]},EUE:{minZoomLevel:9,maxZoomLevel:15,originators:[{id:"DIRENIDF",mnzl:9,mxzl:15},{id:"DIRENHAUTENORMANDIE",mnzl:9,mxzl:15},{id:"SEINEENPARTAGE",mnzl:9,mxzl:15}],bounds:[-1.7105135,47.0975924,6.8420541,50.6299118],fileIdentifiers:[]}},waterb:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.5},FXX:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],bounds:[-13.6841082,32.9683147,34.2102704,75.3561478],fileIdentifiers:["IGNF_BDCARTOr_3-1_HYDROGRAPHIE.xml","IGNF_BDTOPOr_2-0_HYDROGRAPHIE.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],bounds:[-5.559169,41.210393,9.835453,51.218632],fileIdentifiers:["IGNF_BDCARTOr_3-1_HYDROGRAPHIE.xml","IGNF_BDTOPOr_2-0_HYDROGRAPHIE.xml"]},ANF:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_HYDROGRAPHIE.xml","IGNF_BDTOPOr_2-0_HYDROGRAPHIE.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_HYDROGRAPHIE.xml","IGNF_BDTOPOr_2-0_HYDROGRAPHIE.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_HYDROGRAPHIE.xml","IGNF_BDTOPOr_2-0_HYDROGRAPHIE.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],bounds:[-61.253513,14.350048,-60.796397,14.938768],fileIdentifiers:["IGNF_BDCARTOr_3-1_HYDROGRAPHIE.xml","IGNF_BDTOPOr_2-0_HYDROGRAPHIE.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:5,mxzl:16}],bounds:[-54.515799,3.458729,-51.565011,5.813609],fileIdentifiers:["IGNF_BDCARTOr_3-1_HYDROGRAPHIE.xml"]},MYT:{minZoomLevel:14,maxZoomLevel:16,originators:[{id:"ign",mnzl:14,mxzl:16}],bounds:[44.989951,-13.025428,45.366121,-12.657478],fileIdentifiers:["IGNF_BDTOPOr_2-0_HYDROGRAPHIE.xml"]},REU:{minZoomLevel:5,maxZoomLevel:16,originators:[{id:"ign",mnzl:11,mxzl:16}],bounds:[55.177883,-21.414687,55.887313,-20.825967],fileIdentifiers:["IGNF_BDCARTOr_3-1_HYDROGRAPHIE.xml","IGNF_BDTOPOr_2-0_HYDROGRAPHIE.xml"]}},roadl:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.5},FXX:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"eurogeographics",mnzl:5,mxzl:7},{id:"ign",mnzl:8,mxzl:17}],bounds:[-34.21027,32.968315,34.21027,80.065907],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_RESEAU_FERRE.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml","IGNF_BDTOPOr_2-0_VOIES_FERREES_ET_AUTRES.xml","IGNF_EGMr_2-1.xml","IGNF_ERMr_2-2.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"eurogeographics",mnzl:5,mxzl:7},{id:"ign",mnzl:8,mxzl:17}],bounds:[-34.21027,32.968315,34.21027,80.065907],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_RESEAU_FERRE.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml","IGNF_BDTOPOr_2-0_VOIES_FERREES_ET_AUTRES.xml","IGNF_EGMr_2-1.xml","IGNF_ERMr_2-2.xml"]},ANF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-61.253513,14.350048,-60.796397,14.938768],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:15,originators:[{id:"ign",mnzl:5,mxzl:15}],bounds:[-54.36826,3.826679,-51.786321,5.813609],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml"]},MYT:{minZoomLevel:14,maxZoomLevel:17,originators:[{id:"ign",mnzl:14,mxzl:17}],bounds:[45.036973,-12.997832,45.300292,-12.666677],fileIdentifiers:["IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},REU:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[55.177883,-21.414687,55.887313,-20.899557],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]}},raill:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-13.6841082,32.9683147,34.2102704,75.3561478],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_RESEAU_FERRE.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml","IGNF_BDTOPOr_2-0_VOIES_FERREES_ET_AUTRES.xml","IGNF_EGMr_2-1.xml","IGNF_ERMr_2-2.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-6.842054,40.032954,13.684108,51.807352],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_RESEAU_FERRE.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml","IGNF_BDTOPOr_2-0_VOIES_FERREES_ET_AUTRES.xml","IGNF_EGMr_2-1.xml","IGNF_ERMr_2-2.xml"]}},runwaya:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.5},FXX:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],bounds:[-5.131541,41.357573,9.621639,51.071452],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_RESEAU_FERRE.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml","IGNF_BDTOPOr_2-0_VOIES_FERREES_ET_AUTRES.xml","IGNF_EGMr_2-1.xml","IGNF_ERMr_2-2.xml"]},EUE:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],bounds:[-5.131541,41.357573,9.621639,51.071452],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_RESEAU_FERRE.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml","IGNF_BDTOPOr_2-0_VOIES_FERREES_ET_AUTRES.xml","IGNF_EGMr_2-1.xml","IGNF_ERMr_2-2.xml"]},ANF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,14.570818,-60.948769,18.103137],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-61.024955,18.103137],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-61.024955,18.103137],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-61.024955,18.103137],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-61.177327,14.570818,-60.948769,14.717998],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-52.376478,4.783349,-52.302708,4.856939],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml"]},MYT:{minZoomLevel:14,maxZoomLevel:17,originators:[{id:"ign",mnzl:14,mxzl:17}],bounds:[45.272079,-12.823055,45.290887,-12.795459],fileIdentifiers:["IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml"]},REU:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[55.177883,-21.341097,55.572011,-20.825967],fileIdentifiers:["IGNF_BDCARTOr_3-1_RESEAU_ROUTIER.xml","IGNF_BDCARTOr_3-1_RESEAU_FERRE.xml","IGNF_BDCARTOr_3-1_EQUIPEMENT.xml","IGNF_BDTOPOr_2-0_RESEAU_ROUTIER.xml","IGNF_BDTOPOr_2-0_VOIES_FERREES_ET_AUTRES.xml"]}},buildings:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:0.5},FXX:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-13.6841082,32.9683147,34.2102704,75.3561478],fileIdentifiers:["IGNF_BDTOPOr_2-0_BATI.xml","IGNF_BDCARTOr_3-1_HABILLAGE.xml","IGNF_ERMr_2-2.xml","IGNF_EGMr_2-1.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-5.986797,41.210393,10.263081,51.218632],fileIdentifiers:["IGNF_BDTOPOr_2-0_BATI.xml","IGNF_BDCARTOr_3-1_HABILLAGE.xml","IGNF_ERMr_2-2.xml","IGNF_EGMr_2-1.xml"]},ANF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDTOPOr_2-0_BATI.xml","IGNF_BDCARTOr_3-1_HABILLAGE.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDTOPOr_2-0_BATI.xml","IGNF_BDCARTOr_3-1_HABILLAGE.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDTOPOr_2-0_BATI.xml","IGNF_BDCARTOr_3-1_HABILLAGE.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-61.253513,14.350048,-60.796397,14.938768],fileIdentifiers:["IGNF_BDTOPOr_2-0_BATI.xml","IGNF_BDCARTOr_3-1_HABILLAGE.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-54.36826,3.826679,-51.786321,5.813609],fileIdentifiers:["IGNF_BDCARTOr_3-1_HABILLAGE.xml"]},MYT:{minZoomLevel:14,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:14}],bounds:[45.018164,-12.997832,45.300292,-12.63908],fileIdentifiers:["IGNF_BDTOPOr_2-0_BATI.xml"]},REU:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[55.177883,-21.414687,55.887313,-20.825967],fileIdentifiers:["IGNF_BDTOPOr_2-0_BATI.xml","IGNF_BDCARTOr_3-1_HABILLAGE.xml"]}},utility:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["IGNF_BDTOPOr_2-0_TRANSPORT_ENERGIE.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["IGNF_BDTOPOr_2-0_TRANSPORT_ENERGIE.xml"]},ANF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDTOPOr_2-0_TRANSPORT_ENERGIE.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDTOPOr_2-0_TRANSPORT_ENERGIE.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDTOPOr_2-0_TRANSPORT_ENERGIE.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-61.253513,14.350048,-60.796397,14.938768],fileIdentifiers:["IGNF_BDTOPOr_2-0_TRANSPORT_ENERGIE.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-56.646306,4.724861,-51.9435,9.416222],fileIdentifiers:["IGNF_BDTOPOr_2-0_TRANSPORT_ENERGIE.xml"]},REU:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[55.256709,-21.414687,55.808487,-20.825967],fileIdentifiers:["IGNF_BDTOPOr_2-0_TRANSPORT_ENERGIE.xml"]}},limadm:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-13.6841082,32.9683147,34.2102704,75.3561478],fileIdentifiers:["IGNF_BDCARTOr_3-1_ADMINISTRATIF.xml","IGNF_BDTOPOr_2-0_ADMINISTRATIF.xml","IGNF_EBMr_4-0.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-13.6841082,32.9683147,34.2102704,75.3561478],fileIdentifiers:["IGNF_BDCARTOr_3-1_ADMINISTRATIF.xml","IGNF_BDTOPOr_2-0_ADMINISTRATIF.xml","IGNF_EBMr_4-0.xml"]},ANF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:[]},GLP:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_ADMINISTRATIF.xml","IGNF_BDTOPOr_2-0_ADMINISTRATIF.xml"]},SBA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_ADMINISTRATIF.xml","IGNF_BDTOPOr_2-0_ADMINISTRATIF.xml"]},SMA:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-63.158162,15.821847,-60.948769,18.176727],fileIdentifiers:["IGNF_BDCARTOr_3-1_ADMINISTRATIF.xml","IGNF_BDTOPOr_2-0_ADMINISTRATIF.xml"]},MTQ:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-61.253513,14.350048,-60.796397,14.938768],fileIdentifiers:["IGNF_BDCARTOr_3-1_ADMINISTRATIF.xml","IGNF_BDTOPOr_2-0_ADMINISTRATIF.xml"]},GUF:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[-54.663338,2.111463,-51.565011,5.813609],fileIdentifiers:["IGNF_BDCARTOr_3-1_ADMINISTRATIF.xml"]},MYT:{minZoomLevel:14,maxZoomLevel:17,originators:[{id:"ign",mnzl:14,mxzl:17}],bounds:[45.018164,-13.00703,45.300292,-12.629882],fileIdentifiers:["IGNF_BDTOPOr_2-0_ADMINISTRATIF.xml"]},REU:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],bounds:[55.177883,-21.414687,55.887313,-20.825967],fileIdentifiers:["IGNF_BDCARTOr_3-1_ADMINISTRATIF.xml","IGNF_BDTOPOr_2-0_ADMINISTRATIF.xml"]}},level0:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},EUE:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-6.842054,40.032954,10.263081,51.807352],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},ANF:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},GLP:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},SBA:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},SMA:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-63.158162,14.350048,-60.796397,18.176727],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},MTQ:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-61.253513,14.350048,-60.796397,14.938768],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},GUF:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-54.663338,2.111463,-51.565011,5.813609],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},MYT:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[45.018164,-13.00703,45.300292,-12.629882],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},REU:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[55.177883,-21.414687,55.887313,-20.825967],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]},SPM:{minZoomLevel:7,maxZoomLevel:16,originators:[{id:"ign",mnzl:7,mxzl:16},{id:"shom",mnzl:7,mxzl:16}],bounds:[-56.973556,45.926889,-55.253833,48.277167],fileIdentifiers:["IGNF_BDALTIr_1-0.xml","IGNF_SHOM_HISTOLITTr_1-0.xml"],dataURL:["http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5465861"]}},toponyms:{serviceParams:{WMSC:{format:"image/png",transparent:true},options:{}},layerOptions:{opacity:1},FXX:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]},EUE:{minZoomLevel:5,maxZoomLevel:17,originators:[{id:"ign",mnzl:5,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]},ANF:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]},GLP:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]},SBA:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]},SMA:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]},MTQ:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]},GUF:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]},REU:{minZoomLevel:11,maxZoomLevel:17,originators:[{id:"ign",mnzl:11,mxzl:17}],fileIdentifiers:["IGNF_BDNYMEr_2-0.xml"]}}};