/*

  Geoportal.js -- IGN France Geoportal Map Viewer Library

  Copyright 2007-2009 IGN France, released under the BSD license.
  Please see http://api.ign.fr/geoportail/doc/webmaster/license.html
  Please see http://api.ign.fr/geoportail/doc/fr/webmaster/licence.html
  for the full text of the license.

*/

/*
  Contains rewritting of http://hexmen.com/blog/2007/03/printf-sprintf/

  This code is unrestricted: you are free to use it however you like.

 */

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

/*
  Contains OpenLayers.js -- OpenLayers Map Viewer Library

  Copyright 2005-2007 MetaCarta, Inc., released under the BSD license.
  Please see http://svn.openlayers.org/trunk/openlayers/release-license.txt
  for the full text of the license.

  Includes compressed code under the following licenses:

  (For uncompressed versions of the code used please see the
  OpenLayers SVN repository: <http://openlayers.org/>)

*/

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

Proj4js={defaultDatum:"WGS84",transform:function(D,B,A){if(!D.readyToUse||!B.readyToUse){this.reportError("Proj4js initialization for "+D.srsCode+" not yet complete");
return A
}if((D.srsProjNumber=="900913"&&B.datumCode!="WGS84")||(B.srsProjNumber=="900913"&&D.datumCode!="WGS84")){var C=Proj4js.WGS84;
this.transform(D,C,A);
D=C
}if(D.projName=="longlat"){A.x*=Proj4js.common.D2R;
A.y*=Proj4js.common.D2R
}else{if(D.to_meter){A.x*=D.to_meter;
A.y*=D.to_meter
}D.inverse(A)
}if(D.from_greenwich){A.x+=D.from_greenwich
}A=this.datum_transform(D.datum,B.datum,A);
if(B.from_greenwich){A.x-=B.from_greenwich
}if(B.projName=="longlat"){A.x*=Proj4js.common.R2D;
A.y*=Proj4js.common.R2D
}else{B.forward(A);
if(B.to_meter){A.x/=B.to_meter;
A.y/=B.to_meter
}}return A
},datum_transform:function(C,B,A){if(C.compare_datums(B)){return A
}if(C.datum_type==Proj4js.common.PJD_NODATUM||B.datum_type==Proj4js.common.PJD_NODATUM){return A
}if(C.datum_type==Proj4js.common.PJD_GRIDSHIFT){alert("ERROR: Grid shift transformations are not implemented yet.")
}if(B.datum_type==Proj4js.common.PJD_GRIDSHIFT){alert("ERROR: Grid shift transformations are not implemented yet.")
}if(C.es!=B.es||C.a!=B.a||C.datum_type==Proj4js.common.PJD_3PARAM||C.datum_type==Proj4js.common.PJD_7PARAM||B.datum_type==Proj4js.common.PJD_3PARAM||B.datum_type==Proj4js.common.PJD_7PARAM){C.geodetic_to_geocentric(A);
if(C.datum_type==Proj4js.common.PJD_3PARAM||C.datum_type==Proj4js.common.PJD_7PARAM){C.geocentric_to_wgs84(A)
}if(B.datum_type==Proj4js.common.PJD_3PARAM||B.datum_type==Proj4js.common.PJD_7PARAM){B.geocentric_from_wgs84(A)
}B.geocentric_to_geodetic(A)
}if(B.datum_type==Proj4js.common.PJD_GRIDSHIFT){alert("ERROR: Grid shift transformations are not implemented yet.")
}return A
},reportError:function(A){},extend:function(A,D){A=A||{};
if(D){for(var C in D){var B=D[C];
if(B!==undefined){A[C]=B
}}}return A
},Class:function(){var B=function(){this.initialize.apply(this,arguments)
};
var A={};
var D;
for(var C=0;
C<arguments.length;
++C){if(typeof arguments[C]=="function"){D=arguments[C].prototype
}else{D=arguments[C]
}Proj4js.extend(A,D)
}B.prototype=A;
return B
},bind:function(C,B){var A=Array.prototype.slice.apply(arguments,[2]);
return function(){var D=A.concat(Array.prototype.slice.apply(arguments,[0]));
return C.apply(B,D)
}
},scriptName:"proj4js-compressed.js",defsLookupService:"http://spatialreference.org/ref",libPath:null,getScriptLocation:function(){if(this.libPath){return this.libPath
}var E=this.scriptName;
var D=E.length;
var A=document.getElementsByTagName("script");
for(var C=0;
C<A.length;
C++){var F=A[C].getAttribute("src");
if(F){var B=F.lastIndexOf(E);
if((B>-1)&&(B+D==F.length)){this.libPath=F.slice(0,-D);
break
}}}return this.libPath||""
},loadScript:function(D,E,C,A){var B=document.createElement("script");
B.defer=false;
B.type="text/javascript";
B.id=D;
B.src=D;
B.onload=E;
B.onerror=C;
B.loadCheck=A;
if(/MSIE/.test(navigator.userAgent)){B.onreadystatechange=this.checkReadyState
}document.getElementsByTagName("head")[0].appendChild(B)
},checkReadyState:function(){if(this.readyState=="loaded"){if(!this.loadCheck()){this.onerror()
}else{this.onload()
}}}};
Proj4js.Proj=Proj4js.Class({readyToUse:false,title:null,projName:null,units:null,datum:null,initialize:function(C){this.srsCodeInput=C;
if(C.indexOf("urn:")==0){var A=C.split(":");
if((A[1]=="ogc"||A[1]=="x-ogc")&&(A[2]=="def")&&(A[3]=="crs")&&A.length==7){C=A[4]+":"+A[6]
}}else{if(C.indexOf("http://")==0){var B=C.split("#");
if(B[0].match(/epsg.org/)){C="EPSG:"+B[1]
}else{if(B[0].match(/RIG.xml/)){C="IGNF:"+B[1]
}}}}this.srsCode=C.toUpperCase();
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
}var A=Proj4js.getScriptLocation()+"defs/"+this.srsAuth.toUpperCase()+this.srsProjNumber+".js";
Proj4js.loadScript(A,Proj4js.bind(this.defsLoaded,this),Proj4js.bind(this.loadFromService,this),Proj4js.bind(this.checkDefsLoaded,this))
},loadFromService:function(){var A=Proj4js.defsLookupService+"/"+this.srsAuth+"/"+this.srsProjNumber+"/proj4js/";
Proj4js.loadScript(A,Proj4js.bind(this.defsLoaded,this),Proj4js.bind(this.defsFailed,this),Proj4js.bind(this.checkDefsLoaded,this))
},defsLoaded:function(){this.parseDefs();
this.loadProjCode(this.projName)
},checkDefsLoaded:function(){if(Proj4js.defs[this.srsCode]){return true
}else{return false
}},defsFailed:function(){Proj4js.reportError("failed to load projection definition for: "+this.srsCode);
Proj4js.defs[this.srsCode]=Proj4js.defs.WGS84;
this.defsLoaded()
},loadProjCode:function(B){if(Proj4js.Proj[B]){this.initTransforms();
return 
}var A=Proj4js.getScriptLocation()+"projCode/"+B+".js";
Proj4js.loadScript(A,Proj4js.bind(this.loadProjCodeSuccess,this,B),Proj4js.bind(this.loadProjCodeFailure,this,B),Proj4js.bind(this.checkCodeLoaded,this,B))
},loadProjCodeSuccess:function(A){if(Proj4js.Proj[A].dependsOn){this.loadProjCode(Proj4js.Proj[A].dependsOn)
}else{this.initTransforms()
}},loadProjCodeFailure:function(A){Proj4js.reportError("failed to find projection file for: "+A)
},checkCodeLoaded:function(A){if(Proj4js.Proj[A]){return true
}else{return false
}},initTransforms:function(){Proj4js.extend(this,Proj4js.Proj[this.projName]);
this.init();
this.readyToUse=true
},parseDefs:function(){this.defData=Proj4js.defs[this.srsCode];
var D,B;
if(!this.defData){return 
}var A=this.defData.split("+");
for(var E=0;
E<A.length;
E++){var C=A[E].split("=");
D=C[0].toLowerCase();
B=C[1];
switch(D.replace(/\s/gi,"")){case"":break;
case"title":this.title=B;
break;
case"proj":this.projName=B.replace(/\s/gi,"");
break;
case"units":this.units=B.replace(/\s/gi,"");
break;
case"datum":this.datumCode=B.replace(/\s/gi,"");
break;
case"nadgrids":this.nagrids=B.replace(/\s/gi,"");
break;
case"ellps":this.ellps=B.replace(/\s/gi,"");
break;
case"a":this.a=parseFloat(B);
break;
case"b":this.b=parseFloat(B);
break;
case"rf":this.rf=parseFloat(B);
break;
case"lat_0":this.lat0=B*Proj4js.common.D2R;
break;
case"lat_1":this.lat1=B*Proj4js.common.D2R;
break;
case"lat_2":this.lat2=B*Proj4js.common.D2R;
break;
case"lat_ts":this.lat_ts=B*Proj4js.common.D2R;
break;
case"lon_0":this.long0=B*Proj4js.common.D2R;
break;
case"alpha":this.alpha=parseFloat(B)*Proj4js.common.D2R;
break;
case"lonc":this.longc=B*Proj4js.common.D2R;
break;
case"x_0":this.x0=parseFloat(B);
break;
case"y_0":this.y0=parseFloat(B);
break;
case"k_0":this.k0=parseFloat(B);
break;
case"k":this.k0=parseFloat(B);
break;
case"r_a":this.R_A=true;
break;
case"zone":this.zone=parseInt(B);
break;
case"south":this.utmSouth=true;
break;
case"towgs84":this.datum_params=B.split(",");
break;
case"to_meter":this.to_meter=parseFloat(B);
break;
case"from_greenwich":this.from_greenwich=B*Proj4js.common.D2R;
break;
case"pm":B=B.replace(/\s/gi,"");
this.from_greenwich=Proj4js.PrimeMeridian[B]?Proj4js.PrimeMeridian[B]:parseFloat(B);
this.from_greenwich*=Proj4js.common.D2R;
break;
case"no_defs":break;
default:}}this.deriveConstants()
},deriveConstants:function(){if(this.nagrids=="@null"){this.datumCode="none"
}if(this.datumCode&&this.datumCode!="none"){var A=Proj4js.Datum[this.datumCode];
if(A){this.datum_params=A.towgs84.split(",");
this.ellps=A.ellipse;
this.datumName=A.datumName?A.datumName:this.datumCode
}}if(!this.a){var B=Proj4js.Ellipsoid[this.ellps]?Proj4js.Ellipsoid[this.ellps]:Proj4js.Ellipsoid.WGS84;
Proj4js.extend(this,B)
}if(this.rf&&!this.b){this.b=(1-1/this.rf)*this.a
}if(Math.abs(this.a-this.b)<Proj4js.common.EPSLN){this.sphere=true;
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
}this.datum=new Proj4js.datum(this)
}});
Proj4js.Proj.longlat={init:function(){},forward:function(A){return A
},inverse:function(A){return A
}};
Proj4js.defs={WGS84:"+title=long/lat:WGS84 +proj=longlat +ellps=WGS84 +datum=WGS84 +units=degrees","EPSG:4326":"+title=long/lat:WGS84 +proj=longlat +a=6378137.0 +b=6356752.31424518 +ellps=WGS84 +datum=WGS84 +units=degrees","EPSG:4269":"+title=long/lat:NAD83 +proj=longlat +a=6378137.0 +b=6356752.31414036 +ellps=GRS80 +datum=NAD83 +units=degrees","EPSG:3785":"+title= Google Mercator +proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +no_defs"};
Proj4js.defs.GOOGLE=Proj4js.defs["EPSG:3785"];
Proj4js.defs["EPSG:900913"]=Proj4js.defs["EPSG:3785"];
Proj4js.defs["EPSG:102113"]=Proj4js.defs["EPSG:3785"];
Proj4js.common={PI:3.141592653589793,HALF_PI:1.5707963267948966,TWO_PI:6.283185307179586,FORTPI:0.7853981633974483,R2D:57.29577951308232,D2R:0.017453292519943295,SEC_TO_RAD:0.00000484813681109536,EPSLN:1e-10,MAX_ITER:20,COS_67P5:0.3826834323650898,AD_C:1.0026,PJD_UNKNOWN:0,PJD_3PARAM:1,PJD_7PARAM:2,PJD_GRIDSHIFT:3,PJD_WGS84:4,PJD_NODATUM:5,SRS_WGS84_SEMIMAJOR:6378137,SIXTH:0.16666666666666666,RA4:0.04722222222222222,RA6:0.022156084656084655,RV4:0.06944444444444445,RV6:0.04243827160493827,msfnz:function(C,B,D){var A=C*B;
return D/(Math.sqrt(1-A*A))
},tsfnz:function(E,D,C){var A=E*C;
var B=0.5*E;
A=Math.pow(((1-A)/(1+A)),B);
return(Math.tan(0.5*(this.HALF_PI-D))/A)
},phi2z:function(F,E){var D=0.5*F;
var A,B;
var C=this.HALF_PI-2*Math.atan(E);
for(i=0;
i<=15;
i++){A=F*Math.sin(C);
B=this.HALF_PI-2*Math.atan(E*(Math.pow(((1-A)/(1+A)),D)))-C;
C+=B;
if(Math.abs(B)<=1e-10){return C
}}alert("phi2z has NoConvergence");
return(-9999)
},qsfnz:function(C,B,D){var A;
if(C>1e-7){A=C*B;
return((1-C*C)*(B/(1-A*A)-(0.5/C)*Math.log((1-A)/(1+A))))
}else{return(2*B)
}},asinz:function(A){if(Math.abs(A)>1){A=(A>1)?1:-1
}return Math.asin(A)
},e0fn:function(A){return(1-0.25*A*(1+A/16*(3+1.25*A)))
},e1fn:function(A){return(0.375*A*(1+0.25*A*(1+0.46875*A)))
},e2fn:function(A){return(0.05859375*A*A*(1+0.75*A))
},e3fn:function(A){return(A*A*A*(35/3072))
},mlfn:function(E,D,C,B,A){return(E*A-D*Math.sin(2*A)+C*Math.sin(4*A)-B*Math.sin(6*A))
},srat:function(A,B){return(Math.pow((1-A)/(1+A),B))
},sign:function(A){if(A<0){return(-1)
}else{return(1)
}},adjust_lon:function(A){A=(Math.abs(A)<this.PI)?A:(A-(this.sign(A)*this.TWO_PI));
return A
},adjust_lat:function(A){A=(Math.abs(A)<this.HALF_PI)?A:(A-(this.sign(A)*this.PI));
return A
},latiso:function(D,C,B){if(Math.abs(C)>this.HALF_PI){return +Number.NaN
}if(C==this.HALF_PI){return Number.POSITIVE_INFINITY
}if(C==-1*this.HALF_PI){return -1*Number.POSITIVE_INFINITY
}var A=D*B;
return Math.log(Math.tan((this.HALF_PI+C)/2))+D*Math.log((1-A)/(1+A))/2
},fL:function(B,A){return 2*Math.atan(B*Math.exp(A))-this.HALF_PI
},invlatiso:function(E,C){var B=this.fL(1,C);
var D=0;
var A=0;
do{D=B;
A=E*Math.sin(D);
B=this.fL(Math.exp(E*Math.log((1+A)/(1-A))/2),C)
}while(Math.abs(B-D)>1e-12);
return B
},sinh:function(A){var B=Math.exp(A);
B=(B-1/B)/2;
return B
},cosh:function(A){var B=Math.exp(A);
B=(B+1/B)/2;
return B
},tanh:function(A){var B=Math.exp(A);
B=(B-1/B)/(B+1/B);
return B
},asinh:function(A){var B=(A>=0?1:-1);
return B*(Math.log(Math.abs(A)+Math.sqrt(A*A+1)))
},acosh:function(A){return 2*Math.log(Math.sqrt((A+1)/2)+Math.sqrt((A-1)/2))
},atanh:function(A){return Math.log((A-1)/(A+1))/2
},gN:function(A,D,C){var B=D*C;
return A/Math.sqrt(1-B*B)
}};
Proj4js.datum=Proj4js.Class({initialize:function(B){this.datum_type=Proj4js.common.PJD_WGS84;
if(B.datumCode&&B.datumCode=="none"){this.datum_type=Proj4js.common.PJD_NODATUM
}if(B&&B.datum_params){for(var A=0;
A<B.datum_params.length;
A++){B.datum_params[A]=parseFloat(B.datum_params[A])
}if(B.datum_params[0]!=0||B.datum_params[1]!=0||B.datum_params[2]!=0){this.datum_type=Proj4js.common.PJD_3PARAM
}if(B.datum_params.length>3){if(B.datum_params[3]!=0||B.datum_params[4]!=0||B.datum_params[5]!=0||B.datum_params[6]!=0){this.datum_type=Proj4js.common.PJD_7PARAM;
B.datum_params[3]*=Proj4js.common.SEC_TO_RAD;
B.datum_params[4]*=Proj4js.common.SEC_TO_RAD;
B.datum_params[5]*=Proj4js.common.SEC_TO_RAD;
B.datum_params[6]=(B.datum_params[6]/1000000)+1
}}}if(B){this.a=B.a;
this.b=B.b;
this.es=B.es;
this.ep2=B.ep2;
this.datum_params=B.datum_params
}},compare_datums:function(A){if(this.datum_type!=A.datum_type){return false
}else{if(this.a!=A.a||Math.abs(this.es-A.es)>5e-11){return false
}else{if(this.datum_type==Proj4js.common.PJD_3PARAM){return(this.datum_params[0]==A.datum_params[0]&&this.datum_params[1]==A.datum_params[1]&&this.datum_params[2]==A.datum_params[2])
}else{if(this.datum_type==Proj4js.common.PJD_7PARAM){return(this.datum_params[0]==A.datum_params[0]&&this.datum_params[1]==A.datum_params[1]&&this.datum_params[2]==A.datum_params[2]&&this.datum_params[3]==A.datum_params[3]&&this.datum_params[4]==A.datum_params[4]&&this.datum_params[5]==A.datum_params[5]&&this.datum_params[6]==A.datum_params[6])
}else{if(this.datum_type==Proj4js.common.PJD_GRIDSHIFT){return strcmp(pj_param(this.params,"snadgrids").s,pj_param(A.params,"snadgrids").s)==0
}else{return true
}}}}}},geodetic_to_geocentric:function(C){var L=C.x;
var H=C.y;
var D=C.z?C.z:0;
var E;
var B;
var A;
var J=0;
var K;
var I;
var G;
var F;
if(H<-Proj4js.common.HALF_PI&&H>-1.001*Proj4js.common.HALF_PI){H=-Proj4js.common.HALF_PI
}else{if(H>Proj4js.common.HALF_PI&&H<1.001*Proj4js.common.HALF_PI){H=Proj4js.common.HALF_PI
}else{if((H<-Proj4js.common.HALF_PI)||(H>Proj4js.common.HALF_PI)){Proj4js.reportError("geocent:lat out of range:"+H);
return null
}}}if(L>Proj4js.common.PI){L-=(2*Proj4js.common.PI)
}I=Math.sin(H);
F=Math.cos(H);
G=I*I;
K=this.a/(Math.sqrt(1-this.es*G));
E=(K+D)*F*Math.cos(L);
B=(K+D)*F*Math.sin(L);
A=((K*(1-this.es))+D)*I;
C.x=E;
C.y=B;
C.z=A;
return J
},geocentric_to_geodetic:function(T){var b=1e-12;
var U=(b*b);
var F=30;
var L;
var H;
var A;
var N;
var B;
var M;
var K;
var a;
var W;
var J;
var R;
var Q;
var E;
var V;
var G=T.x;
var D=T.y;
var C=T.z?T.z:0;
var I;
var S;
var O;
E=false;
L=Math.sqrt(G*G+D*D);
H=Math.sqrt(G*G+D*D+C*C);
if(L/this.a<b){E=true;
I=0;
if(H/this.a<b){S=Proj4js.common.HALF_PI;
O=-this.b;
return 
}}else{I=Math.atan2(D,G)
}A=C/H;
N=L/H;
B=1/Math.sqrt(1-this.es*(2-this.es)*N*N);
a=N*(1-this.es)*B;
W=A*B;
V=0;
do{V++;
K=this.a/Math.sqrt(1-this.es*W*W);
O=L*a+C*W-K*(1-this.es*W*W);
M=this.es*K/(K+O);
B=1/Math.sqrt(1-M*(2-M)*N*N);
J=N*(1-M)*B;
R=A*B;
Q=R*a-J*W;
a=J;
W=R
}while(Q*Q>U&&V<F);
S=Math.atan(R/Math.abs(J));
T.x=I;
T.y=S;
T.z=O;
return T
},geocentric_to_geodetic_noniter:function(R){var D=R.x;
var C=R.y;
var A=R.z?R.z:0;
var G;
var Q;
var L;
var E;
var N;
var P;
var M;
var J;
var H;
var I;
var U;
var F;
var T;
var S;
var O;
var K;
var B;
D=parseFloat(D);
C=parseFloat(C);
A=parseFloat(A);
B=false;
if(D!=0){G=Math.atan2(C,D)
}else{if(C>0){G=Proj4js.common.HALF_PI
}else{if(C<0){G=-Proj4js.common.HALF_PI
}else{B=true;
G=0;
if(A>0){Q=Proj4js.common.HALF_PI
}else{if(A<0){Q=-Proj4js.common.HALF_PI
}else{Q=Proj4js.common.HALF_PI;
L=-this.b;
return 
}}}}}N=D*D+C*C;
E=Math.sqrt(N);
P=A*Proj4js.common.AD_C;
J=Math.sqrt(P*P+N);
I=P/J;
F=E/J;
U=I*I*I;
M=A+this.b*this.ep2*U;
K=E-this.a*this.es*F*F*F;
H=Math.sqrt(M*M+K*K);
T=M/H;
S=K/H;
O=this.a/Math.sqrt(1-this.es*T*T);
if(S>=Proj4js.common.COS_67P5){L=E/S-O
}else{if(S<=-Proj4js.common.COS_67P5){L=E/-S-O
}else{L=A/T+O*(this.es-1)
}}if(B==false){Q=Math.atan(T/S)
}R.x=G;
R.y=Q;
R.z=L;
return R
},geocentric_to_wgs84:function(B){if(this.datum_type==Proj4js.common.PJD_3PARAM){B.x+=this.datum_params[0];
B.y+=this.datum_params[1];
B.z+=this.datum_params[2]
}else{if(this.datum_type==Proj4js.common.PJD_7PARAM){var F=this.datum_params[0];
var D=this.datum_params[1];
var I=this.datum_params[2];
var E=this.datum_params[3];
var J=this.datum_params[4];
var H=this.datum_params[5];
var G=this.datum_params[6];
var C=G*(B.x-H*B.y+J*B.z)+F;
var A=G*(H*B.x+B.y-E*B.z)+D;
var K=G*(-J*B.x+E*B.y+B.z)+I;
B.x=C;
B.y=A;
B.z=K
}}},geocentric_from_wgs84:function(C){if(this.datum_type==Proj4js.common.PJD_3PARAM){C.x-=this.datum_params[0];
C.y-=this.datum_params[1];
C.z-=this.datum_params[2]
}else{if(this.datum_type==Proj4js.common.PJD_7PARAM){var G=this.datum_params[0];
var D=this.datum_params[1];
var J=this.datum_params[2];
var F=this.datum_params[3];
var K=this.datum_params[4];
var I=this.datum_params[5];
var H=this.datum_params[6];
var E=(C.x-G)/H;
var B=(C.y-D)/H;
var A=(C.z-J)/H;
C.x=E+I*B-K*A;
C.y=-I*E+B+F*A;
C.z=K*E-F*B+A
}}}});
Proj4js.Point=Proj4js.Class({initialize:function(A,D,C){if(typeof A=="object"){this.x=A[0];
this.y=A[1];
this.z=A[2]||0
}else{if(typeof A=="string"){var B=A.split(",");
this.x=parseFloat(B[0]);
this.y=parseFloat(B[1]);
this.z=parseFloat(B[2])||0
}else{this.x=A;
this.y=D;
this.z=C||0
}}},clone:function(){return new Proj4js.Point(this.x,this.y,this.z)
},toString:function(){return("x="+this.x+",y="+this.y)
},toShortString:function(){return(this.x+", "+this.y)
}});
Proj4js.PrimeMeridian={greenwich:0,lisbon:-9.131906111111,paris:2.337229166667,bogota:-74.080916666667,madrid:-3.687938888889,rome:12.452333333333,bern:7.439583333333,jakarta:106.807719444444,ferro:-17.666666666667,brussels:4.367975,stockholm:18.058277777778,athens:23.7163375,oslo:10.722916666667};
Proj4js.Ellipsoid={MERIT:{a:6378137,rf:298.257,ellipseName:"MERIT 1983"},SGS85:{a:6378136,rf:298.257,ellipseName:"Soviet Geodetic System 85"},GRS80:{a:6378137,rf:298.257222101,ellipseName:"GRS 1980(IUGG, 1980)"},IAU76:{a:6378140,rf:298.257,ellipseName:"IAU 1976"},airy:{a:6377563.396,b:6356256.91,ellipseName:"Airy 1830"},"APL4.":{a:6378137,rf:298.25,ellipseName:"Appl. Physics. 1965"},NWL9D:{a:6378145,rf:298.25,ellipseName:"Naval Weapons Lab., 1965"},mod_airy:{a:6377340.189,b:6356034.446,ellipseName:"Modified Airy"},andrae:{a:6377104.43,rf:300,ellipseName:"Andrae 1876 (Den., Iclnd.)"},aust_SA:{a:6378160,rf:298.25,ellipseName:"Australian Natl & S. Amer. 1969"},GRS67:{a:6378160,rf:298.247167427,ellipseName:"GRS 67(IUGG 1967)"},bessel:{a:6377397.155,rf:299.1528128,ellipseName:"Bessel 1841"},bess_nam:{a:6377483.865,rf:299.1528128,ellipseName:"Bessel 1841 (Namibia)"},clrk66:{a:6378206.4,b:6356583.8,ellipseName:"Clarke 1866"},clrk80:{a:6378249.145,rf:293.4663,ellipseName:"Clarke 1880 mod."},CPM:{a:6375738.7,rf:334.29,ellipseName:"Comm. des Poids et Mesures 1799"},delmbr:{a:6376428,rf:311.5,ellipseName:"Delambre 1810 (Belgium)"},engelis:{a:6378136.05,rf:298.2566,ellipseName:"Engelis 1985"},evrst30:{a:6377276.345,rf:300.8017,ellipseName:"Everest 1830"},evrst48:{a:6377304.063,rf:300.8017,ellipseName:"Everest 1948"},evrst56:{a:6377301.243,rf:300.8017,ellipseName:"Everest 1956"},evrst69:{a:6377295.664,rf:300.8017,ellipseName:"Everest 1969"},evrstSS:{a:6377298.556,rf:300.8017,ellipseName:"Everest (Sabah & Sarawak)"},fschr60:{a:6378166,rf:298.3,ellipseName:"Fischer (Mercury Datum) 1960"},fschr60m:{a:6378155,rf:298.3,ellipseName:"Fischer 1960"},fschr68:{a:6378150,rf:298.3,ellipseName:"Fischer 1968"},helmert:{a:6378200,rf:298.3,ellipseName:"Helmert 1906"},hough:{a:6378270,rf:297,ellipseName:"Hough"},intl:{a:6378388,rf:297,ellipseName:"International 1909 (Hayford)"},kaula:{a:6378163,rf:298.24,ellipseName:"Kaula 1961"},lerch:{a:6378139,rf:298.257,ellipseName:"Lerch 1979"},mprts:{a:6397300,rf:191,ellipseName:"Maupertius 1738"},new_intl:{a:6378157.5,b:6356772.2,ellipseName:"New International 1967"},plessis:{a:6376523,rf:6355863,ellipseName:"Plessis 1817 (France)"},krass:{a:6378245,rf:298.3,ellipseName:"Krassovsky, 1942"},SEasia:{a:6378155,b:6356773.3205,ellipseName:"Southeast Asia"},walbeck:{a:6376896,b:6355834.8467,ellipseName:"Walbeck"},WGS60:{a:6378165,rf:298.3,ellipseName:"WGS 60"},WGS66:{a:6378145,rf:298.25,ellipseName:"WGS 66"},WGS72:{a:6378135,rf:298.26,ellipseName:"WGS 72"},WGS84:{a:6378137,rf:298.257223563,ellipseName:"WGS 84"},sphere:{a:6370997,b:6370997,ellipseName:"Normal Sphere (r=6370997)"}};
Proj4js.Datum={WGS84:{towgs84:"0,0,0",ellipse:"WGS84",datumName:"WGS84"},GGRS87:{towgs84:"-199.87,74.79,246.62",ellipse:"GRS80",datumName:"Greek_Geodetic_Reference_System_1987"},NAD83:{towgs84:"0,0,0",ellipse:"GRS80",datumName:"North_American_Datum_1983"},NAD27:{nadgrids:"@conus,@alaska,@ntv2_0.gsb,@ntv1_can.dat",ellipse:"clrk66",datumName:"North_American_Datum_1927"},potsdam:{towgs84:"606.0,23.0,413.0",ellipse:"bessel",datumName:"Potsdam Rauenberg 1950 DHDN"},carthage:{towgs84:"-263.0,6.0,431.0",ellipse:"clark80",datumName:"Carthage 1934 Tunisia"},hermannskogel:{towgs84:"653.0,-212.0,449.0",ellipse:"bessel",datumName:"Hermannskogel"},ire65:{towgs84:"482.530,-130.596,564.557,-1.042,-0.214,-0.631,8.15",ellipse:"mod_airy",datumName:"Ireland 1965"},nzgd49:{towgs84:"59.47,-5.04,187.44,0.47,-0.1,1.024,-4.5993",ellipse:"intl",datumName:"New Zealand Geodetic Datum 1949"},OSGB36:{towgs84:"446.448,-125.157,542.060,0.1502,0.2470,0.8421,-20.4894",ellipse:"airy",datumName:"Airy 1830"}};
Proj4js.WGS84=new Proj4js.Proj("WGS84");
Proj4js.Datum.OSB36=Proj4js.Datum.OSGB36;
Proj4js.Try=function(){var D=null;
for(var C=0,A=arguments.length;
C<A;
C++){var B=arguments[C];
try{D=B();
break
}catch(E){}}return D
};
Proj4js.loadScript=function(D,G,B,A){var F={loaded:false,onload:G,onfail:B,loadCheck:A,transport:Proj4js.Try(function(){return new XMLHttpRequest()
},function(){return new ActiveXObject("Msxml2.XMLHTTP")
},function(){return new ActiveXObject("Microsoft.XMLHTTP")
})||null};
if(!F.transport){return 
}if(F.transport.overrideMimeType){F.transport.overrideMimeType("text/xml")
}var E="_tick_="+new Date().getTime();
D+=(D.indexOf("?")+1?"&":"?")+E;
F.transport.open("GET",D,false);
F.transport.onreadystatechange=Proj4js.bind(this.onStateChange,this,F);
var H={"X-Requested-With":"XMLHttpRequest",Accept:"text/javascript, text/html, application/xml, text/xml, */*",Proj4js:true};
for(var C in H){F.transport.setRequestHeader(C,H[C])
}F.transport.send(null);
if(F.transport.overrideMimeType){this.onStateChange(F)
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
Proj4js.defs["IGNF:RGR92GEO"]="+title=Reseau geodesique de la Reunion 1992 +proj=longlat +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:RGSPM06U21"]="+title=Saint-Pierre-et-Miquelon (2006) UTM Fuseau 21 Nord +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-57.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM42SW84"]="+title=World Geodetic System 1984 UTM fuseau 42 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=69.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGR92UTM40S"]="+title=RGR92 UTM fuseau 40 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=57.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGSPM06GEO"]="+title=Saint-Pierre-et-Miquelon (2006) +proj=longlat +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALREU"]="+title=Geoportail - Reunion et dependances +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-21.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.Proj.tmerc={init:function(){this.e0=Proj4js.common.e0fn(this.es);
this.e1=Proj4js.common.e1fn(this.es);
this.e2=Proj4js.common.e2fn(this.es);
this.e3=Proj4js.common.e3fn(this.es);
this.ml0=this.a*Proj4js.common.mlfn(this.e0,this.e1,this.e2,this.e3,this.lat0)
},forward:function(B){var A=B.x;
var K=B.y;
var E=Proj4js.common.adjust_lon(A-this.long0);
var C;
var P,N;
var O=Math.sin(K);
var I=Math.cos(K);
if(this.sphere){var M=I*Math.sin(E);
if((Math.abs(Math.abs(M)-1))<1e-10){Proj4js.reportError("tmerc:forward: Point projects into infinity");
return(93)
}else{P=0.5*this.a*this.k0*Math.log((1+M)/(1-M));
C=Math.acos(I*Math.cos(E)/Math.sqrt(1-M*M));
if(K<0){C=-C
}N=this.a*this.k0*(C-this.lat0)
}}else{var H=I*E;
var G=Math.pow(H,2);
var J=this.ep2*Math.pow(I,2);
var L=Math.tan(K);
var Q=Math.pow(L,2);
C=1-this.es*Math.pow(O,2);
var D=this.a/Math.sqrt(C);
var F=this.a*Proj4js.common.mlfn(this.e0,this.e1,this.e2,this.e3,K);
P=this.k0*D*H*(1+G/6*(1-Q+J+G/20*(5-18*Q+Math.pow(Q,2)+72*J-58*this.ep2)))+this.x0;
N=this.k0*(F-this.ml0+D*L*(G*(0.5+G/24*(5-Q+9*J+4*Math.pow(J,2)+G/30*(61-58*Q+Math.pow(Q,2)+600*J-330*this.ep2)))))+this.y0
}B.x=P;
B.y=N;
return B
},inverse:function(O){var E,C;
var X;
var Q;
var H=6;
var G,D;
if(this.sphere){var T=Math.exp(O.x/(this.a*this.k0));
var S=0.5*(T-1/T);
var V=this.lat0+O.y/(this.a*this.k0);
var R=Math.cos(V);
E=Math.sqrt((1-R*R)/(1+S*S));
G=Proj4js.common.asinz(E);
if(V<0){G=-G
}if((S==0)&&(R==0)){D=this.long0
}else{D=Proj4js.common.adjust_lon(Math.atan2(S,R)+this.long0)
}}else{var J=O.x-this.x0;
var I=O.y-this.y0;
E=(this.ml0+I/this.k0)/this.a;
C=E;
for(Q=0;
true;
Q++){X=((E+this.e1*Math.sin(2*C)-this.e2*Math.sin(4*C)+this.e3*Math.sin(6*C))/this.e0)-C;
C+=X;
if(Math.abs(X)<=Proj4js.common.EPSLN){break
}if(Q>=H){Proj4js.reportError("tmerc:inverse: Latitude failed to converge");
return(95)
}}if(Math.abs(C)<Proj4js.common.HALF_PI){var B=Math.sin(C);
var Y=Math.cos(C);
var K=Math.tan(C);
var W=this.ep2*Math.pow(Y,2);
var F=Math.pow(W,2);
var L=Math.pow(K,2);
var A=Math.pow(L,2);
E=1-this.es*Math.pow(B,2);
var P=this.a/Math.sqrt(E);
var M=P*(1-this.es)/E;
var U=J/(P*this.k0);
var N=Math.pow(U,2);
G=C-(P*K*N/M)*(0.5-N/24*(5+3*L+10*W-4*F-9*this.ep2-N/30*(61+90*L+298*W+45*A-252*this.ep2-3*F)));
D=Proj4js.common.adjust_lon(this.long0+(U*(1-N/6*(1+2*L+W-N/20*(5-2*W+28*L-3*F+8*this.ep2+24*A)))/Y))
}else{G=Proj4js.common.HALF_PI*Proj4js.common.sign(I);
D=this.long0
}}O.x=D;
O.y=G;
return O
}};
Proj4js.defs["IGNF:GEOPORTALMYT"]="+title=Geoportail - Mayotte +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-12.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGM04GEO"]="+title=RGM04 (Reseau Geodesique de Mayotte 2004) +proj=longlat +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:WGS84RRAFGEO"]="+title=Reseau de reference des Antilles francaises (1988-1991) +proj=longlat +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:RGNCUTM57S"]="+title=Reseau Geodesique de Nouvelle-Caledonie - UTM fuseau 57 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=159.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["EPSG:4258"]="+title=ETRS89 +proj=longlat +ellps=GRS80 +no_defs ";
Proj4js.defs["IGNF:UTM22RGFG95"]="+title=RGFG95 UTM fuseau 22 Nord-Guyane +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-51.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=0.000 +units=m +no_defs";
Proj4js.Proj.eqc={init:function(){if(!this.x0){this.x0=0
}if(!this.y0){this.y0=0
}if(!this.lat0){this.lat0=0
}if(!this.long0){this.long0=0
}if(!this.lat_ts){this.lat_ts=0
}if(!this.title){this.title="Equidistant Cylindrical (Plate Carre)"
}this.rc=Math.cos(this.lat_ts)
},forward:function(D){var E=D.x;
var C=D.y;
var B=Proj4js.common.adjust_lon(E-this.long0);
var A=Proj4js.common.adjust_lat(C-this.lat0);
D.x=this.x0+(this.a*B*this.rc);
D.y=this.y0+(this.a*A);
return D
},inverse:function(B){var A=B.x;
var C=B.y;
B.x=Proj4js.common.adjust_lon(this.long0+((A-this.x0)/(this.a*this.rc)));
B.y=Proj4js.common.adjust_lat(this.lat0+((C-this.y0)/(this.a)));
return B
}};
Proj4js.defs["IGNF:RGPFGEO"]="+title=RGPF (Reseau Geodesique de Polynesie Francaise) +proj=longlat +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALANF"]="+title=Geoportail - Antilles francaises +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=15.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["CRS:84"]="+title=WGS 84 longitude-latitude +proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs ";
Proj4js.defs["IGNF:WGS84G"]="+title=World Geodetic System 1984 +proj=longlat +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:RGFG95GEO"]="+title=Reseau geodesique francais de Guyane 1995 +proj=longlat +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM39SW84"]="+title=World Geodetic System 1984 UTM fuseau 39 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=51.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALKER"]="+title=Geoportail - Kerguelen +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-49.500000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGNCUTM58S"]="+title=Reseau Geodesique de Nouvelle-Caledonie - UTM fuseau 58 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=165.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.Proj.merc={init:function(){if(this.lat_ts){if(this.sphere){this.k0=Math.cos(this.lat_ts)
}else{this.k0=Proj4js.common.msfnz(this.es,Math.sin(this.lat_ts),Math.cos(this.lat_ts))
}}},forward:function(E){var F=E.x;
var D=E.y;
if(D*Proj4js.common.R2D>90&&D*Proj4js.common.R2D<-90&&F*Proj4js.common.R2D>180&&F*Proj4js.common.R2D<-180){Proj4js.reportError("merc:forward: llInputOutOfRange: "+F+" : "+D);
return null
}var A,G;
if(Math.abs(Math.abs(D)-Proj4js.common.HALF_PI)<=Proj4js.common.EPSLN){Proj4js.reportError("merc:forward: ll2mAtPoles");
return null
}else{if(this.sphere){A=this.x0+this.a*this.k0*Proj4js.common.adjust_lon(F-this.long0);
G=this.y0+this.a*this.k0*Math.log(Math.tan(Proj4js.common.FORTPI+0.5*D))
}else{var C=Math.sin(D);
var B=Proj4js.common.tsfnz(this.e,D,C);
A=this.x0+this.a*this.k0*Proj4js.common.adjust_lon(F-this.long0);
G=this.y0-this.a*this.k0*Math.log(B)
}E.x=A;
E.y=G;
return E
}},inverse:function(D){var A=D.x-this.x0;
var F=D.y-this.y0;
var E,C;
if(this.sphere){C=Proj4js.common.HALF_PI-2*Math.atan(Math.exp(-F/this.a*this.k0))
}else{var B=Math.exp(-F/(this.a*this.k0));
C=Proj4js.common.phi2z(this.e,B);
if(C==-9999){Proj4js.reportError("merc:inverse: lat = -9999");
return null
}}E=Proj4js.common.adjust_lon(this.long0+A/(this.a*this.k0));
D.x=E;
D.y=C;
return D
}};
Proj4js.defs["IGNF:GEOPORTALFXX"]="+title=Geoportail - France metropolitaine +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=46.500000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGM04UTM38S"]="+title=UTM fuseau 38 Sud (Reseau Geodesique de Mayotte 2004) +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=45.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALPYF"]="+title=Geoportail - Polynesie francaise +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-15.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:32662"]="+title=WGS 84 / Plate Carree +proj=eqc +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +ellps=WGS84 +datum=WGS84 +units=m +no_defs ";
Proj4js.defs["IGNF:RGF93G"]="+title=Reseau geodesique francais 1993 +proj=longlat +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:MILLER"]="+title=Geoportail - Monde +proj=mill +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lon_0=0.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["EPSG:4171"]="+title=RGF93 +proj=longlat +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +no_defs ";
Proj4js.defs["IGNF:RGNCUTM59S"]="+title=Reseau Geodesique de Nouvelle-Caledonie - UTM fuseau 59 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=171.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALGUF"]="+title=Geoportail - Guyane +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=4.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALCRZ"]="+title=Geoportail - Crozet +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-46.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM20W84MART"]="+title=World Geodetic System 1984 UTM fuseau 20 Nord-Martinique +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-63.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGNCGEO"]="+title=Reseau Geodesique de Nouvelle-Caledonie +proj=longlat +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM01SW84"]="+title=World Geodetic System 1984 UTM fuseau 01 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-177.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGPFUTM5S"]="+title=RGPF - UTM fuseau 5 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-153.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGPFUTM6S"]="+title=RGPF - UTM fuseau 6 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-147.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:UTM20W84GUAD"]="+title=World Geodetic System 1984 UTM fuseau 20 Nord-Guadeloupe +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-63.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:RGPFUTM7S"]="+title=RGPF - UTM fuseau 7 Sud +proj=tmerc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=-141.000000000 +k_0=0.99960000 +x_0=500000.000 +y_0=10000000.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALNCL"]="+title=Geoportail - Nouvelle-Caledonie +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-22.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.defs["IGNF:LAMB93"]="+title=Lambert 93 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=46.500000000 +lon_0=3.000000000 +lat_1=44.000000000 +lat_2=49.000000000 +x_0=700000.000 +y_0=6600000.000 +units=m +no_defs";
Proj4js.defs["IGNF:GEOPORTALSPM"]="+title=Geoportail - Saint-Pierre et Miquelon +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=47.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.Proj.mill={init:function(){},forward:function(D){var E=D.x;
var C=D.y;
var B=Proj4js.common.adjust_lon(E-this.long0);
var A=this.x0+this.a*B;
var F=this.y0+this.a*Math.log(Math.tan((Proj4js.common.PI/4)+(C/2.5)))*1.25;
D.x=A;
D.y=F;
return D
},inverse:function(B){B.x-=this.x0;
B.y-=this.y0;
var C=Proj4js.common.adjust_lon(this.long0+B.x/this.a);
var A=2.5*(Math.atan(Math.exp(0.8*B.y/this.a))-Proj4js.common.PI/4);
B.x=C;
B.y=A;
return B
}};
Proj4js.defs["IGNF:GEOPORTALWLF"]="+title=Geoportail - Wallis et Futuna +proj=eqc +nadgrids=null +towgs84=0.0000,0.0000,0.0000,0.0000,0.0000,0.0000,0.000000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=0.000000000 +lon_0=0.000000000 +lat_ts=-14.000000000 +x_0=0.000 +y_0=0.000 +units=m +no_defs";
Proj4js.Proj.lcc={init:function(){if(!this.lat2){this.lat2=this.lat0
}if(!this.k0){this.k0=1
}if(Math.abs(this.lat1+this.lat2)<Proj4js.common.EPSLN){Proj4js.reportError("lcc:init: Equal Latitudes");
return 
}var J=this.b/this.a;
this.e=Math.sqrt(1-J*J);
var G=Math.sin(this.lat1);
var E=Math.cos(this.lat1);
var I=Proj4js.common.msfnz(this.e,G,E);
var B=Proj4js.common.tsfnz(this.e,this.lat1,G);
var F=Math.sin(this.lat2);
var D=Math.cos(this.lat2);
var H=Proj4js.common.msfnz(this.e,F,D);
var A=Proj4js.common.tsfnz(this.e,this.lat2,F);
var C=Proj4js.common.tsfnz(this.e,this.lat0,Math.sin(this.lat0));
if(Math.abs(this.lat1-this.lat2)>Proj4js.common.EPSLN){this.ns=Math.log(I/H)/Math.log(B/A)
}else{this.ns=G
}this.f0=I/(this.ns*Math.pow(B,this.ns));
this.rh=this.a*this.f0*Math.pow(C,this.ns);
if(!this.title){this.title="Lambert Conformal Conic"
}},forward:function(E){var F=E.x;
var D=E.y;
if(D<=90&&D>=-90&&F<=180&&F>=-180){}else{Proj4js.reportError("lcc:forward: llInputOutOfRange: "+F+" : "+D);
return null
}var A=Math.abs(Math.abs(D)-Proj4js.common.HALF_PI);
var C,G;
if(A>Proj4js.common.EPSLN){C=Proj4js.common.tsfnz(this.e,D,Math.sin(D));
G=this.a*this.f0*Math.pow(C,this.ns)
}else{A=D*this.ns;
if(A<=0){Proj4js.reportError("lcc:forward: No Projection");
return null
}G=0
}var B=this.ns*Proj4js.common.adjust_lon(F-this.long0);
E.x=this.k0*(G*Math.sin(B))+this.x0;
E.y=this.k0*(this.rh-G*Math.cos(B))+this.y0;
return E
},inverse:function(E){var G,A,C;
var D,F;
x=(E.x-this.x0)/this.k0;
y=(this.rh-(E.y-this.y0)/this.k0);
if(this.ns>0){G=Math.sqrt(x*x+y*y);
A=1
}else{G=-Math.sqrt(x*x+y*y);
A=-1
}var B=0;
if(G!=0){B=Math.atan2((A*x),(A*y))
}if((G!=0)||(this.ns>0)){A=1/this.ns;
C=Math.pow((G/(this.a*this.f0)),A);
D=Proj4js.common.phi2z(this.e,C);
if(D==-9999){return null
}}else{D=-Proj4js.common.HALF_PI
}F=Proj4js.common.adjust_lon(B/this.ns+this.long0);
E.x=F;
E.y=D;
return E
}};
OpenLayers.INCHES_PER_UNIT.deg=OpenLayers.INCHES_PER_UNIT.degre=OpenLayers.INCHES_PER_UNIT.degree=OpenLayers.INCHES_PER_UNIT.dd;
OpenLayers.INCHES_PER_UNIT.meters=OpenLayers.INCHES_PER_UNIT.meter=OpenLayers.INCHES_PER_UNIT.metres=OpenLayers.INCHES_PER_UNIT.metre=OpenLayers.INCHES_PER_UNIT.m;
OpenLayers.Util.getResolutionFromScale=function(D,A){if(!A||OpenLayers.INCHES_PER_UNIT[A]==undefined){A="degrees"
}var C=OpenLayers.Util.normalizeScale(D);
var B=1/(C*OpenLayers.INCHES_PER_UNIT[A]*OpenLayers.DOTS_PER_INCH);
return B
};
OpenLayers.Util.getScaleFromResolution=function(B,A){if(!A||OpenLayers.INCHES_PER_UNIT[A]==undefined){A="degrees"
}var C=B*OpenLayers.INCHES_PER_UNIT[A]*OpenLayers.DOTS_PER_INCH;
return C
};
OpenLayers.Util.modifyAlphaImageDiv=function(A,B,J,I,G,F,C,D,H){OpenLayers.Util.modifyDOMElement(A,B,J,I,null,null,null,H);
var E=A.childNodes[0];
if(G){E.src=G
}OpenLayers.Util.modifyDOMElement(E,A.id+"_innerImage",null,I,"relative",C);
if(H){A.style.opacity=H;
A.style.filter="alpha(opacity="+(H*100)+")"
}if(OpenLayers.Util.alphaHack()&&G){var K=G.toLowerCase().substr(G.length-4,4);
if(K==".png"){if(A.style.display!="none"){A.style.display="inline-block"
}if(D==null){D="scale"
}A.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+E.src+"', sizingMethod='"+D+"')";
if(A.style.opacity&&parseFloat(A.style.opacity)>=0&&parseFloat(A.style.opacity)<1){A.style.filter+="alpha(opacity="+A.style.opacity*100+")"
}E.style.filter="alpha(opacity=0)"
}}};
OpenLayers.Util.getParameters=function(B){B=B||window.location.href;
var A="";
if(OpenLayers.String.contains(B,"?")){var C=B.indexOf("?")+1;
var E=OpenLayers.String.contains(B,"#")?B.indexOf("#"):B.length;
A=B.substring(C,E)
}var M={};
var D=A.split(/[&;]/);
for(var H=0,I=D.length;
H<I;
++H){var G=D[H].split("=");
if(G[0]){var K=decodeURIComponent(G[0]);
var J=G[1]||"";
J=J.split(",");
for(var F=0,L=J.length;
F<L;
F++){J[F]=decodeURIComponent(unescape(J[F]))
}if(J.length==1){J=J[0]
}M[K]=J
}}return M
};
OpenLayers.Projection.prototype.domainOfValidity=null;
OpenLayers.Projection.prototype.initialize=function(B,A){OpenLayers.Util.extend(this,A);
this.projCode=B;
this.options=A;
if(window.Proj4js){this.proj=new Proj4js.Proj(B)
}};
OpenLayers.Projection.prototype.clone=function(){return new OpenLayers.Projection(this.projCode,this.options)
};
OpenLayers.Projection.prototype.equals=function(B){var A=false;
if(B){var C=B instanceof OpenLayers.Projection?B.getCode():B;
if(this.getCode()==C){A=true
}else{A=this.isAliasOf(B)
}}return A
};
OpenLayers.Projection.WKALIASES={WGS84G:["EPSG:4326","CRS:84","IGNF:WGS84G","IGNF:WGS84RRAFGEO","IGNF:RGF93G","IGNF:RGFG95GEO","IGNF:RGM04GEO","IGNF:RGNCGEO","IGNF:RGPFGEO","IGNF:RGR92GEO","IGNF:RGSPM06GEO"],LAMB93:["IGNF:LAMB93","EPSG:2154"],LAMBE:["IGNF:LAMBE","EPSG:27572","EPSG:27582"]};
OpenLayers.Projection.prototype.isAliasOf=function(B){for(var D in OpenLayers.Projection.WKALIASES){if(OpenLayers.Projection.WKALIASES.hasOwnProperty(D)){var C=OpenLayers.Projection.WKALIASES[D];
var G=false;
var F=this.getCode();
for(var E=0,A=C.length;
E<A;
E++){if(F==C[E]){G=true;
break
}}if(G){G=false;
F=B instanceof OpenLayers.Projection?B.getCode():B;
for(var E=0,A=C.length;
E<A;
E++){if(F==C[E]){G=true;
break
}}}if(G){return true
}}}return false
};
OpenLayers.Projection.CRS84=window.Proj4js?new OpenLayers.Projection("WGS84",{domainOfValidity:new OpenLayers.Bounds(-180,-90,180,90)}):undefined;
OpenLayers.Projection.prototype.getTitle=function(){return this.proj?this.proj.title:""
};
OpenLayers.Projection.prototype.isCompatibleWith=function(B){var A=false;
if(B){if(this.equals(B)){A=true
}else{var C=B instanceof OpenLayers.Projection?B:new OpenLayers.Projection(B);
if(this.proj.projName==C.proj.projName){A=this.proj.datum.compare_datums(C.proj.datum)
}else{if((this.proj.projName=="eqc"&&C.proj.projName=="longlat")||(this.proj.projName=="longlat"&&C.proj.projName=="eqc")){A=this.proj.datum.compare_datums(C.proj.datum)
}}if(!(C==B)){C.destroy();
C=null
}}}return A
};
OpenLayers.Projection.transform=function(A,C,B){if(C&&B){if(C.proj&&B.proj){if(!C.equals(B)){A=Proj4js.transform(C.proj,B.proj,A)
}}else{if(OpenLayers.Projection.transforms[C.getCode()]&&OpenLayers.Projection.transforms[C.getCode()][B.getCode()]){OpenLayers.Projection.transforms[C.getCode()][B.getCode()](A)
}}}return A
};
OpenLayers.Bounds.prototype.transform=function(M,G,Q){if(!Q){var I=OpenLayers.Projection.transform({x:this.left,y:this.bottom},M,G);
var F=OpenLayers.Projection.transform({x:this.right,y:this.top},M,G);
this.left=I.x<F.x?I.x:F.x;
this.bottom=I.y<F.y?I.y:F.y;
this.right=F.x>I.x?F.x:I.x;
this.top=F.y>I.y?F.y:I.y;
return this
}var R=G.proj.projName=="longlat"?0.000028:1;
var B,D,S,K;
var C=1;
for(var O=0;
O<10;
O++){var J=(this.right-this.left)/(1*C);
var H=(this.top-this.bottom)/(1*C);
var L;
var A,P,E,T;
for(var N=0;
N<C;
N++){L=OpenLayers.Projection.transform({x:this.left+N*J,y:this.bottom},M,G);
if(A==undefined){A=L.x;
P=L.y;
E=L.x,T=L.y
}else{if(L.x<A){A=L.x
}if(L.y<P){P=L.y
}if(L.x>E){E=L.x
}if(L.y>T){T=L.y
}}L=OpenLayers.Projection.transform({x:this.right,y:this.bottom+N*H},M,G);
if(L.x<A){A=L.x
}if(L.y<P){P=L.y
}if(L.x>E){E=L.x
}if(L.y>T){T=L.y
}L=OpenLayers.Projection.transform({x:this.right-N*J,y:this.top},M,G);
if(L.x<A){A=L.x
}if(L.y<P){P=L.y
}if(L.x>E){E=L.x
}if(L.y>T){T=L.y
}L=OpenLayers.Projection.transform({x:this.left,y:this.top-N*H},M,G);
if(L.x<A){A=L.x
}if(L.y<P){P=L.y
}if(L.x>E){E=L.x
}if(L.y>T){T=L.y
}}if(B!=undefined&&Math.abs(A-B)<R&&Math.abs(P-D)<R&&Math.abs(E-S)<R&&Math.abs(T-K)<R){this.left=A;
this.bottom=P;
this.right=E;
this.top=T;
return this
}B=A;
D=P;
S=E;
K=T;
C*=2
}this.left=B;
this.bottom=D;
this.right=S;
this.top=K;
return this
};
OpenLayers.LonLat.prototype.equals=function(E,D){var C=false;
if(!D){D=0.000001
}if(E!=null){var B=(!isNaN(this.lon)&&!isNaN(E.lon))?Math.abs(this.lon-E.lon):1;
var A=(!isNaN(this.lat)&&!isNaN(E.lat))?Math.abs(this.lat-E.lat):1;
C=((B<=D&&A<=D)||(isNaN(this.lon)&&isNaN(this.lat)&&isNaN(E.lon)&&isNaN(E.lat)))
}return C
};
OpenLayers.Map.prototype.isValidZoomLevel=function(B){var A=(B!=null);
A=A&&(B>=0);
A=A&&(B<this.getNumZoomLevels());
if(this.minZoomLevel!=undefined){A=A&&(B>=this.minZoomLevel)
}if(this.maxZoomLevel!=undefined){A=A&&(B<=this.maxZoomLevel)
}return A
};
OpenLayers.Map.prototype.getProjectionObject=OpenLayers.Map.prototype.getProjection=function(){var A=null;
if(this.baseLayer!=null){A=this.baseLayer.projection
}return A?A:null
};
OpenLayers.Map.prototype.getDisplayProjection=function(){var A=null;
if(this.displayProjection){A=this.displayProjection
}else{A=this.getProjection()
}return A
};
OpenLayers.Map.prototype.getMaxExtent=function(B){var A=null;
if(B&&B.restricted&&this.restrictedExtent){A=this.restrictedExtent
}else{if(this.baseLayer!=null){A=this.baseLayer.maxExtent
}}if(!A){this.maxExtent=new OpenLayers.Bounds(-180,-90,180,90);
if(this.getProjection()){this.maxExtent.transform(OpenLayers.Projection.CRS84,this.getProjection())
}A=this.maxExtent
}return A
};
OpenLayers.Map.prototype.getCenter=function(){var A=null;
if(this.center){A=this.center.clone()
}return A
};
OpenLayers.Layer.GML.prototype.getNativeProjection=OpenLayers.Layer.Vector.prototype.getNativeProjection=OpenLayers.Layer.WMS.Untiled.prototype.getNativeProjection=OpenLayers.Layer.WMS.prototype.getNativeProjection=OpenLayers.Layer.Grid.prototype.getNativeProjection=OpenLayers.Layer.HTTPRequest.prototype.getNativeProjection=OpenLayers.Layer.prototype.getNativeProjection=function(){if(this.isBaseLayer){this.projection=this.projection||this.nativeProjection
}else{this.projection=this.projection||this.map.getProjection()
}if(this.projection&&typeof (this.projection)=="string"){this.projection=new OpenLayers.Projection(this.projection)
}return this.projection
};
OpenLayers.Layer.Grid.prototype.mergeNewParams=OpenLayers.Layer.HTTPRequest.prototype.mergeNewParams=function(A){this.params=OpenLayers.Util.extend(this.params,A);
if(this.GeoRM){OpenLayers.Util.extend(this.params,this.GeoRM.token)
}return this.redraw()
};
OpenLayers.Layer.Grid.prototype.getFullRequestString=OpenLayers.Layer.HTTPRequest.prototype.getFullRequestString=function(F,E){var B=E||this.url;
var G=OpenLayers.Util.extend({},this.params);
G=OpenLayers.Util.extend(G,F);
if(this.GeoRM){OpenLayers.Util.extend(G,this.GeoRM.token)
}var A=OpenLayers.Util.getParameterString(G);
if(B instanceof Array){B=this.selectUrl(A,B)
}var D=OpenLayers.Util.upperCaseObject(OpenLayers.Util.getParameters(B));
for(var H in G){if(H.toUpperCase() in D){delete G[H]
}}A=OpenLayers.Util.getParameterString(G);
var I=B;
if(A!=""){var C=B.charAt(B.length-1);
if((C=="&")||(C=="?")){I+=A
}else{if(B.indexOf("?")==-1){I+="?"+A
}else{I+="&"+A
}}}return I
};
OpenLayers.Layer.WMS.prototype.getFullRequestString=function(B){var A=this.getNativeProjection();
this.params.SRS=(A==null)?null:A.getCode();
return OpenLayers.Layer.Grid.prototype.getFullRequestString.apply(this,arguments)
};
OpenLayers.Layer.WMS.prototype.getURL=function(B){var D=B.clone();
D=this.adjustBounds(D);
if(this.getNativeProjection()){D.transform(this.map.getProjection(),this.getNativeProjection())
}var C=this.getImageSize();
var E={BBOX:this.encodeBBOX?D.toBBOX():D.toArray(),WIDTH:C.w,HEIGHT:C.h};
var A=this.getFullRequestString(E);
return A
};
OpenLayers.Layer.WMS.prototype.getDataExtent=function(){return this.maxExtent
};
OpenLayers.Renderer.VML.prototype.getResolution=OpenLayers.Renderer.SVG.prototype.getResolution=OpenLayers.Renderer.Elements.prototype.getResolution=OpenLayers.Renderer.Canvas.prototype.getResolution=OpenLayers.Renderer.prototype.getResolution=function(){this.resolution=this.resolution||(this.map?this.map.getResolution():null);
return this.resolution
};
OpenLayers.Renderer.VML.prototype.setExtent=function(C,D){OpenLayers.Renderer.Elements.prototype.setExtent.apply(this,arguments);
var A=this.getResolution();
if(A==null){return false
}var F=C.left/A;
var E=C.top/A-this.size.h;
if(D){this.offset={x:F,y:E};
F=0;
E=0
}else{F=F-this.offset.x;
E=E-this.offset.y
}var G=F+" "+E;
this.root.setAttribute("coordorigin",G);
var B=this.size.w+" "+this.size.h;
this.root.setAttribute("coordsize",B);
this.root.style.flip="y";
return true
};
OpenLayers.Format.XML.prototype.read=function(D){var B=D.indexOf("<");
if(B>0){D=D.substring(B)
}var C=OpenLayers.Util.Try(OpenLayers.Function.bind((function(){var E;
if(window.ActiveXObject&&!this.xmldom){E=new ActiveXObject("Microsoft.XMLDOM")
}else{E=this.xmldom
}E.loadXML(D);
return E
}),this),function(){return new DOMParser().parseFromString(D,"text/xml")
},function(){var E=new XMLHttpRequest();
E.open("GET","data:text/xml;charset=utf-8,"+encodeURIComponent(D),false);
if(E.overrideMimeType){E.overrideMimeType("text/xml")
}E.send(null);
return E.responseXML
});
var A=OpenLayers.Request.XMLHttpRequest.getParseErrorText(C);
if(A!=OpenLayers.Request.XMLHttpRequest.PARSED_OK&&A!=OpenLayers.Request.XMLHttpRequest.PARSED_EMPTY){alert(OpenLayers.i18n(A))
}return C
};
OpenLayers.Format.XML.prototype.write=function(B){var C;
if(B.xml!=undefined){C=B.xml
}else{var A=new XMLSerializer();
if(B.nodeType==1){var D=document.implementation.createDocument("","",null);
if(D.importNode){B=D.importNode(B,true)
}D.appendChild(B);
C=A.serializeToString(D)
}else{C=A.serializeToString(B)
}}return C
};
OpenLayers.Format.KML.prototype.setAttributeNS=OpenLayers.Format.XML.prototype.setAttributeNS=function(D,C,A,E){if(E==null||E==undefined){E=""
}if(D.setAttributeNS){D.setAttributeNS(C,A,E)
}else{if(this.xmldom){if(C){var B=D.ownerDocument.createNode(2,A,C);
B.nodeValue=E;
D.setAttributeNode(B)
}else{D.setAttribute(A,E)
}}else{throw OpenLayers.i18n("xml.setattributens")
}}};
OpenLayers.Format.KML.prototype.externalProjection=new OpenLayers.Projection("EPSG:4326",{domainOfValidity:new OpenLayers.Bounds(-180,-90,180,90)});
OpenLayers.Format.KML.prototype.parseAttributes=function(D){var E={};
var B,J,I;
var C=D.childNodes;
for(var F=0,G=C.length;
F<G;
++F){B=C[F];
if(B.nodeType==1){J=B.childNodes;
if(1<=J.length&&J.length<=3){var I;
switch(J.length){case 1:I=J[0];
break;
case 2:if(J[0].nodeType==4){I=J[0];
break
}I=J[1];
break;
case 3:default:I=J[1];
break
}if(I.nodeType==3||I.nodeType==4){var A=(B.prefix)?B.nodeName.split(":")[1]:B.nodeName;
var H=OpenLayers.Util.getXmlNodeValue(I);
if(H){H=H.replace(this.regExes.trimSpace,"");
E[A]=H
}}}}}return E
};
var Geoportal={singleFile:true};
(function(){var G=(typeof (Geoportal)=="object"&&Geoportal.singleFile);
window.Geoportal={_scriptName:(!G)?"lib/Geoportal.js":"GeoportalMin.js",_scriptLocation:null,_getScriptLocation:function(){if(Geoportal._scriptLocation){return Geoportal._scriptLocation
}var P=Geoportal._scriptName;
var O=P.length;
var K=document.getElementsByTagName("script");
for(var M=0;
M<K.length;
M++){var Q=K[M].getAttribute("src");
if(Q){var L=Q.lastIndexOf(P);
var N=Q.lastIndexOf("?");
if(N<0){N=Q.length
}if((L>-1)&&(L+O==N)){Geoportal._scriptLocation=Q.slice(0,-O);
break
}}}return Geoportal._scriptLocation||""
}};
if(!G){var H=["../../openlayers/lib/OpenLayers/SingleFile.js","../../openlayers/lib/OpenLayers.js","../../openlayers/lib/OpenLayers/BaseTypes.js","../../openlayers/lib/OpenLayers/BaseTypes/Class.js","../../openlayers/lib/OpenLayers/Util.js","../../openlayers/lib/Rico/Corner.js","../../openlayers/lib/Gears/gears_init.js","../../openlayers/lib/OpenLayers/BaseTypes/Bounds.js","../../openlayers/lib/OpenLayers/BaseTypes/Element.js","../../openlayers/lib/OpenLayers/BaseTypes/LonLat.js","../../openlayers/lib/OpenLayers/BaseTypes/Pixel.js","../../openlayers/lib/OpenLayers/BaseTypes/Size.js","../../openlayers/lib/OpenLayers/Console.js","../../openlayers/lib/OpenLayers/Control.js","../../openlayers/lib/OpenLayers/Icon.js","../../openlayers/lib/OpenLayers/Lang.js","../../openlayers/lib/OpenLayers/Popup.js","../../openlayers/lib/OpenLayers/Protocol.js","../../openlayers/lib/OpenLayers/Renderer.js","../../openlayers/lib/OpenLayers/Request.js","../../openlayers/lib/OpenLayers/Strategy.js","../../openlayers/lib/OpenLayers/Tween.js","../../openlayers/lib/Rico/Color.js","../../openlayers/lib/OpenLayers/Control/ArgParser.js","../../openlayers/lib/OpenLayers/Control/Attribution.js","../../openlayers/lib/OpenLayers/Control/Button.js","../../openlayers/lib/OpenLayers/Control/LayerSwitcher.js","../../openlayers/lib/OpenLayers/Control/MouseDefaults.js","../../openlayers/lib/OpenLayers/Control/MousePosition.js","../../openlayers/lib/OpenLayers/Control/NavigationHistory.js","../../openlayers/lib/OpenLayers/Control/Pan.js","../../openlayers/lib/OpenLayers/Control/PanZoom.js","../../openlayers/lib/OpenLayers/Control/Panel.js","../../openlayers/lib/OpenLayers/Control/Scale.js","../../openlayers/lib/OpenLayers/Control/ScaleLine.js","../../openlayers/lib/OpenLayers/Control/ZoomIn.js","../../openlayers/lib/OpenLayers/Control/ZoomOut.js","../../openlayers/lib/OpenLayers/Control/ZoomToMaxExtent.js","../../openlayers/lib/OpenLayers/Events.js","../../openlayers/lib/OpenLayers/Format.js","../../openlayers/lib/OpenLayers/Lang/en.js","../../openlayers/lib/OpenLayers/Lang/it.js","../../openlayers/lib/OpenLayers/Popup/Anchored.js","../../openlayers/lib/OpenLayers/Projection.js","../../openlayers/lib/OpenLayers/Protocol/SQL.js","../../openlayers/lib/OpenLayers/Renderer/Canvas.js","../../openlayers/lib/OpenLayers/Renderer/Elements.js","../../openlayers/lib/OpenLayers/Request/XMLHttpRequest.js","../../openlayers/lib/OpenLayers/Strategy/Cluster.js","../../openlayers/lib/OpenLayers/Strategy/Fixed.js","../../openlayers/lib/OpenLayers/Strategy/Paging.js","../../openlayers/lib/OpenLayers/Tile.js","../../openlayers/lib/OpenLayers/Ajax.js","../../openlayers/lib/OpenLayers/Control/MouseToolbar.js","../../openlayers/lib/OpenLayers/Control/PanPanel.js","../../openlayers/lib/OpenLayers/Control/PanZoomBar.js","../../openlayers/lib/OpenLayers/Control/Permalink.js","../../openlayers/lib/OpenLayers/Control/ZoomPanel.js","../../openlayers/lib/OpenLayers/Format/JSON.js","../../openlayers/lib/OpenLayers/Format/XML.js","../../openlayers/lib/OpenLayers/Handler.js","../../openlayers/lib/OpenLayers/Lang/de.js","../../openlayers/lib/OpenLayers/Map.js","../../openlayers/lib/OpenLayers/Marker.js","../../openlayers/lib/OpenLayers/Popup/AnchoredBubble.js","../../openlayers/lib/OpenLayers/Popup/Framed.js","../../openlayers/lib/OpenLayers/Renderer/SVG.js","../../openlayers/lib/OpenLayers/Renderer/VML.js","../../openlayers/lib/OpenLayers/Tile/Image.js","../../openlayers/lib/OpenLayers/Tile/WFS.js","../../openlayers/lib/OpenLayers/Control/OverviewMap.js","../../openlayers/lib/OpenLayers/Feature.js","../../openlayers/lib/OpenLayers/Format/WMC.js","../../openlayers/lib/OpenLayers/Format/WMC/v1.js","../../openlayers/lib/OpenLayers/Handler/Click.js","../../openlayers/lib/OpenLayers/Handler/Drag.js","../../openlayers/lib/OpenLayers/Handler/Feature.js","../../openlayers/lib/OpenLayers/Handler/Hover.js","../../openlayers/lib/OpenLayers/Handler/Keyboard.js","../../openlayers/lib/OpenLayers/Handler/MouseWheel.js","../../openlayers/lib/OpenLayers/Layer.js","../../openlayers/lib/OpenLayers/Marker/Box.js","../../openlayers/lib/OpenLayers/Popup/FramedCloud.js","../../openlayers/lib/OpenLayers/Control/DragFeature.js","../../openlayers/lib/OpenLayers/Control/DragPan.js","../../openlayers/lib/OpenLayers/Control/KeyboardDefaults.js","../../openlayers/lib/OpenLayers/Feature/Vector.js","../../openlayers/lib/OpenLayers/Feature/WFS.js","../../openlayers/lib/OpenLayers/Format/WMC/v1_0_0.js","../../openlayers/lib/OpenLayers/Format/WMC/v1_1_0.js","../../openlayers/lib/OpenLayers/Handler/Box.js","../../openlayers/lib/OpenLayers/Handler/RegularPolygon.js","../../openlayers/lib/OpenLayers/Layer/EventPane.js","../../openlayers/lib/OpenLayers/Layer/FixedZoomLevels.js","../../openlayers/lib/OpenLayers/Layer/HTTPRequest.js","../../openlayers/lib/OpenLayers/Layer/Image.js","../../openlayers/lib/OpenLayers/Layer/Markers.js","../../openlayers/lib/OpenLayers/Layer/SphericalMercator.js","../../openlayers/lib/OpenLayers/Control/DrawFeature.js","../../openlayers/lib/OpenLayers/Control/Measure.js","../../openlayers/lib/OpenLayers/Control/SelectFeature.js","../../openlayers/lib/OpenLayers/Control/ZoomBox.js","../../openlayers/lib/OpenLayers/Format/WKT.js","../../openlayers/lib/OpenLayers/Layer/Boxes.js","../../openlayers/lib/OpenLayers/Layer/GeoRSS.js","../../openlayers/lib/OpenLayers/Layer/Google.js","../../openlayers/lib/OpenLayers/Layer/Grid.js","../../openlayers/lib/OpenLayers/Layer/MultiMap.js","../../openlayers/lib/OpenLayers/Layer/Text.js","../../openlayers/lib/OpenLayers/Layer/VirtualEarth.js","../../openlayers/lib/OpenLayers/Layer/Yahoo.js","../../openlayers/lib/OpenLayers/Protocol/HTTP.js","../../openlayers/lib/OpenLayers/Style.js","../../openlayers/lib/OpenLayers/Control/ModifyFeature.js","../../openlayers/lib/OpenLayers/Control/Navigation.js","../../openlayers/lib/OpenLayers/Filter.js","../../openlayers/lib/OpenLayers/Geometry.js","../../openlayers/lib/OpenLayers/Layer/KaMap.js","../../openlayers/lib/OpenLayers/Layer/MapGuide.js","../../openlayers/lib/OpenLayers/Layer/MapServer.js","../../openlayers/lib/OpenLayers/Layer/TMS.js","../../openlayers/lib/OpenLayers/Layer/TileCache.js","../../openlayers/lib/OpenLayers/Layer/WMS.js","../../openlayers/lib/OpenLayers/Layer/WorldWind.js","../../openlayers/lib/OpenLayers/Protocol/SQL/Gears.js","../../openlayers/lib/OpenLayers/Rule.js","../../openlayers/lib/OpenLayers/StyleMap.js","../../openlayers/lib/OpenLayers/Control/NavToolbar.js","../../openlayers/lib/OpenLayers/Filter/Comparison.js","../../openlayers/lib/OpenLayers/Filter/FeatureId.js","../../openlayers/lib/OpenLayers/Filter/Logical.js","../../openlayers/lib/OpenLayers/Filter/Spatial.js","../../openlayers/lib/OpenLayers/Geometry/Collection.js","../../openlayers/lib/OpenLayers/Geometry/Point.js","../../openlayers/lib/OpenLayers/Geometry/Rectangle.js","../../openlayers/lib/OpenLayers/Geometry/Surface.js","../../openlayers/lib/OpenLayers/Layer/KaMapCache.js","../../openlayers/lib/OpenLayers/Layer/MapServer/Untiled.js","../../openlayers/lib/OpenLayers/Layer/Vector.js","../../openlayers/lib/OpenLayers/Layer/WMS/Untiled.js","../../openlayers/lib/OpenLayers/Format/Filter.js","../../openlayers/lib/OpenLayers/Format/SLD.js","../../openlayers/lib/OpenLayers/Format/Text.js","../../openlayers/lib/OpenLayers/Geometry/MultiLineString.js","../../openlayers/lib/OpenLayers/Geometry/MultiPoint.js","../../openlayers/lib/OpenLayers/Geometry/MultiPolygon.js","../../openlayers/lib/OpenLayers/Geometry/Polygon.js","../../openlayers/lib/OpenLayers/Handler/Point.js","../../openlayers/lib/OpenLayers/Layer/GML.js","../../openlayers/lib/OpenLayers/Layer/PointTrack.js","../../openlayers/lib/OpenLayers/Layer/WFS.js","../../openlayers/lib/OpenLayers/Strategy/BBOX.js","../../openlayers/lib/OpenLayers/Format/Filter/v1.js","../../openlayers/lib/OpenLayers/Format/SLD/v1.js","../../openlayers/lib/OpenLayers/Geometry/Curve.js","../../openlayers/lib/OpenLayers/Format/Filter/v1_0_0.js","../../openlayers/lib/OpenLayers/Format/SLD/v1_0_0.js","../../openlayers/lib/OpenLayers/Geometry/LineString.js","../../openlayers/lib/OpenLayers/Format/GML.js","../../openlayers/lib/OpenLayers/Format/GPX.js","../../openlayers/lib/OpenLayers/Format/GeoJSON.js","../../openlayers/lib/OpenLayers/Format/GeoRSS.js","../../openlayers/lib/OpenLayers/Format/KML.js","../../openlayers/lib/OpenLayers/Format/OSM.js","../../openlayers/lib/OpenLayers/Geometry/LinearRing.js","../../openlayers/lib/OpenLayers/Handler/Path.js","../../openlayers/lib/OpenLayers/Format/GML/Base.js","../../openlayers/lib/OpenLayers/Format/WFS.js","../../openlayers/lib/OpenLayers/Handler/Polygon.js","../../openlayers/lib/OpenLayers/Control/EditingToolbar.js","../../openlayers/lib/OpenLayers/Format/GML/v2.js","../../openlayers/lib/OpenLayers/Format/GML/v3.js","../../proj4js/lib/proj4js.js","proj4js/OverloadedProj4js.js","OpenLayers/OverloadedOpenLayersMinimum.js","OpenLayers/OverloadedOpenLayersStandard.js","OpenLayers/OverloadedOpenLayersExtended.js","Geoportal/Lang.js","Geoportal/Lang/en.js","Geoportal/Lang/fr.js","Geoportal/Lang/de.js","Geoportal/Lang/es.js","Geoportal/Lang/it.js","Geoportal/Control.js","Geoportal/Format.js","Geoportal/GeoRMHandler.js","Geoportal/Layer.js","Geoportal/Layer/Aggregate.js","Geoportal/OLS.js","Geoportal/Popup.js","Geoportal/Tile.js","Geoportal/Util.js","Geoportal/Control/Copyright.js","Geoportal/Control/DeleteFeature.js","Geoportal/Control/Floating.js","Geoportal/Control/GraphicScale.js","Geoportal/Control/Logo.js","Geoportal/Control/MousePosition.js","Geoportal/Control/Panel.js","Geoportal/Control/PermanentLogo.js","Geoportal/Control/Projections.js","Geoportal/Control/RemoveLayer.js","Geoportal/Control/SliderBase.js","Geoportal/Control/ToolBox.js","Geoportal/Control/ZoomToLayerMaxExtent.js","Geoportal/Format/GPX.js","Geoportal/Format/Geoconcept.js","Geoportal/Format/XLS.js","Geoportal/Layer/Grid.js","Geoportal/Layer/WFS.js","Geoportal/Layer/WMS.js","Geoportal/Layer/WMSC.js","Geoportal/OLS/AbstractBody.js","Geoportal/OLS/AbstractHeader.js","Geoportal/OLS/AbstractLocation.js","Geoportal/OLS/AbstractRequestParameters.js","Geoportal/OLS/AbstractResponseParameters.js","Geoportal/OLS/AbstractStreetLocator.js","Geoportal/OLS/Error.js","Geoportal/OLS/GeocodeMatchCode.js","Geoportal/OLS/LUS.js","Geoportal/OLS/Place.js","Geoportal/OLS/PostalCode.js","Geoportal/OLS/Street.js","Geoportal/OLS/UOM.js","Geoportal/OLS/XLS.js","Geoportal/Popup/Anchored.js","Geoportal/Tile/Image.js","Geoportal/Control/EditingToolbar.js","Geoportal/Control/Form.js","Geoportal/Control/Information.js","Geoportal/Control/LayerOpacitySlider.js","Geoportal/Control/MeasureToolbar.js","Geoportal/Control/NavToolbar.js","Geoportal/Control/PanelToggle.js","Geoportal/Control/ZoomSlider.js","Geoportal/OLS/AbstractAddress.js","Geoportal/OLS/AbstractPosition.js","Geoportal/OLS/Building.js","Geoportal/OLS/ErrorList.js","Geoportal/OLS/LUS/ReverseGeocodePreference.js","Geoportal/OLS/StreetAddress.js","Geoportal/OLS/UOM/AbstractMeasure.js","Geoportal/OLS/UOM/TimeStamp.js","Geoportal/Control/AddImageLayer.js","Geoportal/Control/AddVectorLayer.js","Geoportal/Control/LayerOpacity.js","Geoportal/Control/ZoomBar.js","Geoportal/OLS/Address.js","Geoportal/OLS/Request.js","Geoportal/OLS/RequestHeader.js","Geoportal/OLS/Response.js","Geoportal/OLS/ResponseHeader.js","Geoportal/OLS/UOM/Angle.js","Geoportal/OLS/UOM/Distance.js","Geoportal/OLS/UOM/Speed.js","Geoportal/OLS/UOM/Time.js","Geoportal/Control/BasicLayerToolbar.js","Geoportal/Control/LayerToolbar.js","Geoportal/Format/XLS/v1_1.js","Geoportal/OLS/HorizontalAcc.js","Geoportal/OLS/LUS/GeocodeRequest.js","Geoportal/OLS/LUS/GeocodedAddress.js","Geoportal/OLS/LUS/SearchCentreDistance.js","Geoportal/OLS/UOM/Distance/Altitude.js","Geoportal/OLS/VerticalAcc.js","Geoportal/Control/LayerSwitcher.js","Geoportal/Format/XLS/v1_0.js","Geoportal/OLS/LUS/GeocodeResponseList.js","Geoportal/OLS/LUS/ReverseGeocodedLocation.js","Geoportal/OLS/QualityOfPosition.js","Geoportal/OLS/LUS/GeocodeResponse.js","Geoportal/OLS/LUS/ReverseGeocodeResponse.js","Geoportal/OLS/Position.js","Geoportal/OLS/LUS/ReverseGeocodeRequest.js","Geoportal/Format/XLS/v1_1/LocationUtilityService.js","Geoportal/Format/XLS/v1_0/LocationUtilityService.js","Geoportal/Layer/OpenLS.js","Geoportal/Layer/OpenLS/Core.js","Geoportal/Layer/OpenLS/Core/LocationUtilityService.js","Geoportal/Control/LocationUtilityService.js","Geoportal/Control/SearchToolbar.js","Geoportal/Catalogue.js","Geoportal/Map.js","Geoportal/Viewer.js","Geoportal/Viewer/Default.js","Geoportal/Viewer/Standard.js"];
var B=navigator.userAgent;
var D=(B.match("MSIE")||B.match("Safari"));
if(D){var A=new Array(H.length)
}else{var E=document.getElementsByTagName("head").length?document.getElementsByTagName("head")[0]:document.body;
if(!E){alert("no head and body to include the JavaScript API.");
return 
}}var I=Geoportal._getScriptLocation()+"lib/";
for(var C=0,F=H.length;
C<F;
C++){if(D){A[C]="<script type='text/javascript' src='"+I+H[C]+"' charset='UTF-8' defer=''><\/script>"
}else{if(E){var J=document.createElement("script");
J.src=I+H[C];
J.charset="UTF-8";
J.type="text/javascript";
J.defer=false;
E.appendChild(J)
}}}if(D){document.write(A.join(""))
}}})();
Geoportal.VERSION_NUMBER="1.0beta4";
Geoportal.Catalogue=OpenLayers.Class({map:null,urlServices:{},initialize:function(B,A){if(B){this.map=B
}if(A.apiKey&&A[A.apiKey]){this[A.apiKey]={tokenServer:A[A.apiKey].tokenServer,geoRMKey:A.apiKey,tokenTimeOut:A[A.apiKey].tokenTimeOut,bounds:A[A.apiKey].bounds?OpenLayers.Bounds.fromArray(A[A.apiKey].bounds):null,layers:A[A.apiKey].resources,allowedGeoportalLayers:A[A.apiKey].allowedGeoportalLayers}
}},destroy:function(){if(this.map){if(this.map.apiKey){this[this.map.apiKey]=null
}this.map=null
}},_orderLayersStack:function(F){var B=[];
var E;
var D,G=F.length;
for(D=0;
D<G;
D++){E={};
var A=F[D];
E.layerId=A;
switch(E.layerId.split(":")[0]){case"ORTHOIMAGERY.ORTHOPHOTOS":E.weight=999;
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":E.weight=998;
break;
case"ELEVATION.SLOPS":E.weight=997;
break;
case"CADASTRALPARCELS.PARCELS":E.weight=996;
break;
case"HYDROGRAPHY.HYDROGRAPHY":E.weight=995;
break;
case"TRANSPORTNETWORKS.ROADS":E.weight=994;
break;
case"TRANSPORTNETWORKS.RAILWAYS":E.weight=993;
break;
case"TRANSPORTNETWORKS.RUNWAYS":E.weight=992;
break;
case"BUILDINGS.BUILDINGS":E.weight=991;
break;
case"UTILITYANDGOVERNMENTALSERVICES.ALL":E.weight=990;
break;
case"ADMINISTRATIVEUNITS.BOUNDARIES":E.weight=989;
break;
case"SEAREGIONS.LEVEL0":E.weight=990;
break;
default:E.weight=0;
break
}B.unshift(E)
}B.sort(function(I,H){return H.weight-I.weight
});
G=B.length;
var C=[];
for(D=0;
D<G;
D++){E=B.shift();
C[D]=E.layerId
}return C
},getTerritory:function(D){if(D==undefined){if(this.map){D=this.map.territory||"FXX"
}else{D="FXX"
}}if(Geoportal.Catalogue.TERRITORIES[D]==undefined){if(this.map){D=this.map.territory||"FXX"
}else{D="FXX"
}}for(var B=0,A=Geoportal.Catalogue.TERRITORIES[D].defaultCRS.length;
B<A;
B++){var C=Geoportal.Catalogue.TERRITORIES[D].defaultCRS[B];
if(typeof (C)=="string"){Geoportal.Catalogue.TERRITORIES[D].defaultCRS[B]=new OpenLayers.Projection(C,{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[D].geobbox)})
}}if(typeof (Geoportal.Catalogue.TERRITORIES[D].geoCRS[0])=="string"){Geoportal.Catalogue.TERRITORIES[D].geoCRS[0]=new OpenLayers.Projection(Geoportal.Catalogue.TERRITORIES[D].geoCRS[0],{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[D].geobbox)})
}return D
},getNativeProjection:function(B,A){if(!A){A=Geoportal.Catalogue.TERRITORIES[B].defaultCRS[0]
}if(typeof (A)=="string"){A=new OpenLayers.Projection(A,{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[B].geobbox)})
}return A
},getDisplayProjections:function(E,B,D){if(E==undefined){if(this.map){E=this.map.territory||"FXX"
}else{E="FXX"
}}var G=[];
if(!B){if(!D){G.push(Geoportal.Catalogue.TERRITORIES[E].displayCRS[0])
}else{G=Geoportal.Catalogue.TERRITORIES[E].displayCRS.slice(0)
}}else{G.push(B)
}for(var C=0,A=G.length;
C<A;
C++){var F=G[C];
if(typeof (F)=="string"){F=new OpenLayers.Projection(F,{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[E].geobbox)});
G[C]=F
}}return G
},getResolutions:function(E,C){var B=null;
var D,A;
if(C.equals(Geoportal.Catalogue.TERRITORIES[E].defaultCRS[0])){B=[];
for(D=0,A=Geoportal.Catalogue.RESOLUTIONS.length;
D<A;
D++){B[D]=Geoportal.Catalogue.RESOLUTIONS[D]
}return B
}if(C.equals(Geoportal.Catalogue.TERRITORIES[E].geoCRS[0])){B=[];
for(D=0,A=Geoportal.Catalogue.RESOLUTIONS.length;
D<A;
D++){var F=new OpenLayers.LonLat(Geoportal.Catalogue.RESOLUTIONS[D],0);
F.transform(Geoportal.Catalogue.TERRITORIES[E].defaultCRS[0],C);
B[D]=F.lon
}return B
}return B
},getCenter:function(C,B){var A=new OpenLayers.LonLat(Geoportal.Catalogue.TERRITORIES[C].geocenter[0],Geoportal.Catalogue.TERRITORIES[C].geocenter[1]);
if(B&&typeof (B)!="string"){A.transform(Geoportal.Catalogue.TERRITORIES[C].geoCRS[0],B)
}return A
},getExtent:function(B,A){var C;
if(!B){C=this.map&&this.map.apiKey&&this[this.map.apiKey]&&this[this.map.apiKey].bounds?this[this.map.apiKey].bounds.clone():new OpenLayers.Bounds(-180,-90,180,90)
}else{C=OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[B].geobbox)
}if(A&&typeof (A)!="string"){C.transform(B?Geoportal.Catalogue.TERRITORIES[B].geoCRS[0]:OpenLayers.Projection.CRS84,A,true)
}return C
},getDefaultMinZoom:function(B){var C=Geoportal.Catalogue.RESOLUTIONS.length-1;
for(var A in Geoportal.Catalogue.TERRITORIES[B].baseLayers){if(Geoportal.Catalogue.TERRITORIES[B].baseLayers.hasOwnProperty(A)){if(Geoportal.Catalogue.TERRITORIES[B].baseLayers[A].minZoomLevel<C){C=Geoportal.Catalogue.TERRITORIES[B].baseLayers[A].minZoomLevel
}}}return C==Geoportal.Catalogue.RESOLUTIONS.length-1?0:C
},getDefaultMaxZoom:function(C){var A=0;
for(var B in Geoportal.Catalogue.TERRITORIES[C].baseLayers){if(Geoportal.Catalogue.TERRITORIES[C].baseLayers.hasOwnProperty(B)){if(Geoportal.Catalogue.TERRITORIES[C].baseLayers[B].maxZoomLevel>A){A=Geoportal.Catalogue.TERRITORIES[C].baseLayers[B].maxZoomLevel
}}}return A==0?Geoportal.Catalogue.RESOLUTIONS.length-1:A
},getDefaultZoom:function(C,A){if(!A){A=this.getNativeProjection(C)
}for(var B in Geoportal.Catalogue.TERRITORIES[C].baseLayers){if(Geoportal.Catalogue.TERRITORIES[C].baseLayers.hasOwnProperty(B)){if(A.isCompatibleWith(B)){return Geoportal.Catalogue.TERRITORIES[C].baseLayers[B].defaultZoomLevel
}}}return 5
},_getOriginator:function(B,A,D){var C={logo:B,url:"#"};
switch(B){case"planetobserver":C.url="http://www.planetobserver.com";
break;
case"spotimage":C.url="http://www.spotimage.fr";
break;
case"cnes":C.url="http://www.cnes.fr";
break;
case"ifn":C.url="http://www.ifn.fr/spip/sommaire.php3";
break;
case"ird":C.url="http://www.cayenne.ird.fr/";
break;
case"seasguyane":C.url="http://www.seas-guyane.org/";
break;
case"nasa":C.url="http://visibleearth.nasa.gov/view_rec.php?id=2430";
break;
default:C.logo="ign";
C.url="http://www.ign.fr";
break
}if(A&&D){C.minZoomLevel=A;
C.maxZoomLevel=D
}return C
},getLayerParameters:function(H,C){var I={};
var G=null;
if(this.map&&this.map.apiKey&&this[this.map.apiKey]){for(var B in this[this.map.apiKey].layers){if(B==C){G=this[this.map.apiKey].layers[B];
break
}if(B.match("^"+C+":")){G=this[this.map.apiKey].layers[B];
break
}}}if(G==null){return null
}I.resourceId=G.name+":"+G.type;
I.url=G.url;
I.params={layers:null,exceptions:"text/xml"};
I.options={isBaseLayer:false,description:G.name+".description",visibility:false,opacity:1,view:{drop:false,zoomToExtent:false}};
switch(G.type){case"WMS":I.classLayer=Geoportal.Layer.WMS;
I.options=OpenLayers.Util.extend(I.options,{transparent:true,buffer:0,singleTile:true});
break;
case"WFS":I.classLayer=Geoportal.Layer.WFS;
I.options=OpenLayers.Util.extend(I.options,{});
break;
default:I.classLayer=Geoportal.Layer.WMSC;
I.options=OpenLayers.Util.extend(I.options,{transparent:true,buffer:0,gridOrigin:new OpenLayers.LonLat(0,0),nativeTileSize:new OpenLayers.Size(256,256),singleTile:false});
break
}var A=Geoportal.Catalogue.TERRITORIES[H].defaultCRS;
switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":case"GEOGRAPHICALGRIDSYSTEMS.MAPS":I.options.visibility=true;
break;
default:break
}switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":case"GEOGRAPHICALGRIDSYSTEMS.MAPS":case"ELEVATION.SLOPS":if(G.type=="WMSC"){I.params.format="image/jpeg"
}break;
default:if(G.type=="WMSC"){I.params.format="image/png"
}break
}var F={};
F.maxExtent=this.getExtent(H);
F.originators=[];
var J="http://www.geocatalogue.fr/Detail.do?fileIdentifier=";
switch(G.name){case"GEOGRAPHICALGRIDSYSTEMS.MAPS":case"ELEVATION.SLOPS":F.opacity=0.3;
break;
case"CADASTRALPARCELS.PARCELS":case"HYDROGRAPHY.HYDROGRAPHY":case"TRANSPORTNETWORKS.ROADS":case"TRANSPORTNETWORKS.RAILWAYS":case"TRANSPORTNETWORKS.RUNWAYS":case"BUILDINGS.BUILDINGS":case"UTILITYANDGOVERNMENTALSERVICES.ALL":case"ADMINISTRATIVEUNITS.BOUNDARIES":case"SEAREGIONS.LEVEL0":F.opacity=0.5;
break;
default:break
}switch(H){case"ATF":I=null;
break;
case"CHE":I=null;
break;
case"CRZ":switch(G.name){case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.opacity=1;
F.minZoomLevel=5;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
break;
default:I=null;
break
}break;
case"EUE":I=null;
break;
case"FXX":default:switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=5;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("planetobserver",5,11));
F.originators.push(this._getOriginator("spotimage",12,13));
F.originators.push(this._getOriginator("cnes",12,13));
F.originators.push(this._getOriginator("ign",14,17));
F.metadataURL=J+"GL_PHOTO_FXX.xml";
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=5;
F.maxZoomLevel=15;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_FXX.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_FXX.xml";
F.dataURL="http://professionnels.ign.fr/ficheProduitCMS.do?idDoc=5323461";
break;
case"CADASTRALPARCELS.PARCELS":F.minZoomLevel=12;
F.maxZoomLevel=18;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_PARCEL_FXX.xml";
break;
case"HYDROGRAPHY.HYDROGRAPHY":F.minZoomLevel=9;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_HYDRO_FXX.xml";
break;
case"TRANSPORTNETWORKS.ROADS":F.minZoomLevel=6;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ROUTE_FXX.xml";
break;
case"TRANSPORTNETWORKS.RAILWAYS":F.minZoomLevel=6;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_RESFER_FXX.xml";
break;
case"TRANSPORTNETWORKS.RUNWAYS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_AERIEN_FXX.xml";
break;
case"BUILDINGS.BUILDINGS":F.minZoomLevel=8;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_BATI_FXX.xml";
break;
case"UTILITYANDGOVERNMENTALSERVICES.ALL":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_SRVPUB_FXX.xml";
break;
case"ADMINISTRATIVEUNITS.BOUNDARIES":F.minZoomLevel=9;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ADMIN_FXX.xml";
break;
case"SEAREGIONS.LEVEL0":F.minZoomLevel=7;
F.maxZoomLevel=16;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_LITO_FXX.xml";
break;
default:I=null;
break
}break;
case"GLP":case"SBA":case"SMA":switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=5;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("planetobserver",5,8));
F.originators.push(this._getOriginator("ign",9,17));
F.metadataURL=J+"GL_PHOTO_GLP.xml";
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=5;
F.maxZoomLevel=15;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_GLP.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_GLP.xml";
break;
case"HYDROGRAPHY.HYDROGRAPHY":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_HYDRO_GLP.xml";
break;
case"TRANSPORTNETWORKS.ROADS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ROUTE_GLP.xml";
break;
case"TRANSPORTNETWORKS.RUNWAYS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_AERIEN_GLP.xml";
break;
case"BUILDINGS.BUILDINGS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_BATI_GLP.xml";
break;
case"ADMINISTRATIVEUNITS.BOUNDARIES":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ADMIN_GLP.xml";
break;
case"UTILITYANDGOVERNMENTALSERVICES.ALL":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_SRVPUB_GLP.xml";
break;
default:I=null;
break
}break;
case"GUF":switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=5;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("planetobserver",5,8));
F.originators.push(this._getOriginator("spotimage",9,11));
F.originators.push(this._getOriginator("cnes",9,11));
F.originators.push(this._getOriginator("ifn",9,11));
F.originators.push(this._getOriginator("ird",9,11));
F.originators.push(this._getOriginator("seasguyane",9,11));
F.originators.push(this._getOriginator("ign",12,17));
F.metadataURL=J+"GL_PHOTO_GUF.xml";
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=5;
F.maxZoomLevel=15;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_GUF.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_GUF.xml";
break;
case"HYDROGRAPHY.HYDROGRAPHY":F.minZoomLevel=11;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_HYDRO_GUF.xml";
break;
case"TRANSPORTNETWORKS.ROADS":F.minZoomLevel=11;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ROUTE_GUF.xml";
break;
case"TRANSPORTNETWORKS.RUNWAYS":F.minZoomLevel=11;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_AERIEN_GUF.xml";
break;
case"BUILDINGS.BUILDINGS":F.minZoomLevel=11;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_BATI_GUF.xml";
break;
case"UTILITYANDGOVERNMENTALSERVICES.ALL":F.minZoomLevel=11;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_SRVPUB_GUF.xml";
break;
case"ADMINISTRATIVEUNITS.BOUNDARIES":F.minZoomLevel=11;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ADMIN_GUF.xml";
break;
default:I=null;
break
}break;
case"KER":switch(G.name){case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.opacity=1;
F.minZoomLevel=5;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_KER.xml";
break;
default:I=null;
break
}break;
case"MTQ":switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=5;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("planetobserver",5,8));
F.originators.push(this._getOriginator("ign",9,17));
F.metadataURL=J+"GL_PHOTO_MTQ.xml";
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=5;
F.maxZoomLevel=15;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_MTQ.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_MTQ.xml";
break;
case"HYDROGRAPHY.HYDROGRAPHY":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_HYDRO_MTQ.xml";
break;
case"TRANSPORTNETWORKS.ROADS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ROUTE_MTQ.xml";
break;
case"TRANSPORTNETWORKS.RUNWAYS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_AERIEN_MTQ.xml";
break;
case"BUILDINGS.BUILDINGS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_BATI_MTQ.xml";
break;
case"ADMINISTRATIVEUNITS.BOUNDARIES":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ADMIN_MTQ.xml";
break;
case"UTILITYANDGOVERNMENTALSERVICES.ALL":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_SRVPUB_MTQ.xml";
break;
default:I=null;
break
}break;
case"MYT":switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=5;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("planetobserver",5,8));
F.originators.push(this._getOriginator("ign",9,17));
F.metadataURL=J+"GL_PHOTO_MYT.xml";
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=5;
F.maxZoomLevel=15;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_MYT.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_MYT.xml";
break;
case"HYDROGRAPHY.HYDROGRAPHY":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_HYDRO_MYT.xml";
break;
case"TRANSPORTNETWORKS.ROADS":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ROUTE_MYT.xml";
break;
case"TRANSPORTNETWORKS.RUNWAYS":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_AERIEN_MYT.xml";
break;
case"BUILDINGS.BUILDINGS":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_BATI_MYT.xml";
break;
case"ADMINISTRATIVEUNITS.BOUNDARIES":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ADMIN_MYT.xml";
break;
default:I=null;
break
}break;
case"NCL":switch(G.name){case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.opacity=1;
F.minZoomLevel=5;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_NCL.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_NCL.xml";
break;
default:I=null;
break
}break;
case"PYF":switch(G.name){case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.opacity=1;
F.minZoomLevel=5;
F.maxZoomLevel=13;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_PYF.xml";
break;
default:I=null;
break
}break;
case"REU":switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=5;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("planetobserver",5,8));
F.originators.push(this._getOriginator("ign",9,17));
F.metadataURL=J+"GL_PHOTO_REU.xml";
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=5;
F.maxZoomLevel=15;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_REU.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_REU.xml";
break;
case"HYDROGRAPHY.HYDROGRAPHY":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_HYDRO_REU.xml";
break;
case"TRANSPORTNETWORKS.ROADS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ROUTE_REU.xml";
break;
case"TRANSPORTNETWORKS.RUNWAYS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_AERIEN_REU.xml";
break;
case"BUILDINGS.BUILDINGS":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_BATI_REU.xml";
break;
case"ADMINISTRATIVEUNITS.BOUNDARIES":F.minZoomLevel=11;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ADMIN_REU.xml";
break;
case"UTILITYANDGOVERNMENTALSERVICES.ALL":F.minZoomLevel=14;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_SRVPUB_REU.xml";
break;
default:I=null;
break
}break;
case"SPM":switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=5;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("planetobserver",5,8));
F.originators.push(this._getOriginator("ign",9,17));
F.metadataURL=J+"GL_PHOTO_SPM.xml";
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=5;
F.maxZoomLevel=15;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_SPM.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_SPM.xml";
break;
default:I=null;
break
}break;
case"WLD":switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=0;
F.maxZoomLevel=4;
F.originators.push(this._getOriginator("planetobserver"));
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=0;
F.maxZoomLevel=4;
F.originators.push(this._getOriginator("ign"));
break;
default:I=null;
break
}break;
case"WLF":switch(G.name){case"ORTHOIMAGERY.ORTHOPHOTOS":F.minZoomLevel=5;
F.maxZoomLevel=17;
F.originators.push(this._getOriginator("planetobserver",5,8));
F.originators.push(this._getOriginator("ign",9,17));
F.metadataURL=J+"GL_PHOTO_WLF.xml";
break;
case"GEOGRAPHICALGRIDSYSTEMS.MAPS":F.minZoomLevel=5;
F.maxZoomLevel=15;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_CARTE_WLF.xml";
break;
case"ELEVATION.SLOPS":F.minZoomLevel=5;
F.maxZoomLevel=12;
F.originators.push(this._getOriginator("ign"));
F.metadataURL=J+"GL_ALTI_WLF.xml";
break;
default:I=null;
break
}break
}if(I){F.name=G.name;
F.projection=null;
for(var D=0,E=A.length;
D<E;
D++){if(typeof (A[D])=="string"){A[D]=new OpenLayers.Projection(A[D],{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[H].geobbox)})
}else{A[D]=new OpenLayers.Projection(A[D].getCode(),{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[H].geobbox)})
}if(A[D].equals(this.map.getProjection())){F.projection=new OpenLayers.Projection(A[D].getCode(),{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[H].geobbox)})
}}if(!F.projection){F.projection=new OpenLayers.Projection(A[0].getCode(),{domainOfValidity:OpenLayers.Bounds.fromArray(Geoportal.Catalogue.TERRITORIES[H].geobbox)})
}I.params.layers=G.name;
if((I.classLayer==Geoportal.Layer.WMSC)&&!F.projection.equals(this.map.getProjection())){F.nativeResolutions=Geoportal.Catalogue.RESOLUTIONS.slice(0)
}F.maxExtent.transform(Geoportal.Catalogue.TERRITORIES[H].geoCRS[0],F.projection,true);
OpenLayers.Util.extend(I.options,F)
}return I
},CLASS_NAME:"Geoportal.Catalogue"});
Geoportal.Catalogue.RESOLUTIONS=[39135.75,19567.875,9783.9375,4891.96875,2445.984375,2048,1024,512,256,128,64,32,16,8,4,2,1,0.5,0.25,0.125,0.0625];
Geoportal.Catalogue.TERRITORIES={ATF:{},CHE:{},CRZ:{geobbox:[49,-47,53,-45],geocenter:[51.866667,-46.433333],defaultCRS:["IGNF:GEOPORTALCRZ"],geoCRS:["IGNF:WGS84G"],displayCRS:["IGNF:WGS84G","IGNF:UTM39SW84"],baseLayers:{"IGNF:GEOPORTALCRZ":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:11}}},EUE:{},FXX:{geobbox:[-15,34,26,58],geocenter:[2.345274398,48.860832558],defaultCRS:["IGNF:GEOPORTALFXX"],geoCRS:["IGNF:RGF93G"],displayCRS:["IGNF:RGF93G","IGNF:LAMB93"],baseLayers:{"IGNF:GEOPORTALFXX":{minZoomLevel:5,maxZoomLevel:18,defaultZoomLevel:5}}},GLP:{geobbox:[-62,15.6,-60.5,16.75],geocenter:[-61.732777778,15.996111111],defaultCRS:["IGNF:GEOPORTALANF"],geoCRS:["IGNF:WGS84RRAFGEO"],displayCRS:["IGNF:WGS84RRAFGEO","IGNF:UTM20W84GUAD"],baseLayers:{"IGNF:GEOPORTALANF":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:13}}},GUF:{geobbox:[-56,1,-46,7],geocenter:[-52.305277778,4.932222222],defaultCRS:["IGNF:GEOPORTALGUF"],geoCRS:["IGNF:RGFG95GEO"],displayCRS:["IGNF:RGFG95GEO","IGNF:UTM22RGFG95"],baseLayers:{"IGNF:GEOPORTALGUF":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:10}}},KER:{geobbox:[68,-51,72,-48],geocenter:[70.215278,-49.354167],defaultCRS:["IGNF:GEOPORTALKER"],geoCRS:["IGNF:WGS84G"],displayCRS:["IGNF:WGS84G","IGNF:UTM42SW84"],baseLayers:{"IGNF:GEOPORTALKER":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:11}}},MTQ:{geobbox:[-62,14,-60,15.5],geocenter:[-61.075,14.6],defaultCRS:["IGNF:GEOPORTALANF"],geoCRS:["IGNF:WGS84RRAFGEO"],displayCRS:["IGNF:WGS84RRAFGEO","IGNF:UTM20W84MART"],baseLayers:{"IGNF:GEOPORTALANF":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:13}}},MYT:{geobbox:[44,-14,46,-12],geocenter:[45.228333333,-12.781666667],defaultCRS:["IGNF:GEOPORTALMYT"],geoCRS:["IGNF:RGM04GEO"],displayCRS:["IGNF:RGM04GEO","IGNF:RGM04UTM38S"],baseLayers:{"IGNF:GEOPORTALMYT":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:14}}},NCL:{geobbox:[163,-23,169,-19],geocenter:[166.433333,-22.283333],defaultCRS:["IGNF:GEOPORTALNCL"],geoCRS:["IGNF:RGNCGEO"],displayCRS:["IGNF:RGNCGEO","IGNF:RGNCUTM57S","IGNF:RGNCUTM58S","IGNF:RGNCUTM59S"],baseLayers:{"IGNF:GEOPORTALNCL":{minZoomLevel:5,maxZoomLevel:13,defaultZoomLevel:9}}},PYF:{geobbox:[-152,-18,-149,-16],geocenter:[-149.569444,-17.536111],defaultCRS:["IGNF:GEOPORTALPYF"],geoCRS:["IGNF:RGPFGEO"],displayCRS:["IGNF:RGPFGEO","IGNF:RGPFUTM5S","IGNF:RGPFUTM6S","IGNF:RGPFUTM7S"],baseLayers:{"IGNF:GEOPORTALPYF":{minZoomLevel:5,maxZoomLevel:11,defaultZoomLevel:11}}},REU:{geobbox:[53,-24,59,-19],geocenter:[55.466666667,-20.875],defaultCRS:["IGNF:GEOPORTALREU"],geoCRS:["IGNF:RGR92GEO"],displayCRS:["IGNF:RGR92GEO","IGNF:RGR92UTM40S"],baseLayers:{"IGNF:GEOPORTALREU":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:13}}},SBA:{geobbox:[-62.97,17.86,-62.77,17.99],geocenter:[-62.85,17.895833],defaultCRS:["IGNF:GEOPORTALANF"],geoCRS:["IGNF:WGS84RRAFGEO"],displayCRS:["IGNF:WGS84RRAFGEO","IGNF:UTM20W84GUAD"],baseLayers:{"IGNF:GEOPORTALANF":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:14}}},SMA:{geobbox:[-63.18,18.03,-62.95,18.15],geocenter:[-63.088888,18.069722],defaultCRS:["IGNF:GEOPORTALANF"],geoCRS:["IGNF:WGS84RRAFGEO"],displayCRS:["IGNF:WGS84RRAFGEO","IGNF:UTM20W84GUAD"],baseLayers:{"IGNF:GEOPORTALANF":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:14}}},SPM:{geobbox:[-57,46,-56,48],geocenter:[-56.173611,46.780556],defaultCRS:["IGNF:GEOPORTALSPM"],geoCRS:["IGNF:RGSPM06GEO"],displayCRS:["IGNF:RGSPM06GEO","IGNF:RGSPM06U21"],baseLayers:{"IGNF:GEOPORTALSPM":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:14}}},WLD:{geobbox:[-180,-90,180,90],geocenter:[2.345274398,48.860832558],defaultCRS:["IGNF:MILLER"],geoCRS:[OpenLayers.Projection.CRS84],displayCRS:[OpenLayers.Projection.CRS84],baseLayers:{"IGNF:MILLER":{minZoomLevel:0,maxZoomLevel:4,defaultZoomLevel:4}}},WLF:{geobbox:[-179,-15,-176,-13],geocenter:[-176.173611,-13.283333],defaultCRS:["IGNF:GEOPORTALWLF"],geoCRS:["IGNF:WGS84G"],displayCRS:["IGNF:WGS84G","IGNF:UTM01SW84"],baseLayers:{"IGNF:GEOPORTALWLF":{minZoomLevel:5,maxZoomLevel:17,defaultZoomLevel:14}}}};
Geoportal.Control=OpenLayers.Class(OpenLayers.Control,{initialize:function(A){this.displayClass=this.CLASS_NAME.replace("Geoportal.","gp").replace(/\./g,"");
OpenLayers.Util.extend(this,A);
this.events=new OpenLayers.Events(this,null,this.EVENT_TYPES);
if(this.eventListeners instanceof Object){this.events.on(this.eventListeners)
}if(this.id==null){this.id=OpenLayers.Util.createUniqueID(this.CLASS_NAME+"_")
}},changeLang:function(A){},CLASS_NAME:"Geoportal.Control"});
Geoportal.Control.selectFeature=function(A){if(A){if(!A.popup){A.createPopup()
}if(A.layer&&A.layer.map&&A.popup){A.layer.map.addPopup(A.popup)
}}};
Geoportal.Control.unselectFeature=function(A){if(A){if(A.popup){A.popup.destroy();
A.popup=null
}}};
Geoportal.GeoRMHandler={};
Geoportal.GeoRMHandler.Updater=OpenLayers.Class({GeoRMKey:null,ServerUrl:"http://jeton-api.ign.fr/",ttl:600000,token:null,maps:[],QueryUrl:null,lastUpdate:0,status:0,script:null,reload:false,DOMhead:null,initialize:function(C,B,A){this.DOMhead=(document.getElementsByTagName("head").length?document.getElementsByTagName("head")[0]:document.body);
this.GeoRMKey=C;
this.lastUpdate=0;
if(B){this.ServerUrl=B
}if(this.ServerUrl.charAt(this.ServerUrl.length-1)!="/"){this.ServerUrl+="/"
}if(A){this.ttl=1000*A
}this.QueryUrl=this.ServerUrl+"getToken?key="+this.GeoRMKey+"&output=json&callback=Geoportal.GeoRMHandler.U"+this.GeoRMKey+".callback&";
OpenLayers.Event.observe(window,"unload",this.destroy)
},addMap:function(C){for(var B=0,A=this.maps.length;
B<A;
B++){if(this.maps[B]===C){return 
}}this.maps.push(C)
},destroy:function(){OpenLayers.Event.stopObserving(window,"unload",this.destroy);
if(this.GeoRMKey){this.GeoRMKey=null
}if(this.ServerUrl){this.ServerUrl="http://jeton-api.ign.fr/"
}if(this.token){this.token=null
}if(this.maps){this.maps=[]
}if(this.QueryUrl){this.QueryUrl=null
}if(this.script){this.script=null
}if(this.DOMhead){this.DOMhead=null
}},getToken:function(){var A=(new Date()).getTime();
var B=(!this.token)||(this.lastUpdate+this.ttl<A);
if(this.lastUpdate+this.ttl/2<A){if(this.status==0){this.lastUpdate=A;
this.updateToken()
}}if(B&&this.status>=0){this.token=null;
this.reload=true;
return null
}return this.token
},updateToken:function(){if(this.script){this.DOMhead.removeChild(this.script);
this.script=null
}this.status++;
if(this.status>=10){this.status=0;
OpenLayers.Console.error("10 erreurs");
return 
}this.script=document.createElement("script");
this.script.src=this.QueryUrl;
for(var B in this.token){this.script.src+=B+"="+this.token[B]+"&"
}this.script.defer=true;
this.DOMhead.appendChild(this.script);
if(this.timeout){window.clearTimeout(this.timeout)
}var A=this.status*this.ttl/10;
this.timeout=window.setTimeout("Geoportal.GeoRMHandler.U"+this.GeoRMKey+".updateToken()",A)
},callback:function(C){if(C==null){OpenLayers.Console.error("Failed to update token for key : "+this.GeoRMKey)
}else{if(this.status>0){this.token=C;
if(this.timeout){window.clearTimeout(this.timeout);
this.timeout=null
}this.status=-1;
if(this.reload){for(var B=0,A=this.maps.length;
B<A;
B++){this.maps[B].setCenter(this.maps[B].center,this.maps[B].zoom,false,true)
}this.reload=false
}this.status=0
}}},CLASS_NAME:"Geoportal.GeoRMHandler.Updater"});
Geoportal.GeoRMHandler.addKey=function(C,B,A,D){if(!Geoportal.GeoRMHandler["U"+C]){Geoportal.GeoRMHandler["U"+C]=new Geoportal.GeoRMHandler.Updater(C,B,A);
Geoportal.GeoRMHandler["U"+C].getToken()
}Geoportal.GeoRMHandler["U"+C].addMap(D);
return Geoportal.GeoRMHandler["U"+C]
};
Geoportal.Layer=OpenLayers.Class(OpenLayers.Layer,{CLASS_NAME:"Geoportal.Layer"});
Geoportal.Tile=OpenLayers.Class(OpenLayers.Tile,{CLASS_NAME:"Geoportal.Tile"});
Geoportal.Util={getImagesLocation:function(){return Geoportal._getScriptLocation()+"theme/geoportal/img/"
},convertToPixels:function(D,A,F){if(!D){return undefined
}if(A==undefined){A=false
}if(/px$/.test(D)){return parseInt(D)
}var C=document.createElement("div");
C.style.display="";
C.style.visibility="hidden";
C.style.position="absolute";
C.style.lineHeight="0";
if(!F){F=document.body
}if(/%$/.test(D)){F=F.parentNode||F;
C.style[A?"width":"height"]=D
}else{C.style.borderStyle="solid";
if(A){C.style.borderBottomHeight="0";
C.style.borderTopHeight=D
}else{C.style.borderBottomWidth="0";
C.style.borderTopWidth=D
}}F.appendChild(C);
var E=this.getElementRenderedDimensions(C);
var B=A?E.w:E.h;
E=null;
F.removeChild(C);
return B
},getComputedStyle:function(F,G,B){var A;
if(F.currentStyle){A=F.currentStyle[OpenLayers.String.camelize(G)]
}else{if(document.defaultView.getComputedStyle){var E=document.defaultView.getComputedStyle(F,null);
A=E.getPropertyValue(G)
}else{A=null
}}var D=/(em|ex|pt|%)$/;
var C=/(width)/i;
A=B?A?D.test(A)?this.convertToPixels(A,C.test(A),F.parentNode):parseInt(A)||0:0:A;
return A
},getBorders:function(D){var F=0,B=0;
var A=Geoportal.Util.getComputedStyle(D,"border-left-width",true);
var H=Geoportal.Util.getComputedStyle(D,"border-right-width",true);
var G=Geoportal.Util.getComputedStyle(D,"border-top-width",true);
var E=Geoportal.Util.getComputedStyle(D,"border-bottom-width",true);
F=A+H;
B=G+E;
if(A==0&&H==0&&G==0&&E==0){var C="<div class='"+D.className+"'>X</div>";
var I=OpenLayers.Util.getRenderedDimensions(C,null,{});
var F=I.w;
var B=I.h;
I=null;
C="<div class='"+D.className+"' style='border:0px!important;'>X</div>";
I=OpenLayers.Util.getRenderedDimensions(C,null,{});
if(I.w>0){F=I.w
}if(I.h>0){B=I.h
}I=null
}return new OpenLayers.Size(F,B)
},getElementRenderedDimensions:function(E){var A=0,C=0;
var D=(E.style&&E.style.display=="none");
var B,F;
if(D){B=E.style.visibility;
E.style.visibility="hidden";
F=E.style.position;
E.style.position="absolute";
E.style.display=""
}if(E.offsetWidth){if(E.scrollWidth&&(E.offsetWidth!=E.scrollWidth)){A=E.scrollWidth
}else{A=E.offsetWidth
}}else{if(E.clip&&E.clip.width){A=E.clip.width
}else{if(E.style&&E.style.pixelWidth){A=E.style.pixelWidth
}}}A=parseInt(A);
if(E.offsetHeight){C=E.offsetHeight
}else{if(E.clip&&E.clip.height){C=E.clip.height
}else{if(E.style&&E.style.pixelHeight){C=E.style.pixelHeight
}}}C=parseInt(C);
if(D){E.style.display="none";
E.style.position=F;
E.style.visibility=B
}return new OpenLayers.Size(A,C)
},getElementGuessedDimensions:function(Y){var N=undefined;
var W=undefined;
if(!Y){return new OpenLayers.Size(0,0)
}if(Y.style){N=this.convertToPixels(Y.style.width,true,Y.parentNode);
W=this.convertToPixels(Y.style.height,false,Y.parentNode)
}if(!N||!W){var R="display:block!important;";
if(Y.style){for(var E=0,F=Y.style.length,T="",O="";
E<F;
E++){T=Y.style.item(E);
O=Y.style.getPropertyValue(T);
R+=T+":"+O+";";
if(!N&&T=="width"){N=this.convertToPixels(O,true,Y.parentNode);
continue
}if(!W&&T=="height"){W=this.convertToPixels(O,false,Y.parentNode);
continue
}}}}if((!N||!W)&&Y.id&&document.styleSheets){var I="#"+Y.id.toLowerCase();
var H=document.styleSheets[0].rules?document.styleSheets[0].rules:document.styleSheets[0].cssRules;
for(var M=0,A=H.length;
M<A;
M++){var Q=H[M];
if(Q&&Q.selectorText&&Q.selectorText.toLowerCase().match(I)){var U=Q.style;
for(var J=0,X=U.length,T="",O="";
J<X;
J++){T=U.item(J);
O=U.getPropertyValue(T);
R+=T+":"+O+";";
if(!N&&T=="width"){N=this.convertToPixels(O,true,Y.parentNode);
continue
}if(!W&&T=="height"){W=this.convertToPixels(O,false,Y.parentNode);
continue
}}}}}var G=Y.className;
if(G.length>0){G="class='"+G+"'"
}if((!N||!W)&&G.length>0&&document.styleSheets){var C=Y.nodeName.toLowerCase()+".";
var V=Y.className.split(" ");
for(var L=0,D=V.length;
L<D;
L++){var Z=V[L];
var K=Z.toLowerCase();
var H=document.styleSheets[0].rules?document.styleSheets[0].rules:document.styleSheets[0].cssRules;
for(var M=0,A=H.length;
M<A;
M++){var Q=H[M];
if(!(Q&&Q.selectorText)){continue
}var P=Q.selectorText.toLowerCase();
if(P==K||P==C+K){var U=Q.style;
for(var J=0,X=U.length,T="",O="";
J<X;
J++){T=U.item(J);
O=U.getPropertyValue(T);
R+=T+":"+O+";";
if(!N&&T=="width"){N=this.convertToPixels(O,true,Y.parentNode);
continue
}if(!W&&T=="height"){W=this.convertToPixels(O,false,Y.parentNode);
continue
}}}}}}if(!N||!W){var B="<div "+G+" style='"+R+"'></div>";
var S=OpenLayers.Util.getRenderedDimensions(B,null,{});
if(!N){width=S.w
}if(!W){height=S.h
}S=null
}return new OpenLayers.Size(N,W)
},getMaxDimensions:function(){var A=0,B=0;
if(document.innerHeight>B){A=document.innerWidth;
B=document.innerHeight
}if(document.documentElement&&document.documentElement.clientHeight>B){A=document.documentElement.clientWidth;
B=document.documentElement.clientHeight
}if(document.body&&document.body.clientHeight>B){A=document.body.clientWidth;
B=document.body.clientHeight
}return new OpenLayers.Size(A,B)
},dmsToDeg:function(H){if(!H){return Number.NaN
}var G=H.match(/(^\s?-)|(\s?[SW]\s?$)/)!=null?-1:1;
H=H.replace(/(^\s?-)|(\s?[NSEW]\s?)$/,"");
H=H.replace(/\s/g,"");
var D=H.match(/(\d{1,3})[.,°d]?(\d{0,2})[']?(\d{0,2})[.,]?(\d{0,})(?:["]|[']{2})?/);
if(D==null){return Number.NaN
}var E=(D[1]?D[1]:"0.0")*1;
var A=(D[2]?D[2]:"0.0")*1;
var B=(D[3]?D[3]:"0.0")*1;
var C=(D[4]?("0."+D[4]):"0.0")*1;
var F=(E+(A/60)+(B/3600)+(C/3600))*G;
return F
},degToDMS:function(D,B,A){var I=Math.abs(D);
var E=Math.round(I+0.5)-1;
var H=60*(I-E);
var G=Math.round(H+0.5)-1;
H=60*(H-G);
var K=Math.round(H+0.5)-1;
if(A===undefined||A<0){A=1
}var F=Math.pow(10,A);
var J=F*(H-K);
J=Math.round(J+0.5)-1;
if(J>=F){K=K+1;
J=0
}if(K==60){G=G+1;
K=0
}if(G==60){E=E+1;
G=0
}var C="";
if(B&&(B instanceof Array)&&B.length==2){C=" "+(D>0?B[0]:B[1])
}else{if(D<0){E=-1*E
}}var L=OpenLayers.String.sprintf("%4d° %02d' %02d",E,G,K)+(A>0?OpenLayers.String.sprintf('.%0*d"',A,J):'"')+C;
return L
}};
Geoportal.Control.PermanentLogo=OpenLayers.Class(Geoportal.Control,{permaLogo:null,permaURL:null,initialize:function(A){Geoportal.Control.prototype.initialize.apply(this,arguments);
if(!this.permaLogo){this.permaLogo=Geoportal.Util.getImagesLocation()+"logo_gp.gif"
}if(!this.permaURL){this.permaURL="http://www.geoportail.fr/"
}},draw:function(B){Geoportal.Control.prototype.draw.apply(this,arguments);
var C=OpenLayers.Util.createImage(null,null,null,this.permaLogo,null,null,null,false);
if(this.permaURL!=null){var A=document.createElement("a");
A.setAttribute("href",this.permaURL);
A.setAttribute("target","_blank");
A.appendChild(C);
this.div.appendChild(A)
}else{this.div.appendChild(C)
}return this.div
},CLASS_NAME:"Geoportal.Control.PermanentLogo"});
Geoportal.Layer.Grid=OpenLayers.Class(OpenLayers.Layer.Grid,{gridOrigin:null,nativeTileSize:null,nativeResolutions:null,resample:false,initialize:function(C,B,D,A){OpenLayers.Layer.Grid.prototype.initialize.apply(this,arguments);
if(!this.gridOrigin){this.gridOrigin=new OpenLayers.LonLat(0,0)
}if(!this.nativeTileSize){this.nativeTileSize=new OpenLayers.Size(256,256)
}this.tileSize=new OpenLayers.Size(OpenLayers.Map.TILE_WIDTH)
},destroy:function(){if(this.gridOrigin){this.gridOrigin=null
}if(this.nativeTileSize){this.nativeTileSize=null
}if(this.nativeResolutions){this.nativeResolutions=null
}this.resample=false;
OpenLayers.Layer.Grid.prototype.destroy.apply(this,arguments)
},clone:function(A){if(A==null){A=new Geoportal.Layer.Grid(this.name,this.url,this.params,this.options)
}A=OpenLayers.Layer.HTTPRequest.prototype.clone.apply(this,[A]);
if(this.tileSize!=null){A.tileSize=this.tileSize.clone()
}if(this.gridOrigin!=null){A.gridOrigin=this.gridOrigin.clone()
}if(this.nativeTileSize!=null){A.nativeTileSize=this.nativeTileSize.clone()
}if(this.nativeResolutions!=null){A.nativeResolutions=this.nativeResolutions.slice(0)
}A.grid=[];
return A
},initResolutions:function(){OpenLayers.Layer.prototype.initResolutions.apply(this,arguments);
if(!this.CLASS_NAME.match(/Geoportal.Layer.WMSC/)){return 
}var A=null;
for(var D=0,C=this.map.getNumLayers();
D<C;
D++){var B=this.map.layers[D];
if(B.isBaseLayer){if(this.getNativeProjection()&&this.getNativeProjection().equals(B.getNativeProjection())){A=B;
break
}}}if(!A){for(var D=0,C=this.map.getNumLayers();
D<C;
D++){var B=this.map.layers[D];
if(B.isBaseLayer){if(this.getNativeProjection()&&this.getNativeProjection().isCompatibleWith(B.getNativeProjection())){A=B;
break
}}}}this.maxResolution=A.resolutions[this.minZoomLevel];
this.minResolution=A.resolutions[this.maxZoomLevel];
this.scales=A.scales.slice(0);
this.minScale=this.scales[this.minZoomLevel];
this.maxScale=this.scales[this.maxZoomLevel]
},moveTo:function(D,A,E){if(this.GeoRM){if(!this.GeoRM.getToken(this,arguments)){return 
}}OpenLayers.Layer.HTTPRequest.prototype.moveTo.apply(this,arguments);
D=D||this.map.getExtent();
if(D!=null){var C=!this.grid.length||A;
if(this.resample){C=true
}var B=this.getTilesBounds();
if(this.singleTile){if(C||(!E&&!B.containsBounds(D))){this.initSingleTile(D)
}}else{if(C||!B.containsBounds(D,true)){this.initGriddedTiles(D)
}else{this.moveGriddedTiles(D)
}}}},calculateGridLayout:function(A,O,E){var K=E*this.tileSize.w;
var C=E*this.tileSize.h;
var I=A.left-this.gridOrigin.lon;
var L=Math.floor(I/K)-this.buffer;
var J=I/K-L;
var F=-J*this.tileSize.w;
var M=this.gridOrigin.lon+L*K;
var B=A.top-(this.gridOrigin.lat+C);
var H=Math.ceil(B/C)+this.buffer;
var N=H-B/C;
var D=-N*this.tileSize.h;
var G=this.gridOrigin.lat+H*C;
return{tilelon:K,tilelat:C,tileoffsetlon:M,tileoffsetlat:G,tileoffsetx:F,tileoffsety:D}
},initGriddedTiles:function(Z){var J=new OpenLayers.LonLat(1,1);
if(this.getNativeProjection()&&!(this.getNativeProjection().equals(this.map.getProjection()))){J.transform(this.getNativeProjection(),this.map.getProjection())
}var g=this.map.getResolution();
var B=g;
if(this.nativeResolutions){var N=0;
for(var h=Math.max(0,this.minZoomLevel),R=Math.min(this.nativeResolutions.length,this.maxZoomLevel+1);
h<R;
h++){var d=this.nativeResolutions[h]*J.lon/g;
if(d>1){d=1/d
}if(d>N){N=d;
B=this.nativeResolutions[h]
}}}this.resample=(B/g*J.lat!=1||B/g*J.lon!=1);
this.tileSize.h=this.nativeTileSize.h*B/g*J.lat;
this.tileSize.w=this.nativeTileSize.w*B/g*J.lon;
var D=this.map.getSize();
var E=Math.ceil(D.h/this.tileSize.h)+Math.max(1,2*this.buffer);
var V=Math.ceil(D.w/this.tileSize.w)+Math.max(1,2*this.buffer);
var C=this.maxExtent;
var Y=this.calculateGridLayout(Z,C,g);
var f=Y.tileoffsetx;
var e=Y.tileoffsety;
if(!this.resample){f=Math.round(f);
e=Math.round(e)
}var j=Y.tileoffsetlon;
var T=Y.tileoffsetlat;
var U=Y.tilelon;
var A=Y.tilelat;
var S=f;
var X=j;
var L=0;
var P=parseInt(this.map.layerContainerDiv.style.left,10);
var W=parseInt(this.map.layerContainerDiv.style.top,10);
do{var O=this.grid[L++];
if(!O){O=[];
this.grid.push(O)
}j=X;
f=S;
var k=0;
do{var a=new OpenLayers.Bounds(j,T,j+U,T+A);
var c=f;
c-=P;
var b=e;
b-=W;
var M=Math.round(c);
var K=Math.round(b);
var H=new OpenLayers.Pixel(M,K);
var F=O[k++];
var I=Math.round(c+this.tileSize.w)-M;
var Q=Math.round(b+this.tileSize.h)-K;
var G=new OpenLayers.Size(I,Q);
if(!F){F=this.addTile(a,H,G);
this.addTileMonitoringHooks(F);
O.push(F)
}else{F.moveTo(a,H,false);
F.setSize(G)
}j+=U;
f+=this.tileSize.w
}while((j<=Z.right+U*this.buffer)||k<V);
T-=A;
e+=this.tileSize.h
}while((T>=Z.bottom-A*this.buffer)||L<E);
this.removeExcessTiles(L,k);
this.spiralTileLoad()
},CLASS_NAME:"Geoportal.Layer.Grid"});
Geoportal.Layer.WFS=OpenLayers.Class(OpenLayers.Layer.WFS,{moveTo:function(A,B,M){if(this.GeoRM){if(!this.GeoRM.getToken(this,arguments)){return 
}}if(this.vectorMode){OpenLayers.Layer.Vector.prototype.moveTo.apply(this,arguments)
}else{OpenLayers.Layer.Markers.prototype.moveTo.apply(this,arguments)
}if(M){return 
}if(B){if(this.vectorMode){this.renderer.clear()
}}if(this.options.minZoomLevel){OpenLayers.Console.warn(OpenLayers.i18n("minZoomLevelError"));
if(this.map.getZoom()<this.options.minZoomLevel){return 
}}if(A==null){A=this.map.getExtent()
}var L=(this.tile==null);
var F=(!L&&!this.tile.bounds.containsBounds(A));
if(B||L||(!M&&F)){var C=A.getCenterLonLat();
var K=A.getWidth()*this.ratio;
var G=A.getHeight()*this.ratio;
var I=new OpenLayers.Bounds(C.lon-(K/2),C.lat-(G/2),C.lon+(K/2),C.lat+(G/2));
var N=this.map.getSize();
N.w=N.w*this.ratio;
N.h=N.h*this.ratio;
var H=new OpenLayers.LonLat(I.left,I.top);
var J=this.map.getLayerPxFromLonLat(H);
var D=this.getFullRequestString();
var E={BBOX:this.encodeBBOX?I.toBBOX():I.toArray()};
if(this.map&&this.getNativeProjection()&&!this.getNativeProjection().equals(this.map.getProjection())){var O=I.clone();
O.transform(this.map.getProjection(),this.getNativeProjection());
E.BBOX=this.encodeBBOX?O.toBBOX():O.toArray()
}D+="&"+OpenLayers.Util.getParameterString(E);
if(!this.tile){this.tile=new OpenLayers.Tile.WFS(this,J,I,D,N);
this.addTileMonitoringHooks(this.tile);
this.tile.draw()
}else{if(this.vectorMode){this.destroyFeatures();
this.renderer.clear()
}else{this.clearMarkers()
}this.removeTileMonitoringHooks(this.tile);
this.tile.destroy();
this.tile=null;
this.tile=new OpenLayers.Tile.WFS(this,J,I,D,N);
this.addTileMonitoringHooks(this.tile);
this.tile.draw()
}}},mergeNewParams:function(C){var B=OpenLayers.Util.upperCaseObject(C);
var A=[B];
return Geoportal.Layer.HTTPRequest.prototype.mergeNewParams.apply(this,A)
},clone:function(A){if(A==null){A=new Geoportal.Layer.WFS(this.name,this.url,this.params,this.options)
}if(this.vectorMode){A=OpenLayers.Layer.Vector.prototype.clone.apply(this,[A])
}else{A=OpenLayers.Layer.Markers.prototype.clone.apply(this,[A])
}return A
},getFullRequestString:function(C,B){var A=this.getNativeProjection();
this.params.SRS=(A==null)?null:A.getCode();
return Geoportal.Layer.Grid.prototype.getFullRequestString.apply(this,arguments)
},commit:function(){if(!this.writer){var A={};
if(this.map&&this.getNativeProjection()&&!this.getNativeProjection().equals(this.map.getProjection())){A.externalProjection=this.getNativeProjection();
A.internalProjection=this.map.getProjection()
}this.writer=new OpenLayers.Format.WFS(A,this)
}var B=this.writer.write(this.features);
OpenLayers.Request.POST({url:this.url,data:B,success:this.commitSuccess,failure:this.commitFailure,scope:this})
},CLASS_NAME:"Geoportal.Layer.WFS"});
Geoportal.Layer.WMS=OpenLayers.Class(Geoportal.Layer.Grid,{DEFAULT_PARAMS:{service:"WMS",version:"1.1.1",request:"GetMap",styles:"",exceptions:"application/vnd.ogc.se_inimage",format:"image/jpeg"},isBaseLayer:true,encodeBBOX:false,initialize:function(D,C,E,B){var A=[];
E=OpenLayers.Util.upperCaseObject(E);
A.push(D,C,E,B);
Geoportal.Layer.Grid.prototype.initialize.apply(this,A);
OpenLayers.Util.applyDefaults(this.params,OpenLayers.Util.upperCaseObject(this.DEFAULT_PARAMS));
if(this.params.TRANSPARENT&&this.params.TRANSPARENT.toString().toLowerCase()=="true"){if((B==null)||(!B.isBaseLayer)){this.isBaseLayer=false
}if(this.params.FORMAT=="image/jpeg"){this.params.FORMAT=OpenLayers.Util.alphaHack()?"image/gif":"image/png"
}}},destroy:function(){Geoportal.Layer.Grid.prototype.destroy.apply(this,arguments)
},clone:function(A){if(A==null){A=new Geoportal.Layer.WMS(this.name,this.url,this.params,this.options)
}A=Geoportal.Layer.Grid.prototype.clone.apply(this,[A]);
return A
},getURL:function(B){var D=B.clone();
D=this.adjustBounds(D);
if(this.getNativeProjection()){D.transform(this.map.getProjection(),this.getNativeProjection())
}var C=this.getImageSize();
var E={BBOX:this.encodeBBOX?D.toBBOX():D.toArray(),WIDTH:C.w,HEIGHT:C.h};
var A=this.getFullRequestString(E);
return A
},addTile:function(B,A){return new Geoportal.Tile.Image(this,A,B,null,this.tileSize)
},mergeNewParams:function(C){var B=OpenLayers.Util.upperCaseObject(C);
var A=[B];
return Geoportal.Layer.Grid.prototype.mergeNewParams.apply(this,A)
},getFullRequestString:function(C,B){var A=this.getNativeProjection();
this.params.SRS=(A==null)?null:A.getCode();
return Geoportal.Layer.Grid.prototype.getFullRequestString.apply(this,arguments)
},getDataExtent:function(){return this.maxExtent
},CLASS_NAME:"Geoportal.Layer.WMS"});
Geoportal.Layer.WMSC=OpenLayers.Class(Geoportal.Layer.Grid,{DEFAULT_PARAMS:{service:"WMS",version:"1.1.1",request:"GetMap",styles:"",exceptions:"application/vnd.ogc.se_inimage",format:"image/jpeg"},isBaseLayer:false,initialize:function(D,C,E,B){var A=[];
E=OpenLayers.Util.upperCaseObject(E);
A.push(D,C,E,B);
Geoportal.Layer.Grid.prototype.initialize.apply(this,A);
OpenLayers.Util.applyDefaults(this.params,OpenLayers.Util.upperCaseObject(this.DEFAULT_PARAMS));
if(this.params.TRANSPARENT&&this.params.TRANSPARENT.toString().toLowerCase()=="true"){if((B==null)||(!B.isBaseLayer)){this.isBaseLayer=false
}if(this.params.FORMAT=="image/jpeg"){this.params.FORMAT=OpenLayers.Util.alphaHack()?"image/gif":"image/png"
}}},destroy:function(){Geoportal.Layer.Grid.prototype.destroy.apply(this,arguments)
},clone:function(A){if(A==null){A=new Geoportal.Layer.WMSC(this.name,this.url,this.params,this.options)
}A=Geoportal.Layer.Grid.prototype.clone.apply(this,[A]);
return A
},getURL:function(A){if(this.gutter){A=this.adjustBoundsByGutter(A)
}var C;
if(this.getNativeProjection()&&!(this.getNativeProjection().equals(this.map.getProjection()))){var B=A.clone();
B.transform(this.map.getProjection(),this.getNativeProjection());
C=B.toBBOX()
}else{C=A.toBBOX()
}var D={BBOX:C,WIDTH:this.nativeTileSize.w,HEIGHT:this.nativeTileSize.h,TILED:true};
return decodeURIComponent(this.getFullRequestString(D))
},mergeNewParams:function(C){var B=OpenLayers.Util.upperCaseObject(C);
var A=[B];
Geoportal.Layer.Grid.prototype.mergeNewParams.apply(this,A)
},getFullRequestString:function(B){var A=this.getNativeProjection()||this.map.getProjection();
this.params.SRS=(A=="none")?null:A;
return Geoportal.Layer.Grid.prototype.getFullRequestString.apply(this,arguments)
},addTile:function(D,A,C){var B=this.getURL(D);
return new Geoportal.Tile.Image(this,A,D,B,C)
},getDataExtent:function(){return this.maxExtent
},CLASS_NAME:"Geoportal.Layer.WMSC"});
Geoportal.Tile.Image=OpenLayers.Class(OpenLayers.Tile.Image,{setSize:function(A){if(this.frame!=null){OpenLayers.Util.modifyDOMElement(this.frame,null,null,A);
this.size=A;
if(this.imgDiv!=null){OpenLayers.Util.modifyDOMElement(this.imgDiv,null,null,A)
}}},resetBackBuffer:function(){this.showTile();
if(this.backBufferTile&&(this.isFirstDraw||!this.layer.numLoadingTiles)){this.isFirstDraw=false;
var A=this.layer.maxExtent;
var B=(A&&this.bounds.intersectsBounds(A,false));
if(B){this.backBufferTile.position=this.position;
this.backBufferTile.bounds=this.bounds;
this.backBufferTile.size=this.size;
this.backBufferTile.imageSize=this.size||this.layer.imageSize;
this.backBufferTile.imageOffset=this.layer.imageOffset;
this.backBufferTile.resolution=this.layer.getResolution();
this.backBufferTile.renderTile()
}}},renderTile:function(){if(this.imgDiv==null){this.initImgDiv()
}this.imgDiv.viewRequestID=this.layer.map.viewRequestID;
if(this.layer.url instanceof Array){this.imgDiv.urls=this.layer.url.slice()
}this.url=this.layer.getURL(this.bounds);
OpenLayers.Util.modifyDOMElement(this.frame,null,this.position,this.size);
var A=this.size||this.layer.getImageSize();
if(this.layerAlphaHack){OpenLayers.Util.modifyAlphaImageDiv(this.imgDiv,null,null,A,this.url)
}else{OpenLayers.Util.modifyDOMElement(this.imgDiv,null,null,A);
this.imgDiv.src=this.url
}return true
},initImgDiv:function(){var D=this.layer.imageOffset;
var B=this.size||this.layer.getImageSize();
if(this.layerAlphaHack){this.imgDiv=OpenLayers.Util.createAlphaImageDiv(null,D,B,null,"relative",null,null,null,true)
}else{this.imgDiv=OpenLayers.Util.createImage(null,D,B,null,"relative",null,null,true)
}this.imgDiv.className="olTileImage";
this.frame.style.zIndex=this.isBackBuffer?0:1;
this.frame.appendChild(this.imgDiv);
this.layer.div.appendChild(this.frame);
if(this.layer.opacity!=null){OpenLayers.Util.modifyDOMElement(this.imgDiv,null,null,null,null,null,null,this.layer.opacity)
}this.imgDiv.map=this.layer.map;
var C=function(){if(this.isLoading){this.isLoading=false;
this.events.triggerEvent("loadend")
}};
if(this.layerAlphaHack){OpenLayers.Event.observe(this.imgDiv.childNodes[0],"load",OpenLayers.Function.bind(C,this))
}else{OpenLayers.Event.observe(this.imgDiv,"load",OpenLayers.Function.bind(C,this))
}var A=function(){if(this.imgDiv._attempts>OpenLayers.IMAGE_RELOAD_ATTEMPTS){C.call(this)
}};
OpenLayers.Event.observe(this.imgDiv,"error",OpenLayers.Function.bind(A,this))
},CLASS_NAME:"Geoportal.Tile.Image"});