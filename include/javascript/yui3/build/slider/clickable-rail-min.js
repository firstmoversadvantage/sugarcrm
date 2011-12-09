/*
 Copyright (c) 2010, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.com/yui/license.html
 version: 3.3.0
 build: 3167
 */
YUI.add("clickable-rail",function(b){function a(){this._initClickableRail();}b.ClickableRail=b.mix(a,{prototype:{_initClickableRail:function(){this._evtGuid=this._evtGuid||(b.guid()+"|");this.publish("railMouseDown",{defaultFn:this._defRailMouseDownFn});this.after("render",this._bindClickableRail);this.on("destroy",this._unbindClickableRail);},_bindClickableRail:function(){this._dd.addHandle(this.rail);this.rail.on(this._evtGuid+b.DD.Drag.START_EVENT,b.bind(this._onRailMouseDown,this));},_unbindClickableRail:function(){if(this.get("rendered")){var c=this.get("contentBox"),d=c.one("."+this.getClassName("rail"));d.detach(this.evtGuid+"*");}},_onRailMouseDown:function(c){if(this.get("clickableRail")&&!this.get("disabled")){this.fire("railMouseDown",{ev:c});}},_defRailMouseDownFn:function(k){k=k.ev;var c=this._resolveThumb(k),g=this._key.xyIndex,h=parseFloat(this.get("length"),10),f,d,j;if(c){f=c.get("dragNode");d=parseFloat(f.getStyle(this._key.dim),10);j=this._getThumbDestination(k,f);j=j[g]-this.rail.getXY()[g];j=Math.min(Math.max(j,0),(h-d));this._uiMoveThumb(j);k.target=this.thumb.one("img")||this.thumb;c._handleMouseDownEvent(k);}},_resolveThumb:function(c){return this._dd;},_getThumbDestination:function(g,f){var d=f.get("offsetWidth"),c=f.get("offsetHeight");return[(g.pageX-Math.round((d/2))),(g.pageY-Math.round((c/2)))];}},ATTRS:{clickableRail:{value:true,validator:b.Lang.isBoolean}}},true);},"3.3.0",{requires:["slider-base"]});
