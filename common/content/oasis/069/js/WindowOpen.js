//<!--
// Ultimate client-side JavaScript client sniff. Version 3.03
// (C) Netscape Communications 1999-2001.  Permission granted to reuse and distribute.
// Revised 17 May 99 to add is_nav5up and is_ie5up (see below).
// Revised 20 Dec 00 to add is_gecko and change is_nav5up to is_nav6up
//                      also added support for IE5.5 Opera4&5 HotJava3 AOLTV
// Revised 22 Feb 01 to correct Javascript Detection for IE 5.x, Opera 4, 
//                      correct Opera 5 detection
//                      add support for winME and win2k
//                      synch with browser-type-oo.js
// Revised 26 Mar 01 to correct Opera detection
// Revised 02 Oct 01 to add IE6 detection

// Everything you always wanted to know about your JavaScript client
// but were afraid to ask. Creates "is_" variables indicating:
// (1) browser vendor:
//     is_nav, is_ie, is_opera, is_hotjava, is_webtv, is_TVNavigator, is_AOLTV
// (2) browser version number:
//     is_major (integer indicating major version number: 2, 3, 4 ...)
//     is_minor (float   indicating full  version number: 2.02, 3.01, 4.04 ...)
// (3) browser vendor AND major version number
//     is_nav2, is_nav3, is_nav4, is_nav4up, is_nav6, is_nav6up, is_gecko, is_ie3,
//     is_ie4, is_ie4up, is_ie5, is_ie5up, is_ie5_5, is_ie5_5up, is_ie6, is_ie6up, is_hotjava3, is_hotjava3up,
//     is_opera2, is_opera3, is_opera4, is_opera5, is_opera5up
// (4) JavaScript version number:
//     is_js (float indicating full JavaScript version number: 1, 1.1, 1.2 ...)
// (5) OS platform and version:
//     is_win, is_win16, is_win32, is_win31, is_win95, is_winnt, is_win98, is_winme, is_win2k
//     is_os2
//     is_mac, is_mac68k, is_macppc
//     is_unix
//     is_sun, is_sun4, is_sun5, is_suni86
//     is_irix, is_irix5, is_irix6
//     is_hpux, is_hpux9, is_hpux10
//     is_aix, is_aix1, is_aix2, is_aix3, is_aix4
//     is_linux, is_sco, is_unixware, is_mpras, is_reliant
//     is_dec, is_sinix, is_freebsd, is_bsd
//     is_vms
//
// See http://www.it97.de/JavaScript/JS_tutorial/bstat/navobj.html and
// http://www.it97.de/JavaScript/JS_tutorial/bstat/Browseraol.html
// for detailed lists of userAgent strings.
//
// Note: you don't want your Nav4 or IE4 code to "turn off" or
// stop working when new versions of browsers are released, so
// in conditional code forks, use is_ie5up ("IE 5.0 or greater") 
// is_opera5up ("Opera 5.0 or greater") instead of is_ie5 or is_opera5
// to check version in code which you want to work on future
// versions.

    // convert all characters to lowercase to simplify testing
    var agt=navigator.userAgent.toLowerCase();

    // *** BROWSER VERSION ***
    // Note: On IE5, these return 4, so use is_ie5up to detect IE5.
    var is_major = parseInt(navigator.appVersion);
    var is_minor = parseFloat(navigator.appVersion);

    // Note: Opera and WebTV spoof Navigator.  We do strict client detection.
    // If you want to allow spoofing, take out the tests for opera and webtv.
    var is_nav  = ((agt.indexOf('mozilla')!=-1) && (agt.indexOf('spoofer')==-1)
                && (agt.indexOf('compatible') == -1) && (agt.indexOf('opera')==-1)
                && (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1));
    var is_nav2 = (is_nav && (is_major == 2));
    var is_nav3 = (is_nav && (is_major == 3));
    var is_nav4 = (is_nav && (is_major == 4));
    var is_nav4up = (is_nav && (is_major >= 4));
    var is_navonly      = (is_nav && ((agt.indexOf(";nav") != -1) ||
                          (agt.indexOf("; nav") != -1)) );
    var is_nav6 = (is_nav && (is_major == 5));
    var is_nav6up = (is_nav && (is_major >= 5));
    var is_gecko = (agt.indexOf('gecko') != -1);


    var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
    var is_ie3    = (is_ie && (is_major < 4));
    var is_ie4    = (is_ie && (is_major == 4) && (agt.indexOf("msie 4")!=-1) );
    var is_ie4up  = (is_ie && (is_major >= 4));
    var is_ie5    = (is_ie && (is_major == 4) && (agt.indexOf("msie 5.0")!=-1) );
    var is_ie5_5  = (is_ie && (is_major == 4) && (agt.indexOf("msie 5.5") !=-1));
    var is_ie5up  = (is_ie && !is_ie3 && !is_ie4);
    var is_ie5_5up =(is_ie && !is_ie3 && !is_ie4 && !is_ie5);
    var is_ie6    = (is_ie && (is_major == 4) && (agt.indexOf("msie 6.")!=-1) );
    var is_ie6up  = (is_ie && !is_ie3 && !is_ie4 && !is_ie5 && !is_ie5_5);

    // KNOWN BUG: On AOL4, returns false if IE3 is embedded browser
    // or if this is the first browser window opened.  Thus the
    // variables is_aol, is_aol3, and is_aol4 aren't 100% reliable.
    var is_aol   = (agt.indexOf("aol") != -1);
    var is_aol3  = (is_aol && is_ie3);
    var is_aol4  = (is_aol && is_ie4);
    var is_aol5  = (agt.indexOf("aol 5") != -1);
    var is_aol6  = (agt.indexOf("aol 6") != -1);

    var is_opera = (agt.indexOf("opera") != -1);
    var is_opera2 = (agt.indexOf("opera 2") != -1 || agt.indexOf("opera/2") != -1);
    var is_opera3 = (agt.indexOf("opera 3") != -1 || agt.indexOf("opera/3") != -1);
    var is_opera4 = (agt.indexOf("opera 4") != -1 || agt.indexOf("opera/4") != -1);
    var is_opera5 = (agt.indexOf("opera 5") != -1 || agt.indexOf("opera/5") != -1);
    var is_opera5up = (is_opera && !is_opera2 && !is_opera3 && !is_opera4);

    var is_webtv = (agt.indexOf("webtv") != -1); 

    var is_TVNavigator = ((agt.indexOf("navio") != -1) || (agt.indexOf("navio_aoltv") != -1)); 
    var is_AOLTV = is_TVNavigator;

    var is_hotjava = (agt.indexOf("hotjava") != -1);
    var is_hotjava3 = (is_hotjava && (is_major == 3));
    var is_hotjava3up = (is_hotjava && (is_major >= 3));

    // *** JAVASCRIPT VERSION CHECK ***
    var is_js;
    if (is_nav2 || is_ie3) is_js = 1.0;
    else if (is_nav3) is_js = 1.1;
    else if (is_opera5up) is_js = 1.3;
    else if (is_opera) is_js = 1.1;
    else if ((is_nav4 && (is_minor <= 4.05)) || is_ie4) is_js = 1.2;
    else if ((is_nav4 && (is_minor > 4.05)) || is_ie5) is_js = 1.3;
    else if (is_hotjava3up) is_js = 1.4;
    else if (is_nav6 || is_gecko) is_js = 1.5;
    // NOTE: In the future, update this code when newer versions of JS
    // are released. For now, we try to provide some upward compatibility
    // so that future versions of Nav and IE will show they are at
    // *least* JS 1.x capable. Always check for JS version compatibility
    // with > or >=.
    else if (is_nav6up) is_js = 1.5;
    // NOTE: ie5up on mac is 1.4
    else if (is_ie5up) is_js = 1.3

    // HACK: no idea for other browsers; always check for JS version with > or >=
    else is_js = 0.0;

    // *** PLATFORM ***
    var is_win   = ( (agt.indexOf("win")!=-1) || (agt.indexOf("16bit")!=-1) );
    // NOTE: On Opera 3.0, the userAgent string includes "Windows 95/NT4" on all
    //        Win32, so you can't distinguish between Win95 and WinNT.
    var is_win95 = ((agt.indexOf("win95")!=-1) || (agt.indexOf("windows 95")!=-1));

    // is this a 16 bit compiled version?
    var is_win16 = ((agt.indexOf("win16")!=-1) || 
               (agt.indexOf("16bit")!=-1) || (agt.indexOf("windows 3.1")!=-1) || 
               (agt.indexOf("windows 16-bit")!=-1) );  

    var is_win31 = ((agt.indexOf("windows 3.1")!=-1) || (agt.indexOf("win16")!=-1) ||
                    (agt.indexOf("windows 16-bit")!=-1));

    var is_winme = ((agt.indexOf("win 9x 4.90")!=-1));
    var is_win2k = ((agt.indexOf("windows nt 5.0")!=-1));

    // NOTE: Reliable detection of Win98 may not be possible. It appears that:
    //       - On Nav 4.x and before you'll get plain "Windows" in userAgent.
    //       - On Mercury client, the 32-bit version will return "Win98", but
    //         the 16-bit version running on Win98 will still return "Win95".
    var is_win98 = ((agt.indexOf("win98")!=-1) || (agt.indexOf("windows 98")!=-1));
    var is_winnt = ((agt.indexOf("winnt")!=-1) || (agt.indexOf("windows nt")!=-1));
    var is_win32 = (is_win95 || is_winnt || is_win98 || 
                    ((is_major >= 4) && (navigator.platform == "Win32")) ||
                    (agt.indexOf("win32")!=-1) || (agt.indexOf("32bit")!=-1));

    var is_os2   = ((agt.indexOf("os/2")!=-1) || 
                    (navigator.appVersion.indexOf("OS/2")!=-1) ||   
                    (agt.indexOf("ibm-webexplorer")!=-1));

    var is_mac    = (agt.indexOf("mac")!=-1);
    // hack ie5 js version for mac
    if (is_mac && is_ie5up) is_js = 1.4;
    var is_mac68k = (is_mac && ((agt.indexOf("68k")!=-1) || 
                               (agt.indexOf("68000")!=-1)));
    var is_macppc = (is_mac && ((agt.indexOf("ppc")!=-1) || 
                                (agt.indexOf("powerpc")!=-1)));

    var is_sun   = (agt.indexOf("sunos")!=-1);
    var is_sun4  = (agt.indexOf("sunos 4")!=-1);
    var is_sun5  = (agt.indexOf("sunos 5")!=-1);
    var is_suni86= (is_sun && (agt.indexOf("i86")!=-1));
    var is_irix  = (agt.indexOf("irix") !=-1);    // SGI
    var is_irix5 = (agt.indexOf("irix 5") !=-1);
    var is_irix6 = ((agt.indexOf("irix 6") !=-1) || (agt.indexOf("irix6") !=-1));
    var is_hpux  = (agt.indexOf("hp-ux")!=-1);
    var is_hpux9 = (is_hpux && (agt.indexOf("09.")!=-1));
    var is_hpux10= (is_hpux && (agt.indexOf("10.")!=-1));
    var is_aix   = (agt.indexOf("aix") !=-1);      // IBM
    var is_aix1  = (agt.indexOf("aix 1") !=-1);    
    var is_aix2  = (agt.indexOf("aix 2") !=-1);    
    var is_aix3  = (agt.indexOf("aix 3") !=-1);    
    var is_aix4  = (agt.indexOf("aix 4") !=-1);    
    var is_linux = (agt.indexOf("inux")!=-1);
    var is_sco   = (agt.indexOf("sco")!=-1) || (agt.indexOf("unix_sv")!=-1);
    var is_unixware = (agt.indexOf("unix_system_v")!=-1); 
    var is_mpras    = (agt.indexOf("ncr")!=-1); 
    var is_reliant  = (agt.indexOf("reliantunix")!=-1);
    var is_dec   = ((agt.indexOf("dec")!=-1) || (agt.indexOf("osf1")!=-1) || 
           (agt.indexOf("dec_alpha")!=-1) || (agt.indexOf("alphaserver")!=-1) || 
           (agt.indexOf("ultrix")!=-1) || (agt.indexOf("alphastation")!=-1)); 
    var is_sinix = (agt.indexOf("sinix")!=-1);
    var is_freebsd = (agt.indexOf("freebsd")!=-1);
    var is_bsd = (agt.indexOf("bsd")!=-1);
    var is_unix  = ((agt.indexOf("x11")!=-1) || is_sun || is_irix || is_hpux || 
                 is_sco ||is_unixware || is_mpras || is_reliant || 
                 is_dec || is_sinix || is_aix || is_linux || is_bsd || is_freebsd);

    var is_vms   = ((agt.indexOf("vax")!=-1) || (agt.indexOf("openvms")!=-1));

