var url = opener.mediaWindow.url;

if (navigator.platform == "Win32") {
	
	// Windows Media Player 6.4 Code
	
	//IE Code
	document.write ('<OBJECT ID=MediaPlayer ');
	document.write ('CLASSID=CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95 ');
	document.write ('CODEBASE=http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715 ');
	document.write ('standby="Loading Microsoft Windows Media Player components..." ');
	document.write ('TYPE="application/x-oleobject" width="100%" height="100%">');
	document.write ('<PARAM NAME="FileName" VALUE="' + url + '">');
	document.write ('<PARAM NAME="AutoStart" VALUE="true">');
	document.write ('<PARAM NAME="AutoSize" VALUE="true">');
	document.write ('<PARAM NAME="ShowControls" VALUE="true">');
	document.write ('<PARAM NAME="ShowDisplay" VALUE="0">');
	document.write ('<PARAM NAME="ShowStatusBar" VALUE="0">');
	document.write ('<PARAM NAME="DisplaySize" VALUE="0">');
	document.write ('<PARAM NAME="ShowCaptioning" VALUE="0">');
	document.write ('<PARAM NAME="AllowChangeDisplaySize" VALUE="0">');
	document.write ('<PARAM NAME="EnableContextMenu" VALUE="0">');
	
	//Netscape code
	document.write ('    <Embed type="application/x-mplayer2"');
	document.write ('        pluginspage="http://www.microsoft.com/windows/windowsmedia/"');
	document.write ('        filename="' + url + '"');
	document.write ('        src="' + url + '"');
	document.write ('        Name=MediaPlayer');
	document.write ('        AutoStart=1');
	document.write ('        AutoSize=1');
	document.write ('        ShowControls=1');
	document.write ('        ShowDisplay=0');
	document.write ('        ShowStatusBar=0');
	document.write ('        DisplaySize=0');
	document.write ('        ShowCaptioning=0');
	document.write ('        AllowChangeDisplaySize=0');
	document.write ('        EnableContextMenu=0');
	document.write ('        width=100%');
	document.write ('        height=100%>');
	document.write ('    </embed>');
	
	document.write ('</OBJECT>');
	
}
else if (navigator.platform == "MacPPC") {
	
	document.write ("<embed type='video/quicktime'");
	document.write ("    pluginspage='http://www.apple.com/quicktime/download/'");
	document.write ("    src='" + url + "'");
	document.write ("    scale='TOFIT'");
	document.write ("    autoplay='true'");
	document.write ("    controller='false'");
	document.write ("    width='100%'");
	document.write ("    height='100%'>");
	document.write ("</embed>");
	
}

