/*
 jQuery Cookie plugin

 Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 Dual licensed under the MIT and GPL licenses:
 http://www.opensource.org/licenses/mit-license.php
 http://www.gnu.org/licenses/gpl.html

*/
jQuery.cookie=function(e,b,a){if(arguments.length>1&&(b===null||typeof b!=="object")){a=jQuery.extend({},a);if(b===null)a.expires=-1;if(typeof a.expires==="number"){var d=a.expires,c=a.expires=new Date;c.setDate(c.getDate()+d)}return document.cookie=[encodeURIComponent(e),"=",a.raw?String(b):encodeURIComponent(String(b)),a.expires?"; expires="+a.expires.toUTCString():"",a.path?"; path="+a.path:"",a.domain?"; domain="+a.domain:"",a.secure?"; secure":""].join("")}a=b||{};c=a.raw?function(a){return a}:
decodeURIComponent;return(d=RegExp("(?:^|; )"+encodeURIComponent(e)+"=([^;]*)").exec(document.cookie))?c(d[1]):null};