//--> end hide JavaScript

var windowNumber = 0;
var windowFrame = {width:0,height:0};
var sizeMainWindow = {width:0,height:0};
var mediaWindow = {top:0,left:0};
var currentParams = {};

/*
if (opener && opener.windowFrame) {
	windowFrame = opener.windowFrame;
}
*/

function WindowOpenMain( width, height, windowName, param ) {
	
	sizeMainWindow.width = width;
	sizeMainWindow.height = height;
	
	if (windowNumber == 0) {
		var left = (screen.width - width) / 2;
		var top = (screen.height - height) / 2;
	}
	else {
		var tmp_left = (screen.width - width) / 2 + windowNumber * 20;
		var tmp_top = (screen.height - height) / 2 + windowNumber * 20;
		var left = Math.max(0, Math.min(tmp_left, screen.width - width));
		var top = Math.max(0, Math.min(tmp_top, screen.height - height));
	}
	var keyword = param.keyword;
	var pagecode = param.pagecode;
	var paramStr = "";
	if(keyword){
		keyword = encodeURI(keyword);
		paramStr = "?k=" + keyword;
	}else{
		paramStr = "?";
	}
	if(pagecode){
		if(paramStr != "?"){
			paramStr = paramStr + "&";
		}
		paramStr = paramStr + "pagecode=" + pagecode;
	}else{
		if(paramStr == "?"){
			paramStr = "";
		}
	}
	var hwnd = window.open("_SWF_Window.html" + paramStr, windowName, "width=" + width + ",height=" + height + ","
		+ "resizable=" + ((param.resizable)?param.resizable:'yes') + ","
		+ "screenX=" + left + ","
		+ "screenY=" + top + ","
		+ "left=" + left + ","
		+ "top=" + top + ","
		+ "scrollbars=no"
	);
	windowNumber++;
	if (is_ie) {
		windowFrame.width = tmp_left - left;
		windowFrame.height = tmp_top - top;
	}
	
}
function WindowOpenSample( width, height, windowName, param ) {
	
	sizeMainWindow.width = width;
	sizeMainWindow.height = height;
	
	if (windowNumber == 0) {
		var left = (screen.width - width) / 2;
		var top = (screen.height - height) / 2;
	}
	else {
		var tmp_left = (screen.width - width) / 2 + windowNumber * 20;
		var tmp_top = (screen.height - height) / 2 + windowNumber * 20;
		var left = Math.max(0, Math.min(tmp_left, screen.width - width));
		var top = Math.max(0, Math.min(tmp_top, screen.height - height));
	}
	var hwnd = window.open("_SWF_Window.html?mode=1063", windowName, "width=" + width + ",height=" + height + ","
		+ "resizable=" + ((param.resizable)?param.resizable:'yes') + ","
		+ "screenX=" + left + ","
		+ "screenY=" + top + ","
		+ "left=" + left + ","
		+ "top=" + top + ","
		+ "scrollbars=no"
	);
	windowNumber++;
	if (is_ie) {
		windowFrame.width = tmp_left - left;
		windowFrame.height = tmp_top - top;
	}
	
}
function WindowOpenMedia( url, params ) {
	
	var width_match = new String(params.match( /width=([0-9]+)/i ));
	var height_match = new String(params.match( /height=([0-9]+)/i ));
	var width = width_match.substring(width_match.indexOf(",")+1);
	var height = height_match.substring(height_match.indexOf(",")+1);
	var left = (screen.width - width) / 2;
	var top = (screen.height - height) / 2;
	
	params = params + ((params == '')?'':',')
		+ "resizable=yes,"
		+ "screenX=" + left + ","
		+ "screenY=" + top + ","
		+ "left=" + left + ","
		+ "top=" + top + "";
	var hwnd = window.open( url, "MediaWindow", params );
	
}

