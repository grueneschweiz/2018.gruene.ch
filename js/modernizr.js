!function(e,n){var o=[],s={_version:"3.12.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,n){var s=this;setTimeout(function(){n(s[e])},0)},addTest:function(e,n,s){o.push({name:e,fn:n,options:s})},addAsyncTest:function(e){o.push({name:null,fn:e})}},a=function(){},t=(a.prototype=s,a=new a,[]);var i,l,f,c,r,p,u,d,h,g,m,n=n.documentElement,w="svg"===n.nodeName.toLowerCase();for(u in o)if(o.hasOwnProperty(u)){if(i=[],(l=o[u]).name&&(i.push(l.name.toLowerCase()),l.options)&&l.options.aliases&&l.options.aliases.length)for(f=0;f<l.options.aliases.length;f++)i.push(l.options.aliases[f].toLowerCase());for(c="function"==typeof l.fn?l.fn():l.fn,r=0;r<i.length;r++)1===(p=i[r].split(".")).length?a[p[0]]=c:(a[p[0]]&&(!a[p[0]]||a[p[0]]instanceof Boolean)||(a[p[0]]=new Boolean(a[p[0]])),a[p[0]][p[1]]=c),t.push((c?"":"no-")+p.join("-"))}d=t,g=n.className,m=a._config.classPrefix||"",w&&(g=g.baseVal),a._config.enableJSClass&&(h=new RegExp("(^|\\s)"+m+"no-js(\\s|$)"),g=g.replace(h,"$1"+m+"js$2")),a._config.enableClasses&&(0<d.length&&(g+=" "+m+d.join(" "+m)),w?n.className.baseVal=g:n.className=g),delete s.addTest,delete s.addAsyncTest;for(var _=0;_<a._q.length;_++)a._q[_]();e.Modernizr=a}(window,(window,document));