/* 
 * リファラーから検索文字列を取得しArrayを返す
 * 引数referrerが指定されてるときはその文字列（URL）から検索文字列を取得しArrayを返す
 */
function getSearchWords(referrer) {
	
	//引数が無ければリファラーを取得
	if (!referrer) {
		referrer = document.referrer;
	}
	
	//referrer（文字列）をパースしてURIオブジェクトへ（要parseUri 1.2.2）
	uri = parseUri(referrer);
	
	//ホスト判定してからクエリの検索文字列と区切り文字を取得
	queryString = "";
	delimiter = "+";
	
	//文字列のデコードを行う関数（要ecl.js Ver.041208）
	DecodeFunction = UnescapeUTF8;
		
	switch(uri.host) {
		case "www.google.com":            if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} break;
		case "www.google.co.jp":          if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} break;
		case "search.msn.co.jp":          if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} else 
		                                  if (queryString = uri.queryKey.MT)     {DecodeFunction = UnescapeUTF8;} break;
		case "www.bing.com":	          if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} break;
		case "search.livedoor.com":       if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeEUCJP;} break;
		case "home.search.biglobe.ne.jp": if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeEUCJP;} break;
		case "search.live.com":           if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} break;
		case "jp.ask.com":                if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} break;			
		case "search.nifty.com":          if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} else 
		                                  if (queryString = uri.queryKey.Text)   {DecodeFunction = UnescapeUTF8;} break;
		case "www.altavista.com":         if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} break;
		case "www.alltheweb.com":         if (queryString = uri.queryKey.q)      {DecodeFunction = UnescapeUTF8;} break;
		case "search.yahoo.co.jp":        if (queryString = uri.queryKey.p)      {DecodeFunction = UnescapeUTF8;} break;
		case "search.yahoo.com":          if (queryString = uri.queryKey.p)      {DecodeFunction = UnescapeUTF8;} break;
		case "search.goo.ne.jp":          if (queryString = uri.queryKey.MT)     {DecodeFunction = UnescapeEUCJP;} break;
		case "www.excite.co.jp":          if (queryString = uri.queryKey.search) {DecodeFunction = UnescapeSJIS;} break;
		case "search.www.infoseek.co.jp": if (queryString = uri.queryKey.qt)     {DecodeFunction = UnescapeUTF8;} break;
		default:
			return "";
		break;
	}

	//検索文字列デコード＋Array化
	words = queryString=="" ? [] : DecodeFunction(queryString).split(delimiter);	
	return words;
}

function SetHighLightWords(words, HighLightID, BeginHighLightTAG, EndHighLightTAG) {
  if (words == "" || HighLightID == ""){ return "";}
  
  var TaregetText = document.getElementById(HighLightID).innerHTML;
    
  var pattern = new RegExp(words.join("|"), "gi");
    
  var SetHighLightTag = function SetHighLightTag(SearchWord){
  	if ((BeginHighLightTAG =="" || EndHighLightTAG=="") || (!BeginHighLightTAG || !EndHighLightTAG)){  
		return '<span style="color:#FF0000; font-weight:bold;">' + SearchWord + '</span>';
	}else{
		return BeginHighLightTAG + SearchWord + EndHighLightTAG;
	}
  }
  
  document.getElementById(HighLightID).innerHTML = TaregetText.replace(pattern, SetHighLightTag);   
}