function getLeftMargin() {
	if (is_ie) {
		return windowFrame.width + 2;
	}
	if (is_gecko) {
		return 3;
	}
	if (is_opera) {
		return window.screenX;
	}
	return 0;
}

function getTopMargin() {
	if (is_ie) {
		return windowFrame.height + 2;
	}
	if (is_gecko) {
		return 23;
	}
	if (is_opera) {
		return window.screenY;
	}
	return 0;
}

function getWindowLeft() {
	if (is_ie) {
		return window.screenLeft;
	}
	if (is_gecko) {
		return window.screenX;
	}
	if (is_opera) {
		return window.screenX;
	}
	return (window.screenLeft | window.screenX);
}

function getWindowTop() {
	if (is_ie) {
		return window.screenTop;
	}
	if (is_gecko) {
		return window.screenY;
	}
	if (is_opera) {
		return window.screenY;
	}
	return (window.screenTop | window.screenY);
}

function getInnerWidth() {
	if (is_ie) {
		return document.body.clientWidth;
	}
	if (is_gecko) {
		return window.innerWidth;
	}
	if (is_opera) {
		return window.innerWidth;
	}
	return (window.innerWidth | document.body.clientWidth);
}

function getInnerHeight() {
	if (is_ie) {
		return document.body.clientHeight;
	}
	if (is_gecko) {
		return window.innerHeight;
	}
	if (is_opera) {
		return window.innerHeight;
	}
	return (window.innerHeight | document.body.clientHeight);
}

