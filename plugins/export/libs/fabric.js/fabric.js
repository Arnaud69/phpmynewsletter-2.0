var fabric=fabric||{version:"1.5.0"};if(typeof exports!=='undefined'){exports.fabric=fabric;}
if(typeof document!=='undefined'&&typeof window!=='undefined'){fabric.document=document;fabric.window=window;window.fabric=fabric;}
else{fabric.document=require("jsdom").jsdom("<!DOCTYPE html><html><head></head><body></body></html>");if(fabric.document.createWindow){fabric.window=fabric.document.createWindow();}else{fabric.window=fabric.document.parentWindow;}}
fabric.isTouchSupported="ontouchstart"in fabric.document.documentElement;fabric.isLikelyNode=typeof Buffer!=='undefined'&&typeof window==='undefined';fabric.SHARED_ATTRIBUTES=["display","transform","fill","fill-opacity","fill-rule","opacity","stroke","stroke-dasharray","stroke-linecap","stroke-linejoin","stroke-miterlimit","stroke-opacity","stroke-width"];fabric.DPI=96;fabric.reNum='(?:[-+]?(?:\\d+|\\d*\\.\\d+)(?:e[-+]?\\d+)?)';(function(){function _removeEventListener(eventName,handler){if(!this.__eventListeners[eventName]){return;}
if(handler){fabric.util.removeFromArray(this.__eventListeners[eventName],handler);}
else{this.__eventListeners[eventName].length=0;}}
function observe(eventName,handler){if(!this.__eventListeners){this.__eventListeners={};}
if(arguments.length===1){for(var prop in eventName){this.on(prop,eventName[prop]);}}
else{if(!this.__eventListeners[eventName]){this.__eventListeners[eventName]=[];}
this.__eventListeners[eventName].push(handler);}
return this;}
function stopObserving(eventName,handler){if(!this.__eventListeners){return;}
if(arguments.length===0){this.__eventListeners={};}
else if(arguments.length===1&&typeof arguments[0]==='object'){for(var prop in eventName){_removeEventListener.call(this,prop,eventName[prop]);}}
else{_removeEventListener.call(this,eventName,handler);}
return this;}
function fire(eventName,options){if(!this.__eventListeners){return;}
var listenersForEvent=this.__eventListeners[eventName];if(!listenersForEvent){return;}
for(var i=0,len=listenersForEvent.length;i<len;i++){listenersForEvent[i].call(this,options||{});}
return this;}
fabric.Observable={observe:observe,stopObserving:stopObserving,fire:fire,on:observe,off:stopObserving,trigger:fire};})();fabric.Collection={add:function(){this._objects.push.apply(this._objects,arguments);for(var i=0,length=arguments.length;i<length;i++){this._onObjectAdded(arguments[i]);}
this.renderOnAddRemove&&this.renderAll();return this;},insertAt:function(object,index,nonSplicing){var objects=this.getObjects();if(nonSplicing){objects[index]=object;}
else{objects.splice(index,0,object);}
this._onObjectAdded(object);this.renderOnAddRemove&&this.renderAll();return this;},remove:function(){var objects=this.getObjects(),index;for(var i=0,length=arguments.length;i<length;i++){index=objects.indexOf(arguments[i]);if(index!==-1){objects.splice(index,1);this._onObjectRemoved(arguments[i]);}}
this.renderOnAddRemove&&this.renderAll();return this;},forEachObject:function(callback,context){var objects=this.getObjects(),i=objects.length;while(i--){callback.call(context,objects[i],i,objects);}
return this;},getObjects:function(type){if(typeof type==='undefined'){return this._objects;}
return this._objects.filter(function(o){return o.type===type;});},item:function(index){return this.getObjects()[index];},isEmpty:function(){return this.getObjects().length===0;},size:function(){return this.getObjects().length;},contains:function(object){return this.getObjects().indexOf(object)>-1;},complexity:function(){return this.getObjects().reduce(function(memo,current){memo+=current.complexity?current.complexity():0;return memo;},0);}};(function(global){var sqrt=Math.sqrt,atan2=Math.atan2,PiBy180=Math.PI/180;fabric.util={removeFromArray:function(array,value){var idx=array.indexOf(value);if(idx!==-1){array.splice(idx,1);}
return array;},getRandomInt:function(min,max){return Math.floor(Math.random()*(max- min+ 1))+ min;},degreesToRadians:function(degrees){return degrees*PiBy180;},radiansToDegrees:function(radians){return radians/PiBy180;},rotatePoint:function(point,origin,radians){var sin=Math.sin(radians),cos=Math.cos(radians);point.subtractEquals(origin);var rx=point.x*cos- point.y*sin,ry=point.x*sin+ point.y*cos;return new fabric.Point(rx,ry).addEquals(origin);},transformPoint:function(p,t,ignoreOffset){if(ignoreOffset){return new fabric.Point(t[0]*p.x+ t[2]*p.y,t[1]*p.x+ t[3]*p.y);}
return new fabric.Point(t[0]*p.x+ t[2]*p.y+ t[4],t[1]*p.x+ t[3]*p.y+ t[5]);},invertTransform:function(t){var r=t.slice(),a=1/(t[0]*t[3]- t[1]*t[2]);r=[a*t[3],-a*t[1],-a*t[2],a*t[0],0,0];var o=fabric.util.transformPoint({x:t[4],y:t[5]},r);r[4]=-o.x;r[5]=-o.y;return r;},toFixed:function(number,fractionDigits){return parseFloat(Number(number).toFixed(fractionDigits));},parseUnit:function(value,fontSize){var unit=/\D{0,2}$/.exec(value),number=parseFloat(value);if(!fontSize){fontSize=fabric.Text.DEFAULT_SVG_FONT_SIZE;}
switch(unit[0]){case'mm':return number*fabric.DPI/25.4;case'cm':return number*fabric.DPI/2.54;case'in':return number*fabric.DPI;case'pt':return number*fabric.DPI/72;case'pc':return number*fabric.DPI/72*12;case'em':return number*fontSize;default:return number;}},falseFunction:function(){return false;},getKlass:function(type,namespace){type=fabric.util.string.camelize(type.charAt(0).toUpperCase()+ type.slice(1));return fabric.util.resolveNamespace(namespace)[type];},resolveNamespace:function(namespace){if(!namespace){return fabric;}
var parts=namespace.split('.'),len=parts.length,obj=global||fabric.window;for(var i=0;i<len;++i){obj=obj[parts[i]];}
return obj;},loadImage:function(url,callback,context,crossOrigin){if(!url){callback&&callback.call(context,url);return;}
var img=fabric.util.createImage();img.onload=function(){callback&&callback.call(context,img);img=img.onload=img.onerror=null;};img.onerror=function(){fabric.log('Error loading '+ img.src);callback&&callback.call(context,null,true);img=img.onload=img.onerror=null;};if(url.indexOf('data')!==0&&typeof crossOrigin!=='undefined'){img.crossOrigin=crossOrigin;}
img.src=url;},enlivenObjects:function(objects,callback,namespace,reviver){objects=objects||[];function onLoaded(){if(++numLoadedObjects===numTotalObjects){callback&&callback(enlivenedObjects);}}
var enlivenedObjects=[],numLoadedObjects=0,numTotalObjects=objects.length;if(!numTotalObjects){callback&&callback(enlivenedObjects);return;}
objects.forEach(function(o,index){if(!o||!o.type){onLoaded();return;}
var klass=fabric.util.getKlass(o.type,namespace);if(klass.async){klass.fromObject(o,function(obj,error){if(!error){enlivenedObjects[index]=obj;reviver&&reviver(o,enlivenedObjects[index]);}
onLoaded();});}
else{enlivenedObjects[index]=klass.fromObject(o);reviver&&reviver(o,enlivenedObjects[index]);onLoaded();}});},groupSVGElements:function(elements,options,path){var object;object=new fabric.PathGroup(elements,options);if(typeof path!=='undefined'){object.setSourcePath(path);}
return object;},populateWithProperties:function(source,destination,properties){if(properties&&Object.prototype.toString.call(properties)==='[object Array]'){for(var i=0,len=properties.length;i<len;i++){if(properties[i]in source){destination[properties[i]]=source[properties[i]];}}}},drawDashedLine:function(ctx,x,y,x2,y2,da){var dx=x2- x,dy=y2- y,len=sqrt(dx*dx+ dy*dy),rot=atan2(dy,dx),dc=da.length,di=0,draw=true;ctx.save();ctx.translate(x,y);ctx.moveTo(0,0);ctx.rotate(rot);x=0;while(len>x){x+=da[di++%dc];if(x>len){x=len;}
ctx[draw?'lineTo':'moveTo'](x,0);draw=!draw;}
ctx.restore();},createCanvasElement:function(canvasEl){canvasEl||(canvasEl=fabric.document.createElement('canvas'));if(!canvasEl.getContext&&typeof G_vmlCanvasManager!=='undefined'){G_vmlCanvasManager.initElement(canvasEl);}
return canvasEl;},createImage:function(){return fabric.isLikelyNode?new(require('canvas').Image)():fabric.document.createElement('img');},createAccessors:function(klass){var proto=klass.prototype;for(var i=proto.stateProperties.length;i--;){var propName=proto.stateProperties[i],capitalizedPropName=propName.charAt(0).toUpperCase()+ propName.slice(1),setterName='set'+ capitalizedPropName,getterName='get'+ capitalizedPropName;if(!proto[getterName]){proto[getterName]=(function(property){return new Function('return this.get("'+ property+'")');})(propName);}
if(!proto[setterName]){proto[setterName]=(function(property){return new Function('value','return this.set("'+ property+'", value)');})(propName);}}},clipContext:function(receiver,ctx){ctx.save();ctx.beginPath();receiver.clipTo(ctx);ctx.clip();},multiplyTransformMatrices:function(a,b){return[a[0]*b[0]+ a[2]*b[1],a[1]*b[0]+ a[3]*b[1],a[0]*b[2]+ a[2]*b[3],a[1]*b[2]+ a[3]*b[3],a[0]*b[4]+ a[2]*b[5]+ a[4],a[1]*b[4]+ a[3]*b[5]+ a[5]];},getFunctionBody:function(fn){return(String(fn).match(/function[^{]*\{([\s\S]*)\}/)||{})[1];},isTransparent:function(ctx,x,y,tolerance){if(tolerance>0){if(x>tolerance){x-=tolerance;}
else{x=0;}
if(y>tolerance){y-=tolerance;}
else{y=0;}}
var _isTransparent=true,imageData=ctx.getImageData(x,y,(tolerance*2)||1,(tolerance*2)||1);for(var i=3,l=imageData.data.length;i<l;i+=4){var temp=imageData.data[i];_isTransparent=temp<=0;if(_isTransparent===false){break;}}
imageData=null;return _isTransparent;}};})(typeof exports!=='undefined'?exports:this);(function(){var arcToSegmentsCache={},segmentToBezierCache={},boundsOfCurveCache={},_join=Array.prototype.join;function arcToSegments(toX,toY,rx,ry,large,sweep,rotateX){var argsString=_join.call(arguments);if(arcToSegmentsCache[argsString]){return arcToSegmentsCache[argsString];}
var PI=Math.PI,th=rotateX*PI/180,sinTh=Math.sin(th),cosTh=Math.cos(th),fromX=0,fromY=0;rx=Math.abs(rx);ry=Math.abs(ry);var px=-cosTh*toX*0.5- sinTh*toY*0.5,py=-cosTh*toY*0.5+ sinTh*toX*0.5,rx2=rx*rx,ry2=ry*ry,py2=py*py,px2=px*px,pl=rx2*ry2- rx2*py2- ry2*px2,root=0;if(pl<0){var s=Math.sqrt(1- pl/(rx2*ry2));rx*=s;ry*=s;}
else{root=(large===sweep?-1.0:1.0)*Math.sqrt(pl/(rx2*py2+ ry2*px2));}
var cx=root*rx*py/ry,cy=-root*ry*px/rx,cx1=cosTh*cx- sinTh*cy+ toX*0.5,cy1=sinTh*cx+ cosTh*cy+ toY*0.5,mTheta=calcVectorAngle(1,0,(px- cx)/ rx, (py - cy) / ry),
dtheta=calcVectorAngle((px- cx)/ rx, (py - cy) / ry, (-px - cx) / rx, (-py - cy) / ry);
if(sweep===0&&dtheta>0){dtheta-=2*PI;}
else if(sweep===1&&dtheta<0){dtheta+=2*PI;}
var segments=Math.ceil(Math.abs(dtheta/PI*2)),result=[],mDelta=dtheta/segments,mT=8/3*Math.sin(mDelta/4)*Math.sin(mDelta/4)/ Math.sin(mDelta / 2),
th3=mTheta+ mDelta;for(var i=0;i<segments;i++){result[i]=segmentToBezier(mTheta,th3,cosTh,sinTh,rx,ry,cx1,cy1,mT,fromX,fromY);fromX=result[i][4];fromY=result[i][5];mTheta=th3;th3+=mDelta;}
arcToSegmentsCache[argsString]=result;return result;}
function segmentToBezier(th2,th3,cosTh,sinTh,rx,ry,cx1,cy1,mT,fromX,fromY){var argsString2=_join.call(arguments);if(segmentToBezierCache[argsString2]){return segmentToBezierCache[argsString2];}
var costh2=Math.cos(th2),sinth2=Math.sin(th2),costh3=Math.cos(th3),sinth3=Math.sin(th3),toX=cosTh*rx*costh3- sinTh*ry*sinth3+ cx1,toY=sinTh*rx*costh3+ cosTh*ry*sinth3+ cy1,cp1X=fromX+ mT*(- cosTh*rx*sinth2- sinTh*ry*costh2),cp1Y=fromY+ mT*(- sinTh*rx*sinth2+ cosTh*ry*costh2),cp2X=toX+ mT*(cosTh*rx*sinth3+ sinTh*ry*costh3),cp2Y=toY+ mT*(sinTh*rx*sinth3- cosTh*ry*costh3);segmentToBezierCache[argsString2]=[cp1X,cp1Y,cp2X,cp2Y,toX,toY];return segmentToBezierCache[argsString2];}
function calcVectorAngle(ux,uy,vx,vy){var ta=Math.atan2(uy,ux),tb=Math.atan2(vy,vx);if(tb>=ta){return tb- ta;}
else{return 2*Math.PI-(ta- tb);}}
fabric.util.drawArc=function(ctx,fx,fy,coords){var rx=coords[0],ry=coords[1],rot=coords[2],large=coords[3],sweep=coords[4],tx=coords[5],ty=coords[6],segs=[[],[],[],[]],segsNorm=arcToSegments(tx- fx,ty- fy,rx,ry,large,sweep,rot);for(var i=0,len=segsNorm.length;i<len;i++){segs[i][0]=segsNorm[i][0]+ fx;segs[i][1]=segsNorm[i][1]+ fy;segs[i][2]=segsNorm[i][2]+ fx;segs[i][3]=segsNorm[i][3]+ fy;segs[i][4]=segsNorm[i][4]+ fx;segs[i][5]=segsNorm[i][5]+ fy;ctx.bezierCurveTo.apply(ctx,segs[i]);}};fabric.util.getBoundsOfArc=function(fx,fy,rx,ry,rot,large,sweep,tx,ty){var fromX=0,fromY=0,bound=[],bounds=[],segs=arcToSegments(tx- fx,ty- fy,rx,ry,large,sweep,rot),boundCopy=[[],[]];for(var i=0,len=segs.length;i<len;i++){bound=getBoundsOfCurve(fromX,fromY,segs[i][0],segs[i][1],segs[i][2],segs[i][3],segs[i][4],segs[i][5]);boundCopy[0].x=bound[0].x+ fx;boundCopy[0].y=bound[0].y+ fy;boundCopy[1].x=bound[1].x+ fx;boundCopy[1].y=bound[1].y+ fy;bounds.push(boundCopy[0]);bounds.push(boundCopy[1]);fromX=segs[i][4];fromY=segs[i][5];}
return bounds;};function getBoundsOfCurve(x0,y0,x1,y1,x2,y2,x3,y3){var argsString=_join.call(arguments);if(boundsOfCurveCache[argsString]){return boundsOfCurveCache[argsString];}
var sqrt=Math.sqrt,min=Math.min,max=Math.max,abs=Math.abs,tvalues=[],bounds=[[],[]],a,b,c,t,t1,t2,b2ac,sqrtb2ac;b=6*x0- 12*x1+ 6*x2;a=-3*x0+ 9*x1- 9*x2+ 3*x3;c=3*x1- 3*x0;for(var i=0;i<2;++i){if(i>0){b=6*y0- 12*y1+ 6*y2;a=-3*y0+ 9*y1- 9*y2+ 3*y3;c=3*y1- 3*y0;}
if(abs(a)<1e-12){if(abs(b)<1e-12){continue;}
t=-c/b;if(0<t&&t<1){tvalues.push(t);}
continue;}
b2ac=b*b- 4*c*a;if(b2ac<0){continue;}
sqrtb2ac=sqrt(b2ac);t1=(-b+ sqrtb2ac)/ (2 * a);
if(0<t1&&t1<1){tvalues.push(t1);}
t2=(-b- sqrtb2ac)/ (2 * a);
if(0<t2&&t2<1){tvalues.push(t2);}}
var x,y,j=tvalues.length,jlen=j,mt;while(j--){t=tvalues[j];mt=1- t;x=(mt*mt*mt*x0)+(3*mt*mt*t*x1)+(3*mt*t*t*x2)+(t*t*t*x3);bounds[0][j]=x;y=(mt*mt*mt*y0)+(3*mt*mt*t*y1)+(3*mt*t*t*y2)+(t*t*t*y3);bounds[1][j]=y;}
bounds[0][jlen]=x0;bounds[1][jlen]=y0;bounds[0][jlen+ 1]=x3;bounds[1][jlen+ 1]=y3;var result=[{x:min.apply(null,bounds[0]),y:min.apply(null,bounds[1])},{x:max.apply(null,bounds[0]),y:max.apply(null,bounds[1])}];boundsOfCurveCache[argsString]=result;return result;}
fabric.util.getBoundsOfCurve=getBoundsOfCurve;})();(function(){var slice=Array.prototype.slice;if(!Array.prototype.indexOf){Array.prototype.indexOf=function(searchElement){if(this===void 0||this===null){throw new TypeError();}
var t=Object(this),len=t.length>>>0;if(len===0){return-1;}
var n=0;if(arguments.length>0){n=Number(arguments[1]);if(n!==n){n=0;}
else if(n!==0&&n!==Number.POSITIVE_INFINITY&&n!==Number.NEGATIVE_INFINITY){n=(n>0||-1)*Math.floor(Math.abs(n));}}
if(n>=len){return-1;}
var k=n>=0?n:Math.max(len- Math.abs(n),0);for(;k<len;k++){if(k in t&&t[k]===searchElement){return k;}}
return-1;};}
if(!Array.prototype.forEach){Array.prototype.forEach=function(fn,context){for(var i=0,len=this.length>>>0;i<len;i++){if(i in this){fn.call(context,this[i],i,this);}}};}
if(!Array.prototype.map){Array.prototype.map=function(fn,context){var result=[];for(var i=0,len=this.length>>>0;i<len;i++){if(i in this){result[i]=fn.call(context,this[i],i,this);}}
return result;};}
if(!Array.prototype.every){Array.prototype.every=function(fn,context){for(var i=0,len=this.length>>>0;i<len;i++){if(i in this&&!fn.call(context,this[i],i,this)){return false;}}
return true;};}
if(!Array.prototype.some){Array.prototype.some=function(fn,context){for(var i=0,len=this.length>>>0;i<len;i++){if(i in this&&fn.call(context,this[i],i,this)){return true;}}
return false;};}
if(!Array.prototype.filter){Array.prototype.filter=function(fn,context){var result=[],val;for(var i=0,len=this.length>>>0;i<len;i++){if(i in this){val=this[i];if(fn.call(context,val,i,this)){result.push(val);}}}
return result;};}
if(!Array.prototype.reduce){Array.prototype.reduce=function(fn){var len=this.length>>>0,i=0,rv;if(arguments.length>1){rv=arguments[1];}
else{do{if(i in this){rv=this[i++];break;}
if(++i>=len){throw new TypeError();}}
while(true);}
for(;i<len;i++){if(i in this){rv=fn.call(null,rv,this[i],i,this);}}
return rv;};}
function invoke(array,method){var args=slice.call(arguments,2),result=[];for(var i=0,len=array.length;i<len;i++){result[i]=args.length?array[i][method].apply(array[i],args):array[i][method].call(array[i]);}
return result;}
function max(array,byProperty){return find(array,byProperty,function(value1,value2){return value1>=value2;});}
function min(array,byProperty){return find(array,byProperty,function(value1,value2){return value1<value2;});}
function find(array,byProperty,condition){if(!array||array.length===0){return;}
var i=array.length- 1,result=byProperty?array[i][byProperty]:array[i];if(byProperty){while(i--){if(condition(array[i][byProperty],result)){result=array[i][byProperty];}}}
else{while(i--){if(condition(array[i],result)){result=array[i];}}}
return result;}
fabric.util.array={invoke:invoke,min:min,max:max};})();(function(){function extend(destination,source){for(var property in source){destination[property]=source[property];}
return destination;}
function clone(object){return extend({},object);}
fabric.util.object={extend:extend,clone:clone};})();(function(){if(!String.prototype.trim){String.prototype.trim=function(){return this.replace(/^[\s\xA0]+/,'').replace(/[\s\xA0]+$/,'');};}
function camelize(string){return string.replace(/-+(.)?/g,function(match,character){return character?character.toUpperCase():'';});}
function capitalize(string,firstLetterOnly){return string.charAt(0).toUpperCase()+
(firstLetterOnly?string.slice(1):string.slice(1).toLowerCase());}
function escapeXml(string){return string.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&apos;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
fabric.util.string={camelize:camelize,capitalize:capitalize,escapeXml:escapeXml};}());(function(){var slice=Array.prototype.slice,apply=Function.prototype.apply,Dummy=function(){};if(!Function.prototype.bind){Function.prototype.bind=function(thisArg){var _this=this,args=slice.call(arguments,1),bound;if(args.length){bound=function(){return apply.call(_this,this instanceof Dummy?this:thisArg,args.concat(slice.call(arguments)));};}
else{bound=function(){return apply.call(_this,this instanceof Dummy?this:thisArg,arguments);};}
Dummy.prototype=this.prototype;bound.prototype=new Dummy();return bound;};}})();(function(){var slice=Array.prototype.slice,emptyFunction=function(){},IS_DONTENUM_BUGGY=(function(){for(var p in{toString:1}){if(p==='toString'){return false;}}
return true;})(),addMethods=function(klass,source,parent){for(var property in source){if(property in klass.prototype&&typeof klass.prototype[property]==='function'&&(source[property]+'').indexOf('callSuper')>-1){klass.prototype[property]=(function(property){return function(){var superclass=this.constructor.superclass;this.constructor.superclass=parent;var returnValue=source[property].apply(this,arguments);this.constructor.superclass=superclass;if(property!=='initialize'){return returnValue;}};})(property);}
else{klass.prototype[property]=source[property];}
if(IS_DONTENUM_BUGGY){if(source.toString!==Object.prototype.toString){klass.prototype.toString=source.toString;}
if(source.valueOf!==Object.prototype.valueOf){klass.prototype.valueOf=source.valueOf;}}}};function Subclass(){}
function callSuper(methodName){var fn=this.constructor.superclass.prototype[methodName];return(arguments.length>1)?fn.apply(this,slice.call(arguments,1)):fn.call(this);}
function createClass(){var parent=null,properties=slice.call(arguments,0);if(typeof properties[0]==='function'){parent=properties.shift();}
function klass(){this.initialize.apply(this,arguments);}
klass.superclass=parent;klass.subclasses=[];if(parent){Subclass.prototype=parent.prototype;klass.prototype=new Subclass();parent.subclasses.push(klass);}
for(var i=0,length=properties.length;i<length;i++){addMethods(klass,properties[i],parent);}
if(!klass.prototype.initialize){klass.prototype.initialize=emptyFunction;}
klass.prototype.constructor=klass;klass.prototype.callSuper=callSuper;return klass;}
fabric.util.createClass=createClass;})();(function(){var unknown='unknown';function areHostMethods(object){var methodNames=Array.prototype.slice.call(arguments,1),t,i,len=methodNames.length;for(i=0;i<len;i++){t=typeof object[methodNames[i]];if(!(/^(?:function|object|unknown)$/).test(t)){return false;}}
return true;}
var getElement,setElement,getUniqueId=(function(){var uid=0;return function(element){return element.__uniqueID||(element.__uniqueID='uniqueID__'+ uid++);};})();(function(){var elements={};getElement=function(uid){return elements[uid];};setElement=function(uid,element){elements[uid]=element;};})();function createListener(uid,handler){return{handler:handler,wrappedHandler:createWrappedHandler(uid,handler)};}
function createWrappedHandler(uid,handler){return function(e){handler.call(getElement(uid),e||fabric.window.event);};}
function createDispatcher(uid,eventName){return function(e){if(handlers[uid]&&handlers[uid][eventName]){var handlersForEvent=handlers[uid][eventName];for(var i=0,len=handlersForEvent.length;i<len;i++){handlersForEvent[i].call(this,e||fabric.window.event);}}};}
var shouldUseAddListenerRemoveListener=(areHostMethods(fabric.document.documentElement,'addEventListener','removeEventListener')&&areHostMethods(fabric.window,'addEventListener','removeEventListener')),shouldUseAttachEventDetachEvent=(areHostMethods(fabric.document.documentElement,'attachEvent','detachEvent')&&areHostMethods(fabric.window,'attachEvent','detachEvent')),listeners={},handlers={},addListener,removeListener;if(shouldUseAddListenerRemoveListener){addListener=function(element,eventName,handler){element.addEventListener(eventName,handler,false);};removeListener=function(element,eventName,handler){element.removeEventListener(eventName,handler,false);};}
else if(shouldUseAttachEventDetachEvent){addListener=function(element,eventName,handler){var uid=getUniqueId(element);setElement(uid,element);if(!listeners[uid]){listeners[uid]={};}
if(!listeners[uid][eventName]){listeners[uid][eventName]=[];}
var listener=createListener(uid,handler);listeners[uid][eventName].push(listener);element.attachEvent('on'+ eventName,listener.wrappedHandler);};removeListener=function(element,eventName,handler){var uid=getUniqueId(element),listener;if(listeners[uid]&&listeners[uid][eventName]){for(var i=0,len=listeners[uid][eventName].length;i<len;i++){listener=listeners[uid][eventName][i];if(listener&&listener.handler===handler){element.detachEvent('on'+ eventName,listener.wrappedHandler);listeners[uid][eventName][i]=null;}}}};}
else{addListener=function(element,eventName,handler){var uid=getUniqueId(element);if(!handlers[uid]){handlers[uid]={};}
if(!handlers[uid][eventName]){handlers[uid][eventName]=[];var existingHandler=element['on'+ eventName];if(existingHandler){handlers[uid][eventName].push(existingHandler);}
element['on'+ eventName]=createDispatcher(uid,eventName);}
handlers[uid][eventName].push(handler);};removeListener=function(element,eventName,handler){var uid=getUniqueId(element);if(handlers[uid]&&handlers[uid][eventName]){var handlersForEvent=handlers[uid][eventName];for(var i=0,len=handlersForEvent.length;i<len;i++){if(handlersForEvent[i]===handler){handlersForEvent.splice(i,1);}}}};}
fabric.util.addListener=addListener;fabric.util.removeListener=removeListener;function getPointer(event,upperCanvasEl){event||(event=fabric.window.event);var element=event.target||(typeof event.srcElement!==unknown?event.srcElement:null),scroll=fabric.util.getScrollLeftTop(element,upperCanvasEl);return{x:pointerX(event)+ scroll.left,y:pointerY(event)+ scroll.top};}
var pointerX=function(event){return(typeof event.clientX!==unknown?event.clientX:0);},pointerY=function(event){return(typeof event.clientY!==unknown?event.clientY:0);};function _getPointer(event,pageProp,clientProp){var touchProp=event.type==='touchend'?'changedTouches':'touches';return(event[touchProp]&&event[touchProp][0]?(event[touchProp][0][pageProp]-(event[touchProp][0][pageProp]- event[touchProp][0][clientProp]))||event[clientProp]:event[clientProp]);}
if(fabric.isTouchSupported){pointerX=function(event){return _getPointer(event,'pageX','clientX');};pointerY=function(event){return _getPointer(event,'pageY','clientY');};}
fabric.util.getPointer=getPointer;fabric.util.object.extend(fabric.util,fabric.Observable);})();(function(){function setStyle(element,styles){var elementStyle=element.style;if(!elementStyle){return element;}
if(typeof styles==='string'){element.style.cssText+=';'+ styles;return styles.indexOf('opacity')>-1?setOpacity(element,styles.match(/opacity:\s*(\d?\.?\d*)/)[1]):element;}
for(var property in styles){if(property==='opacity'){setOpacity(element,styles[property]);}
else{var normalizedProperty=(property==='float'||property==='cssFloat')?(typeof elementStyle.styleFloat==='undefined'?'cssFloat':'styleFloat'):property;elementStyle[normalizedProperty]=styles[property];}}
return element;}
var parseEl=fabric.document.createElement('div'),supportsOpacity=typeof parseEl.style.opacity==='string',supportsFilters=typeof parseEl.style.filter==='string',reOpacity=/alpha\s*\(\s*opacity\s*=\s*([^\)]+)\)/,setOpacity=function(element){return element;};if(supportsOpacity){setOpacity=function(element,value){element.style.opacity=value;return element;};}
else if(supportsFilters){setOpacity=function(element,value){var es=element.style;if(element.currentStyle&&!element.currentStyle.hasLayout){es.zoom=1;}
if(reOpacity.test(es.filter)){value=value>=0.9999?'':('alpha(opacity='+(value*100)+')');es.filter=es.filter.replace(reOpacity,value);}
else{es.filter+=' alpha(opacity='+(value*100)+')';}
return element;};}
fabric.util.setStyle=setStyle;})();(function(){var _slice=Array.prototype.slice;function getById(id){return typeof id==='string'?fabric.document.getElementById(id):id;}
var sliceCanConvertNodelists,toArray=function(arrayLike){return _slice.call(arrayLike,0);};try{sliceCanConvertNodelists=toArray(fabric.document.childNodes)instanceof Array;}
catch(err){}
if(!sliceCanConvertNodelists){toArray=function(arrayLike){var arr=new Array(arrayLike.length),i=arrayLike.length;while(i--){arr[i]=arrayLike[i];}
return arr;};}
function makeElement(tagName,attributes){var el=fabric.document.createElement(tagName);for(var prop in attributes){if(prop==='class'){el.className=attributes[prop];}
else if(prop==='for'){el.htmlFor=attributes[prop];}
else{el.setAttribute(prop,attributes[prop]);}}
return el;}
function addClass(element,className){if(element&&(' '+ element.className+' ').indexOf(' '+ className+' ')===-1){element.className+=(element.className?' ':'')+ className;}}
function wrapElement(element,wrapper,attributes){if(typeof wrapper==='string'){wrapper=makeElement(wrapper,attributes);}
if(element.parentNode){element.parentNode.replaceChild(wrapper,element);}
wrapper.appendChild(element);return wrapper;}
function getScrollLeftTop(element,upperCanvasEl){var firstFixedAncestor,origElement,left=0,top=0,docElement=fabric.document.documentElement,body=fabric.document.body||{scrollLeft:0,scrollTop:0};origElement=element;while(element&&element.parentNode&&!firstFixedAncestor){element=element.parentNode;if(element.nodeType===1&&fabric.util.getElementStyle(element,'position')==='fixed'){firstFixedAncestor=element;}
if(element.nodeType===1&&origElement!==upperCanvasEl&&fabric.util.getElementStyle(element,'position')==='absolute'){left=0;top=0;}
else if(element===fabric.document){left=body.scrollLeft||docElement.scrollLeft||0;top=body.scrollTop||docElement.scrollTop||0;}
else{left+=element.scrollLeft||0;top+=element.scrollTop||0;}}
return{left:left,top:top};}
function getElementOffset(element){var docElem,doc=element&&element.ownerDocument,box={left:0,top:0},offset={left:0,top:0},scrollLeftTop,offsetAttributes={borderLeftWidth:'left',borderTopWidth:'top',paddingLeft:'left',paddingTop:'top'};if(!doc){return{left:0,top:0};}
for(var attr in offsetAttributes){offset[offsetAttributes[attr]]+=parseInt(getElementStyle(element,attr),10)||0;}
docElem=doc.documentElement;if(typeof element.getBoundingClientRect!=='undefined'){box=element.getBoundingClientRect();}
scrollLeftTop=fabric.util.getScrollLeftTop(element,null);return{left:box.left+ scrollLeftTop.left-(docElem.clientLeft||0)+ offset.left,top:box.top+ scrollLeftTop.top-(docElem.clientTop||0)+ offset.top};}
var getElementStyle;if(fabric.document.defaultView&&fabric.document.defaultView.getComputedStyle){getElementStyle=function(element,attr){var style=fabric.document.defaultView.getComputedStyle(element,null);return style?style[attr]:undefined;};}
else{getElementStyle=function(element,attr){var value=element.style[attr];if(!value&&element.currentStyle){value=element.currentStyle[attr];}
return value;};}
(function(){var style=fabric.document.documentElement.style,selectProp='userSelect'in style?'userSelect':'MozUserSelect'in style?'MozUserSelect':'WebkitUserSelect'in style?'WebkitUserSelect':'KhtmlUserSelect'in style?'KhtmlUserSelect':'';function makeElementUnselectable(element){if(typeof element.onselectstart!=='undefined'){element.onselectstart=fabric.util.falseFunction;}
if(selectProp){element.style[selectProp]='none';}
else if(typeof element.unselectable==='string'){element.unselectable='on';}
return element;}
function makeElementSelectable(element){if(typeof element.onselectstart!=='undefined'){element.onselectstart=null;}
if(selectProp){element.style[selectProp]='';}
else if(typeof element.unselectable==='string'){element.unselectable='';}
return element;}
fabric.util.makeElementUnselectable=makeElementUnselectable;fabric.util.makeElementSelectable=makeElementSelectable;})();(function(){function getScript(url,callback){var headEl=fabric.document.getElementsByTagName('head')[0],scriptEl=fabric.document.createElement('script'),loading=true;scriptEl.onload=scriptEl.onreadystatechange=function(e){if(loading){if(typeof this.readyState==='string'&&this.readyState!=='loaded'&&this.readyState!=='complete'){return;}
loading=false;callback(e||fabric.window.event);scriptEl=scriptEl.onload=scriptEl.onreadystatechange=null;}};scriptEl.src=url;headEl.appendChild(scriptEl);}
fabric.util.getScript=getScript;})();fabric.util.getById=getById;fabric.util.toArray=toArray;fabric.util.makeElement=makeElement;fabric.util.addClass=addClass;fabric.util.wrapElement=wrapElement;fabric.util.getScrollLeftTop=getScrollLeftTop;fabric.util.getElementOffset=getElementOffset;fabric.util.getElementStyle=getElementStyle;})();(function(){function addParamToUrl(url,param){return url+(/\?/.test(url)?'&':'?')+ param;}
var makeXHR=(function(){var factories=[function(){return new ActiveXObject('Microsoft.XMLHTTP');},function(){return new ActiveXObject('Msxml2.XMLHTTP');},function(){return new ActiveXObject('Msxml2.XMLHTTP.3.0');},function(){return new XMLHttpRequest();}];for(var i=factories.length;i--;){try{var req=factories[i]();if(req){return factories[i];}}
catch(err){}}})();function emptyFn(){}
function request(url,options){options||(options={});var method=options.method?options.method.toUpperCase():'GET',onComplete=options.onComplete||function(){},xhr=makeXHR(),body;xhr.onreadystatechange=function(){if(xhr.readyState===4){onComplete(xhr);xhr.onreadystatechange=emptyFn;}};if(method==='GET'){body=null;if(typeof options.parameters==='string'){url=addParamToUrl(url,options.parameters);}}
xhr.open(method,url,true);if(method==='POST'||method==='PUT'){xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');}
xhr.send(body);return xhr;}
fabric.util.request=request;})();fabric.log=function(){};fabric.warn=function(){};if(typeof console!=='undefined'){['log','warn'].forEach(function(methodName){if(typeof console[methodName]!=='undefined'&&typeof console[methodName].apply==='function'){fabric[methodName]=function(){return console[methodName].apply(console,arguments);};}});}
(function(){function animate(options){requestAnimFrame(function(timestamp){options||(options={});var start=timestamp||+new Date(),duration=options.duration||500,finish=start+ duration,time,onChange=options.onChange||function(){},abort=options.abort||function(){return false;},easing=options.easing||function(t,b,c,d){return-c*Math.cos(t/d*(Math.PI/2))+ c+ b;},startValue='startValue'in options?options.startValue:0,endValue='endValue'in options?options.endValue:100,byValue=options.byValue||endValue- startValue;options.onStart&&options.onStart();(function tick(ticktime){time=ticktime||+new Date();var currentTime=time>finish?duration:(time- start);if(abort()){options.onComplete&&options.onComplete();return;}
onChange(easing(currentTime,startValue,byValue,duration));if(time>finish){options.onComplete&&options.onComplete();return;}
requestAnimFrame(tick);})(start);});}
var _requestAnimFrame=fabric.window.requestAnimationFrame||fabric.window.webkitRequestAnimationFrame||fabric.window.mozRequestAnimationFrame||fabric.window.oRequestAnimationFrame||fabric.window.msRequestAnimationFrame||function(callback){fabric.window.setTimeout(callback,1000/60);};function requestAnimFrame(){return _requestAnimFrame.apply(fabric.window,arguments);}
fabric.util.animate=animate;fabric.util.requestAnimFrame=requestAnimFrame;})();(function(){function normalize(a,c,p,s){if(a<Math.abs(c)){a=c;s=p/4;}
else{s=p/(2*Math.PI)*Math.asin(c/a);}
return{a:a,c:c,p:p,s:s};}
function elastic(opts,t,d){return opts.a*Math.pow(2,10*(t-=1))*Math.sin((t*d- opts.s)*(2*Math.PI)/ opts.p );
}
function easeOutCubic(t,b,c,d){return c*((t=t/d- 1)*t*t+ 1)+ b;}
function easeInOutCubic(t,b,c,d){t/=d/2;if(t<1){return c/2*t*t*t+ b;}
return c/2*((t-=2)*t*t+ 2)+ b;}
function easeInQuart(t,b,c,d){return c*(t/=d)*t*t*t+ b;}
function easeOutQuart(t,b,c,d){return-c*((t=t/d- 1)*t*t*t- 1)+ b;}
function easeInOutQuart(t,b,c,d){t/=d/2;if(t<1){return c/2*t*t*t*t+ b;}
return-c/2*((t-=2)*t*t*t- 2)+ b;}
function easeInQuint(t,b,c,d){return c*(t/=d)*t*t*t*t+ b;}
function easeOutQuint(t,b,c,d){return c*((t=t/d- 1)*t*t*t*t+ 1)+ b;}
function easeInOutQuint(t,b,c,d){t/=d/2;if(t<1){return c/2*t*t*t*t*t+ b;}
return c/2*((t-=2)*t*t*t*t+ 2)+ b;}
function easeInSine(t,b,c,d){return-c*Math.cos(t/d*(Math.PI/2))+ c+ b;}
function easeOutSine(t,b,c,d){return c*Math.sin(t/d*(Math.PI/2))+ b;}
function easeInOutSine(t,b,c,d){return-c/2*(Math.cos(Math.PI*t/d)- 1)+ b;}
function easeInExpo(t,b,c,d){return(t===0)?b:c*Math.pow(2,10*(t/d- 1))+ b;}
function easeOutExpo(t,b,c,d){return(t===d)?b+ c:c*(-Math.pow(2,-10*t/d)+ 1)+ b;}
function easeInOutExpo(t,b,c,d){if(t===0){return b;}
if(t===d){return b+ c;}
t/=d/2;if(t<1){return c/2*Math.pow(2,10*(t- 1))+ b;}
return c/2*(-Math.pow(2,-10*--t)+ 2)+ b;}
function easeInCirc(t,b,c,d){return-c*(Math.sqrt(1-(t/=d)*t)- 1)+ b;}
function easeOutCirc(t,b,c,d){return c*Math.sqrt(1-(t=t/d- 1)*t)+ b;}
function easeInOutCirc(t,b,c,d){t/=d/2;if(t<1){return-c/2*(Math.sqrt(1- t*t)- 1)+ b;}
return c/2*(Math.sqrt(1-(t-=2)*t)+ 1)+ b;}
function easeInElastic(t,b,c,d){var s=1.70158,p=0,a=c;if(t===0){return b;}
t/=d;if(t===1){return b+ c;}
if(!p){p=d*0.3;}
var opts=normalize(a,c,p,s);return-elastic(opts,t,d)+ b;}
function easeOutElastic(t,b,c,d){var s=1.70158,p=0,a=c;if(t===0){return b;}
t/=d;if(t===1){return b+ c;}
if(!p){p=d*0.3;}
var opts=normalize(a,c,p,s);return opts.a*Math.pow(2,-10*t)*Math.sin((t*d- opts.s)*(2*Math.PI)/ opts.p ) + opts.c + b;
}
function easeInOutElastic(t,b,c,d){var s=1.70158,p=0,a=c;if(t===0){return b;}
t/=d/2;if(t===2){return b+ c;}
if(!p){p=d*(0.3*1.5);}
var opts=normalize(a,c,p,s);if(t<1){return-0.5*elastic(opts,t,d)+ b;}
return opts.a*Math.pow(2,-10*(t-=1))*Math.sin((t*d- opts.s)*(2*Math.PI)/ opts.p ) * 0.5 + opts.c + b;
}
function easeInBack(t,b,c,d,s){if(s===undefined){s=1.70158;}
return c*(t/=d)*t*((s+ 1)*t- s)+ b;}
function easeOutBack(t,b,c,d,s){if(s===undefined){s=1.70158;}
return c*((t=t/d- 1)*t*((s+ 1)*t+ s)+ 1)+ b;}
function easeInOutBack(t,b,c,d,s){if(s===undefined){s=1.70158;}
t/=d/2;if(t<1){return c/2*(t*t*(((s*=(1.525))+ 1)*t- s))+ b;}
return c/2*((t-=2)*t*(((s*=(1.525))+ 1)*t+ s)+ 2)+ b;}
function easeInBounce(t,b,c,d){return c- easeOutBounce(d- t,0,c,d)+ b;}
function easeOutBounce(t,b,c,d){if((t/=d)<(1/2.75)){return c*(7.5625*t*t)+ b;}
else if(t<(2/2.75)){return c*(7.5625*(t-=(1.5/2.75))*t+ 0.75)+ b;}
else if(t<(2.5/2.75)){return c*(7.5625*(t-=(2.25/2.75))*t+ 0.9375)+ b;}
else{return c*(7.5625*(t-=(2.625/2.75))*t+ 0.984375)+ b;}}
function easeInOutBounce(t,b,c,d){if(t<d/2){return easeInBounce(t*2,0,c,d)*0.5+ b;}
return easeOutBounce(t*2- d,0,c,d)*0.5+ c*0.5+ b;}
fabric.util.ease={easeInQuad:function(t,b,c,d){return c*(t/=d)*t+ b;},easeOutQuad:function(t,b,c,d){return-c*(t/=d)*(t- 2)+ b;},easeInOutQuad:function(t,b,c,d){t/=(d/2);if(t<1){return c/2*t*t+ b;}
return-c/2*((--t)*(t- 2)- 1)+ b;},easeInCubic:function(t,b,c,d){return c*(t/=d)*t*t+ b;},easeOutCubic:easeOutCubic,easeInOutCubic:easeInOutCubic,easeInQuart:easeInQuart,easeOutQuart:easeOutQuart,easeInOutQuart:easeInOutQuart,easeInQuint:easeInQuint,easeOutQuint:easeOutQuint,easeInOutQuint:easeInOutQuint,easeInSine:easeInSine,easeOutSine:easeOutSine,easeInOutSine:easeInOutSine,easeInExpo:easeInExpo,easeOutExpo:easeOutExpo,easeInOutExpo:easeInOutExpo,easeInCirc:easeInCirc,easeOutCirc:easeOutCirc,easeInOutCirc:easeInOutCirc,easeInElastic:easeInElastic,easeOutElastic:easeOutElastic,easeInOutElastic:easeInOutElastic,easeInBack:easeInBack,easeOutBack:easeOutBack,easeInOutBack:easeInOutBack,easeInBounce:easeInBounce,easeOutBounce:easeOutBounce,easeInOutBounce:easeInOutBounce};}());(function(global){'use strict';var fabric=global.fabric||(global.fabric={}),extend=fabric.util.object.extend,capitalize=fabric.util.string.capitalize,clone=fabric.util.object.clone,toFixed=fabric.util.toFixed,parseUnit=fabric.util.parseUnit,multiplyTransformMatrices=fabric.util.multiplyTransformMatrices,attributesMap={cx:'left',x:'left',r:'radius',cy:'top',y:'top',display:'visible',visibility:'visible',transform:'transformMatrix','fill-opacity':'fillOpacity','fill-rule':'fillRule','font-family':'fontFamily','font-size':'fontSize','font-style':'fontStyle','font-weight':'fontWeight','stroke-dasharray':'strokeDashArray','stroke-linecap':'strokeLineCap','stroke-linejoin':'strokeLineJoin','stroke-miterlimit':'strokeMiterLimit','stroke-opacity':'strokeOpacity','stroke-width':'strokeWidth','text-decoration':'textDecoration','text-anchor':'originX'},colorAttributes={stroke:'strokeOpacity',fill:'fillOpacity'};fabric.cssRules={};fabric.gradientDefs={};function normalizeAttr(attr){if(attr in attributesMap){return attributesMap[attr];}
return attr;}
function normalizeValue(attr,value,parentAttributes,fontSize){var isArray=Object.prototype.toString.call(value)==='[object Array]',parsed;if((attr==='fill'||attr==='stroke')&&value==='none'){value='';}
else if(attr==='strokeDashArray'){value=value.replace(/,/g,' ').split(/\s+/).map(function(n){return parseFloat(n);});}
else if(attr==='transformMatrix'){if(parentAttributes&&parentAttributes.transformMatrix){value=multiplyTransformMatrices(parentAttributes.transformMatrix,fabric.parseTransformAttribute(value));}
else{value=fabric.parseTransformAttribute(value);}}
else if(attr==='visible'){value=(value==='none'||value==='hidden')?false:true;if(parentAttributes&&parentAttributes.visible===false){value=false;}}
else if(attr==='originX'){value=value==='start'?'left':value==='end'?'right':'center';}
else{parsed=isArray?value.map(parseUnit):parseUnit(value,fontSize);}
return(!isArray&&isNaN(parsed)?value:parsed);}
function _setStrokeFillOpacity(attributes){for(var attr in colorAttributes){if(!attributes[attr]||typeof attributes[colorAttributes[attr]]==='undefined'){continue;}
if(attributes[attr].indexOf('url(')===0){continue;}
var color=new fabric.Color(attributes[attr]);attributes[attr]=color.setAlpha(toFixed(color.getAlpha()*attributes[colorAttributes[attr]],2)).toRgba();}
return attributes;}
fabric.parseTransformAttribute=(function(){function rotateMatrix(matrix,args){var angle=args[0];matrix[0]=Math.cos(angle);matrix[1]=Math.sin(angle);matrix[2]=-Math.sin(angle);matrix[3]=Math.cos(angle);}
function scaleMatrix(matrix,args){var multiplierX=args[0],multiplierY=(args.length===2)?args[1]:args[0];matrix[0]=multiplierX;matrix[3]=multiplierY;}
function skewXMatrix(matrix,args){matrix[2]=Math.tan(fabric.util.degreesToRadians(args[0]));}
function skewYMatrix(matrix,args){matrix[1]=Math.tan(fabric.util.degreesToRadians(args[0]));}
function translateMatrix(matrix,args){matrix[4]=args[0];if(args.length===2){matrix[5]=args[1];}}
var iMatrix=[1,0,0,1,0,0],number=fabric.reNum,commaWsp='(?:\\s+,?\\s*|,\\s*)',skewX='(?:(skewX)\\s*\\(\\s*('+ number+')\\s*\\))',skewY='(?:(skewY)\\s*\\(\\s*('+ number+')\\s*\\))',rotate='(?:(rotate)\\s*\\(\\s*('+ number+')(?:'+
commaWsp+'('+ number+')'+
commaWsp+'('+ number+'))?\\s*\\))',scale='(?:(scale)\\s*\\(\\s*('+ number+')(?:'+
commaWsp+'('+ number+'))?\\s*\\))',translate='(?:(translate)\\s*\\(\\s*('+ number+')(?:'+
commaWsp+'('+ number+'))?\\s*\\))',matrix='(?:(matrix)\\s*\\(\\s*'+'('+ number+')'+ commaWsp+'('+ number+')'+ commaWsp+'('+ number+')'+ commaWsp+'('+ number+')'+ commaWsp+'('+ number+')'+ commaWsp+'('+ number+')'+'\\s*\\))',transform='(?:'+
matrix+'|'+
translate+'|'+
scale+'|'+
rotate+'|'+
skewX+'|'+
skewY+')',transforms='(?:'+ transform+'(?:'+ commaWsp+ transform+')*'+')',transformList='^\\s*(?:'+ transforms+'?)\\s*$',reTransformList=new RegExp(transformList),reTransform=new RegExp(transform,'g');return function(attributeValue){var matrix=iMatrix.concat(),matrices=[];if(!attributeValue||(attributeValue&&!reTransformList.test(attributeValue))){return matrix;}
attributeValue.replace(reTransform,function(match){var m=new RegExp(transform).exec(match).filter(function(match){return(match!==''&&match!=null);}),operation=m[1],args=m.slice(2).map(parseFloat);switch(operation){case'translate':translateMatrix(matrix,args);break;case'rotate':args[0]=fabric.util.degreesToRadians(args[0]);rotateMatrix(matrix,args);break;case'scale':scaleMatrix(matrix,args);break;case'skewX':skewXMatrix(matrix,args);break;case'skewY':skewYMatrix(matrix,args);break;case'matrix':matrix=args;break;}
matrices.push(matrix.concat());matrix=iMatrix.concat();});var combinedMatrix=matrices[0];while(matrices.length>1){matrices.shift();combinedMatrix=fabric.util.multiplyTransformMatrices(combinedMatrix,matrices[0]);}
return combinedMatrix;};})();function parseStyleString(style,oStyle){var attr,value;style.replace(/;$/,'').split(';').forEach(function(chunk){var pair=chunk.split(':');attr=normalizeAttr(pair[0].trim().toLowerCase());value=normalizeValue(attr,pair[1].trim());oStyle[attr]=value;});}
function parseStyleObject(style,oStyle){var attr,value;for(var prop in style){if(typeof style[prop]==='undefined'){continue;}
attr=normalizeAttr(prop.toLowerCase());value=normalizeValue(attr,style[prop]);oStyle[attr]=value;}}
function getGlobalStylesForElement(element,svgUid){var styles={};for(var rule in fabric.cssRules[svgUid]){if(elementMatchesRule(element,rule.split(' '))){for(var property in fabric.cssRules[svgUid][rule]){styles[property]=fabric.cssRules[svgUid][rule][property];}}}
return styles;}
function elementMatchesRule(element,selectors){var firstMatching,parentMatching=true;firstMatching=selectorMatches(element,selectors.pop());if(firstMatching&&selectors.length){parentMatching=doesSomeParentMatch(element,selectors);}
return firstMatching&&parentMatching&&(selectors.length===0);}
function doesSomeParentMatch(element,selectors){var selector,parentMatching=true;while(element.parentNode&&element.parentNode.nodeType===1&&selectors.length){if(parentMatching){selector=selectors.pop();}
element=element.parentNode;parentMatching=selectorMatches(element,selector);}
return selectors.length===0;}
function selectorMatches(element,selector){var nodeName=element.nodeName,classNames=element.getAttribute('class'),id=element.getAttribute('id'),matcher;matcher=new RegExp('^'+ nodeName,'i');selector=selector.replace(matcher,'');if(id&&selector.length){matcher=new RegExp('#'+ id+'(?![a-zA-Z\\-]+)','i');selector=selector.replace(matcher,'');}
if(classNames&&selector.length){classNames=classNames.split(' ');for(var i=classNames.length;i--;){matcher=new RegExp('\\.'+ classNames[i]+'(?![a-zA-Z\\-]+)','i');selector=selector.replace(matcher,'');}}
return selector.length===0;}
function parseUseDirectives(doc){var nodelist=doc.getElementsByTagName('use');while(nodelist.length){var el=nodelist[0],xlink=el.getAttribute('xlink:href').substr(1),x=el.getAttribute('x')||0,y=el.getAttribute('y')||0,el2=doc.getElementById(xlink).cloneNode(true),currentTrans=(el2.getAttribute('transform')||'')+' translate('+ x+', '+ y+')',parentNode;for(var j=0,attrs=el.attributes,l=attrs.length;j<l;j++){var attr=attrs.item(j);if(attr.nodeName==='x'||attr.nodeName==='y'||attr.nodeName==='xlink:href'){continue;}
if(attr.nodeName==='transform'){currentTrans=attr.nodeValue+' '+ currentTrans;}
else{el2.setAttribute(attr.nodeName,attr.nodeValue);}}
el2.setAttribute('transform',currentTrans);el2.setAttribute('instantiated_by_use','1');el2.removeAttribute('id');parentNode=el.parentNode;parentNode.replaceChild(el2,el);}}
var reViewBoxAttrValue=new RegExp('^'+'\\s*('+ fabric.reNum+'+)\\s*,?'+'\\s*('+ fabric.reNum+'+)\\s*,?'+'\\s*('+ fabric.reNum+'+)\\s*,?'+'\\s*('+ fabric.reNum+'+)\\s*'+'$');function addVBTransform(element,widthAttr,heightAttr){var viewBoxAttr=element.getAttribute('viewBox'),scaleX=1,scaleY=1,minX=0,minY=0,viewBoxWidth,viewBoxHeight,matrix,el;if(viewBoxAttr&&(viewBoxAttr=viewBoxAttr.match(reViewBoxAttrValue))){minX=-parseFloat(viewBoxAttr[1]),minY=-parseFloat(viewBoxAttr[2]),viewBoxWidth=parseFloat(viewBoxAttr[3]),viewBoxHeight=parseFloat(viewBoxAttr[4]);}
else{return;}
if(widthAttr&&widthAttr!==viewBoxWidth){scaleX=widthAttr/viewBoxWidth;}
if(heightAttr&&heightAttr!==viewBoxHeight){scaleY=heightAttr/viewBoxHeight;}
scaleY=scaleX=(scaleX>scaleY?scaleY:scaleX);if(!(scaleX!==1||scaleY!==1||minX!==0||minY!==0)){return;}
matrix=' matrix('+ scaleX+' 0'+' 0 '+
scaleY+' '+
(minX*scaleX)+' '+
(minY*scaleY)+') ';if(element.tagName==='svg'){el=element.ownerDocument.createElement('g');while(element.firstChild!=null){el.appendChild(element.firstChild);}
element.appendChild(el);}
else{el=element;matrix=el.getAttribute('transform')+ matrix;}
el.setAttribute('transform',matrix);}
fabric.parseSVGDocument=(function(){var reAllowedSVGTagNames=/^(path|circle|polygon|polyline|ellipse|rect|line|image|text)$/,reViewBoxTagNames=/^(symbol|image|marker|pattern|view)$/;function hasAncestorWithNodeName(element,nodeName){while(element&&(element=element.parentNode)){if(nodeName.test(element.nodeName)&&!element.getAttribute('instantiated_by_use')){return true;}}
return false;}
return function(doc,callback,reviver){if(!doc){return;}
parseUseDirectives(doc);var startTime=new Date(),svgUid=fabric.Object.__uid++,widthAttr,heightAttr,toBeParsed=false;if(doc.getAttribute('width')&&doc.getAttribute('width')!=='100%'){widthAttr=parseUnit(doc.getAttribute('width'));}
if(doc.getAttribute('height')&&doc.getAttribute('height')!=='100%'){heightAttr=parseUnit(doc.getAttribute('height'));}
if(!widthAttr||!heightAttr){var viewBoxAttr=doc.getAttribute('viewBox');if(viewBoxAttr&&(viewBoxAttr=viewBoxAttr.match(reViewBoxAttrValue))){widthAttr=parseFloat(viewBoxAttr[3]),heightAttr=parseFloat(viewBoxAttr[4]);}
else{toBeParsed=true;}}
addVBTransform(doc,widthAttr,heightAttr);var descendants=fabric.util.toArray(doc.getElementsByTagName('*'));if(descendants.length===0&&fabric.isLikelyNode){descendants=doc.selectNodes('//*[name(.)!="svg"]');var arr=[];for(var i=0,len=descendants.length;i<len;i++){arr[i]=descendants[i];}
descendants=arr;}
var elements=descendants.filter(function(el){reViewBoxTagNames.test(el.tagName)&&addVBTransform(el,0,0);return reAllowedSVGTagNames.test(el.tagName)&&!hasAncestorWithNodeName(el,/^(?:pattern|defs|symbol)$/);});if(!elements||(elements&&!elements.length)){callback&&callback([],{});return;}
var options={width:widthAttr,height:heightAttr,svgUid:svgUid,toBeParsed:toBeParsed};fabric.gradientDefs[svgUid]=fabric.getGradientDefs(doc);fabric.cssRules[svgUid]=fabric.getCSSRules(doc);fabric.parseElements(elements,function(instances){fabric.documentParsingTime=new Date()- startTime;if(callback){callback(instances,options);}},clone(options),reviver);};})();var svgCache={has:function(name,callback){callback(false);},get:function(){},set:function(){}};function _enlivenCachedObject(cachedObject){var objects=cachedObject.objects,options=cachedObject.options;objects=objects.map(function(o){return fabric[capitalize(o.type)].fromObject(o);});return({objects:objects,options:options});}
function _createSVGPattern(markup,canvas,property){if(canvas[property]&&canvas[property].toSVG){markup.push('<pattern x="0" y="0" id="',property,'Pattern" ','width="',canvas[property].source.width,'" height="',canvas[property].source.height,'" patternUnits="userSpaceOnUse">','<image x="0" y="0" ','width="',canvas[property].source.width,'" height="',canvas[property].source.height,'" xlink:href="',canvas[property].source.src,'"></image></pattern>');}}
var reFontDeclaration=new RegExp('(normal|italic)?\\s*(normal|small-caps)?\\s*'+'(normal|bold|bolder|lighter|100|200|300|400|500|600|700|800|900)?\\s*('+
fabric.reNum+'(?:px|cm|mm|em|pt|pc|in)*)(?:\\/(normal|'+ fabric.reNum+'))?\\s+(.*)');extend(fabric,{parseFontDeclaration:function(value,oStyle){var match=value.match(reFontDeclaration);if(!match){return;}
var fontStyle=match[1],fontWeight=match[3],fontSize=match[4],lineHeight=match[5],fontFamily=match[6];if(fontStyle){oStyle.fontStyle=fontStyle;}
if(fontWeight){oStyle.fontWeight=isNaN(parseFloat(fontWeight))?fontWeight:parseFloat(fontWeight);}
if(fontSize){oStyle.fontSize=parseUnit(fontSize);}
if(fontFamily){oStyle.fontFamily=fontFamily;}
if(lineHeight){oStyle.lineHeight=lineHeight==='normal'?1:lineHeight;}},getGradientDefs:function(doc){var linearGradientEls=doc.getElementsByTagName('linearGradient'),radialGradientEls=doc.getElementsByTagName('radialGradient'),el,i,j=0,id,xlink,elList=[],gradientDefs={},idsToXlinkMap={};elList.length=linearGradientEls.length+ radialGradientEls.length;i=linearGradientEls.length;while(i--){elList[j++]=linearGradientEls[i];}
i=radialGradientEls.length;while(i--){elList[j++]=radialGradientEls[i];}
while(j--){el=elList[j];xlink=el.getAttribute('xlink:href');id=el.getAttribute('id');if(xlink){idsToXlinkMap[id]=xlink.substr(1);}
gradientDefs[id]=el;}
for(id in idsToXlinkMap){var el2=gradientDefs[idsToXlinkMap[id]].cloneNode(true);el=gradientDefs[id];while(el2.firstChild){el.appendChild(el2.firstChild);}}
return gradientDefs;},parseAttributes:function(element,attributes,svgUid){if(!element){return;}
var value,parentAttributes={},fontSize;if(typeof svgUid==='undefined'){svgUid=element.getAttribute('svgUid');}
if(element.parentNode&&/^symbol|[g|a]$/i.test(element.parentNode.nodeName)){parentAttributes=fabric.parseAttributes(element.parentNode,attributes,svgUid);}
fontSize=(parentAttributes&&parentAttributes.fontSize)||element.getAttribute('font-size')||fabric.Text.DEFAULT_SVG_FONT_SIZE;var ownAttributes=attributes.reduce(function(memo,attr){value=element.getAttribute(attr);if(value){attr=normalizeAttr(attr);value=normalizeValue(attr,value,parentAttributes,fontSize);memo[attr]=value;}
return memo;},{});ownAttributes=extend(ownAttributes,extend(getGlobalStylesForElement(element,svgUid),fabric.parseStyleAttribute(element)));if(ownAttributes.font){fabric.parseFontDeclaration(ownAttributes.font,ownAttributes);}
return _setStrokeFillOpacity(extend(parentAttributes,ownAttributes));},parseElements:function(elements,callback,options,reviver){new fabric.ElementsParser(elements,callback,options,reviver).parse();},parseStyleAttribute:function(element){var oStyle={},style=element.getAttribute('style');if(!style){return oStyle;}
if(typeof style==='string'){parseStyleString(style,oStyle);}
else{parseStyleObject(style,oStyle);}
return oStyle;},parsePointsAttribute:function(points){if(!points){return null;}
points=points.replace(/,/g,' ').trim();points=points.split(/\s+/);var parsedPoints=[],i,len;i=0;len=points.length;for(;i<len;i+=2){parsedPoints.push({x:parseFloat(points[i]),y:parseFloat(points[i+ 1])});}
return parsedPoints;},getCSSRules:function(doc){var styles=doc.getElementsByTagName('style'),allRules={},rules;for(var i=0,len=styles.length;i<len;i++){var styleContents=styles[i].textContent;styleContents=styleContents.replace(/\/\*[\s\S]*?\*\//g,'');if(styleContents.trim()===''){continue;}
rules=styleContents.match(/[^{]*\{[\s\S]*?\}/g);rules=rules.map(function(rule){return rule.trim();});rules.forEach(function(rule){var match=rule.match(/([\s\S]*?)\s*\{([^}]*)\}/),ruleObj={},declaration=match[2].trim(),propertyValuePairs=declaration.replace(/;$/,'').split(/\s*;\s*/);for(var i=0,len=propertyValuePairs.length;i<len;i++){var pair=propertyValuePairs[i].split(/\s*:\s*/),property=normalizeAttr(pair[0]),value=normalizeValue(property,pair[1],pair[0]);ruleObj[property]=value;}
rule=match[1];rule.split(',').forEach(function(_rule){_rule=_rule.replace(/^svg/i,'').trim();if(_rule===''){return;}
allRules[_rule]=fabric.util.object.clone(ruleObj);});});}
return allRules;},loadSVGFromURL:function(url,callback,reviver){url=url.replace(/^\n\s*/,'').trim();svgCache.has(url,function(hasUrl){if(hasUrl){svgCache.get(url,function(value){var enlivedRecord=_enlivenCachedObject(value);callback(enlivedRecord.objects,enlivedRecord.options);});}
else{new fabric.util.request(url,{method:'get',onComplete:onComplete});}});function onComplete(r){var xml=r.responseXML;if(xml&&!xml.documentElement&&fabric.window.ActiveXObject&&r.responseText){xml=new ActiveXObject('Microsoft.XMLDOM');xml.async='false';xml.loadXML(r.responseText.replace(/<!DOCTYPE[\s\S]*?(\[[\s\S]*\])*?>/i,''));}
if(!xml||!xml.documentElement){return;}
fabric.parseSVGDocument(xml.documentElement,function(results,options){svgCache.set(url,{objects:fabric.util.array.invoke(results,'toObject'),options:options});callback(results,options);},reviver);}},loadSVGFromString:function(string,callback,reviver){string=string.trim();var doc;if(typeof DOMParser!=='undefined'){var parser=new DOMParser();if(parser&&parser.parseFromString){doc=parser.parseFromString(string,'text/xml');}}
else if(fabric.window.ActiveXObject){doc=new ActiveXObject('Microsoft.XMLDOM');doc.async='false';doc.loadXML(string.replace(/<!DOCTYPE[\s\S]*?(\[[\s\S]*\])*?>/i,''));}
fabric.parseSVGDocument(doc.documentElement,function(results,options){callback(results,options);},reviver);},createSVGFontFacesMarkup:function(objects){var markup='';for(var i=0,len=objects.length;i<len;i++){if(objects[i].type!=='text'||!objects[i].path){continue;}
markup+=['@font-face {','font-family: ',objects[i].fontFamily,'; ','src: url(\'',objects[i].path,'\')','}'].join('');}
if(markup){markup=['<style type="text/css">','<![CDATA[',markup,']]>','</style>'].join('');}
return markup;},createSVGRefElementsMarkup:function(canvas){var markup=[];_createSVGPattern(markup,canvas,'backgroundColor');_createSVGPattern(markup,canvas,'overlayColor');return markup.join('');}});})(typeof exports!=='undefined'?exports:this);fabric.ElementsParser=function(elements,callback,options,reviver){this.elements=elements;this.callback=callback;this.options=options;this.reviver=reviver;this.svgUid=(options&&options.svgUid)||0;};fabric.ElementsParser.prototype.parse=function(){this.instances=new Array(this.elements.length);this.numElements=this.elements.length;this.createObjects();};fabric.ElementsParser.prototype.createObjects=function(){for(var i=0,len=this.elements.length;i<len;i++){this.elements[i].setAttribute('svgUid',this.svgUid);(function(_this,i){setTimeout(function(){_this.createObject(_this.elements[i],i);},0);})(this,i);}};fabric.ElementsParser.prototype.createObject=function(el,index){var klass=fabric[fabric.util.string.capitalize(el.tagName)];if(klass&&klass.fromElement){try{this._createObject(klass,el,index);}
catch(err){fabric.log(err);}}
else{this.checkIfDone();}};fabric.ElementsParser.prototype._createObject=function(klass,el,index){if(klass.async){klass.fromElement(el,this.createCallback(index,el),this.options);}
else{var obj=klass.fromElement(el,this.options);this.resolveGradient(obj,'fill');this.resolveGradient(obj,'stroke');this.reviver&&this.reviver(el,obj);this.instances[index]=obj;this.checkIfDone();}};fabric.ElementsParser.prototype.createCallback=function(index,el){var _this=this;return function(obj){_this.resolveGradient(obj,'fill');_this.resolveGradient(obj,'stroke');_this.reviver&&_this.reviver(el,obj);_this.instances[index]=obj;_this.checkIfDone();};};fabric.ElementsParser.prototype.resolveGradient=function(obj,property){var instanceFillValue=obj.get(property);if(!(/^url\(/).test(instanceFillValue)){return;}
var gradientId=instanceFillValue.slice(5,instanceFillValue.length- 1);if(fabric.gradientDefs[this.svgUid][gradientId]){obj.set(property,fabric.Gradient.fromElement(fabric.gradientDefs[this.svgUid][gradientId],obj));}};fabric.ElementsParser.prototype.checkIfDone=function(){if(--this.numElements===0){this.instances=this.instances.filter(function(el){return el!=null;});this.callback(this.instances);}};(function(global){'use strict';var fabric=global.fabric||(global.fabric={});if(fabric.Point){fabric.warn('fabric.Point is already defined');return;}
fabric.Point=Point;function Point(x,y){this.x=x;this.y=y;}
Point.prototype={constructor:Point,add:function(that){return new Point(this.x+ that.x,this.y+ that.y);},addEquals:function(that){this.x+=that.x;this.y+=that.y;return this;},scalarAdd:function(scalar){return new Point(this.x+ scalar,this.y+ scalar);},scalarAddEquals:function(scalar){this.x+=scalar;this.y+=scalar;return this;},subtract:function(that){return new Point(this.x- that.x,this.y- that.y);},subtractEquals:function(that){this.x-=that.x;this.y-=that.y;return this;},scalarSubtract:function(scalar){return new Point(this.x- scalar,this.y- scalar);},scalarSubtractEquals:function(scalar){this.x-=scalar;this.y-=scalar;return this;},multiply:function(scalar){return new Point(this.x*scalar,this.y*scalar);},multiplyEquals:function(scalar){this.x*=scalar;this.y*=scalar;return this;},divide:function(scalar){return new Point(this.x/scalar,this.y/scalar);},divideEquals:function(scalar){this.x/=scalar;this.y/=scalar;return this;},eq:function(that){return(this.x===that.x&&this.y===that.y);},lt:function(that){return(this.x<that.x&&this.y<that.y);},lte:function(that){return(this.x<=that.x&&this.y<=that.y);},gt:function(that){return(this.x>that.x&&this.y>that.y);},gte:function(that){return(this.x>=that.x&&this.y>=that.y);},lerp:function(that,t){return new Point(this.x+(that.x- this.x)*t,this.y+(that.y- this.y)*t);},distanceFrom:function(that){var dx=this.x- that.x,dy=this.y- that.y;return Math.sqrt(dx*dx+ dy*dy);},midPointFrom:function(that){return new Point(this.x+(that.x- this.x)/2, this.y + (that.y - this.y)/2);
},min:function(that){return new Point(Math.min(this.x,that.x),Math.min(this.y,that.y));},max:function(that){return new Point(Math.max(this.x,that.x),Math.max(this.y,that.y));},toString:function(){return this.x+','+ this.y;},setXY:function(x,y){this.x=x;this.y=y;},setFromPoint:function(that){this.x=that.x;this.y=that.y;},swap:function(that){var x=this.x,y=this.y;this.x=that.x;this.y=that.y;that.x=x;that.y=y;}};})(typeof exports!=='undefined'?exports:this);(function(global){'use strict';var fabric=global.fabric||(global.fabric={});if(fabric.Intersection){fabric.warn('fabric.Intersection is already defined');return;}
function Intersection(status){this.status=status;this.points=[];}
fabric.Intersection=Intersection;fabric.Intersection.prototype={appendPoint:function(point){this.points.push(point);},appendPoints:function(points){this.points=this.points.concat(points);}};fabric.Intersection.intersectLineLine=function(a1,a2,b1,b2){var result,uaT=(b2.x- b1.x)*(a1.y- b1.y)-(b2.y- b1.y)*(a1.x- b1.x),ubT=(a2.x- a1.x)*(a1.y- b1.y)-(a2.y- a1.y)*(a1.x- b1.x),uB=(b2.y- b1.y)*(a2.x- a1.x)-(b2.x- b1.x)*(a2.y- a1.y);if(uB!==0){var ua=uaT/uB,ub=ubT/uB;if(0<=ua&&ua<=1&&0<=ub&&ub<=1){result=new Intersection('Intersection');result.points.push(new fabric.Point(a1.x+ ua*(a2.x- a1.x),a1.y+ ua*(a2.y- a1.y)));}
else{result=new Intersection();}}
else{if(uaT===0||ubT===0){result=new Intersection('Coincident');}
else{result=new Intersection('Parallel');}}
return result;};fabric.Intersection.intersectLinePolygon=function(a1,a2,points){var result=new Intersection(),length=points.length;for(var i=0;i<length;i++){var b1=points[i],b2=points[(i+ 1)%length],inter=Intersection.intersectLineLine(a1,a2,b1,b2);result.appendPoints(inter.points);}
if(result.points.length>0){result.status='Intersection';}
return result;};fabric.Intersection.intersectPolygonPolygon=function(points1,points2){var result=new Intersection(),length=points1.length;for(var i=0;i<length;i++){var a1=points1[i],a2=points1[(i+ 1)%length],inter=Intersection.intersectLinePolygon(a1,a2,points2);result.appendPoints(inter.points);}
if(result.points.length>0){result.status='Intersection';}
return result;};fabric.Intersection.intersectPolygonRectangle=function(points,r1,r2){var min=r1.min(r2),max=r1.max(r2),topRight=new fabric.Point(max.x,min.y),bottomLeft=new fabric.Point(min.x,max.y),inter1=Intersection.intersectLinePolygon(min,topRight,points),inter2=Intersection.intersectLinePolygon(topRight,max,points),inter3=Intersection.intersectLinePolygon(max,bottomLeft,points),inter4=Intersection.intersectLinePolygon(bottomLeft,min,points),result=new Intersection();result.appendPoints(inter1.points);result.appendPoints(inter2.points);result.appendPoints(inter3.points);result.appendPoints(inter4.points);if(result.points.length>0){result.status='Intersection';}
return result;};})(typeof exports!=='undefined'?exports:this);(function(global){'use strict';var fabric=global.fabric||(global.fabric={});if(fabric.Color){fabric.warn('fabric.Color is already defined.');return;}
function Color(color){if(!color){this.setSource([0,0,0,1]);}
else{this._tryParsingColor(color);}}
fabric.Color=Color;fabric.Color.prototype={_tryParsingColor:function(color){var source;if(color in Color.colorNameMap){color=Color.colorNameMap[color];}
if(color==='transparent'){this.setSource([255,255,255,0]);return;}
source=Color.sourceFromHex(color);if(!source){source=Color.sourceFromRgb(color);}
if(!source){source=Color.sourceFromHsl(color);}
if(source){this.setSource(source);}},_rgbToHsl:function(r,g,b){r/=255,g/=255,b/=255;var h,s,l,max=fabric.util.array.max([r,g,b]),min=fabric.util.array.min([r,g,b]);l=(max+ min)/ 2;
if(max===min){h=s=0;}
else{var d=max- min;s=l>0.5?d/(2- max- min):d/(max+ min);switch(max){case r:h=(g- b)/ d + (g < b ? 6 : 0);
break;case g:h=(b- r)/ d + 2;
break;case b:h=(r- g)/ d + 4;
break;}
h/=6;}
return[Math.round(h*360),Math.round(s*100),Math.round(l*100)];},getSource:function(){return this._source;},setSource:function(source){this._source=source;},toRgb:function(){var source=this.getSource();return'rgb('+ source[0]+','+ source[1]+','+ source[2]+')';},toRgba:function(){var source=this.getSource();return'rgba('+ source[0]+','+ source[1]+','+ source[2]+','+ source[3]+')';},toHsl:function(){var source=this.getSource(),hsl=this._rgbToHsl(source[0],source[1],source[2]);return'hsl('+ hsl[0]+','+ hsl[1]+'%,'+ hsl[2]+'%)';},toHsla:function(){var source=this.getSource(),hsl=this._rgbToHsl(source[0],source[1],source[2]);return'hsla('+ hsl[0]+','+ hsl[1]+'%,'+ hsl[2]+'%,'+ source[3]+')';},toHex:function(){var source=this.getSource(),r,g,b;r=source[0].toString(16);r=(r.length===1)?('0'+ r):r;g=source[1].toString(16);g=(g.length===1)?('0'+ g):g;b=source[2].toString(16);b=(b.length===1)?('0'+ b):b;return r.toUpperCase()+ g.toUpperCase()+ b.toUpperCase();},getAlpha:function(){return this.getSource()[3];},setAlpha:function(alpha){var source=this.getSource();source[3]=alpha;this.setSource(source);return this;},toGrayscale:function(){var source=this.getSource(),average=parseInt((source[0]*0.3+ source[1]*0.59+ source[2]*0.11).toFixed(0),10),currentAlpha=source[3];this.setSource([average,average,average,currentAlpha]);return this;},toBlackWhite:function(threshold){var source=this.getSource(),average=(source[0]*0.3+ source[1]*0.59+ source[2]*0.11).toFixed(0),currentAlpha=source[3];threshold=threshold||127;average=(Number(average)<Number(threshold))?0:255;this.setSource([average,average,average,currentAlpha]);return this;},overlayWith:function(otherColor){if(!(otherColor instanceof Color)){otherColor=new Color(otherColor);}
var result=[],alpha=this.getAlpha(),otherAlpha=0.5,source=this.getSource(),otherSource=otherColor.getSource();for(var i=0;i<3;i++){result.push(Math.round((source[i]*(1- otherAlpha))+(otherSource[i]*otherAlpha)));}
result[3]=alpha;this.setSource(result);return this;}};fabric.Color.reRGBa=/^rgba?\(\s*(\d{1,3}(?:\.\d+)?\%?)\s*,\s*(\d{1,3}(?:\.\d+)?\%?)\s*,\s*(\d{1,3}(?:\.\d+)?\%?)\s*(?:\s*,\s*(\d+(?:\.\d+)?)\s*)?\)$/;fabric.Color.reHSLa=/^hsla?\(\s*(\d{1,3})\s*,\s*(\d{1,3}\%)\s*,\s*(\d{1,3}\%)\s*(?:\s*,\s*(\d+(?:\.\d+)?)\s*)?\)$/;fabric.Color.reHex=/^#?([0-9a-f]{6}|[0-9a-f]{3})$/i;fabric.Color.colorNameMap={aqua:'#00FFFF',black:'#000000',blue:'#0000FF',fuchsia:'#FF00FF',gray:'#808080',green:'#008000',lime:'#00FF00',maroon:'#800000',navy:'#000080',olive:'#808000',orange:'#FFA500',purple:'#800080',red:'#FF0000',silver:'#C0C0C0',teal:'#008080',white:'#FFFFFF',yellow:'#FFFF00'};function hue2rgb(p,q,t){if(t<0){t+=1;}
if(t>1){t-=1;}
if(t<1/6){return p+(q- p)*6*t;}
if(t<1/2){return q;}
if(t<2/3){return p+(q- p)*(2/3- t)*6;}
return p;}
fabric.Color.fromRgb=function(color){return Color.fromSource(Color.sourceFromRgb(color));};fabric.Color.sourceFromRgb=function(color){var match=color.match(Color.reRGBa);if(match){var r=parseInt(match[1],10)/ (/%$/.test(match[1]) ? 100 : 1) * (/%$/.test(match[1]) ? 255 : 1),
g=parseInt(match[2],10)/ (/%$/.test(match[2]) ? 100 : 1) * (/%$/.test(match[2]) ? 255 : 1),
b=parseInt(match[3],10)/ (/%$/.test(match[3]) ? 100 : 1) * (/%$/.test(match[3]) ? 255 : 1);
return[parseInt(r,10),parseInt(g,10),parseInt(b,10),match[4]?parseFloat(match[4]):1];}};fabric.Color.fromRgba=Color.fromRgb;fabric.Color.fromHsl=function(color){return Color.fromSource(Color.sourceFromHsl(color));};fabric.Color.sourceFromHsl=function(color){var match=color.match(Color.reHSLa);if(!match){return;}
var h=(((parseFloat(match[1])%360)+ 360)%360)/ 360,
s=parseFloat(match[2])/ (/%$/.test(match[2]) ? 100 : 1),
l=parseFloat(match[3])/ (/%$/.test(match[3]) ? 100 : 1),
r,g,b;if(s===0){r=g=b=l;}
else{var q=l<=0.5?l*(s+ 1):l+ s- l*s,p=l*2- q;r=hue2rgb(p,q,h+ 1/3);g=hue2rgb(p,q,h);b=hue2rgb(p,q,h- 1/3);}
return[Math.round(r*255),Math.round(g*255),Math.round(b*255),match[4]?parseFloat(match[4]):1];};fabric.Color.fromHsla=Color.fromHsl;fabric.Color.fromHex=function(color){return Color.fromSource(Color.sourceFromHex(color));};fabric.Color.sourceFromHex=function(color){if(color.match(Color.reHex)){var value=color.slice(color.indexOf('#')+ 1),isShortNotation=(value.length===3),r=isShortNotation?(value.charAt(0)+ value.charAt(0)):value.substring(0,2),g=isShortNotation?(value.charAt(1)+ value.charAt(1)):value.substring(2,4),b=isShortNotation?(value.charAt(2)+ value.charAt(2)):value.substring(4,6);return[parseInt(r,16),parseInt(g,16),parseInt(b,16),1];}};fabric.Color.fromSource=function(source){var oColor=new Color();oColor.setSource(source);return oColor;};})(typeof exports!=='undefined'?exports:this);(function(){function getColorStop(el){var style=el.getAttribute('style'),offset=el.getAttribute('offset'),color,colorAlpha,opacity;offset=parseFloat(offset)/ (/%$/.test(offset) ? 100 : 1);
offset=offset<0?0:offset>1?1:offset;if(style){var keyValuePairs=style.split(/\s*;\s*/);if(keyValuePairs[keyValuePairs.length- 1]===''){keyValuePairs.pop();}
for(var i=keyValuePairs.length;i--;){var split=keyValuePairs[i].split(/\s*:\s*/),key=split[0].trim(),value=split[1].trim();if(key==='stop-color'){color=value;}
else if(key==='stop-opacity'){opacity=value;}}}
if(!color){color=el.getAttribute('stop-color')||'rgb(0,0,0)';}
if(!opacity){opacity=el.getAttribute('stop-opacity');}
color=new fabric.Color(color);colorAlpha=color.getAlpha();opacity=isNaN(parseFloat(opacity))?1:parseFloat(opacity);opacity*=colorAlpha;return{offset:offset,color:color.toRgb(),opacity:opacity};}
function getLinearCoords(el){return{x1:el.getAttribute('x1')||0,y1:el.getAttribute('y1')||0,x2:el.getAttribute('x2')||'100%',y2:el.getAttribute('y2')||0};}
function getRadialCoords(el){return{x1:el.getAttribute('fx')||el.getAttribute('cx')||'50%',y1:el.getAttribute('fy')||el.getAttribute('cy')||'50%',r1:0,x2:el.getAttribute('cx')||'50%',y2:el.getAttribute('cy')||'50%',r2:el.getAttribute('r')||'50%'};}
fabric.Gradient=fabric.util.createClass({offsetX:0,offsetY:0,initialize:function(options){options||(options={});var coords={};this.id=fabric.Object.__uid++;this.type=options.type||'linear';coords={x1:options.coords.x1||0,y1:options.coords.y1||0,x2:options.coords.x2||0,y2:options.coords.y2||0};if(this.type==='radial'){coords.r1=options.coords.r1||0;coords.r2=options.coords.r2||0;}
this.coords=coords;this.colorStops=options.colorStops.slice();if(options.gradientTransform){this.gradientTransform=options.gradientTransform;}
this.offsetX=options.offsetX||this.offsetX;this.offsetY=options.offsetY||this.offsetY;},addColorStop:function(colorStop){for(var position in colorStop){var color=new fabric.Color(colorStop[position]);this.colorStops.push({offset:position,color:color.toRgb(),opacity:color.getAlpha()});}
return this;},toObject:function(){return{type:this.type,coords:this.coords,colorStops:this.colorStops,offsetX:this.offsetX,offsetY:this.offsetY};},toSVG:function(object){var coords=fabric.util.object.clone(this.coords),markup,commonAttributes;this.colorStops.sort(function(a,b){return a.offset- b.offset;});if(!(object.group&&object.group.type==='path-group')){for(var prop in coords){if(prop==='x1'||prop==='x2'||prop==='r2'){coords[prop]+=this.offsetX- object.width/2;}
else if(prop==='y1'||prop==='y2'){coords[prop]+=this.offsetY- object.height/2;}}}
commonAttributes='id="SVGID_'+ this.id+'" gradientUnits="userSpaceOnUse"';if(this.gradientTransform){commonAttributes+=' gradientTransform="matrix('+ this.gradientTransform.join(' ')+')" ';}
if(this.type==='linear'){markup=['<linearGradient ',commonAttributes,' x1="',coords.x1,'" y1="',coords.y1,'" x2="',coords.x2,'" y2="',coords.y2,'">\n'];}
else if(this.type==='radial'){markup=['<radialGradient ',commonAttributes,' cx="',coords.x2,'" cy="',coords.y2,'" r="',coords.r2,'" fx="',coords.x1,'" fy="',coords.y1,'">\n'];}
for(var i=0;i<this.colorStops.length;i++){markup.push('<stop ','offset="',(this.colorStops[i].offset*100)+'%','" style="stop-color:',this.colorStops[i].color,(this.colorStops[i].opacity!=null?';stop-opacity: '+ this.colorStops[i].opacity:';'),'"/>\n');}
markup.push((this.type==='linear'?'</linearGradient>\n':'</radialGradient>\n'));return markup.join('');},toLive:function(ctx,object){var gradient,prop,coords=fabric.util.object.clone(this.coords);if(!this.type){return;}
if(object.group&&object.group.type==='path-group'){for(prop in coords){if(prop==='x1'||prop==='x2'){coords[prop]+=-this.offsetX+ object.width/2;}
else if(prop==='y1'||prop==='y2'){coords[prop]+=-this.offsetY+ object.height/2;}}}
if(object.type==='text'||object.type==='i-text'){for(prop in coords){if(prop==='x1'||prop==='x2'){coords[prop]-=object.width/2;}
else if(prop==='y1'||prop==='y2'){coords[prop]-=object.height/2;}}}
if(this.type==='linear'){gradient=ctx.createLinearGradient(coords.x1,coords.y1,coords.x2,coords.y2);}
else if(this.type==='radial'){gradient=ctx.createRadialGradient(coords.x1,coords.y1,coords.r1,coords.x2,coords.y2,coords.r2);}
for(var i=0,len=this.colorStops.length;i<len;i++){var color=this.colorStops[i].color,opacity=this.colorStops[i].opacity,offset=this.colorStops[i].offset;if(typeof opacity!=='undefined'){color=new fabric.Color(color).setAlpha(opacity).toRgba();}
gradient.addColorStop(parseFloat(offset),color);}
return gradient;}});fabric.util.object.extend(fabric.Gradient,{fromElement:function(el,instance){var colorStopEls=el.getElementsByTagName('stop'),type=(el.nodeName==='linearGradient'?'linear':'radial'),gradientUnits=el.getAttribute('gradientUnits')||'objectBoundingBox',gradientTransform=el.getAttribute('gradientTransform'),colorStops=[],coords={},ellipseMatrix;if(type==='linear'){coords=getLinearCoords(el);}
else if(type==='radial'){coords=getRadialCoords(el);}
for(var i=colorStopEls.length;i--;){colorStops.push(getColorStop(colorStopEls[i]));}
ellipseMatrix=_convertPercentUnitsToValues(instance,coords,gradientUnits);var gradient=new fabric.Gradient({type:type,coords:coords,colorStops:colorStops,offsetX:-instance.left,offsetY:-instance.top});if(gradientTransform||ellipseMatrix!==''){gradient.gradientTransform=fabric.parseTransformAttribute((gradientTransform||'')+ ellipseMatrix);}
return gradient;},forObject:function(obj,options){options||(options={});_convertPercentUnitsToValues(obj,options.coords,'userSpaceOnUse');return new fabric.Gradient(options);}});function _convertPercentUnitsToValues(object,options,gradientUnits){var propValue,addFactor=0,multFactor=1,ellipseMatrix='';for(var prop in options){propValue=parseFloat(options[prop],10);if(typeof options[prop]==='string'&&/^\d+%$/.test(options[prop])){multFactor=0.01;}
else{multFactor=1;}
if(prop==='x1'||prop==='x2'||prop==='r2'){multFactor*=gradientUnits==='objectBoundingBox'?object.width:1;addFactor=gradientUnits==='objectBoundingBox'?object.left||0:0;}
else if(prop==='y1'||prop==='y2'){multFactor*=gradientUnits==='objectBoundingBox'?object.height:1;addFactor=gradientUnits==='objectBoundingBox'?object.top||0:0;}
options[prop]=propValue*multFactor+ addFactor;}
if(object.type==='ellipse'&&options.r2!==null&&gradientUnits==='objectBoundingBox'&&object.rx!==object.ry){var scaleFactor=object.ry/object.rx;ellipseMatrix=' scale(1, '+ scaleFactor+')';if(options.y1){options.y1/=scaleFactor;}
if(options.y2){options.y2/=scaleFactor;}}
return ellipseMatrix;}})();fabric.Pattern=fabric.util.createClass({repeat:'repeat',offsetX:0,offsetY:0,initialize:function(options){options||(options={});this.id=fabric.Object.__uid++;if(options.source){if(typeof options.source==='string'){if(typeof fabric.util.getFunctionBody(options.source)!=='undefined'){this.source=new Function(fabric.util.getFunctionBody(options.source));}
else{var _this=this;this.source=fabric.util.createImage();fabric.util.loadImage(options.source,function(img){_this.source=img;});}}
else{this.source=options.source;}}
if(options.repeat){this.repeat=options.repeat;}
if(options.offsetX){this.offsetX=options.offsetX;}
if(options.offsetY){this.offsetY=options.offsetY;}},toObject:function(){var source;if(typeof this.source==='function'){source=String(this.source);}
else if(typeof this.source.src==='string'){source=this.source.src;}
return{source:source,repeat:this.repeat,offsetX:this.offsetX,offsetY:this.offsetY};},toSVG:function(object){var patternSource=typeof this.source==='function'?this.source():this.source,patternWidth=patternSource.width/object.getWidth(),patternHeight=patternSource.height/object.getHeight(),patternOffsetX=this.offsetX/object.getWidth(),patternOffsetY=this.offsetY/object.getHeight(),patternImgSrc='';if(this.repeat==='repeat-x'||this.repeat==='no-repeat'){patternHeight=1;}
if(this.repeat==='repeat-y'||this.repeat==='no-repeat'){patternWidth=1;}
if(patternSource.src){patternImgSrc=patternSource.src;}
else if(patternSource.toDataURL){patternImgSrc=patternSource.toDataURL();}
return'<pattern id="SVGID_'+ this.id+'" x="'+ patternOffsetX+'" y="'+ patternOffsetY+'" width="'+ patternWidth+'" height="'+ patternHeight+'">\n'+'<image x="0" y="0"'+' width="'+ patternSource.width+'" height="'+ patternSource.height+'" xlink:href="'+ patternImgSrc+'"></image>\n'+'</pattern>\n';},toLive:function(ctx){var source=typeof this.source==='function'?this.source():this.source;if(!source){return'';}
if(typeof source.src!=='undefined'){if(!source.complete){return'';}
if(source.naturalWidth===0||source.naturalHeight===0){return'';}}
return ctx.createPattern(source,this.repeat);}});(function(global){'use strict';var fabric=global.fabric||(global.fabric={}),toFixed=fabric.util.toFixed;if(fabric.Shadow){fabric.warn('fabric.Shadow is already defined.');return;}
fabric.Shadow=fabric.util.createClass({color:'rgb(0,0,0)',blur:0,offsetX:0,offsetY:0,affectStroke:false,includeDefaultValues:true,initialize:function(options){if(typeof options==='string'){options=this._parseShadow(options);}
for(var prop in options){this[prop]=options[prop];}
this.id=fabric.Object.__uid++;},_parseShadow:function(shadow){var shadowStr=shadow.trim(),offsetsAndBlur=fabric.Shadow.reOffsetsAndBlur.exec(shadowStr)||[],color=shadowStr.replace(fabric.Shadow.reOffsetsAndBlur,'')||'rgb(0,0,0)';return{color:color.trim(),offsetX:parseInt(offsetsAndBlur[1],10)||0,offsetY:parseInt(offsetsAndBlur[2],10)||0,blur:parseInt(offsetsAndBlur[3],10)||0};},toString:function(){return[this.offsetX,this.offsetY,this.blur,this.color].join('px ');},toSVG:function(object){var mode='SourceAlpha',fBoxX=40,fBoxY=40;if(object&&(object.fill===this.color||object.stroke===this.color)){mode='SourceGraphic';}
if(object.width&&object.height){fBoxX=toFixed(Math.abs(this.offsetX/object.getWidth()),2)*100+ 20;fBoxY=toFixed(Math.abs(this.offsetY/object.getHeight()),2)*100+ 20;}
return('<filter id="SVGID_'+ this.id+'" y="-'+ fBoxY+'%" height="'+(100+ 2*fBoxY)+'%" '+'x="-'+ fBoxX+'%" width="'+(100+ 2*fBoxX)+'%" '+'>\n'+'\t<feGaussianBlur in="'+ mode+'" stdDeviation="'+
toFixed(this.blur?this.blur/2:0,3)+'" result="blurOut"></feGaussianBlur>\n'+'\t<feColorMatrix result="matrixOut" in="blurOut" type="matrix" '+'values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 0.30 0" ></feColorMatrix >\n'+'\t<feOffset dx="'+ this.offsetX+'" dy="'+ this.offsetY+'"></feOffset>\n'+'\t<feMerge>\n'+'\t\t<feMergeNode></feMergeNode>\n'+'\t\t<feMergeNode in="SourceGraphic"></feMergeNode>\n'+'\t</feMerge>\n'+'</filter>\n');},toObject:function(){if(this.includeDefaultValues){return{color:this.color,blur:this.blur,offsetX:this.offsetX,offsetY:this.offsetY};}
var obj={},proto=fabric.Shadow.prototype;if(this.color!==proto.color){obj.color=this.color;}
if(this.blur!==proto.blur){obj.blur=this.blur;}
if(this.offsetX!==proto.offsetX){obj.offsetX=this.offsetX;}
if(this.offsetY!==proto.offsetY){obj.offsetY=this.offsetY;}
return obj;}});fabric.Shadow.reOffsetsAndBlur=/(?:\s|^)(-?\d+(?:px)?(?:\s?|$))?(-?\d+(?:px)?(?:\s?|$))?(\d+(?:px)?)?(?:\s?|$)(?:$|\s)/;})(typeof exports!=='undefined'?exports:this);(function(){'use strict';if(fabric.StaticCanvas){fabric.warn('fabric.StaticCanvas is already defined.');return;}
var extend=fabric.util.object.extend,getElementOffset=fabric.util.getElementOffset,removeFromArray=fabric.util.removeFromArray,CANVAS_INIT_ERROR=new Error('Could not initialize `canvas` element');fabric.StaticCanvas=fabric.util.createClass({initialize:function(el,options){options||(options={});this._initStatic(el,options);fabric.StaticCanvas.activeInstance=this;},backgroundColor:'',backgroundImage:null,overlayColor:'',overlayImage:null,includeDefaultValues:true,stateful:true,renderOnAddRemove:true,clipTo:null,controlsAboveOverlay:false,allowTouchScrolling:false,imageSmoothingEnabled:true,preserveObjectStacking:false,viewportTransform:[1,0,0,1,0,0],onBeforeScaleRotate:function(){},_initStatic:function(el,options){this._objects=[];this._createLowerCanvas(el);this._initOptions(options);this._setImageSmoothing();if(options.overlayImage){this.setOverlayImage(options.overlayImage,this.renderAll.bind(this));}
if(options.backgroundImage){this.setBackgroundImage(options.backgroundImage,this.renderAll.bind(this));}
if(options.backgroundColor){this.setBackgroundColor(options.backgroundColor,this.renderAll.bind(this));}
if(options.overlayColor){this.setOverlayColor(options.overlayColor,this.renderAll.bind(this));}
this.calcOffset();},calcOffset:function(){this._offset=getElementOffset(this.lowerCanvasEl);return this;},setOverlayImage:function(image,callback,options){return this.__setBgOverlayImage('overlayImage',image,callback,options);},setBackgroundImage:function(image,callback,options){return this.__setBgOverlayImage('backgroundImage',image,callback,options);},setOverlayColor:function(overlayColor,callback){return this.__setBgOverlayColor('overlayColor',overlayColor,callback);},setBackgroundColor:function(backgroundColor,callback){return this.__setBgOverlayColor('backgroundColor',backgroundColor,callback);},_setImageSmoothing:function(){var ctx=this.getContext();ctx.imageSmoothingEnabled=this.imageSmoothingEnabled;ctx.webkitImageSmoothingEnabled=this.imageSmoothingEnabled;ctx.mozImageSmoothingEnabled=this.imageSmoothingEnabled;ctx.msImageSmoothingEnabled=this.imageSmoothingEnabled;ctx.oImageSmoothingEnabled=this.imageSmoothingEnabled;},__setBgOverlayImage:function(property,image,callback,options){if(typeof image==='string'){fabric.util.loadImage(image,function(img){this[property]=new fabric.Image(img,options);callback&&callback();},this,options&&options.crossOrigin);}
else{options&&image.setOptions(options);this[property]=image;callback&&callback();}
return this;},__setBgOverlayColor:function(property,color,callback){if(color&&color.source){var _this=this;fabric.util.loadImage(color.source,function(img){_this[property]=new fabric.Pattern({source:img,repeat:color.repeat,offsetX:color.offsetX,offsetY:color.offsetY});callback&&callback();});}
else{this[property]=color;callback&&callback();}
return this;},_createCanvasElement:function(){var element=fabric.document.createElement('canvas');if(!element.style){element.style={};}
if(!element){throw CANVAS_INIT_ERROR;}
this._initCanvasElement(element);return element;},_initCanvasElement:function(element){fabric.util.createCanvasElement(element);if(typeof element.getContext==='undefined'){throw CANVAS_INIT_ERROR;}},_initOptions:function(options){for(var prop in options){this[prop]=options[prop];}
this.width=this.width||parseInt(this.lowerCanvasEl.width,10)||0;this.height=this.height||parseInt(this.lowerCanvasEl.height,10)||0;if(!this.lowerCanvasEl.style){return;}
this.lowerCanvasEl.width=this.width;this.lowerCanvasEl.height=this.height;this.lowerCanvasEl.style.width=this.width+'px';this.lowerCanvasEl.style.height=this.height+'px';this.viewportTransform=this.viewportTransform.slice();},_createLowerCanvas:function(canvasEl){this.lowerCanvasEl=fabric.util.getById(canvasEl)||this._createCanvasElement();this._initCanvasElement(this.lowerCanvasEl);fabric.util.addClass(this.lowerCanvasEl,'lower-canvas');if(this.interactive){this._applyCanvasStyle(this.lowerCanvasEl);}
this.contextContainer=this.lowerCanvasEl.getContext('2d');},getWidth:function(){return this.width;},getHeight:function(){return this.height;},setWidth:function(value,options){return this.setDimensions({width:value},options);},setHeight:function(value,options){return this.setDimensions({height:value},options);},setDimensions:function(dimensions,options){var cssValue;options=options||{};for(var prop in dimensions){cssValue=dimensions[prop];if(!options.cssOnly){this._setBackstoreDimension(prop,dimensions[prop]);cssValue+='px';}
if(!options.backstoreOnly){this._setCssDimension(prop,cssValue);}}
if(!options.cssOnly){this.renderAll();}
this.calcOffset();return this;},_setBackstoreDimension:function(prop,value){this.lowerCanvasEl[prop]=value;if(this.upperCanvasEl){this.upperCanvasEl[prop]=value;}
if(this.cacheCanvasEl){this.cacheCanvasEl[prop]=value;}
this[prop]=value;return this;},_setCssDimension:function(prop,value){this.lowerCanvasEl.style[prop]=value;if(this.upperCanvasEl){this.upperCanvasEl.style[prop]=value;}
if(this.wrapperEl){this.wrapperEl.style[prop]=value;}
return this;},getZoom:function(){return Math.sqrt(this.viewportTransform[0]*this.viewportTransform[3]);},setViewportTransform:function(vpt){var activeGroup=this.getActiveGroup();this.viewportTransform=vpt;this.renderAll();for(var i=0,len=this._objects.length;i<len;i++){this._objects[i].setCoords();}
if(activeGroup){activeGroup.setCoords();}
return this;},zoomToPoint:function(point,value){var before=point;point=fabric.util.transformPoint(point,fabric.util.invertTransform(this.viewportTransform));this.viewportTransform[0]=value;this.viewportTransform[3]=value;var after=fabric.util.transformPoint(point,this.viewportTransform);this.viewportTransform[4]+=before.x- after.x;this.viewportTransform[5]+=before.y- after.y;this.renderAll();for(var i=0,len=this._objects.length;i<len;i++){this._objects[i].setCoords();}
return this;},setZoom:function(value){this.zoomToPoint(new fabric.Point(0,0),value);return this;},absolutePan:function(point){this.viewportTransform[4]=-point.x;this.viewportTransform[5]=-point.y;this.renderAll();for(var i=0,len=this._objects.length;i<len;i++){this._objects[i].setCoords();}
return this;},relativePan:function(point){return this.absolutePan(new fabric.Point(-point.x- this.viewportTransform[4],-point.y- this.viewportTransform[5]));},getElement:function(){return this.lowerCanvasEl;},getActiveObject:function(){return null;},getActiveGroup:function(){return null;},_draw:function(ctx,object){if(!object){return;}
ctx.save();var v=this.viewportTransform;ctx.transform(v[0],v[1],v[2],v[3],v[4],v[5]);if(this._shouldRenderObject(object)){object.render(ctx);}
ctx.restore();if(!this.controlsAboveOverlay){object._renderControls(ctx);}},_shouldRenderObject:function(object){if(!object){return false;}
return(object!==this.getActiveGroup()||!this.preserveObjectStacking);},_onObjectAdded:function(obj){this.stateful&&obj.setupState();obj.canvas=this;obj.setCoords();this.fire('object:added',{target:obj});obj.fire('added');},_onObjectRemoved:function(obj){if(this.getActiveObject()===obj){this.fire('before:selection:cleared',{target:obj});this._discardActiveObject();this.fire('selection:cleared');}
this.fire('object:removed',{target:obj});obj.fire('removed');},clearContext:function(ctx){ctx.clearRect(0,0,this.width,this.height);return this;},getContext:function(){return this.contextContainer;},clear:function(){this._objects.length=0;if(this.discardActiveGroup){this.discardActiveGroup();}
if(this.discardActiveObject){this.discardActiveObject();}
this.clearContext(this.contextContainer);if(this.contextTop){this.clearContext(this.contextTop);}
this.fire('canvas:cleared');this.renderAll();return this;},renderAll:function(allOnTop){var canvasToDrawOn=this[(allOnTop===true&&this.interactive)?'contextTop':'contextContainer'],activeGroup=this.getActiveGroup();if(this.contextTop&&this.selection&&!this._groupSelector){this.clearContext(this.contextTop);}
if(!allOnTop){this.clearContext(canvasToDrawOn);}
this.fire('before:render');if(this.clipTo){fabric.util.clipContext(this,canvasToDrawOn);}
this._renderBackground(canvasToDrawOn);this._renderObjects(canvasToDrawOn,activeGroup);this._renderActiveGroup(canvasToDrawOn,activeGroup);if(this.clipTo){canvasToDrawOn.restore();}
this._renderOverlay(canvasToDrawOn);if(this.controlsAboveOverlay&&this.interactive){this.drawControls(canvasToDrawOn);}
this.fire('after:render');return this;},_renderObjects:function(ctx,activeGroup){var i,length;if(!activeGroup||this.preserveObjectStacking){for(i=0,length=this._objects.length;i<length;++i){this._draw(ctx,this._objects[i]);}}
else{for(i=0,length=this._objects.length;i<length;++i){if(this._objects[i]&&!activeGroup.contains(this._objects[i])){this._draw(ctx,this._objects[i]);}}}},_renderActiveGroup:function(ctx,activeGroup){if(activeGroup){var sortedObjects=[];this.forEachObject(function(object){if(activeGroup.contains(object)){sortedObjects.push(object);}});activeGroup._set('objects',sortedObjects);this._draw(ctx,activeGroup);}},_renderBackground:function(ctx){if(this.backgroundColor){ctx.fillStyle=this.backgroundColor.toLive?this.backgroundColor.toLive(ctx):this.backgroundColor;ctx.fillRect(this.backgroundColor.offsetX||0,this.backgroundColor.offsetY||0,this.width,this.height);}
if(this.backgroundImage){this._draw(ctx,this.backgroundImage);}},_renderOverlay:function(ctx){if(this.overlayColor){ctx.fillStyle=this.overlayColor.toLive?this.overlayColor.toLive(ctx):this.overlayColor;ctx.fillRect(this.overlayColor.offsetX||0,this.overlayColor.offsetY||0,this.width,this.height);}
if(this.overlayImage){this._draw(ctx,this.overlayImage);}},renderTop:function(){var ctx=this.contextTop||this.contextContainer;this.clearContext(ctx);if(this.selection&&this._groupSelector){this._drawSelection();}
var activeGroup=this.getActiveGroup();if(activeGroup){activeGroup.render(ctx);}
this._renderOverlay(ctx);this.fire('after:render');return this;},getCenter:function(){return{top:this.getHeight()/ 2,
left:this.getWidth()/ 2
};},centerObjectH:function(object){this._centerObject(object,new fabric.Point(this.getCenter().left,object.getCenterPoint().y));this.renderAll();return this;},centerObjectV:function(object){this._centerObject(object,new fabric.Point(object.getCenterPoint().x,this.getCenter().top));this.renderAll();return this;},centerObject:function(object){var center=this.getCenter();this._centerObject(object,new fabric.Point(center.left,center.top));this.renderAll();return this;},_centerObject:function(object,center){object.setPositionByOrigin(center,'center','center');return this;},toDatalessJSON:function(propertiesToInclude){return this.toDatalessObject(propertiesToInclude);},toObject:function(propertiesToInclude){return this._toObjectMethod('toObject',propertiesToInclude);},toDatalessObject:function(propertiesToInclude){return this._toObjectMethod('toDatalessObject',propertiesToInclude);},_toObjectMethod:function(methodName,propertiesToInclude){var data={objects:this._toObjects(methodName,propertiesToInclude)};extend(data,this.__serializeBgOverlay());fabric.util.populateWithProperties(this,data,propertiesToInclude);return data;},_toObjects:function(methodName,propertiesToInclude){return this.getObjects().map(function(instance){return this._toObject(instance,methodName,propertiesToInclude);},this);},_toObject:function(instance,methodName,propertiesToInclude){var originalValue;if(!this.includeDefaultValues){originalValue=instance.includeDefaultValues;instance.includeDefaultValues=false;}
var originalProperties=this._realizeGroupTransformOnObject(instance),object=instance[methodName](propertiesToInclude);if(!this.includeDefaultValues){instance.includeDefaultValues=originalValue;}
this._unwindGroupTransformOnObject(instance,originalProperties);return object;},_realizeGroupTransformOnObject:function(instance){var layoutProps=['angle','flipX','flipY','height','left','scaleX','scaleY','top','width'];if(instance.group&&instance.group===this.getActiveGroup()){var originalValues={};layoutProps.forEach(function(prop){originalValues[prop]=instance[prop];});this.getActiveGroup().realizeTransform(instance);return originalValues;}
else{return null;}},_unwindGroupTransformOnObject:function(instance,originalValues){if(originalValues){instance.set(originalValues);}},__serializeBgOverlay:function(){var data={background:(this.backgroundColor&&this.backgroundColor.toObject)?this.backgroundColor.toObject():this.backgroundColor};if(this.overlayColor){data.overlay=this.overlayColor.toObject?this.overlayColor.toObject():this.overlayColor;}
if(this.backgroundImage){data.backgroundImage=this.backgroundImage.toObject();}
if(this.overlayImage){data.overlayImage=this.overlayImage.toObject();}
return data;},svgViewportTransformation:true,toSVG:function(options,reviver){options||(options={});var markup=[];this._setSVGPreamble(markup,options);this._setSVGHeader(markup,options);this._setSVGBgOverlayColor(markup,'backgroundColor');this._setSVGBgOverlayImage(markup,'backgroundImage');this._setSVGObjects(markup,reviver);this._setSVGBgOverlayColor(markup,'overlayColor');this._setSVGBgOverlayImage(markup,'overlayImage');markup.push('</svg>');return markup.join('');},_setSVGPreamble:function(markup,options){if(!options.suppressPreamble){markup.push('<?xml version="1.0" encoding="',(options.encoding||'UTF-8'),'" standalone="no" ?>','<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" ','"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n');}},_setSVGHeader:function(markup,options){var width,height,vpt;if(options.viewBox){width=options.viewBox.width;height=options.viewBox.height;}
else{width=this.width;height=this.height;if(!this.svgViewportTransformation){vpt=this.viewportTransform;width/=vpt[0];height/=vpt[3];}}
markup.push('<svg ','xmlns="http://www.w3.org/2000/svg" ','xmlns:xlink="http://www.w3.org/1999/xlink" ','version="1.1" ','width="',width,'" ','height="',height,'" ',(this.backgroundColor&&!this.backgroundColor.toLive?'style="background-color: '+ this.backgroundColor+'" ':null),(options.viewBox?'viewBox="'+
options.viewBox.x+' '+
options.viewBox.y+' '+
options.viewBox.width+' '+
options.viewBox.height+'" ':null),'xml:space="preserve">','<desc>Created with Fabric.js ',fabric.version,'</desc>','<defs>',fabric.createSVGFontFacesMarkup(this.getObjects()),fabric.createSVGRefElementsMarkup(this),'</defs>');},_setSVGObjects:function(markup,reviver){for(var i=0,objects=this.getObjects(),len=objects.length;i<len;i++){var instance=objects[i],originalProperties=this._realizeGroupTransformOnObject(instance);markup.push(instance.toSVG(reviver));this._unwindGroupTransformOnObject(instance,originalProperties);}},_setSVGBgOverlayImage:function(markup,property){if(this[property]&&this[property].toSVG){markup.push(this[property].toSVG());}},_setSVGBgOverlayColor:function(markup,property){if(this[property]&&this[property].source){markup.push('<rect x="',this[property].offsetX,'" y="',this[property].offsetY,'" ','width="',(this[property].repeat==='repeat-y'||this[property].repeat==='no-repeat'?this[property].source.width:this.width),'" height="',(this[property].repeat==='repeat-x'||this[property].repeat==='no-repeat'?this[property].source.height:this.height),'" fill="url(#'+ property+'Pattern)"','></rect>');}
else if(this[property]&&property==='overlayColor'){markup.push('<rect x="0" y="0" ','width="',this.width,'" height="',this.height,'" fill="',this[property],'"','></rect>');}},sendToBack:function(object){removeFromArray(this._objects,object);this._objects.unshift(object);return this.renderAll&&this.renderAll();},bringToFront:function(object){removeFromArray(this._objects,object);this._objects.push(object);return this.renderAll&&this.renderAll();},sendBackwards:function(object,intersecting){var idx=this._objects.indexOf(object);if(idx!==0){var newIdx=this._findNewLowerIndex(object,idx,intersecting);removeFromArray(this._objects,object);this._objects.splice(newIdx,0,object);this.renderAll&&this.renderAll();}
return this;},_findNewLowerIndex:function(object,idx,intersecting){var newIdx;if(intersecting){newIdx=idx;for(var i=idx- 1;i>=0;--i){var isIntersecting=object.intersectsWithObject(this._objects[i])||object.isContainedWithinObject(this._objects[i])||this._objects[i].isContainedWithinObject(object);if(isIntersecting){newIdx=i;break;}}}
else{newIdx=idx- 1;}
return newIdx;},bringForward:function(object,intersecting){var idx=this._objects.indexOf(object);if(idx!==this._objects.length- 1){var newIdx=this._findNewUpperIndex(object,idx,intersecting);removeFromArray(this._objects,object);this._objects.splice(newIdx,0,object);this.renderAll&&this.renderAll();}
return this;},_findNewUpperIndex:function(object,idx,intersecting){var newIdx;if(intersecting){newIdx=idx;for(var i=idx+ 1;i<this._objects.length;++i){var isIntersecting=object.intersectsWithObject(this._objects[i])||object.isContainedWithinObject(this._objects[i])||this._objects[i].isContainedWithinObject(object);if(isIntersecting){newIdx=i;break;}}}
else{newIdx=idx+ 1;}
return newIdx;},moveTo:function(object,index){removeFromArray(this._objects,object);this._objects.splice(index,0,object);return this.renderAll&&this.renderAll();},dispose:function(){this.clear();this.interactive&&this.removeListeners();return this;},toString:function(){return'#<fabric.Canvas ('+ this.complexity()+'): '+'{ objects: '+ this.getObjects().length+' }>';}});extend(fabric.StaticCanvas.prototype,fabric.Observable);extend(fabric.StaticCanvas.prototype,fabric.Collection);extend(fabric.StaticCanvas.prototype,fabric.DataURLExporter);extend(fabric.StaticCanvas,{EMPTY_JSON:'{"objects": [], "background": "white"}',supports:function(methodName){var el=fabric.util.createCanvasElement();if(!el||!el.getContext){return null;}
var ctx=el.getContext('2d');if(!ctx){return null;}
switch(methodName){case'getImageData':return typeof ctx.getImageData!=='undefined';case'setLineDash':return typeof ctx.setLineDash!=='undefined';case'toDataURL':return typeof el.toDataURL!=='undefined';case'toDataURLWithQuality':try{el.toDataURL('image/jpeg',0);return true;}
catch(e){}
return false;default:return null;}}});fabric.StaticCanvas.prototype.toJSON=fabric.StaticCanvas.prototype.toObject;})();fabric.BaseBrush=fabric.util.createClass({color:'rgb(0, 0, 0)',width:1,shadow:null,strokeLineCap:'round',strokeLineJoin:'round',strokeDashArray:null,setShadow:function(options){this.shadow=new fabric.Shadow(options);return this;},_setBrushStyles:function(){var ctx=this.canvas.contextTop;ctx.strokeStyle=this.color;ctx.lineWidth=this.width;ctx.lineCap=this.strokeLineCap;ctx.lineJoin=this.strokeLineJoin;if(this.strokeDashArray&&fabric.StaticCanvas.supports('setLineDash')){ctx.setLineDash(this.strokeDashArray);}},_setShadow:function(){if(!this.shadow){return;}
var ctx=this.canvas.contextTop;ctx.shadowColor=this.shadow.color;ctx.shadowBlur=this.shadow.blur;ctx.shadowOffsetX=this.shadow.offsetX;ctx.shadowOffsetY=this.shadow.offsetY;},_resetShadow:function(){var ctx=this.canvas.contextTop;ctx.shadowColor='';ctx.shadowBlur=ctx.shadowOffsetX=ctx.shadowOffsetY=0;}});(function(){fabric.PencilBrush=fabric.util.createClass(fabric.BaseBrush,{initialize:function(canvas){this.canvas=canvas;this._points=[];},onMouseDown:function(pointer){this._prepareForDrawing(pointer);this._captureDrawingPath(pointer);this._render();},onMouseMove:function(pointer){this._captureDrawingPath(pointer);this.canvas.clearContext(this.canvas.contextTop);this._render();},onMouseUp:function(){this._finalizeAndAddPath();},_prepareForDrawing:function(pointer){var p=new fabric.Point(pointer.x,pointer.y);this._reset();this._addPoint(p);this.canvas.contextTop.moveTo(p.x,p.y);},_addPoint:function(point){this._points.push(point);},_reset:function(){this._points.length=0;this._setBrushStyles();this._setShadow();},_captureDrawingPath:function(pointer){var pointerPoint=new fabric.Point(pointer.x,pointer.y);this._addPoint(pointerPoint);},_render:function(){var ctx=this.canvas.contextTop,v=this.canvas.viewportTransform,p1=this._points[0],p2=this._points[1];ctx.save();ctx.transform(v[0],v[1],v[2],v[3],v[4],v[5]);ctx.beginPath();if(this._points.length===2&&p1.x===p2.x&&p1.y===p2.y){p1.x-=0.5;p2.x+=0.5;}
ctx.moveTo(p1.x,p1.y);for(var i=1,len=this._points.length;i<len;i++){var midPoint=p1.midPointFrom(p2);ctx.quadraticCurveTo(p1.x,p1.y,midPoint.x,midPoint.y);p1=this._points[i];p2=this._points[i+ 1];}
ctx.lineTo(p1.x,p1.y);ctx.stroke();ctx.restore();},convertPointsToSVGPath:function(points){var path=[],p1=new fabric.Point(points[0].x,points[0].y),p2=new fabric.Point(points[1].x,points[1].y);path.push('M ',points[0].x,' ',points[0].y,' ');for(var i=1,len=points.length;i<len;i++){var midPoint=p1.midPointFrom(p2);path.push('Q ',p1.x,' ',p1.y,' ',midPoint.x,' ',midPoint.y,' ');p1=new fabric.Point(points[i].x,points[i].y);if((i+ 1)<points.length){p2=new fabric.Point(points[i+ 1].x,points[i+ 1].y);}}
path.push('L ',p1.x,' ',p1.y,' ');return path;},createPath:function(pathData){var path=new fabric.Path(pathData,{fill:null,stroke:this.color,strokeWidth:this.width,strokeLineCap:this.strokeLineCap,strokeLineJoin:this.strokeLineJoin,strokeDashArray:this.strokeDashArray,originX:'center',originY:'center'});if(this.shadow){this.shadow.affectStroke=true;path.setShadow(this.shadow);}
return path;},_finalizeAndAddPath:function(){var ctx=this.canvas.contextTop;ctx.closePath();var pathData=this.convertPointsToSVGPath(this._points).join('');if(pathData==='M 0 0 Q 0 0 0 0 L 0 0'){this.canvas.renderAll();return;}
var path=this.createPath(pathData);this.canvas.add(path);path.setCoords();this.canvas.clearContext(this.canvas.contextTop);this._resetShadow();this.canvas.renderAll();this.canvas.fire('path:created',{path:path});}});})();fabric.CircleBrush=fabric.util.createClass(fabric.BaseBrush,{width:10,initialize:function(canvas){this.canvas=canvas;this.points=[];},drawDot:function(pointer){var point=this.addPoint(pointer),ctx=this.canvas.contextTop,v=this.canvas.viewportTransform;ctx.save();ctx.transform(v[0],v[1],v[2],v[3],v[4],v[5]);ctx.fillStyle=point.fill;ctx.beginPath();ctx.arc(point.x,point.y,point.radius,0,Math.PI*2,false);ctx.closePath();ctx.fill();ctx.restore();},onMouseDown:function(pointer){this.points.length=0;this.canvas.clearContext(this.canvas.contextTop);this._setShadow();this.drawDot(pointer);},onMouseMove:function(pointer){this.drawDot(pointer);},onMouseUp:function(){var originalRenderOnAddRemove=this.canvas.renderOnAddRemove;this.canvas.renderOnAddRemove=false;var circles=[];for(var i=0,len=this.points.length;i<len;i++){var point=this.points[i],circle=new fabric.Circle({radius:point.radius,left:point.x,top:point.y,originX:'center',originY:'center',fill:point.fill});this.shadow&&circle.setShadow(this.shadow);circles.push(circle);}
var group=new fabric.Group(circles,{originX:'center',originY:'center'});group.canvas=this.canvas;this.canvas.add(group);this.canvas.fire('path:created',{path:group});this.canvas.clearContext(this.canvas.contextTop);this._resetShadow();this.canvas.renderOnAddRemove=originalRenderOnAddRemove;this.canvas.renderAll();},addPoint:function(pointer){var pointerPoint=new fabric.Point(pointer.x,pointer.y),circleRadius=fabric.util.getRandomInt(Math.max(0,this.width- 20),this.width+ 20)/ 2,
circleColor=new fabric.Color(this.color).setAlpha(fabric.util.getRandomInt(0,100)/ 100)
.toRgba();pointerPoint.radius=circleRadius;pointerPoint.fill=circleColor;this.points.push(pointerPoint);return pointerPoint;}});fabric.SprayBrush=fabric.util.createClass(fabric.BaseBrush,{width:10,density:20,dotWidth:1,dotWidthVariance:1,randomOpacity:false,optimizeOverlapping:true,initialize:function(canvas){this.canvas=canvas;this.sprayChunks=[];},onMouseDown:function(pointer){this.sprayChunks.length=0;this.canvas.clearContext(this.canvas.contextTop);this._setShadow();this.addSprayChunk(pointer);this.render();},onMouseMove:function(pointer){this.addSprayChunk(pointer);this.render();},onMouseUp:function(){var originalRenderOnAddRemove=this.canvas.renderOnAddRemove;this.canvas.renderOnAddRemove=false;var rects=[];for(var i=0,ilen=this.sprayChunks.length;i<ilen;i++){var sprayChunk=this.sprayChunks[i];for(var j=0,jlen=sprayChunk.length;j<jlen;j++){var rect=new fabric.Rect({width:sprayChunk[j].width,height:sprayChunk[j].width,left:sprayChunk[j].x+ 1,top:sprayChunk[j].y+ 1,originX:'center',originY:'center',fill:this.color});this.shadow&&rect.setShadow(this.shadow);rects.push(rect);}}
if(this.optimizeOverlapping){rects=this._getOptimizedRects(rects);}
var group=new fabric.Group(rects,{originX:'center',originY:'center'});group.canvas=this.canvas;this.canvas.add(group);this.canvas.fire('path:created',{path:group});this.canvas.clearContext(this.canvas.contextTop);this._resetShadow();this.canvas.renderOnAddRemove=originalRenderOnAddRemove;this.canvas.renderAll();},_getOptimizedRects:function(rects){var uniqueRects={},key;for(var i=0,len=rects.length;i<len;i++){key=rects[i].left+''+ rects[i].top;if(!uniqueRects[key]){uniqueRects[key]=rects[i];}}
var uniqueRectsArray=[];for(key in uniqueRects){uniqueRectsArray.push(uniqueRects[key]);}
return uniqueRectsArray;},render:function(){var ctx=this.canvas.contextTop;ctx.fillStyle=this.color;var v=this.canvas.viewportTransform;ctx.save();ctx.transform(v[0],v[1],v[2],v[3],v[4],v[5]);for(var i=0,len=this.sprayChunkPoints.length;i<len;i++){var point=this.sprayChunkPoints[i];if(typeof point.opacity!=='undefined'){ctx.globalAlpha=point.opacity;}
ctx.fillRect(point.x,point.y,point.width,point.width);}
ctx.restore();},addSprayChunk:function(pointer){this.sprayChunkPoints=[];var x,y,width,radius=this.width/2;for(var i=0;i<this.density;i++){x=fabric.util.getRandomInt(pointer.x- radius,pointer.x+ radius);y=fabric.util.getRandomInt(pointer.y- radius,pointer.y+ radius);if(this.dotWidthVariance){width=fabric.util.getRandomInt(Math.max(1,this.dotWidth- this.dotWidthVariance),this.dotWidth+ this.dotWidthVariance);}
else{width=this.dotWidth;}
var point=new fabric.Point(x,y);point.width=width;if(this.randomOpacity){point.opacity=fabric.util.getRandomInt(0,100)/ 100;
}
this.sprayChunkPoints.push(point);}
this.sprayChunks.push(this.sprayChunkPoints);}});fabric.PatternBrush=fabric.util.createClass(fabric.PencilBrush,{getPatternSrc:function(){var dotWidth=20,dotDistance=5,patternCanvas=fabric.document.createElement('canvas'),patternCtx=patternCanvas.getContext('2d');patternCanvas.width=patternCanvas.height=dotWidth+ dotDistance;patternCtx.fillStyle=this.color;patternCtx.beginPath();patternCtx.arc(dotWidth/2,dotWidth/2,dotWidth/2,0,Math.PI*2,false);patternCtx.closePath();patternCtx.fill();return patternCanvas;},getPatternSrcFunction:function(){return String(this.getPatternSrc).replace('this.color','"'+ this.color+'"');},getPattern:function(){return this.canvas.contextTop.createPattern(this.source||this.getPatternSrc(),'repeat');},_setBrushStyles:function(){this.callSuper('_setBrushStyles');this.canvas.contextTop.strokeStyle=this.getPattern();},createPath:function(pathData){var path=this.callSuper('createPath',pathData);path.stroke=new fabric.Pattern({source:this.source||this.getPatternSrcFunction()});return path;}});(function(){var getPointer=fabric.util.getPointer,degreesToRadians=fabric.util.degreesToRadians,radiansToDegrees=fabric.util.radiansToDegrees,atan2=Math.atan2,abs=Math.abs,STROKE_OFFSET=0.5;fabric.Canvas=fabric.util.createClass(fabric.StaticCanvas,{initialize:function(el,options){options||(options={});this._initStatic(el,options);this._initInteractive();this._createCacheCanvas();fabric.Canvas.activeInstance=this;},uniScaleTransform:false,centeredScaling:false,centeredRotation:false,interactive:true,selection:true,selectionColor:'rgba(100, 100, 255, 0.3)',selectionDashArray:[],selectionBorderColor:'rgba(255, 255, 255, 0.3)',selectionLineWidth:1,hoverCursor:'move',moveCursor:'move',defaultCursor:'default',freeDrawingCursor:'crosshair',rotationCursor:'crosshair',containerClass:'canvas-container',perPixelTargetFind:false,targetFindTolerance:0,skipTargetFind:false,_initInteractive:function(){this._currentTransform=null;this._groupSelector=null;this._initWrapperElement();this._createUpperCanvas();this._initEventListeners();this.freeDrawingBrush=fabric.PencilBrush&&new fabric.PencilBrush(this);this.calcOffset();},_resetCurrentTransform:function(e){var t=this._currentTransform;t.target.set({scaleX:t.original.scaleX,scaleY:t.original.scaleY,left:t.original.left,top:t.original.top});if(this._shouldCenterTransform(e,t.target)){if(t.action==='rotate'){this._setOriginToCenter(t.target);}
else{if(t.originX!=='center'){if(t.originX==='right'){t.mouseXSign=-1;}
else{t.mouseXSign=1;}}
if(t.originY!=='center'){if(t.originY==='bottom'){t.mouseYSign=-1;}
else{t.mouseYSign=1;}}
t.originX='center';t.originY='center';}}
else{t.originX=t.original.originX;t.originY=t.original.originY;}},containsPoint:function(e,target){var pointer=this.getPointer(e,true),xy=this._normalizePointer(target,pointer);return(target.containsPoint(xy)||target._findTargetCorner(pointer));},_normalizePointer:function(object,pointer){var activeGroup=this.getActiveGroup(),x=pointer.x,y=pointer.y,isObjectInGroup=(activeGroup&&object.type!=='group'&&activeGroup.contains(object)),lt;if(isObjectInGroup){lt=new fabric.Point(activeGroup.left,activeGroup.top);lt=fabric.util.transformPoint(lt,this.viewportTransform,true);x-=lt.x;y-=lt.y;}
return{x:x,y:y};},isTargetTransparent:function(target,x,y){var hasBorders=target.hasBorders,transparentCorners=target.transparentCorners;target.hasBorders=target.transparentCorners=false;this._draw(this.contextCache,target);target.hasBorders=hasBorders;target.transparentCorners=transparentCorners;var isTransparent=fabric.util.isTransparent(this.contextCache,x,y,this.targetFindTolerance);this.clearContext(this.contextCache);return isTransparent;},_shouldClearSelection:function(e,target){var activeGroup=this.getActiveGroup(),activeObject=this.getActiveObject();return(!target||(target&&activeGroup&&!activeGroup.contains(target)&&activeGroup!==target&&!e.shiftKey)||(target&&!target.evented)||(target&&!target.selectable&&activeObject&&activeObject!==target));},_shouldCenterTransform:function(e,target){if(!target){return;}
var t=this._currentTransform,centerTransform;if(t.action==='scale'||t.action==='scaleX'||t.action==='scaleY'){centerTransform=this.centeredScaling||target.centeredScaling;}
else if(t.action==='rotate'){centerTransform=this.centeredRotation||target.centeredRotation;}
return centerTransform?!e.altKey:e.altKey;},_getOriginFromCorner:function(target,corner){var origin={x:target.originX,y:target.originY};if(corner==='ml'||corner==='tl'||corner==='bl'){origin.x='right';}
else if(corner==='mr'||corner==='tr'||corner==='br'){origin.x='left';}
if(corner==='tl'||corner==='mt'||corner==='tr'){origin.y='bottom';}
else if(corner==='bl'||corner==='mb'||corner==='br'){origin.y='top';}
return origin;},_getActionFromCorner:function(target,corner){var action='drag';if(corner){action=(corner==='ml'||corner==='mr')?'scaleX':(corner==='mt'||corner==='mb')?'scaleY':corner==='mtr'?'rotate':'scale';}
return action;},_setupCurrentTransform:function(e,target){if(!target){return;}
var pointer=this.getPointer(e),corner=target._findTargetCorner(this.getPointer(e,true)),action=this._getActionFromCorner(target,corner),origin=this._getOriginFromCorner(target,corner);this._currentTransform={target:target,action:action,scaleX:target.scaleX,scaleY:target.scaleY,offsetX:pointer.x- target.left,offsetY:pointer.y- target.top,originX:origin.x,originY:origin.y,ex:pointer.x,ey:pointer.y,left:target.left,top:target.top,theta:degreesToRadians(target.angle),width:target.width*target.scaleX,mouseXSign:1,mouseYSign:1};this._currentTransform.original={left:target.left,top:target.top,scaleX:target.scaleX,scaleY:target.scaleY,originX:origin.x,originY:origin.y};this._resetCurrentTransform(e);},_translateObject:function(x,y){var target=this._currentTransform.target;if(!target.get('lockMovementX')){target.set('left',x- this._currentTransform.offsetX);}
if(!target.get('lockMovementY')){target.set('top',y- this._currentTransform.offsetY);}},_scaleObject:function(x,y,by){var t=this._currentTransform,target=t.target,lockScalingX=target.get('lockScalingX'),lockScalingY=target.get('lockScalingY'),lockScalingFlip=target.get('lockScalingFlip');if(lockScalingX&&lockScalingY){return;}
var constraintPosition=target.translateToOriginPoint(target.getCenterPoint(),t.originX,t.originY),localMouse=target.toLocalPoint(new fabric.Point(x,y),t.originX,t.originY);this._setLocalMouse(localMouse,t);this._setObjectScale(localMouse,t,lockScalingX,lockScalingY,by,lockScalingFlip);target.setPositionByOrigin(constraintPosition,t.originX,t.originY);},_setObjectScale:function(localMouse,transform,lockScalingX,lockScalingY,by,lockScalingFlip){var target=transform.target,forbidScalingX=false,forbidScalingY=false,strokeWidth=target.stroke?target.strokeWidth:0;transform.newScaleX=localMouse.x/(target.width+ strokeWidth/2);transform.newScaleY=localMouse.y/(target.height+ strokeWidth/2);if(lockScalingFlip&&transform.newScaleX<=0&&transform.newScaleX<target.scaleX){forbidScalingX=true;}
if(lockScalingFlip&&transform.newScaleY<=0&&transform.newScaleY<target.scaleY){forbidScalingY=true;}
if(by==='equally'&&!lockScalingX&&!lockScalingY){forbidScalingX||forbidScalingY||this._scaleObjectEqually(localMouse,target,transform);}
else if(!by){forbidScalingX||lockScalingX||target.set('scaleX',transform.newScaleX);forbidScalingY||lockScalingY||target.set('scaleY',transform.newScaleY);}
else if(by==='x'&&!target.get('lockUniScaling')){forbidScalingX||lockScalingX||target.set('scaleX',transform.newScaleX);}
else if(by==='y'&&!target.get('lockUniScaling')){forbidScalingY||lockScalingY||target.set('scaleY',transform.newScaleY);}
forbidScalingX||forbidScalingY||this._flipObject(transform,by);},_scaleObjectEqually:function(localMouse,target,transform){var dist=localMouse.y+ localMouse.x,strokeWidth=target.stroke?target.strokeWidth:0,lastDist=(target.height+(strokeWidth/2))*transform.original.scaleY+
(target.width+(strokeWidth/2))*transform.original.scaleX;transform.newScaleX=transform.original.scaleX*dist/lastDist;transform.newScaleY=transform.original.scaleY*dist/lastDist;target.set('scaleX',transform.newScaleX);target.set('scaleY',transform.newScaleY);},_flipObject:function(transform,by){if(transform.newScaleX<0&&by!=='y'){if(transform.originX==='left'){transform.originX='right';}
else if(transform.originX==='right'){transform.originX='left';}}
if(transform.newScaleY<0&&by!=='x'){if(transform.originY==='top'){transform.originY='bottom';}
else if(transform.originY==='bottom'){transform.originY='top';}}},_setLocalMouse:function(localMouse,t){var target=t.target;if(t.originX==='right'){localMouse.x*=-1;}
else if(t.originX==='center'){localMouse.x*=t.mouseXSign*2;if(localMouse.x<0){t.mouseXSign=-t.mouseXSign;}}
if(t.originY==='bottom'){localMouse.y*=-1;}
else if(t.originY==='center'){localMouse.y*=t.mouseYSign*2;if(localMouse.y<0){t.mouseYSign=-t.mouseYSign;}}
if(abs(localMouse.x)>target.padding){if(localMouse.x<0){localMouse.x+=target.padding;}
else{localMouse.x-=target.padding;}}
else{localMouse.x=0;}
if(abs(localMouse.y)>target.padding){if(localMouse.y<0){localMouse.y+=target.padding;}
else{localMouse.y-=target.padding;}}
else{localMouse.y=0;}},_rotateObject:function(x,y){var t=this._currentTransform;if(t.target.get('lockRotation')){return;}
var lastAngle=atan2(t.ey- t.top,t.ex- t.left),curAngle=atan2(y- t.top,x- t.left),angle=radiansToDegrees(curAngle- lastAngle+ t.theta);if(angle<0){angle=360+ angle;}
t.target.angle=angle%360;},setCursor:function(value){this.upperCanvasEl.style.cursor=value;},_resetObjectTransform:function(target){target.scaleX=1;target.scaleY=1;target.setAngle(0);},_drawSelection:function(){var ctx=this.contextTop,groupSelector=this._groupSelector,left=groupSelector.left,top=groupSelector.top,aleft=abs(left),atop=abs(top);ctx.fillStyle=this.selectionColor;ctx.fillRect(groupSelector.ex-((left>0)?0:-left),groupSelector.ey-((top>0)?0:-top),aleft,atop);ctx.lineWidth=this.selectionLineWidth;ctx.strokeStyle=this.selectionBorderColor;if(this.selectionDashArray.length>1){var px=groupSelector.ex+ STROKE_OFFSET-((left>0)?0:aleft),py=groupSelector.ey+ STROKE_OFFSET-((top>0)?0:atop);ctx.beginPath();fabric.util.drawDashedLine(ctx,px,py,px+ aleft,py,this.selectionDashArray);fabric.util.drawDashedLine(ctx,px,py+ atop- 1,px+ aleft,py+ atop- 1,this.selectionDashArray);fabric.util.drawDashedLine(ctx,px,py,px,py+ atop,this.selectionDashArray);fabric.util.drawDashedLine(ctx,px+ aleft- 1,py,px+ aleft- 1,py+ atop,this.selectionDashArray);ctx.closePath();ctx.stroke();}
else{ctx.strokeRect(groupSelector.ex+ STROKE_OFFSET-((left>0)?0:aleft),groupSelector.ey+ STROKE_OFFSET-((top>0)?0:atop),aleft,atop);}},_isLastRenderedObject:function(e){return(this.controlsAboveOverlay&&this.lastRenderedObjectWithControlsAboveOverlay&&this.lastRenderedObjectWithControlsAboveOverlay.visible&&this.containsPoint(e,this.lastRenderedObjectWithControlsAboveOverlay)&&this.lastRenderedObjectWithControlsAboveOverlay._findTargetCorner(this.getPointer(e,true)));},findTarget:function(e,skipGroup){if(this.skipTargetFind){return;}
if(this._isLastRenderedObject(e)){return this.lastRenderedObjectWithControlsAboveOverlay;}
var activeGroup=this.getActiveGroup();if(activeGroup&&!skipGroup&&this.containsPoint(e,activeGroup)){return activeGroup;}
var target=this._searchPossibleTargets(e);this._fireOverOutEvents(target);return target;},_fireOverOutEvents:function(target){if(target){if(this._hoveredTarget!==target){this.fire('mouse:over',{target:target});target.fire('mouseover');if(this._hoveredTarget){this.fire('mouse:out',{target:this._hoveredTarget});this._hoveredTarget.fire('mouseout');}
this._hoveredTarget=target;}}
else if(this._hoveredTarget){this.fire('mouse:out',{target:this._hoveredTarget});this._hoveredTarget.fire('mouseout');this._hoveredTarget=null;}},_checkTarget:function(e,obj,pointer){if(obj&&obj.visible&&obj.evented&&this.containsPoint(e,obj)){if((this.perPixelTargetFind||obj.perPixelTargetFind)&&!obj.isEditing){var isTransparent=this.isTargetTransparent(obj,pointer.x,pointer.y);if(!isTransparent){return true;}}
else{return true;}}},_searchPossibleTargets:function(e){var target,pointer=this.getPointer(e,true),i=this._objects.length;while(i--){if(!this._objects[i].group&&this._checkTarget(e,this._objects[i],pointer)){this.relatedTarget=this._objects[i];target=this._objects[i];break;}}
return target;},getPointer:function(e,ignoreZoom,upperCanvasEl){if(!upperCanvasEl){upperCanvasEl=this.upperCanvasEl;}
var pointer=getPointer(e,upperCanvasEl),bounds=upperCanvasEl.getBoundingClientRect(),boundsWidth=bounds.width||0,boundsHeight=bounds.height||0,cssScale;if(!boundsWidth||!boundsHeight){if('top'in bounds&&'bottom'in bounds){boundsHeight=Math.abs(bounds.top- bounds.bottom);}
if('right'in bounds&&'left'in bounds){boundsWidth=Math.abs(bounds.right- bounds.left);}}
this.calcOffset();pointer.x=pointer.x- this._offset.left;pointer.y=pointer.y- this._offset.top;if(!ignoreZoom){pointer=fabric.util.transformPoint(pointer,fabric.util.invertTransform(this.viewportTransform));}
if(boundsWidth===0||boundsHeight===0){cssScale={width:1,height:1};}
else{cssScale={width:upperCanvasEl.width/boundsWidth,height:upperCanvasEl.height/boundsHeight};}
return{x:pointer.x*cssScale.width,y:pointer.y*cssScale.height};},_createUpperCanvas:function(){var lowerCanvasClass=this.lowerCanvasEl.className.replace(/\s*lower-canvas\s*/,'');this.upperCanvasEl=this._createCanvasElement();fabric.util.addClass(this.upperCanvasEl,'upper-canvas '+ lowerCanvasClass);this.wrapperEl.appendChild(this.upperCanvasEl);this._copyCanvasStyle(this.lowerCanvasEl,this.upperCanvasEl);this._applyCanvasStyle(this.upperCanvasEl);this.contextTop=this.upperCanvasEl.getContext('2d');},_createCacheCanvas:function(){this.cacheCanvasEl=this._createCanvasElement();this.cacheCanvasEl.setAttribute('width',this.width);this.cacheCanvasEl.setAttribute('height',this.height);this.contextCache=this.cacheCanvasEl.getContext('2d');},_initWrapperElement:function(){this.wrapperEl=fabric.util.wrapElement(this.lowerCanvasEl,'div',{'class':this.containerClass});fabric.util.setStyle(this.wrapperEl,{width:this.getWidth()+'px',height:this.getHeight()+'px',position:'relative'});fabric.util.makeElementUnselectable(this.wrapperEl);},_applyCanvasStyle:function(element){var width=this.getWidth()||element.width,height=this.getHeight()||element.height;fabric.util.setStyle(element,{position:'absolute',width:width+'px',height:height+'px',left:0,top:0});element.width=width;element.height=height;fabric.util.makeElementUnselectable(element);},_copyCanvasStyle:function(fromEl,toEl){toEl.style.cssText=fromEl.style.cssText;},getSelectionContext:function(){return this.contextTop;},getSelectionElement:function(){return this.upperCanvasEl;},_setActiveObject:function(object){if(this._activeObject){this._activeObject.set('active',false);}
this._activeObject=object;object.set('active',true);},setActiveObject:function(object,e){this._setActiveObject(object);this.renderAll();this.fire('object:selected',{target:object,e:e});object.fire('selected',{e:e});return this;},getActiveObject:function(){return this._activeObject;},_discardActiveObject:function(){if(this._activeObject){this._activeObject.set('active',false);}
this._activeObject=null;},discardActiveObject:function(e){this._discardActiveObject();this.renderAll();this.fire('selection:cleared',{e:e});return this;},_setActiveGroup:function(group){this._activeGroup=group;if(group){group.set('active',true);}},setActiveGroup:function(group,e){this._setActiveGroup(group);if(group){this.fire('object:selected',{target:group,e:e});group.fire('selected',{e:e});}
return this;},getActiveGroup:function(){return this._activeGroup;},_discardActiveGroup:function(){var g=this.getActiveGroup();if(g){g.destroy();}
this.setActiveGroup(null);},discardActiveGroup:function(e){this._discardActiveGroup();this.fire('selection:cleared',{e:e});return this;},deactivateAll:function(){var allObjects=this.getObjects(),i=0,len=allObjects.length;for(;i<len;i++){allObjects[i].set('active',false);}
this._discardActiveGroup();this._discardActiveObject();return this;},deactivateAllWithDispatch:function(e){var activeObject=this.getActiveGroup()||this.getActiveObject();if(activeObject){this.fire('before:selection:cleared',{target:activeObject,e:e});}
this.deactivateAll();if(activeObject){this.fire('selection:cleared',{e:e});}
return this;},drawControls:function(ctx){var activeGroup=this.getActiveGroup();if(activeGroup){this._drawGroupControls(ctx,activeGroup);}
else{this._drawObjectsControls(ctx);}},_drawGroupControls:function(ctx,activeGroup){activeGroup._renderControls(ctx);},_drawObjectsControls:function(ctx){for(var i=0,len=this._objects.length;i<len;++i){if(!this._objects[i]||!this._objects[i].active){continue;}
this._objects[i]._renderControls(ctx);this.lastRenderedObjectWithControlsAboveOverlay=this._objects[i];}}});for(var prop in fabric.StaticCanvas){if(prop!=='prototype'){fabric.Canvas[prop]=fabric.StaticCanvas[prop];}}
if(fabric.isTouchSupported){fabric.Canvas.prototype._setCursorFromEvent=function(){};}
fabric.Element=fabric.Canvas;})();(function(){var cursorOffset={mt:0,tr:1,mr:2,br:3,mb:4,bl:5,ml:6,tl:7},addListener=fabric.util.addListener,removeListener=fabric.util.removeListener;fabric.util.object.extend(fabric.Canvas.prototype,{cursorMap:['n-resize','ne-resize','e-resize','se-resize','s-resize','sw-resize','w-resize','nw-resize'],_initEventListeners:function(){this._bindEvents();addListener(fabric.window,'resize',this._onResize);addListener(this.upperCanvasEl,'mousedown',this._onMouseDown);addListener(this.upperCanvasEl,'mousemove',this._onMouseMove);addListener(this.upperCanvasEl,'mousewheel',this._onMouseWheel);addListener(this.upperCanvasEl,'touchstart',this._onMouseDown);addListener(this.upperCanvasEl,'touchmove',this._onMouseMove);if(typeof eventjs!=='undefined'&&'add'in eventjs){eventjs.add(this.upperCanvasEl,'gesture',this._onGesture);eventjs.add(this.upperCanvasEl,'drag',this._onDrag);eventjs.add(this.upperCanvasEl,'orientation',this._onOrientationChange);eventjs.add(this.upperCanvasEl,'shake',this._onShake);eventjs.add(this.upperCanvasEl,'longpress',this._onLongPress);}},_bindEvents:function(){this._onMouseDown=this._onMouseDown.bind(this);this._onMouseMove=this._onMouseMove.bind(this);this._onMouseUp=this._onMouseUp.bind(this);this._onResize=this._onResize.bind(this);this._onGesture=this._onGesture.bind(this);this._onDrag=this._onDrag.bind(this);this._onShake=this._onShake.bind(this);this._onLongPress=this._onLongPress.bind(this);this._onOrientationChange=this._onOrientationChange.bind(this);this._onMouseWheel=this._onMouseWheel.bind(this);},removeListeners:function(){removeListener(fabric.window,'resize',this._onResize);removeListener(this.upperCanvasEl,'mousedown',this._onMouseDown);removeListener(this.upperCanvasEl,'mousemove',this._onMouseMove);removeListener(this.upperCanvasEl,'mousewheel',this._onMouseWheel);removeListener(this.upperCanvasEl,'touchstart',this._onMouseDown);removeListener(this.upperCanvasEl,'touchmove',this._onMouseMove);if(typeof eventjs!=='undefined'&&'remove'in eventjs){eventjs.remove(this.upperCanvasEl,'gesture',this._onGesture);eventjs.remove(this.upperCanvasEl,'drag',this._onDrag);eventjs.remove(this.upperCanvasEl,'orientation',this._onOrientationChange);eventjs.remove(this.upperCanvasEl,'shake',this._onShake);eventjs.remove(this.upperCanvasEl,'longpress',this._onLongPress);}},_onGesture:function(e,self){this.__onTransformGesture&&this.__onTransformGesture(e,self);},_onDrag:function(e,self){this.__onDrag&&this.__onDrag(e,self);},_onMouseWheel:function(e,self){this.__onMouseWheel&&this.__onMouseWheel(e,self);},_onOrientationChange:function(e,self){this.__onOrientationChange&&this.__onOrientationChange(e,self);},_onShake:function(e,self){this.__onShake&&this.__onShake(e,self);},_onLongPress:function(e,self){this.__onLongPress&&this.__onLongPress(e,self);},_onMouseDown:function(e){this.__onMouseDown(e);addListener(fabric.document,'touchend',this._onMouseUp);addListener(fabric.document,'touchmove',this._onMouseMove);removeListener(this.upperCanvasEl,'mousemove',this._onMouseMove);removeListener(this.upperCanvasEl,'touchmove',this._onMouseMove);if(e.type==='touchstart'){removeListener(this.upperCanvasEl,'mousedown',this._onMouseDown);}
else{addListener(fabric.document,'mouseup',this._onMouseUp);addListener(fabric.document,'mousemove',this._onMouseMove);}},_onMouseUp:function(e){this.__onMouseUp(e);removeListener(fabric.document,'mouseup',this._onMouseUp);removeListener(fabric.document,'touchend',this._onMouseUp);removeListener(fabric.document,'mousemove',this._onMouseMove);removeListener(fabric.document,'touchmove',this._onMouseMove);addListener(this.upperCanvasEl,'mousemove',this._onMouseMove);addListener(this.upperCanvasEl,'touchmove',this._onMouseMove);if(e.type==='touchend'){var _this=this;setTimeout(function(){addListener(_this.upperCanvasEl,'mousedown',_this._onMouseDown);},400);}},_onMouseMove:function(e){!this.allowTouchScrolling&&e.preventDefault&&e.preventDefault();this.__onMouseMove(e);},_onResize:function(){this.calcOffset();},_shouldRender:function(target,pointer){var activeObject=this.getActiveGroup()||this.getActiveObject();return!!((target&&(target.isMoving||target!==activeObject))||(!target&&!!activeObject)||(!target&&!activeObject&&!this._groupSelector)||(pointer&&this._previousPointer&&this.selection&&(pointer.x!==this._previousPointer.x||pointer.y!==this._previousPointer.y)));},__onMouseUp:function(e){var target;if(this.isDrawingMode&&this._isCurrentlyDrawing){this._onMouseUpInDrawingMode(e);return;}
if(this._currentTransform){this._finalizeCurrentTransform();target=this._currentTransform.target;}
else{target=this.findTarget(e,true);}
var shouldRender=this._shouldRender(target,this.getPointer(e));this._maybeGroupObjects(e);if(target){target.isMoving=false;}
shouldRender&&this.renderAll();this._handleCursorAndEvent(e,target);},_handleCursorAndEvent:function(e,target){this._setCursorFromEvent(e,target);var _this=this;setTimeout(function(){_this._setCursorFromEvent(e,target);},50);this.fire('mouse:up',{target:target,e:e});target&&target.fire('mouseup',{e:e});},_finalizeCurrentTransform:function(){var transform=this._currentTransform,target=transform.target;if(target._scaling){target._scaling=false;}
target.setCoords();if(this.stateful&&target.hasStateChanged()){this.fire('object:modified',{target:target});target.fire('modified');}
this._restoreOriginXY(target);},_restoreOriginXY:function(target){if(this._previousOriginX&&this._previousOriginY){var originPoint=target.translateToOriginPoint(target.getCenterPoint(),this._previousOriginX,this._previousOriginY);target.originX=this._previousOriginX;target.originY=this._previousOriginY;target.left=originPoint.x;target.top=originPoint.y;this._previousOriginX=null;this._previousOriginY=null;}},_onMouseDownInDrawingMode:function(e){this._isCurrentlyDrawing=true;this.discardActiveObject(e).renderAll();if(this.clipTo){fabric.util.clipContext(this,this.contextTop);}
var ivt=fabric.util.invertTransform(this.viewportTransform),pointer=fabric.util.transformPoint(this.getPointer(e,true),ivt);this.freeDrawingBrush.onMouseDown(pointer);this.fire('mouse:down',{e:e});var target=this.findTarget(e);if(typeof target!=='undefined'){target.fire('mousedown',{e:e,target:target});}},_onMouseMoveInDrawingMode:function(e){if(this._isCurrentlyDrawing){var ivt=fabric.util.invertTransform(this.viewportTransform),pointer=fabric.util.transformPoint(this.getPointer(e,true),ivt);this.freeDrawingBrush.onMouseMove(pointer);}
this.setCursor(this.freeDrawingCursor);this.fire('mouse:move',{e:e});var target=this.findTarget(e);if(typeof target!=='undefined'){target.fire('mousemove',{e:e,target:target});}},_onMouseUpInDrawingMode:function(e){this._isCurrentlyDrawing=false;if(this.clipTo){this.contextTop.restore();}
this.freeDrawingBrush.onMouseUp();this.fire('mouse:up',{e:e});var target=this.findTarget(e);if(typeof target!=='undefined'){target.fire('mouseup',{e:e,target:target});}},__onMouseDown:function(e){var isLeftClick='which'in e?e.which===1:e.button===1;if(!isLeftClick&&!fabric.isTouchSupported){return;}
if(this.isDrawingMode){this._onMouseDownInDrawingMode(e);return;}
if(this._currentTransform){return;}
var target=this.findTarget(e),pointer=this.getPointer(e,true);this._previousPointer=pointer;var shouldRender=this._shouldRender(target,pointer),shouldGroup=this._shouldGroup(e,target);if(this._shouldClearSelection(e,target)){this._clearSelection(e,target,pointer);}
else if(shouldGroup){this._handleGrouping(e,target);target=this.getActiveGroup();}
if(target&&target.selectable&&!shouldGroup){this._beforeTransform(e,target);this._setupCurrentTransform(e,target);}
shouldRender&&this.renderAll();this.fire('mouse:down',{target:target,e:e});target&&target.fire('mousedown',{e:e});},_beforeTransform:function(e,target){this.stateful&&target.saveState();if(target._findTargetCorner(this.getPointer(e))){this.onBeforeScaleRotate(target);}
if(target!==this.getActiveGroup()&&target!==this.getActiveObject()){this.deactivateAll();this.setActiveObject(target,e);}},_clearSelection:function(e,target,pointer){this.deactivateAllWithDispatch(e);if(target&&target.selectable){this.setActiveObject(target,e);}
else if(this.selection){this._groupSelector={ex:pointer.x,ey:pointer.y,top:0,left:0};}},_setOriginToCenter:function(target){this._previousOriginX=this._currentTransform.target.originX;this._previousOriginY=this._currentTransform.target.originY;var center=target.getCenterPoint();target.originX='center';target.originY='center';target.left=center.x;target.top=center.y;this._currentTransform.left=target.left;this._currentTransform.top=target.top;},_setCenterToOrigin:function(target){var originPoint=target.translateToOriginPoint(target.getCenterPoint(),this._previousOriginX,this._previousOriginY);target.originX=this._previousOriginX;target.originY=this._previousOriginY;target.left=originPoint.x;target.top=originPoint.y;this._previousOriginX=null;this._previousOriginY=null;},__onMouseMove:function(e){var target,pointer;if(this.isDrawingMode){this._onMouseMoveInDrawingMode(e);return;}
if(typeof e.touches!=='undefined'&&e.touches.length>1){return;}
var groupSelector=this._groupSelector;if(groupSelector){pointer=this.getPointer(e,true);groupSelector.left=pointer.x- groupSelector.ex;groupSelector.top=pointer.y- groupSelector.ey;this.renderTop();}
else if(!this._currentTransform){target=this.findTarget(e);if(!target||target&&!target.selectable){this.setCursor(this.defaultCursor);}
else{this._setCursorFromEvent(e,target);}}
else{this._transformObject(e);}
this.fire('mouse:move',{target:target,e:e});target&&target.fire('mousemove',{e:e});},_transformObject:function(e){var pointer=this.getPointer(e),transform=this._currentTransform;transform.reset=false,transform.target.isMoving=true;this._beforeScaleTransform(e,transform);this._performTransformAction(e,transform,pointer);this.renderAll();},_performTransformAction:function(e,transform,pointer){var x=pointer.x,y=pointer.y,target=transform.target,action=transform.action;if(action==='rotate'){this._rotateObject(x,y);this._fire('rotating',target,e);}
else if(action==='scale'){this._onScale(e,transform,x,y);this._fire('scaling',target,e);}
else if(action==='scaleX'){this._scaleObject(x,y,'x');this._fire('scaling',target,e);}
else if(action==='scaleY'){this._scaleObject(x,y,'y');this._fire('scaling',target,e);}
else{this._translateObject(x,y);this._fire('moving',target,e);this.setCursor(this.moveCursor);}},_fire:function(eventName,target,e){this.fire('object:'+ eventName,{target:target,e:e});target.fire(eventName,{e:e});},_beforeScaleTransform:function(e,transform){if(transform.action==='scale'||transform.action==='scaleX'||transform.action==='scaleY'){var centerTransform=this._shouldCenterTransform(e,transform.target);if((centerTransform&&(transform.originX!=='center'||transform.originY!=='center'))||(!centerTransform&&transform.originX==='center'&&transform.originY==='center')){this._resetCurrentTransform(e);transform.reset=true;}}},_onScale:function(e,transform,x,y){if((e.shiftKey||this.uniScaleTransform)&&!transform.target.get('lockUniScaling')){transform.currentAction='scale';this._scaleObject(x,y);}
else{if(!transform.reset&&transform.currentAction==='scale'){this._resetCurrentTransform(e,transform.target);}
transform.currentAction='scaleEqually';this._scaleObject(x,y,'equally');}},_setCursorFromEvent:function(e,target){if(!target||!target.selectable){this.setCursor(this.defaultCursor);return false;}
else{var activeGroup=this.getActiveGroup(),corner=target._findTargetCorner&&(!activeGroup||!activeGroup.contains(target))&&target._findTargetCorner(this.getPointer(e,true));if(!corner){this.setCursor(target.hoverCursor||this.hoverCursor);}
else{this._setCornerCursor(corner,target);}}
return true;},_setCornerCursor:function(corner,target){if(corner in cursorOffset){this.setCursor(this._getRotatedCornerCursor(corner,target));}
else if(corner==='mtr'&&target.hasRotatingPoint){this.setCursor(this.rotationCursor);}
else{this.setCursor(this.defaultCursor);return false;}},_getRotatedCornerCursor:function(corner,target){var n=Math.round((target.getAngle()%360)/ 45);
if(n<0){n+=8;}
n+=cursorOffset[corner];n%=8;return this.cursorMap[n];}});})();(function(){var min=Math.min,max=Math.max;fabric.util.object.extend(fabric.Canvas.prototype,{_shouldGroup:function(e,target){var activeObject=this.getActiveObject();return e.shiftKey&&(this.getActiveGroup()||(activeObject&&activeObject!==target))&&this.selection;},_handleGrouping:function(e,target){if(target===this.getActiveGroup()){target=this.findTarget(e,true);if(!target||target.isType('group')){return;}}
if(this.getActiveGroup()){this._updateActiveGroup(target,e);}
else{this._createActiveGroup(target,e);}
if(this._activeGroup){this._activeGroup.saveCoords();}},_updateActiveGroup:function(target,e){var activeGroup=this.getActiveGroup();if(activeGroup.contains(target)){activeGroup.removeWithUpdate(target);this._resetObjectTransform(activeGroup);target.set('active',false);if(activeGroup.size()===1){this.discardActiveGroup(e);this.setActiveObject(activeGroup.item(0));return;}}
else{activeGroup.addWithUpdate(target);this._resetObjectTransform(activeGroup);}
this.fire('selection:created',{target:activeGroup,e:e});activeGroup.set('active',true);},_createActiveGroup:function(target,e){if(this._activeObject&&target!==this._activeObject){var group=this._createGroup(target);group.addWithUpdate();this.setActiveGroup(group);this._activeObject=null;this.fire('selection:created',{target:group,e:e});}
target.set('active',true);},_createGroup:function(target){var objects=this.getObjects(),isActiveLower=objects.indexOf(this._activeObject)<objects.indexOf(target),groupObjects=isActiveLower?[this._activeObject,target]:[target,this._activeObject];return new fabric.Group(groupObjects,{canvas:this});},_groupSelectedObjects:function(e){var group=this._collectObjects();if(group.length===1){this.setActiveObject(group[0],e);}
else if(group.length>1){group=new fabric.Group(group.reverse(),{canvas:this});group.addWithUpdate();this.setActiveGroup(group,e);group.saveCoords();this.fire('selection:created',{target:group});this.renderAll();}},_collectObjects:function(){var group=[],currentObject,x1=this._groupSelector.ex,y1=this._groupSelector.ey,x2=x1+ this._groupSelector.left,y2=y1+ this._groupSelector.top,selectionX1Y1=new fabric.Point(min(x1,x2),min(y1,y2)),selectionX2Y2=new fabric.Point(max(x1,x2),max(y1,y2)),isClick=x1===x2&&y1===y2;for(var i=this._objects.length;i--;){currentObject=this._objects[i];if(!currentObject||!currentObject.selectable||!currentObject.visible){continue;}
if(currentObject.intersectsWithRect(selectionX1Y1,selectionX2Y2)||currentObject.isContainedWithinRect(selectionX1Y1,selectionX2Y2)||currentObject.containsPoint(selectionX1Y1)||currentObject.containsPoint(selectionX2Y2)){currentObject.set('active',true);group.push(currentObject);if(isClick){break;}}}
return group;},_maybeGroupObjects:function(e){if(this.selection&&this._groupSelector){this._groupSelectedObjects(e);}
var activeGroup=this.getActiveGroup();if(activeGroup){activeGroup.setObjectsCoords().setCoords();activeGroup.isMoving=false;this.setCursor(this.defaultCursor);}
this._groupSelector=null;this._currentTransform=null;}});})();fabric.util.object.extend(fabric.StaticCanvas.prototype,{toDataURL:function(options){options||(options={});var format=options.format||'png',quality=options.quality||1,multiplier=options.multiplier||1,cropping={left:options.left,top:options.top,width:options.width,height:options.height};if(multiplier!==1){return this.__toDataURLWithMultiplier(format,quality,cropping,multiplier);}
else{return this.__toDataURL(format,quality,cropping);}},__toDataURL:function(format,quality,cropping){this.renderAll(true);var canvasEl=this.upperCanvasEl||this.lowerCanvasEl,croppedCanvasEl=this.__getCroppedCanvas(canvasEl,cropping);if(format==='jpg'){format='jpeg';}
var data=(fabric.StaticCanvas.supports('toDataURLWithQuality'))?(croppedCanvasEl||canvasEl).toDataURL('image/'+ format,quality):(croppedCanvasEl||canvasEl).toDataURL('image/'+ format);this.contextTop&&this.clearContext(this.contextTop);this.renderAll();if(croppedCanvasEl){croppedCanvasEl=null;}
return data;},__getCroppedCanvas:function(canvasEl,cropping){var croppedCanvasEl,croppedCtx,shouldCrop='left'in cropping||'top'in cropping||'width'in cropping||'height'in cropping;if(shouldCrop){croppedCanvasEl=fabric.util.createCanvasElement();croppedCtx=croppedCanvasEl.getContext('2d');croppedCanvasEl.width=cropping.width||this.width;croppedCanvasEl.height=cropping.height||this.height;croppedCtx.drawImage(canvasEl,-cropping.left||0,-cropping.top||0);}
return croppedCanvasEl;},__toDataURLWithMultiplier:function(format,quality,cropping,multiplier){var origWidth=this.getWidth(),origHeight=this.getHeight(),scaledWidth=origWidth*multiplier,scaledHeight=origHeight*multiplier,activeObject=this.getActiveObject(),activeGroup=this.getActiveGroup(),ctx=this.contextTop||this.contextContainer;if(multiplier>1){this.setWidth(scaledWidth).setHeight(scaledHeight);}
ctx.scale(multiplier,multiplier);if(cropping.left){cropping.left*=multiplier;}
if(cropping.top){cropping.top*=multiplier;}
if(cropping.width){cropping.width*=multiplier;}
else if(multiplier<1){cropping.width=scaledWidth;}
if(cropping.height){cropping.height*=multiplier;}
else if(multiplier<1){cropping.height=scaledHeight;}
if(activeGroup){this._tempRemoveBordersControlsFromGroup(activeGroup);}
else if(activeObject&&this.deactivateAll){this.deactivateAll();}
this.renderAll(true);var data=this.__toDataURL(format,quality,cropping);this.width=origWidth;this.height=origHeight;ctx.scale(1/multiplier,1/multiplier);this.setWidth(origWidth).setHeight(origHeight);if(activeGroup){this._restoreBordersControlsOnGroup(activeGroup);}
else if(activeObject&&this.setActiveObject){this.setActiveObject(activeObject);}
this.contextTop&&this.clearContext(this.contextTop);this.renderAll();return data;},toDataURLWithMultiplier:function(format,multiplier,quality){return this.toDataURL({format:format,multiplier:multiplier,quality:quality});},_tempRemoveBordersControlsFromGroup:function(group){group.origHasControls=group.hasControls;group.origBorderColor=group.borderColor;group.hasControls=true;group.borderColor='rgba(0,0,0,0)';group.forEachObject(function(o){o.origBorderColor=o.borderColor;o.borderColor='rgba(0,0,0,0)';});},_restoreBordersControlsOnGroup:function(group){group.hideControls=group.origHideControls;group.borderColor=group.origBorderColor;group.forEachObject(function(o){o.borderColor=o.origBorderColor;delete o.origBorderColor;});}});fabric.util.object.extend(fabric.StaticCanvas.prototype,{loadFromDatalessJSON:function(json,callback,reviver){return this.loadFromJSON(json,callback,reviver);},loadFromJSON:function(json,callback,reviver){if(!json){return;}
var serialized=(typeof json==='string')?JSON.parse(json):json;this.clear();var _this=this;this._enlivenObjects(serialized.objects,function(){_this._setBgOverlay(serialized,callback);},reviver);return this;},_setBgOverlay:function(serialized,callback){var _this=this,loaded={backgroundColor:false,overlayColor:false,backgroundImage:false,overlayImage:false};if(!serialized.backgroundImage&&!serialized.overlayImage&&!serialized.background&&!serialized.overlay){callback&&callback();return;}
var cbIfLoaded=function(){if(loaded.backgroundImage&&loaded.overlayImage&&loaded.backgroundColor&&loaded.overlayColor){_this.renderAll();callback&&callback();}};this.__setBgOverlay('backgroundImage',serialized.backgroundImage,loaded,cbIfLoaded);this.__setBgOverlay('overlayImage',serialized.overlayImage,loaded,cbIfLoaded);this.__setBgOverlay('backgroundColor',serialized.background,loaded,cbIfLoaded);this.__setBgOverlay('overlayColor',serialized.overlay,loaded,cbIfLoaded);cbIfLoaded();},__setBgOverlay:function(property,value,loaded,callback){var _this=this;if(!value){loaded[property]=true;return;}
if(property==='backgroundImage'||property==='overlayImage'){fabric.Image.fromObject(value,function(img){_this[property]=img;loaded[property]=true;callback&&callback();});}
else{this['set'+ fabric.util.string.capitalize(property,true)](value,function(){loaded[property]=true;callback&&callback();});}},_enlivenObjects:function(objects,callback,reviver){var _this=this;if(!objects||objects.length===0){callback&&callback();return;}
var renderOnAddRemove=this.renderOnAddRemove;this.renderOnAddRemove=false;fabric.util.enlivenObjects(objects,function(enlivenedObjects){enlivenedObjects.forEach(function(obj,index){_this.insertAt(obj,index,true);});_this.renderOnAddRemove=renderOnAddRemove;callback&&callback();},null,reviver);},_toDataURL:function(format,callback){this.clone(function(clone){callback(clone.toDataURL(format));});},_toDataURLWithMultiplier:function(format,multiplier,callback){this.clone(function(clone){callback(clone.toDataURLWithMultiplier(format,multiplier));});},clone:function(callback,properties){var data=JSON.stringify(this.toJSON(properties));this.cloneWithoutData(function(clone){clone.loadFromJSON(data,function(){callback&&callback(clone);});});},cloneWithoutData:function(callback){var el=fabric.document.createElement('canvas');el.width=this.getWidth();el.height=this.getHeight();var clone=new fabric.Canvas(el);clone.clipTo=this.clipTo;if(this.backgroundImage){clone.setBackgroundImage(this.backgroundImage.src,function(){clone.renderAll();callback&&callback(clone);});clone.backgroundImageOpacity=this.backgroundImageOpacity;clone.backgroundImageStretch=this.backgroundImageStretch;}
else{callback&&callback(clone);}}});(function(global){'use strict';var fabric=global.fabric||(global.fabric={}),extend=fabric.util.object.extend,toFixed=fabric.util.toFixed,capitalize=fabric.util.string.capitalize,degreesToRadians=fabric.util.degreesToRadians,supportsLineDash=fabric.StaticCanvas.supports('setLineDash');if(fabric.Object){return;}
fabric.Object=fabric.util.createClass({type:'object',originX:'left',originY:'top',top:0,left:0,width:0,height:0,scaleX:1,scaleY:1,flipX:false,flipY:false,opacity:1,angle:0,cornerSize:12,transparentCorners:true,hoverCursor:null,padding:0,borderColor:'rgba(102,153,255,0.75)',cornerColor:'rgba(102,153,255,0.5)',centeredScaling:false,centeredRotation:true,fill:'rgb(0,0,0)',fillRule:'nonzero',globalCompositeOperation:'source-over',backgroundColor:'',stroke:null,strokeWidth:1,strokeDashArray:null,strokeLineCap:'butt',strokeLineJoin:'miter',strokeMiterLimit:10,shadow:null,borderOpacityWhenMoving:0.4,borderScaleFactor:1,transformMatrix:null,minScaleLimit:0.01,selectable:true,evented:true,visible:true,hasControls:true,hasBorders:true,hasRotatingPoint:true,rotatingPointOffset:40,perPixelTargetFind:false,includeDefaultValues:true,clipTo:null,lockMovementX:false,lockMovementY:false,lockRotation:false,lockScalingX:false,lockScalingY:false,lockUniScaling:false,lockScalingFlip:false,stateProperties:('top left width height scaleX scaleY flipX flipY originX originY transformMatrix '+'stroke strokeWidth strokeDashArray strokeLineCap strokeLineJoin strokeMiterLimit '+'angle opacity fill fillRule globalCompositeOperation shadow clipTo visible backgroundColor').split(' '),initialize:function(options){if(options){this.setOptions(options);}},_initGradient:function(options){if(options.fill&&options.fill.colorStops&&!(options.fill instanceof fabric.Gradient)){this.set('fill',new fabric.Gradient(options.fill));}},_initPattern:function(options){if(options.fill&&options.fill.source&&!(options.fill instanceof fabric.Pattern)){this.set('fill',new fabric.Pattern(options.fill));}
if(options.stroke&&options.stroke.source&&!(options.stroke instanceof fabric.Pattern)){this.set('stroke',new fabric.Pattern(options.stroke));}},_initClipping:function(options){if(!options.clipTo||typeof options.clipTo!=='string'){return;}
var functionBody=fabric.util.getFunctionBody(options.clipTo);if(typeof functionBody!=='undefined'){this.clipTo=new Function('ctx',functionBody);}},setOptions:function(options){for(var prop in options){this.set(prop,options[prop]);}
this._initGradient(options);this._initPattern(options);this._initClipping(options);},transform:function(ctx,fromLeft){var center=fromLeft?this._getLeftTopCoords():this.getCenterPoint();ctx.translate(center.x,center.y);ctx.rotate(degreesToRadians(this.angle));ctx.scale(this.scaleX*(this.flipX?-1:1),this.scaleY*(this.flipY?-1:1));},toObject:function(propertiesToInclude){var NUM_FRACTION_DIGITS=fabric.Object.NUM_FRACTION_DIGITS,object={type:this.type,originX:this.originX,originY:this.originY,left:toFixed(this.left,NUM_FRACTION_DIGITS),top:toFixed(this.top,NUM_FRACTION_DIGITS),width:toFixed(this.width,NUM_FRACTION_DIGITS),height:toFixed(this.height,NUM_FRACTION_DIGITS),fill:(this.fill&&this.fill.toObject)?this.fill.toObject():this.fill,stroke:(this.stroke&&this.stroke.toObject)?this.stroke.toObject():this.stroke,strokeWidth:toFixed(this.strokeWidth,NUM_FRACTION_DIGITS),strokeDashArray:this.strokeDashArray,strokeLineCap:this.strokeLineCap,strokeLineJoin:this.strokeLineJoin,strokeMiterLimit:toFixed(this.strokeMiterLimit,NUM_FRACTION_DIGITS),scaleX:toFixed(this.scaleX,NUM_FRACTION_DIGITS),scaleY:toFixed(this.scaleY,NUM_FRACTION_DIGITS),angle:toFixed(this.getAngle(),NUM_FRACTION_DIGITS),flipX:this.flipX,flipY:this.flipY,opacity:toFixed(this.opacity,NUM_FRACTION_DIGITS),shadow:(this.shadow&&this.shadow.toObject)?this.shadow.toObject():this.shadow,visible:this.visible,clipTo:this.clipTo&&String(this.clipTo),backgroundColor:this.backgroundColor,fillRule:this.fillRule,globalCompositeOperation:this.globalCompositeOperation};if(!this.includeDefaultValues){object=this._removeDefaultValues(object);}
fabric.util.populateWithProperties(this,object,propertiesToInclude);return object;},toDatalessObject:function(propertiesToInclude){return this.toObject(propertiesToInclude);},_removeDefaultValues:function(object){var prototype=fabric.util.getKlass(object.type).prototype,stateProperties=prototype.stateProperties;stateProperties.forEach(function(prop){if(object[prop]===prototype[prop]){delete object[prop];}});return object;},toString:function(){return'#<fabric.'+ capitalize(this.type)+'>';},get:function(property){return this[property];},_setObject:function(obj){for(var prop in obj){this._set(prop,obj[prop]);}},set:function(key,value){if(typeof key==='object'){this._setObject(key);}
else{if(typeof value==='function'&&key!=='clipTo'){this._set(key,value(this.get(key)));}
else{this._set(key,value);}}
return this;},_set:function(key,value){var shouldConstrainValue=(key==='scaleX'||key==='scaleY');if(shouldConstrainValue){value=this._constrainScale(value);}
if(key==='scaleX'&&value<0){this.flipX=!this.flipX;value*=-1;}
else if(key==='scaleY'&&value<0){this.flipY=!this.flipY;value*=-1;}
else if(key==='width'||key==='height'){this.minScaleLimit=toFixed(Math.min(0.1,1/Math.max(this.width,this.height)),2);}
else if(key==='shadow'&&value&&!(value instanceof fabric.Shadow)){value=new fabric.Shadow(value);}
this[key]=value;return this;},toggle:function(property){var value=this.get(property);if(typeof value==='boolean'){this.set(property,!value);}
return this;},setSourcePath:function(value){this.sourcePath=value;return this;},getViewportTransform:function(){if(this.canvas&&this.canvas.viewportTransform){return this.canvas.viewportTransform;}
return[1,0,0,1,0,0];},render:function(ctx,noTransform){if((this.width===0&&this.height===0)||!this.visible){return;}
ctx.save();this._setupCompositeOperation(ctx);if(!noTransform){this.transform(ctx);}
this._setStrokeStyles(ctx);this._setFillStyles(ctx);if(this.transformMatrix){ctx.transform.apply(ctx,this.transformMatrix);}
this._setOpacity(ctx);this._setShadow(ctx);this.clipTo&&fabric.util.clipContext(this,ctx);this._render(ctx,noTransform);this.clipTo&&ctx.restore();this._removeShadow(ctx);this._restoreCompositeOperation(ctx);ctx.restore();},_setOpacity:function(ctx){if(this.group){this.group._setOpacity(ctx);}
ctx.globalAlpha*=this.opacity;},_setStrokeStyles:function(ctx){if(this.stroke){ctx.lineWidth=this.strokeWidth;ctx.lineCap=this.strokeLineCap;ctx.lineJoin=this.strokeLineJoin;ctx.miterLimit=this.strokeMiterLimit;ctx.strokeStyle=this.stroke.toLive?this.stroke.toLive(ctx,this):this.stroke;}},_setFillStyles:function(ctx){if(this.fill){ctx.fillStyle=this.fill.toLive?this.fill.toLive(ctx,this):this.fill;}},_renderControls:function(ctx,noTransform){if(!this.active||noTransform){return;}
var vpt=this.getViewportTransform();ctx.save();var center;if(this.group){center=fabric.util.transformPoint(this.group.getCenterPoint(),vpt);ctx.translate(center.x,center.y);ctx.rotate(degreesToRadians(this.group.angle));}
center=fabric.util.transformPoint(this.getCenterPoint(),vpt,null!=this.group);if(this.group){center.x*=this.group.scaleX;center.y*=this.group.scaleY;}
ctx.translate(center.x,center.y);ctx.rotate(degreesToRadians(this.angle));this.drawBorders(ctx);this.drawControls(ctx);ctx.restore();},_setShadow:function(ctx){if(!this.shadow){return;}
var multX=(this.canvas&&this.canvas.viewportTransform[0])||1,multY=(this.canvas&&this.canvas.viewportTransform[3])||1;ctx.shadowColor=this.shadow.color;ctx.shadowBlur=this.shadow.blur*(multX+ multY)*(this.scaleX+ this.scaleY)/ 4;
ctx.shadowOffsetX=this.shadow.offsetX*multX*this.scaleX;ctx.shadowOffsetY=this.shadow.offsetY*multY*this.scaleY;},_removeShadow:function(ctx){if(!this.shadow){return;}
ctx.shadowColor='';ctx.shadowBlur=ctx.shadowOffsetX=ctx.shadowOffsetY=0;},_renderFill:function(ctx){if(!this.fill){return;}
ctx.save();if(this.fill.gradientTransform){var g=this.fill.gradientTransform;ctx.transform.apply(ctx,g);}
if(this.fill.toLive){ctx.translate(-this.width/2+ this.fill.offsetX||0,-this.height/2+ this.fill.offsetY||0);}
if(this.fillRule==='evenodd'){ctx.fill('evenodd');}
else{ctx.fill();}
ctx.restore();if(this.shadow&&!this.shadow.affectStroke){this._removeShadow(ctx);}},_renderStroke:function(ctx){if(!this.stroke||this.strokeWidth===0){return;}
ctx.save();if(this.strokeDashArray){if(1&this.strokeDashArray.length){this.strokeDashArray.push.apply(this.strokeDashArray,this.strokeDashArray);}
if(supportsLineDash){ctx.setLineDash(this.strokeDashArray);this._stroke&&this._stroke(ctx);}
else{this._renderDashedStroke&&this._renderDashedStroke(ctx);}
ctx.stroke();}
else{if(this.stroke.gradientTransform){var g=this.stroke.gradientTransform;ctx.transform.apply(ctx,g);}
this._stroke?this._stroke(ctx):ctx.stroke();}
this._removeShadow(ctx);ctx.restore();},clone:function(callback,propertiesToInclude){if(this.constructor.fromObject){return this.constructor.fromObject(this.toObject(propertiesToInclude),callback);}
return new fabric.Object(this.toObject(propertiesToInclude));},cloneAsImage:function(callback){var dataUrl=this.toDataURL();fabric.util.loadImage(dataUrl,function(img){if(callback){callback(new fabric.Image(img));}});return this;},toDataURL:function(options){options||(options={});var el=fabric.util.createCanvasElement(),boundingRect=this.getBoundingRect();el.width=boundingRect.width;el.height=boundingRect.height;fabric.util.wrapElement(el,'div');var canvas=new fabric.StaticCanvas(el);if(options.format==='jpg'){options.format='jpeg';}
if(options.format==='jpeg'){canvas.backgroundColor='#fff';}
var origParams={active:this.get('active'),left:this.getLeft(),top:this.getTop()};this.set('active',false);this.setPositionByOrigin(new fabric.Point(el.width/2,el.height/2),'center','center');var originalCanvas=this.canvas;canvas.add(this);var data=canvas.toDataURL(options);this.set(origParams).setCoords();this.canvas=originalCanvas;canvas.dispose();canvas=null;return data;},isType:function(type){return this.type===type;},complexity:function(){return 0;},toJSON:function(propertiesToInclude){return this.toObject(propertiesToInclude);},setGradient:function(property,options){options||(options={});var gradient={colorStops:[]};gradient.type=options.type||(options.r1||options.r2?'radial':'linear');gradient.coords={x1:options.x1,y1:options.y1,x2:options.x2,y2:options.y2};if(options.r1||options.r2){gradient.coords.r1=options.r1;gradient.coords.r2=options.r2;}
for(var position in options.colorStops){var color=new fabric.Color(options.colorStops[position]);gradient.colorStops.push({offset:position,color:color.toRgb(),opacity:color.getAlpha()});}
return this.set(property,fabric.Gradient.forObject(this,gradient));},setPatternFill:function(options){return this.set('fill',new fabric.Pattern(options));},setShadow:function(options){return this.set('shadow',options?new fabric.Shadow(options):null);},setColor:function(color){this.set('fill',color);return this;},setAngle:function(angle){var shouldCenterOrigin=(this.originX!=='center'||this.originY!=='center')&&this.centeredRotation;if(shouldCenterOrigin){this._setOriginToCenter();}
this.set('angle',angle);if(shouldCenterOrigin){this._resetOrigin();}
return this;},centerH:function(){this.canvas.centerObjectH(this);return this;},centerV:function(){this.canvas.centerObjectV(this);return this;},center:function(){this.canvas.centerObject(this);return this;},remove:function(){this.canvas.remove(this);return this;},getLocalPointer:function(e,pointer){pointer=pointer||this.canvas.getPointer(e);var objectLeftTop=this.translateToOriginPoint(this.getCenterPoint(),'left','top');return{x:pointer.x- objectLeftTop.x,y:pointer.y- objectLeftTop.y};},_setupCompositeOperation:function(ctx){if(this.globalCompositeOperation){this._prevGlobalCompositeOperation=ctx.globalCompositeOperation;ctx.globalCompositeOperation=this.globalCompositeOperation;}},_restoreCompositeOperation:function(ctx){if(this.globalCompositeOperation&&this._prevGlobalCompositeOperation){ctx.globalCompositeOperation=this._prevGlobalCompositeOperation;}}});fabric.util.createAccessors(fabric.Object);fabric.Object.prototype.rotate=fabric.Object.prototype.setAngle;extend(fabric.Object.prototype,fabric.Observable);fabric.Object.NUM_FRACTION_DIGITS=2;fabric.Object.__uid=0;})(typeof exports!=='undefined'?exports:this);(function(){var degreesToRadians=fabric.util.degreesToRadians;fabric.util.object.extend(fabric.Object.prototype,{translateToCenterPoint:function(point,originX,originY){var cx=point.x,cy=point.y,strokeWidth=this.stroke?this.strokeWidth:0;if(originX==='left'){cx=point.x+(this.getWidth()+ strokeWidth*this.scaleX)/ 2;
}
else if(originX==='right'){cx=point.x-(this.getWidth()+ strokeWidth*this.scaleX)/ 2;
}
if(originY==='top'){cy=point.y+(this.getHeight()+ strokeWidth*this.scaleY)/ 2;
}
else if(originY==='bottom'){cy=point.y-(this.getHeight()+ strokeWidth*this.scaleY)/ 2;
}
return fabric.util.rotatePoint(new fabric.Point(cx,cy),point,degreesToRadians(this.angle));},translateToOriginPoint:function(center,originX,originY){var x=center.x,y=center.y,strokeWidth=this.stroke?this.strokeWidth:0;if(originX==='left'){x=center.x-(this.getWidth()+ strokeWidth*this.scaleX)/ 2;
}
else if(originX==='right'){x=center.x+(this.getWidth()+ strokeWidth*this.scaleX)/ 2;
}
if(originY==='top'){y=center.y-(this.getHeight()+ strokeWidth*this.scaleY)/ 2;
}
else if(originY==='bottom'){y=center.y+(this.getHeight()+ strokeWidth*this.scaleY)/ 2;
}
return fabric.util.rotatePoint(new fabric.Point(x,y),center,degreesToRadians(this.angle));},getCenterPoint:function(){var leftTop=new fabric.Point(this.left,this.top);return this.translateToCenterPoint(leftTop,this.originX,this.originY);},getPointByOrigin:function(originX,originY){var center=this.getCenterPoint();return this.translateToOriginPoint(center,originX,originY);},toLocalPoint:function(point,originX,originY){var center=this.getCenterPoint(),strokeWidth=this.stroke?this.strokeWidth:0,x,y;if(originX&&originY){if(originX==='left'){x=center.x-(this.getWidth()+ strokeWidth*this.scaleX)/ 2;
}
else if(originX==='right'){x=center.x+(this.getWidth()+ strokeWidth*this.scaleX)/ 2;
}
else{x=center.x;}
if(originY==='top'){y=center.y-(this.getHeight()+ strokeWidth*this.scaleY)/ 2;
}
else if(originY==='bottom'){y=center.y+(this.getHeight()+ strokeWidth*this.scaleY)/ 2;
}
else{y=center.y;}}
else{x=this.left;y=this.top;}
return fabric.util.rotatePoint(new fabric.Point(point.x,point.y),center,-degreesToRadians(this.angle)).subtractEquals(new fabric.Point(x,y));},setPositionByOrigin:function(pos,originX,originY){var center=this.translateToCenterPoint(pos,originX,originY),position=this.translateToOriginPoint(center,this.originX,this.originY);this.set('left',position.x);this.set('top',position.y);},adjustPosition:function(to){var angle=degreesToRadians(this.angle),hypotHalf=this.getWidth()/ 2,
xHalf=Math.cos(angle)*hypotHalf,yHalf=Math.sin(angle)*hypotHalf,hypotFull=this.getWidth(),xFull=Math.cos(angle)*hypotFull,yFull=Math.sin(angle)*hypotFull;if(this.originX==='center'&&to==='left'||this.originX==='right'&&to==='center'){this.left-=xHalf;this.top-=yHalf;}
else if(this.originX==='left'&&to==='center'||this.originX==='center'&&to==='right'){this.left+=xHalf;this.top+=yHalf;}
else if(this.originX==='left'&&to==='right'){this.left+=xFull;this.top+=yFull;}
else if(this.originX==='right'&&to==='left'){this.left-=xFull;this.top-=yFull;}
this.setCoords();this.originX=to;},_setOriginToCenter:function(){this._originalOriginX=this.originX;this._originalOriginY=this.originY;var center=this.getCenterPoint();this.originX='center';this.originY='center';this.left=center.x;this.top=center.y;},_resetOrigin:function(){var originPoint=this.translateToOriginPoint(this.getCenterPoint(),this._originalOriginX,this._originalOriginY);this.originX=this._originalOriginX;this.originY=this._originalOriginY;this.left=originPoint.x;this.top=originPoint.y;this._originalOriginX=null;this._originalOriginY=null;},_getLeftTopCoords:function(){return this.translateToOriginPoint(this.getCenterPoint(),'left','center');}});})();(function(){var degreesToRadians=fabric.util.degreesToRadians;fabric.util.object.extend(fabric.Object.prototype,{oCoords:null,intersectsWithRect:function(pointTL,pointBR){var oCoords=this.oCoords,tl=new fabric.Point(oCoords.tl.x,oCoords.tl.y),tr=new fabric.Point(oCoords.tr.x,oCoords.tr.y),bl=new fabric.Point(oCoords.bl.x,oCoords.bl.y),br=new fabric.Point(oCoords.br.x,oCoords.br.y),intersection=fabric.Intersection.intersectPolygonRectangle([tl,tr,br,bl],pointTL,pointBR);return intersection.status==='Intersection';},intersectsWithObject:function(other){function getCoords(oCoords){return{tl:new fabric.Point(oCoords.tl.x,oCoords.tl.y),tr:new fabric.Point(oCoords.tr.x,oCoords.tr.y),bl:new fabric.Point(oCoords.bl.x,oCoords.bl.y),br:new fabric.Point(oCoords.br.x,oCoords.br.y)};}
var thisCoords=getCoords(this.oCoords),otherCoords=getCoords(other.oCoords),intersection=fabric.Intersection.intersectPolygonPolygon([thisCoords.tl,thisCoords.tr,thisCoords.br,thisCoords.bl],[otherCoords.tl,otherCoords.tr,otherCoords.br,otherCoords.bl]);return intersection.status==='Intersection';},isContainedWithinObject:function(other){var boundingRect=other.getBoundingRect(),point1=new fabric.Point(boundingRect.left,boundingRect.top),point2=new fabric.Point(boundingRect.left+ boundingRect.width, boundingRect.top + boundingRect.height);

      return this.isContainedWithinRect(point1, point2);
    },

    /**
     * Checks if object is fully contained within area formed by 2 points
     * @param {Object} pointTL top-left point of area
     * @param {Object} pointBR bottom-right point of area
     * @return {Boolean} true if object is fully contained within area formed by 2 points
     */
    isContainedWithinRect: function(pointTL, pointBR) {
      var boundingRect = this.getBoundingRect();

      return (
        boundingRect.left >= pointTL.x &&
        boundingRect.left + boundingRect.width <= pointBR.x &&
        boundingRect.top >= pointTL.y &&
        boundingRect.top + boundingRect.height <= pointBR.y
      );
    },

    /**
     * Checks if point is inside the object
     * @param {fabric.Point} point Point to check against
     * @return {Boolean} true if point is inside the object
     */
    containsPoint: function(point) {
      var lines = this._getImageLines(this.oCoords),
          xPoints = this._findCrossPoints(point, lines);

      // if xPoints is odd then point is inside the object
      return (xPoints !== 0 && xPoints % 2 === 1);
    },

    /**
     * Method that returns an object with the object edges in it, given the coordinates of the corners
     * @private
     * @param {Object} oCoords Coordinates of the object corners
     */
    _getImageLines: function(oCoords) {
      return {
        topline: {
          o: oCoords.tl,
          d: oCoords.tr
        },
        rightline: {
          o: oCoords.tr,
          d: oCoords.br
        },
        bottomline: {
          o: oCoords.br,
          d: oCoords.bl
        },
        leftline: {
          o: oCoords.bl,
          d: oCoords.tl
        }
      };
    },

    /**
     * Helper method to determine how many cross points are between the 4 object edges
     * and the horizontal line determined by a point on canvas
     * @private
     * @param {fabric.Point} point Point to check
     * @param {Object} oCoords Coordinates of the object being evaluated
     */
    _findCrossPoints: function(point, oCoords) {
      var b1, b2, a1, a2, xi, yi,
          xcount = 0,
          iLine;

      for (var lineKey in oCoords) {
        iLine = oCoords[lineKey];
        // optimisation 1: line below point. no cross
        if ((iLine.o.y < point.y) && (iLine.d.y < point.y)) {
          continue;
        }
        // optimisation 2: line above point. no cross
        if ((iLine.o.y >= point.y) && (iLine.d.y >= point.y)) {
          continue;
        }
        // optimisation 3: vertical line case
        if ((iLine.o.x === iLine.d.x) && (iLine.o.x >= point.x)) {
          xi = iLine.o.x;
          yi = point.y;
        }
        // calculate the intersection point
        else {
          b1 = 0;
          b2 = (iLine.d.y - iLine.o.y) / (iLine.d.x - iLine.o.x);
          a1 = point.y - b1 * point.x;
          a2 = iLine.o.y - b2 * iLine.o.x;

          xi = - (a1 - a2) / (b1 - b2);
          yi = a1 + b1 * xi;
        }
        // dont count xi < point.x cases
        if (xi >= point.x) {
          xcount += 1;
        }
        // optimisation 4: specific for square images
        if (xcount === 2) {
          break;
        }
      }
      return xcount;
    },

    /**
     * Returns width of an object's bounding rectangle
     * @deprecated since 1.0.4
     * @return {Number} width value
     */
    getBoundingRectWidth: function() {
      return this.getBoundingRect().width;
    },

    /**
     * Returns height of an object's bounding rectangle
     * @deprecated since 1.0.4
     * @return {Number} height value
     */
    getBoundingRectHeight: function() {
      return this.getBoundingRect().height;
    },

    /**
     * Returns coordinates of object's bounding rectangle (left, top, width, height)
     * @return {Object} Object with left, top, width, height properties
     */
    getBoundingRect: function() {
      this.oCoords || this.setCoords();

      var xCoords = [this.oCoords.tl.x, this.oCoords.tr.x, this.oCoords.br.x, this.oCoords.bl.x],
          minX = fabric.util.array.min(xCoords),
          maxX = fabric.util.array.max(xCoords),
          width = Math.abs(minX - maxX),

          yCoords = [this.oCoords.tl.y, this.oCoords.tr.y, this.oCoords.br.y, this.oCoords.bl.y],
          minY = fabric.util.array.min(yCoords),
          maxY = fabric.util.array.max(yCoords),
          height = Math.abs(minY - maxY);

      return {
        left: minX,
        top: minY,
        width: width,
        height: height
      };
    },

    /**
     * Returns width of an object
     * @return {Number} width value
     */
    getWidth: function() {
      return this.width * this.scaleX;
    },

    /**
     * Returns height of an object
     * @return {Number} height value
     */
    getHeight: function() {
      return this.height * this.scaleY;
    },

    /**
     * Makes sure the scale is valid and modifies it if necessary
     * @private
     * @param {Number} value
     * @return {Number}
     */
    _constrainScale: function(value) {
      if (Math.abs(value) < this.minScaleLimit) {
        if (value < 0) {
          return -this.minScaleLimit;
        }
        else {
          return this.minScaleLimit;
        }
      }
      return value;
    },

    /**
     * Scales an object (equally by x and y)
     * @param {Number} value Scale factor
     * @return {fabric.Object} thisArg
     * @chainable
     */
    scale: function(value) {
      value = this._constrainScale(value);

      if (value < 0) {
        this.flipX = !this.flipX;
        this.flipY = !this.flipY;
        value *= -1;
      }

      this.scaleX = value;
      this.scaleY = value;
      this.setCoords();
      return this;
    },

    /**
     * Scales an object to a given width, with respect to bounding box (scaling by x/y equally)
     * @param {Number} value New width value
     * @return {fabric.Object} thisArg
     * @chainable
     */
    scaleToWidth: function(value) {
      // adjust to bounding rect factor so that rotated shapes would fit as well
      var boundingRectFactor = this.getBoundingRectWidth() / this.getWidth();
      return this.scale(value / this.width / boundingRectFactor);
    },

    /**
     * Scales an object to a given height, with respect to bounding box (scaling by x/y equally)
     * @param {Number} value New height value
     * @return {fabric.Object} thisArg
     * @chainable
     */
    scaleToHeight: function(value) {
      // adjust to bounding rect factor so that rotated shapes would fit as well
      var boundingRectFactor = this.getBoundingRectHeight() / this.getHeight();
      return this.scale(value / this.height / boundingRectFactor);
    },

    /**
     * Sets corner position coordinates based on current angle, width and height
     * See https://github.com/kangax/fabric.js/wiki/When-to-call-setCoords
     * @return {fabric.Object} thisArg
     * @chainable
     */
    setCoords: function() {
      var theta = degreesToRadians(this.angle),
          vpt = this.getViewportTransform(),
          f = function (p) {
            return fabric.util.transformPoint(p, vpt);
          },
          p = this._calculateCurrentDimensions(false),
          currentWidth = p.x, currentHeight = p.y;

      // If width is negative, make postive. Fixes path selection issue
      if (currentWidth < 0) {
        currentWidth = Math.abs(currentWidth);
      }

      var _hypotenuse = Math.sqrt(
            Math.pow(currentWidth / 2, 2) +
            Math.pow(currentHeight / 2, 2)),

          _angle = Math.atan(
            isFinite(currentHeight / currentWidth)
              ? currentHeight / currentWidth
              : 0),

          // offset added for rotate and scale actions
          offsetX = Math.cos(_angle + theta) * _hypotenuse,
          offsetY = Math.sin(_angle + theta) * _hypotenuse,
          sinTh = Math.sin(theta),
          cosTh = Math.cos(theta),
          coords = this.getCenterPoint(),
          wh = new fabric.Point(currentWidth, currentHeight),
          _tl =   new fabric.Point(coords.x - offsetX, coords.y - offsetY),
          _tr =   new fabric.Point(_tl.x + (wh.x * cosTh),   _tl.y + (wh.x * sinTh)),
          bl =  f(new fabric.Point(_tl.x - (wh.y * sinTh),   _tl.y + (wh.y * cosTh))),
          br  = f(new fabric.Point(_tr.x - (wh.y * sinTh),   _tr.y + (wh.y * cosTh))),
          tl  = f(_tl),
          tr  = f(_tr),
          ml  = new fabric.Point((tl.x + bl.x)/2, (tl.y + bl.y)/2),
          mt  = new fabric.Point((tr.x + tl.x)/2, (tr.y + tl.y)/2),
          mr  = new fabric.Point((br.x + tr.x)/2, (br.y + tr.y)/2),
          mb  = new fabric.Point((br.x + bl.x)/2, (br.y + bl.y)/2),
          mtr = new fabric.Point(mt.x + sinTh * this.rotatingPointOffset, mt.y - cosTh * this.rotatingPointOffset);
      // debugging

      /* setTimeout(function() {
         canvas.contextTop.fillStyle = 'green';
         canvas.contextTop.fillRect(mb.x, mb.y, 3, 3);
         canvas.contextTop.fillRect(bl.x, bl.y, 3, 3);
         canvas.contextTop.fillRect(br.x, br.y, 3, 3);
         canvas.contextTop.fillRect(tl.x, tl.y, 3, 3);
         canvas.contextTop.fillRect(tr.x, tr.y, 3, 3);
         canvas.contextTop.fillRect(ml.x, ml.y, 3, 3);
         canvas.contextTop.fillRect(mr.x, mr.y, 3, 3);
         canvas.contextTop.fillRect(mt.x, mt.y, 3, 3);
         canvas.contextTop.fillRect(mtr.x, mtr.y, 3, 3);
       }, 50); */

      this.oCoords = {
        // corners
        tl: tl, tr: tr, br: br, bl: bl,
        // middle
        ml: ml, mt: mt, mr: mr, mb: mb,
        // rotating point
        mtr: mtr
      };

      // set coordinates of the draggable boxes in the corners used to scale/rotate the image
      this._setCornerCoords && this._setCornerCoords();

      return this;
    }
  });
})();


fabric.util.object.extend(fabric.Object.prototype, /** @lends fabric.Object.prototype */ {

  /**
   * Moves an object to the bottom of the stack of drawn objects
   * @return {fabric.Object} thisArg
   * @chainable
   */
  sendToBack: function() {
    if (this.group) {
      fabric.StaticCanvas.prototype.sendToBack.call(this.group, this);
    }
    else {
      this.canvas.sendToBack(this);
    }
    return this;
  },

  /**
   * Moves an object to the top of the stack of drawn objects
   * @return {fabric.Object} thisArg
   * @chainable
   */
  bringToFront: function() {
    if (this.group) {
      fabric.StaticCanvas.prototype.bringToFront.call(this.group, this);
    }
    else {
      this.canvas.bringToFront(this);
    }
    return this;
  },

  /**
   * Moves an object down in stack of drawn objects
   * @param {Boolean} [intersecting] If `true`, send object behind next lower intersecting object
   * @return {fabric.Object} thisArg
   * @chainable
   */
  sendBackwards: function(intersecting) {
    if (this.group) {
      fabric.StaticCanvas.prototype.sendBackwards.call(this.group, this, intersecting);
    }
    else {
      this.canvas.sendBackwards(this, intersecting);
    }
    return this;
  },

  /**
   * Moves an object up in stack of drawn objects
   * @param {Boolean} [intersecting] If `true`, send object in front of next upper intersecting object
   * @return {fabric.Object} thisArg
   * @chainable
   */
  bringForward: function(intersecting) {
    if (this.group) {
      fabric.StaticCanvas.prototype.bringForward.call(this.group, this, intersecting);
    }
    else {
      this.canvas.bringForward(this, intersecting);
    }
    return this;
  },

  /**
   * Moves an object to specified level in stack of drawn objects
   * @param {Number} index New position of object
   * @return {fabric.Object} thisArg
   * @chainable
   */
  moveTo: function(index) {
    if (this.group) {
      fabric.StaticCanvas.prototype.moveTo.call(this.group, this, index);
    }
    else {
      this.canvas.moveTo(this, index);
    }
    return this;
  }
});


/* _TO_SVG_START_ */
fabric.util.object.extend(fabric.Object.prototype, /** @lends fabric.Object.prototype */ {

  /**
   * Returns styles-string for svg-export
   * @return {String}
   */
  getSvgStyles: function() {

    var fill = this.fill
          ? (this.fill.toLive ? 'url(#SVGID_' + this.fill.id + ')' : this.fill)
          : 'none',
        fillRule = this.fillRule,
        stroke = this.stroke
          ? (this.stroke.toLive ? 'url(#SVGID_' + this.stroke.id + ')' : this.stroke)
          : 'none',

        strokeWidth = this.strokeWidth ? this.strokeWidth : '0',
        strokeDashArray = this.strokeDashArray ? this.strokeDashArray.join(' ') : '',
        strokeLineCap = this.strokeLineCap ? this.strokeLineCap : 'butt',
        strokeLineJoin = this.strokeLineJoin ? this.strokeLineJoin : 'miter',
        strokeMiterLimit = this.strokeMiterLimit ? this.strokeMiterLimit : '4',
        opacity = typeof this.opacity !== 'undefined' ? this.opacity : '1',

        visibility = this.visible ? '' : ' visibility: hidden;',
        filter = this.shadow ? 'filter: url(#SVGID_' + this.shadow.id + ');' : '';

    return [
      'stroke: ', stroke, '; ',
      'stroke-width: ', strokeWidth, '; ',
      'stroke-dasharray: ', strokeDashArray, '; ',
      'stroke-linecap: ', strokeLineCap, '; ',
      'stroke-linejoin: ', strokeLineJoin, '; ',
      'stroke-miterlimit: ', strokeMiterLimit, '; ',
      'fill: ', fill, '; ',
      'fill-rule: ', fillRule, '; ',
      'opacity: ', opacity, ';',
      filter,
      visibility
    ].join('');
  },

  /**
   * Returns transform-string for svg-export
   * @return {String}
   */
  getSvgTransform: function() {
    if (this.group && this.group.type === 'path-group') {
      return '';
    }
    var toFixed = fabric.util.toFixed,
        angle = this.getAngle(),
        vpt = !this.canvas || this.canvas.svgViewportTransformation ? this.getViewportTransform() : [1, 0, 0, 1, 0, 0],
        center = fabric.util.transformPoint(this.getCenterPoint(), vpt),

        NUM_FRACTION_DIGITS = fabric.Object.NUM_FRACTION_DIGITS,

        translatePart = this.type === 'path-group' ? '' : 'translate(' +
                          toFixed(center.x, NUM_FRACTION_DIGITS) +
                          ' ' +
                          toFixed(center.y, NUM_FRACTION_DIGITS) +
                        ')',

        anglePart = angle !== 0
          ? (' rotate(' + toFixed(angle, NUM_FRACTION_DIGITS) + ')')
          : '',

        scalePart = (this.scaleX === 1 && this.scaleY === 1 && vpt[0] === 1 && vpt[3] === 1)
          ? '' :
          (' scale(' +
            toFixed(this.scaleX * vpt[0], NUM_FRACTION_DIGITS) +
            ' ' +
            toFixed(this.scaleY * vpt[3], NUM_FRACTION_DIGITS) +
          ')'),

        addTranslateX = this.type === 'path-group' ? this.width * vpt[0] : 0,

        flipXPart = this.flipX ? ' matrix(-1 0 0 1 ' + addTranslateX + ' 0) ' : '',

        addTranslateY = this.type === 'path-group' ? this.height * vpt[3] : 0,

        flipYPart = this.flipY ? ' matrix(1 0 0 -1 0 ' + addTranslateY + ')' : '';

    return [
      translatePart, anglePart, scalePart, flipXPart, flipYPart
    ].join('');
  },

  /**
   * Returns transform-string for svg-export from the transform matrix of single elements
   * @return {String}
   */
  getSvgTransformMatrix: function() {
    return this.transformMatrix ? ' matrix(' + this.transformMatrix.join(' ') + ') ' : '';
  },

  /**
   * @private
   */
  _createBaseSVGMarkup: function() {
    var markup = [ ];

    if (this.fill && this.fill.toLive) {
      markup.push(this.fill.toSVG(this, false));
    }
    if (this.stroke && this.stroke.toLive) {
      markup.push(this.stroke.toSVG(this, false));
    }
    if (this.shadow) {
      markup.push(this.shadow.toSVG(this));
    }
    return markup;
  }
});
/* _TO_SVG_END_ */


/*
  Depends on `stateProperties`
*/
fabric.util.object.extend(fabric.Object.prototype, /** @lends fabric.Object.prototype */ {

  /**
   * Returns true if object state (one of its state properties) was changed
   * @return {Boolean} true if instance' state has changed since `{@link fabric.Object#saveState}` was called
   */
  hasStateChanged: function() {
    return this.stateProperties.some(function(prop) {
      return this.get(prop) !== this.originalState[prop];
    }, this);
  },

  /**
   * Saves state of an object
   * @param {Object} [options] Object with additional `stateProperties` array to include when saving state
   * @return {fabric.Object} thisArg
   */
  saveState: function(options) {
    this.stateProperties.forEach(function(prop) {
      this.originalState[prop] = this.get(prop);
    }, this);

    if (options && options.stateProperties) {
      options.stateProperties.forEach(function(prop) {
        this.originalState[prop] = this.get(prop);
      }, this);
    }

    return this;
  },

  /**
   * Setups state of an object
   * @return {fabric.Object} thisArg
   */
  setupState: function() {
    this.originalState = { };
    this.saveState();

    return this;
  }
});


(function() {

  var degreesToRadians = fabric.util.degreesToRadians,
      //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
      isVML = function() { return typeof G_vmlCanvasManager !== 'undefined'; };
  //jscs:enable requireCamelCaseOrUpperCaseIdentifiers

  fabric.util.object.extend(fabric.Object.prototype, /** @lends fabric.Object.prototype */ {

    /**
     * The object interactivity controls.
     * @private
     */
    _controlsVisibility: null,

    /**
     * Determines which corner has been clicked
     * @private
     * @param {Object} pointer The pointer indicating the mouse position
     * @return {String|Boolean} corner code (tl, tr, bl, br, etc.), or false if nothing is found
     */
    _findTargetCorner: function(pointer) {
      if (!this.hasControls || !this.active) {
        return false;
      }

      var ex = pointer.x,
          ey = pointer.y,
          xPoints,
          lines;

      for (var i in this.oCoords) {

        if (!this.isControlVisible(i)) {
          continue;
        }

        if (i === 'mtr' && !this.hasRotatingPoint) {
          continue;
        }

        if (this.get('lockUniScaling') &&
           (i === 'mt' || i === 'mr' || i === 'mb' || i === 'ml')) {
          continue;
        }

        lines = this._getImageLines(this.oCoords[i].corner);

        // debugging

        // canvas.contextTop.fillRect(lines.bottomline.d.x, lines.bottomline.d.y, 2, 2);
        // canvas.contextTop.fillRect(lines.bottomline.o.x, lines.bottomline.o.y, 2, 2);

        // canvas.contextTop.fillRect(lines.leftline.d.x, lines.leftline.d.y, 2, 2);
        // canvas.contextTop.fillRect(lines.leftline.o.x, lines.leftline.o.y, 2, 2);

        // canvas.contextTop.fillRect(lines.topline.d.x, lines.topline.d.y, 2, 2);
        // canvas.contextTop.fillRect(lines.topline.o.x, lines.topline.o.y, 2, 2);

        // canvas.contextTop.fillRect(lines.rightline.d.x, lines.rightline.d.y, 2, 2);
        // canvas.contextTop.fillRect(lines.rightline.o.x, lines.rightline.o.y, 2, 2);

        xPoints = this._findCrossPoints({ x: ex, y: ey }, lines);
        if (xPoints !== 0 && xPoints % 2 === 1) {
          this.__corner = i;
          return i;
        }
      }
      return false;
    },

    /**
     * Sets the coordinates of the draggable boxes in the corners of
     * the image used to scale/rotate it.
     * @private
     */
    _setCornerCoords: function() {
      var coords = this.oCoords,
          newTheta = degreesToRadians(45 - this.angle),
          cornerHypotenuse = Math.sqrt(2 * Math.pow(this.cornerSize, 2)) / 2,
          cosHalfOffset = cornerHypotenuse * Math.cos(newTheta),
          sinHalfOffset = cornerHypotenuse * Math.sin(newTheta),
          x, y;

      for (var point in coords) {
        x = coords[point].x;
        y = coords[point].y;
        coords[point].corner = {
          tl: {
            x: x - sinHalfOffset,
            y: y - cosHalfOffset
          },
          tr: {
            x: x + cosHalfOffset,
            y: y - sinHalfOffset
          },
          bl: {
            x: x - cosHalfOffset,
            y: y + sinHalfOffset
          },
          br: {
            x: x + sinHalfOffset,
            y: y + cosHalfOffset
          }
        };
      }
    },

    _calculateCurrentDimensions: function(shouldTransform)  {
      var vpt = this.getViewportTransform(),
          strokeWidth = this.strokeWidth,
          w = this.width,
          h = this.height,
          capped = this.strokeLineCap === 'round' || this.strokeLineCap === 'square',
          vLine = this.type === 'line' && this.width === 0,
          hLine = this.type === 'line' && this.height === 0,
          sLine = vLine || hLine,
          strokeW = (capped && hLine) || !sLine,
          strokeH = (capped && vLine) || !sLine;

      if (vLine) {
        w = strokeWidth;
      }
      else if (hLine) {
        h = strokeWidth;
      }
      if (strokeW) {
        w += (w < 0 ? -strokeWidth : strokeWidth);
      }
      if (strokeH) {
        h += (h < 0 ? -strokeWidth : strokeWidth);
      }

      w = w * this.scaleX + 2 * this.padding;
      h = h * this.scaleY + 2 * this.padding;

      if (shouldTransform) {
        return fabric.util.transformPoint(new fabric.Point(w, h), vpt, true);
      }
      return { x: w, y: h };
    },

    /**
     * Draws borders of an object's bounding box.
     * Requires public properties: width, height
     * Requires public options: padding, borderColor
     * @param {CanvasRenderingContext2D} ctx Context to draw on
     * @return {fabric.Object} thisArg
     * @chainable
     */
    drawBorders: function(ctx) {
      if (!this.hasBorders) {
        return this;
      }

      ctx.save();

      ctx.globalAlpha = this.isMoving ? this.borderOpacityWhenMoving : 1;
      ctx.strokeStyle = this.borderColor;
      ctx.lineWidth = 1 / this.borderScaleFactor;

      var wh = this._calculateCurrentDimensions(true),
          width = wh.x,
          height = wh.y;
      if (this.group) {
        width = width * this.group.scaleX;
        height = height * this.group.scaleY;
      }

      ctx.strokeRect(
        ~~(-(width / 2)) - 0.5, // offset needed to make lines look sharper
        ~~(-(height / 2)) - 0.5,
        ~~(width) + 1, // double offset needed to make lines look sharper
        ~~(height) + 1
      );

      if (this.hasRotatingPoint && this.isControlVisible('mtr') && !this.get('lockRotation') && this.hasControls) {

        var rotateHeight = -height / 2;

        ctx.beginPath();
        ctx.moveTo(0, rotateHeight);
        ctx.lineTo(0, rotateHeight - this.rotatingPointOffset);
        ctx.closePath();
        ctx.stroke();
      }

      ctx.restore();
      return this;
    },

    /**
     * Draws corners of an object's bounding box.
     * Requires public properties: width, height
     * Requires public options: cornerSize, padding
     * @param {CanvasRenderingContext2D} ctx Context to draw on
     * @return {fabric.Object} thisArg
     * @chainable
     */
    drawControls: function(ctx) {
      if (!this.hasControls) {
        return this;
      }

      var wh = this._calculateCurrentDimensions(true),
          width = wh.x,
          height = wh.y,
          left = -(width / 2),
          top = -(height / 2),
          scaleOffset = this.cornerSize / 2,
          methodName = this.transparentCorners ? 'strokeRect' : 'fillRect';

      ctx.save();

      ctx.lineWidth = 1;

      ctx.globalAlpha = this.isMoving ? this.borderOpacityWhenMoving : 1;
      ctx.strokeStyle = ctx.fillStyle = this.cornerColor;

      // top-left
      this._drawControl('tl', ctx, methodName,
        left - scaleOffset,
        top - scaleOffset);

      // top-right
      this._drawControl('tr', ctx, methodName,
        left + width - scaleOffset,
        top - scaleOffset);

      // bottom-left
      this._drawControl('bl', ctx, methodName,
        left - scaleOffset,
        top + height - scaleOffset);

      // bottom-right
      this._drawControl('br', ctx, methodName,
        left + width - scaleOffset,
        top + height - scaleOffset);

      if (!this.get('lockUniScaling')) {

        // middle-top
        this._drawControl('mt', ctx, methodName,
          left + width/2 - scaleOffset,
          top - scaleOffset);

        // middle-bottom
        this._drawControl('mb', ctx, methodName,
          left + width/2 - scaleOffset,
          top + height - scaleOffset);

        // middle-right
        this._drawControl('mr', ctx, methodName,
          left + width - scaleOffset,
          top + height/2 - scaleOffset);

        // middle-left
        this._drawControl('ml', ctx, methodName,
          left - scaleOffset,
          top + height/2 - scaleOffset);
      }

      // middle-top-rotate
      if (this.hasRotatingPoint) {
        this._drawControl('mtr', ctx, methodName,
          left + width/2 - scaleOffset,
          top - this.rotatingPointOffset - scaleOffset);
      }

      ctx.restore();

      return this;
    },

    /**
     * @private
     */
    _drawControl: function(control, ctx, methodName, left, top) {
      if (!this.isControlVisible(control)) {
        return;
      }
      var size = this.cornerSize;
      isVML() || this.transparentCorners || ctx.clearRect(left, top, size, size);
      ctx[methodName](left, top, size, size);
    },

    /**
     * Returns true if the specified control is visible, false otherwise.
     * @param {String} controlName The name of the control. Possible values are 'tl', 'tr', 'br', 'bl', 'ml', 'mt', 'mr', 'mb', 'mtr'.
     * @returns {Boolean} true if the specified control is visible, false otherwise
     */
    isControlVisible: function(controlName) {
      return this._getControlsVisibility()[controlName];
    },

    /**
     * Sets the visibility of the specified control.
     * @param {String} controlName The name of the control. Possible values are 'tl', 'tr', 'br', 'bl', 'ml', 'mt', 'mr', 'mb', 'mtr'.
     * @param {Boolean} visible true to set the specified control visible, false otherwise
     * @return {fabric.Object} thisArg
     * @chainable
     */
    setControlVisible: function(controlName, visible) {
      this._getControlsVisibility()[controlName] = visible;
      return this;
    },

    /**
     * Sets the visibility state of object controls.
     * @param {Object} [options] Options object
     * @param {Boolean} [options.bl] true to enable the bottom-left control, false to disable it
     * @param {Boolean} [options.br] true to enable the bottom-right control, false to disable it
     * @param {Boolean} [options.mb] true to enable the middle-bottom control, false to disable it
     * @param {Boolean} [options.ml] true to enable the middle-left control, false to disable it
     * @param {Boolean} [options.mr] true to enable the middle-right control, false to disable it
     * @param {Boolean} [options.mt] true to enable the middle-top control, false to disable it
     * @param {Boolean} [options.tl] true to enable the top-left control, false to disable it
     * @param {Boolean} [options.tr] true to enable the top-right control, false to disable it
     * @param {Boolean} [options.mtr] true to enable the middle-top-rotate control, false to disable it
     * @return {fabric.Object} thisArg
     * @chainable
     */
    setControlsVisibility: function(options) {
      options || (options = { });

      for (var p in options) {
        this.setControlVisible(p, options[p]);
      }
      return this;
    },

    /**
     * Returns the instance of the control visibility set for this object.
     * @private
     * @returns {Object}
     */
    _getControlsVisibility: function() {
      if (!this._controlsVisibility) {
        this._controlsVisibility = {
          tl: true,
          tr: true,
          br: true,
          bl: true,
          ml: true,
          mt: true,
          mr: true,
          mb: true,
          mtr: true
        };
      }
      return this._controlsVisibility;
    }
  });
})();


fabric.util.object.extend(fabric.StaticCanvas.prototype, /** @lends fabric.StaticCanvas.prototype */ {

  /**
   * Animation duration (in ms) for fx* methods
   * @type Number
   * @default
   */
  FX_DURATION: 500,

  /**
   * Centers object horizontally with animation.
   * @param {fabric.Object} object Object to center
   * @param {Object} [callbacks] Callbacks object with optional "onComplete" and/or "onChange" properties
   * @param {Function} [callbacks.onComplete] Invoked on completion
   * @param {Function} [callbacks.onChange] Invoked on every step of animation
   * @return {fabric.Canvas} thisArg
   * @chainable
   */
  fxCenterObjectH: function (object, callbacks) {
    callbacks = callbacks || { };

    var empty = function() { },
        onComplete = callbacks.onComplete || empty,
        onChange = callbacks.onChange || empty,
        _this = this;

    fabric.util.animate({
      startValue: object.get('left'),
      endValue: this.getCenter().left,
      duration: this.FX_DURATION,
      onChange: function(value) {
        object.set('left', value);
        _this.renderAll();
        onChange();
      },
      onComplete: function() {
        object.setCoords();
        onComplete();
      }
    });

    return this;
  },

  /**
   * Centers object vertically with animation.
   * @param {fabric.Object} object Object to center
   * @param {Object} [callbacks] Callbacks object with optional "onComplete" and/or "onChange" properties
   * @param {Function} [callbacks.onComplete] Invoked on completion
   * @param {Function} [callbacks.onChange] Invoked on every step of animation
   * @return {fabric.Canvas} thisArg
   * @chainable
   */
  fxCenterObjectV: function (object, callbacks) {
    callbacks = callbacks || { };

    var empty = function() { },
        onComplete = callbacks.onComplete || empty,
        onChange = callbacks.onChange || empty,
        _this = this;

    fabric.util.animate({
      startValue: object.get('top'),
      endValue: this.getCenter().top,
      duration: this.FX_DURATION,
      onChange: function(value) {
        object.set('top', value);
        _this.renderAll();
        onChange();
      },
      onComplete: function() {
        object.setCoords();
        onComplete();
      }
    });

    return this;
  },

  /**
   * Same as `fabric.Canvas#remove` but animated
   * @param {fabric.Object} object Object to remove
   * @param {Object} [callbacks] Callbacks object with optional "onComplete" and/or "onChange" properties
   * @param {Function} [callbacks.onComplete] Invoked on completion
   * @param {Function} [callbacks.onChange] Invoked on every step of animation
   * @return {fabric.Canvas} thisArg
   * @chainable
   */
  fxRemove: function (object, callbacks) {
    callbacks = callbacks || { };

    var empty = function() { },
        onComplete = callbacks.onComplete || empty,
        onChange = callbacks.onChange || empty,
        _this = this;

    fabric.util.animate({
      startValue: object.get('opacity'),
      endValue: 0,
      duration: this.FX_DURATION,
      onStart: function() {
        object.set('active', false);
      },
      onChange: function(value) {
        object.set('opacity', value);
        _this.renderAll();
        onChange();
      },
      onComplete: function () {
        _this.remove(object);
        onComplete();
      }
    });

    return this;
  }
});

fabric.util.object.extend(fabric.Object.prototype, /** @lends fabric.Object.prototype */ {
  /**
   * Animates object's properties
   * @param {String|Object} property Property to animate (if string) or properties to animate (if object)
   * @param {Number|Object} value Value to animate property to (if string was given first) or options object
   * @return {fabric.Object} thisArg
   * @tutorial {@link http://fabricjs.com/fabric-intro-part-2/#animation}
   * @chainable
   *
   * As object — multiple properties
   *
   * object.animate({ left: ..., top: ... });
   * object.animate({ left: ..., top: ... }, { duration: ... });
   *
   * As string — one property
   *
   * object.animate('left', ...);
   * object.animate('left', { duration: ... });
   *
   */
  animate: function() {
    if (arguments[0] && typeof arguments[0] === 'object') {
      var propsToAnimate = [ ], prop, skipCallbacks;
      for (prop in arguments[0]) {
        propsToAnimate.push(prop);
      }
      for (var i = 0, len = propsToAnimate.length; i < len; i++) {
        prop = propsToAnimate[i];
        skipCallbacks = i !== len - 1;
        this._animate(prop, arguments[0][prop], arguments[1], skipCallbacks);
      }
    }
    else {
      this._animate.apply(this, arguments);
    }
    return this;
  },

  /**
   * @private
   * @param {String} property Property to animate
   * @param {String} to Value to animate to
   * @param {Object} [options] Options object
   * @param {Boolean} [skipCallbacks] When true, callbacks like onchange and oncomplete are not invoked
   */
  _animate: function(property, to, options, skipCallbacks) {
    var _this = this, propPair;

    to = to.toString();

    if (!options) {
      options = { };
    }
    else {
      options = fabric.util.object.clone(options);
    }

    if (~property.indexOf('.')) {
      propPair = property.split('.');
    }

    var currentValue = propPair
      ? this.get(propPair[0])[propPair[1]]
      : this.get(property);

    if (!('from' in options)) {
      options.from = currentValue;
    }

    if (~to.indexOf('=')) {
      to = currentValue + parseFloat(to.replace('=', ''));
    }
    else {
      to = parseFloat(to);
    }

    fabric.util.animate({
      startValue: options.from,
      endValue: to,
      byValue: options.by,
      easing: options.easing,
      duration: options.duration,
      abort: options.abort && function() {
        return options.abort.call(_this);
      },
      onChange: function(value) {
        if (propPair) {
          _this[propPair[0]][propPair[1]] = value;
        }
        else {
          _this.set(property, value);
        }
        if (skipCallbacks) {
          return;
        }
        options.onChange && options.onChange();
      },
      onComplete: function() {
        if (skipCallbacks) {
          return;
        }

        _this.setCoords();
        options.onComplete && options.onComplete();
      }
    });
  }
});


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend,
      coordProps = { x1: 1, x2: 1, y1: 1, y2: 1 },
      supportsLineDash = fabric.StaticCanvas.supports('setLineDash');

  if (fabric.Line) {
    fabric.warn('fabric.Line is already defined');
    return;
  }

  /**
   * Line class
   * @class fabric.Line
   * @extends fabric.Object
   * @see {@link fabric.Line#initialize} for constructor definition
   */
  fabric.Line = fabric.util.createClass(fabric.Object, /** @lends fabric.Line.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'line',

    /**
     * x value or first line edge
     * @type Number
     * @default
     */
    x1: 0,

    /**
     * y value or first line edge
     * @type Number
     * @default
     */
    y1: 0,

    /**
     * x value or second line edge
     * @type Number
     * @default
     */
    x2: 0,

    /**
     * y value or second line edge
     * @type Number
     * @default
     */
    y2: 0,

    /**
     * Constructor
     * @param {Array} [points] Array of points
     * @param {Object} [options] Options object
     * @return {fabric.Line} thisArg
     */
    initialize: function(points, options) {
      options = options || { };

      if (!points) {
        points = [0, 0, 0, 0];
      }

      this.callSuper('initialize', options);

      this.set('x1', points[0]);
      this.set('y1', points[1]);
      this.set('x2', points[2]);
      this.set('y2', points[3]);

      this._setWidthHeight(options);
    },

    /**
     * @private
     * @param {Object} [options] Options
     */
    _setWidthHeight: function(options) {
      options || (options = { });

      this.width = Math.abs(this.x2 - this.x1);
      this.height = Math.abs(this.y2 - this.y1);

      this.left = 'left' in options
        ? options.left
        : this._getLeftToOriginX();

      this.top = 'top' in options
        ? options.top
        : this._getTopToOriginY();
    },

    /**
     * @private
     * @param {String} key
     * @param {Any} value
     */
    _set: function(key, value) {
      this.callSuper('_set', key, value);
      if (typeof coordProps[key] !== 'undefined') {
        this._setWidthHeight();
      }
      return this;
    },

    /**
     * @private
     * @return {Number} leftToOriginX Distance from left edge of canvas to originX of Line.
     */
    _getLeftToOriginX: makeEdgeToOriginGetter(
      { // property names
        origin: 'originX',
        axis1: 'x1',
        axis2: 'x2',
        dimension: 'width'
      },
      { // possible values of origin
        nearest: 'left',
        center: 'center',
        farthest: 'right'
      }
    ),

    /**
     * @private
     * @return {Number} topToOriginY Distance from top edge of canvas to originY of Line.
     */
    _getTopToOriginY: makeEdgeToOriginGetter(
      { // property names
        origin: 'originY',
        axis1: 'y1',
        axis2: 'y2',
        dimension: 'height'
      },
      { // possible values of origin
        nearest: 'top',
        center: 'center',
        farthest: 'bottom'
      }
    ),

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _render: function(ctx, noTransform) {
      ctx.beginPath();

      if (noTransform) {
        //  Line coords are distances from left-top of canvas to origin of line.
        //  To render line in a path-group, we need to translate them to
        //  distances from center of path-group to center of line.
        var cp = this.getCenterPoint();
        ctx.translate(
          cp.x - this.strokeWidth / 2,
          cp.y - this.strokeWidth / 2
        );
      }

      if (!this.strokeDashArray || this.strokeDashArray && supportsLineDash) {
        // move from center (of virtual box) to its left/top corner
        // we can't assume x1, y1 is top left and x2, y2 is bottom right
        var p = this.calcLinePoints();
        ctx.moveTo(p.x1, p.y1);
        ctx.lineTo(p.x2, p.y2);
      }

      ctx.lineWidth = this.strokeWidth;

      // TODO: test this
      // make sure setting "fill" changes color of a line
      // (by copying fillStyle to strokeStyle, since line is stroked, not filled)
      var origStrokeStyle = ctx.strokeStyle;
      ctx.strokeStyle = this.stroke || ctx.fillStyle;
      this.stroke && this._renderStroke(ctx);
      ctx.strokeStyle = origStrokeStyle;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderDashedStroke: function(ctx) {
      var p = this.calcLinePoints();

      ctx.beginPath();
      fabric.util.drawDashedLine(ctx, p.x1, p.y1, p.x2, p.y2, this.strokeDashArray);
      ctx.closePath();
    },

    /**
     * Returns object representation of an instance
     * @methd toObject
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return extend(this.callSuper('toObject', propertiesToInclude), this.calcLinePoints());
    },

    /**
     * Recalculates line points given width and height
     * @private
     */
    calcLinePoints: function() {
      var xMult = this.x1 <= this.x2 ? -1 : 1,
          yMult = this.y1 <= this.y2 ? -1 : 1,
          x1 = (xMult * this.width * 0.5),
          y1 = (yMult * this.height * 0.5),
          x2 = (xMult * this.width * -0.5),
          y2 = (yMult * this.height * -0.5);

      return {
        x1: x1,
        x2: x2,
        y1: y1,
        y2: y2
      };
    },

    /* _TO_SVG_START_ */
    /**
     * Returns SVG representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var markup = this._createBaseSVGMarkup(),
          p = { x1: this.x1, x2: this.x2, y1: this.y1, y2: this.y2 };

      if (!(this.group && this.group.type === 'path-group')) {
        p = this.calcLinePoints();
      }
      markup.push(
        '<line ',
          'x1="', p.x1,
          '" y1="', p.y1,
          '" x2="', p.x2,
          '" y2="', p.y2,
          '" style="', this.getSvgStyles(),
          '" transform="', this.getSvgTransform(),
          this.getSvgTransformMatrix(),
        '"/>\n'
      );

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * Returns complexity of an instance
     * @return {Number} complexity
     */
    complexity: function() {
      return 1;
    }
  });

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by {@link fabric.Line.fromElement})
   * @static
   * @memberOf fabric.Line
   * @see http://www.w3.org/TR/SVG/shapes.html#LineElement
   */
  fabric.Line.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat('x1 y1 x2 y2'.split(' '));

  /**
   * Returns fabric.Line instance from an SVG element
   * @static
   * @memberOf fabric.Line
   * @param {SVGElement} element Element to parse
   * @param {Object} [options] Options object
   * @return {fabric.Line} instance of fabric.Line
   */
  fabric.Line.fromElement = function(element, options) {
    var parsedAttributes = fabric.parseAttributes(element, fabric.Line.ATTRIBUTE_NAMES),
        points = [
          parsedAttributes.x1 || 0,
          parsedAttributes.y1 || 0,
          parsedAttributes.x2 || 0,
          parsedAttributes.y2 || 0
        ];
    return new fabric.Line(points, extend(parsedAttributes, options));
  };
  /* _FROM_SVG_END_ */

  /**
   * Returns fabric.Line instance from an object representation
   * @static
   * @memberOf fabric.Line
   * @param {Object} object Object to create an instance from
   * @return {fabric.Line} instance of fabric.Line
   */
  fabric.Line.fromObject = function(object) {
    var points = [object.x1, object.y1, object.x2, object.y2];
    return new fabric.Line(points, object);
  };

  /**
   * Produces a function that calculates distance from canvas edge to Line origin.
   */
  function makeEdgeToOriginGetter(propertyNames, originValues) {
    var origin = propertyNames.origin,
        axis1 = propertyNames.axis1,
        axis2 = propertyNames.axis2,
        dimension = propertyNames.dimension,
        nearest = originValues.nearest,
        center = originValues.center,
        farthest = originValues.farthest;

    return function() {
      switch (this.get(origin)) {
      case nearest:
        return Math.min(this.get(axis1), this.get(axis2));
      case center:
        return Math.min(this.get(axis1), this.get(axis2)) + (0.5 * this.get(dimension));
      case farthest:
        return Math.max(this.get(axis1), this.get(axis2));
      }
    };

  }

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      pi = Math.PI,
      extend = fabric.util.object.extend;

  if (fabric.Circle) {
    fabric.warn('fabric.Circle is already defined.');
    return;
  }

  /**
   * Circle class
   * @class fabric.Circle
   * @extends fabric.Object
   * @see {@link fabric.Circle#initialize} for constructor definition
   */
  fabric.Circle = fabric.util.createClass(fabric.Object, /** @lends fabric.Circle.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'circle',

    /**
     * Radius of this circle
     * @type Number
     * @default
     */
    radius: 0,

    /**
     * Start angle of the circle, moving clockwise
     * @type Number
     * @default 0
     */
    startAngle: 0,

    /**
     * End angle of the circle
     * @type Number
     * @default 2Pi
     */
    endAngle: pi * 2,

    /**
     * Constructor
     * @param {Object} [options] Options object
     * @return {fabric.Circle} thisArg
     */
    initialize: function(options) {
      options = options || { };

      this.callSuper('initialize', options);
      this.set('radius', options.radius || 0);
      this.startAngle = options.startAngle || this.startAngle;
      this.endAngle = options.endAngle || this.endAngle;
    },

    /**
     * @private
     * @param {String} key
     * @param {Any} value
     * @return {fabric.Circle} thisArg
     */
    _set: function(key, value) {
      this.callSuper('_set', key, value);

      if (key === 'radius') {
        this.setRadius(value);
      }

      return this;
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return extend(this.callSuper('toObject', propertiesToInclude), {
        radius: this.get('radius'),
        startAngle: this.startAngle,
        endAngle: this.endAngle
      });
    },

    /* _TO_SVG_START_ */
    /**
     * Returns svg representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var markup = this._createBaseSVGMarkup(), x = 0, y = 0,
      angle = (this.endAngle - this.startAngle) % ( 2 * pi);

      if (angle === 0) {
        if (this.group && this.group.type === 'path-group') {
          x = this.left + this.radius;
          y = this.top + this.radius;
        }
        markup.push(
          '<circle ',
            'cx="' + x + '" cy="' + y + '" ',
            'r="', this.radius,
            '" style="', this.getSvgStyles(),
            '" transform="', this.getSvgTransform(),
            ' ', this.getSvgTransformMatrix(),
          '"/>\n'
        );
      }
      else {
        var startX = Math.cos(this.startAngle) * this.radius,
            startY = Math.sin(this.startAngle) * this.radius,
            endX = Math.cos(this.endAngle) * this.radius,
            endY = Math.sin(this.endAngle) * this.radius,
            largeFlag = angle > pi ? '1' : '0';

        markup.push(
          '<path d="M ' + startX + ' ' + startY,
          ' A ' + this.radius + ' ' + this.radius,
          ' 0 ', + largeFlag + ' 1', ' ' + endX + ' ' + endY,
          '" style="', this.getSvgStyles(),
          '" transform="', this.getSvgTransform(),
          ' ', this.getSvgTransformMatrix(),
          '"/>\n'
        );
      }

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx context to render on
     * @param {Boolean} [noTransform] When true, context is not transformed
     */
    _render: function(ctx, noTransform) {
      ctx.beginPath();
      ctx.arc(noTransform ? this.left + this.radius : 0,
              noTransform ? this.top + this.radius : 0,
              this.radius,
              this.startAngle,
              this.endAngle, false);
      this._renderFill(ctx);
      this._renderStroke(ctx);
    },

    /**
     * Returns horizontal radius of an object (according to how an object is scaled)
     * @return {Number}
     */
    getRadiusX: function() {
      return this.get('radius') * this.get('scaleX');
    },

    /**
     * Returns vertical radius of an object (according to how an object is scaled)
     * @return {Number}
     */
    getRadiusY: function() {
      return this.get('radius') * this.get('scaleY');
    },

    /**
     * Sets radius of an object (and updates width accordingly)
     * @return {Number}
     */
    setRadius: function(value) {
      this.radius = value;
      this.set('width', value * 2).set('height', value * 2);
    },

    /**
     * Returns complexity of an instance
     * @return {Number} complexity of this instance
     */
    complexity: function() {
      return 1;
    }
  });

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by {@link fabric.Circle.fromElement})
   * @static
   * @memberOf fabric.Circle
   * @see: http://www.w3.org/TR/SVG/shapes.html#CircleElement
   */
  fabric.Circle.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat('cx cy r'.split(' '));

  /**
   * Returns {@link fabric.Circle} instance from an SVG element
   * @static
   * @memberOf fabric.Circle
   * @param {SVGElement} element Element to parse
   * @param {Object} [options] Options object
   * @throws {Error} If value of `r` attribute is missing or invalid
   * @return {fabric.Circle} Instance of fabric.Circle
   */
  fabric.Circle.fromElement = function(element, options) {
    options || (options = { });

    var parsedAttributes = fabric.parseAttributes(element, fabric.Circle.ATTRIBUTE_NAMES);

    if (!isValidRadius(parsedAttributes)) {
      throw new Error('value of `r` attribute is required and can not be negative');
    }

    parsedAttributes.left = parsedAttributes.left || 0;
    parsedAttributes.top = parsedAttributes.top || 0;

    var obj = new fabric.Circle(extend(parsedAttributes, options));

    obj.left -= obj.radius;
    obj.top -= obj.radius;
    return obj;
  };

  /**
   * @private
   */
  function isValidRadius(attributes) {
    return (('radius' in attributes) && (attributes.radius >= 0));
  }
  /* _FROM_SVG_END_ */

  /**
   * Returns {@link fabric.Circle} instance from an object representation
   * @static
   * @memberOf fabric.Circle
   * @param {Object} object Object to create an instance from
   * @return {Object} Instance of fabric.Circle
   */
  fabric.Circle.fromObject = function(object) {
    return new fabric.Circle(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { });

  if (fabric.Triangle) {
    fabric.warn('fabric.Triangle is already defined');
    return;
  }

  /**
   * Triangle class
   * @class fabric.Triangle
   * @extends fabric.Object
   * @return {fabric.Triangle} thisArg
   * @see {@link fabric.Triangle#initialize} for constructor definition
   */
  fabric.Triangle = fabric.util.createClass(fabric.Object, /** @lends fabric.Triangle.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'triangle',

    /**
     * Constructor
     * @param {Object} [options] Options object
     * @return {Object} thisArg
     */
    initialize: function(options) {
      options = options || { };

      this.callSuper('initialize', options);

      this.set('width', options.width || 100)
          .set('height', options.height || 100);
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _render: function(ctx) {
      var widthBy2 = this.width / 2,
          heightBy2 = this.height / 2;

      ctx.beginPath();
      ctx.moveTo(-widthBy2, heightBy2);
      ctx.lineTo(0, -heightBy2);
      ctx.lineTo(widthBy2, heightBy2);
      ctx.closePath();

      this._renderFill(ctx);
      this._renderStroke(ctx);
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderDashedStroke: function(ctx) {
      var widthBy2 = this.width / 2,
          heightBy2 = this.height / 2;

      ctx.beginPath();
      fabric.util.drawDashedLine(ctx, -widthBy2, heightBy2, 0, -heightBy2, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, 0, -heightBy2, widthBy2, heightBy2, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, widthBy2, heightBy2, -widthBy2, heightBy2, this.strokeDashArray);
      ctx.closePath();
    },

    /* _TO_SVG_START_ */
    /**
     * Returns SVG representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var markup = this._createBaseSVGMarkup(),
          widthBy2 = this.width / 2,
          heightBy2 = this.height / 2,
          points = [
            -widthBy2 + ' ' + heightBy2,
            '0 ' + -heightBy2,
            widthBy2 + ' ' + heightBy2
          ]
          .join(',');

      markup.push(
        '<polygon ',
          'points="', points,
          '" style="', this.getSvgStyles(),
          '" transform="', this.getSvgTransform(),
        '"/>'
      );

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * Returns complexity of an instance
     * @return {Number} complexity of this instance
     */
    complexity: function() {
      return 1;
    }
  });

  /**
   * Returns fabric.Triangle instance from an object representation
   * @static
   * @memberOf fabric.Triangle
   * @param {Object} object Object to create an instance from
   * @return {Object} instance of Canvas.Triangle
   */
  fabric.Triangle.fromObject = function(object) {
    return new fabric.Triangle(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      piBy2   = Math.PI * 2,
      extend = fabric.util.object.extend;

  if (fabric.Ellipse) {
    fabric.warn('fabric.Ellipse is already defined.');
    return;
  }

  /**
   * Ellipse class
   * @class fabric.Ellipse
   * @extends fabric.Object
   * @return {fabric.Ellipse} thisArg
   * @see {@link fabric.Ellipse#initialize} for constructor definition
   */
  fabric.Ellipse = fabric.util.createClass(fabric.Object, /** @lends fabric.Ellipse.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'ellipse',

    /**
     * Horizontal radius
     * @type Number
     * @default
     */
    rx:   0,

    /**
     * Vertical radius
     * @type Number
     * @default
     */
    ry:   0,

    /**
     * Constructor
     * @param {Object} [options] Options object
     * @return {fabric.Ellipse} thisArg
     */
    initialize: function(options) {
      options = options || { };

      this.callSuper('initialize', options);

      this.set('rx', options.rx || 0);
      this.set('ry', options.ry || 0);
    },

    /**
     * @private
     * @param {String} key
     * @param {Any} value
     * @return {fabric.Ellipse} thisArg
     */
    _set: function(key, value) {
      this.callSuper('_set', key, value);
      switch (key) {

        case 'rx':
          this.rx = value;
          this.set('width', value * 2);
          break;

        case 'ry':
          this.ry = value;
          this.set('height', value * 2);
          break;

      }
      return this;
    },

    /**
     * Returns horizontal radius of an object (according to how an object is scaled)
     * @return {Number}
     */
    getRx: function() {
      return this.get('rx') * this.get('scaleX');
    },

    /**
     * Returns Vertical radius of an object (according to how an object is scaled)
     * @return {Number}
     */
    getRy: function() {
      return this.get('ry') * this.get('scaleY');
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return extend(this.callSuper('toObject', propertiesToInclude), {
        rx: this.get('rx'),
        ry: this.get('ry')
      });
    },

    /* _TO_SVG_START_ */
    /**
     * Returns svg representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var markup = this._createBaseSVGMarkup(), x = 0, y = 0;
      if (this.group && this.group.type === 'path-group') {
        x = this.left + this.rx;
        y = this.top + this.ry;
      }
      markup.push(
        '<ellipse ',
          'cx="', x, '" cy="', y, '" ',
          'rx="', this.rx,
          '" ry="', this.ry,
          '" style="', this.getSvgStyles(),
          '" transform="', this.getSvgTransform(),
          this.getSvgTransformMatrix(),
        '"/>\n'
      );

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx context to render on
     * @param {Boolean} [noTransform] When true, context is not transformed
     */
    _render: function(ctx, noTransform) {
      ctx.beginPath();
      ctx.save();
      ctx.transform(1, 0, 0, this.ry/this.rx, 0, 0);
      ctx.arc(
        noTransform ? this.left + this.rx : 0,
        noTransform ? (this.top + this.ry) * this.rx/this.ry : 0,
        this.rx,
        0,
        piBy2,
        false);
      ctx.restore();
      this._renderFill(ctx);
      this._renderStroke(ctx);
    },

    /**
     * Returns complexity of an instance
     * @return {Number} complexity
     */
    complexity: function() {
      return 1;
    }
  });

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by {@link fabric.Ellipse.fromElement})
   * @static
   * @memberOf fabric.Ellipse
   * @see http://www.w3.org/TR/SVG/shapes.html#EllipseElement
   */
  fabric.Ellipse.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat('cx cy rx ry'.split(' '));

  /**
   * Returns {@link fabric.Ellipse} instance from an SVG element
   * @static
   * @memberOf fabric.Ellipse
   * @param {SVGElement} element Element to parse
   * @param {Object} [options] Options object
   * @return {fabric.Ellipse}
   */
  fabric.Ellipse.fromElement = function(element, options) {
    options || (options = { });

    var parsedAttributes = fabric.parseAttributes(element, fabric.Ellipse.ATTRIBUTE_NAMES);

    parsedAttributes.left = parsedAttributes.left || 0;
    parsedAttributes.top = parsedAttributes.top || 0;

    var ellipse = new fabric.Ellipse(extend(parsedAttributes, options));

    ellipse.top -= ellipse.ry;
    ellipse.left -= ellipse.rx;
    return ellipse;
  };
  /* _FROM_SVG_END_ */

  /**
   * Returns {@link fabric.Ellipse} instance from an object representation
   * @static
   * @memberOf fabric.Ellipse
   * @param {Object} object Object to create an instance from
   * @return {fabric.Ellipse}
   */
  fabric.Ellipse.fromObject = function(object) {
    return new fabric.Ellipse(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  if (fabric.Rect) {
    console.warn('fabric.Rect is already defined');
    return;
  }

  var stateProperties = fabric.Object.prototype.stateProperties.concat();
  stateProperties.push('rx', 'ry', 'x', 'y');

  /**
   * Rectangle class
   * @class fabric.Rect
   * @extends fabric.Object
   * @return {fabric.Rect} thisArg
   * @see {@link fabric.Rect#initialize} for constructor definition
   */
  fabric.Rect = fabric.util.createClass(fabric.Object, /** @lends fabric.Rect.prototype */ {

    /**
     * List of properties to consider when checking if state of an object is changed ({@link fabric.Object#hasStateChanged})
     * as well as for history (undo/redo) purposes
     * @type Array
     */
    stateProperties: stateProperties,

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'rect',

    /**
     * Horizontal border radius
     * @type Number
     * @default
     */
    rx:   0,

    /**
     * Vertical border radius
     * @type Number
     * @default
     */
    ry:   0,

    /**
     * Used to specify dash pattern for stroke on this object
     * @type Array
     */
    strokeDashArray: null,

    /**
     * Constructor
     * @param {Object} [options] Options object
     * @return {Object} thisArg
     */
    initialize: function(options) {
      options = options || { };

      this.callSuper('initialize', options);
      this._initRxRy();

    },

    /**
     * Initializes rx/ry attributes
     * @private
     */
    _initRxRy: function() {
      if (this.rx && !this.ry) {
        this.ry = this.rx;
      }
      else if (this.ry && !this.rx) {
        this.rx = this.ry;
      }
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _render: function(ctx, noTransform) {

      // optimize 1x1 case (used in spray brush)
      if (this.width === 1 && this.height === 1) {
        ctx.fillRect(0, 0, 1, 1);
        return;
      }

      var rx = this.rx ? Math.min(this.rx, this.width / 2) : 0,
          ry = this.ry ? Math.min(this.ry, this.height / 2) : 0,
          w = this.width,
          h = this.height,
          x = noTransform ? this.left : -this.width / 2,
          y = noTransform ? this.top : -this.height / 2,
          isRounded = rx !== 0 || ry !== 0,
          k = 1 - 0.5522847498 /* "magic number" for bezier approximations of arcs (http://itc.ktu.lt/itc354/Riskus354.pdf) */;

      ctx.beginPath();

      ctx.moveTo(x + rx, y);

      ctx.lineTo(x + w - rx, y);
      isRounded && ctx.bezierCurveTo(x + w - k * rx, y, x + w, y + k * ry, x + w, y + ry);

      ctx.lineTo(x + w, y + h - ry);
      isRounded && ctx.bezierCurveTo(x + w, y + h - k * ry, x + w - k * rx, y + h, x + w - rx, y + h);

      ctx.lineTo(x + rx, y + h);
      isRounded && ctx.bezierCurveTo(x + k * rx, y + h, x, y + h - k * ry, x, y + h - ry);

      ctx.lineTo(x, y + ry);
      isRounded && ctx.bezierCurveTo(x, y + k * ry, x + k * rx, y, x + rx, y);

      ctx.closePath();

      this._renderFill(ctx);
      this._renderStroke(ctx);
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderDashedStroke: function(ctx) {
      var x = -this.width / 2,
          y = -this.height / 2,
          w = this.width,
          h = this.height;

      ctx.beginPath();
      fabric.util.drawDashedLine(ctx, x, y, x + w, y, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x + w, y, x + w, y + h, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x + w, y + h, x, y + h, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x, y + h, x, y, this.strokeDashArray);
      ctx.closePath();
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      var object = extend(this.callSuper('toObject', propertiesToInclude), {
        rx: this.get('rx') || 0,
        ry: this.get('ry') || 0
      });
      if (!this.includeDefaultValues) {
        this._removeDefaultValues(object);
      }
      return object;
    },

    /* _TO_SVG_START_ */
    /**
     * Returns svg representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var markup = this._createBaseSVGMarkup(), x = this.left, y = this.top;
      if (!(this.group && this.group.type === 'path-group')) {
        x = -this.width / 2;
        y = -this.height / 2;
      }
      markup.push(
        '<rect ',
          'x="', x, '" y="', y,
          '" rx="', this.get('rx'), '" ry="', this.get('ry'),
          '" width="', this.width, '" height="', this.height,
          '" style="', this.getSvgStyles(),
          '" transform="', this.getSvgTransform(),
          this.getSvgTransformMatrix(),
        '"/>\n');

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * Returns complexity of an instance
     * @return {Number} complexity
     */
    complexity: function() {
      return 1;
    }
  });

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by `fabric.Rect.fromElement`)
   * @static
   * @memberOf fabric.Rect
   * @see: http://www.w3.org/TR/SVG/shapes.html#RectElement
   */
  fabric.Rect.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat('x y rx ry width height'.split(' '));

  /**
   * Returns {@link fabric.Rect} instance from an SVG element
   * @static
   * @memberOf fabric.Rect
   * @param {SVGElement} element Element to parse
   * @param {Object} [options] Options object
   * @return {fabric.Rect} Instance of fabric.Rect
   */
  fabric.Rect.fromElement = function(element, options) {
    if (!element) {
      return null;
    }
    options = options || { };

    var parsedAttributes = fabric.parseAttributes(element, fabric.Rect.ATTRIBUTE_NAMES);

    parsedAttributes.left = parsedAttributes.left || 0;
    parsedAttributes.top  = parsedAttributes.top  || 0;
    var rect = new fabric.Rect(extend((options ? fabric.util.object.clone(options) : { }), parsedAttributes));
    rect.visible = rect.width > 0 && rect.height > 0;
    return rect;
  };
  /* _FROM_SVG_END_ */

  /**
   * Returns {@link fabric.Rect} instance from an object representation
   * @static
   * @memberOf fabric.Rect
   * @param {Object} object Object to create an instance from
   * @return {Object} instance of fabric.Rect
   */
  fabric.Rect.fromObject = function(object) {
    return new fabric.Rect(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { });

  if (fabric.Polyline) {
    fabric.warn('fabric.Polyline is already defined');
    return;
  }

  /**
   * Polyline class
   * @class fabric.Polyline
   * @extends fabric.Object
   * @see {@link fabric.Polyline#initialize} for constructor definition
   */
  fabric.Polyline = fabric.util.createClass(fabric.Object, /** @lends fabric.Polyline.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'polyline',

    /**
     * Points array
     * @type Array
     * @default
     */
    points: null,

    /**
     * Minimum X from points values, necessary to offset points
     * @type Number
     * @default
     */
    minX: 0,

    /**
     * Minimum Y from points values, necessary to offset points
     * @type Number
     * @default
     */
    minY: 0,

    /**
     * Constructor
     * @param {Array} points Array of points (where each point is an object with x and y)
     * @param {Object} [options] Options object
     * @param {Boolean} [skipOffset] Whether points offsetting should be skipped
     * @return {fabric.Polyline} thisArg
     * @example
     * var poly = new fabric.Polyline([
     *     { x: 10, y: 10 },
     *     { x: 50, y: 30 },
     *     { x: 40, y: 70 },
     *     { x: 60, y: 50 },
     *     { x: 100, y: 150 },
     *     { x: 40, y: 100 }
     *   ], {
     *   stroke: 'red',
     *   left: 100,
     *   top: 100
     * });
     */
    initialize: function(points, options) {
      return fabric.Polygon.prototype.initialize.call(this, points, options);
    },

    /**
     * @private
     */
    _calcDimensions: function() {
      return fabric.Polygon.prototype._calcDimensions.call(this);
    },

    /**
     * @private
     */
    _applyPointOffset: function() {
      return fabric.Polygon.prototype._applyPointOffset.call(this);
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} Object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return fabric.Polygon.prototype.toObject.call(this, propertiesToInclude);
    },

    /* _TO_SVG_START_ */
    /**
     * Returns SVG representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      return fabric.Polygon.prototype.toSVG.call(this, reviver);
    },
    /* _TO_SVG_END_ */

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _render: function(ctx) {
      if (!fabric.Polygon.prototype.commonRender.call(this, ctx)) {
        return;
      }
      this._renderFill(ctx);
      this._renderStroke(ctx);
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderDashedStroke: function(ctx) {
      var p1, p2;

      ctx.beginPath();
      for (var i = 0, len = this.points.length; i < len; i++) {
        p1 = this.points[i];
        p2 = this.points[i + 1] || p1;
        fabric.util.drawDashedLine(ctx, p1.x, p1.y, p2.x, p2.y, this.strokeDashArray);
      }
    },

    /**
     * Returns complexity of an instance
     * @return {Number} complexity of this instance
     */
    complexity: function() {
      return this.get('points').length;
    }
  });

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by {@link fabric.Polyline.fromElement})
   * @static
   * @memberOf fabric.Polyline
   * @see: http://www.w3.org/TR/SVG/shapes.html#PolylineElement
   */
  fabric.Polyline.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat();

  /**
   * Returns fabric.Polyline instance from an SVG element
   * @static
   * @memberOf fabric.Polyline
   * @param {SVGElement} element Element to parse
   * @param {Object} [options] Options object
   * @return {fabric.Polyline} Instance of fabric.Polyline
   */
  fabric.Polyline.fromElement = function(element, options) {
    if (!element) {
      return null;
    }
    options || (options = { });

    var points = fabric.parsePointsAttribute(element.getAttribute('points')),
        parsedAttributes = fabric.parseAttributes(element, fabric.Polyline.ATTRIBUTE_NAMES);

    return new fabric.Polyline(points, fabric.util.object.extend(parsedAttributes, options));
  };
  /* _FROM_SVG_END_ */

  /**
   * Returns fabric.Polyline instance from an object representation
   * @static
   * @memberOf fabric.Polyline
   * @param {Object} object Object to create an instance from
   * @return {fabric.Polyline} Instance of fabric.Polyline
   */
  fabric.Polyline.fromObject = function(object) {
    var points = object.points;
    return new fabric.Polyline(points, object, true);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend,
      min = fabric.util.array.min,
      max = fabric.util.array.max,
      toFixed = fabric.util.toFixed;

  if (fabric.Polygon) {
    fabric.warn('fabric.Polygon is already defined');
    return;
  }

  /**
   * Polygon class
   * @class fabric.Polygon
   * @extends fabric.Object
   * @see {@link fabric.Polygon#initialize} for constructor definition
   */
  fabric.Polygon = fabric.util.createClass(fabric.Object, /** @lends fabric.Polygon.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'polygon',

    /**
     * Points array
     * @type Array
     * @default
     */
    points: null,

    /**
     * Minimum X from points values, necessary to offset points
     * @type Number
     * @default
     */
    minX: 0,

    /**
     * Minimum Y from points values, necessary to offset points
     * @type Number
     * @default
     */
    minY: 0,

    /**
     * Constructor
     * @param {Array} points Array of points
     * @param {Object} [options] Options object
     * @return {fabric.Polygon} thisArg
     */
    initialize: function(points, options) {
      options = options || { };
      this.points = points || [ ];
      this.callSuper('initialize', options);
      this._calcDimensions();
      if (!('top' in options)) {
        this.top = this.minY;
      }
      if (!('left' in options)) {
        this.left = this.minX;
      }
    },

    /**
     * @private
     */
    _calcDimensions: function() {

      var points = this.points,
          minX = min(points, 'x'),
          minY = min(points, 'y'),
          maxX = max(points, 'x'),
          maxY = max(points, 'y');

      this.width = (maxX - minX) || 0;
      this.height = (maxY - minY) || 0;

      this.minX = minX || 0,
      this.minY = minY || 0;
    },

    /**
     * @private
     */
    _applyPointOffset: function() {
      // change points to offset polygon into a bounding box
      // executed one time
      this.points.forEach(function(p) {
        p.x -= (this.minX + this.width / 2);
        p.y -= (this.minY + this.height / 2);
      }, this);
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} Object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return extend(this.callSuper('toObject', propertiesToInclude), {
        points: this.points.concat()
      });
    },

    /* _TO_SVG_START_ */
    /**
     * Returns svg representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var points = [],
          markup = this._createBaseSVGMarkup();

      for (var i = 0, len = this.points.length; i < len; i++) {
        points.push(toFixed(this.points[i].x, 2), ',', toFixed(this.points[i].y, 2), ' ');
      }

      markup.push(
        '<', this.type, ' ',
          'points="', points.join(''),
          '" style="', this.getSvgStyles(),
          '" transform="', this.getSvgTransform(),
          ' ', this.getSvgTransformMatrix(),
        '"/>\n'
      );

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _render: function(ctx) {
      if (!this.commonRender(ctx)) {
        return;
      }
      this._renderFill(ctx);
      if (this.stroke || this.strokeDashArray) {
        ctx.closePath();
        this._renderStroke(ctx);
      }
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    commonRender: function(ctx) {
      var point, len = this.points.length;

      if (!len || isNaN(this.points[len - 1].y)) {
        // do not draw if no points or odd points
        // NaN comes from parseFloat of a empty string in parser
        return false;
      }

      ctx.beginPath();

      if (this._applyPointOffset) {
        if (!(this.group && this.group.type === 'path-group')) {
          this._applyPointOffset();
        }
        this._applyPointOffset = null;
      }

      ctx.moveTo(this.points[0].x, this.points[0].y);
      for (var i = 0; i < len; i++) {
        point = this.points[i];
        ctx.lineTo(point.x, point.y);
      }
      return true;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderDashedStroke: function(ctx) {
      fabric.Polyline.prototype._renderDashedStroke.call(this, ctx);
      ctx.closePath();
    },

    /**
     * Returns complexity of an instance
     * @return {Number} complexity of this instance
     */
    complexity: function() {
      return this.points.length;
    }
  });

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by `fabric.Polygon.fromElement`)
   * @static
   * @memberOf fabric.Polygon
   * @see: http://www.w3.org/TR/SVG/shapes.html#PolygonElement
   */
  fabric.Polygon.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat();

  /**
   * Returns {@link fabric.Polygon} instance from an SVG element
   * @static
   * @memberOf fabric.Polygon
   * @param {SVGElement} element Element to parse
   * @param {Object} [options] Options object
   * @return {fabric.Polygon} Instance of fabric.Polygon
   */
  fabric.Polygon.fromElement = function(element, options) {
    if (!element) {
      return null;
    }

    options || (options = { });

    var points = fabric.parsePointsAttribute(element.getAttribute('points')),
        parsedAttributes = fabric.parseAttributes(element, fabric.Polygon.ATTRIBUTE_NAMES);

    return new fabric.Polygon(points, extend(parsedAttributes, options));
  };
  /* _FROM_SVG_END_ */

  /**
   * Returns fabric.Polygon instance from an object representation
   * @static
   * @memberOf fabric.Polygon
   * @param {Object} object Object to create an instance from
   * @return {fabric.Polygon} Instance of fabric.Polygon
   */
  fabric.Polygon.fromObject = function(object) {
    return new fabric.Polygon(object.points, object, true);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      min = fabric.util.array.min,
      max = fabric.util.array.max,
      extend = fabric.util.object.extend,
      _toString = Object.prototype.toString,
      drawArc = fabric.util.drawArc,
      commandLengths = {
        m: 2,
        l: 2,
        h: 1,
        v: 1,
        c: 6,
        s: 4,
        q: 4,
        t: 2,
        a: 7
      },
      repeatedCommands = {
        m: 'l',
        M: 'L'
      };

  if (fabric.Path) {
    fabric.warn('fabric.Path is already defined');
    return;
  }

  /**
   * Path class
   * @class fabric.Path
   * @extends fabric.Object
   * @tutorial {@link http://fabricjs.com/fabric-intro-part-1/#path_and_pathgroup}
   * @see {@link fabric.Path#initialize} for constructor definition
   */
  fabric.Path = fabric.util.createClass(fabric.Object, /** @lends fabric.Path.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'path',

    /**
     * Array of path points
     * @type Array
     * @default
     */
    path: null,

    /**
     * Minimum X from points values, necessary to offset points
     * @type Number
     * @default
     */
    minX: 0,

    /**
     * Minimum Y from points values, necessary to offset points
     * @type Number
     * @default
     */
    minY: 0,

    /**
     * Constructor
     * @param {Array|String} path Path data (sequence of coordinates and corresponding "command" tokens)
     * @param {Object} [options] Options object
     * @return {fabric.Path} thisArg
     */
    initialize: function(path, options) {
      options = options || { };

      this.setOptions(options);

      if (!path) {
        throw new Error('`path` argument is required');
      }

      var fromArray = _toString.call(path) === '[object Array]';

      this.path = fromArray
        ? path
        // one of commands (m,M,l,L,q,Q,c,C,etc.) followed by non-command characters (i.e. command values)
        : path.match && path.match(/[mzlhvcsqta][^mzlhvcsqta]*/gi);

      if (!this.path) {
        return;
      }

      if (!fromArray) {
        this.path = this._parsePath();
      }

      this._setPositionDimensions();

      if (options.sourcePath) {
        this.setSourcePath(options.sourcePath);
      }
    },

    /**
     * @private
     */
    _setPositionDimensions: function() {
      var calcDim = this._parseDimensions();

      this.minX = calcDim.left;
      this.minY = calcDim.top;
      this.width = calcDim.width;
      this.height = calcDim.height;

      calcDim.left += this.originX === 'center'
        ? this.width / 2
        : this.originX === 'right'
          ? this.width
          : 0;

      calcDim.top += this.originY === 'center'
        ? this.height / 2
        : this.originY === 'bottom'
          ? this.height
          : 0;

      this.top = this.top || calcDim.top;
      this.left = this.left || calcDim.left;

      this.pathOffset = this.pathOffset || {
        x: this.minX + this.width / 2,
        y: this.minY + this.height / 2
      };
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx context to render path on
     */
    _render: function(ctx) {
      var current, // current instruction
          previous = null,
          subpathStartX = 0,
          subpathStartY = 0,
          x = 0, // current x
          y = 0, // current y
          controlX = 0, // current control point x
          controlY = 0, // current control point y
          tempX,
          tempY,
          l = -this.pathOffset.x,
          t = -this.pathOffset.y;

      if (this.group && this.group.type === 'path-group') {
        l = 0;
        t = 0;
      }

      ctx.beginPath();

      for (var i = 0, len = this.path.length; i < len; ++i) {

        current = this.path[i];

        switch (current[0]) { // first letter

          case 'l': // lineto, relative
            x += current[1];
            y += current[2];
            ctx.lineTo(x + l, y + t);
            break;

          case 'L': // lineto, absolute
            x = current[1];
            y = current[2];
            ctx.lineTo(x + l, y + t);
            break;

          case 'h': // horizontal lineto, relative
            x += current[1];
            ctx.lineTo(x + l, y + t);
            break;

          case 'H': // horizontal lineto, absolute
            x = current[1];
            ctx.lineTo(x + l, y + t);
            break;

          case 'v': // vertical lineto, relative
            y += current[1];
            ctx.lineTo(x + l, y + t);
            break;

          case 'V': // verical lineto, absolute
            y = current[1];
            ctx.lineTo(x + l, y + t);
            break;

          case 'm': // moveTo, relative
            x += current[1];
            y += current[2];
            subpathStartX = x;
            subpathStartY = y;
            ctx.moveTo(x + l, y + t);
            break;

          case 'M': // moveTo, absolute
            x = current[1];
            y = current[2];
            subpathStartX = x;
            subpathStartY = y;
            ctx.moveTo(x + l, y + t);
            break;

          case 'c': // bezierCurveTo, relative
            tempX = x + current[5];
            tempY = y + current[6];
            controlX = x + current[3];
            controlY = y + current[4];
            ctx.bezierCurveTo(
              x + current[1] + l, // x1
              y + current[2] + t, // y1
              controlX + l, // x2
              controlY + t, // y2
              tempX + l,
              tempY + t
            );
            x = tempX;
            y = tempY;
            break;

          case 'C': // bezierCurveTo, absolute
            x = current[5];
            y = current[6];
            controlX = current[3];
            controlY = current[4];
            ctx.bezierCurveTo(
              current[1] + l,
              current[2] + t,
              controlX + l,
              controlY + t,
              x + l,
              y + t
            );
            break;

          case 's': // shorthand cubic bezierCurveTo, relative

            // transform to absolute x,y
            tempX = x + current[3];
            tempY = y + current[4];

            if (previous[0].match(/[CcSs]/) === null) {
              // If there is no previous command or if the previous command was not a C, c, S, or s,
              // the control point is coincident with the current point
              controlX = x;
              controlY = y;
            }
            else {
              // calculate reflection of previous control points
              controlX = 2 * x - controlX;
              controlY = 2 * y - controlY;
            }

            ctx.bezierCurveTo(
              controlX + l,
              controlY + t,
              x + current[1] + l,
              y + current[2] + t,
              tempX + l,
              tempY + t
            );
            // set control point to 2nd one of this command
            // "... the first control point is assumed to be
            // the reflection of the second control point on
            // the previous command relative to the current point."
            controlX = x + current[1];
            controlY = y + current[2];

            x = tempX;
            y = tempY;
            break;

          case 'S': // shorthand cubic bezierCurveTo, absolute
            tempX = current[3];
            tempY = current[4];
            if (previous[0].match(/[CcSs]/) === null) {
              // If there is no previous command or if the previous command was not a C, c, S, or s,
              // the control point is coincident with the current point
              controlX = x;
              controlY = y;
            }
            else {
              // calculate reflection of previous control points
              controlX = 2 * x - controlX;
              controlY = 2 * y - controlY;
            }
            ctx.bezierCurveTo(
              controlX + l,
              controlY + t,
              current[1] + l,
              current[2] + t,
              tempX + l,
              tempY + t
            );
            x = tempX;
            y = tempY;

            // set control point to 2nd one of this command
            // "... the first control point is assumed to be
            // the reflection of the second control point on
            // the previous command relative to the current point."
            controlX = current[1];
            controlY = current[2];

            break;

          case 'q': // quadraticCurveTo, relative
            // transform to absolute x,y
            tempX = x + current[3];
            tempY = y + current[4];

            controlX = x + current[1];
            controlY = y + current[2];

            ctx.quadraticCurveTo(
              controlX + l,
              controlY + t,
              tempX + l,
              tempY + t
            );
            x = tempX;
            y = tempY;
            break;

          case 'Q': // quadraticCurveTo, absolute
            tempX = current[3];
            tempY = current[4];

            ctx.quadraticCurveTo(
              current[1] + l,
              current[2] + t,
              tempX + l,
              tempY + t
            );
            x = tempX;
            y = tempY;
            controlX = current[1];
            controlY = current[2];
            break;

          case 't': // shorthand quadraticCurveTo, relative

            // transform to absolute x,y
            tempX = x + current[1];
            tempY = y + current[2];

            if (previous[0].match(/[QqTt]/) === null) {
              // If there is no previous command or if the previous command was not a Q, q, T or t,
              // assume the control point is coincident with the current point
              controlX = x;
              controlY = y;
            }
            else {
              // calculate reflection of previous control point
              controlX = 2 * x - controlX;
              controlY = 2 * y - controlY;
            }

            ctx.quadraticCurveTo(
              controlX + l,
              controlY + t,
              tempX + l,
              tempY + t
            );
            x = tempX;
            y = tempY;

            break;

          case 'T':
            tempX = current[1];
            tempY = current[2];

            if (previous[0].match(/[QqTt]/) === null) {
              // If there is no previous command or if the previous command was not a Q, q, T or t,
              // assume the control point is coincident with the current point
              controlX = x;
              controlY = y;
            }
            else {
              // calculate reflection of previous control point
              controlX = 2 * x - controlX;
              controlY = 2 * y - controlY;
            }
            ctx.quadraticCurveTo(
              controlX + l,
              controlY + t,
              tempX + l,
              tempY + t
            );
            x = tempX;
            y = tempY;
            break;

          case 'a':
            // TODO: optimize this
            drawArc(ctx, x + l, y + t, [
              current[1],
              current[2],
              current[3],
              current[4],
              current[5],
              current[6] + x + l,
              current[7] + y + t
            ]);
            x += current[6];
            y += current[7];
            break;

          case 'A':
            // TODO: optimize this
            drawArc(ctx, x + l, y + t, [
              current[1],
              current[2],
              current[3],
              current[4],
              current[5],
              current[6] + l,
              current[7] + t
            ]);
            x = current[6];
            y = current[7];
            break;

          case 'z':
          case 'Z':
            x = subpathStartX;
            y = subpathStartY;
            ctx.closePath();
            break;
        }
        previous = current;
      }
      this._renderFill(ctx);
      this._renderStroke(ctx);
    },

    /**
     * Returns string representation of an instance
     * @return {String} string representation of an instance
     */
    toString: function() {
      return '#<fabric.Path (' + this.complexity() +
        '): { "top": ' + this.top + ', "left": ' + this.left + ' }>';
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      var o = extend(this.callSuper('toObject', propertiesToInclude), {
        path: this.path.map(function(item) { return item.slice() }),
        pathOffset: this.pathOffset
      });
      if (this.sourcePath) {
        o.sourcePath = this.sourcePath;
      }
      if (this.transformMatrix) {
        o.transformMatrix = this.transformMatrix;
      }
      return o;
    },

    /**
     * Returns dataless object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toDatalessObject: function(propertiesToInclude) {
      var o = this.toObject(propertiesToInclude);
      if (this.sourcePath) {
        o.path = this.sourcePath;
      }
      delete o.sourcePath;
      return o;
    },

    /* _TO_SVG_START_ */
    /**
     * Returns svg representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var chunks = [],
          markup = this._createBaseSVGMarkup(), addTransform = '';

      for (var i = 0, len = this.path.length; i < len; i++) {
        chunks.push(this.path[i].join(' '));
      }
      var path = chunks.join(' ');
      if (!(this.group && this.group.type === 'path-group')) {
        addTransform = ' translate(' + (-this.pathOffset.x) + ', ' + (-this.pathOffset.y) + ') ';
      }
      markup.push(
        //jscs:disable validateIndentation
        '<path ',
          'd="', path,
          '" style="', this.getSvgStyles(),
          '" transform="', this.getSvgTransform(), addTransform,
          this.getSvgTransformMatrix(), '" stroke-linecap="round" ',
        '/>\n'
        //jscs:enable validateIndentation
      );

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * Returns number representation of an instance complexity
     * @return {Number} complexity of this instance
     */
    complexity: function() {
      return this.path.length;
    },

    /**
     * @private
     */
    _parsePath: function() {
      var result = [ ],
          coords = [ ],
          currentPath,
          parsed,
          re = /([-+]?((\d+\.\d+)|((\d+)|(\.\d+)))(?:e[-+]?\d+)?)/ig,
          match,
          coordsStr;

      for (var i = 0, coordsParsed, len = this.path.length; i < len; i++) {
        currentPath = this.path[i];

        coordsStr = currentPath.slice(1).trim();
        coords.length = 0;

        while ((match = re.exec(coordsStr))) {
          coords.push(match[0]);
        }

        coordsParsed = [ currentPath.charAt(0) ];

        for (var j = 0, jlen = coords.length; j < jlen; j++) {
          parsed = parseFloat(coords[j]);
          if (!isNaN(parsed)) {
            coordsParsed.push(parsed);
          }
        }

        var command = coordsParsed[0],
            commandLength = commandLengths[command.toLowerCase()],
            repeatedCommand = repeatedCommands[command] || command;

        if (coordsParsed.length - 1 > commandLength) {
          for (var k = 1, klen = coordsParsed.length; k < klen; k += commandLength) {
            result.push([ command ].concat(coordsParsed.slice(k, k + commandLength)));
            command = repeatedCommand;
          }
        }
        else {
          result.push(coordsParsed);
        }
      }

      return result;
    },

    /**
     * @private
     */
    _parseDimensions: function() {

      var aX = [],
          aY = [],
          current, // current instruction
          previous = null,
          subpathStartX = 0,
          subpathStartY = 0,
          x = 0, // current x
          y = 0, // current y
          controlX = 0, // current control point x
          controlY = 0, // current control point y
          tempX,
          tempY,
          bounds;

      for (var i = 0, len = this.path.length; i < len; ++i) {

        current = this.path[i];

        switch (current[0]) { // first letter

          case 'l': // lineto, relative
            x += current[1];
            y += current[2];
            bounds = [ ];
            break;

          case 'L': // lineto, absolute
            x = current[1];
            y = current[2];
            bounds = [ ];
            break;

          case 'h': // horizontal lineto, relative
            x += current[1];
            bounds = [ ];
            break;

          case 'H': // horizontal lineto, absolute
            x = current[1];
            bounds = [ ];
            break;

          case 'v': // vertical lineto, relative
            y += current[1];
            bounds = [ ];
            break;

          case 'V': // verical lineto, absolute
            y = current[1];
            bounds = [ ];
            break;

          case 'm': // moveTo, relative
            x += current[1];
            y += current[2];
            subpathStartX = x;
            subpathStartY = y;
            bounds = [ ];
            break;

          case 'M': // moveTo, absolute
            x = current[1];
            y = current[2];
            subpathStartX = x;
            subpathStartY = y;
            bounds = [ ];
            break;

          case 'c': // bezierCurveTo, relative
            tempX = x + current[5];
            tempY = y + current[6];
            controlX = x + current[3];
            controlY = y + current[4];
            bounds = fabric.util.getBoundsOfCurve(x, y,
              x + current[1], // x1
              y + current[2], // y1
              controlX, // x2
              controlY, // y2
              tempX,
              tempY
            );
            x = tempX;
            y = tempY;
            break;

          case 'C': // bezierCurveTo, absolute
            x = current[5];
            y = current[6];
            controlX = current[3];
            controlY = current[4];
            bounds = fabric.util.getBoundsOfCurve(x, y,
              current[1],
              current[2],
              controlX,
              controlY,
              x,
              y
            );
            break;

          case 's': // shorthand cubic bezierCurveTo, relative

            // transform to absolute x,y
            tempX = x + current[3];
            tempY = y + current[4];

            if (previous[0].match(/[CcSs]/) === null) {
              // If there is no previous command or if the previous command was not a C, c, S, or s,
              // the control point is coincident with the current point
              controlX = x;
              controlY = y;
            }
            else {
              // calculate reflection of previous control points
              controlX = 2 * x - controlX;
              controlY = 2 * y - controlY;
            }

            bounds = fabric.util.getBoundsOfCurve(x, y,
              controlX,
              controlY,
              x + current[1],
              y + current[2],
              tempX,
              tempY
            );
            // set control point to 2nd one of this command
            // "... the first control point is assumed to be
            // the reflection of the second control point on
            // the previous command relative to the current point."
            controlX = x + current[1];
            controlY = y + current[2];
            x = tempX;
            y = tempY;
            break;

          case 'S': // shorthand cubic bezierCurveTo, absolute
            tempX = current[3];
            tempY = current[4];
            if (previous[0].match(/[CcSs]/) === null) {
              // If there is no previous command or if the previous command was not a C, c, S, or s,
              // the control point is coincident with the current point
              controlX = x;
              controlY = y;
            }
            else {
              // calculate reflection of previous control points
              controlX = 2 * x - controlX;
              controlY = 2 * y - controlY;
            }
            bounds = fabric.util.getBoundsOfCurve(x, y,
              controlX,
              controlY,
              current[1],
              current[2],
              tempX,
              tempY
            );
            x = tempX;
            y = tempY;
            // set control point to 2nd one of this command
            // "... the first control point is assumed to be
            // the reflection of the second control point on
            // the previous command relative to the current point."
            controlX = current[1];
            controlY = current[2];
            break;

          case 'q': // quadraticCurveTo, relative
            // transform to absolute x,y
            tempX = x + current[3];
            tempY = y + current[4];
            controlX = x + current[1];
            controlY = y + current[2];
            bounds = fabric.util.getBoundsOfCurve(x, y,
              controlX,
              controlY,
              controlX,
              controlY,
              tempX,
              tempY
            );
            x = tempX;
            y = tempY;
            break;

          case 'Q': // quadraticCurveTo, absolute
            controlX = current[1];
            controlY = current[2];
            bounds = fabric.util.getBoundsOfCurve(x, y,
              controlX,
              controlY,
              controlX,
              controlY,
              current[3],
              current[4]
            );
            x = current[3];
            y = current[4];
            break;

          case 't': // shorthand quadraticCurveTo, relative
            // transform to absolute x,y
            tempX = x + current[1];
            tempY = y + current[2];
            if (previous[0].match(/[QqTt]/) === null) {
              // If there is no previous command or if the previous command was not a Q, q, T or t,
              // assume the control point is coincident with the current point
              controlX = x;
              controlY = y;
            }
            else {
              // calculate reflection of previous control point
              controlX = 2 * x - controlX;
              controlY = 2 * y - controlY;
            }

            bounds = fabric.util.getBoundsOfCurve(x, y,
              controlX,
              controlY,
              controlX,
              controlY,
              tempX,
              tempY
            );
            x = tempX;
            y = tempY;

            break;

          case 'T':
            tempX = current[1];
            tempY = current[2];

            if (previous[0].match(/[QqTt]/) === null) {
              // If there is no previous command or if the previous command was not a Q, q, T or t,
              // assume the control point is coincident with the current point
              controlX = x;
              controlY = y;
            }
            else {
              // calculate reflection of previous control point
              controlX = 2 * x - controlX;
              controlY = 2 * y - controlY;
            }
            bounds = fabric.util.getBoundsOfCurve(x, y,
              controlX,
              controlY,
              controlX,
              controlY,
              tempX,
              tempY
            );
            x = tempX;
            y = tempY;
            break;

          case 'a':
            // TODO: optimize this
            bounds = fabric.util.getBoundsOfArc(x, y,
              current[1],
              current[2],
              current[3],
              current[4],
              current[5],
              current[6] + x,
              current[7] + y
            );
            x += current[6];
            y += current[7];
            break;

          case 'A':
            // TODO: optimize this
            bounds = fabric.util.getBoundsOfArc(x, y,
              current[1],
              current[2],
              current[3],
              current[4],
              current[5],
              current[6],
              current[7]
            );
            x = current[6];
            y = current[7];
            break;

          case 'z':
          case 'Z':
            x = subpathStartX;
            y = subpathStartY;
            break;
        }
        previous = current;
        bounds.forEach(function (point) {
          aX.push(point.x);
          aY.push(point.y);
        });
        aX.push(x);
        aY.push(y);
      }

      var minX = min(aX),
          minY = min(aY),
          maxX = max(aX),
          maxY = max(aY),
          deltaX = maxX - minX,
          deltaY = maxY - minY,

          o = {
            left: minX,
            top: minY,
            width: deltaX,
            height: deltaY
          };

      return o;
    }
  });

  /**
   * Creates an instance of fabric.Path from an object
   * @static
   * @memberOf fabric.Path
   * @param {Object} object
   * @param {Function} callback Callback to invoke when an fabric.Path instance is created
   */
  fabric.Path.fromObject = function(object, callback) {
    if (typeof object.path === 'string') {
      fabric.loadSVGFromURL(object.path, function (elements) {
        var path = elements[0],
            pathUrl = object.path;

        delete object.path;

        fabric.util.object.extend(path, object);
        path.setSourcePath(pathUrl);

        callback(path);
      });
    }
    else {
      callback(new fabric.Path(object.path, object));
    }
  };

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by `fabric.Path.fromElement`)
   * @static
   * @memberOf fabric.Path
   * @see http://www.w3.org/TR/SVG/paths.html#PathElement
   */
  fabric.Path.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat(['d']);

  /**
   * Creates an instance of fabric.Path from an SVG <path> element
   * @static
   * @memberOf fabric.Path
   * @param {SVGElement} element to parse
   * @param {Function} callback Callback to invoke when an fabric.Path instance is created
   * @param {Object} [options] Options object
   */
  fabric.Path.fromElement = function(element, callback, options) {
    var parsedAttributes = fabric.parseAttributes(element, fabric.Path.ATTRIBUTE_NAMES);
    callback && callback(new fabric.Path(parsedAttributes.d, extend(parsedAttributes, options)));
  };
  /* _FROM_SVG_END_ */

  /**
   * Indicates that instances of this type are async
   * @static
   * @memberOf fabric.Path
   * @type Boolean
   * @default
   */
  fabric.Path.async = true;

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend,
      invoke = fabric.util.array.invoke,
      parentToObject = fabric.Object.prototype.toObject;

  if (fabric.PathGroup) {
    fabric.warn('fabric.PathGroup is already defined');
    return;
  }

  /**
   * Path group class
   * @class fabric.PathGroup
   * @extends fabric.Path
   * @tutorial {@link http://fabricjs.com/fabric-intro-part-1/#path_and_pathgroup}
   * @see {@link fabric.PathGroup#initialize} for constructor definition
   */
  fabric.PathGroup = fabric.util.createClass(fabric.Path, /** @lends fabric.PathGroup.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'path-group',

    /**
     * Fill value
     * @type String
     * @default
     */
    fill: '',

    /**
     * Constructor
     * @param {Array} paths
     * @param {Object} [options] Options object
     * @return {fabric.PathGroup} thisArg
     */
    initialize: function(paths, options) {

      options = options || { };
      this.paths = paths || [ ];

      for (var i = this.paths.length; i--;) {
        this.paths[i].group = this;
      }

      if (options.toBeParsed) {
        this.parseDimensionsFromPaths(options);
        delete options.toBeParsed;
      }
      this.setOptions(options);
      this.setCoords();

      if (options.sourcePath) {
        this.setSourcePath(options.sourcePath);
      }
    },

    /**
     * Calculate width and height based on paths contained
     */
    parseDimensionsFromPaths: function(options) {
      var points, p, xC = [ ], yC = [ ], path, height, width,
          m = this.transformMatrix;
      for (var j = this.paths.length; j--;) {
        path = this.paths[j];
        height = path.height + path.strokeWidth;
        width = path.width + path.strokeWidth;
        points = [
          { x: path.left, y: path.top },
          { x: path.left + width, y: path.top },
          { x: path.left, y: path.top + height },
          { x: path.left + width, y: path.top + height }
        ];
        for (var i = 0; i < points.length; i++) {
          p = points[i];
          if (m) {
            p = fabric.util.transformPoint(p, m, false);
          }
          xC.push(p.x);
          yC.push(p.y);
        }
      }
      options.width = Math.max.apply(null, xC);
      options.height = Math.max.apply(null, yC);
    },

    /**
     * Renders this group on a specified context
     * @param {CanvasRenderingContext2D} ctx Context to render this instance on
     */
    render: function(ctx) {
      // do not render if object is not visible
      if (!this.visible) {
        return;
      }

      ctx.save();

      if (this.transformMatrix) {
        ctx.transform.apply(ctx, this.transformMatrix);
      }
      this.transform(ctx);

      this._setShadow(ctx);
      this.clipTo && fabric.util.clipContext(this, ctx);
      ctx.translate(-this.width/2, -this.height/2);
      for (var i = 0, l = this.paths.length; i < l; ++i) {
        this.paths[i].render(ctx, true);
      }
      this.clipTo && ctx.restore();
      this._removeShadow(ctx);
      ctx.restore();
    },

    /**
     * Sets certain property to a certain value
     * @param {String} prop
     * @param {Any} value
     * @return {fabric.PathGroup} thisArg
     */
    _set: function(prop, value) {

      if (prop === 'fill' && value && this.isSameColor()) {
        var i = this.paths.length;
        while (i--) {
          this.paths[i]._set(prop, value);
        }
      }

      return this.callSuper('_set', prop, value);
    },

    /**
     * Returns object representation of this path group
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      var o = extend(parentToObject.call(this, propertiesToInclude), {
        paths: invoke(this.getObjects(), 'toObject', propertiesToInclude)
      });
      if (this.sourcePath) {
        o.sourcePath = this.sourcePath;
      }
      return o;
    },

    /**
     * Returns dataless object representation of this path group
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} dataless object representation of an instance
     */
    toDatalessObject: function(propertiesToInclude) {
      var o = this.toObject(propertiesToInclude);
      if (this.sourcePath) {
        o.paths = this.sourcePath;
      }
      return o;
    },

    /* _TO_SVG_START_ */
    /**
     * Returns svg representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var objects = this.getObjects(),
          p = this.getPointByOrigin('left', 'top'),
          translatePart = 'translate(' + p.x + ' ' + p.y + ')',
          markup = [
            //jscs:disable validateIndentation
            '<g ',
              'style="', this.getSvgStyles(), '" ',
              'transform="', this.getSvgTransformMatrix(), translatePart, this.getSvgTransform(), '" ',
            '>\n'
            //jscs:enable validateIndentation
          ];

      for (var i = 0, len = objects.length; i < len; i++) {
        markup.push(objects[i].toSVG(reviver));
      }
      markup.push('</g>\n');

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * Returns a string representation of this path group
     * @return {String} string representation of an object
     */
    toString: function() {
      return '#<fabric.PathGroup (' + this.complexity() +
        '): { top: ' + this.top + ', left: ' + this.left + ' }>';
    },

    /**
     * Returns true if all paths in this group are of same color
     * @return {Boolean} true if all paths are of the same color (`fill`)
     */
    isSameColor: function() {
      var firstPathFill = (this.getObjects()[0].get('fill') || '').toLowerCase();
      return this.getObjects().every(function(path) {
        return (path.get('fill') || '').toLowerCase() === firstPathFill;
      });
    },

    /**
     * Returns number representation of object's complexity
     * @return {Number} complexity
     */
    complexity: function() {
      return this.paths.reduce(function(total, path) {
        return total + ((path && path.complexity) ? path.complexity() : 0);
      }, 0);
    },

    /**
     * Returns all paths in this path group
     * @return {Array} array of path objects included in this path group
     */
    getObjects: function() {
      return this.paths;
    }
  });

  /**
   * Creates fabric.PathGroup instance from an object representation
   * @static
   * @memberOf fabric.PathGroup
   * @param {Object} object Object to create an instance from
   * @param {Function} callback Callback to invoke when an fabric.PathGroup instance is created
   */
  fabric.PathGroup.fromObject = function(object, callback) {
    if (typeof object.paths === 'string') {
      fabric.loadSVGFromURL(object.paths, function (elements) {

        var pathUrl = object.paths;
        delete object.paths;

        var pathGroup = fabric.util.groupSVGElements(elements, object, pathUrl);

        callback(pathGroup);
      });
    }
    else {
      fabric.util.enlivenObjects(object.paths, function(enlivenedObjects) {
        delete object.paths;
        callback(new fabric.PathGroup(enlivenedObjects, object));
      });
    }
  };

  /**
   * Indicates that instances of this type are async
   * @static
   * @memberOf fabric.PathGroup
   * @type Boolean
   * @default
   */
  fabric.PathGroup.async = true;

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend,
      min = fabric.util.array.min,
      max = fabric.util.array.max,
      invoke = fabric.util.array.invoke;

  if (fabric.Group) {
    return;
  }

  // lock-related properties, for use in fabric.Group#get
  // to enable locking behavior on group
  // when one of its objects has lock-related properties set
  var _lockProperties = {
    lockMovementX:  true,
    lockMovementY:  true,
    lockRotation:   true,
    lockScalingX:   true,
    lockScalingY:   true,
    lockUniScaling: true
  };

  /**
   * Group class
   * @class fabric.Group
   * @extends fabric.Object
   * @mixes fabric.Collection
   * @tutorial {@link http://fabricjs.com/fabric-intro-part-3/#groups}
   * @see {@link fabric.Group#initialize} for constructor definition
   */
  fabric.Group = fabric.util.createClass(fabric.Object, fabric.Collection, /** @lends fabric.Group.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'group',

    /**
     * Constructor
     * @param {Object} objects Group objects
     * @param {Object} [options] Options object
     * @return {Object} thisArg
     */
    initialize: function(objects, options) {
      options = options || { };

      this._objects = objects || [];
      for (var i = this._objects.length; i--; ) {
        this._objects[i].group = this;
      }

      this.originalState = { };
      this.callSuper('initialize');

      if (options.originX) {
        this.originX = options.originX;
      }

      if (options.originY) {
        this.originY = options.originY;
      }

      this._calcBounds();
      this._updateObjectsCoords();

      this.callSuper('initialize', options);

      this.setCoords();
      this.saveCoords();
    },

    /**
     * @private
     */
    _updateObjectsCoords: function() {
      this.forEachObject(this._updateObjectCoords, this);
    },

    /**
     * @private
     */
    _updateObjectCoords: function(object) {
      var objectLeft = object.getLeft(),
          objectTop = object.getTop(),
          center = this.getCenterPoint();

      object.set({
        originalLeft: objectLeft,
        originalTop: objectTop,
        left: objectLeft - center.x,
        top: objectTop - center.y
      });

      object.setCoords();

      // do not display corners of objects enclosed in a group
      object.__origHasControls = object.hasControls;
      object.hasControls = false;
    },

    /**
     * Returns string represenation of a group
     * @return {String}
     */
    toString: function() {
      return '#<fabric.Group: (' + this.complexity() + ')>';
    },

    /**
     * Adds an object to a group; Then recalculates group's dimension, position.
     * @param {Object} object
     * @return {fabric.Group} thisArg
     * @chainable
     */
    addWithUpdate: function(object) {
      this._restoreObjectsState();
      if (object) {
        this._objects.push(object);
        object.group = this;
      }
      // since _restoreObjectsState set objects inactive
      this.forEachObject(this._setObjectActive, this);
      this._calcBounds();
      this._updateObjectsCoords();
      return this;
    },

    /**
     * @private
     */
    _setObjectActive: function(object) {
      object.set('active', true);
      object.group = this;
    },

    /**
     * Removes an object from a group; Then recalculates group's dimension, position.
     * @param {Object} object
     * @return {fabric.Group} thisArg
     * @chainable
     */
    removeWithUpdate: function(object) {
      this._moveFlippedObject(object);
      this._restoreObjectsState();

      // since _restoreObjectsState set objects inactive
      this.forEachObject(this._setObjectActive, this);

      this.remove(object);
      this._calcBounds();
      this._updateObjectsCoords();

      return this;
    },

    /**
     * @private
     */
    _onObjectAdded: function(object) {
      object.group = this;
    },

    /**
     * @private
     */
    _onObjectRemoved: function(object) {
      delete object.group;
      object.set('active', false);
    },

    /**
     * Properties that are delegated to group objects when reading/writing
     * @param {Object} delegatedProperties
     */
    delegatedProperties: {
      fill:             true,
      opacity:          true,
      fontFamily:       true,
      fontWeight:       true,
      fontSize:         true,
      fontStyle:        true,
      lineHeight:       true,
      textDecoration:   true,
      textAlign:        true,
      backgroundColor:  true
    },

    /**
     * @private
     */
    _set: function(key, value) {
      if (key in this.delegatedProperties) {
        var i = this._objects.length;
        while (i--) {
          this._objects[i].set(key, value);
        }
      }
      this.callSuper('_set', key, value);
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return extend(this.callSuper('toObject', propertiesToInclude), {
        objects: invoke(this._objects, 'toObject', propertiesToInclude)
      });
    },

    /**
     * Renders instance on a given context
     * @param {CanvasRenderingContext2D} ctx context to render instance on
     */
    render: function(ctx) {
      // do not render if object is not visible
      if (!this.visible) {
        return;
      }

      ctx.save();
      this.clipTo && fabric.util.clipContext(this, ctx);
      this.transform(ctx);
      // the array is now sorted in order of highest first, so start from end
      for (var i = 0, len = this._objects.length; i < len; i++) {
        this._renderObject(this._objects[i], ctx);
      }

      this.clipTo && ctx.restore();

      ctx.restore();
    },

    /**
     * Renders controls and borders for the object
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {Boolean} [noTransform] When true, context is not transformed
     */
    _renderControls: function(ctx, noTransform) {
      this.callSuper('_renderControls', ctx, noTransform);
      for (var i = 0, len = this._objects.length; i < len; i++) {
        this._objects[i]._renderControls(ctx);
      }
    },

    /**
     * @private
     */
    _renderObject: function(object, ctx) {
      var originalHasRotatingPoint = object.hasRotatingPoint;

      // do not render if object is not visible
      if (!object.visible) {
        return;
      }

      object.hasRotatingPoint = false;

      object.render(ctx);

      object.hasRotatingPoint = originalHasRotatingPoint;
    },

    /**
     * Retores original state of each of group objects (original state is that which was before group was created).
     * @private
     * @return {fabric.Group} thisArg
     * @chainable
     */
    _restoreObjectsState: function() {
      this._objects.forEach(this._restoreObjectState, this);
      return this;
    },

    /**
     * Realises the transform from this group onto the supplied object
     * i.e. it tells you what would happen if the supplied object was in
     * the group, and then the group was destroyed. It mutates the supplied
     * object.
     * @param {fabric.Object} object
     * @return {fabric.Object} transformedObject
    */
    realizeTransform: function(object) {
      this._moveFlippedObject(object);
      this._setObjectPosition(object);
      return object;
    },
    /**
     * Moves a flipped object to the position where it's displayed
     * @private
     * @param {fabric.Object} object
     * @return {fabric.Group} thisArg
     */
    _moveFlippedObject: function(object) {
      var oldOriginX = object.get('originX'),
          oldOriginY = object.get('originY'),
          center = object.getCenterPoint();

      object.set({
        originX: 'center',
        originY: 'center',
        left: center.x,
        top: center.y
      });

      this._toggleFlipping(object);

      var newOrigin = object.getPointByOrigin(oldOriginX, oldOriginY);

      object.set({
        originX: oldOriginX,
        originY: oldOriginY,
        left: newOrigin.x,
        top: newOrigin.y
      });

      return this;
    },

    /**
     * @private
     */
    _toggleFlipping: function(object) {
      if (this.flipX) {
        object.toggle('flipX');
        object.set('left', -object.get('left'));
        object.setAngle(-object.getAngle());
      }
      if (this.flipY) {
        object.toggle('flipY');
        object.set('top', -object.get('top'));
        object.setAngle(-object.getAngle());
      }
    },

    /**
     * Restores original state of a specified object in group
     * @private
     * @param {fabric.Object} object
     * @return {fabric.Group} thisArg
     */
    _restoreObjectState: function(object) {
      this._setObjectPosition(object);

      object.setCoords();
      object.hasControls = object.__origHasControls;
      delete object.__origHasControls;
      object.set('active', false);
      object.setCoords();
      delete object.group;

      return this;
    },

    /**
     * @private
     */
    _setObjectPosition: function(object) {
      var center = this.getCenterPoint(),
          rotated = this._getRotatedLeftTop(object);

      object.set({
        angle: object.getAngle() + this.getAngle(),
        left: center.x + rotated.left,
        top: center.y + rotated.top,
        scaleX: object.get('scaleX') * this.get('scaleX'),
        scaleY: object.get('scaleY') * this.get('scaleY')
      });
    },

    /**
     * @private
     */
    _getRotatedLeftTop: function(object) {
      var groupAngle = this.getAngle() * (Math.PI / 180);
      return {
        left: (-Math.sin(groupAngle) * object.getTop() * this.get('scaleY') +
                Math.cos(groupAngle) * object.getLeft() * this.get('scaleX')),

        top:  (Math.cos(groupAngle) * object.getTop() * this.get('scaleY') +
               Math.sin(groupAngle) * object.getLeft() * this.get('scaleX'))
      };
    },

    /**
     * Destroys a group (restoring state of its objects)
     * @return {fabric.Group} thisArg
     * @chainable
     */
    destroy: function() {
      this._objects.forEach(this._moveFlippedObject, this);
      return this._restoreObjectsState();
    },

    /**
     * Saves coordinates of this instance (to be used together with `hasMoved`)
     * @saveCoords
     * @return {fabric.Group} thisArg
     * @chainable
     */
    saveCoords: function() {
      this._originalLeft = this.get('left');
      this._originalTop = this.get('top');
      return this;
    },

    /**
     * Checks whether this group was moved (since `saveCoords` was called last)
     * @return {Boolean} true if an object was moved (since fabric.Group#saveCoords was called)
     */
    hasMoved: function() {
      return this._originalLeft !== this.get('left') ||
             this._originalTop !== this.get('top');
    },

    /**
     * Sets coordinates of all group objects
     * @return {fabric.Group} thisArg
     * @chainable
     */
    setObjectsCoords: function() {
      this.forEachObject(function(object) {
        object.setCoords();
      });
      return this;
    },

    /**
     * @private
     */
    _calcBounds: function(onlyWidthHeight) {
      var aX = [],
          aY = [],
          o, prop,
          props = ['tr', 'br', 'bl', 'tl'];

      for (var i = 0, len = this._objects.length; i < len; ++i) {
        o = this._objects[i];
        o.setCoords();
        for (var j = 0; j < props.length; j++) {
          prop = props[j];
          aX.push(o.oCoords[prop].x);
          aY.push(o.oCoords[prop].y);
        }
      }

      this.set(this._getBounds(aX, aY, onlyWidthHeight));
    },

    /**
     * @private
     */
    _getBounds: function(aX, aY, onlyWidthHeight) {
      var ivt = fabric.util.invertTransform(this.getViewportTransform()),
          minXY = fabric.util.transformPoint(new fabric.Point(min(aX), min(aY)), ivt),
          maxXY = fabric.util.transformPoint(new fabric.Point(max(aX), max(aY)), ivt),
          obj = {
            width: (maxXY.x - minXY.x) || 0,
            height: (maxXY.y - minXY.y) || 0
          };

      if (!onlyWidthHeight) {
        obj.left = minXY.x || 0;
        obj.top = minXY.y || 0;
        if (this.originX === 'center') {
          obj.left += obj.width / 2;
        }
        if (this.originX === 'right') {
          obj.left += obj.width;
        }
        if (this.originY === 'center') {
          obj.top += obj.height / 2;
        }
        if (this.originY === 'bottom') {
          obj.top += obj.height;
        }
      }
      return obj;
    },

    /* _TO_SVG_START_ */
    /**
     * Returns svg representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var markup = [
        //jscs:disable validateIndentation
        '<g ',
          'transform="', this.getSvgTransform(),
        '">\n'
        //jscs:enable validateIndentation
      ];

      for (var i = 0, len = this._objects.length; i < len; i++) {
        markup.push(this._objects[i].toSVG(reviver));
      }

      markup.push('</g>\n');

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * Returns requested property
     * @param {String} prop Property to get
     * @return {Any}
     */
    get: function(prop) {
      if (prop in _lockProperties) {
        if (this[prop]) {
          return this[prop];
        }
        else {
          for (var i = 0, len = this._objects.length; i < len; i++) {
            if (this._objects[i][prop]) {
              return true;
            }
          }
          return false;
        }
      }
      else {
        if (prop in this.delegatedProperties) {
          return this._objects[0] && this._objects[0].get(prop);
        }
        return this[prop];
      }
    }
  });

  /**
   * Returns {@link fabric.Group} instance from an object representation
   * @static
   * @memberOf fabric.Group
   * @param {Object} object Object to create a group from
   * @param {Function} [callback] Callback to invoke when an group instance is created
   * @return {fabric.Group} An instance of fabric.Group
   */
  fabric.Group.fromObject = function(object, callback) {
    fabric.util.enlivenObjects(object.objects, function(enlivenedObjects) {
      delete object.objects;
      callback && callback(new fabric.Group(enlivenedObjects, object));
    });
  };

  /**
   * Indicates that instances of this type are async
   * @static
   * @memberOf fabric.Group
   * @type Boolean
   * @default
   */
  fabric.Group.async = true;

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var extend = fabric.util.object.extend;

  if (!global.fabric) {
    global.fabric = { };
  }

  if (global.fabric.Image) {
    fabric.warn('fabric.Image is already defined.');
    return;
  }

  /**
   * Image class
   * @class fabric.Image
   * @extends fabric.Object
   * @tutorial {@link http://fabricjs.com/fabric-intro-part-1/#images}
   * @see {@link fabric.Image#initialize} for constructor definition
   */
  fabric.Image = fabric.util.createClass(fabric.Object, /** @lends fabric.Image.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'image',

    /**
     * crossOrigin value (one of "", "anonymous", "allow-credentials")
     * @see https://developer.mozilla.org/en-US/docs/HTML/CORS_settings_attributes
     * @type String
     * @default
     */
    crossOrigin: '',

    /**
     * AlignX value, part of preserveAspectRatio (one of "none", "mid", "min", "max")
     * @see http://www.w3.org/TR/SVG/coords.html#PreserveAspectRatioAttribute
     * This parameter defines how the picture is aligned to its viewport when image element width differs from image width.
     * @type String
     * @default
     */
    alignX: 'none',

    /**
     * AlignY value, part of preserveAspectRatio (one of "none", "mid", "min", "max")
     * @see http://www.w3.org/TR/SVG/coords.html#PreserveAspectRatioAttribute
     * This parameter defines how the picture is aligned to its viewport when image element height differs from image height.
     * @type String
     * @default
     */
    alignY: 'none',

    /**
     * meetOrSlice value, part of preserveAspectRatio  (one of "meet", "slice").
     * if meet the image is always fully visibile, if slice the viewport is always filled with image.
     * @see http://www.w3.org/TR/SVG/coords.html#PreserveAspectRatioAttribute
     * @type String
     * @default
     */
    meetOrSlice: 'meet',

    /**
     * private
     * contains last value of scaleX to detect
     * if the Image got resized after the last Render
     * @type Number
     */
    _lastScaleX: 1,

    /**
     * private
     * contains last value of scaleY to detect
     * if the Image got resized after the last Render
     * @type Number
     */
    _lastScaleY: 1,

    /**
     * Constructor
     * @param {HTMLImageElement | String} element Image element
     * @param {Object} [options] Options object
     * @return {fabric.Image} thisArg
     */
    initialize: function(element, options) {
      options || (options = { });

      this.filters = [ ];
      this.resizeFilters = [ ];
      this.callSuper('initialize', options);

      this._initElement(element, options);
      this._initConfig(options);

      if (options.filters) {
        this.filters = options.filters;
        this.applyFilters();
      }
    },

    /**
     * Returns image element which this instance if based on
     * @return {HTMLImageElement} Image element
     */
    getElement: function() {
      return this._element;
    },

    /**
     * Sets image element for this instance to a specified one.
     * If filters defined they are applied to new image.
     * You might need to call `canvas.renderAll` and `object.setCoords` after replacing, to render new image and update controls area.
     * @param {HTMLImageElement} element
     * @param {Function} [callback] Callback is invoked when all filters have been applied and new image is generated
     * @param {Object} [options] Options object
     * @return {fabric.Image} thisArg
     * @chainable
     */
    setElement: function(element, callback, options) {
      this._element = element;
      this._originalElement = element;
      this._initConfig(options);

      if (this.filters.length !== 0) {
        this.applyFilters(callback);
      }
      else if (callback) {
        callback();
      }

      return this;
    },

    /**
     * Sets crossOrigin value (on an instance and corresponding image element)
     * @return {fabric.Image} thisArg
     * @chainable
     */
    setCrossOrigin: function(value) {
      this.crossOrigin = value;
      this._element.crossOrigin = value;

      return this;
    },

    /**
     * Returns original size of an image
     * @return {Object} Object with "width" and "height" properties
     */
    getOriginalSize: function() {
      var element = this.getElement();
      return {
        width: element.width,
        height: element.height
      };
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _stroke: function(ctx) {
      ctx.save();
      this._setStrokeStyles(ctx);
      ctx.beginPath();
      ctx.strokeRect(-this.width / 2, -this.height / 2, this.width, this.height);
      ctx.closePath();
      ctx.restore();
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderDashedStroke: function(ctx) {
      var x = -this.width / 2,
          y = -this.height / 2,
          w = this.width,
          h = this.height;

      ctx.save();
      this._setStrokeStyles(ctx);

      ctx.beginPath();
      fabric.util.drawDashedLine(ctx, x, y, x + w, y, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x + w, y, x + w, y + h, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x + w, y + h, x, y + h, this.strokeDashArray);
      fabric.util.drawDashedLine(ctx, x, y + h, x, y, this.strokeDashArray);
      ctx.closePath();
      ctx.restore();
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} Object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return extend(this.callSuper('toObject', propertiesToInclude), {
        src: this._originalElement.src || this._originalElement._src,
        filters: this.filters.map(function(filterObj) {
          return filterObj && filterObj.toObject();
        }),
        crossOrigin: this.crossOrigin,
        alignX: this.alignX,
        alignY: this.alignY,
        meetOrSlice: this.meetOrSlice
      });
    },

    /* _TO_SVG_START_ */
    /**
     * Returns SVG representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var markup = [], x = -this.width / 2, y = -this.height / 2,
          preserveAspectRatio = 'none';
      if (this.group && this.group.type === 'path-group') {
        x = this.left;
        y = this.top;
      }
      if (this.alignX !== 'none' && this.alignY !== 'none') {
        preserveAspectRatio = 'x' + this.alignX + 'Y' + this.alignY + ' ' + this.meetOrSlice;
      }
      markup.push(
        '<g transform="', this.getSvgTransform(), this.getSvgTransformMatrix(), '">\n',
          '<image xlink:href="', this.getSvgSrc(),
            '" x="', x, '" y="', y,
            '" style="', this.getSvgStyles(),
            // we're essentially moving origin of transformation from top/left corner to the center of the shape
            // by wrapping it in container <g> element with actual transformation, then offsetting object to the top/left
            // so that object's center aligns with container's left/top
            '" width="', this.width,
            '" height="', this.height,
            '" preserveAspectRatio="', preserveAspectRatio, '"',
          '></image>\n'
      );

      if (this.stroke || this.strokeDashArray) {
        var origFill = this.fill;
        this.fill = null;
        markup.push(
          '<rect ',
            'x="', x, '" y="', y,
            '" width="', this.width, '" height="', this.height,
            '" style="', this.getSvgStyles(),
          '"/>\n'
        );
        this.fill = origFill;
      }

      markup.push('</g>\n');

      return reviver ? reviver(markup.join('')) : markup.join('');
    },
    /* _TO_SVG_END_ */

    /**
     * Returns source of an image
     * @return {String} Source of an image
     */
    getSrc: function() {
      if (this.getElement()) {
        return this.getElement().src || this.getElement()._src;
      }
    },

    /**
     * Sets source of an image
     * @param {String} src Source string (URL)
     * @param {Function} [callback] Callback is invoked when image has been loaded (and all filters have been applied)
     * @param {Object} [options] Options object
     * @return {fabric.Image} thisArg
     * @chainable
     */
    setSrc: function(src, callback, options) {
      fabric.util.loadImage(src, function(img) {
        return this.setElement(img, callback, options);
      }, this, options && options.crossOrigin);
    },

    /**
     * Returns string representation of an instance
     * @return {String} String representation of an instance
     */
    toString: function() {
      return '#<fabric.Image: { src: "' + this.getSrc() + '" }>';
    },

    /**
     * Returns a clone of an instance
     * @param {Function} callback Callback is invoked with a clone as a first argument
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     */
    clone: function(callback, propertiesToInclude) {
      this.constructor.fromObject(this.toObject(propertiesToInclude), callback);
    },

    /**
     * Applies filters assigned to this image (from "filters" array)
     * @method applyFilters
     * @param {Function} callback Callback is invoked when all filters have been applied and new image is generated
     * @return {fabric.Image} thisArg
     * @chainable
     */
    applyFilters: function(callback, filters, imgElement, forResizing) {

      filters = filters || this.filters;
      imgElement = imgElement || this._originalElement;

      if (!imgElement) {
        return;
      }

      var imgEl = imgElement,
          canvasEl = fabric.util.createCanvasElement(),
          replacement = fabric.util.createImage(),
          _this = this;

      canvasEl.width = imgEl.width;
      canvasEl.height = imgEl.height;
      canvasEl.getContext('2d').drawImage(imgEl, 0, 0, imgEl.width, imgEl.height);

      if (filters.length === 0) {
        this._element = imgElement;
        callback && callback();
        return canvasEl;
      }
      filters.forEach(function(filter) {
        filter && filter.applyTo(canvasEl, filter.scaleX || _this.scaleX, filter.scaleY || _this.scaleY);
        if (!forResizing && filter && filter.type === 'Resize') {
          _this.width *= filter.scaleX;
          _this.height *= filter.scaleY;
        }
      });

      /** @ignore */
      replacement.width = canvasEl.width;
      replacement.height = canvasEl.height;

      if (fabric.isLikelyNode) {
        replacement.src = canvasEl.toBuffer(undefined, fabric.Image.pngCompression);
        // onload doesn't fire in some node versions, so we invoke callback manually
        _this._element = replacement;
        !forResizing && (_this._filteredEl = replacement);
        callback && callback();
      }
      else {
        replacement.onload = function() {
          _this._element = replacement;
          !forResizing && (_this._filteredEl = replacement);
          callback && callback();
          replacement.onload = canvasEl = imgEl = null;
        };
        replacement.src = canvasEl.toDataURL('image/png');
      }
      return canvasEl;
    },
    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _render: function(ctx, noTransform) {
      var x, y, imageMargins = this._findMargins(), elementToDraw;

      x = (noTransform ? this.left : -this.width / 2);
      y = (noTransform ? this.top : -this.height / 2);

      if (this.meetOrSlice === 'slice') {
        ctx.beginPath();
        ctx.rect(x, y, this.width, this.height);
        ctx.clip();
      }

      if (this.isMoving === false && this.resizeFilters.length && this._needsResize()) {
        this._lastScaleX = this.scaleX;
        this._lastScaleY = this.scaleY;
        elementToDraw = this.applyFilters(null, this.resizeFilters, this._filteredEl || this._originalElement, true);
      }
      else {
        elementToDraw = this._element;
      }
      elementToDraw && ctx.drawImage(elementToDraw,
                                     x + imageMargins.marginX,
                                     y + imageMargins.marginY,
                                     imageMargins.width,
                                     imageMargins.height
                                    );

      this._renderStroke(ctx);
    },
    /**
     * @private, needed to check if image needs resize
     */
    _needsResize: function() {
      return (this.scaleX !== this._lastScaleX || this.scaleY !== this._lastScaleY);
    },

    /**
     * @private
     */
    _findMargins: function() {
      var width = this.width, height = this.height, scales,
          scale, marginX = 0, marginY = 0;

      if (this.alignX !== 'none' || this.alignY !== 'none') {
        scales = [this.width / this._element.width, this.height / this._element.height];
        scale = this.meetOrSlice === 'meet'
                ? Math.min.apply(null, scales) : Math.max.apply(null, scales);
        width = this._element.width * scale;
        height = this._element.height * scale;
        if (this.alignX === 'Mid') {
          marginX = (this.width - width) / 2;
        }
        if (this.alignX === 'Max') {
          marginX = this.width - width;
        }
        if (this.alignY === 'Mid') {
          marginY = (this.height - height) / 2;
        }
        if (this.alignY === 'Max') {
          marginY = this.height - height;
        }
      }
      return {
        width:  width,
        height: height,
        marginX: marginX,
        marginY: marginY
      };
    },

    /**
     * @private
     */
    _resetWidthHeight: function() {
      var element = this.getElement();

      this.set('width', element.width);
      this.set('height', element.height);
    },

    /**
     * The Image class's initialization method. This method is automatically
     * called by the constructor.
     * @private
     * @param {HTMLImageElement|String} element The element representing the image
     */
    _initElement: function(element) {
      this.setElement(fabric.util.getById(element));
      fabric.util.addClass(this.getElement(), fabric.Image.CSS_CANVAS);
    },

    /**
     * @private
     * @param {Object} [options] Options object
     */
    _initConfig: function(options) {
      options || (options = { });
      this.setOptions(options);
      this._setWidthHeight(options);
      if (this._element && this.crossOrigin) {
        this._element.crossOrigin = this.crossOrigin;
      }
    },

    /**
     * @private
     * @param {Object} object Object with filters property
     * @param {Function} callback Callback to invoke when all fabric.Image.filters instances are created
     */
    _initFilters: function(object, callback) {
      if (object.filters && object.filters.length) {
        fabric.util.enlivenObjects(object.filters, function(enlivenedObjects) {
          callback && callback(enlivenedObjects);
        }, 'fabric.Image.filters');
      }
      else {
        callback && callback();
      }
    },

    /**
     * @private
     * @param {Object} [options] Object with width/height properties
     */
    _setWidthHeight: function(options) {
      this.width = 'width' in options
        ? options.width
        : (this.getElement()
            ? this.getElement().width || 0
            : 0);

      this.height = 'height' in options
        ? options.height
        : (this.getElement()
            ? this.getElement().height || 0
            : 0);
    },

    /**
     * Returns complexity of an instance
     * @return {Number} complexity of this instance
     */
    complexity: function() {
      return 1;
    }
  });

  /**
   * Default CSS class name for canvas
   * @static
   * @type String
   * @default
   */
  fabric.Image.CSS_CANVAS = 'canvas-img';

  /**
   * Alias for getSrc
   * @static
   */
  fabric.Image.prototype.getSvgSrc = fabric.Image.prototype.getSrc;

  /**
   * Creates an instance of fabric.Image from its object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @param {Function} [callback] Callback to invoke when an image instance is created
   */
  fabric.Image.fromObject = function(object, callback) {
    fabric.util.loadImage(object.src, function(img) {
      fabric.Image.prototype._initFilters.call(object, object, function(filters) {
        object.filters = filters || [ ];
        var instance = new fabric.Image(img, object);
        callback && callback(instance);
      });
    }, null, object.crossOrigin);
  };

  /**
   * Creates an instance of fabric.Image from an URL string
   * @static
   * @param {String} url URL to create an image from
   * @param {Function} [callback] Callback to invoke when image is created (newly created image is passed as a first argument)
   * @param {Object} [imgOptions] Options object
   */
  fabric.Image.fromURL = function(url, callback, imgOptions) {
    fabric.util.loadImage(url, function(img) {
      callback && callback(new fabric.Image(img, imgOptions));
    }, null, imgOptions && imgOptions.crossOrigin);
  };

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by {@link fabric.Image.fromElement})
   * @static
   * @see {@link http://www.w3.org/TR/SVG/struct.html#ImageElement}
   */
  fabric.Image.ATTRIBUTE_NAMES =
    fabric.SHARED_ATTRIBUTES.concat('x y width height preserveAspectRatio xlink:href'.split(' '));

  /**
   * Returns {@link fabric.Image} instance from an SVG element
   * @static
   * @param {SVGElement} element Element to parse
   * @param {Function} callback Callback to execute when fabric.Image object is created
   * @param {Object} [options] Options object
   * @return {fabric.Image} Instance of fabric.Image
   */
  fabric.Image.fromElement = function(element, callback, options) {
    var parsedAttributes = fabric.parseAttributes(element, fabric.Image.ATTRIBUTE_NAMES),
        align = 'xMidYMid', meetOrSlice = 'meet', alignX, alignY, aspectRatioAttrs;

    if (parsedAttributes.preserveAspectRatio) {
      aspectRatioAttrs = parsedAttributes.preserveAspectRatio.split(' ');
    }

    if (aspectRatioAttrs && aspectRatioAttrs.length) {
      meetOrSlice = aspectRatioAttrs.pop();
      if (meetOrSlice !== 'meet' && meetOrSlice !== 'slice') {
        align = meetOrSlice;
        meetOrSlice = 'meet';
      }
      else if (aspectRatioAttrs.length) {
        align = aspectRatioAttrs.pop();
      }
    }
    //divide align in alignX and alignY
    alignX = align !== 'none' ? align.slice(1, 4) : 'none';
    alignY = align !== 'none' ? align.slice(5, 8) : 'none';
    parsedAttributes.alignX = alignX;
    parsedAttributes.alignY = alignY;
    parsedAttributes.meetOrSlice = meetOrSlice;
    fabric.Image.fromURL(parsedAttributes['xlink:href'], callback,
      extend((options ? fabric.util.object.clone(options) : { }), parsedAttributes));
  };
  /* _FROM_SVG_END_ */

  /**
   * Indicates that instances of this type are async
   * @static
   * @type Boolean
   * @default
   */
  fabric.Image.async = true;

  /**
   * Indicates compression level used when generating PNG under Node (in applyFilters). Any of 0-9
   * @static
   * @type Number
   * @default
   */
  fabric.Image.pngCompression = 1;

})(typeof exports !== 'undefined' ? exports : this);


fabric.util.object.extend(fabric.Object.prototype, /** @lends fabric.Object.prototype */ {

  /**
   * @private
   * @return {Number} angle value
   */
  _getAngleValueForStraighten: function() {
    var angle = this.getAngle() % 360;
    if (angle > 0) {
      return Math.round((angle - 1) / 90) * 90;
    }
    return Math.round(angle / 90) * 90;
  },

  /**
   * Straightens an object (rotating it from current angle to one of 0, 90, 180, 270, etc. depending on which is closer)
   * @return {fabric.Object} thisArg
   * @chainable
   */
  straighten: function() {
    this.setAngle(this._getAngleValueForStraighten());
    return this;
  },

  /**
   * Same as {@link fabric.Object.prototype.straighten} but with animation
   * @param {Object} callbacks Object with callback functions
   * @param {Function} [callbacks.onComplete] Invoked on completion
   * @param {Function} [callbacks.onChange] Invoked on every step of animation
   * @return {fabric.Object} thisArg
   * @chainable
   */
  fxStraighten: function(callbacks) {
    callbacks = callbacks || { };

    var empty = function() { },
        onComplete = callbacks.onComplete || empty,
        onChange = callbacks.onChange || empty,
        _this = this;

    fabric.util.animate({
      startValue: this.get('angle'),
      endValue: this._getAngleValueForStraighten(),
      duration: this.FX_DURATION,
      onChange: function(value) {
        _this.setAngle(value);
        onChange();
      },
      onComplete: function() {
        _this.setCoords();
        onComplete();
      },
      onStart: function() {
        _this.set('active', false);
      }
    });

    return this;
  }
});

fabric.util.object.extend(fabric.StaticCanvas.prototype, /** @lends fabric.StaticCanvas.prototype */ {

  /**
   * Straightens object, then rerenders canvas
   * @param {fabric.Object} object Object to straighten
   * @return {fabric.Canvas} thisArg
   * @chainable
   */
  straightenObject: function (object) {
    object.straighten();
    this.renderAll();
    return this;
  },

  /**
   * Same as {@link fabric.Canvas.prototype.straightenObject}, but animated
   * @param {fabric.Object} object Object to straighten
   * @return {fabric.Canvas} thisArg
   * @chainable
   */
  fxStraightenObject: function (object) {
    object.fxStraighten({
      onChange: this.renderAll.bind(this)
    });
    return this;
  }
});


/**
 * @namespace fabric.Image.filters
 * @memberOf fabric.Image
 * @tutorial {@link http://fabricjs.com/fabric-intro-part-2/#image_filters}
 * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
 */
fabric.Image.filters = fabric.Image.filters || { };

/**
 * Root filter class from which all filter classes inherit from
 * @class fabric.Image.filters.BaseFilter
 * @memberOf fabric.Image.filters
 */
fabric.Image.filters.BaseFilter = fabric.util.createClass(/** @lends fabric.Image.filters.BaseFilter.prototype */ {

  /**
   * Filter type
   * @param {String} type
   * @default
   */
  type: 'BaseFilter',

  /**
   * Constructor
   * @param {Object} [options] Options object
   */
  initialize: function(options) {
    if (options) {
      this.setOptions(options);
    }
  },

  /**
   * Sets filter's properties from options
   * @param {Object} [options] Options object
   */
  setOptions: function(options) {
    for (var prop in options) {
      this[prop] = options[prop];
    }
  },

  /**
   * Returns object representation of an instance
   * @return {Object} Object representation of an instance
   */
  toObject: function() {
    return { type: this.type };
  },

  /**
   * Returns a JSON representation of an instance
   * @return {Object} JSON
   */
  toJSON: function() {
    // delegate, not alias
    return this.toObject();
  }
});


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * Brightness filter class
   * @class fabric.Image.filters.Brightness
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link fabric.Image.filters.Brightness#initialize} for constructor definition
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.Brightness({
   *   brightness: 200
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Brightness = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Brightness.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Brightness',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.Brightness.prototype
     * @param {Object} [options] Options object
     * @param {Number} [options.brightness=0] Value to brighten the image up (0..255)
     */
    initialize: function(options) {
      options = options || { };
      this.brightness = options.brightness || 0;
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          brightness = this.brightness;

      for (var i = 0, len = data.length; i < len; i += 4) {
        data[i] += brightness;
        data[i + 1] += brightness;
        data[i + 2] += brightness;
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        brightness: this.brightness
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @return {fabric.Image.filters.Brightness} Instance of fabric.Image.filters.Brightness
   */
  fabric.Image.filters.Brightness.fromObject = function(object) {
    return new fabric.Image.filters.Brightness(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * Adapted from <a href="http://www.html5rocks.com/en/tutorials/canvas/imagefilters/">html5rocks article</a>
   * @class fabric.Image.filters.Convolute
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link fabric.Image.filters.Convolute#initialize} for constructor definition
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example <caption>Sharpen filter</caption>
   * var filter = new fabric.Image.filters.Convolute({
   *   matrix: [ 0, -1,  0,
   *            -1,  5, -1,
   *             0, -1,  0 ]
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   * @example <caption>Blur filter</caption>
   * var filter = new fabric.Image.filters.Convolute({
   *   matrix: [ 1/9, 1/9, 1/9,
   *             1/9, 1/9, 1/9,
   *             1/9, 1/9, 1/9 ]
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   * @example <caption>Emboss filter</caption>
   * var filter = new fabric.Image.filters.Convolute({
   *   matrix: [ 1,   1,  1,
   *             1, 0.7, -1,
   *            -1,  -1, -1 ]
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   * @example <caption>Emboss filter with opaqueness</caption>
   * var filter = new fabric.Image.filters.Convolute({
   *   opaque: true,
   *   matrix: [ 1,   1,  1,
   *             1, 0.7, -1,
   *            -1,  -1, -1 ]
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Convolute = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Convolute.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Convolute',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.Convolute.prototype
     * @param {Object} [options] Options object
     * @param {Boolean} [options.opaque=false] Opaque value (true/false)
     * @param {Array} [options.matrix] Filter matrix
     */
    initialize: function(options) {
      options = options || { };

      this.opaque = options.opaque;
      this.matrix = options.matrix || [
        0, 0, 0,
        0, 1, 0,
        0, 0, 0
      ];

      var canvasEl = fabric.util.createCanvasElement();
      this.tmpCtx = canvasEl.getContext('2d');
    },

    /**
     * @private
     */
    _createImageData: function(w, h) {
      return this.tmpCtx.createImageData(w, h);
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {

      var weights = this.matrix,
          context = canvasEl.getContext('2d'),
          pixels = context.getImageData(0, 0, canvasEl.width, canvasEl.height),

          side = Math.round(Math.sqrt(weights.length)),
          halfSide = Math.floor(side/2),
          src = pixels.data,
          sw = pixels.width,
          sh = pixels.height,

          // pad output by the convolution matrix
          w = sw,
          h = sh,
          output = this._createImageData(w, h),

          dst = output.data,

          // go through the destination image pixels
          alphaFac = this.opaque ? 1 : 0;

      for (var y = 0; y < h; y++) {
        for (var x = 0; x < w; x++) {
          var sy = y,
              sx = x,
              dstOff = (y * w + x) * 4,
              // calculate the weighed sum of the source image pixels that
              // fall under the convolution matrix
              r = 0, g = 0, b = 0, a = 0;

          for (var cy = 0; cy < side; cy++) {
            for (var cx = 0; cx < side; cx++) {

              var scy = sy + cy - halfSide,
                  scx = sx + cx - halfSide;

              /* jshint maxdepth:5 */
              if (scy < 0 || scy > sh || scx < 0 || scx > sw) {
                continue;
              }

              var srcOff = (scy * sw + scx) * 4,
                  wt = weights[cy * side + cx];

              r += src[srcOff] * wt;
              g += src[srcOff + 1] * wt;
              b += src[srcOff + 2] * wt;
              a += src[srcOff + 3] * wt;
            }
          }
          dst[dstOff] = r;
          dst[dstOff + 1] = g;
          dst[dstOff + 2] = b;
          dst[dstOff + 3] = a + alphaFac * (255 - a);
        }
      }

      context.putImageData(output, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        opaque: this.opaque,
        matrix: this.matrix
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @return {fabric.Image.filters.Convolute} Instance of fabric.Image.filters.Convolute
   */
  fabric.Image.filters.Convolute.fromObject = function(object) {
    return new fabric.Image.filters.Convolute(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * GradientTransparency filter class
   * @class fabric.Image.filters.GradientTransparency
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link fabric.Image.filters.GradientTransparency#initialize} for constructor definition
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.GradientTransparency({
   *   threshold: 200
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.GradientTransparency = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.GradientTransparency.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'GradientTransparency',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.GradientTransparency.prototype
     * @param {Object} [options] Options object
     * @param {Number} [options.threshold=100] Threshold value
     */
    initialize: function(options) {
      options = options || { };
      this.threshold = options.threshold || 100;
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          threshold = this.threshold,
          total = data.length;

      for (var i = 0, len = data.length; i < len; i += 4) {
        data[i + 3] = threshold + 255 * (total - i) / total;
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        threshold: this.threshold
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @return {fabric.Image.filters.GradientTransparency} Instance of fabric.Image.filters.GradientTransparency
   */
  fabric.Image.filters.GradientTransparency.fromObject = function(object) {
    return new fabric.Image.filters.GradientTransparency(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { });

  /**
   * Grayscale image filter class
   * @class fabric.Image.filters.Grayscale
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.Grayscale();
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Grayscale = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Grayscale.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Grayscale',

    /**
     * Applies filter to canvas element
     * @memberOf fabric.Image.filters.Grayscale.prototype
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          len = imageData.width * imageData.height * 4,
          index = 0,
          average;

      while (index < len) {
        average = (data[index] + data[index + 1] + data[index + 2]) / 3;
        data[index]     = average;
        data[index + 1] = average;
        data[index + 2] = average;
        index += 4;
      }

      context.putImageData(imageData, 0, 0);
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @return {fabric.Image.filters.Grayscale} Instance of fabric.Image.filters.Grayscale
   */
  fabric.Image.filters.Grayscale.fromObject = function() {
    return new fabric.Image.filters.Grayscale();
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { });

  /**
   * Invert filter class
   * @class fabric.Image.filters.Invert
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.Invert();
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Invert = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Invert.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Invert',

    /**
     * Applies filter to canvas element
     * @memberOf fabric.Image.filters.Invert.prototype
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          iLen = data.length, i;

      for (i = 0; i < iLen; i+=4) {
        data[i] = 255 - data[i];
        data[i + 1] = 255 - data[i + 1];
        data[i + 2] = 255 - data[i + 2];
      }

      context.putImageData(imageData, 0, 0);
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @return {fabric.Image.filters.Invert} Instance of fabric.Image.filters.Invert
   */
  fabric.Image.filters.Invert.fromObject = function() {
    return new fabric.Image.filters.Invert();
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * Mask filter class
   * See http://resources.aleph-1.com/mask/
   * @class fabric.Image.filters.Mask
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link fabric.Image.filters.Mask#initialize} for constructor definition
   */
  fabric.Image.filters.Mask = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Mask.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Mask',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.Mask.prototype
     * @param {Object} [options] Options object
     * @param {fabric.Image} [options.mask] Mask image object
     * @param {Number} [options.channel=0] Rgb channel (0, 1, 2 or 3)
     */
    initialize: function(options) {
      options = options || { };

      this.mask = options.mask;
      this.channel = [ 0, 1, 2, 3 ].indexOf(options.channel) > -1 ? options.channel : 0;
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      if (!this.mask) {
        return;
      }

      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          maskEl = this.mask.getElement(),
          maskCanvasEl = fabric.util.createCanvasElement(),
          channel = this.channel,
          i,
          iLen = imageData.width * imageData.height * 4;

      maskCanvasEl.width = maskEl.width;
      maskCanvasEl.height = maskEl.height;

      maskCanvasEl.getContext('2d').drawImage(maskEl, 0, 0, maskEl.width, maskEl.height);

      var maskImageData = maskCanvasEl.getContext('2d').getImageData(0, 0, maskEl.width, maskEl.height),
          maskData = maskImageData.data;

      for (i = 0; i < iLen; i += 4) {
        data[i + 3] = maskData[i + channel];
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        mask: this.mask.toObject(),
        channel: this.channel
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @param {Function} [callback] Callback to invoke when a mask filter instance is created
   */
  fabric.Image.filters.Mask.fromObject = function(object, callback) {
    fabric.util.loadImage(object.mask.src, function(img) {
      object.mask = new fabric.Image(img, object.mask);
      callback && callback(new fabric.Image.filters.Mask(object));
    });
  };

  /**
   * Indicates that instances of this type are async
   * @static
   * @type Boolean
   * @default
   */
  fabric.Image.filters.Mask.async = true;

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * Noise filter class
   * @class fabric.Image.filters.Noise
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link fabric.Image.filters.Noise#initialize} for constructor definition
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.Noise({
   *   noise: 700
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Noise = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Noise.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Noise',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.Noise.prototype
     * @param {Object} [options] Options object
     * @param {Number} [options.noise=0] Noise value
     */
    initialize: function(options) {
      options = options || { };
      this.noise = options.noise || 0;
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          noise = this.noise, rand;

      for (var i = 0, len = data.length; i < len; i += 4) {

        rand = (0.5 - Math.random()) * noise;

        data[i] += rand;
        data[i + 1] += rand;
        data[i + 2] += rand;
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        noise: this.noise
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @return {fabric.Image.filters.Noise} Instance of fabric.Image.filters.Noise
   */
  fabric.Image.filters.Noise.fromObject = function(object) {
    return new fabric.Image.filters.Noise(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * Pixelate filter class
   * @class fabric.Image.filters.Pixelate
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link fabric.Image.filters.Pixelate#initialize} for constructor definition
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.Pixelate({
   *   blocksize: 8
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Pixelate = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Pixelate.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Pixelate',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.Pixelate.prototype
     * @param {Object} [options] Options object
     * @param {Number} [options.blocksize=4] Blocksize for pixelate
     */
    initialize: function(options) {
      options = options || { };
      this.blocksize = options.blocksize || 4;
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          iLen = imageData.height,
          jLen = imageData.width,
          index, i, j, r, g, b, a;

      for (i = 0; i < iLen; i += this.blocksize) {
        for (j = 0; j < jLen; j += this.blocksize) {

          index = (i * 4) * jLen + (j * 4);

          r = data[index];
          g = data[index + 1];
          b = data[index + 2];
          a = data[index + 3];

          /*
           blocksize: 4

           [1,x,x,x,1]
           [x,x,x,x,1]
           [x,x,x,x,1]
           [x,x,x,x,1]
           [1,1,1,1,1]
           */

          for (var _i = i, _ilen = i + this.blocksize; _i < _ilen; _i++) {
            for (var _j = j, _jlen = j + this.blocksize; _j < _jlen; _j++) {
              index = (_i * 4) * jLen + (_j * 4);
              data[index] = r;
              data[index + 1] = g;
              data[index + 2] = b;
              data[index + 3] = a;
            }
          }
        }
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        blocksize: this.blocksize
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @return {fabric.Image.filters.Pixelate} Instance of fabric.Image.filters.Pixelate
   */
  fabric.Image.filters.Pixelate.fromObject = function(object) {
    return new fabric.Image.filters.Pixelate(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * Remove white filter class
   * @class fabric.Image.filters.RemoveWhite
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link fabric.Image.filters.RemoveWhite#initialize} for constructor definition
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.RemoveWhite({
   *   threshold: 40,
   *   distance: 140
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.RemoveWhite = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.RemoveWhite.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'RemoveWhite',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.RemoveWhite.prototype
     * @param {Object} [options] Options object
     * @param {Number} [options.threshold=30] Threshold value
     * @param {Number} [options.distance=20] Distance value
     */
    initialize: function(options) {
      options = options || { };
      this.threshold = options.threshold || 30;
      this.distance = options.distance || 20;
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          threshold = this.threshold,
          distance = this.distance,
          limit = 255 - threshold,
          abs = Math.abs,
          r, g, b;

      for (var i = 0, len = data.length; i < len; i += 4) {
        r = data[i];
        g = data[i + 1];
        b = data[i + 2];

        if (r > limit &&
            g > limit &&
            b > limit &&
            abs(r - g) < distance &&
            abs(r - b) < distance &&
            abs(g - b) < distance
        ) {
          data[i + 3] = 1;
        }
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        threshold: this.threshold,
        distance: this.distance
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @return {fabric.Image.filters.RemoveWhite} Instance of fabric.Image.filters.RemoveWhite
   */
  fabric.Image.filters.RemoveWhite.fromObject = function(object) {
    return new fabric.Image.filters.RemoveWhite(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { });

  /**
   * Sepia filter class
   * @class fabric.Image.filters.Sepia
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.Sepia();
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Sepia = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Sepia.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Sepia',

    /**
     * Applies filter to canvas element
     * @memberOf fabric.Image.filters.Sepia.prototype
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          iLen = data.length, i, avg;

      for (i = 0; i < iLen; i+=4) {
        avg = 0.3  * data[i] + 0.59 * data[i + 1] + 0.11 * data[i + 2];
        data[i] = avg + 100;
        data[i + 1] = avg + 50;
        data[i + 2] = avg + 255;
      }

      context.putImageData(imageData, 0, 0);
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @return {fabric.Image.filters.Sepia} Instance of fabric.Image.filters.Sepia
   */
  fabric.Image.filters.Sepia.fromObject = function() {
    return new fabric.Image.filters.Sepia();
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { });

  /**
   * Sepia2 filter class
   * @class fabric.Image.filters.Sepia2
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.Sepia2();
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Sepia2 = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Sepia2.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Sepia2',

    /**
     * Applies filter to canvas element
     * @memberOf fabric.Image.filters.Sepia.prototype
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          iLen = data.length, i, r, g, b;

      for (i = 0; i < iLen; i+=4) {
        r = data[i];
        g = data[i + 1];
        b = data[i + 2];

        data[i] = (r * 0.393 + g * 0.769 + b * 0.189 ) / 1.351;
        data[i + 1] = (r * 0.349 + g * 0.686 + b * 0.168 ) / 1.203;
        data[i + 2] = (r * 0.272 + g * 0.534 + b * 0.131 ) / 2.140;
      }

      context.putImageData(imageData, 0, 0);
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @return {fabric.Image.filters.Sepia2} Instance of fabric.Image.filters.Sepia2
   */
  fabric.Image.filters.Sepia2.fromObject = function() {
    return new fabric.Image.filters.Sepia2();
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * Tint filter class
   * Adapted from <a href="https://github.com/mezzoblue/PaintbrushJS">https://github.com/mezzoblue/PaintbrushJS</a>
   * @class fabric.Image.filters.Tint
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link fabric.Image.filters.Tint#initialize} for constructor definition
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example <caption>Tint filter with hex color and opacity</caption>
   * var filter = new fabric.Image.filters.Tint({
   *   color: '#3513B0',
   *   opacity: 0.5
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   * @example <caption>Tint filter with rgba color</caption>
   * var filter = new fabric.Image.filters.Tint({
   *   color: 'rgba(53, 21, 176, 0.5)'
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Tint = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Tint.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Tint',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.Tint.prototype
     * @param {Object} [options] Options object
     * @param {String} [options.color=#000000] Color to tint the image with
     * @param {Number} [options.opacity] Opacity value that controls the tint effect's transparency (0..1)
     */
    initialize: function(options) {
      options = options || { };

      this.color = options.color || '#000000';
      this.opacity = typeof options.opacity !== 'undefined'
                      ? options.opacity
                      : new fabric.Color(this.color).getAlpha();
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          iLen = data.length, i,
          tintR, tintG, tintB,
          r, g, b, alpha1,
          source;

      source = new fabric.Color(this.color).getSource();

      tintR = source[0] * this.opacity;
      tintG = source[1] * this.opacity;
      tintB = source[2] * this.opacity;

      alpha1 = 1 - this.opacity;

      for (i = 0; i < iLen; i+=4) {
        r = data[i];
        g = data[i + 1];
        b = data[i + 2];

        // alpha compositing
        data[i] = tintR + r * alpha1;
        data[i + 1] = tintG + g * alpha1;
        data[i + 2] = tintB + b * alpha1;
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        color: this.color,
        opacity: this.opacity
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @return {fabric.Image.filters.Tint} Instance of fabric.Image.filters.Tint
   */
  fabric.Image.filters.Tint.fromObject = function(object) {
    return new fabric.Image.filters.Tint(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend;

  /**
   * Multiply filter class
   * Adapted from <a href="http://www.laurenscorijn.com/articles/colormath-basics">http://www.laurenscorijn.com/articles/colormath-basics</a>
   * @class fabric.Image.filters.Multiply
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @example <caption>Multiply filter with hex color</caption>
   * var filter = new fabric.Image.filters.Multiply({
   *   color: '#F0F'
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   * @example <caption>Multiply filter with rgb color</caption>
   * var filter = new fabric.Image.filters.Multiply({
   *   color: 'rgb(53, 21, 176)'
   * });
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Multiply = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Multiply.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Multiply',

    /**
     * Constructor
     * @memberOf fabric.Image.filters.Multiply.prototype
     * @param {Object} [options] Options object
     * @param {String} [options.color=#000000] Color to multiply the image pixels with
     */
    initialize: function(options) {
      options = options || { };

      this.color = options.color || '#000000';
    },

    /**
     * Applies filter to canvas element
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          iLen = data.length, i,
          source;

      source = new fabric.Color(this.color).getSource();

      for (i = 0; i < iLen; i+=4) {
        data[i] *= source[0] / 255;
        data[i + 1] *= source[1] / 255;
        data[i + 2] *= source[2] / 255;
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return extend(this.callSuper('toObject'), {
        color: this.color
      });
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @param {Object} object Object to create an instance from
   * @return {fabric.Image.filters.Multiply} Instance of fabric.Image.filters.Multiply
   */
  fabric.Image.filters.Multiply.fromObject = function(object) {
    return new fabric.Image.filters.Multiply(object);
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {
  'use strict';

  var fabric = global.fabric;

  /**
   * Color Blend filter class
   * @class fabric.Image.filter.Blend
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @example
   * var filter = new fabric.Image.filters.Blend({
   *  color: '#000',
   *  mode: 'multiply'
   * });
   *
   * var filter = new fabric.Image.filters.Blend({
   *  image: fabricImageObject,
   *  mode: 'multiply',
   *  alpha: 0.5
   * });

   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Blend = fabric.util.createClass({
    type: 'Blend',

    initialize: function(options) {
      options = options || {};
      this.color = options.color || '#000';
      this.image = options.image || false;
      this.mode = options.mode || 'multiply';
      this.alpha = options.alpha || 1;
    },

    applyTo: function(canvasEl) {
      var context = canvasEl.getContext('2d'),
          imageData = context.getImageData(0, 0, canvasEl.width, canvasEl.height),
          data = imageData.data,
          tr, tg, tb,
          r, g, b,
          _r, _g, _b,
          source,
          isImage = false;

      if (this.image) {
        // Blend images
        isImage = true;

        var _el = fabric.util.createCanvasElement();
        _el.width = this.image.width;
        _el.height = this.image.height;

        var tmpCanvas = new fabric.StaticCanvas(_el);
        tmpCanvas.add(this.image);
        var context2 =  tmpCanvas.getContext('2d');
        source = context2.getImageData(0, 0, tmpCanvas.width, tmpCanvas.height).data;
      }
      else {
        // Blend color
        source = new fabric.Color(this.color).getSource();

        tr = source[0] * this.alpha;
        tg = source[1] * this.alpha;
        tb = source[2] * this.alpha;
      }

      for (var i = 0, len = data.length; i < len; i += 4) {

        r = data[i];
        g = data[i + 1];
        b = data[i + 2];

        if (isImage) {
          tr = source[i] * this.alpha;
          tg = source[i + 1] * this.alpha;
          tb = source[i + 2] * this.alpha;
        }

        switch (this.mode) {
          case 'multiply':
            data[i] = r * tr / 255;
            data[i + 1] = g * tg / 255;
            data[i + 2] = b * tb / 255;
            break;
          case 'screen':
            data[i] = 1 - (1 - r) * (1 - tr);
            data[i + 1] = 1 - (1 - g) * (1 - tg);
            data[i + 2] = 1 - (1 - b) * (1 - tb);
            break;
          case 'add':
            data[i] = Math.min(255, r + tr);
            data[i + 1] = Math.min(255, g + tg);
            data[i + 2] = Math.min(255, b + tb);
            break;
          case 'diff':
          case 'difference':
            data[i] = Math.abs(r - tr);
            data[i + 1] = Math.abs(g - tg);
            data[i + 2] = Math.abs(b - tb);
            break;
          case 'subtract':
            _r = r - tr;
            _g = g - tg;
            _b = b - tb;

            data[i] = (_r < 0) ? 0 : _r;
            data[i + 1] = (_g < 0) ? 0 : _g;
            data[i + 2] = (_b < 0) ? 0 : _b;
            break;
          case 'darken':
            data[i] = Math.min(r, tr);
            data[i + 1] = Math.min(g, tg);
            data[i + 2] = Math.min(b, tb);
            break;
          case 'lighten':
            data[i] = Math.max(r, tr);
            data[i + 1] = Math.max(g, tg);
            data[i + 2] = Math.max(b, tb);
            break;
        }
      }

      context.putImageData(imageData, 0, 0);
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return {
        color: this.color,
        image: this.image,
        mode: this.mode,
        alpha: this.alpha
      };
    }
  });

  fabric.Image.filters.Blend.fromObject = function(object) {
    return new fabric.Image.filters.Blend(object);
  };
})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric  = global.fabric || (global.fabric = { }), pow = Math.pow, floor = Math.floor,
      sqrt = Math.sqrt, abs = Math.abs, max = Math.max, round = Math.round, sin = Math.sin,
      ceil = Math.ceil;

  /**
   * Resize image filter class
   * @class fabric.Image.filters.Resize
   * @memberOf fabric.Image.filters
   * @extends fabric.Image.filters.BaseFilter
   * @see {@link http://fabricjs.com/image-filters/|ImageFilters demo}
   * @example
   * var filter = new fabric.Image.filters.Resize();
   * object.filters.push(filter);
   * object.applyFilters(canvas.renderAll.bind(canvas));
   */
  fabric.Image.filters.Resize = fabric.util.createClass(fabric.Image.filters.BaseFilter, /** @lends fabric.Image.filters.Resize.prototype */ {

    /**
     * Filter type
     * @param {String} type
     * @default
     */
    type: 'Resize',

    /**
     * Resize type
     * @param {String} resizeType
     * @default
     */
    resizeType: 'hermite',

    /**
     * Scale factor for resizing, x axis
     * @param {Number} scaleX
     * @default
     */
    scaleX: 0,

    /**
     * Scale factor for resizing, y axis
     * @param {Number} scaleY
     * @default
     */
    scaleY: 0,

    /**
     * LanczosLobes parameter for lanczos filter
     * @param {Number} lanczosLobes
     * @default
     */
    lanczosLobes: 3,

    /**
     * Applies filter to canvas element
     * @memberOf fabric.Image.filters.Resize.prototype
     * @param {Object} canvasEl Canvas element to apply filter to
     */
    applyTo: function(canvasEl, scaleX, scaleY) {

      this.rcpScaleX = 1 / scaleX;
      this.rcpScaleY = 1 / scaleY;

      var oW = canvasEl.width, oH = canvasEl.height,
          dW = round(oW * scaleX), dH = round(oH * scaleY),
          imageData;

      if (this.resizeType === 'sliceHack') {
        imageData = this.sliceByTwo(canvasEl, oW, oH, dW, dH);
      }
      if (this.resizeType === 'hermite') {
        imageData = this.hermiteFastResize(canvasEl, oW, oH, dW, dH);
      }
      if (this.resizeType === 'bilinear') {
        imageData = this.bilinearFiltering(canvasEl, oW, oH, dW, dH);
      }
      if (this.resizeType === 'lanczos') {
        imageData = this.lanczosResize(canvasEl, oW, oH, dW, dH);
      }
      canvasEl.width = dW;
      canvasEl.height = dH;
      canvasEl.getContext('2d').putImageData(imageData, 0, 0);
    },

    sliceByTwo: function(canvasEl, width, height, newWidth, newHeight) {
      var context = canvasEl.getContext('2d'), imageData,
          multW = 0.5, multH = 0.5, signW = 1, signH = 1,
          doneW = false, doneH = false, stepW = width, stepH = height,
          tmpCanvas = fabric.util.createCanvasElement(),
          tmpCtx = tmpCanvas.getContext('2d');
      newWidth = floor(newWidth);
      newHeight = floor(newHeight);
      tmpCanvas.width = max(newWidth, width);
      tmpCanvas.height = max(newHeight, height);

      if (newWidth > width) {
        multW = 2;
        signW = -1;
      }
      if (newHeight > height) {
        multH = 2;
        signH = -1;
      }
      imageData = context.getImageData(0, 0, width, height);
      canvasEl.width = max(newWidth, width);
      canvasEl.height = max(newHeight, height);
      context.putImageData(imageData, 0, 0);

      while (!doneW || !doneH) {
        width = stepW;
        height = stepH;
        if (newWidth * signW < floor(stepW * multW * signW)) {
          stepW = floor(stepW * multW);
        }
        else {
          stepW = newWidth;
          doneW = true;
        }
        if (newHeight * signH < floor(stepH * multH * signH)) {
          stepH = floor(stepH * multH);
        }
        else {
          stepH = newHeight;
          doneH = true;
        }
        imageData = context.getImageData(0, 0, width, height);
        tmpCtx.putImageData(imageData, 0, 0);
        context.clearRect(0, 0, stepW, stepH);
        context.drawImage(tmpCanvas, 0, 0, width, height, 0, 0, stepW, stepH);
      }
      return context.getImageData(0, 0, newWidth, newHeight);
    },

    lanczosResize: function(canvasEl, oW, oH, dW, dH) {

      function lanczosCreate(lobes) {
        return function(x) {
          if (x > lobes) {
            return 0;
          }
          x *= Math.PI;
          if (abs(x) < 1e-16) {
            return 1;
          }
          var xx = x / lobes;
          return sin(x) * sin(xx) / x / xx;
        };
      }

      function process(u) {
        var v, i, weight, idx, a, red, green,
            blue, alpha, fX, fY;
        center.x = (u + 0.5) * ratioX;
        icenter.x = floor(center.x);
        for (v = 0; v < dH; v++) {
          center.y = (v + 0.5) * ratioY;
          icenter.y = floor(center.y);
          a = 0, red = 0, green = 0, blue = 0, alpha = 0;
          for (i = icenter.x - range2X; i <= icenter.x + range2X; i++) {
            if (i < 0 || i >= oW) {
              continue;
            }
            fX = floor(1000 * abs(i - center.x));
            if (!cacheLanc[fX]) {
              cacheLanc[fX] = { };
            }
            for (var j = icenter.y - range2Y; j <= icenter.y + range2Y; j++) {
              if (j < 0 || j >= oH) {
                continue;
              }
              fY = floor(1000 * abs(j - center.y));
              if (!cacheLanc[fX][fY]) {
                cacheLanc[fX][fY] = lanczos(sqrt(pow(fX * rcpRatioX, 2) + pow(fY * rcpRatioY, 2)) / 1000);
              }
              weight = cacheLanc[fX][fY];
              if (weight > 0) {
                idx = (j * oW + i) * 4;
                a += weight;
                red += weight * srcData[idx];
                green += weight * srcData[idx + 1];
                blue += weight * srcData[idx + 2];
                alpha += weight * srcData[idx + 3];
              }
            }
          }
          idx = (v * dW + u) * 4;
          destData[idx] = red / a;
          destData[idx + 1] = green / a;
          destData[idx + 2] = blue / a;
          destData[idx + 3] = alpha / a;
        }

        if (++u < dW) {
          return process(u);
        }
        else {
          return destImg;
        }
      }

      var context = canvasEl.getContext('2d'),
          srcImg = context.getImageData(0, 0, oW, oH),
          destImg = context.getImageData(0, 0, dW, dH),
          srcData = srcImg.data, destData = destImg.data,
          lanczos = lanczosCreate(this.lanczosLobes),
          ratioX = this.rcpScaleX, ratioY = this.rcpScaleY,
          rcpRatioX = 2 / this.rcpScaleX, rcpRatioY = 2 / this.rcpScaleY,
          range2X = ceil(ratioX * this.lanczosLobes / 2),
          range2Y = ceil(ratioY * this.lanczosLobes / 2),
          cacheLanc = { }, center = { }, icenter = { };

      return process(0);
    },

    bilinearFiltering: function(canvasEl, w, h, w2, h2) {
      var a, b, c, d, x, y, i, j, xDiff, yDiff, chnl,
          color, offset = 0, origPix, ratioX = this.rcpScaleX,
          ratioY = this.rcpScaleY, context = canvasEl.getContext('2d'),
          w4 = 4 * (w - 1), img = context.getImageData(0, 0, w, h),
          pixels = img.data, destImage = context.getImageData(0, 0, w2, h2),
          destPixels = destImage.data;
      for (i = 0; i < h2; i++) {
        for (j = 0; j < w2; j++) {
          x = floor(ratioX * j);
          y = floor(ratioY * i);
          xDiff = ratioX * j - x;
          yDiff = ratioY * i - y;
          origPix = 4 * (y * w + x);

          for (chnl = 0; chnl < 4; chnl++) {
            a = pixels[origPix + chnl];
            b = pixels[origPix + 4 + chnl];
            c = pixels[origPix + w4 + chnl];
            d = pixels[origPix + w4 + 4 + chnl];
            color = a * (1 - xDiff) * (1 - yDiff) + b * xDiff * (1 - yDiff) +
                    c * yDiff * (1 - xDiff) + d * xDiff * yDiff;
            destPixels[offset++] = color;
          }
        }
      }
      return destImage;
    },

    hermiteFastResize: function(canvasEl, oW, oH, dW, dH) {
      var ratioW = this.rcpScaleX, ratioH = this.rcpScaleY,
          ratioWHalf = ceil(ratioW / 2),
          ratioHHalf = ceil(ratioH / 2),
          context = canvasEl.getContext('2d'),
          img = context.getImageData(0, 0, oW, oH), data = img.data,
          img2 = context.getImageData(0, 0, dW, dH), data2 = img2.data;
      for (var j = 0; j < dH; j++) {
        for (var i = 0; i < dW; i++) {
          var x2 = (i + j * dW) * 4, weight = 0, weights = 0, weightsAlpha = 0,
              gxR = 0, gxG = 0, gxB = 0, gxA = 0, centerY = (j + 0.5) * ratioH;
          for (var yy = floor(j * ratioH); yy < (j + 1) * ratioH; yy++) {
            var dy = abs(centerY - (yy + 0.5)) / ratioHHalf,
                centerX = (i + 0.5) * ratioW, w0 = dy * dy;
            for (var xx = floor(i * ratioW); xx < (i + 1) * ratioW; xx++) {
              var dx = abs(centerX - (xx + 0.5)) / ratioWHalf,
                  w = sqrt(w0 + dx * dx);
              /*jshint maxdepth:5 */
              if (w > 1 && w < -1) {
                continue;
              }
              //hermite filter
              weight = 2 * w * w * w - 3 * w * w + 1;
              if (weight > 0) {
                dx = 4 * (xx + yy * oW);
                //alpha
                gxA += weight * data[dx + 3];
                weightsAlpha += weight;
                //colors
                /*jshint maxdepth:6 */
                if (data[dx + 3] < 255) {
                  weight = weight * data[dx + 3] / 250;
                }
                /*jshint maxdepth:5 */
                gxR += weight * data[dx];
                gxG += weight * data[dx + 1];
                gxB += weight * data[dx + 2];
                weights += weight;
              }
              /*jshint maxdepth:4 */
            }
          }
          data2[x2] = gxR / weights;
          data2[x2 + 1] = gxG / weights;
          data2[x2 + 2] = gxB / weights;
          data2[x2 + 3] = gxA / weightsAlpha;
        }
      }
      return img2;
    },

    /**
     * Returns object representation of an instance
     * @return {Object} Object representation of an instance
     */
    toObject: function() {
      return {
        type: this.type,
        scaleX: this.scaleX,
        scaley: this.scaleY,
        resizeType: this.resizeType,
        lanczosLobes: this.lanczosLobes
      };
    }
  });

  /**
   * Returns filter instance from an object representation
   * @static
   * @return {fabric.Image.filters.Resize} Instance of fabric.Image.filters.Resize
   */
  fabric.Image.filters.Resize.fromObject = function() {
    return new fabric.Image.filters.Resize();
  };

})(typeof exports !== 'undefined' ? exports : this);


(function(global) {

  'use strict';

  var fabric = global.fabric || (global.fabric = { }),
      extend = fabric.util.object.extend,
      clone = fabric.util.object.clone,
      toFixed = fabric.util.toFixed,
      supportsLineDash = fabric.StaticCanvas.supports('setLineDash');

  if (fabric.Text) {
    fabric.warn('fabric.Text is already defined');
    return;
  }

  var stateProperties = fabric.Object.prototype.stateProperties.concat();
  stateProperties.push(
    'fontFamily',
    'fontWeight',
    'fontSize',
    'text',
    'textDecoration',
    'textAlign',
    'fontStyle',
    'lineHeight',
    'textBackgroundColor'
  );

  /**
   * Text class
   * @class fabric.Text
   * @extends fabric.Object
   * @return {fabric.Text} thisArg
   * @tutorial {@link http://fabricjs.com/fabric-intro-part-2/#text}
   * @see {@link fabric.Text#initialize} for constructor definition
   */
  fabric.Text = fabric.util.createClass(fabric.Object, /** @lends fabric.Text.prototype */ {

    /**
     * Properties which when set cause object to change dimensions
     * @type Object
     * @private
     */
    _dimensionAffectingProps: {
      fontSize: true,
      fontWeight: true,
      fontFamily: true,
      fontStyle: true,
      lineHeight: true,
      stroke: true,
      strokeWidth: true,
      text: true,
      textAlign: true
    },

    /**
     * @private
     */
    _reNewline: /\r?\n/,

    /**
     * Retrieves object's fontSize
     * @method getFontSize
     * @memberOf fabric.Text.prototype
     * @return {String} Font size (in pixels)
     */

    /**
     * Sets object's fontSize
     * @method setFontSize
     * @memberOf fabric.Text.prototype
     * @param {Number} fontSize Font size (in pixels)
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Retrieves object's fontWeight
     * @method getFontWeight
     * @memberOf fabric.Text.prototype
     * @return {(String|Number)} Font weight
     */

    /**
     * Sets object's fontWeight
     * @method setFontWeight
     * @memberOf fabric.Text.prototype
     * @param {(Number|String)} fontWeight Font weight
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Retrieves object's fontFamily
     * @method getFontFamily
     * @memberOf fabric.Text.prototype
     * @return {String} Font family
     */

    /**
     * Sets object's fontFamily
     * @method setFontFamily
     * @memberOf fabric.Text.prototype
     * @param {String} fontFamily Font family
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Retrieves object's text
     * @method getText
     * @memberOf fabric.Text.prototype
     * @return {String} text
     */

    /**
     * Sets object's text
     * @method setText
     * @memberOf fabric.Text.prototype
     * @param {String} text Text
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Retrieves object's textDecoration
     * @method getTextDecoration
     * @memberOf fabric.Text.prototype
     * @return {String} Text decoration
     */

    /**
     * Sets object's textDecoration
     * @method setTextDecoration
     * @memberOf fabric.Text.prototype
     * @param {String} textDecoration Text decoration
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Retrieves object's fontStyle
     * @method getFontStyle
     * @memberOf fabric.Text.prototype
     * @return {String} Font style
     */

    /**
     * Sets object's fontStyle
     * @method setFontStyle
     * @memberOf fabric.Text.prototype
     * @param {String} fontStyle Font style
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Retrieves object's lineHeight
     * @method getLineHeight
     * @memberOf fabric.Text.prototype
     * @return {Number} Line height
     */

    /**
     * Sets object's lineHeight
     * @method setLineHeight
     * @memberOf fabric.Text.prototype
     * @param {Number} lineHeight Line height
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Retrieves object's textAlign
     * @method getTextAlign
     * @memberOf fabric.Text.prototype
     * @return {String} Text alignment
     */

    /**
     * Sets object's textAlign
     * @method setTextAlign
     * @memberOf fabric.Text.prototype
     * @param {String} textAlign Text alignment
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Retrieves object's textBackgroundColor
     * @method getTextBackgroundColor
     * @memberOf fabric.Text.prototype
     * @return {String} Text background color
     */

    /**
     * Sets object's textBackgroundColor
     * @method setTextBackgroundColor
     * @memberOf fabric.Text.prototype
     * @param {String} textBackgroundColor Text background color
     * @return {fabric.Text}
     * @chainable
     */

    /**
     * Type of an object
     * @type String
     * @default
     */
    type:                 'text',

    /**
     * Font size (in pixels)
     * @type Number
     * @default
     */
    fontSize:             40,

    /**
     * Font weight (e.g. bold, normal, 400, 600, 800)
     * @type {(Number|String)}
     * @default
     */
    fontWeight:           'normal',

    /**
     * Font family
     * @type String
     * @default
     */
    fontFamily:           'Times New Roman',

    /**
     * Text decoration Possible values: "", "underline", "overline" or "line-through".
     * @type String
     * @default
     */
    textDecoration:       '',

    /**
     * Text alignment. Possible values: "left", "center", or "right".
     * @type String
     * @default
     */
    textAlign:            'left',

    /**
     * Font style . Possible values: "", "normal", "italic" or "oblique".
     * @type String
     * @default
     */
    fontStyle:            '',

    /**
     * Line height
     * @type Number
     * @default
     */
    lineHeight:           1.16,

    /**
     * Background color of text lines
     * @type String
     * @default
     */
    textBackgroundColor:  '',

    /**
     * List of properties to consider when checking if
     * state of an object is changed ({@link fabric.Object#hasStateChanged})
     * as well as for history (undo/redo) purposes
     * @type Array
     */
    stateProperties:      stateProperties,

    /**
     * When defined, an object is rendered via stroke and this property specifies its color.
     * <b>Backwards incompatibility note:</b> This property was named "strokeStyle" until v1.1.6
     * @type String
     * @default
     */
    stroke:               null,

    /**
     * Shadow object representing shadow of this shape.
     * <b>Backwards incompatibility note:</b> This property was named "textShadow" (String) until v1.2.11
     * @type fabric.Shadow
     * @default
     */
    shadow:               null,

    /**
     * @private
     */
    _fontSizeFraction: 0.25,

    /**
     * Text Line proportion to font Size (in pixels)
     * @type Number
     * @default
     */
    _fontSizeMult:             1.13,

    /**
     * Constructor
     * @param {String} text Text string
     * @param {Object} [options] Options object
     * @return {fabric.Text} thisArg
     */
    initialize: function(text, options) {
      options = options || { };
      this.text = text;
      this.__skipDimension = true;
      this.setOptions(options);
      this.__skipDimension = false;
      this._initDimensions();
    },

    /**
     * Renders text object on offscreen canvas, so that it would get dimensions
     * @private
     */
    _initDimensions: function(ctx) {
      if (this.__skipDimension) {
        return;
      }
      if (!ctx) {
        ctx = fabric.util.createCanvasElement().getContext('2d');
        this._setTextStyles(ctx);
      }
      this._textLines = this.text.split(this._reNewline);
      this._clearCache();
      var currentTextAlign = this.textAlign;
      this.textAlign = 'left';
      this.width = this._getTextWidth(ctx);
      this.textAlign = currentTextAlign;
      this.height = this._getTextHeight(ctx);
    },

    /**
     * Returns string representation of an instance
     * @return {String} String representation of text object
     */
    toString: function() {
      return '#<fabric.Text (' + this.complexity() +
        '): { "text": "' + this.text + '", "fontFamily": "' + this.fontFamily + '" }>';
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _render: function(ctx) {

      this.clipTo && fabric.util.clipContext(this, ctx);

      this._renderTextBackground(ctx);
      this._renderText(ctx);

      this._renderTextDecoration(ctx);
      this.clipTo && ctx.restore();
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderText: function(ctx) {
      ctx.save();
      this._translateForTextAlign(ctx);
      this._setOpacity(ctx);
      this._setShadow(ctx);
      this._setupCompositeOperation(ctx);
      this._renderTextFill(ctx);
      this._renderTextStroke(ctx);
      this._restoreCompositeOperation(ctx);
      this._removeShadow(ctx);
      ctx.restore();
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _translateForTextAlign: function(ctx) {
      if (this.textAlign !== 'left' && this.textAlign !== 'justify') {
        ctx.translate(this.textAlign === 'center' ? (this.width / 2) : this.width, 0);
      }
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _setTextStyles: function(ctx) {
      ctx.textBaseline = 'alphabetic';
      if (!this.skipTextAlign) {
        ctx.textAlign = this.textAlign;
      }
      ctx.font = this._getFontDeclaration();
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @return {Number} Height of fabric.Text object
     */
    _getTextHeight: function() {
      return this._textLines.length * this._getHeightOfLine();
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @return {Number} Maximum width of fabric.Text object
     */
    _getTextWidth: function(ctx) {
      var maxWidth = this._getLineWidth(ctx, 0);

      for (var i = 1, len = this._textLines.length; i < len; i++) {
        var currentLineWidth = this._getLineWidth(ctx, i);
        if (currentLineWidth > maxWidth) {
          maxWidth = currentLineWidth;
        }
      }
      return maxWidth;
    },

    /**
     * @private
     * @param {String} method Method name ("fillText" or "strokeText")
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {String} chars Chars to render
     * @param {Number} left Left position of text
     * @param {Number} top Top position of text
     */
    _renderChars: function(method, ctx, chars, left, top) {
      ctx[method](chars, left, top);
    },

    /**
     * @private
     * @param {String} method Method name ("fillText" or "strokeText")
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {String} line Text to render
     * @param {Number} left Left position of text
     * @param {Number} top Top position of text
     * @param {Number} lineIndex Index of a line in a text
     */
    _renderTextLine: function(method, ctx, line, left, top, lineIndex) {
      // lift the line by quarter of fontSize
      top -= this.fontSize * this._fontSizeFraction;

      // short-circuit
      if (this.textAlign !== 'justify') {
        this._renderChars(method, ctx, line, left, top, lineIndex);
        return;
      }

      var lineWidth = this._getLineWidth(ctx, lineIndex),
          totalWidth = this.width;
      if (totalWidth >= lineWidth) {
        // stretch the line
        var words = line.split(/\s+/),
            wordsWidth = this._getWidthOfWords(ctx, line, lineIndex),
            widthDiff = totalWidth - wordsWidth,
            numSpaces = words.length - 1,
            spaceWidth = widthDiff / numSpaces,
            leftOffset = 0;

        for (var i = 0, len = words.length; i < len; i++) {
          this._renderChars(method, ctx, words[i], left + leftOffset, top, lineIndex);
          leftOffset += ctx.measureText(words[i]).width + spaceWidth;
        }
      }
      else {
        this._renderChars(method, ctx, line, left, top, lineIndex);
      }
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {Number} line
     */
    _getWidthOfWords: function (ctx, line) {
      return ctx.measureText(line.replace(/\s+/g, '')).width;
    },

    /**
     * @private
     * @return {Number} Left offset
     */
    _getLeftOffset: function() {
      return -this.width / 2;
    },

    /**
     * @private
     * @return {Number} Top offset
     */
    _getTopOffset: function() {
      return -this.height / 2;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderTextFill: function(ctx) {
      if (!this.fill && !this._skipFillStrokeCheck) {
        return;
      }

      var lineHeights = 0;

      for (var i = 0, len = this._textLines.length; i < len; i++) {
        var heightOfLine = this._getHeightOfLine(ctx, i),
            maxHeight = heightOfLine / this.lineHeight;

        this._renderTextLine(
          'fillText',
          ctx,
          this._textLines[i],
          this._getLeftOffset(),
          this._getTopOffset() + lineHeights + maxHeight,
          i
        );
        lineHeights += heightOfLine;
      }
      if (this.shadow && !this.shadow.affectStroke) {
        this._removeShadow(ctx);
      }
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderTextStroke: function(ctx) {
      if ((!this.stroke || this.strokeWidth === 0) && !this._skipFillStrokeCheck) {
        return;
      }

      var lineHeights = 0;

      ctx.save();

      if (this.strokeDashArray) {
        // Spec requires the concatenation of two copies the dash list when the number of elements is odd
        if (1 & this.strokeDashArray.length) {
          this.strokeDashArray.push.apply(this.strokeDashArray, this.strokeDashArray);
        }
        supportsLineDash && ctx.setLineDash(this.strokeDashArray);
      }

      ctx.beginPath();
      for (var i = 0, len = this._textLines.length; i < len; i++) {
        var heightOfLine = this._getHeightOfLine(ctx, i),
            maxHeight = heightOfLine / this.lineHeight;

        this._renderTextLine(
          'strokeText',
          ctx,
          this._textLines[i],
          this._getLeftOffset(),
          this._getTopOffset() + lineHeights + maxHeight,
          i
        );
        lineHeights += heightOfLine;
      }
      ctx.closePath();
      ctx.restore();
    },

    _getHeightOfLine: function() {
      return this.fontSize * this._fontSizeMult * this.lineHeight;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {Array} textLines Array of all text lines
     */
    _renderTextBackground: function(ctx) {
      this._renderTextBoxBackground(ctx);
      this._renderTextLinesBackground(ctx);
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderTextBoxBackground: function(ctx) {
      if (!this.backgroundColor) {
        return;
      }

      ctx.save();
      ctx.fillStyle = this.backgroundColor;

      ctx.fillRect(
        this._getLeftOffset(),
        this._getTopOffset(),
        this.width,
        this.height
      );

      ctx.restore();
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderTextLinesBackground: function(ctx) {
      var lineTopOffset = 0, heightOfLine = this._getHeightOfLine();
      if (!this.textBackgroundColor) {
        return;
      }

      ctx.save();
      ctx.fillStyle = this.textBackgroundColor;

      for (var i = 0, len = this._textLines.length; i < len; i++) {

        if (this._textLines[i] !== '') {

          var lineWidth = this._getLineWidth(ctx, i),
              lineLeftOffset = this._getLineLeftOffset(lineWidth);

          ctx.fillRect(
            this._getLeftOffset() + lineLeftOffset,
            this._getTopOffset() + lineTopOffset,
            lineWidth,
            this.fontSize * this._fontSizeMult
          );
        }
        lineTopOffset += heightOfLine;
      }
      ctx.restore();
    },

    /**
     * @private
     * @param {Number} lineWidth Width of text line
     * @return {Number} Line left offset
     */
    _getLineLeftOffset: function(lineWidth) {
      if (this.textAlign === 'center') {
        return (this.width - lineWidth) / 2;
      }
      if (this.textAlign === 'right') {
        return this.width - lineWidth;
      }
      return 0;
    },

    /**
     * @private
     */
    _clearCache: function() {
      this.__lineWidths = [ ];
      this.__lineHeights = [ ];
      this.__lineOffsets = [ ];
    },

    /**
     * @private
     */
    _shouldClearCache: function() {
      var shouldClear = false;
      for (var prop in this._dimensionAffectingProps) {
        if (this['__' + prop] !== this[prop]) {
          this['__' + prop] = this[prop];
          shouldClear = true;
        }
      }
      return shouldClear;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @return {Number} Line width
     */
    _getLineWidth: function(ctx, lineIndex) {
      if (this.__lineWidths[lineIndex]) {
        return this.__lineWidths[lineIndex];
      }
      this.__lineWidths[lineIndex] = ctx.measureText(this._textLines[lineIndex]).width;
      return this.__lineWidths[lineIndex];
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderTextDecoration: function(ctx) {
      if (!this.textDecoration) {
        return;
      }

      var halfOfVerticalBox = this.height / 2,
          _this = this, offsets = [];

      /** @ignore */
      function renderLinesAtOffset(offsets) {
        var i, lineHeight = 0, len, j, oLen;
        for (i = 0, len = _this._textLines.length; i < len; i++) {

          var lineWidth = _this._getLineWidth(ctx, i),
              lineLeftOffset = _this._getLineLeftOffset(lineWidth),
              heightOfLine = _this._getHeightOfLine(ctx, i);

          for (j = 0, oLen = offsets.length; j < oLen; j++) {
            ctx.fillRect(
              _this._getLeftOffset() + lineLeftOffset,
              lineHeight + (_this._fontSizeMult - 1 + offsets[j] ) * _this.fontSize - halfOfVerticalBox,
              lineWidth,
              _this.fontSize / 15);
          }
          lineHeight += heightOfLine;
        }
      }

      if (this.textDecoration.indexOf('underline') > -1) {
        offsets.push(0.85); // 1 - 3/16
      }
      if (this.textDecoration.indexOf('line-through') > -1) {
        offsets.push(0.43);
      }
      if (this.textDecoration.indexOf('overline') > -1) {
        offsets.push(-0.12);
      }

      if (offsets.length > 0) {
        renderLinesAtOffset(offsets);
      }
    },

    /**
     * @private
     */
    _getFontDeclaration: function() {
      return [
        // node-canvas needs "weight style", while browsers need "style weight"
        (fabric.isLikelyNode ? this.fontWeight : this.fontStyle),
        (fabric.isLikelyNode ? this.fontStyle : this.fontWeight),
        this.fontSize + 'px',
        (fabric.isLikelyNode ? ('"' + this.fontFamily + '"') : this.fontFamily)
      ].join(' ');
    },

    /**
     * Renders text instance on a specified context
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    render: function(ctx, noTransform) {
      // do not render if object is not visible
      if (!this.visible) {
        return;
      }

      ctx.save();
      this._setTextStyles(ctx);

      if (this._shouldClearCache()) {
        this._initDimensions(ctx);
      }
      if (!noTransform) {
        this.transform(ctx);
      }
      this._setStrokeStyles(ctx);
      this._setFillStyles(ctx);
      if (this.transformMatrix) {
        ctx.transform.apply(ctx, this.transformMatrix);
      }
      if (this.group && this.group.type === 'path-group') {
        ctx.translate(this.left, this.top);
      }
      this._render(ctx);
      ctx.restore();
    },

    /**
     * Returns object representation of an instance
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} Object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      var object = extend(this.callSuper('toObject', propertiesToInclude), {
        text:                 this.text,
        fontSize:             this.fontSize,
        fontWeight:           this.fontWeight,
        fontFamily:           this.fontFamily,
        fontStyle:            this.fontStyle,
        lineHeight:           this.lineHeight,
        textDecoration:       this.textDecoration,
        textAlign:            this.textAlign,
        textBackgroundColor:  this.textBackgroundColor
      });
      if (!this.includeDefaultValues) {
        this._removeDefaultValues(object);
      }
      return object;
    },

    /* _TO_SVG_START_ */
    /**
     * Returns SVG representation of an instance
     * @param {Function} [reviver] Method for further parsing of svg representation.
     * @return {String} svg representation of an instance
     */
    toSVG: function(reviver) {
      var markup = this._createBaseSVGMarkup(),
          offsets = this._getSVGLeftTopOffsets(this.ctx),
          textAndBg = this._getSVGTextAndBg(offsets.textTop, offsets.textLeft);
      this._wrapSVGTextAndBg(markup, textAndBg);

      return reviver ? reviver(markup.join('')) : markup.join('');
    },

    /**
     * @private
     */
    _getSVGLeftTopOffsets: function(ctx) {
      var lineTop = this._getHeightOfLine(ctx, 0),
          textLeft = -this.width / 2,
          textTop = 0;

      return {
        textLeft: textLeft + (this.group && this.group.type === 'path-group' ? this.left : 0),
        textTop: textTop + (this.group && this.group.type === 'path-group' ? -this.top : 0),
        lineTop: lineTop
      };
    },

    /**
     * @private
     */
    _wrapSVGTextAndBg: function(markup, textAndBg) {
      markup.push(
        '\t<g transform="', this.getSvgTransform(), this.getSvgTransformMatrix(), '">\n',
          textAndBg.textBgRects.join(''),
          '\t\t<text ',
            (this.fontFamily ? 'font-family="' + this.fontFamily.replace(/"/g, '\'') + '" ': ''),
            (this.fontSize ? 'font-size="' + this.fontSize + '" ': ''),
            (this.fontStyle ? 'font-style="' + this.fontStyle + '" ': ''),
            (this.fontWeight ? 'font-weight="' + this.fontWeight + '" ': ''),
            (this.textDecoration ? 'text-decoration="' + this.textDecoration + '" ': ''),
            'style="', this.getSvgStyles(), '" >',
            textAndBg.textSpans.join(''),
          '</text>\n',
        '\t</g>\n'
      );
    },

    /**
     * @private
     * @param {Number} textTopOffset Text top offset
     * @param {Number} textLeftOffset Text left offset
     * @return {Object}
     */
    _getSVGTextAndBg: function(textTopOffset, textLeftOffset) {
      var textSpans = [ ],
          textBgRects = [ ],
          height = 0;
      // bounding-box background
      this._setSVGBg(textBgRects);

      // text and text-background
      for (var i = 0, len = this._textLines.length; i < len; i++) {
        if (this.textBackgroundColor) {
          this._setSVGTextLineBg(textBgRects, i, textLeftOffset, textTopOffset, height);
        }
        this._setSVGTextLineText(i, textSpans, height, textLeftOffset, textTopOffset, textBgRects);
        height += this._getHeightOfLine(this.ctx, i);
      }

      return {
        textSpans: textSpans,
        textBgRects: textBgRects
      };
    },

    _setSVGTextLineText: function(i, textSpans, height, textLeftOffset, textTopOffset) {
      var yPos = this.fontSize * (this._fontSizeMult - this._fontSizeFraction)
        - textTopOffset + height - this.height / 2;
      textSpans.push(
        '<tspan x="',
          toFixed(textLeftOffset + this._getLineLeftOffset(this.__lineWidths[i]), 4), '" ',
          'y="',
          toFixed(yPos, 4),
          '" ',
          // doing this on <tspan> elements since setting opacity
          // on containing <text> one doesn't work in Illustrator
          this._getFillAttributes(this.fill), '>',
          fabric.util.string.escapeXml(this._textLines[i]),
        '</tspan>'
      );
    },

    _setSVGTextLineBg: function(textBgRects, i, textLeftOffset, textTopOffset, height) {
      textBgRects.push(
        '\t\t<rect ',
          this._getFillAttributes(this.textBackgroundColor),
          ' x="',
          toFixed(textLeftOffset + this._getLineLeftOffset(this.__lineWidths[i]), 4),
          '" y="',
          toFixed(height - this.height / 2, 4),
          '" width="',
          toFixed(this.__lineWidths[i], 4),
          '" height="',
          toFixed(this._getHeightOfLine(this.ctx, i) / this.lineHeight, 4),
        '"></rect>\n');
    },

    _setSVGBg: function(textBgRects) {
      if (this.backgroundColor) {
        textBgRects.push(
          '\t\t<rect ',
            this._getFillAttributes(this.backgroundColor),
            ' x="',
            toFixed(-this.width / 2, 4),
            '" y="',
            toFixed(-this.height / 2, 4),
            '" width="',
            toFixed(this.width, 4),
            '" height="',
            toFixed(this.height, 4),
          '"></rect>\n');
      }
    },

    /**
     * Adobe Illustrator (at least CS5) is unable to render rgba()-based fill values
     * we work around it by "moving" alpha channel into opacity attribute and setting fill's alpha to 1
     *
     * @private
     * @param {Any} value
     * @return {String}
     */
    _getFillAttributes: function(value) {
      var fillColor = (value && typeof value === 'string') ? new fabric.Color(value) : '';
      if (!fillColor || !fillColor.getSource() || fillColor.getAlpha() === 1) {
        return 'fill="' + value + '"';
      }
      return 'opacity="' + fillColor.getAlpha() + '" fill="' + fillColor.setAlpha(1).toRgb() + '"';
    },
    /* _TO_SVG_END_ */

    /**
     * Sets specified property to a specified value
     * @param {String} key
     * @param {Any} value
     * @return {fabric.Text} thisArg
     * @chainable
     */
    _set: function(key, value) {
      this.callSuper('_set', key, value);

      if (key in this._dimensionAffectingProps) {
        this._initDimensions();
        this.setCoords();
      }
    },

    /**
     * Returns complexity of an instance
     * @return {Number} complexity
     */
    complexity: function() {
      return 1;
    }
  });

  /* _FROM_SVG_START_ */
  /**
   * List of attribute names to account for when parsing SVG element (used by {@link fabric.Text.fromElement})
   * @static
   * @memberOf fabric.Text
   * @see: http://www.w3.org/TR/SVG/text.html#TextElement
   */
  fabric.Text.ATTRIBUTE_NAMES = fabric.SHARED_ATTRIBUTES.concat(
    'x y dx dy font-family font-style font-weight font-size text-decoration text-anchor'.split(' '));

  /**
   * Default SVG font size
   * @static
   * @memberOf fabric.Text
   */
  fabric.Text.DEFAULT_SVG_FONT_SIZE = 16;

  /**
   * Returns fabric.Text instance from an SVG element (<b>not yet implemented</b>)
   * @static
   * @memberOf fabric.Text
   * @param {SVGElement} element Element to parse
   * @param {Object} [options] Options object
   * @return {fabric.Text} Instance of fabric.Text
   */
  fabric.Text.fromElement = function(element, options) {
    if (!element) {
      return null;
    }

    var parsedAttributes = fabric.parseAttributes(element, fabric.Text.ATTRIBUTE_NAMES);
    options = fabric.util.object.extend((options ? fabric.util.object.clone(options) : { }), parsedAttributes);

    options.top = options.top || 0;
    options.left = options.left || 0;
    if ('dx' in parsedAttributes) {
      options.left += parsedAttributes.dx;
    }
    if ('dy' in parsedAttributes) {
      options.top += parsedAttributes.dy;
    }
    if (!('fontSize' in options)) {
      options.fontSize = fabric.Text.DEFAULT_SVG_FONT_SIZE;
    }

    if (!options.originX) {
      options.originX = 'left';
    }
    var textContent = element.textContent.replace(/^\s+|\s+$|\n+/g, '').replace(/\s+/g, ' '),
        text = new fabric.Text(textContent, options),
        /*
          Adjust positioning:
            x/y attributes in SVG correspond to the bottom-left corner of text bounding box
            top/left properties in Fabric correspond to center point of text bounding box
        */
        offX = 0;

    if (text.originX === 'left') {
      offX = text.getWidth() / 2;
    }
    if (text.originX === 'right') {
      offX = -text.getWidth() / 2;
    }
    text.set({
      left: text.getLeft() + offX,
      top: text.getTop() - text.getHeight() / 2 + text.fontSize * (0.18 + text._fontSizeFraction) /* 0.3 is the old lineHeight */
    });

    return text;
  };
  /* _FROM_SVG_END_ */

  /**
   * Returns fabric.Text instance from an object representation
   * @static
   * @memberOf fabric.Text
   * @param {Object} object Object to create an instance from
   * @return {fabric.Text} Instance of fabric.Text
   */
  fabric.Text.fromObject = function(object) {
    return new fabric.Text(object.text, clone(object));
  };

  fabric.util.createAccessors(fabric.Text);

})(typeof exports !== 'undefined' ? exports : this);


(function() {

  var clone = fabric.util.object.clone;

  /**
   * IText class (introduced in <b>v1.4</b>) Events are also fired with "text:"
   * prefix when observing canvas.
   * @class fabric.IText
   * @extends fabric.Text
   * @mixes fabric.Observable
   *
   * @fires changed
   * @fires selection:changed
   * @fires editing:entered
   * @fires editing:exited
   *
   * @return {fabric.IText} thisArg
   * @see {@link fabric.IText#initialize} for constructor definition
   *
   * <p>Supported key combinations:</p>
   * <pre>
   *   Move cursor:                    left, right, up, down
   *   Select character:               shift + left, shift + right
   *   Select text vertically:         shift + up, shift + down
   *   Move cursor by word:            alt + left, alt + right
   *   Select words:                   shift + alt + left, shift + alt + right
   *   Move cursor to line start/end:  cmd + left, cmd + right or home, end
   *   Select till start/end of line:  cmd + shift + left, cmd + shift + right or shift + home, shift + end
   *   Jump to start/end of text:      cmd + up, cmd + down
   *   Select till start/end of text:  cmd + shift + up, cmd + shift + down or shift + pgUp, shift + pgDown
   *   Delete character:               backspace
   *   Delete word:                    alt + backspace
   *   Delete line:                    cmd + backspace
   *   Forward delete:                 delete
   *   Copy text:                      ctrl/cmd + c
   *   Paste text:                     ctrl/cmd + v
   *   Cut text:                       ctrl/cmd + x
   *   Select entire text:             ctrl/cmd + a
   *   Quit editing                    tab or esc
   * </pre>
   *
   * <p>Supported mouse/touch combination</p>
   * <pre>
   *   Position cursor:                click/touch
   *   Create selection:               click/touch & drag
   *   Create selection:               click & shift + click
   *   Select word:                    double click
   *   Select line:                    triple click
   * </pre>
   */
  fabric.IText = fabric.util.createClass(fabric.Text, fabric.Observable, /** @lends fabric.IText.prototype */ {

    /**
     * Type of an object
     * @type String
     * @default
     */
    type: 'i-text',

    /**
     * Index where text selection starts (or where cursor is when there is no selection)
     * @type Nubmer
     * @default
     */
    selectionStart: 0,

    /**
     * Index where text selection ends
     * @type Nubmer
     * @default
     */
    selectionEnd: 0,

    /**
     * Color of text selection
     * @type String
     * @default
     */
    selectionColor: 'rgba(17,119,255,0.3)',

    /**
     * Indicates whether text is in editing mode
     * @type Boolean
     * @default
     */
    isEditing: false,

    /**
     * Indicates whether a text can be edited
     * @type Boolean
     * @default
     */
    editable: true,

    /**
     * Border color of text object while it's in editing mode
     * @type String
     * @default
     */
    editingBorderColor: 'rgba(102,153,255,0.25)',

    /**
     * Width of cursor (in px)
     * @type Number
     * @default
     */
    cursorWidth: 2,

    /**
     * Color of default cursor (when not overwritten by character style)
     * @type String
     * @default
     */
    cursorColor: '#333',

    /**
     * Delay between cursor blink (in ms)
     * @type Number
     * @default
     */
    cursorDelay: 1000,

    /**
     * Duration of cursor fadein (in ms)
     * @type Number
     * @default
     */
    cursorDuration: 600,

    /**
     * Object containing character styles
     * (where top-level properties corresponds to line number and 2nd-level properties -- to char number in a line)
     * @type Object
     * @default
     */
    styles: null,

    /**
     * Indicates whether internal text char widths can be cached
     * @type Boolean
     * @default
     */
    caching: true,

    /**
     * @private
     * @type Boolean
     * @default
     */
    _skipFillStrokeCheck: false,

    /**
     * @private
     */
    _reSpace: /\s|\n/,

    /**
     * @private
     */
    _currentCursorOpacity: 0,

    /**
     * @private
     */
    _selectionDirection: null,

    /**
     * @private
     */
    _abortCursorAnimation: false,

    /**
     * @private
     */
    _charWidthsCache: { },

    /**
     * Constructor
     * @param {String} text Text string
     * @param {Object} [options] Options object
     * @return {fabric.IText} thisArg
     */
    initialize: function(text, options) {
      this.styles = options ? (options.styles || { }) : { };
      this.callSuper('initialize', text, options);
      this.initBehavior();
    },

    /**
     * @private
     */
    _clearCache: function() {
      this.callSuper('_clearCache');
      this.__maxFontHeights = [ ];
      this.__widthOfSpace = [ ];
    },

    /**
     * Returns true if object has no styling
     */
    isEmptyStyles: function() {
      if (!this.styles) {
        return true;
      }
      var obj = this.styles;

      for (var p1 in obj) {
        for (var p2 in obj[p1]) {
          /*jshint unused:false */
          for (var p3 in obj[p1][p2]) {
            return false;
          }
        }
      }
      return true;
    },

    /**
     * Sets selection start (left boundary of a selection)
     * @param {Number} index Index to set selection start to
     */
    setSelectionStart: function(index) {
      index = Math.max(index, 0);
      if (this.selectionStart !== index) {
        this.fire('selection:changed');
        this.canvas && this.canvas.fire('text:selection:changed', { target: this });
        this.selectionStart = index;
      }
      this._updateTextarea();
    },

    /**
     * Sets selection end (right boundary of a selection)
     * @param {Number} index Index to set selection end to
     */
    setSelectionEnd: function(index) {
      index = Math.min(index, this.text.length);
      if (this.selectionEnd !== index) {
        this.fire('selection:changed');
        this.canvas && this.canvas.fire('text:selection:changed', { target: this });
        this.selectionEnd = index;
      }
      this._updateTextarea();
    },

    /**
     * Gets style of a current selection/cursor (at the start position)
     * @param {Number} [startIndex] Start index to get styles at
     * @param {Number} [endIndex] End index to get styles at
     * @return {Object} styles Style object at a specified (or current) index
     */
    getSelectionStyles: function(startIndex, endIndex) {

      if (arguments.length === 2) {
        var styles = [ ];
        for (var i = startIndex; i < endIndex; i++) {
          styles.push(this.getSelectionStyles(i));
        }
        return styles;
      }

      var loc = this.get2DCursorLocation(startIndex);
      if (this.styles[loc.lineIndex]) {
        return this.styles[loc.lineIndex][loc.charIndex] || { };
      }

      return { };
    },

    /**
     * Sets style of a current selection
     * @param {Object} [styles] Styles object
     * @return {fabric.IText} thisArg
     * @chainable
     */
    setSelectionStyles: function(styles) {
      if (this.selectionStart === this.selectionEnd) {
        this._extendStyles(this.selectionStart, styles);
      }
      else {
        for (var i = this.selectionStart; i < this.selectionEnd; i++) {
          this._extendStyles(i, styles);
        }
      }
      /* not included in _extendStyles to avoid clearing cache more than once */
      this._clearCache();
      return this;
    },

    /**
     * @private
     */
    _extendStyles: function(index, styles) {
      var loc = this.get2DCursorLocation(index);

      if (!this.styles[loc.lineIndex]) {
        this.styles[loc.lineIndex] = { };
      }
      if (!this.styles[loc.lineIndex][loc.charIndex]) {
        this.styles[loc.lineIndex][loc.charIndex] = { };
      }
      fabric.util.object.extend(this.styles[loc.lineIndex][loc.charIndex], styles);
    },

    /**
    * @private
    * @param {CanvasRenderingContext2D} ctx Context to render on
    */
    _render: function(ctx) {
      this.callSuper('_render', ctx);
      this.ctx = ctx;
      this.isEditing && this.renderCursorOrSelection();
    },

    /**
     * Renders cursor or selection (depending on what exists)
     */
    renderCursorOrSelection: function() {
      if (!this.active) {
        return;
      }

      var chars = this.text.split(''),
          boundaries, ctx;

      if (this.canvas.contextTop) {
        ctx = this.canvas.contextTop;
        ctx.save();
        ctx.transform.apply(ctx, this.canvas.viewportTransform);
        this.transform(ctx);
      }
      else {
        ctx = this.ctx;
        ctx.save();
      }

      if (this.selectionStart === this.selectionEnd) {
        boundaries = this._getCursorBoundaries(chars, 'cursor');
        this.renderCursor(boundaries, ctx);
      }
      else {
        boundaries = this._getCursorBoundaries(chars, 'selection');
        this.renderSelection(chars, boundaries, ctx);
      }

      ctx.restore();
    },

    /**
     * Returns 2d representation (lineIndex and charIndex) of cursor (or selection start)
     * @param {Number} [selectionStart] Optional index. When not given, current selectionStart is used.
     */
    get2DCursorLocation: function(selectionStart) {
      if (typeof selectionStart === 'undefined') {
        selectionStart = this.selectionStart;
      }
      var textBeforeCursor = this.text.slice(0, selectionStart),
          linesBeforeCursor = textBeforeCursor.split(this._reNewline);

      return {
        lineIndex: linesBeforeCursor.length - 1,
        charIndex: linesBeforeCursor[linesBeforeCursor.length - 1].length
      };
    },

    /**
     * Returns complete style of char at the current cursor
     * @param {Number} lineIndex Line index
     * @param {Number} charIndex Char index
    * @return {Object} Character style
     */
    getCurrentCharStyle: function(lineIndex, charIndex) {
      var style = this.styles[lineIndex] && this.styles[lineIndex][charIndex === 0 ? 0 : (charIndex - 1)];

      return {
        fontSize: style && style.fontSize || this.fontSize,
        fill: style && style.fill || this.fill,
        textBackgroundColor: style && style.textBackgroundColor || this.textBackgroundColor,
        textDecoration: style && style.textDecoration || this.textDecoration,
        fontFamily: style && style.fontFamily || this.fontFamily,
        fontWeight: style && style.fontWeight || this.fontWeight,
        fontStyle: style && style.fontStyle || this.fontStyle,
        stroke: style && style.stroke || this.stroke,
        strokeWidth: style && style.strokeWidth || this.strokeWidth
      };
    },

    /**
     * Returns fontSize of char at the current cursor
     * @param {Number} lineIndex Line index
     * @param {Number} charIndex Char index
     * @return {Number} Character font size
     */
    getCurrentCharFontSize: function(lineIndex, charIndex) {
      return (
        this.styles[lineIndex] &&
        this.styles[lineIndex][charIndex === 0 ? 0 : (charIndex - 1)] &&
        this.styles[lineIndex][charIndex === 0 ? 0 : (charIndex - 1)].fontSize) || this.fontSize;
    },

    /**
     * Returns color (fill) of char at the current cursor
     * @param {Number} lineIndex Line index
     * @param {Number} charIndex Char index
     * @return {String} Character color (fill)
     */
    getCurrentCharColor: function(lineIndex, charIndex) {
      return (
        this.styles[lineIndex] &&
        this.styles[lineIndex][charIndex === 0 ? 0 : (charIndex - 1)] &&
        this.styles[lineIndex][charIndex === 0 ? 0 : (charIndex - 1)].fill) || this.cursorColor;
    },

    /**
     * Returns cursor boundaries (left, top, leftOffset, topOffset)
     * @private
     * @param {Array} chars Array of characters
     * @param {String} typeOfBoundaries
     */
    _getCursorBoundaries: function(chars, typeOfBoundaries) {

      // left/top are left/top of entire text box
      // leftOffset/topOffset are offset from that left/top point of a text box

      var left = Math.round(this._getLeftOffset()),
          top = this._getTopOffset(),

          offsets = this._getCursorBoundariesOffsets(
                      chars, typeOfBoundaries);

      return {
        left: left,
        top: top,
        leftOffset: offsets.left + offsets.lineLeft,
        topOffset: offsets.top
      };
    },

    /**
     * @private
     */
    _getCursorBoundariesOffsets: function(chars, typeOfBoundaries) {

      var lineLeftOffset = 0,

          lineIndex = 0,
          charIndex = 0,
          topOffset = 0,
          leftOffset = 0;

      for (var i = 0; i < this.selectionStart; i++) {
        if (chars[i] === '\n') {
          leftOffset = 0;
          topOffset += this._getHeightOfLine(this.ctx, lineIndex);

          lineIndex++;
          charIndex = 0;
        }
        else {
          leftOffset += this._getWidthOfChar(this.ctx, chars[i], lineIndex, charIndex);
          charIndex++;
        }

        lineLeftOffset = this._getCachedLineOffset(lineIndex);
      }
      if (typeOfBoundaries === 'cursor') {
        topOffset += (1 - this._fontSizeFraction) * this._getHeightOfLine(this.ctx, lineIndex) / this.lineHeight
          - this.getCurrentCharFontSize(lineIndex, charIndex) * (1 - this._fontSizeFraction);
      }

      return {
        top: topOffset,
        left: leftOffset,
        lineLeft: lineLeftOffset
      };
    },

    /**
     * @private
     */
    _getCachedLineOffset: function(lineIndex) {
      var widthOfLine = this._getLineWidth(this.ctx, lineIndex);

      return this.__lineOffsets[lineIndex] ||
        (this.__lineOffsets[lineIndex] = this._getLineLeftOffset(widthOfLine));
    },

    /**
     * Renders cursor
     * @param {Object} boundaries
     * @param {CanvasRenderingContext2D} ctx transformed context to draw on
     */
    renderCursor: function(boundaries, ctx) {

      var cursorLocation = this.get2DCursorLocation(),
          lineIndex = cursorLocation.lineIndex,
          charIndex = cursorLocation.charIndex,
          charHeight = this.getCurrentCharFontSize(lineIndex, charIndex),
          leftOffset = (lineIndex === 0 && charIndex === 0)
                    ? this._getCachedLineOffset(lineIndex)
                    : boundaries.leftOffset;

      ctx.fillStyle = this.getCurrentCharColor(lineIndex, charIndex);
      ctx.globalAlpha = this.__isMousedown ? 1 : this._currentCursorOpacity;

      ctx.fillRect(
        boundaries.left + leftOffset,
        boundaries.top + boundaries.topOffset,
        this.cursorWidth / this.scaleX,
        charHeight);

    },

    /**
     * Renders text selection
     * @param {Array} chars Array of characters
     * @param {Object} boundaries Object with left/top/leftOffset/topOffset
     * @param {CanvasRenderingContext2D} ctx transformed context to draw on
     */
    renderSelection: function(chars, boundaries, ctx) {

      ctx.fillStyle = this.selectionColor;

      var start = this.get2DCursorLocation(this.selectionStart),
          end = this.get2DCursorLocation(this.selectionEnd),
          startLine = start.lineIndex,
          endLine = end.lineIndex;

      for (var i = startLine; i <= endLine; i++) {
        var lineOffset = this._getCachedLineOffset(i) || 0,
            lineHeight = this._getHeightOfLine(this.ctx, i),
            boxWidth = 0, line = this._textLines[i];

        if (i === startLine) {
          for (var j = 0, len = line.length; j < len; j++) {
            if (j >= start.charIndex && (i !== endLine || j < end.charIndex)) {
              boxWidth += this._getWidthOfChar(ctx, line[j], i, j);
            }
            if (j < start.charIndex) {
              lineOffset += this._getWidthOfChar(ctx, line[j], i, j);
            }
          }
        }
        else if (i > startLine && i < endLine) {
          boxWidth += this._getLineWidth(ctx, i) || 5;
        }
        else if (i === endLine) {
          for (var j2 = 0, j2len = end.charIndex; j2 < j2len; j2++) {
            boxWidth += this._getWidthOfChar(ctx, line[j2], i, j2);
          }
        }

        ctx.fillRect(
          boundaries.left + lineOffset,
          boundaries.top + boundaries.topOffset,
          boxWidth,
          lineHeight);

        boundaries.topOffset += lineHeight;
      }
    },

    /**
     * @private
     * @param {String} method
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderChars: function(method, ctx, line, left, top, lineIndex) {

      if (this.isEmptyStyles()) {
        return this._renderCharsFast(method, ctx, line, left, top);
      }

      this.skipTextAlign = true;

      // set proper box offset
      left -= this.textAlign === 'center'
        ? (this.width / 2)
        : (this.textAlign === 'right')
          ? this.width
          : 0;

      // set proper line offset
      var lineHeight = this._getHeightOfLine(ctx, lineIndex),
          lineLeftOffset = this._getCachedLineOffset(lineIndex),
          chars = line.split(''),
          prevStyle,
          charsToRender = '';

      left += lineLeftOffset || 0;

      ctx.save();
      top -= lineHeight / this.lineHeight * this._fontSizeFraction;
      for (var i = 0, len = chars.length; i <= len; i++) {
        prevStyle = prevStyle || this.getCurrentCharStyle(lineIndex, i);
        var thisStyle = this.getCurrentCharStyle(lineIndex, i + 1);

        if (this._hasStyleChanged(prevStyle, thisStyle) || i === len) {
          this._renderChar(method, ctx, lineIndex, i - 1, charsToRender, left, top, lineHeight);
          charsToRender = '';
          prevStyle = thisStyle;
        }
        charsToRender += chars[i];
      }

      ctx.restore();
    },

    /**
     * @private
     * @param {String} method
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {String} line Content of the line
     * @param {Number} left Left coordinate
     * @param {Number} top Top coordinate
     */
    _renderCharsFast: function(method, ctx, line, left, top) {
      this.skipTextAlign = false;

      if (method === 'fillText' && this.fill) {
        this.callSuper('_renderChars', method, ctx, line, left, top);
      }
      if (method === 'strokeText' && ((this.stroke && this.strokeWidth > 0) || this.skipFillStrokeCheck)) {
        this.callSuper('_renderChars', method, ctx, line, left, top);
      }
    },

    /**
     * @private
     * @param {String} method
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {Number} lineIndex
     * @param {Number} i
     * @param {String} _char
     * @param {Number} left Left coordinate
     * @param {Number} top Top coordinate
     * @param {Number} lineHeight Height of the line
     */
    _renderChar: function(method, ctx, lineIndex, i, _char, left, top, lineHeight) {
      var decl, charWidth, charHeight,
          offset = this._fontSizeFraction * lineHeight / this.lineHeight;

      if (this.styles && this.styles[lineIndex] && (decl = this.styles[lineIndex][i])) {

        var shouldStroke = decl.stroke || this.stroke,
            shouldFill = decl.fill || this.fill;

        ctx.save();
        charWidth = this._applyCharStylesGetWidth(ctx, _char, lineIndex, i, decl);
        charHeight = this._getHeightOfChar(ctx, _char, lineIndex, i);

        if (shouldFill) {
          ctx.fillText(_char, left, top);
        }
        if (shouldStroke) {
          ctx.strokeText(_char, left, top);
        }

        this._renderCharDecoration(ctx, decl, left, top, offset, charWidth, charHeight);
        ctx.restore();

        ctx.translate(charWidth, 0);
      }
      else {
        if (method === 'strokeText' && this.stroke) {
          ctx[method](_char, left, top);
        }
        if (method === 'fillText' && this.fill) {
          ctx[method](_char, left, top);
        }
        charWidth = this._applyCharStylesGetWidth(ctx, _char, lineIndex, i);
        this._renderCharDecoration(ctx, null, left, top, offset, charWidth, this.fontSize);

        ctx.translate(ctx.measureText(_char).width, 0);
      }
    },

    /**
     * @private
     * @param {Object} prevStyle
     * @param {Object} thisStyle
     */
    _hasStyleChanged: function(prevStyle, thisStyle) {
      return (prevStyle.fill !== thisStyle.fill ||
              prevStyle.fontSize !== thisStyle.fontSize ||
              prevStyle.textBackgroundColor !== thisStyle.textBackgroundColor ||
              prevStyle.textDecoration !== thisStyle.textDecoration ||
              prevStyle.fontFamily !== thisStyle.fontFamily ||
              prevStyle.fontWeight !== thisStyle.fontWeight ||
              prevStyle.fontStyle !== thisStyle.fontStyle ||
              prevStyle.stroke !== thisStyle.stroke ||
              prevStyle.strokeWidth !== thisStyle.strokeWidth
      );
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderCharDecoration: function(ctx, styleDeclaration, left, top, offset, charWidth, charHeight) {

      var textDecoration = styleDeclaration
            ? (styleDeclaration.textDecoration || this.textDecoration)
            : this.textDecoration;

      if (!textDecoration) {
        return;
      }

      if (textDecoration.indexOf('underline') > -1) {
        ctx.fillRect(
          left,
          top + charHeight / 10,
          charWidth ,
          charHeight / 15
        );
      }
      if (textDecoration.indexOf('line-through') > -1) {
        ctx.fillRect(
          left,
          top - charHeight * (this._fontSizeFraction + this._fontSizeMult - 1) + charHeight / 15,
          charWidth,
          charHeight / 15
        );
      }
      if (textDecoration.indexOf('overline') > -1) {
        ctx.fillRect(
          left,
          top - (this._fontSizeMult - this._fontSizeFraction) * charHeight,
          charWidth,
          charHeight / 15
        );
      }
    },

    /**
     * @private
     * @param {String} method
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {String} line
     */
    _renderTextLine: function(method, ctx, line, left, top, lineIndex) {
      // to "cancel" this.fontSize subtraction in fabric.Text#_renderTextLine
      // the adding 0.03 is just to align text with itext by overlap test
      if (!this.isEmptyStyles()) {
        top += this.fontSize * (this._fontSizeFraction + 0.03);
      }
      this.callSuper('_renderTextLine', method, ctx, line, left, top, lineIndex);
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderTextDecoration: function(ctx) {
      if (this.isEmptyStyles()) {
        return this.callSuper('_renderTextDecoration', ctx);
      }
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _renderTextLinesBackground: function(ctx) {
      if (!this.textBackgroundColor && !this.styles) {
        return;
      }

      ctx.save();

      if (this.textBackgroundColor) {
        ctx.fillStyle = this.textBackgroundColor;
      }

      var lineHeights = 0;

      for (var i = 0, len = this._textLines.length; i < len; i++) {

        var heightOfLine = this._getHeightOfLine(ctx, i);
        if (this._textLines[i] === '') {
          lineHeights += heightOfLine;
          continue;
        }

        var lineWidth = this._getLineWidth(ctx, i),
            lineLeftOffset = this._getCachedLineOffset(i);

        if (this.textBackgroundColor) {
          ctx.fillStyle = this.textBackgroundColor;

          ctx.fillRect(
            this._getLeftOffset() + lineLeftOffset,
            this._getTopOffset() + lineHeights,
            lineWidth,
            heightOfLine / this.lineHeight
          );
        }
        if (this.styles[i]) {
          for (var j = 0, jlen = this._textLines[i].length; j < jlen; j++) {
            if (this.styles[i] && this.styles[i][j] && this.styles[i][j].textBackgroundColor) {

              var _char = this._textLines[i][j];

              ctx.fillStyle = this.styles[i][j].textBackgroundColor;

              ctx.fillRect(
                this._getLeftOffset() + lineLeftOffset + this._getWidthOfCharsAt(ctx, i, j),
                this._getTopOffset() + lineHeights,
                this._getWidthOfChar(ctx, _char, i, j) + 1,
                heightOfLine / this.lineHeight
              );
            }
          }
        }
        lineHeights += heightOfLine;
      }
      ctx.restore();
    },

    /**
     * @private
     */
    _getCacheProp: function(_char, styleDeclaration) {
      return _char +
             styleDeclaration.fontFamily +
             styleDeclaration.fontSize +
             styleDeclaration.fontWeight +
             styleDeclaration.fontStyle +
             styleDeclaration.shadow;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {String} _char
     * @param {Number} lineIndex
     * @param {Number} charIndex
     * @param {Object} [decl]
     */
    _applyCharStylesGetWidth: function(ctx, _char, lineIndex, charIndex, decl) {
      var styleDeclaration = decl ||
                            (this.styles[lineIndex] &&
                             this.styles[lineIndex][charIndex]);

      if (styleDeclaration) {
        // cloning so that original style object is not polluted with following font declarations
        styleDeclaration = clone(styleDeclaration);
      }
      else {
        styleDeclaration = { };
      }

      this._applyFontStyles(styleDeclaration);

      var cacheProp = this._getCacheProp(_char, styleDeclaration);

      // short-circuit if no styles
      if (this.isEmptyStyles() && this._charWidthsCache[cacheProp] && this.caching) {
        return this._charWidthsCache[cacheProp];
      }

      if (typeof styleDeclaration.shadow === 'string') {
        styleDeclaration.shadow = new fabric.Shadow(styleDeclaration.shadow);
      }

      var fill = styleDeclaration.fill || this.fill;
      ctx.fillStyle = fill.toLive
        ? fill.toLive(ctx, this)
        : fill;

      if (styleDeclaration.stroke) {
        ctx.strokeStyle = (styleDeclaration.stroke && styleDeclaration.stroke.toLive)
          ? styleDeclaration.stroke.toLive(ctx, this)
          : styleDeclaration.stroke;
      }

      ctx.lineWidth = styleDeclaration.strokeWidth || this.strokeWidth;
      ctx.font = this._getFontDeclaration.call(styleDeclaration);
      this._setShadow.call(styleDeclaration, ctx);

      if (!this.caching) {
        return ctx.measureText(_char).width;
      }

      if (!this._charWidthsCache[cacheProp]) {
        this._charWidthsCache[cacheProp] = ctx.measureText(_char).width;
      }

      return this._charWidthsCache[cacheProp];
    },

    /**
     * @private
     * @param {Object} styleDeclaration
     */
    _applyFontStyles: function(styleDeclaration) {
      if (!styleDeclaration.fontFamily) {
        styleDeclaration.fontFamily = this.fontFamily;
      }
      if (!styleDeclaration.fontSize) {
        styleDeclaration.fontSize = this.fontSize;
      }
      if (!styleDeclaration.fontWeight) {
        styleDeclaration.fontWeight = this.fontWeight;
      }
      if (!styleDeclaration.fontStyle) {
        styleDeclaration.fontStyle = this.fontStyle;
      }
    },

    /**
     * @private
     * @param {Number} lineIndex
     * @param {Number} charIndex
     */
    _getStyleDeclaration: function(lineIndex, charIndex) {
      return (this.styles[lineIndex] && this.styles[lineIndex][charIndex])
        ? clone(this.styles[lineIndex][charIndex])
        : { };
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _getWidthOfChar: function(ctx, _char, lineIndex, charIndex) {
      if (this.textAlign === 'justify' && /\s/.test(_char)) {
        return this._getWidthOfSpace(ctx, lineIndex);
      }

      var styleDeclaration = this._getStyleDeclaration(lineIndex, charIndex);
      this._applyFontStyles(styleDeclaration);
      var cacheProp = this._getCacheProp(_char, styleDeclaration);

      if (this._charWidthsCache[cacheProp] && this.caching) {
        return this._charWidthsCache[cacheProp];
      }
      else if (ctx) {
        ctx.save();
        var width = this._applyCharStylesGetWidth(ctx, _char, lineIndex, charIndex);
        ctx.restore();
        return width;
      }
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _getHeightOfChar: function(ctx, _char, lineIndex, charIndex) {
      if (this.styles[lineIndex] && this.styles[lineIndex][charIndex]) {
        return this.styles[lineIndex][charIndex].fontSize || this.fontSize;
      }
      return this.fontSize;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _getHeightOfCharAt: function(ctx, lineIndex, charIndex) {
      var _char = this._textLines[lineIndex][charIndex];
      return this._getHeightOfChar(ctx, _char, lineIndex, charIndex);
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _getWidthOfCharsAt: function(ctx, lineIndex, charIndex) {
      var width = 0, i, _char;
      for (i = 0; i < charIndex; i++) {
        _char = this._textLines[lineIndex][i];
        width += this._getWidthOfChar(ctx, _char, lineIndex, i);
      }
      return width;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _getLineWidth: function(ctx, lineIndex) {
      if (this.__lineWidths[lineIndex]) {
        return this.__lineWidths[lineIndex];
      }
      this.__lineWidths[lineIndex] = this._getWidthOfCharsAt(ctx, lineIndex, this._textLines[lineIndex].length);
      return this.__lineWidths[lineIndex];
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {Number} lineIndex
     */
    _getWidthOfSpace: function (ctx, lineIndex) {
      if (this.__widthOfSpace[lineIndex]) {
        return this.__widthOfSpace[lineIndex];
      }
      var line = this._textLines[lineIndex],
          wordsWidth = this._getWidthOfWords(ctx, line, lineIndex),
          widthDiff = this.width - wordsWidth,
          numSpaces = line.length - line.replace(/\s+/g, '').length,
          width = widthDiff / numSpaces;
      this.__widthOfSpace[lineIndex] = width;
      return width;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     * @param {Number} line
     * @param {Number} lineIndex
     */
    _getWidthOfWords: function (ctx, line, lineIndex) {
      var width = 0;

      for (var charIndex = 0; charIndex < line.length; charIndex++) {
        var _char = line[charIndex];

        if (!_char.match(/\s/)) {
          width += this._getWidthOfChar(ctx, _char, lineIndex, charIndex);
        }
      }

      return width;
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _getHeightOfLine: function(ctx, lineIndex) {
      if (this.__lineHeights[lineIndex]) {
        return this.__lineHeights[lineIndex];
      }

      var line = this._textLines[lineIndex],
          maxHeight = this._getHeightOfChar(ctx, line[0], lineIndex, 0);

      for (var i = 1, len = line.length; i < len; i++) {
        var currentCharHeight = this._getHeightOfChar(ctx, line[i], lineIndex, i);
        if (currentCharHeight > maxHeight) {
          maxHeight = currentCharHeight;
        }
      }
      this.__maxFontHeights[lineIndex] = maxHeight;
      this.__lineHeights[lineIndex] = maxHeight * this.lineHeight * this._fontSizeMult;
      return this.__lineHeights[lineIndex];
    },

    /**
     * @private
     * @param {CanvasRenderingContext2D} ctx Context to render on
     */
    _getTextHeight: function(ctx) {
      var height = 0;
      for (var i = 0, len = this._textLines.length; i < len; i++) {
        height += this._getHeightOfLine(ctx, i);
      }
      return height;
    },

    /**
     * This method is overwritten to account for different top offset
     * @private
     */
    _renderTextBoxBackground: function(ctx) {
      if (!this.backgroundColor) {
        return;
      }

      ctx.save();
      ctx.fillStyle = this.backgroundColor;

      ctx.fillRect(
        this._getLeftOffset(),
        this._getTopOffset(),
        this.width,
        this.height
      );

      ctx.restore();
    },

    /**
     * Returns object representation of an instance
     * @method toObject
     * @param {Array} [propertiesToInclude] Any properties that you might want to additionally include in the output
     * @return {Object} object representation of an instance
     */
    toObject: function(propertiesToInclude) {
      return fabric.util.object.extend(this.callSuper('toObject', propertiesToInclude), {
        styles: clone(this.styles)
      });
    }
  });

  /**
   * Returns fabric.IText instance from an object representation
   * @static
   * @memberOf fabric.IText
   * @param {Object} object Object to create an instance from
   * @return {fabric.IText} instance of fabric.IText
   */
  fabric.IText.fromObject = function(object) {
    return new fabric.IText(object.text, clone(object));
  };
})();


(function() {

  var clone = fabric.util.object.clone;

  fabric.util.object.extend(fabric.IText.prototype, /** @lends fabric.IText.prototype */ {

    /**
     * Initializes all the interactive behavior of IText
     */
    initBehavior: function() {
      this.initAddedHandler();
      this.initRemovedHandler();
      this.initCursorSelectionHandlers();
      this.initDoubleClickSimulation();
    },

    /**
     * Initializes "selected" event handler
     */
    initSelectedHandler: function() {
      this.on('selected', function() {

        var _this = this;
        setTimeout(function() {
          _this.selected = true;
        }, 100);
      });
    },

    /**
     * Initializes "added" event handler
     */
    initAddedHandler: function() {
      var _this = this;
      this.on('added', function() {
        if (this.canvas && !this.canvas._hasITextHandlers) {
          this.canvas._hasITextHandlers = true;
          this._initCanvasHandlers();
        }

        // Track IText instances per-canvas. Only register in this array once added
        // to a canvas; we don't want to leak a reference to the instance forever
        // simply because it existed at some point.
        //
        // (Might be added to a collection, but not on a canvas.)
        if (_this.canvas) {
          _this.canvas._iTextInstances = _this.canvas._iTextInstances || [];
          _this.canvas._iTextInstances.push(_this);
        }
      });
    },

    initRemovedHandler: function() {
      var _this = this;
      this.on('removed', function() {
        // (Might be removed from a collection, but not on a canvas.)
        if (_this.canvas) {
          _this.canvas._iTextInstances = _this.canvas._iTextInstances || [];
          fabric.util.removeFromArray(_this.canvas._iTextInstances, _this);
        }
      });
    },

    /**
     * @private
     */
    _initCanvasHandlers: function() {
      var _this = this;

      this.canvas.on('selection:cleared', function() {
        fabric.IText.prototype.exitEditingOnOthers(_this.canvas);
      });

      this.canvas.on('mouse:up', function() {
        if (_this.canvas._iTextInstances) {
          _this.canvas._iTextInstances.forEach(function(obj) {
            obj.__isMousedown = false;
          });
        }
      });

      this.canvas.on('object:selected', function() {
        fabric.IText.prototype.exitEditingOnOthers(_this.canvas);
      });
    },

    /**
     * @private
     */
    _tick: function() {
      this._currentTickState = this._animateCursor(this, 1, this.cursorDuration, '_onTickComplete');
    },

    /**
     * @private
     */
    _animateCursor: function(obj, targetOpacity, duration, completeMethod) {

      var tickState;

      tickState = {
        isAborted: false,
        abort: function() {
          this.isAborted = true;
        },
      };

      obj.animate('_currentCursorOpacity', targetOpacity, {
        duration: duration,
        onComplete: function() {
          if (!tickState.isAborted) {
            obj[completeMethod]();
          }
        },
        onChange: function() {
          if (obj.canvas) {
            obj.canvas.clearContext(obj.canvas.contextTop || obj.ctx);
            obj.renderCursorOrSelection();
          }
        },
        abort: function() {
          return tickState.isAborted;
        }
      });
      return tickState;
    },

    /**
     * @private
     */
    _onTickComplete: function() {

      var _this = this;

      if (this._cursorTimeout1) {
        clearTimeout(this._cursorTimeout1);
      }
      this._cursorTimeout1 = setTimeout(function() {
        _this._currentTickCompleteState = _this._animateCursor(_this, 0, this.cursorDuration / 2, '_tick');
      }, 100);
    },

    /**
     * Initializes delayed cursor
     */
    initDelayedCursor: function(restart) {
      var _this = this,
          delay = restart ? 0 : this.cursorDelay;

      this._currentTickState && this._currentTickState.abort();
      this._currentTickCompleteState && this._currentTickCompleteState.abort();
      clearTimeout(this._cursorTimeout1);
      this._currentCursorOpacity = 1;
      if (this.canvas) {
        this.canvas.clearContext(this.canvas.contextTop || this.ctx);
        this.renderCursorOrSelection();
      }
      if (this._cursorTimeout2) {
        clearTimeout(this._cursorTimeout2);
      }
      this._cursorTimeout2 = setTimeout(function() {
        _this._tick();
      }, delay);
    },

    /**
     * Aborts cursor animation and clears all timeouts
     */
    abortCursorAnimation: function() {
      this._currentTickState && this._currentTickState.abort();
      this._currentTickCompleteState && this._currentTickCompleteState.abort();

      clearTimeout(this._cursorTimeout1);
      clearTimeout(this._cursorTimeout2);

      this._currentCursorOpacity = 0;
      this.canvas && this.canvas.clearContext(this.canvas.contextTop || this.ctx);
    },

    /**
     * Selects entire text
     */
    selectAll: function() {
      this.setSelectionStart(0);
      this.setSelectionEnd(this.text.length);
    },

    /**
     * Returns selected text
     * @return {String}
     */
    getSelectedText: function() {
      return this.text.slice(this.selectionStart, this.selectionEnd);
    },

    /**
     * Find new selection index representing start of current word according to current selection index
     * @param {Number} startFrom Surrent selection index
     * @return {Number} New selection index
     */
    findWordBoundaryLeft: function(startFrom) {
      var offset = 0, index = startFrom - 1;

      // remove space before cursor first
      if (this._reSpace.test(this.text.charAt(index))) {
        while (this._reSpace.test(this.text.charAt(index))) {
          offset++;
          index--;
        }
      }
      while (/\S/.test(this.text.charAt(index)) && index > -1) {
        offset++;
        index--;
      }

      return startFrom - offset;
    },

    /**
     * Find new selection index representing end of current word according to current selection index
     * @param {Number} startFrom Current selection index
     * @return {Number} New selection index
     */
    findWordBoundaryRight: function(startFrom) {
      var offset = 0, index = startFrom;

      // remove space after cursor first
      if (this._reSpace.test(this.text.charAt(index))) {
        while (this._reSpace.test(this.text.charAt(index))) {
          offset++;
          index++;
        }
      }
      while (/\S/.test(this.text.charAt(index)) && index < this.text.length) {
        offset++;
        index++;
      }

      return startFrom + offset;
    },

    /**
     * Find new selection index representing start of current line according to current selection index
     * @param {Number} startFrom Current selection index
     * @return {Number} New selection index
     */
    findLineBoundaryLeft: function(startFrom) {
      var offset = 0, index = startFrom - 1;

      while (!/\n/.test(this.text.charAt(index)) && index > -1) {
        offset++;
        index--;
      }

      return startFrom - offset;
    },

    /**
     * Find new selection index representing end of current line according to current selection index
     * @param {Number} startFrom Current selection index
     * @return {Number} New selection index
     */
    findLineBoundaryRight: function(startFrom) {
      var offset = 0, index = startFrom;

      while (!/\n/.test(this.text.charAt(index)) && index < this.text.length) {
        offset++;
        index++;
      }

      return startFrom + offset;
    },

    /**
     * Returns number of newlines in selected text
     * @return {Number} Number of newlines in selected text
     */
    getNumNewLinesInSelectedText: function() {
      var selectedText = this.getSelectedText(),
          numNewLines = 0;

      for (var i = 0, chars = selectedText.split(''), len = chars.length; i < len; i++) {
        if (chars[i] === '\n') {
          numNewLines++;
        }
      }
      return numNewLines;
    },

    /**
     * Finds index corresponding to beginning or end of a word
     * @param {Number} selectionStart Index of a character
     * @param {Number} direction: 1 or -1
     * @return {Number} Index of the beginning or end of a word
     */
    searchWordBoundary: function(selectionStart, direction) {
      var index = this._reSpace.test(this.text.charAt(selectionStart)) ? selectionStart - 1 : selectionStart,
          _char = this.text.charAt(index),
          reNonWord = /[ \n\.,;!\?\-]/;

      while (!reNonWord.test(_char) && index > 0 && index < this.text.length) {
        index += direction;
        _char = this.text.charAt(index);
      }
      if (reNonWord.test(_char) && _char !== '\n') {
        index += direction === 1 ? 0 : 1;
      }
      return index;
    },

    /**
     * Selects a word based on the index
     * @param {Number} selectionStart Index of a character
     */
    selectWord: function(selectionStart) {
      var newSelectionStart = this.searchWordBoundary(selectionStart, -1), /* search backwards */
          newSelectionEnd = this.searchWordBoundary(selectionStart, 1); /* search forward */

      this.setSelectionStart(newSelectionStart);
      this.setSelectionEnd(newSelectionEnd);
    },

    /**
     * Selects a line based on the index
     * @param {Number} selectionStart Index of a character
     */
    selectLine: function(selectionStart) {
      var newSelectionStart = this.findLineBoundaryLeft(selectionStart),
          newSelectionEnd = this.findLineBoundaryRight(selectionStart);

      this.setSelectionStart(newSelectionStart);
      this.setSelectionEnd(newSelectionEnd);
    },

    /**
     * Enters editing state
     * @return {fabric.IText} thisArg
     * @chainable
     */
    enterEditing: function() {
      if (this.isEditing || !this.editable) {
        return;
      }

      if (this.canvas) {
        this.exitEditingOnOthers(this.canvas);
      }

      this.isEditing = true;

      this.initHiddenTextarea();
      this.hiddenTextarea.focus();
      this._updateTextarea();
      this._saveEditingProps();
      this._setEditingProps();

      this._tick();
      this.fire('editing:entered');

      if (!this.canvas) {
        return this;
      }

      this.canvas.renderAll();
      this.canvas.fire('text:editing:entered', { target: this });
      this.initMouseMoveHandler();
      return this;
    },

    exitEditingOnOthers: function(canvas) {
      if (canvas._iTextInstances) {
        canvas._iTextInstances.forEach(function(obj) {
          obj.selected = false;
          if (obj.isEditing) {
            obj.exitEditing();
          }
        });
      }
    },

    /**
    * Initializes "mousemove" event handler
    */
    initMouseMoveHandler: function() {
      var _this = this;
      this.canvas.on('mouse:move',  function(options) {
        if (!_this.__isMousedown || !_this.isEditing) {
          return;
        }

        var newSelectionStart = _this.getSelectionStartFromPointer(options.e);
        if (newSelectionStart >= _this.__selectionStartOnMouseDown) {
          _this.setSelectionStart(_this.__selectionStartOnMouseDown);
          _this.setSelectionEnd(newSelectionStart);
        }
        else {
          _this.setSelectionStart(newSelectionStart);
          _this.setSelectionEnd(_this.__selectionStartOnMouseDown);
        }
      });
    },

    /**
     * @private
     */
    _setEditingProps: function() {
      this.hoverCursor = 'text';

      if (this.canvas) {
        this.canvas.defaultCursor = this.canvas.moveCursor = 'text';
      }

      this.borderColor = this.editingBorderColor;

      this.hasControls = this.selectable = false;
      this.lockMovementX = this.lockMovementY = true;
    },

    /**
     * @private
     */
    _updateTextarea: function() {
      if (!this.hiddenTextarea) {
        return;
      }

      this.hiddenTextarea.value = this.text;
      this.hiddenTextarea.selectionStart = this.selectionStart;
      this.hiddenTextarea.selectionEnd = this.selectionEnd;
    },

    /**
     * @private
     */
    _saveEditingProps: function() {
      this._savedProps = {
        hasControls: this.hasControls,
        borderColor: this.borderColor,
        lockMovementX: this.lockMovementX,
        lockMovementY: this.lockMovementY,
        hoverCursor: this.hoverCursor,
        defaultCursor: this.canvas && this.canvas.defaultCursor,
        moveCursor: this.canvas && this.canvas.moveCursor
      };
    },

    /**
     * @private
     */
    _restoreEditingProps: function() {
      if (!this._savedProps) {
        return;
      }

      this.hoverCursor = this._savedProps.overCursor;
      this.hasControls = this._savedProps.hasControls;
      this.borderColor = this._savedProps.borderColor;
      this.lockMovementX = this._savedProps.lockMovementX;
      this.lockMovementY = this._savedProps.lockMovementY;

      if (this.canvas) {
        this.canvas.defaultCursor = this._savedProps.defaultCursor;
        this.canvas.moveCursor = this._savedProps.moveCursor;
      }
    },

    /**
     * Exits from editing state
     * @return {fabric.IText} thisArg
     * @chainable
     */
    exitEditing: function() {

      this.selected = false;
      this.isEditing = false;
      this.selectable = true;

      this.selectionEnd = this.selectionStart;
      this.hiddenTextarea && this.canvas && this.hiddenTextarea.parentNode.removeChild(this.hiddenTextarea);
      this.hiddenTextarea = null;

      this.abortCursorAnimation();
      this._restoreEditingProps();
      this._currentCursorOpacity = 0;

      this.fire('editing:exited');
      this.canvas && this.canvas.fire('text:editing:exited', { target: this });

      return this;
    },

    /**
     * @private
     */
    _removeExtraneousStyles: function() {
      for (var prop in this.styles) {
        if (!this._textLines[prop]) {
          delete this.styles[prop];
        }
      }
    },

    /**
     * @private
     */
    _removeCharsFromTo: function(start, end) {

      var i = end;
      while (i !== start) {

        var prevIndex = this.get2DCursorLocation(i).charIndex;
        i--;

        var index = this.get2DCursorLocation(i).charIndex,
            isNewline = index > prevIndex;

        if (isNewline) {
          this.removeStyleObject(isNewline, i + 1);
        }
        else {
          this.removeStyleObject(this.get2DCursorLocation(i).charIndex === 0, i);
        }

      }

      this.text = this.text.slice(0, start) +
                  this.text.slice(end);
      this._clearCache();
    },

    /**
     * Inserts a character where cursor is (replacing selection if one exists)
     * @param {String} _chars Characters to insert
     */
    insertChars: function(_chars, useCopiedStyle) {
      var isEndOfLine = this.text.slice(this.selectionStart, this.selectionStart + 1) === '\n';

      this.text = this.text.slice(0, this.selectionStart) +
                    _chars +
                  this.text.slice(this.selectionEnd);

      if (this.selectionStart === this.selectionEnd) {
        this.insertStyleObjects(_chars, isEndOfLine, useCopiedStyle);
      }
      // else if (this.selectionEnd - this.selectionStart > 1) {
      // TODO: replace styles properly
      // console.log('replacing MORE than 1 char');
      // }
      this.setSelectionStart(this.selectionStart + _chars.length);
      this.setSelectionEnd(this.selectionStart);
      this._clearCache();
      this.canvas && this.canvas.renderAll();

      this.setCoords();
      this.fire('changed');
      this.canvas && this.canvas.fire('text:changed', { target: this });
    },

    /**
     * Inserts new style object
     * @param {Number} lineIndex Index of a line
     * @param {Number} charIndex Index of a char
     * @param {Boolean} isEndOfLine True if it's end of line
     */
    insertNewlineStyleObject: function(lineIndex, charIndex, isEndOfLine) {

      this.shiftLineStyles(lineIndex, +1);

      if (!this.styles[lineIndex + 1]) {
        this.styles[lineIndex + 1] = { };
      }

      var currentCharStyle = this.styles[lineIndex][charIndex - 1],
          newLineStyles = { };

      // if there's nothing after cursor,
      // we clone current char style onto the next (otherwise empty) line
      if (isEndOfLine) {
        newLineStyles[0] = clone(currentCharStyle);
        this.styles[lineIndex + 1] = newLineStyles;
      }
      // otherwise we clone styles of all chars
      // after cursor onto the next line, from the beginning
      else {
        for (var index in this.styles[lineIndex]) {
          if (parseInt(index, 10) >= charIndex) {
            newLineStyles[parseInt(index, 10) - charIndex] = this.styles[lineIndex][index];
            // remove lines from the previous line since they're on a new line now
            delete this.styles[lineIndex][index];
          }
        }
        this.styles[lineIndex + 1] = newLineStyles;
      }
      this._clearCache();
    },

    /**
     * Inserts style object for a given line/char index
     * @param {Number} lineIndex Index of a line
     * @param {Number} charIndex Index of a char
     * @param {Object} [style] Style object to insert, if given
     */
    insertCharStyleObject: function(lineIndex, charIndex, style) {

      var currentLineStyles = this.styles[lineIndex],
          currentLineStylesCloned = clone(currentLineStyles);

      if (charIndex === 0 && !style) {
        charIndex = 1;
      }

      // shift all char styles by 1 forward
      // 0,1,2,3 -> (charIndex=2) -> 0,1,3,4 -> (insert 2) -> 0,1,2,3,4
      for (var index in currentLineStylesCloned) {
        var numericIndex = parseInt(index, 10);
        if (numericIndex >= charIndex) {
          currentLineStyles[numericIndex + 1] = currentLineStylesCloned[numericIndex];
          //delete currentLineStyles[index];
        }
      }

      this.styles[lineIndex][charIndex] =
        style || clone(currentLineStyles[charIndex - 1]);
      this._clearCache();
    },

    /**
     * Inserts style object(s)
     * @param {String} _chars Characters at the location where style is inserted
     * @param {Boolean} isEndOfLine True if it's end of line
     * @param {Boolean} [useCopiedStyle] Style to insert
     */
    insertStyleObjects: function(_chars, isEndOfLine, useCopiedStyle) {
      // removed shortcircuit over isEmptyStyles

      var cursorLocation = this.get2DCursorLocation(),
          lineIndex = cursorLocation.lineIndex,
          charIndex = cursorLocation.charIndex;

      if (!this.styles[lineIndex]) {
        this.styles[lineIndex] = { };
      }

      if (_chars === '\n') {
        this.insertNewlineStyleObject(lineIndex, charIndex, isEndOfLine);
      }
      else {
        if (useCopiedStyle) {
          this._insertStyles(this.copiedStyles);
        }
        else {
          // TODO: support multiple style insertion if _chars.length > 1
          this.insertCharStyleObject(lineIndex, charIndex);
        }
      }
    },

    /**
     * @private
     */
    _insertStyles: function(styles) {
      for (var i = 0, len = styles.length; i < len; i++) {

        var cursorLocation = this.get2DCursorLocation(this.selectionStart + i),
            lineIndex = cursorLocation.lineIndex,
            charIndex = cursorLocation.charIndex;

        this.insertCharStyleObject(lineIndex, charIndex, styles[i]);
      }
    },

    /**
     * Shifts line styles up or down
     * @param {Number} lineIndex Index of a line
     * @param {Number} offset Can be -1 or +1
     */
    shiftLineStyles: function(lineIndex, offset) {
      // shift all line styles by 1 upward
      var clonedStyles = clone(this.styles);
      for (var line in this.styles) {
        var numericLine = parseInt(line, 10);
        if (numericLine > lineIndex) {
          this.styles[numericLine + offset] = clonedStyles[numericLine];
        }
      }
    },

    /**
     * Removes style object
     * @param {Boolean} isBeginningOfLine True if cursor is at the beginning of line
     * @param {Number} [index] Optional index. When not given, current selectionStart is used.
     */
    removeStyleObject: function(isBeginningOfLine, index) {

      var cursorLocation = this.get2DCursorLocation(index),
          lineIndex = cursorLocation.lineIndex,
          charIndex = cursorLocation.charIndex;

      if (isBeginningOfLine) {

        var textOnPreviousLine = this._textLines[lineIndex - 1],
            newCharIndexOnPrevLine = textOnPreviousLine
              ? textOnPreviousLine.length
              : 0;

        if (!this.styles[lineIndex - 1]) {
          this.styles[lineIndex - 1] = { };
        }

        for (charIndex in this.styles[lineIndex]) {
          this.styles[lineIndex - 1][parseInt(charIndex, 10) + newCharIndexOnPrevLine]
            = this.styles[lineIndex][charIndex];
        }

        this.shiftLineStyles(lineIndex, -1);
      }
      else {
        var currentLineStyles = this.styles[lineIndex];

        if (currentLineStyles) {
          var offset = this.selectionStart === this.selectionEnd ? -1 : 0;
          delete currentLineStyles[charIndex + offset];
          // console.log('deleting', lineIndex, charIndex + offset);
        }

        var currentLineStylesCloned = clone(currentLineStyles);

        // shift all styles by 1 backwards
        for (var i in currentLineStylesCloned) {
          var numericIndex = parseInt(i, 10);
          if (numericIndex >= charIndex && numericIndex !== 0) {
            currentLineStyles[numericIndex - 1] = currentLineStylesCloned[numericIndex];
            delete currentLineStyles[numericIndex];
          }
        }
      }
    },

    /**
     * Inserts new line
     */
    insertNewline: function() {
      this.insertChars('\n');
    }
  });
})();


fabric.util.object.extend(fabric.IText.prototype, /** @lends fabric.IText.prototype */ {
  /**
   * Initializes "dbclick" event handler
   */
  initDoubleClickSimulation: function() {

    // for double click
    this.__lastClickTime = +new Date();

    // for triple click
    this.__lastLastClickTime = +new Date();

    this.__lastPointer = { };

    this.on('mousedown', this.onMouseDown.bind(this));
  },

  onMouseDown: function(options) {

    this.__newClickTime = +new Date();
    var newPointer = this.canvas.getPointer(options.e);

    if (this.isTripleClick(newPointer)) {
      this.fire('tripleclick', options);
      this._stopEvent(options.e);
    }
    else if (this.isDoubleClick(newPointer)) {
      this.fire('dblclick', options);
      this._stopEvent(options.e);
    }

    this.__lastLastClickTime = this.__lastClickTime;
    this.__lastClickTime = this.__newClickTime;
    this.__lastPointer = newPointer;
    this.__lastIsEditing = this.isEditing;
    this.__lastSelected = this.selected;
  },

  isDoubleClick: function(newPointer) {
    return this.__newClickTime - this.__lastClickTime < 500 &&
        this.__lastPointer.x === newPointer.x &&
        this.__lastPointer.y === newPointer.y && this.__lastIsEditing;
  },

  isTripleClick: function(newPointer) {
    return this.__newClickTime - this.__lastClickTime < 500 &&
        this.__lastClickTime - this.__lastLastClickTime < 500 &&
        this.__lastPointer.x === newPointer.x &&
        this.__lastPointer.y === newPointer.y;
  },

  /**
   * @private
   */
  _stopEvent: function(e) {
    e.preventDefault && e.preventDefault();
    e.stopPropagation && e.stopPropagation();
  },

  /**
   * Initializes event handlers related to cursor or selection
   */
  initCursorSelectionHandlers: function() {
    this.initSelectedHandler();
    this.initMousedownHandler();
    this.initMouseupHandler();
    this.initClicks();
  },

  /**
   * Initializes double and triple click event handlers
   */
  initClicks: function() {
    this.on('dblclick', function(options) {
      this.selectWord(this.getSelectionStartFromPointer(options.e));
    });
    this.on('tripleclick', function(options) {
      this.selectLine(this.getSelectionStartFromPointer(options.e));
    });
  },

  /**
   * Initializes "mousedown" event handler
   */
  initMousedownHandler: function() {
    this.on('mousedown', function(options) {

      var pointer = this.canvas.getPointer(options.e);

      this.__mousedownX = pointer.x;
      this.__mousedownY = pointer.y;
      this.__isMousedown = true;

      if (this.hiddenTextarea && this.canvas) {
        this.canvas.wrapperEl.appendChild(this.hiddenTextarea);
      }

      if (this.selected) {
        this.setCursorByClick(options.e);
      }

      if (this.isEditing) {
        this.__selectionStartOnMouseDown = this.selectionStart;
        this.initDelayedCursor(true);
      }
    });
  },

  /**
   * @private
   */
  _isObjectMoved: function(e) {
    var pointer = this.canvas.getPointer(e);

    return this.__mousedownX !== pointer.x ||
           this.__mousedownY !== pointer.y;
  },

  /**
   * Initializes "mouseup" event handler
   */
  initMouseupHandler: function() {
    this.on('mouseup', function(options) {
      this.__isMousedown = false;
      if (this._isObjectMoved(options.e)) {
        return;
      }

      if (this.__lastSelected) {
        this.enterEditing();
        this.initDelayedCursor(true);
      }
      this.selected = true;
    });
  },

  /**
   * Changes cursor location in a text depending on passed pointer (x/y) object
   * @param {Event} e Event object
   */
  setCursorByClick: function(e) {
    var newSelectionStart = this.getSelectionStartFromPointer(e);

    if (e.shiftKey) {
      if (newSelectionStart < this.selectionStart) {
        this.setSelectionEnd(this.selectionStart);
        this.setSelectionStart(newSelectionStart);
      }
      else {
        this.setSelectionEnd(newSelectionStart);
      }
    }
    else {
      this.setSelectionStart(newSelectionStart);
      this.setSelectionEnd(newSelectionStart);
    }
  },

  /**
   * @private
   * @param {Event} e Event object
   * @return {Object} Coordinates of a pointer (x, y)
   */
  _getLocalRotatedPointer: function(e) {
    var pointer = this.canvas.getPointer(e),

        pClicked = new fabric.Point(pointer.x, pointer.y),
        pLeftTop = new fabric.Point(this.left, this.top),

        rotated = fabric.util.rotatePoint(
          pClicked, pLeftTop, fabric.util.degreesToRadians(-this.angle));

    return this.getLocalPointer(e, rotated);
  },

  /**
   * Returns index of a character corresponding to where an object was clicked
   * @param {Event} e Event object
   * @return {Number} Index of a character
   */
  getSelectionStartFromPointer: function(e) {
    var mouseOffset = this._getLocalRotatedPointer(e),
        prevWidth = 0,
        width = 0,
        height = 0,
        charIndex = 0,
        newSelectionStart,
        line;

    for (var i = 0, len = this._textLines.length; i < len; i++) {
      line = this._textLines[i].split('');
      height += this._getHeightOfLine(this.ctx, i) * this.scaleY;

      var widthOfLine = this._getLineWidth(this.ctx, i),
          lineLeftOffset = this._getLineLeftOffset(widthOfLine);

      width = lineLeftOffset * this.scaleX;

      if (this.flipX) {
        // when oject is horizontally flipped we reverse chars
        this._textLines[i] = line.reverse().join('');
      }

      for (var j = 0, jlen = line.length; j < jlen; j++) {

        var _char = line[j];
        prevWidth = width;

        width += this._getWidthOfChar(this.ctx, _char, i, this.flipX ? jlen - j : j) *
                 this.scaleX;

        if (height <= mouseOffset.y || width <= mouseOffset.x) {
          charIndex++;
          continue;
        }

        return this._getNewSelectionStartFromOffset(
          mouseOffset, prevWidth, width, charIndex + i, jlen);
      }

      if (mouseOffset.y < height) {
        return this._getNewSelectionStartFromOffset(
          mouseOffset, prevWidth, width, charIndex + i, jlen);
      }
    }

    // clicked somewhere after all chars, so set at the end
    if (typeof newSelectionStart === 'undefined') {
      return this.text.length;
    }
  },

  /**
   * @private
   */
  _getNewSelectionStartFromOffset: function(mouseOffset, prevWidth, width, index, jlen) {

    var distanceBtwLastCharAndCursor = mouseOffset.x - prevWidth,
        distanceBtwNextCharAndCursor = width - mouseOffset.x,
        offset = distanceBtwNextCharAndCursor > distanceBtwLastCharAndCursor ? 0 : 1,
        newSelectionStart = index + offset;

    // if object is horizontally flipped, mirror cursor location from the end
    if (this.flipX) {
      newSelectionStart = jlen - newSelectionStart;
    }

    if (newSelectionStart > this.text.length) {
      newSelectionStart = this.text.length;
    }

    return newSelectionStart;
  }
});


fabric.util.object.extend(fabric.IText.prototype, /** @lends fabric.IText.prototype */ {

  /**
   * Initializes hidden textarea (needed to bring up keyboard in iOS)
   */
  initHiddenTextarea: function() {
    this.hiddenTextarea = fabric.document.createElement('textarea');

    this.hiddenTextarea.setAttribute('autocapitalize', 'off');
    this.hiddenTextarea.style.cssText = 'position: fixed; bottom: 20px; left: 0px; opacity: 0;'
                                        + ' width: 0px; height: 0px; z-index: -999;';
    fabric.document.body.appendChild(this.hiddenTextarea);

    fabric.util.addListener(this.hiddenTextarea, 'keydown', this.onKeyDown.bind(this));
    fabric.util.addListener(this.hiddenTextarea, 'keypress', this.onKeyPress.bind(this));
    fabric.util.addListener(this.hiddenTextarea, 'copy', this.copy.bind(this));
    fabric.util.addListener(this.hiddenTextarea, 'paste', this.paste.bind(this));

    if (!this._clickHandlerInitialized && this.canvas) {
      fabric.util.addListener(this.canvas.upperCanvasEl, 'click', this.onClick.bind(this));
      this._clickHandlerInitialized = true;
    }
  },

  /**
   * @private
   */
  _keysMap: {
    8:  'removeChars',
    9:  'exitEditing',
    27: 'exitEditing',
    13: 'insertNewline',
    33: 'moveCursorUp',
    34: 'moveCursorDown',
    35: 'moveCursorRight',
    36: 'moveCursorLeft',
    37: 'moveCursorLeft',
    38: 'moveCursorUp',
    39: 'moveCursorRight',
    40: 'moveCursorDown',
    46: 'forwardDelete'
  },

  /**
   * @private
   */
  _ctrlKeysMap: {
    65: 'selectAll',
    88: 'cut'
  },

  onClick: function() {
    // No need to trigger click event here, focus is enough to have the keyboard appear on Android
    this.hiddenTextarea && this.hiddenTextarea.focus();
  },

  /**
   * Handles keyup event
   * @param {Event} e Event object
   */
  onKeyDown: function(e) {
    if (!this.isEditing) {
      return;
    }
    if (e.keyCode in this._keysMap) {
      this[this._keysMap[e.keyCode]](e);
    }
    else if ((e.keyCode in this._ctrlKeysMap) && (e.ctrlKey || e.metaKey)) {
      this[this._ctrlKeysMap[e.keyCode]](e);
    }
    else {
      return;
    }
    e.stopImmediatePropagation();
    e.preventDefault();
    this.canvas && this.canvas.renderAll();
  },

  /**
   * Forward delete
   */
  forwardDelete: function(e) {
    if (this.selectionStart === this.selectionEnd) {
      this.moveCursorRight(e);
    }
    this.removeChars(e);
  },

  /**
   * Copies selected text
   * @param {Event} e Event object
   */
  copy: function(e) {
    var selectedText = this.getSelectedText(),
        clipboardData = this._getClipboardData(e);

    // Check for backward compatibility with old browsers
    if (clipboardData) {
      clipboardData.setData('text', selectedText);
    }

    this.copiedText = selectedText;
    this.copiedStyles = this.getSelectionStyles(
                          this.selectionStart,
                          this.selectionEnd);
  },

  /**
   * Pastes text
   * @param {Event} e Event object
   */
  paste: function(e) {
    var copiedText = null,
        clipboardData = this._getClipboardData(e);

    // Check for backward compatibility with old browsers
    if (clipboardData) {
      copiedText = clipboardData.getData('text');
    }
    else {
      copiedText = this.copiedText;
    }

    if (copiedText) {
      this.insertChars(copiedText, true);
    }
  },

  /**
   * Cuts text
   * @param {Event} e Event object
   */
  cut: function(e) {
    if (this.selectionStart === this.selectionEnd) {
      return;
    }

    this.copy();
    this.removeChars(e);
  },

  /**
   * @private
   * @param {Event} e Event object
   * @return {Object} Clipboard data object
   */
  _getClipboardData: function(e) {
    return e && (e.clipboardData || fabric.window.clipboardData);
  },

  /**
   * Handles keypress event
   * @param {Event} e Event object
   */
  onKeyPress: function(e) {
    if (!this.isEditing || e.metaKey || e.ctrlKey) {
      return;
    }
    if (e.which !== 0) {
      this.insertChars(String.fromCharCode(e.which));
    }
    e.stopPropagation();
  },

  /**
   * Gets start offset of a selection
   * @param {Event} e Event object
   * @param {Boolean} isRight
   * @return {Number}
   */
  getDownCursorOffset: function(e, isRight) {
    var selectionProp = isRight ? this.selectionEnd : this.selectionStart,
        _char, lineLeftOffset,
        textBeforeCursor = this.text.slice(0, selectionProp),
        textAfterCursor = this.text.slice(selectionProp),

        textOnSameLineBeforeCursor = textBeforeCursor.slice(textBeforeCursor.lastIndexOf('\n') + 1),
        textOnSameLineAfterCursor = textAfterCursor.match(/(.*)\n?/)[1],
        textOnNextLine = (textAfterCursor.match(/.*\n(.*)\n?/) || { })[1] || '',

        cursorLocation = this.get2DCursorLocation(selectionProp);

    // if on last line, down cursor goes to end of line
    if (cursorLocation.lineIndex === this._textLines.length - 1 || e.metaKey || e.keyCode === 34) {

      // move to the end of a text
      return this.text.length - selectionProp;
    }

    var widthOfSameLineBeforeCursor = this._getLineWidth(this.ctx, cursorLocation.lineIndex);
    lineLeftOffset = this._getLineLeftOffset(widthOfSameLineBeforeCursor);

    var widthOfCharsOnSameLineBeforeCursor = lineLeftOffset,
        lineIndex = cursorLocation.lineIndex;

    for (var i = 0, len = textOnSameLineBeforeCursor.length; i < len; i++) {
      _char = textOnSameLineBeforeCursor[i];
      widthOfCharsOnSameLineBeforeCursor += this._getWidthOfChar(this.ctx, _char, lineIndex, i);
    }

    var indexOnNextLine = this._getIndexOnNextLine(
      cursorLocation, textOnNextLine, widthOfCharsOnSameLineBeforeCursor);

    return textOnSameLineAfterCursor.length + 1 + indexOnNextLine;
  },

  /**
   * @private
   */
  _getIndexOnNextLine: function(cursorLocation, textOnNextLine, widthOfCharsOnSameLineBeforeCursor) {
    var lineIndex = cursorLocation.lineIndex + 1,
        widthOfNextLine = this._getLineWidth(this.ctx, lineIndex),
        lineLeftOffset = this._getLineLeftOffset(widthOfNextLine),
        widthOfCharsOnNextLine = lineLeftOffset,
        indexOnNextLine = 0,
        foundMatch;

    for (var j = 0, jlen = textOnNextLine.length; j < jlen; j++) {

      var _char = textOnNextLine[j],
          widthOfChar = this._getWidthOfChar(this.ctx, _char, lineIndex, j);

      widthOfCharsOnNextLine += widthOfChar;

      if (widthOfCharsOnNextLine > widthOfCharsOnSameLineBeforeCursor) {

        foundMatch = true;

        var leftEdge = widthOfCharsOnNextLine - widthOfChar,
            rightEdge = widthOfCharsOnNextLine,
            offsetFromLeftEdge = Math.abs(leftEdge - widthOfCharsOnSameLineBeforeCursor),
            offsetFromRightEdge = Math.abs(rightEdge - widthOfCharsOnSameLineBeforeCursor);

        indexOnNextLine = offsetFromRightEdge < offsetFromLeftEdge ? j + 1 : j;

        break;
      }
    }

    // reached end
    if (!foundMatch) {
      indexOnNextLine = textOnNextLine.length;
    }

    return indexOnNextLine;
  },

  /**
   * Moves cursor down
   * @param {Event} e Event object
   */
  moveCursorDown: function(e) {
    this.abortCursorAnimation();
    this._currentCursorOpacity = 1;

    var offset = this.getDownCursorOffset(e, this._selectionDirection === 'right');

    if (e.shiftKey) {
      this.moveCursorDownWithShift(offset);
    }
    else {
      this.moveCursorDownWithoutShift(offset);
    }

    this.initDelayedCursor();
  },

  /**
   * Moves cursor down without keeping selection
   * @param {Number} offset
   */
  moveCursorDownWithoutShift: function(offset) {
    this._selectionDirection = 'right';
    this.setSelectionStart(this.selectionStart + offset);
    this.setSelectionEnd(this.selectionStart);
  },

  /**
   * private
   */
  swapSelectionPoints: function() {
    var swapSel = this.selectionEnd;
    this.setSelectionEnd(this.selectionStart);
    this.setSelectionStart(swapSel);
  },

  /**
   * Moves cursor down while keeping selection
   * @param {Number} offset
   */
  moveCursorDownWithShift: function(offset) {
    if (this.selectionEnd === this.selectionStart) {
      this._selectionDirection = 'right';
    }
    if (this._selectionDirection === 'right') {
      this.setSelectionEnd(this.selectionEnd + offset);
    }
    else {
      this.setSelectionStart(this.selectionStart + offset);
    }
    if (this.selectionEnd < this.selectionStart  && this._selectionDirection === 'left') {
      this.swapSelectionPoints();
      this._selectionDirection = 'right';
    }
    if (this.selectionEnd > this.text.length) {
      this.setSelectionEnd(this.text.length);
    }
  },

  /**
   * @param {Event} e Event object
   * @param {Boolean} isRight
   * @return {Number}
   */
  getUpCursorOffset: function(e, isRight) {
    var selectionProp = isRight ? this.selectionEnd : this.selectionStart,
        cursorLocation = this.get2DCursorLocation(selectionProp);
    // if on first line, up cursor goes to start of line
    if (cursorLocation.lineIndex === 0 || e.metaKey || e.keyCode === 33) {
      return selectionProp;
    }

    var textBeforeCursor = this.text.slice(0, selectionProp),
        textOnSameLineBeforeCursor = textBeforeCursor.slice(textBeforeCursor.lastIndexOf('\n') + 1),
        textOnPreviousLine = (textBeforeCursor.match(/\n?(.*)\n.*$/) || {})[1] || '',
        _char,
        widthOfSameLineBeforeCursor = this._getLineWidth(this.ctx, cursorLocation.lineIndex),
        lineLeftOffset = this._getLineLeftOffset(widthOfSameLineBeforeCursor),
        widthOfCharsOnSameLineBeforeCursor = lineLeftOffset,
        lineIndex = cursorLocation.lineIndex;

    for (var i = 0, len = textOnSameLineBeforeCursor.length; i < len; i++) {
      _char = textOnSameLineBeforeCursor[i];
      widthOfCharsOnSameLineBeforeCursor += this._getWidthOfChar(this.ctx, _char, lineIndex, i);
    }

    var indexOnPrevLine = this._getIndexOnPrevLine(
      cursorLocation, textOnPreviousLine, widthOfCharsOnSameLineBeforeCursor);

    return textOnPreviousLine.length - indexOnPrevLine + textOnSameLineBeforeCursor.length;
  },

  /**
   * @private
   */
  _getIndexOnPrevLine: function(cursorLocation, textOnPreviousLine, widthOfCharsOnSameLineBeforeCursor) {

    var lineIndex = cursorLocation.lineIndex - 1,
        widthOfPreviousLine = this._getLineWidth(this.ctx, lineIndex),
        lineLeftOffset = this._getLineLeftOffset(widthOfPreviousLine),
        widthOfCharsOnPreviousLine = lineLeftOffset,
        indexOnPrevLine = 0,
        foundMatch;

    for (var j = 0, jlen = textOnPreviousLine.length; j < jlen; j++) {

      var _char = textOnPreviousLine[j],
          widthOfChar = this._getWidthOfChar(this.ctx, _char, lineIndex, j);

      widthOfCharsOnPreviousLine += widthOfChar;

      if (widthOfCharsOnPreviousLine > widthOfCharsOnSameLineBeforeCursor) {

        foundMatch = true;

        var leftEdge = widthOfCharsOnPreviousLine - widthOfChar,
            rightEdge = widthOfCharsOnPreviousLine,
            offsetFromLeftEdge = Math.abs(leftEdge - widthOfCharsOnSameLineBeforeCursor),
            offsetFromRightEdge = Math.abs(rightEdge - widthOfCharsOnSameLineBeforeCursor);

        indexOnPrevLine = offsetFromRightEdge < offsetFromLeftEdge ? j : (j - 1);

        break;
      }
    }

    // reached end
    if (!foundMatch) {
      indexOnPrevLine = textOnPreviousLine.length - 1;
    }

    return indexOnPrevLine;
  },

  /**
   * Moves cursor up
   * @param {Event} e Event object
   */
  moveCursorUp: function(e) {

    this.abortCursorAnimation();
    this._currentCursorOpacity = 1;

    var offset = this.getUpCursorOffset(e, this._selectionDirection === 'right');
    if (e.shiftKey) {
      this.moveCursorUpWithShift(offset);
    }
    else {
      this.moveCursorUpWithoutShift(offset);
    }

    this.initDelayedCursor();
  },

  /**
   * Moves cursor up with shift
   * @param {Number} offset
   */
  moveCursorUpWithShift: function(offset) {
    if (this.selectionEnd === this.selectionStart) {
      this._selectionDirection = 'left';
    }
    if (this._selectionDirection === 'right') {
      this.setSelectionEnd(this.selectionEnd - offset);
    }
    else {
      this.setSelectionStart(this.selectionStart - offset);
    }
    if (this.selectionEnd < this.selectionStart && this._selectionDirection === 'right') {
      this.swapSelectionPoints();
      this._selectionDirection = 'left';
    }
  },

  /**
   * Moves cursor up without shift
   * @param {Number} offset
   */
  moveCursorUpWithoutShift: function(offset) {
    if (this.selectionStart === this.selectionEnd) {
      this.setSelectionStart(this.selectionStart - offset);
    }
    this.setSelectionEnd(this.selectionStart);

    this._selectionDirection = 'left';
  },

  /**
   * Moves cursor left
   * @param {Event} e Event object
   */
  moveCursorLeft: function(e) {
    if (this.selectionStart === 0 && this.selectionEnd === 0) {
      return;
    }

    this.abortCursorAnimation();
    this._currentCursorOpacity = 1;

    if (e.shiftKey) {
      this.moveCursorLeftWithShift(e);
    }
    else {
      this.moveCursorLeftWithoutShift(e);
    }

    this.initDelayedCursor();
  },

  /**
   * @private
   */
  _move: function(e, prop, direction) {
    var propMethod = (prop === 'selectionStart' ? 'setSelectionStart' : 'setSelectionEnd');
    if (e.altKey) {
      this[propMethod](this['findWordBoundary' + direction](this[prop]));
    }
    else if (e.metaKey || e.keyCode === 35 ||  e.keyCode === 36 ) {
      this[propMethod](this['findLineBoundary' + direction](this[prop]));
    }
    else {
      this[propMethod](this[prop] + (direction === 'Left' ? -1 : 1));
    }
  },

  /**
   * @private
   */
  _moveLeft: function(e, prop) {
    this._move(e, prop, 'Left');
  },

  /**
   * @private
   */
  _moveRight: function(e, prop) {
    this._move(e, prop, 'Right');
  },

  /**
   * Moves cursor left without keeping selection
   * @param {Event} e
   */
  moveCursorLeftWithoutShift: function(e) {
    this._selectionDirection = 'left';

    // only move cursor when there is no selection,
    // otherwise we discard it, and leave cursor on same place
    if (this.selectionEnd === this.selectionStart) {
      this._moveLeft(e, 'selectionStart');
    }
    this.setSelectionEnd(this.selectionStart);
  },

  /**
   * Moves cursor left while keeping selection
   * @param {Event} e
   */
  moveCursorLeftWithShift: function(e) {
    if (this._selectionDirection === 'right' && this.selectionStart !== this.selectionEnd) {
      this._moveLeft(e, 'selectionEnd');
    }
    else {
      this._selectionDirection = 'left';
      this._moveLeft(e, 'selectionStart');

      // increase selection by one if it's a newline
      if (this.text.charAt(this.selectionStart) === '\n') {
        this.setSelectionStart(this.selectionStart - 1);
      }
    }
  },

  /**
   * Moves cursor right
   * @param {Event} e Event object
   */
  moveCursorRight: function(e) {
    if (this.selectionStart >= this.text.length && this.selectionEnd >= this.text.length) {
      return;
    }

    this.abortCursorAnimation();
    this._currentCursorOpacity = 1;

    if (e.shiftKey) {
      this.moveCursorRightWithShift(e);
    }
    else {
      this.moveCursorRightWithoutShift(e);
    }

    this.initDelayedCursor();
  },

  /**
   * Moves cursor right while keeping selection
   * @param {Event} e
   */
  moveCursorRightWithShift: function(e) {
    if (this._selectionDirection === 'left' && this.selectionStart !== this.selectionEnd) {
      this._moveRight(e, 'selectionStart');
    }
    else {
      this._selectionDirection = 'right';
      this._moveRight(e, 'selectionEnd');

      // increase selection by one if it's a newline
      if (this.text.charAt(this.selectionEnd - 1) === '\n') {
        this.setSelectionEnd(this.selectionEnd + 1);
      }
    }
  },

  /**
   * Moves cursor right without keeping selection
   * @param {Event} e Event object
   */
  moveCursorRightWithoutShift: function(e) {
    this._selectionDirection = 'right';

    if (this.selectionStart === this.selectionEnd) {
      this._moveRight(e, 'selectionStart');
      this.setSelectionEnd(this.selectionStart);
    }
    else {
      this.setSelectionEnd(this.selectionEnd + this.getNumNewLinesInSelectedText());
      this.setSelectionStart(this.selectionEnd);
    }
  },

  /**
   * Removes characters selected by selection
   * @param {Event} e Event object
   */
  removeChars: function(e) {
    if (this.selectionStart === this.selectionEnd) {
      this._removeCharsNearCursor(e);
    }
    else {
      this._removeCharsFromTo(this.selectionStart, this.selectionEnd);
    }

    this.setSelectionEnd(this.selectionStart);

    this._removeExtraneousStyles();

    this._clearCache();
    this.canvas && this.canvas.renderAll();

    this.setCoords();
    this.fire('changed');
    this.canvas && this.canvas.fire('text:changed', { target: this });
  },

  /**
   * @private
   * @param {Event} e Event object
   */
  _removeCharsNearCursor: function(e) {
    if (this.selectionStart !== 0) {

      if (e.metaKey) {
        // remove all till the start of current line
        var leftLineBoundary = this.findLineBoundaryLeft(this.selectionStart);

        this._removeCharsFromTo(leftLineBoundary, this.selectionStart);
        this.setSelectionStart(leftLineBoundary);
      }
      else if (e.altKey) {
        // remove all till the start of current word
        var leftWordBoundary = this.findWordBoundaryLeft(this.selectionStart);

        this._removeCharsFromTo(leftWordBoundary, this.selectionStart);
        this.setSelectionStart(leftWordBoundary);
      }
      else {
        var isBeginningOfLine = this.text.slice(this.selectionStart - 1, this.selectionStart) === '\n';
        this.removeStyleObject(isBeginningOfLine);
        this.setSelectionStart(this.selectionStart - 1);
        this.text = this.text.slice(0, this.selectionStart) +
                    this.text.slice(this.selectionStart + 1);
      }
    }
  }
});


/* _TO_SVG_START_ */
fabric.util.object.extend(fabric.IText.prototype, /** @lends fabric.IText.prototype */ {

  /**
   * @private
   */
  _setSVGTextLineText: function(lineIndex, textSpans, height, textLeftOffset, textTopOffset, textBgRects) {
    if (!this.styles[lineIndex]) {
      this.callSuper('_setSVGTextLineText',
        lineIndex, textSpans, height, textLeftOffset, textTopOffset);
    }
    else {
      this._setSVGTextLineChars(
        lineIndex, textSpans, height, textLeftOffset, textBgRects);
    }
  },

  /**
   * @private
   */
  _setSVGTextLineChars: function(lineIndex, textSpans, height, textLeftOffset, textBgRects) {

    var chars = this._textLines[lineIndex].split(''),
        charOffset = 0,
        lineLeftOffset = this._getSVGLineLeftOffset(lineIndex) - this.width / 2,
        lineOffset = this._getSVGLineTopOffset(lineIndex),
        heightOfLine = this._getHeightOfLine(this.ctx, lineIndex);

    for (var i = 0, len = chars.length; i < len; i++) {
      var styleDecl = this.styles[lineIndex][i] || { };

      textSpans.push(
        this._createTextCharSpan(
          chars[i], styleDecl, lineLeftOffset, lineOffset.lineTop + lineOffset.offset, charOffset));

      var charWidth = this._getWidthOfChar(this.ctx, chars[i], lineIndex, i);

      if (styleDecl.textBackgroundColor) {
        textBgRects.push(
          this._createTextCharBg(
            styleDecl, lineLeftOffset, lineOffset.lineTop, heightOfLine, charWidth, charOffset));
      }

      charOffset += charWidth;
    }
  },

  /**
   * @private
   */
  _getSVGLineLeftOffset: function(lineIndex) {
    return fabric.util.toFixed(this._getLineLeftOffset(this.__lineWidths[lineIndex]), 2);
  },

  /**
   * @private
   */
  _getSVGLineTopOffset: function(lineIndex) {
    var lineTopOffset = 0, lastHeight = 0;
    for (var j = 0; j < lineIndex; j++) {
      lineTopOffset += this._getHeightOfLine(this.ctx, j);
    }
    lastHeight = this._getHeightOfLine(this.ctx, j);
    return {
      lineTop: lineTopOffset,
      offset: (this._fontSizeMult - this._fontSizeFraction) * lastHeight / (this.lineHeight * this._fontSizeMult)
    };
  },

  /**
   * @private
   */
  _createTextCharBg: function(styleDecl, lineLeftOffset, lineTopOffset, heightOfLine, charWidth, charOffset) {
    return [
      //jscs:disable validateIndentation
      '<rect fill="', styleDecl.textBackgroundColor,
      '" x="', lineLeftOffset + charOffset,
      '" y="', lineTopOffset - this.height/2,
      '" width="', charWidth,
      '" height="', heightOfLine / this.lineHeight,
      '"></rect>'
      //jscs:enable validateIndentation
    ].join('');
  },

  /**
   * @private
   */
  _createTextCharSpan: function(_char, styleDecl, lineLeftOffset, lineTopOffset, charOffset) {

    var fillStyles = this.getSvgStyles.call(fabric.util.object.extend({
      visible: true,
      fill: this.fill,
      stroke: this.stroke,
      type: 'text'
    }, styleDecl));

    return [
      //jscs:disable validateIndentation
      '<tspan x="', lineLeftOffset + charOffset, '" y="',
        lineTopOffset - this.height/2, '" ',
        (styleDecl.fontFamily ? 'font-family="' + styleDecl.fontFamily.replace(/"/g, '\'') + '" ': ''),
        (styleDecl.fontSize ? 'font-size="' + styleDecl.fontSize + '" ': ''),
        (styleDecl.fontStyle ? 'font-style="' + styleDecl.fontStyle + '" ': ''),
        (styleDecl.fontWeight ? 'font-weight="' + styleDecl.fontWeight + '" ': ''),
        (styleDecl.textDecoration ? 'text-decoration="' + styleDecl.textDecoration + '" ': ''),
        'style="', fillStyles, '">',
        fabric.util.string.escapeXml(_char),
      '</tspan>'
      //jscs:enable validateIndentation
    ].join('');
  }
});
/* _TO_SVG_END_ */


(function() {

  if (typeof document !== 'undefined' && typeof window !== 'undefined') {
    return;
  }

  var DOMParser = require('xmldom').DOMParser,
      URL = require('url'),
      HTTP = require('http'),
      HTTPS = require('https'),

      Canvas = require('canvas'),
      Image = require('canvas').Image;

  /** @private */
  function request(url, encoding, callback) {
    var oURL = URL.parse(url);

    // detect if http or https is used
    if ( !oURL.port ) {
      oURL.port = ( oURL.protocol.indexOf('https:') === 0 ) ? 443 : 80;
    }

    // assign request handler based on protocol
    var reqHandler = (oURL.protocol.indexOf('https:') === 0 ) ? HTTPS : HTTP,
        req = reqHandler.request({
          hostname: oURL.hostname,
          port: oURL.port,
          path: oURL.path,
          method: 'GET'
        }, function(response) {
          var body = '';
          if (encoding) {
            response.setEncoding(encoding);
          }
          response.on('end', function () {
            callback(body);
          });
          response.on('data', function (chunk) {
            if (response.statusCode === 200) {
              body += chunk;
            }
          });
        });

    req.on('error', function(err) {
      if (err.errno === process.ECONNREFUSED) {
        fabric.log('ECONNREFUSED: connection refused to ' + oURL.hostname + ':' + oURL.port);
      }
      else {
        fabric.log(err.message);
      }
    });

    req.end();
  }

  /** @private */
  function requestFs(path, callback) {
    var fs = require('fs');
    fs.readFile(path, function (err, data) {
      if (err) {
        fabric.log(err);
        throw err;
      }
      else {
        callback(data);
      }
    });
  }

  fabric.util.loadImage = function(url, callback, context) {
    function createImageAndCallBack(data) {
      img.src = new Buffer(data, 'binary');
      // preserving original url, which seems to be lost in node-canvas
      img._src = url;
      callback && callback.call(context, img);
    }
    var img = new Image();
    if (url && (url instanceof Buffer || url.indexOf('data') === 0)) {
      img.src = img._src = url;
      callback && callback.call(context, img);
    }
    else if (url && url.indexOf('http') !== 0) {
      requestFs(url, createImageAndCallBack);
    }
    else if (url) {
      request(url, 'binary', createImageAndCallBack);
    }
    else {
      callback && callback.call(context, url);
    }
  };

  fabric.loadSVGFromURL = function(url, callback, reviver) {
    url = url.replace(/^\n\s*/, '').replace(/\?.*$/, '').trim();
    if (url.indexOf('http') !== 0) {
      requestFs(url, function(body) {
        fabric.loadSVGFromString(body.toString(), callback, reviver);
      });
    }
    else {
      request(url, '', function(body) {
        fabric.loadSVGFromString(body, callback, reviver);
      });
    }
  };

  fabric.loadSVGFromString = function(string, callback, reviver) {
    var doc = new DOMParser().parseFromString(string);
    fabric.parseSVGDocument(doc.documentElement, function(results, options) {
      callback && callback(results, options);
    }, reviver);
  };

  fabric.util.getScript = function(url, callback) {
    request(url, '', function(body) {
      eval(body);
      callback && callback();
    });
  };

  fabric.Image.fromObject = function(object, callback) {
    fabric.util.loadImage(object.src, function(img) {
      var oImg = new fabric.Image(img);

      oImg._initConfig(object);
      oImg._initFilters(object, function(filters) {
        oImg.filters = filters || [ ];
        callback && callback(oImg);
      });
    });
  };

  /**
   * Only available when running fabric on node.js
   * @param {Number} width Canvas width
   * @param {Number} height Canvas height
   * @param {Object} [options] Options to pass to FabricCanvas.
   * @param {Object} [nodeCanvasOptions] Options to pass to NodeCanvas.
   * @return {Object} wrapped canvas instance
   */
  fabric.createCanvasForNode = function(width, height, options, nodeCanvasOptions) {
    nodeCanvasOptions = nodeCanvasOptions || options;

    var canvasEl = fabric.document.createElement('canvas'),
        nodeCanvas = new Canvas(width || 600, height || 600, nodeCanvasOptions);

    // jsdom doesn't create style on canvas element, so here be temp. workaround
    canvasEl.style = { };

    canvasEl.width = nodeCanvas.width;
    canvasEl.height = nodeCanvas.height;

    var FabricCanvas = fabric.Canvas || fabric.StaticCanvas,
        fabricCanvas = new FabricCanvas(canvasEl, options);

    fabricCanvas.contextContainer = nodeCanvas.getContext('2d');
    fabricCanvas.nodeCanvas = nodeCanvas;
    fabricCanvas.Font = Canvas.Font;

    return fabricCanvas;
  };

  /** @ignore */
  fabric.StaticCanvas.prototype.createPNGStream = function() {
    return this.nodeCanvas.createPNGStream();
  };

  fabric.StaticCanvas.prototype.createJPEGStream = function(opts) {
    return this.nodeCanvas.createJPEGStream(opts);
  };

  var origSetWidth = fabric.StaticCanvas.prototype.setWidth;
  fabric.StaticCanvas.prototype.setWidth = function(width, options) {
    origSetWidth.call(this, width, options);
    this.nodeCanvas.width = width;
    return this;
  };
  if (fabric.Canvas) {
    fabric.Canvas.prototype.setWidth = fabric.StaticCanvas.prototype.setWidth;
  }

  var origSetHeight = fabric.StaticCanvas.prototype.setHeight;
  fabric.StaticCanvas.prototype.setHeight = function(height, options) {
    origSetHeight.call(this, height, options);
    this.nodeCanvas.height = height;
    return this;
  };
  if (fabric.Canvas) {
    fabric.Canvas.prototype.setHeight = fabric.StaticCanvas.prototype.setHeight;
  }

})();
