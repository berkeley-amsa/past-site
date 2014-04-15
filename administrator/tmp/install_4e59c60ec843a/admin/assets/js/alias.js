/* Copyright (C) 2007 - 2011 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function(c){var d=function(){};c.extend(d.prototype,{name:"AliasEdit",options:{edit:false},initialize:function(b,f){this.options=c.extend({},this.options,f);var a=this;this.input=b;this.trigger=b.find("a.trigger");this.panel=b.find("div.panel");this.text=this.panel.find("input:text");this.name=b.find('input[name="name"]');this.options.edit||this.name.bind("blur.name",function(){var e=c.String.slugify(c(this).val());if(e!=""){a.text.val(e);a.trigger.text(e);c(this).unbind("blur.name")}});this.trigger.bind("click",
function(e){e.preventDefault();c(this).hide();a.panel.addClass("active");a.text.focus();a.text.bind("keydown",function(g){g.stopPropagation();g.which==13&&a.setAlias();g.which==27&&a.remove()});a.input.find("input.accept").bind("click",function(g){g.preventDefault();a.setAlias()});a.input.find("a.cancel").bind("click",function(g){g.preventDefault();a.remove()})})},setAlias:function(){var b=c.String.slugify(this.text.val());if(b=="")b=(b=c.String.slugify(this.name.val()))?b:"42";this.text.val(b);this.trigger.text(b);
this.remove()},remove:function(){this.trigger.show();this.panel.removeClass("active")}});c.fn[d.prototype.name]=function(){var b=arguments,f=b[0]?b[0]:null;return this.each(function(){var a=c(this);if(d.prototype[f]&&a.data(d.prototype.name)&&f!="initialize")a.data(d.prototype.name)[f].apply(a.data(d.prototype.name),Array.prototype.slice.call(b,1));else if(!f||c.isPlainObject(f)){var e=new d;d.prototype.initialize&&e.initialize.apply(e,c.merge([a],b));a.data(d.prototype.name,e)}else c.error("Method "+
f+" does not exist on jQuery."+d.name)})}})(jQuery);
