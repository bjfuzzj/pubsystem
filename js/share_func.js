////////////////////////////////
///
///Html preview
///
////////////////////////////////
function toCookie(cookiename,cookievalue,expire) 
{ 
        document.cookie = '' ;
        expr =makeYearExpDate(expire) ;
        cookiename = fixSep(cookiename) ;
        astr = fixSep(cookievalue) ;
        if(expire != 0){
        	astr = cookiename + '=' + astr + ';expires=' + expr + ';path=/'  + '';
        }else{
                astr = cookiename + '=' + astr + ';path=/'  + '';
        }
        document.cookie=astr ;
        return false;
} 

function makeYearExpDate(yr) 
{ 
        var expire = new Date (); 
        expire.setTime (expire.getTime() + ((yr *365) *24 * 60 * 60 * 1000)); 
        expire = expire.toGMTString() ;
        return expire; 
} 

function fixSep(what) 
{ 
        n=0 ;
        while ( n >= 0 ) { 
                n = what.indexOf(';',n) ;
                if (n < 0) return what ;
                else { 
                        what = what.substring(0,n) + escape(';') + what.substring(n+1,what.length) ;
                        n++ ;
                } 
        } 
        return what ;
} 

function y2k(number) 
 { 
  return (number < 1000) ? number + 1900 : number; 
 } 

function getTime() 
{ 

  var LocalNow = new Date(); 

  var AbsoluteNow = new Date(); 

  AbsoluteNow.setTime(LocalNow.getTime() + LocalNow.getTimezoneOffset()*60); 

  //alert(LocalNow.getTimezoneOffset()); 

  var PekingDate = new Date(); 

  PekingDate.setTime(AbsoluteNow.getTime()+8*3600); 

  var thisYear = PekingDate.getYear(); 

  var thisMonth = PekingDate.getMonth() + 1; 

  var thisDay = PekingDate.getDate(); 

  var str = y2k(thisYear)+'年'; 

  if (thisMonth < 10) 

   str += '0'; 

  str += thisMonth + '月'; 

  if (thisDay < 10) 

   str += '0'; 

  str += thisDay + '日'; 

  return str; 

 } 

////////////////////////////////
///
///open a new navigator window
///
///js_callpage("http://192.168.23.200:8344/publish/sys_entry.html","Sina_publish_system","toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes");
////////////////////////////////
function js_callpage(htmlurl,title,winattrib) {
	if(navigator.appName.indexOf("Netscape") != -1)
	  {
	    newwin=window.open(htmlurl,title,winattrib);
	    //self.window.close();
	    //self.window=null;
	    newwin.focus();
	  }else{ 
	     if(self.newwin != null){
		  self.newwin.close();
		  newwin = null;
	          newwin=window.open(htmlurl,title,winattrib);
		}else{
	          newwin=window.open(htmlurl,title,winattrib);
	     } 
 	     //self.window=null;
	     //self.window.close();
	     newwin.focus();
	 }
	 return true;
}


function actionclick(my_form,_action)
{
   my_form._action.value=_action;
   if(_action =="insert")
   {
    my_form.submit();return true;
   }
   else
   {
    len     =       my_form.elements.length;
    var     index   =       0;
    var     ele_checked=false;
    for( index=0; index < len; index++ )
    {
    if( my_form.elements[index].name == "_u_id" && my_form.elements[index].checked==true)
     {
      ele_checked=true;
      if(_action=="del"){
        if(prompt("请确认是否真的删除?(yes/no)","no")=="yes"){
                my_form.submit();return true;
        }else{
                return false;
        }
      }else
      {
      	my_form.submit();return true;
      }
     }
    }
    if(!ele_checked){alert("请先选择一个用户.");return false;}
   } 
}