function getTopToolbarMargin() {
	if (is_ie) {
		return 0;//windowFrame.height + 2;
	}
	if (is_gecko) {
		return 67;
	}
	if (is_opera) {
		return 0;//window.screenY;
	}
	return 0;
}

function _parseParamString( str ) {
	
	var params = new Array;
	var tmp = str.match( /[^=,&]+=[^=,&]+/ig );
	for ( var i = 0 ; i < tmp.length ; i++ ) {
		var tmp2 = tmp[i].match( /([^=,&]+)=([^=,&]+)/i );
		params[tmp2[1]] = tmp2[2];
	}
	
	return params;
	
}

function _joinParamString( paramArray ) {
	
	var retString = "";
	for ( key in paramArray ) {
		
		retString += key + "=" + paramArray[key] + ",";
		
	}
	if (retString.length !== 0) {
		retString = retString.substring(0, retString.length - 1);
	}
	return retString;
	
}

function getMediaType( url ) {
	
	var ext = url.match( /(?:\.([^\/\.]+))?$/i );
	
	var type = "page";
	
	if (ext[1]) {
		switch (ext[1].toLowerCase()) {
		case "avi":
		case "asf":
		case "asx":
		case "wmv":
			type = "wmv";
			break;
		case "mp3":
			type = "mp3";
			break;
		case "swf":
			type = "swf";
			break;
		case "flv":
			type = "flv";
			break;
		case "mov":
		case "mpeg":
		case "mpg":
			type = "quicktime";
			break;
		case "jpg":
		case "jpeg":
		case "png":
		case "gif":
			type = "image";
			break;
		}
	}
	
	return type;
	
}
var typesProperty = {
	"page":{
		"toolbar":"yes"
	},
	"wmv":{
		"toolbar":"no"
	},
	"mp3":{
		"toolbar":"no"
	},
	"swf":{
		"toolbar":"no"
	},
	"quicktime":{
		"toolbar":"no"
	},
	"image":{
		"toolbar":"no"
	},
	"flv":{
		"toolbar":"no"
	}
};

