/* Copyright (C) 2007 - 2011 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

function selectRelateditem(b,a,e){SqueezeBox.close?SqueezeBox.close():document.getElementById("sbox-window").close();jQuery("#"+e.replace(/_/g,"-").replace(/^a/g,"")).ElementRelatedItems("addItem",b,a)}
(function(b){var a=function(){};b.extend(a.prototype,{name:"ElementRelatedItems",options:{variable:null,msgDeleteItem:"Delete Item",msgSortItem:"Sort Item"},initialize:function(a,c){this.options=b.extend({},this.options,c);this.list=a.find("ul");this.list.delegate("div.item-delete","click",function(){b(this).closest("li").fadeOut(200,function(){b(this).remove()})}).sortable({handle:"div.item-sort",placeholder:"dragging",axis:"y",opacity:1,delay:100,tolerance:"pointer",containment:"parent",forcePlaceholderSize:!0,
scroll:!1,start:function(a,b){b.helper.addClass("ghost")},stop:function(b,a){a.item.removeClass("ghost")}})},addItem:function(a,c){var d=!1;this.list.find("li input").each(function(){b(this).val()==a&&(d=!0)});d||b('<li><div><div class="item-name">'+c+'</div><div class="item-sort" title="'+this.options.msgSortItem+'"></div><div class="item-delete" title="'+this.options.msgDeleteItem+'"></div><input type="hidden" name="'+this.options.variable+'" value="'+a+'"/></div></li>').appendTo(this.list)}});
b.fn[a.prototype.name]=function(){var e=arguments,c=e[0]?e[0]:null;return this.each(function(){var d=b(this);if(a.prototype[c]&&d.data(a.prototype.name)&&c!="initialize")d.data(a.prototype.name)[c].apply(d.data(a.prototype.name),Array.prototype.slice.call(e,1));else if(!c||b.isPlainObject(c)){var f=new a;a.prototype.initialize&&f.initialize.apply(f,b.merge([d],e));d.data(a.prototype.name,f)}else b.error("Method "+c+" does not exist on jQuery."+a.name)})}})(jQuery);
