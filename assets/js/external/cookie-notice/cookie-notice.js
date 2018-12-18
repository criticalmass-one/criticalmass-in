/*! cookie-notice-js v1.0.0 by Alessandro Benoit 2018-04-23 */

!function(){"use strict";var v,h={messageLocales:{it:"Utilizziamo i cookie per essere sicuri che tu possa avere la migliore esperienza sul nostro sito. Se continui ad utilizzare questo sito assumiamo che tu ne sia felice.",en:"We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.",de:"Wir verwenden Cookies um sicherzustellen dass Sie das beste Erlebnis auf unserer Website haben.",fr:"Nous utilisons des cookies afin d'être sûr que vous pouvez avoir la meilleure expérience sur notre site. Si vous continuez à utiliser ce site, nous supposons que vous acceptez."},cookieNoticePosition:"bottom",learnMoreLinkEnabled:!1,learnMoreLinkHref:"/cookie-banner-information.html",learnMoreLinkText:{it:"Saperne di più",en:"Learn more",de:"Mehr erfahren",fr:"En savoir plus"},buttonLocales:{en:"Ok"},expiresIn:30,buttonBgColor:"#d35400",buttonTextColor:"#fff",noticeBgColor:"#000",noticeTextColor:"#fff",linkColor:"#009fdd"};function x(e){var o=(navigator.userLanguage||navigator.language).substr(0,2);return e[o]?e[o]:e.en}document.addEventListener("DOMContentLoaded",function(){v||new cookieNoticeJS}),window.cookieNoticeJS=function(){if(void 0===v&&(v=this,document.cookie="testCookie=1",-1!=document.cookie.indexOf("testCookie")&&-1==document.cookie.indexOf("cookie_notice"))){var e,o,t,n,i,r,a=function e(o,t){var n;for(n in t)t.hasOwnProperty(n)&&("object"==typeof o[n]?o[n]=e(o[n],t[n]):o[n]=t[n]);return o}(h,arguments[0]||{}),s=function(e,o,t,n){var i=document.createElement("div"),r=i.style;i.innerHTML=e+"&nbsp;",i.setAttribute("id","cookieNotice"),r.position="fixed","top"===n?r.top="0":r.bottom="0";return r.left="0",r.right="0",r.background=o,r.color=t,r["z-index"]="999",r.padding="10px 5px",r["text-align"]="center",r["font-size"]="12px",r["line-height"]="28px",r.fontFamily="Helvetica neue, Helvetica, sans-serif",i}(x(a.messageLocales),a.noticeBgColor,a.noticeTextColor,a.cookieNoticePosition);if(a.learnMoreLinkEnabled){var c=x(a.learnMoreLinkText);o=c,t=a.learnMoreLinkHref,n=a.linkColor,i=document.createElement("a"),r=i.style,i.href=t,i.textContent=o,i.target="_blank",i.className="learn-more",r.color=n,r["text-decoration"]="none",r.display="inline",e=i}var l,u,d,p,f,m=x(a.buttonLocales),k=(l=m,u=a.buttonBgColor,d=a.buttonTextColor,p=document.createElement("a"),f=p.style,p.href="#",p.innerHTML=l,p.className="confirm",f.background=u,f.color=d,f["text-decoration"]="none",f.display="inline-block",f.padding="0 15px",f.margin="0 0 0 10px",p);k.addEventListener("click",function(e){var o,t,n,i;e.preventDefault(),o=60*parseInt(a.expiresIn+"",10)*1e3*60*24,t=new Date,(n=new Date).setTime(t.getTime()+o),document.cookie="cookie_notice=1; expires="+n.toUTCString()+"; path=/;",(i=s).style.opacity=1,function e(){(i.style.opacity-=.1)<.01?document.body.removeChild(i):setTimeout(e,40)}()});var b=document.body.appendChild(s);e&&b.appendChild(e),b.appendChild(k)}}}();