/**
 * メディア用Windowオープン関数
 * 
 * @param string url URL
 * @param string paramString パラメータをkey1=value1,key2=value2,...の形式で指定する
 */
function WindowOpenRelative( url, paramString ) {
	
	var params = _parseParamString(paramString);
	this.currentParams = _parseParamString(paramString);
	
	var type = getMediaType(url);
	
	
	if (!params.toolbar) {
		params.toolbar = typesProperty[type].toolbar;
	}
	
	var autoScaleX = (opener && opener.sizeMainWindow.width != 0)?getInnerWidth() / opener.sizeMainWindow.width:1.0;
	var autoScaleY = (opener && opener.sizeMainWindow.height != 0)?getInnerHeight() / opener.sizeMainWindow.height:1.0;
	
	var scaleX = (params.scaleX)?Number(params.scaleX) / 100.0:autoScaleX;
	var scaleY = (params.scaleY)?Number(params.scaleY) / 100.0:autoScaleY;
	
	var width = Number(params.width) * scaleX;
	var height = Number(params.height) * scaleY;
	var relleft = Number(params.relleft) * scaleX;
	var reltop = Number(params.reltop) * scaleY;
	
	var left = getWindowLeft() + relleft/* + getLeftMargin()*/;
	var top = getWindowTop() + reltop - ((params.toolbar == "yes")?getTopToolbarMargin():0);
	
	// FullScreen
	if ((is_gecko || is_ie) && params.fullscreen == "yes") {
		left = 0;
		top = 0;
		//alert("width " + width);
		//alert("width " + width);

		width = screen.width - (windowFrame.width * 2);
		height = screen.height - (windowFrame.height);
		//alert("width " + width);
		//alert("width " + width);
		params.fullscreen = "no";
	}
	
	params.resizable = "yes";
	params.screenX = left;
	params.screenY = top;
	params.left = left;
	params.top = top;
	params.width = width;
	params.height = height;
	params.allowscriptaccess = "always";
	
	
	mediaWindow.width = width;
	mediaWindow.height = height;
	mediaWindow.top = top;
	mediaWindow.left = left;
	mediaWindow.type = type;
	if(type == "flv"){
		mediaWindow.type = "swf";
		mediaWindow.url = "FlvPlayer.swf";
		mediaWindow.swfpath = url;
	}else{
		mediaWindow.url = url;		
	}
	
	var paramString = _joinParamString(params);

	mediaWindow.params = paramString;
	//var hwnd = window.open( "MediaTemplate.html", "_blank", paramString );
	document.getElementById("open").click();
}
function openMediaWindow(paramString){
	window.open('MediaTemplate.html', "_blank", paramString);
}
function onLoadMediaWindow() {
	
	if (is_ie) {
		if (opener.currentParams.fullscreen != "yes") {
			var moveRelX = -(getWindowLeft() - opener.mediaWindow.left);
			var moveRelY = -(getWindowTop() - opener.mediaWindow.top);
			//window.moveBy(moveRelX, moveRelY);
		}
	}
	
}
function putMediaTag() {
	
	document.write("<script type=\"text/javascript\" src=\"./mediaModules/type_" + opener.mediaWindow.type + ".js\" charset=\"euc-jp\"></script>");
	
}


function html5_load(){
 if(navigator.userAgent.indexOf("iPhone") != -1 || navigator.userAgent.indexOf("iPod") != -1||navigator.userAgent.indexOf("iPad") != -1||navigator.userAgent.indexOf("Android") != -1){
                  window.open("./HTML5/Main.html");		
  }else{
         WindowOpenMain(1000,670,'_blank',{'resizable':'yes'});
  }   
}