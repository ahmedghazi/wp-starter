var lg_polyfill_flexbox = (function(){

	var lg_always_polyfill_flexbox = window.LG_ALWAYS_POLYFILL_FLEXBOX || false;

	var do_polyfill_flexbox = function(){

		if(!Modernizr.flexbox || lg_always_polyfill_flexbox){
			jQuery('.lg-col.lg-align-middle').each(function(){
				var doMarginTop = false;
				var $self = jQuery(this);
				if(window.innerWidth < lg_lgPassedData.phoneSize){
					$self.css('margin-top', '0');
					if(jQuery($self[0].parentNode.parentNode).hasClass('lg-100vh')){
						if( jQuery($self[0].parentNode).find('.lg-col').length == 1 ){
							doMarginTop = true;
						}
					}
				}
				else{
					doMarginTop = true;			
				}

				if(doMarginTop){
					var rh = jQuery(this.parentNode).outerHeight();
					if(jQuery($self[0].parentNode.parentNode).hasClass('lg-100vh')){
						rh = window.innerHeight;
					}
					var h = $self.outerHeight();
					var mt = (rh - h)/2;
					$self.css('margin-top', mt+'px');	
				}

				if(lg_always_polyfill_flexbox){
					jQuery(this).css({
						'align-self': 'auto',
						'-webkit-align-self': 'auto',
						'-ms-flex-item-align': 'auto'
					});
				}
			});

			// need polyfill for align bottom only inside 100vh row
			jQuery('.lg-col.lg-align-bottom').each(function(){
				var doMarginTop = false;
				var $self = jQuery(this);
				if(window.innerWidth < lg_lgPassedData.phoneSize){
					$self.css('margin-top', '0');
					if(jQuery($self[0].parentNode.parentNode).hasClass('lg-100vh')){
						if( jQuery($self[0].parentNode).find('.lg-col').length == 1 ){
							doMarginTop = true;
						}
					}
				}
				else{
					if(jQuery($self[0].parentNode.parentNode).hasClass('lg-100vh')){
						doMarginTop = true;
					}
				}

				if(doMarginTop){
					var rh = jQuery(this.parentNode).outerHeight();
					if(jQuery($self[0].parentNode.parentNode).hasClass('lg-100vh')){
						rh = window.innerHeight;
					}
					var h = $self.outerHeight();
					// console.log(rh);
					// console.log(h);

					var mt = (rh - h);
					$self.css('margin-top', mt+'px');	
				}

				if(lg_always_polyfill_flexbox){
					jQuery(this).css({
						'align-self': 'auto',
						'-webkit-align-self': 'auto',
						'-ms-flex-item-align': 'auto'
					});
				}

			});
		}
	};

	var bind_resize_orientationchange = function(){
		jQuery(window).on('resize', do_polyfill_flexbox);

		jQuery(window).on('orientationchange', function(){
			// wait until content has rotated
			setTimeout(function() {
				do_polyfill_flexbox();				
			}, 400);
		});
	};

	var init = function(){
		do_polyfill_flexbox();
		bind_resize_orientationchange();
	};

	return {
		init : init,
		do_polyfill_flexbox : do_polyfill_flexbox
	}
}());	

jQuery(document).ready(function(){
	lg_polyfill_flexbox.init();
});

// recognize flexbox

/*! modernizr 3.3.1 (Custom Build) | MIT *
 * https://modernizr.com/download/?-flexbox !*/
