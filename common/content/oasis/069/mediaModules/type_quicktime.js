var url = opener.mediaWindow.url;

document.write ("<embed type='video/quicktime'");
document.write ("    pluginspage='http://www.apple.com/quicktime/download/'");
document.write ("    src='" + url + "'");
document.write ("    scale='TOFIT'");
document.write ("    autoplay='true'");
document.write ("    controller='false'");
document.write ("    width='100%'");
document.write ("    height='100%'");

document.write ('        AutoStart=1');
document.write ('        AutoSize=1');
document.write ('        ShowControls=0');
document.write ('        ShowDisplay=0');
document.write ('        ShowStatusBar=0');
document.write ('        DisplaySize=0');
document.write ('        ShowCaptioning=0');
document.write ('        AllowChangeDisplaySize=0');
document.write ('        EnableContextMenu=0');
document.write (">");

document.write ("</embed>");


