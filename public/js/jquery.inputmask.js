!function(e){function t(e){var t=document.createElement("input"),e="on"+e,a=e in t;return a||(t.setAttribute(e,"return;"),a="function"==typeof t[e]),t=null,a}function a(t,i,n){var r=n.aliases[t];return r?(r.alias&&a(r.alias,void 0,n),e.extend(!0,n,r),e.extend(!0,n,i),!0):!1}function i(t){function a(a){t.numericInput&&(a=a.split("").reverse().join(""));var i=!1,n=0,r=t.greedy,s=t.repeat;"*"==s&&(r=!1),1==a.length&&0==r&&0!=s&&(t.placeholder="");for(var o=e.map(a.split(""),function(e,a){var r=[];if(e==t.escapeChar)i=!0;else if(e!=t.optionalmarker.start&&e!=t.optionalmarker.end||i){var s=t.definitions[e];if(s&&!i)for(var o=0;o<s.cardinality;o++)r.push(t.placeholder.charAt((n+o)%t.placeholder.length));else r.push(e),i=!1;return n+=r.length,r}}),l=o.slice(),u=1;s>u&&r;u++)l=l.concat(o.slice());return{mask:l,repeat:s,greedy:r}}function i(a){t.numericInput&&(a=a.split("").reverse().join(""));var i=!1,n=!1,r=!1;return e.map(a.split(""),function(e,a){var s=[];if(e==t.escapeChar)n=!0;else if(e!=t.optionalmarker.start||n){if(e!=t.optionalmarker.end||n){var o=t.definitions[e];if(o&&!n){for(var l=o.prevalidator,u=l?l.length:0,d=1;d<o.cardinality;d++){var c=u>=d?l[d-1]:[],p=c.validator,f=c.cardinality;s.push({fn:p?"string"==typeof p?new RegExp(p):new function(){this.test=p}:new RegExp("."),cardinality:f?f:1,optionality:i,newBlockMarker:1==i?r:!1,offset:0,casing:o.casing,def:o.definitionSymbol||e}),1==i&&(r=!1)}s.push({fn:o.validator?"string"==typeof o.validator?new RegExp(o.validator):new function(){this.test=o.validator}:new RegExp("."),cardinality:o.cardinality,optionality:i,newBlockMarker:r,offset:0,casing:o.casing,def:o.definitionSymbol||e})}else s.push({fn:null,cardinality:0,optionality:i,newBlockMarker:r,offset:0,casing:null,def:e}),n=!1;return r=!1,s}i=!1,r=!0}else i=!0,r=!0})}function n(e){return t.optionalmarker.start+e+t.optionalmarker.end}function r(e){for(var a=0,i=0,n=e.length,r=0;n>r&&(e.charAt(r)==t.optionalmarker.start&&a++,e.charAt(r)==t.optionalmarker.end&&i++,!(a>0&&a==i));r++);var s=[e.substring(0,r)];return n>r&&s.push(e.substring(r+1,n)),s}function s(e){for(var a=e.length,i=0;a>i&&e.charAt(i)!=t.optionalmarker.start;i++);var n=[e.substring(0,i)];return a>i&&n.push(e.substring(i+1,a)),n}function o(t,d,c){var p,f,v=r(d),m=s(v[0]);m.length>1?(p=t+m[0]+n(m[1])+(v.length>1?v[1]:""),-1==e.inArray(p,u)&&""!=p&&(u.push(p),f=a(p),l.push({mask:p,_buffer:f.mask,buffer:f.mask.slice(),tests:i(p),lastValidPosition:-1,greedy:f.greedy,repeat:f.repeat,metadata:c})),p=t+m[0]+(v.length>1?v[1]:""),-1==e.inArray(p,u)&&""!=p&&(u.push(p),f=a(p),l.push({mask:p,_buffer:f.mask,buffer:f.mask.slice(),tests:i(p),lastValidPosition:-1,greedy:f.greedy,repeat:f.repeat,metadata:c})),s(m[1]).length>1&&o(t+m[0],m[1]+v[1],c),v.length>1&&s(v[1]).length>1&&(o(t+m[0]+n(m[1]),v[1],c),o(t+m[0],v[1],c))):(p=t+v,-1==e.inArray(p,u)&&""!=p&&(u.push(p),f=a(p),l.push({mask:p,_buffer:f.mask,buffer:f.mask.slice(),tests:i(p),lastValidPosition:-1,greedy:f.greedy,repeat:f.repeat,metadata:c})))}var l=[],u=[];return e.isFunction(t.mask)&&(t.mask=t.mask.call(this,t)),e.isArray(t.mask)?e.each(t.mask,function(e,t){void 0!=t.mask?o("",t.mask.toString(),t):o("",t.toString())}):o("",t.mask.toString()),t.greedy?l:l.sort(function(e,t){return e.mask.length-t.mask.length})}function n(t,a,i,n){function d(){return t[a]}function c(){return d().tests}function p(){return d()._buffer}function f(){return d().buffer}function v(n,r,s){function o(e,t,a,n){for(var r=h(e),s=a?1:0,o="",l=t.buffer,u=t.tests[r].cardinality;u>s;u--)o+=_(l,r-(u-1));return a&&(o+=a),null!=t.tests[r].fn?t.tests[r].fn.test(o,l,e,n,i):a==_(t._buffer,e,!0)||a==i.skipOptionalPartCharacter?{refresh:!0,c:_(t._buffer,e,!0),pos:e}:!1}function l(a,i){var s=!1;if(e.each(i,function(t,i){return s=-1==e.inArray(i.activeMasksetIndex,a)&&i.result!==!1,s?!1:void 0}),s)i=e.map(i,function(i,n){return-1==e.inArray(i.activeMasksetIndex,a)?i:void(t[i.activeMasksetIndex].lastValidPosition=m)});else{var l,u=-1,d=-1;e.each(i,function(t,i){-1!=e.inArray(i.activeMasksetIndex,a)&&i.result!==!1&(-1==u||u>i.result.pos)&&(u=i.result.pos,d=i.activeMasksetIndex)}),i=e.map(i,function(i,s){if(-1!=e.inArray(i.activeMasksetIndex,a)){if(i.result.pos==u)return i;if(i.result!==!1){for(var c=n;u>c;c++){if(l=o(c,t[i.activeMasksetIndex],t[d].buffer[c],!0),l===!1){t[i.activeMasksetIndex].lastValidPosition=u-1;break}b(t[i.activeMasksetIndex].buffer,c,t[d].buffer[c],!0),t[i.activeMasksetIndex].lastValidPosition=c}return l=o(u,t[i.activeMasksetIndex],r,!0),l!==!1&&(b(t[i.activeMasksetIndex].buffer,u,r,!0),t[i.activeMasksetIndex].lastValidPosition=u),i}}})}return i}if(s=s===!0){var u=o(n,d(),r,s);return u===!0&&(u={pos:n}),u}var c=[],u=!1,p=a,v=f().slice(),m=d().lastValidPosition,P=(x(n),[]);return e.each(t,function(e,t){if("object"==typeof t){a=e;var i,l=n,h=d().lastValidPosition;if(h==m){if(l-m>1)for(var x=-1==h?0:h;l>x&&(i=o(x,d(),v[x],!0),i!==!1);x++){b(f(),x,v[x],!0),i===!0&&(i={pos:x});var _=i.pos||x;d().lastValidPosition<_&&(d().lastValidPosition=_)}if(!y(l)&&!o(l,d(),r,s)){for(var A=k(l)-l,C=0;A>C&&o(++l,d(),r,s)===!1;C++);P.push(a)}}if((d().lastValidPosition>=m||a==p)&&l>=0&&l<g()){if(u=o(l,d(),r,s),u!==!1){u===!0&&(u={pos:l});var _=u.pos||l;d().lastValidPosition<_&&(d().lastValidPosition=_)}c.push({activeMasksetIndex:e,result:u})}}}),a=p,l(P,c)}function m(){var i=a,n={activeMasksetIndex:0,lastValidPosition:-1,next:-1};e.each(t,function(e,t){"object"==typeof t&&(a=e,d().lastValidPosition>n.lastValidPosition?(n.activeMasksetIndex=e,n.lastValidPosition=d().lastValidPosition,n.next=k(d().lastValidPosition)):d().lastValidPosition==n.lastValidPosition&&(-1==n.next||n.next>k(d().lastValidPosition))&&(n.activeMasksetIndex=e,n.lastValidPosition=d().lastValidPosition,n.next=k(d().lastValidPosition)))}),a=-1!=n.lastValidPosition&&t[i].lastValidPosition==n.lastValidPosition?i:n.activeMasksetIndex,i!=a&&(C(f(),k(n.lastValidPosition),g()),d().writeOutBuffer=!0),W.data("_inputmask").activeMasksetIndex=a}function y(e){var t=h(e),a=c()[t];return void 0!=a?a.fn:!1}function h(e){return e%c().length}function g(){return i.getMaskLength(p(),d().greedy,d().repeat,f(),i)}function k(e){var t=g();if(e>=t)return t;for(var a=e;++a<t&&!y(a););return a}function x(e){var t=e;if(0>=t)return 0;for(;--t>0&&!y(t););return t}function b(e,t,a,i){i&&(t=P(e,t));var n=c()[h(t)],r=a;if(void 0!=r&&void 0!=n)switch(n.casing){case"upper":r=a.toUpperCase();break;case"lower":r=a.toLowerCase()}e[t]=r}function _(e,t,a){return a&&(t=P(e,t)),e[t]}function P(e,t){for(var a;void 0==e[t]&&e.length<g();)for(a=0;void 0!==p()[a];)e.push(p()[a++]);return t}function A(e,t,a){e._valueSet(t.join("")),void 0!=a&&V(e,a)}function C(e,t,a,i){for(var n=t,r=g();a>n&&r>n;n++)i===!0?y(n)||b(e,n,""):b(e,n,_(p().slice(),n,!0))}function E(e,t){var a=h(t);b(e,t,_(p(),a))}function M(e){return i.placeholder.charAt(e%i.placeholder.length)}function I(i,n,r,s,o){var l=void 0!=s?s.slice():S(i._valueGet()).split("");e.each(t,function(e,t){"object"==typeof t&&(t.buffer=t._buffer.slice(),t.lastValidPosition=-1,t.p=-1)}),r!==!0&&(a=0),n&&i._valueSet("");g();e.each(l,function(t,a){if(o===!0){var s=d().p,l=-1==s?s:x(s),u=-1==l?t:k(l);-1==e.inArray(a,p().slice(l+1,u))&&H.call(i,void 0,!0,a.charCodeAt(0),n,r,t)}else H.call(i,void 0,!0,a.charCodeAt(0),n,r,t)}),r===!0&&-1!=d().p&&(d().lastValidPosition=x(d().p))}function w(t){return e.inputmask.escapeRegex.call(this,t)}function S(e){return e.replace(new RegExp("("+w(p().join(""))+")*$"),"")}function R(e){for(var t,a,i=f(),n=i.slice(),a=n.length-1;a>=0;a--){var t=h(a);if(!c()[t].optionality)break;if(y(a)&&v(a,i[a],!0))break;n.pop()}A(e,n)}function j(t,a){if(!c()||a!==!0&&t.hasClass("hasDatepicker"))return t[0]._valueGet();var n=e.map(f(),function(e,t){return y(t)&&v(t,e,!0)?e:null}),r=(Q?n.reverse():n).join("");return void 0!=i.onUnMask?i.onUnMask.call(this,f().join(""),r):r}function O(e){if(Q&&"number"==typeof e&&(!i.greedy||""!=i.placeholder)){var t=f().length;e=t-e}return e}function V(t,a,n){var r,s=t.jquery&&t.length>0?t[0]:t;return"number"!=typeof a?e(t).is(":visible")?(s.setSelectionRange?(a=s.selectionStart,n=s.selectionEnd):document.selection&&document.selection.createRange&&(r=document.selection.createRange(),a=0-r.duplicate().moveStart("character",-1e5),n=a+r.text.length),a=O(a),n=O(n),{begin:a,end:n}):{begin:0,end:0}:(a=O(a),n=O(n),e(t).is(":visible")&&(n="number"==typeof n?n:a,s.scrollLeft=s.scrollWidth,0==i.insertMode&&a==n&&n++,s.setSelectionRange?(s.selectionStart=a,s.selectionEnd=o?a:n):s.createTextRange&&(r=s.createTextRange(),r.collapse(!0),r.moveEnd("character",n),r.moveStart("character",a),r.select())),void 0)}function D(n){if("*"!=i.repeat){var r=!1,s=0,o=a;return e.each(t,function(e,t){if("object"==typeof t){a=e;var i=x(g());if(t.lastValidPosition>=s&&t.lastValidPosition==i){for(var o=!0,l=0;i>=l;l++){var u=y(l),d=h(l);if(u&&(void 0==n[l]||n[l]==M(l))||!u&&n[l]!=p()[d]){o=!1;break}}if(r=r||o)return!1}s=t.lastValidPosition}}),a=o,r}}function G(e,t){return Q?e-t>1||e-t==1&&i.insertMode:t-e>1||t-e==1&&i.insertMode}function T(t){var a=e._data(t).events;e.each(a,function(t,a){e.each(a,function(e,t){if("inputmask"==t.namespace&&"setvalue"!=t.type){var a=t.handler;t.handler=function(e){return this.readOnly||this.disabled?void e.preventDefault:a.apply(this,arguments)}}})})}function K(t){var a;if(Object.getOwnPropertyDescriptor&&(a=Object.getOwnPropertyDescriptor(t,"value")),a&&a.get){if(!t._valueGet){var i=a.get,n=a.set;t._valueGet=function(){return Q?i.call(this).split("").reverse().join(""):i.call(this)},t._valueSet=function(e){n.call(this,Q?e.split("").reverse().join(""):e)},Object.defineProperty(t,"value",{get:function(){var t=e(this),a=e(this).data("_inputmask"),n=a.masksets,r=a.activeMasksetIndex;return a&&a.opts.autoUnmask?t.inputmask("unmaskedvalue"):i.call(this)!=n[r]._buffer.join("")?i.call(this):""},set:function(t){n.call(this,t),e(this).triggerHandler("setvalue.inputmask")}})}}else if(document.__lookupGetter__&&t.__lookupGetter__("value")){if(!t._valueGet){var i=t.__lookupGetter__("value"),n=t.__lookupSetter__("value");t._valueGet=function(){return Q?i.call(this).split("").reverse().join(""):i.call(this)},t._valueSet=function(e){n.call(this,Q?e.split("").reverse().join(""):e)},t.__defineGetter__("value",function(){var t=e(this),a=e(this).data("_inputmask"),n=a.masksets,r=a.activeMasksetIndex;return a&&a.opts.autoUnmask?t.inputmask("unmaskedvalue"):i.call(this)!=n[r]._buffer.join("")?i.call(this):""}),t.__defineSetter__("value",function(t){n.call(this,t),e(this).triggerHandler("setvalue.inputmask")})}}else if(t._valueGet||(t._valueGet=function(){return Q?this.value.split("").reverse().join(""):this.value},t._valueSet=function(e){this.value=Q?e.split("").reverse().join(""):e}),void 0==e.valHooks.text||1!=e.valHooks.text.inputmaskpatch){var i=e.valHooks.text&&e.valHooks.text.get?e.valHooks.text.get:function(e){return e.value},n=e.valHooks.text&&e.valHooks.text.set?e.valHooks.text.set:function(e,t){return e.value=t,e};jQuery.extend(e.valHooks,{text:{get:function(t){var a=e(t);if(a.data("_inputmask")){if(a.data("_inputmask").opts.autoUnmask)return a.inputmask("unmaskedvalue");var n=i(t),r=a.data("_inputmask"),s=r.masksets,o=r.activeMasksetIndex;return n!=s[o]._buffer.join("")?n:""}return i(t)},set:function(t,a){var i=e(t),r=n(t,a);return i.data("_inputmask")&&i.triggerHandler("setvalue.inputmask"),r},inputmaskpatch:!0}})}}function N(e,t,a,i){var n=f();if(i!==!1)for(;!y(e)&&e-1>=0;)e--;for(var r=e;t>r&&r<g();r++)if(y(r)){E(n,r);var s=k(r),o=_(n,s);if(o!=M(s))if(s<g()&&v(r,o,!0)!==!1&&c()[h(r)].def==c()[h(s)].def)b(n,r,o,!0);else if(y(r))break}else E(n,r);if(void 0!=a&&b(n,x(t),a),0==d().greedy){var l=S(n.join("")).split("");n.length=l.length;for(var r=0,u=n.length;u>r;r++)n[r]=l[r];0==n.length&&(d().buffer=p().slice())}return e}function L(e,t,a){var i=f();if(_(i,e,!0)!=M(e))for(var n=x(t);n>e&&n>=0;n--)if(y(n)){var r=x(n),s=_(i,r);s!=M(r)&&v(r,s,!0)!==!1&&c()[h(n)].def==c()[h(r)].def&&(b(i,n,s,!0),E(i,r))}else E(i,n);void 0!=a&&_(i,e)==M(e)&&b(i,e,a);var o=i.length;if(0==d().greedy){var l=S(i.join("")).split("");i.length=l.length;for(var n=0,u=i.length;u>n;n++)i[n]=l[n];0==i.length&&(d().buffer=p().slice())}return t-(o-i.length)}function U(e,a,n){if(i.numericInput||Q){switch(a){case i.keyCode.BACKSPACE:a=i.keyCode.DELETE;break;case i.keyCode.DELETE:a=i.keyCode.BACKSPACE}if(Q){var r=n.end;n.end=n.begin,n.begin=r}}var s=!0;if(n.begin==n.end){var o=a==i.keyCode.BACKSPACE?n.begin-1:n.begin;i.isNumeric&&""!=i.radixPoint&&f()[o]==i.radixPoint&&(n.begin=f().length-1==o?n.begin:a==i.keyCode.BACKSPACE?o:k(o),n.end=n.begin),s=!1,a==i.keyCode.BACKSPACE?n.begin--:a==i.keyCode.DELETE&&n.end++}else n.end-n.begin!=1||i.insertMode||(s=!1,a==i.keyCode.BACKSPACE&&n.begin--);C(f(),n.begin,n.end);var l=g();if(0==i.greedy)N(n.begin,l,void 0,!Q&&a==i.keyCode.BACKSPACE&&!s);else{for(var u=n.begin,c=n.begin;c<n.end;c++)!y(c)&&s||(u=N(n.begin,l,void 0,!Q&&a==i.keyCode.BACKSPACE&&!s));s||(n.begin=u)}var p=k(-1);C(f(),n.begin,n.end,!0),I(e,!1,void 0==t[1]||p>=n.end,f()),d().lastValidPosition<p?(d().lastValidPosition=-1,d().p=p):d().p=n.begin}function B(t){q=!1;var a=this,n=e(a),r=t.keyCode,o=V(a);r==i.keyCode.BACKSPACE||r==i.keyCode.DELETE||s&&127==r||t.ctrlKey&&88==r?(t.preventDefault(),88==r&&(Z=f().join("")),U(a,r,o),m(),A(a,f(),d().p),a._valueGet()==p().join("")&&n.trigger("cleared"),i.showTooltip&&n.prop("title",d().mask)):r==i.keyCode.END||r==i.keyCode.PAGE_DOWN?setTimeout(function(){var e=k(d().lastValidPosition);i.insertMode||e!=g()||t.shiftKey||e--,V(a,t.shiftKey?o.begin:e,e)},0):r==i.keyCode.HOME&&!t.shiftKey||r==i.keyCode.PAGE_UP?V(a,0,t.shiftKey?o.begin:0):r==i.keyCode.ESCAPE||90==r&&t.ctrlKey?(I(a,!0,!1,Z.split("")),n.click()):r!=i.keyCode.INSERT||t.shiftKey||t.ctrlKey?0!=i.insertMode||t.shiftKey||(r==i.keyCode.RIGHT?setTimeout(function(){var e=V(a);V(a,e.begin)},0):r==i.keyCode.LEFT&&setTimeout(function(){var e=V(a);V(a,e.begin-1)},0)):(i.insertMode=!i.insertMode,V(a,i.insertMode||o.begin!=g()?o.begin:o.begin-1));var l=V(a);i.onKeyDown.call(this,t,f(),i)===!0&&V(a,l.begin,l.end),X=-1!=e.inArray(r,i.ignorables)}function H(n,r,s,o,l,u){if(void 0==s&&q)return!1;q=!0;var c=this,p=e(c);n=n||window.event;var s=r?s:n.which||n.charCode||n.keyCode;if(!(r===!0||n.ctrlKey&&n.altKey)&&(n.ctrlKey||n.metaKey||X))return!0;if(s){r!==!0&&46==s&&0==n.shiftKey&&","==i.radixPoint&&(s=44);var y,h,P,C=String.fromCharCode(s);if(r){var E=l?u:d().lastValidPosition+1;y={begin:E,end:E}}else y=V(c);var I=G(y.begin,y.end),w=a;I&&(a=w,e.each(t,function(e,t){"object"==typeof t&&(a=e,d().undoBuffer=f().join(""))}),U(c,i.keyCode.DELETE,y),i.insertMode||e.each(t,function(e,t){"object"==typeof t&&(a=e,L(y.begin,g()),d().lastValidPosition=k(d().lastValidPosition))}),a=w);var S=f().join("").indexOf(i.radixPoint);i.isNumeric&&r!==!0&&-1!=S&&(i.greedy&&y.begin<=S?(y.begin=x(y.begin),y.end=y.begin):C==i.radixPoint&&(y.begin=S,y.end=y.begin));var R=y.begin;h=v(R,C,l),l===!0&&(h=[{activeMasksetIndex:a,result:h}]);var j=-1;if(e.each(h,function(e,t){a=t.activeMasksetIndex,d().writeOutBuffer=!0;var n=t.result;if(n!==!1){var r=!1,s=f();if(n!==!0&&(r=n.refresh,R=void 0!=n.pos?n.pos:R,C=void 0!=n.c?n.c:C),r!==!0){if(1==i.insertMode){for(var o=g(),u=s.slice();_(u,o,!0)!=M(o)&&o>=R;)o=0==o?-1:x(o);if(o>=R){L(R,g(),C);var c=d().lastValidPosition,p=k(c);p!=g()&&c>=R&&_(f(),p,!0)!=M(p)&&(d().lastValidPosition=p)}else d().writeOutBuffer=!1}else b(s,R,C,!0);(-1==j||j>k(R))&&(j=k(R))}else if(!l){var v=R<g()?R+1:R;(-1==j||j>v)&&(j=v)}j>d().p&&(d().p=j)}}),l!==!0&&(a=w,m()),o!==!1&&(e.each(h,function(e,t){return t.activeMasksetIndex==a?(P=t,!1):void 0}),void 0!=P)){var O=this;if(setTimeout(function(){i.onKeyValidation.call(O,P.result,i)},0),d().writeOutBuffer&&P.result!==!1){var T,K=f();T=r?void 0:i.numericInput?R>S?x(j):C==i.radixPoint?j-1:x(j-1):j,A(c,K,T),r!==!0&&setTimeout(function(){D(K)===!0&&p.trigger("complete"),J=!0,p.trigger("input")},0)}else I&&(d().buffer=d().undoBuffer.split(""))}i.showTooltip&&p.prop("title",d().mask),n&&(n.preventDefault?n.preventDefault():n.returnValue=!1)}}function F(t){var a=e(this),n=this,r=t.keyCode,s=f();l&&r==i.keyCode.BACKSPACE&&$==n._valueGet()&&B.call(this,t),i.onKeyUp.call(this,t,s,i),r==i.keyCode.TAB&&i.showMaskOnFocus&&(a.hasClass("focus.inputmask")&&0==n._valueGet().length?(s=p().slice(),A(n,s),V(n,0),Z=f().join("")):(A(n,s),s.join("")==p().join("")&&-1!=e.inArray(i.radixPoint,s)?(V(n,O(0)),a.click()):V(n,O(0),O(g()))))}function Y(t){if(J===!0)return J=!1,!0;var a=this,i=e(a);$=f().join(""),I(a,!1,!1),A(a,f()),D(f())===!0&&i.trigger("complete"),i.click()}function z(n){if(W=e(n),W.is(":input")){if(W.data("_inputmask",{masksets:t,activeMasksetIndex:a,opts:i,isRTL:!1}),i.showTooltip&&W.prop("title",d().mask),d().greedy=d().greedy?d().greedy:0==d().repeat,null!=W.attr("maxLength")){var s=W.prop("maxLength");s>-1&&e.each(t,function(e,t){"object"==typeof t&&"*"==t.repeat&&(t.repeat=s)}),g()>=s&&s>-1&&(s<p().length&&(p().length=s),0==d().greedy&&(d().repeat=Math.round(s/p().length)),W.prop("maxLength",2*g()))}if(K(n),i.numericInput&&(i.isNumeric=i.numericInput),("rtl"==n.dir||i.numericInput&&i.rightAlignNumerics||i.isNumeric&&i.rightAlignNumerics)&&W.css("text-align","right"),"rtl"==n.dir||i.numericInput){n.dir="ltr",W.removeAttr("dir");var o=W.data("_inputmask");o.isRTL=!0,W.data("_inputmask",o),Q=!0}W.unbind(".inputmask"),W.removeClass("focus.inputmask"),W.closest("form").bind("submit",function(){Z!=f().join("")&&W.change()}).bind("reset",function(){setTimeout(function(){W.trigger("setvalue")},0)}),W.bind("mouseenter.inputmask",function(){var t=e(this),a=this;!t.hasClass("focus.inputmask")&&i.showMaskOnHover&&a._valueGet()!=f().join("")&&A(a,f())}).bind("blur.inputmask",function(){var n=e(this),r=this,s=r._valueGet(),o=f();n.removeClass("focus.inputmask"),Z!=f().join("")&&n.change(),i.clearMaskOnLostFocus&&""!=s&&(s==p().join("")?r._valueSet(""):R(r)),D(o)===!1&&(n.trigger("incomplete"),i.clearIncomplete&&(e.each(t,function(e,t){"object"==typeof t&&(t.buffer=t._buffer.slice(),t.lastValidPosition=-1)}),a=0,i.clearMaskOnLostFocus?r._valueSet(""):(o=p().slice(),A(r,o))))}).bind("focus.inputmask",function(){var t=e(this),a=this,n=a._valueGet();i.showMaskOnFocus&&!t.hasClass("focus.inputmask")&&(!i.showMaskOnHover||i.showMaskOnHover&&""==n)&&a._valueGet()!=f().join("")&&A(a,f(),k(d().lastValidPosition)),t.addClass("focus.inputmask"),Z=f().join("")}).bind("mouseleave.inputmask",function(){var t=e(this),a=this;i.clearMaskOnLostFocus&&(t.hasClass("focus.inputmask")||a._valueGet()==t.attr("placeholder")||(a._valueGet()==p().join("")||""==a._valueGet()?a._valueSet(""):R(a)))}).bind("click.inputmask",function(){var t=this;setTimeout(function(){var a=V(t),n=f();if(a.begin==a.end){var r,s=Q?O(a.begin):a.begin,o=d().lastValidPosition;r=i.isNumeric&&i.skipRadixDance===!1&&""!=i.radixPoint&&-1!=e.inArray(i.radixPoint,n)?i.numericInput?k(e.inArray(i.radixPoint,n)):e.inArray(i.radixPoint,n):k(o),r>s?y(s)?V(t,s):V(t,k(s)):V(t,r)}},0)}).bind("dblclick.inputmask",function(){var e=this;setTimeout(function(){V(e,0,k(d().lastValidPosition))},0)}).bind(u+".inputmask dragdrop.inputmask drop.inputmask",function(t){if(J===!0)return J=!1,!0;var a=this,n=e(a);return"propertychange"==t.type&&a._valueGet().length<=g()?!0:void setTimeout(function(){var e=void 0!=i.onBeforePaste?i.onBeforePaste.call(this,a._valueGet()):a._valueGet();I(a,!0,!1,e.split(""),!0),D(f())===!0&&n.trigger("complete"),n.click()},0)}).bind("setvalue.inputmask",function(){var e=this;I(e,!0),Z=f().join(""),e._valueGet()==p().join("")&&e._valueSet("")}).bind("complete.inputmask",i.oncomplete).bind("incomplete.inputmask",i.onincomplete).bind("cleared.inputmask",i.oncleared).bind("keyup.inputmask",F),l?W.bind("input.inputmask",Y):W.bind("keydown.inputmask",B).bind("keypress.inputmask",H),r&&W.bind("input.inputmask",Y),I(n,!0,!1),Z=f().join("");var c;try{c=document.activeElement}catch(v){}c===n?(W.addClass("focus.inputmask"),V(n,k(d().lastValidPosition))):i.clearMaskOnLostFocus?f().join("")==p().join("")?n._valueSet(""):R(n):A(n,f()),T(n)}}var W,$,Q=!1,Z=f().join(""),q=!1,J=!1,X=!1;if(void 0!=n)switch(n.action){case"isComplete":return D(n.buffer);case"unmaskedvalue":return Q=n.$input.data("_inputmask").isRTL,j(n.$input,n.skipDatepickerCheck);case"mask":z(n.el);break;case"format":return W=e({}),W.data("_inputmask",{masksets:t,activeMasksetIndex:a,opts:i,isRTL:i.numericInput}),i.numericInput&&(i.isNumeric=i.numericInput,Q=!0),I(W,!1,!1,n.value.split(""),!0),f().join("")}}if(void 0===e.fn.inputmask){var r=null!==navigator.userAgent.match(new RegExp("msie 10","i")),s=null!==navigator.userAgent.match(new RegExp("iphone","i")),o=null!==navigator.userAgent.match(new RegExp("android.*safari.*","i")),l=null!==navigator.userAgent.match(new RegExp("android.*chrome.*","i")),u=t("paste")?"paste":t("input")?"input":"propertychange";e.inputmask={defaults:{placeholder:"_",optionalmarker:{start:"[",end:"]"},quantifiermarker:{start:"{",end:"}"},groupmarker:{start:"(",end:")"},escapeChar:"\\",mask:null,oncomplete:e.noop,onincomplete:e.noop,oncleared:e.noop,repeat:0,greedy:!0,autoUnmask:!1,clearMaskOnLostFocus:!0,insertMode:!0,clearIncomplete:!1,aliases:{},onKeyUp:e.noop,onKeyDown:e.noop,onBeforePaste:void 0,onUnMask:void 0,showMaskOnFocus:!0,showMaskOnHover:!0,onKeyValidation:e.noop,skipOptionalPartCharacter:" ",showTooltip:!1,numericInput:!1,isNumeric:!1,radixPoint:"",skipRadixDance:!1,rightAlignNumerics:!0,definitions:{9:{validator:"[0-9]",cardinality:1},a:{validator:"[A-Za-zА-яЁё]",cardinality:1},"*":{validator:"[A-Za-zА-яЁё0-9]",cardinality:1}},keyCode:{ALT:18,BACKSPACE:8,CAPS_LOCK:20,COMMA:188,COMMAND:91,COMMAND_LEFT:91,COMMAND_RIGHT:93,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,MENU:93,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38,WINDOWS:91},ignorables:[8,9,13,19,27,33,34,35,36,37,38,39,40,45,46,93,112,113,114,115,116,117,118,119,120,121,122,123],getMaskLength:function(e,t,a,i,n){var r=e.length;return t||("*"==a?r=i.length+1:a>1&&(r+=e.length*(a-1))),r}},escapeRegex:function(e){var t=["/",".","*","+","?","|","(",")","[","]","{","}","\\"];return e.replace(new RegExp("(\\"+t.join("|\\")+")","gim"),"\\$1")},format:function(t,r){var s=e.extend(!0,{},e.inputmask.defaults,r);return a(s.alias,r,s),n(i(s),0,s,{action:"format",value:t})}},e.fn.inputmask=function(t,r){var s,o=e.extend(!0,{},e.inputmask.defaults,r),l=0;if("string"==typeof t)switch(t){case"mask":return a(o.alias,r,o),s=i(o),0==s.length?this:this.each(function(){n(e.extend(!0,{},s),0,o,{action:"mask",el:this})});case"unmaskedvalue":var u=e(this);return u.data("_inputmask")?(s=u.data("_inputmask").masksets,l=u.data("_inputmask").activeMasksetIndex,o=u.data("_inputmask").opts,n(s,l,o,{action:"unmaskedvalue",$input:u})):u.val();case"remove":return this.each(function(){var t=e(this),a=this;if(t.data("_inputmask")){s=t.data("_inputmask").masksets,l=t.data("_inputmask").activeMasksetIndex,o=t.data("_inputmask").opts,a._valueSet(n(s,l,o,{action:"unmaskedvalue",$input:t,skipDatepickerCheck:!0})),t.removeData("_inputmask"),t.unbind(".inputmask"),t.removeClass("focus.inputmask");var i;Object.getOwnPropertyDescriptor&&(i=Object.getOwnPropertyDescriptor(a,"value")),i&&i.get?a._valueGet&&Object.defineProperty(a,"value",{get:a._valueGet,set:a._valueSet}):document.__lookupGetter__&&a.__lookupGetter__("value")&&a._valueGet&&(a.__defineGetter__("value",a._valueGet),a.__defineSetter__("value",a._valueSet));try{delete a._valueGet,delete a._valueSet}catch(r){a._valueGet=void 0,a._valueSet=void 0}}});case"getemptymask":return this.data("_inputmask")?(s=this.data("_inputmask").masksets,l=this.data("_inputmask").activeMasksetIndex,s[l]._buffer.join("")):"";case"hasMaskedValue":return this.data("_inputmask")?!this.data("_inputmask").opts.autoUnmask:!1;case"isComplete":return s=this.data("_inputmask").masksets,l=this.data("_inputmask").activeMasksetIndex,o=this.data("_inputmask").opts,n(s,l,o,{action:"isComplete",buffer:this[0]._valueGet().split("")});case"getmetadata":return this.data("_inputmask")?(s=this.data("_inputmask").masksets,l=this.data("_inputmask").activeMasksetIndex,s[l].metadata):void 0;default:return a(t,r,o)||(o.mask=t),s=i(o),0==s.length?this:this.each(function(){n(e.extend(!0,{},s),l,o,{action:"mask",el:this})})}else{if("object"==typeof t)return o=e.extend(!0,{},e.inputmask.defaults,t),a(o.alias,t,o),s=i(o),0==s.length?this:this.each(function(){n(e.extend(!0,{},s),l,o,{action:"mask",el:this})});if(void 0==t)return this.each(function(){var t=e(this).attr("data-inputmask");if(t&&""!=t)try{t=t.replace(new RegExp("'","g"),'"');var i=e.parseJSON("{"+t+"}");e.extend(!0,i,r),o=e.extend(!0,{},e.inputmask.defaults,i),a(o.alias,i,o),o.alias=void 0,e(this).inputmask(o)}catch(n){}})}}}}(jQuery),function(e){e.extend(e.inputmask.defaults.definitions,{h:{validator:"[01][0-9]|2[0-3]",cardinality:2,prevalidator:[{validator:"[0-2]",cardinality:1}]},s:{validator:"[0-5][0-9]",cardinality:2,prevalidator:[{validator:"[0-5]",cardinality:1}]},d:{validator:"0[1-9]|[12][0-9]|3[01]",cardinality:2,prevalidator:[{validator:"[0-3]",cardinality:1}]},m:{validator:"0[1-9]|1[012]",cardinality:2,prevalidator:[{validator:"[01]",cardinality:1}]},y:{validator:"(19|20)\\d{2}",cardinality:4,prevalidator:[{validator:"[12]",cardinality:1},{validator:"(19|20)",cardinality:2},{validator:"(19|20)\\d",cardinality:3}]}}),e.extend(e.inputmask.defaults.aliases,{"dd/mm/yyyy":{mask:"1/2/y",placeholder:"dd/mm/yyyy",regex:{val1pre:new RegExp("[0-3]"),val1:new RegExp("0[1-9]|[12][0-9]|3[01]"),val2pre:function(t){var a=e.inputmask.escapeRegex.call(this,t);return new RegExp("((0[1-9]|[12][0-9]|3[01])"+a+"[01])")},val2:function(t){var a=e.inputmask.escapeRegex.call(this,t);return new RegExp("((0[1-9]|[12][0-9])"+a+"(0[1-9]|1[012]))|(30"+a+"(0[13-9]|1[012]))|(31"+a+"(0[13578]|1[02]))")}},leapday:"29/02/",separator:"/",yearrange:{minyear:1900,maxyear:2099},isInYearRange:function(e,t,a){var i=parseInt(e.concat(t.toString().slice(e.length))),n=parseInt(e.concat(a.toString().slice(e.length)));return(NaN!=i?i>=t&&a>=i:!1)||(NaN!=n?n>=t&&a>=n:!1)},determinebaseyear:function(e,t,a){var i=(new Date).getFullYear();if(e>i)return e;if(i>t){for(var n=t.toString().slice(0,2),r=t.toString().slice(2,4);n+a>t;)n--;var s=n+r;return e>s?e:s}return i},onKeyUp:function(t,a,i){var n=e(this);if(t.ctrlKey&&t.keyCode==i.keyCode.RIGHT){var r=new Date;n.val(r.getDate().toString()+(r.getMonth()+1).toString()+r.getFullYear().toString())}},definitions:{1:{validator:function(e,t,a,i,n){var r=n.regex.val1.test(e);return i||r||e.charAt(1)!=n.separator&&-1=="-./".indexOf(e.charAt(1))||!(r=n.regex.val1.test("0"+e.charAt(0)))?r:(t[a-1]="0",{pos:a,c:e.charAt(0)})},cardinality:2,prevalidator:[{validator:function(e,t,a,i,n){var r=n.regex.val1pre.test(e);return i||r||!(r=n.regex.val1.test("0"+e))?r:(t[a]="0",a++,{pos:a})},cardinality:1}]},2:{validator:function(e,t,a,i,n){var r=t.join("").substr(0,3);-1!=r.indexOf(n.placeholder[0])&&(r="01"+n.separator);var s=n.regex.val2(n.separator).test(r+e);return i||s||e.charAt(1)!=n.separator&&-1=="-./".indexOf(e.charAt(1))||!(s=n.regex.val2(n.separator).test(r+"0"+e.charAt(0)))?s:(t[a-1]="0",{pos:a,c:e.charAt(0)})},cardinality:2,prevalidator:[{validator:function(e,t,a,i,n){var r=t.join("").substr(0,3);-1!=r.indexOf(n.placeholder[0])&&(r="01"+n.separator);var s=n.regex.val2pre(n.separator).test(r+e);return i||s||!(s=n.regex.val2(n.separator).test(r+"0"+e))?s:(t[a]="0",a++,{pos:a})},cardinality:1}]},y:{validator:function(e,t,a,i,n){if(n.isInYearRange(e,n.yearrange.minyear,n.yearrange.maxyear)){var r=t.join("").substr(0,6);if(r!=n.leapday)return!0;var s=parseInt(e,10);return s%4===0?s%100===0?s%400===0:!0:!1}return!1},cardinality:4,prevalidator:[{validator:function(e,t,a,i,n){var r=n.isInYearRange(e,n.yearrange.minyear,n.yearrange.maxyear);if(!i&&!r){var s=n.determinebaseyear(n.yearrange.minyear,n.yearrange.maxyear,e+"0").toString().slice(0,1);if(r=n.isInYearRange(s+e,n.yearrange.minyear,n.yearrange.maxyear))return t[a++]=s[0],{pos:a};if(s=n.determinebaseyear(n.yearrange.minyear,n.yearrange.maxyear,e+"0").toString().slice(0,2),r=n.isInYearRange(s+e,n.yearrange.minyear,n.yearrange.maxyear))return t[a++]=s[0],t[a++]=s[1],{pos:a}}return r},cardinality:1},{validator:function(e,t,a,i,n){var r=n.isInYearRange(e,n.yearrange.minyear,n.yearrange.maxyear);if(!i&&!r){var s=n.determinebaseyear(n.yearrange.minyear,n.yearrange.maxyear,e).toString().slice(0,2);if(r=n.isInYearRange(e[0]+s[1]+e[1],n.yearrange.minyear,n.yearrange.maxyear))return t[a++]=s[1],{pos:a};if(s=n.determinebaseyear(n.yearrange.minyear,n.yearrange.maxyear,e).toString().slice(0,2),n.isInYearRange(s+e,n.yearrange.minyear,n.yearrange.maxyear)){var o=t.join("").substr(0,6);if(o!=n.leapday)r=!0;else{var l=parseInt(e,10);r=l%4===0?l%100===0?l%400===0:!0:!1}}else r=!1;if(r)return t[a-1]=s[0],t[a++]=s[1],t[a++]=e[0],{pos:a}}return r},cardinality:2},{validator:function(e,t,a,i,n){return n.isInYearRange(e,n.yearrange.minyear,n.yearrange.maxyear)},cardinality:3}]}},insertMode:!1,autoUnmask:!1},"mm/dd/yyyy":{placeholder:"mm/dd/yyyy",alias:"dd/mm/yyyy",regex:{val2pre:function(t){var a=e.inputmask.escapeRegex.call(this,t);return new RegExp("((0[13-9]|1[012])"+a+"[0-3])|(02"+a+"[0-2])")},val2:function(t){var a=e.inputmask.escapeRegex.call(this,t);return new RegExp("((0[1-9]|1[012])"+a+"(0[1-9]|[12][0-9]))|((0[13-9]|1[012])"+a+"30)|((0[13578]|1[02])"+a+"31)")},val1pre:new RegExp("[01]"),val1:new RegExp("0[1-9]|1[012]")},leapday:"02/29/",onKeyUp:function(t,a,i){var n=e(this);if(t.ctrlKey&&t.keyCode==i.keyCode.RIGHT){var r=new Date;n.val((r.getMonth()+1).toString()+r.getDate().toString()+r.getFullYear().toString())}}},"yyyy/mm/dd":{mask:"y/1/2",placeholder:"yyyy/mm/dd",alias:"mm/dd/yyyy",leapday:"/02/29",onKeyUp:function(t,a,i){var n=e(this);if(t.ctrlKey&&t.keyCode==i.keyCode.RIGHT){var r=new Date;n.val(r.getFullYear().toString()+(r.getMonth()+1).toString()+r.getDate().toString())}},definitions:{2:{validator:function(e,t,a,i,n){var r=t.join("").substr(5,3);-1!=r.indexOf(n.placeholder[5])&&(r="01"+n.separator);var s=n.regex.val2(n.separator).test(r+e);if(!i&&!s&&(e.charAt(1)==n.separator||-1!="-./".indexOf(e.charAt(1)))&&(s=n.regex.val2(n.separator).test(r+"0"+e.charAt(0))))return t[a-1]="0",{pos:a,c:e.charAt(0)};if(s){var o=t.join("").substr(4,4)+e;if(o!=n.leapday)return!0;var l=parseInt(t.join("").substr(0,4),10);return l%4===0?l%100===0?l%400===0:!0:!1}return s},cardinality:2,prevalidator:[{validator:function(e,t,a,i,n){var r=t.join("").substr(5,3);-1!=r.indexOf(n.placeholder[5])&&(r="01"+n.separator);var s=n.regex.val2pre(n.separator).test(r+e);return i||s||!(s=n.regex.val2(n.separator).test(r+"0"+e))?s:(t[a]="0",a++,{pos:a})},cardinality:1}]}}},"dd.mm.yyyy":{mask:"1.2.y",placeholder:"dd.mm.yyyy",leapday:"29.02.",separator:".",alias:"dd/mm/yyyy"},"dd-mm-yyyy":{mask:"1-2-y",placeholder:"dd-mm-yyyy",leapday:"29-02-",separator:"-",alias:"dd/mm/yyyy"},"mm.dd.yyyy":{mask:"1.2.y",placeholder:"mm.dd.yyyy",leapday:"02.29.",separator:".",alias:"mm/dd/yyyy"},"mm-dd-yyyy":{mask:"1-2-y",placeholder:"mm-dd-yyyy",leapday:"02-29-",separator:"-",alias:"mm/dd/yyyy"},"yyyy.mm.dd":{mask:"y.1.2",placeholder:"yyyy.mm.dd",leapday:".02.29",separator:".",alias:"yyyy/mm/dd"},"yyyy-mm-dd":{mask:"y-1-2",placeholder:"yyyy-mm-dd",leapday:"-02-29",separator:"-",alias:"yyyy/mm/dd"},datetime:{mask:"1/2/y h:s",placeholder:"dd/mm/yyyy hh:mm",alias:"dd/mm/yyyy",regex:{hrspre:new RegExp("[012]"),hrs24:new RegExp("2[0-9]|1[3-9]"),hrs:new RegExp("[01][0-9]|2[0-3]"),ampm:new RegExp("^[a|p|A|P][m|M]")},timeseparator:":",hourFormat:"24",definitions:{h:{validator:function(e,t,a,i,n){var r=n.regex.hrs.test(e);if(!i&&!r&&(e.charAt(1)==n.timeseparator||-1!="-.:".indexOf(e.charAt(1)))&&(r=n.regex.hrs.test("0"+e.charAt(0))))return t[a-1]="0",t[a]=e.charAt(0),a++,{pos:a};if(r&&"24"!==n.hourFormat&&n.regex.hrs24.test(e)){
var s=parseInt(e,10);return 24==s?(t[a+5]="a",t[a+6]="m"):(t[a+5]="p",t[a+6]="m"),s-=12,10>s?(t[a]=s.toString(),t[a-1]="0"):(t[a]=s.toString().charAt(1),t[a-1]=s.toString().charAt(0)),{pos:a,c:t[a]}}return r},cardinality:2,prevalidator:[{validator:function(e,t,a,i,n){var r=n.regex.hrspre.test(e);return i||r||!(r=n.regex.hrs.test("0"+e))?r:(t[a]="0",a++,{pos:a})},cardinality:1}]},t:{validator:function(e,t,a,i,n){return n.regex.ampm.test(e+"m")},casing:"lower",cardinality:1}},insertMode:!1,autoUnmask:!1},datetime12:{mask:"1/2/y h:s t\\m",placeholder:"dd/mm/yyyy hh:mm xm",alias:"datetime",hourFormat:"12"},"hh:mm t":{mask:"h:s t\\m",placeholder:"hh:mm xm",alias:"datetime",hourFormat:"12"},"h:s t":{mask:"h:s t\\m",placeholder:"hh:mm xm",alias:"datetime",hourFormat:"12"},"hh:mm:ss":{mask:"h:s:s",autoUnmask:!1},"hh:mm":{mask:"h:s",autoUnmask:!1},date:{alias:"dd/mm/yyyy"},"mm/yyyy":{mask:"1/y",placeholder:"mm/yyyy",leapday:"donotuse",separator:"/",alias:"mm/dd/yyyy"}})}(jQuery),function(e){e.extend(e.inputmask.defaults.definitions,{A:{validator:"[A-Za-z]",cardinality:1,casing:"upper"},"#":{validator:"[A-Za-zА-яЁё0-9]",cardinality:1,casing:"upper"}}),e.extend(e.inputmask.defaults.aliases,{url:{mask:"ir",placeholder:"",separator:"",defaultPrefix:"http://",regex:{urlpre1:new RegExp("[fh]"),urlpre2:new RegExp("(ft|ht)"),urlpre3:new RegExp("(ftp|htt)"),urlpre4:new RegExp("(ftp:|http|ftps)"),urlpre5:new RegExp("(ftp:/|ftps:|http:|https)"),urlpre6:new RegExp("(ftp://|ftps:/|http:/|https:)"),urlpre7:new RegExp("(ftp://|ftps://|http://|https:/)"),urlpre8:new RegExp("(ftp://|ftps://|http://|https://)")},definitions:{i:{validator:function(e,t,a,i,n){return!0},cardinality:8,prevalidator:function(){for(var e=[],t=8,a=0;t>a;a++)e[a]=function(){var e=a;return{validator:function(t,a,i,n,r){if(r.regex["urlpre"+(e+1)]){var s,o=t;e+1-t.length>0&&(o=a.join("").substring(0,e+1-t.length)+""+o);var l=r.regex["urlpre"+(e+1)].test(o);if(!n&&!l){for(i-=e,s=0;s<r.defaultPrefix.length;s++)a[i]=r.defaultPrefix[s],i++;for(s=0;s<o.length-1;s++)a[i]=o[s],i++;return{pos:i}}return l}return!1},cardinality:e}}();return e}()},r:{validator:".",cardinality:50}},insertMode:!1,autoUnmask:!1},ip:{mask:["[[x]y]z.[[x]y]z.[[x]y]z.x[yz]","[[x]y]z.[[x]y]z.[[x]y]z.[[x]y][z]"],definitions:{x:{validator:"[012]",cardinality:1,definitionSymbol:"i"},y:{validator:function(e,t,a,i,n){return e=a-1>-1&&"."!=t[a-1]?t[a-1]+e:"0"+e,new RegExp("2[0-5]|[01][0-9]").test(e)},cardinality:1,definitionSymbol:"i"},z:{validator:function(e,t,a,i,n){return a-1>-1&&"."!=t[a-1]?(e=t[a-1]+e,e=a-2>-1&&"."!=t[a-2]?t[a-2]+e:"0"+e):e="00"+e,new RegExp("25[0-5]|2[0-4][0-9]|[01][0-9][0-9]").test(e)},cardinality:1,definitionSymbol:"i"}}}})}(jQuery);