!function(e,n,t){function r(e,n){return typeof e===n}function o(){var e,n,t,o,i,s,l;for(var f in v)if(v.hasOwnProperty(f)){if(e=[],n=v[f],n.name&&(e.push(n.name.toLowerCase()),n.options&&n.options.aliases&&n.options.aliases.length))for(t=0;t<n.options.aliases.length;t++)e.push(n.options.aliases[t].toLowerCase());for(o=r(n.fn,"function")?n.fn():n.fn,i=0;i<e.length;i++)s=e[i],l=s.split("."),1===l.length?Modernizr[l[0]]=o:(!Modernizr[l[0]]||Modernizr[l[0]]instanceof Boolean||(Modernizr[l[0]]=new Boolean(Modernizr[l[0]])),Modernizr[l[0]][l[1]]=o),C.push((o?"":"no-")+l.join("-"))}}function i(e,n){return!!~(""+e).indexOf(n)}function s(e){return e.replace(/([a-z])-([a-z])/g,function(e,n,t){return n+t.toUpperCase()}).replace(/^-/,"")}function l(e,n){return function(){return e.apply(n,arguments)}}function f(e,n,t){var o;for(var i in e)if(e[i]in n)return t===!1?e[i]:(o=n[e[i]],r(o,"function")?l(o,t||n):o);return!1}function a(e){return e.replace(/([A-Z])/g,function(e,n){return"-"+n.toLowerCase()}).replace(/^ms-/,"-ms-")}function u(){return"function"!=typeof n.createElement?n.createElement(arguments[0]):b?n.createElementNS.call(n,"http://www.w3.org/2000/svg",arguments[0]):n.createElement.apply(n,arguments)}function d(){var e=n.body;return e||(e=u(b?"svg":"body"),e.fake=!0),e}function p(e,t,r,o){var i,s,l,f,a="modernizr",p=u("div"),c=d();if(parseInt(r,10))for(;r--;)l=u("div"),l.id=o?o[r]:a+(r+1),p.appendChild(l);return i=u("style"),i.type="text/css",i.id="s"+a,(c.fake?c:p).appendChild(i),c.appendChild(p),i.styleSheet?i.styleSheet.cssText=e:i.appendChild(n.createTextNode(e)),p.id=a,c.fake&&(c.style.background="",c.style.overflow="hidden",f=_.style.overflow,_.style.overflow="hidden",_.appendChild(c)),s=t(p,e),c.fake?(c.parentNode.removeChild(c),_.style.overflow=f,_.offsetHeight):p.parentNode.removeChild(p),!!s}function c(n,r){var o=n.length;if("CSS"in e&&"supports"in e.CSS){for(;o--;)if(e.CSS.supports(a(n[o]),r))return!0;return!1}if("CSSSupportsRule"in e){for(var i=[];o--;)i.push("("+a(n[o])+":"+r+")");return i=i.join(" or "),p("@supports ("+i+") { #modernizr { position: absolute; } }",function(e){return"absolute"==getComputedStyle(e,null).position})}return t}function m(e,n,o,l){function f(){d&&(delete E.style,delete E.modElem)}if(l=r(l,"undefined")?!1:l,!r(o,"undefined")){var a=c(e,o);if(!r(a,"undefined"))return a}for(var d,p,m,h,y,v=["modernizr","tspan","samp"];!E.style&&v.length;)d=!0,E.modElem=u(v.shift()),E.style=E.modElem.style;for(m=e.length,p=0;m>p;p++)if(h=e[p],y=E.style[h],i(h,"-")&&(h=s(h)),E.style[h]!==t){if(l||r(o,"undefined"))return f(),"pfx"==n?h:!0;try{E.style[h]=o}catch(g){}if(E.style[h]!=y)return f(),"pfx"==n?h:!0}return f(),!1}function h(e,n,t,o,i){var s=e.charAt(0).toUpperCase()+e.slice(1),l=(e+" "+x.join(s+" ")+s).split(" ");return r(n,"string")||r(n,"undefined")?m(l,n,o,i):(l=(e+" "+S.join(s+" ")+s).split(" "),f(l,n,t))}function y(e,n,r){return h(e,t,t,n,r)}var v=[],g={_version:"3.3.1",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,n){var t=this;setTimeout(function(){n(t[e])},0)},addTest:function(e,n,t){v.push({name:e,fn:n,options:t})},addAsyncTest:function(e){v.push({name:null,fn:e})}},Modernizr=function(){};Modernizr.prototype=g,Modernizr=new Modernizr;var C=[],w="Moz O ms Webkit",x=g._config.usePrefixes?w.split(" "):[];g._cssomPrefixes=x;var S=g._config.usePrefixes?w.toLowerCase().split(" "):[];g._domPrefixes=S;var _=n.documentElement,b="svg"===_.nodeName.toLowerCase(),z={elem:u("modernizr")};Modernizr._q.push(function(){delete z.elem});var E={style:z.elem.style};Modernizr._q.unshift(function(){delete E.style}),g.testAllProps=h,g.testAllProps=y,Modernizr.addTest("flexbox",y("flexBasis","1px",!0)),o(),delete g.addTest,delete g.addAsyncTest;for(var P=0;P<Modernizr._q.length;P++)Modernizr._q[P]();e.Modernizr=Modernizr}(window,document);