/*
    Contact Form
    Version: 2.3.1
    Release date: Mon Aug 31 2020

    https://elfsight.com

    Copyright (c) 2020 Elfsight, LLC. ALL RIGHTS RESERVED
*/

"use strict";!function(e,t){var i,a,n,s,o,r,d,p,g,c=t.pluginParams.slug,l=t.pluginParams.domainId,f=JSON.parse(t.pluginParams.user),u="true"===t.pluginParams.widgetsClogged,h=!1,m=t.pluginParams.restApiUrl,v=t.wpApiSettings.nonce;function w(){this.pages=[]}w.prototype={constructor:w,add:function(t,i){if(!t||!i||!e.isFunction(i))return!1;this.pages[t]=i()||{}},get:function(e){return this.pages[e]||!1},show:function(t,i){var r,g=this,c=this.get(t);return!!c&&("init"in c&&e.isFunction(c.init)&&(r=c.init(i)),e.when(r).then(function(){d.hasClass("elfsight-admin-other-products-hidden-permanently")||d.toggleClass("elfsight-admin-other-products-hidden","widgets"!==t),a.removeClass("elfsight-admin-loading"),s.removeClass("active"),s.filter("[data-elfsight-admin-page="+t+"]").addClass("active"),p.length&&p.removeClass("elfsight-admin-page-active"),p=n.filter("[data-elfsight-admin-page-id="+t+"]"),o.css("min-height",p.outerHeight()),p.addClass("elfsight-admin-page-animation elfsight-admin-page-active"),setTimeout(function(){p.removeClass("elfsight-admin-page-animation"),"function"==typeof g.onPageChanged&&g.onPageChanged(t)},200)}),c)}};var C=new w;function S(){this.popups=[]}t.elfsightAdminPagesController=C,S.prototype={constructor:S,add:function(t,i){if(!t||!i||!e.isFunction(i))return!1;this.popups[t]=i()||{}},get:function(e){return this.popups[e]||!1},hide:function(e){(g=r.filter("[data-elfsight-admin-popup-id="+e+"]")).removeClass("elfsight-admin-popup-active")},show:function(t,i){var a,n=this.get(t);return!!n&&("init"in n&&e.isFunction(n.init)&&(a=n.init(i)),e.when(a).then(function(){g.length&&g.removeClass("elfsight-admin-popup-active"),(g=r.filter("[data-elfsight-admin-popup-id="+t+"]")).addClass("elfsight-admin-popup-animation elfsight-admin-popup-active"),setTimeout(function(){g.removeClass("elfsight-admin-popup-animation")},200)}),n)}};var b=new S;e(function(){i=e(".elfsight-admin"),a=e(".elfsight-admin-main"),n=e("[data-elfsight-admin-page-id]"),s=e("[data-elfsight-admin-page]"),o=e(".elfsight-admin-pages-container"),r=e("[data-elfsight-admin-popup-id]"),d=e(".elfsight-admin-other-products"),p=e(),g=e(),C.add("welcome",e.noop),C.add("widgets",function(){var t=[],i=e(".elfsight-admin-page-widgets"),a=e(".elfsight-admin-page-widgets-list",i),n=e(".elfsight-admin-template-widgets-list-item",i),s=e(".elfsight-admin-template-widgets-list-empty",i);a.on("click",".elfsight-admin-page-widgets-list-item-actions-remove",function(t){var i=e(this),a=i.attr("data-widget-id");i.closest(".elfsight-admin-page-widgets-list-item").addClass("elfsight-admin-page-widgets-list-item-removed"),S("widgets/remove",{id:a},"post",!0),t.preventDefault()}),a.on("click",".elfsight-admin-page-widgets-list-item-actions-restore a",function(t){var i=e(this),a=i.attr("data-widget-id");i.closest(".elfsight-admin-page-widgets-list-item").removeClass("elfsight-admin-page-widgets-list-item-removed"),S("widgets/restore",{id:a},"post",!0),t.preventDefault()}),a.tablesorter({cssAsc:"elfsight-admin-page-widgets-list-sort-asc",cssDesc:"elfsight-admin-page-widgets-list-sort-desc",cssHeader:"elfsight-admin-page-widgets-list-sort",headers:{0:{},1:{},2:{},3:{sorter:!1}}});var o=function(){var i=e("tbody",a).empty();e.isArray(t)&&t.length?(e.each(t,function(t,a){var s=e(n.html()),o="["+c.split("-").join("_")+' id="'+a.id+'"]';s.find(".elfsight-admin-page-widgets-list-item-name a").attr("href","#/edit-widget/"+a.id+"/").text(a.name);var r=new Date(1e3*(a.time_updated||a.time_created));s.find(".elfsight-admin-page-widgets-list-item-date").text(function(e){e instanceof Date||(e=new Date(Date.parse(e)));return["January","February","March","April","May","June","July","August","September","October","November","December"][e.getMonth()]+" "+e.getDate()+", "+e.getFullYear()}(r)),s.find(".elfsight-admin-page-widgets-list-item-shortcode-hidden").text(o);var d=s.find(".elfsight-admin-page-widgets-list-item-shortcode-input").val(o),p=s.find(".elfsight-admin-page-widgets-list-item-shortcode-copy-trigger").attr("data-clipboard-text",o),g=new ClipboardJS(p.get(0));g.on("success",function(){p.addClass("elfsight-admin-page-widgets-list-item-shortcode-copy-trigger-copied").find("span").text("Copied"),setTimeout(function(){p.removeClass("elfsight-admin-page-widgets-list-item-shortcode-copy-trigger-copied").find("span").text("Copy")},5e3)}),g.on("error",function(){var e=s.find(".elfsight-admin-page-widgets-list-item-shortcode-copy-error").show();d.select(),setTimeout(function(){e.hide()},5e3)}),s.find(".elfsight-admin-page-widgets-list-item-shortcode-value").text(o),s.find(".elfsight-admin-page-widgets-list-item-actions-edit").attr("href","#/edit-widget/"+a.id+"/"),s.find(".elfsight-admin-page-widgets-list-item-actions-duplicate").attr("href","#/edit-widget/"+a.id+"/duplicate/"),s.find(".elfsight-admin-page-widgets-list-item-actions-remove, .elfsight-admin-page-widgets-list-item-actions-restore a").attr("data-widget-id",a.id),s.appendTo(i)}),a.trigger("update")):e(s.html()).appendTo(i)};return{init:function(i,a){return S("widgets/list").then(function(i){if(i.status){if(t=i.data,!(u||e.isArray(t)&&t.length))return C.show("welcome"),e.Deferred().reject(i).promise();o(),h=!0}})}}}),C.add("edit-widget",function(){var i,a,n,s="add",o=e(".elfsight-admin-page-edit-widget"),r=e(".elfsight-admin-page-edit-widget-form-submit",o),d=e(".elfsight-admin-page-edit-widget-form-apply",o),p=e(".elfsight-admin-page-edit-widget-unsaved",o),g=e(".elfsight-admin-page-edit-widget-form-cancel",o),c=e(".elfsight-admin-page-edit-widget-name-input",o),u="elfsight-admin-page-edit-widget-form-editor",m=u+"-clone",v=e("."+m,o).parent(),w=e("."+m,o),y=!1,x=JSON.parse(w.attr("data-elfsight-admin-editor-settings")),_=JSON.parse(w.attr("data-elfsight-admin-editor-preferences")),k=JSON.parse(w.attr("data-elfsight-admin-editor-preview-url")),D=w.attr("data-elfsight-admin-editor-observer-url")||null;D&&(D=JSON.parse(D));var P=function(e){e,p.toggleClass("elfsight-admin-page-edit-widget-unsaved-visible",e),e?t.addEventListener("beforeunload",T):t.removeEventListener("beforeunload",T)},T=function(e){e.preventDefault(),e.returnValue="Widget has unsaved changed"},j=function(t){var n,o=c.val(),r={};a.getData()?r=a.getData():i&&(r=i.options),"update"===s&&(n="widgets/update"),"add"===s&&(n="widgets/add");var d={name:o||"Untitled",options:encodeURIComponent(JSON.stringify(r))};i&&(d.id=i.id),S(n,d,"post").done(function(a){a.status&&(a.id&&(i={id:a.id},h=!0,b.popups.rating.open(!0,3e4)),e.isFunction(t)&&t())})};return r.on("click",function(){e("html, body").animate({scrollTop:0}),j(function(){P(!1),hasher.setHash("widgets/")})}),d.on("click",function(){j(),P(!1)}),g.on("click",function(){hasher.setHash("widgets/"),P(!1)}),{init:function(t,r){var d=function(){c.val(i?i.name:""),n&&n.remove(),(n=w.clone().removeClass(m).addClass(u)).appendTo(v);var t=angular.module("elfsightEditor",["elfsightAppsEditor"]),s=i.public_id||i.id;t.controller("AppController",["$elfsightAppsEditor","$scope","$rootScope","$timeout",function(t,o,r,d){a=t,r.user=f,y=!1;var p={parent:n,previewUrl:k,observerUrl:D||void 0,settings:e.extend(!0,{},x),preferences:_,widget:{data:i.options,public_id:l+"-"+s},enableCustomCSS:!1,enableCloudProperties:!1,onChange:function(e){y?P(!0):y=!0}};t.init(p)}]),n.attr("ng-controller","AppController as app"),angular.bootstrap(n,["elfsightEditor"])};return t&&t.id?S("widgets/list",{id:t.id}).then(function(a){if(a.status){if(!a.data.length)return C.show("error",{message:"There is no widget with id "+t.id+"."}),e.Deferred().reject(a).promise();i=a.data[0],h=!0,d(),P(!1),o.toggleClass("elfsight-admin-page-edit-widget-new",!!t.duplicate),t.duplicate?S("widgets/prepare").then(function(e){i.public_id=e.data.widget_id,d(),s="add"}):s="update"}},function(){i=null}):S("widgets/prepare").then(function(e){s="add",i={public_id:e.data.widget_id},d(),o.addClass("elfsight-admin-page-edit-widget-new")})}}}),C.add("support",function(){var t=e(".elfsight-admin-page-support-ticket-form-container"),i=e(".elfsight-admin-page-support-ticket-form"),a=e(".elfsight-admin-page-support-ticket-form-purchase-code",i),n=e(".elfsight-admin-page-support-ticket-form-submit",i),s=e(".elfsight-admin-page-support-ticket-form-result"),o=e(".elfsight-admin-page-support-ticket-form-result-success"),r=e(".elfsight-admin-page-support-ticket-form-result-error"),d=e(".elfsight-admin-page-support-ticket-form-expired-date",t),p=function(){i.hide(),s.hide(),o.show()},g=function(){i.hide(),s.hide(),r.show()};return i.submit(function(t){var a;t.preventDefault(),t.stopPropagation(),n.prop("disabled",!0).val("Sending..."),a=new FormData(i[0]),e.ajax({type:"POST",url:i.attr("action"),data:a,processData:!1,contentType:!1}).done(function(e){200===e.code||201===e.code?p():g()})}),{init:function(n,s){var o=a.val();return!o||e.get(i.attr("data-cs"),{purchase_code:o},function(e){new Date(e.date)<new Date&&(t.addClass("elfsight-admin-page-support-ticket-expired"),d.text(new Date(e.date).toLocaleDateString()))})}}}),C.add("preferences",function(){var t=e(".elfsight-admin-page-preferences-form"),i=function(t,i){var a=i.find(".elfsight-admin-page-preferences-option-save");a.addClass("elfsight-admin-page-preferences-option-save-loading"),e.ajax({type:"POST",url:m+"/update-preferences/",data:t,dataType:"json",beforeSend:function(e){e.setRequestHeader("X-WP-Nonce",v)}}).done(function(e){var t=i.find(".elfsight-admin-page-preferences-option-save-success"),n=i.find(".elfsight-admin-page-preferences-option-save-error");a.removeClass("elfsight-admin-page-preferences-option-save-loading"),e.success?(n.text(""),t.addClass("elfsight-admin-page-preferences-option-save-success-visible"),setTimeout(function(){t.removeClass("elfsight-admin-page-preferences-option-save-success-visible")},2e3)):e.error&&n.text(e.error)})},a=function(e,t){var i=e.getSession().getScreenLength()*e.renderer.lineHeight+e.renderer.scrollBar.getWidth();t.height(i.toString()+"px"),e.resize()},n=ace.edit("elfsightPreferencesSnippetCSS");n.setOption("useWorker",!1),n.setTheme("ace/theme/monokai"),n.getSession().setMode("ace/mode/css"),n.commands.addCommand({name:"save",bindKey:{win:"Ctrl-S",mac:"Command-S"},exec:function(){var t=e(".elfsight-admin-page-preferences-option-css");i({preferences_custom_css:n.getSession().doc.getValue()},t)}}),a(n,e("#elfsightPreferencesSnippetCSS")),n.getSession().on("change",function(){a(n,e("#elfsightPreferencesSnippetCSS"))});var s=ace.edit("elfsightPreferencesSnippetJS");s.setOption("useWorker",!1),s.setTheme("ace/theme/monokai"),s.getSession().setMode("ace/mode/javascript"),s.commands.addCommand({name:"save",bindKey:{win:"Ctrl-S",mac:"Command-S"},exec:function(){var t=e(".elfsight-admin-page-preferences-option-js");i({preferences_custom_js:s.getSession().doc.getValue()},t)}}),a(s,e("#elfsightPreferencesSnippetJS")),s.getSession().on("change",function(){a(s,e("#elfsightPreferencesSnippetJS"))}),t.find(".elfsight-admin-page-preferences-option-save").click(function(t){t.preventDefault();var a=e(this),o=a.closest(".elfsight-admin-page-preferences-option"),r=a.closest(".elfsight-admin-page-preferences-option-input-container").find('input[type="text"]'),d={};r.each(function(t,i){d[e(i).attr("name")]=e(i).val()}),o.is(".elfsight-admin-page-preferences-option-css")&&(d.preferences_custom_css=n.getSession().doc.getValue()),o.is(".elfsight-admin-page-preferences-option-js")&&(d.preferences_custom_js=s.getSession().doc.getValue()),i(d,o)}),t.find('[name="preferences_force_script_add"]').change(function(){var t=e(this),a=t.closest(".elfsight-admin-page-preferences-option");i({option:{name:"force_script_add",value:t.is(":checked")?"on":"off"}},a)}),t.find('[name="preferences_access_role"]').change(function(){var t=e(this),a=t.closest(".elfsight-admin-page-preferences-option");i({option:{name:"access_role",value:t.val()}},a)}),t.find('[name="preferences_auto_upgrade"]').change(function(){var t=e(this),a=t.closest(".elfsight-admin-page-preferences-option");i({option:{name:"auto_upgrade",value:t.is(":checked")?"on":"off"}},a)})}),C.add("activation",function(){var t=e(".elfsight-admin-page-activation-form"),a=e(".elfsight-admin-page-activation-form-purchase-code-input",t),n=e(".elfsight-admin-page-activation-form-activated-input",t),s=e(".elfsight-admin-page-activation-form-supported-until-input",t),o=e(".elfsight-admin-page-activation-form-host-input",t),r=e(".elfsight-admin-page-activation-form-activation-button",t),d=e(".elfsight-admin-page-activation-form-activation",t),p=e(".elfsight-admin-page-activation-form-activation-confirm-no",t),g=e(".elfsight-admin-page-activation-form-activation-confirm-yes",t),l=(e(".elfsight-admin-page-activation-form-deactivation",t),e(".elfsight-admin-page-activation-form-deactivation-button",t)),f=e(".elfsight-admin-page-activation-form-deactivation-confirm-no",t),u=e(".elfsight-admin-page-activation-form-deactivation-confirm-yes",t),h=e(".elfsight-admin-page-activation-form-message-success",t),w=e(".elfsight-admin-page-activation-form-message-error",t),C=e(".elfsight-admin-page-activation-form-message-fail",t),S=e(".elfsight-admin-page-activation-faq"),b=e(".elfsight-admin-page-activation-faq-list-item",S),y=null,x=t.attr("data-activation-url"),_=t.attr("data-activation-version"),k=function(t,i,n){e.ajax({type:"POST",url:m+"/update-activation-data/",data:{purchase_code:t,supported_until:n||0,activated:i},beforeSend:function(e){e.setRequestHeader("X-WP-Nonce",v)}}),a.prop("readonly",i)};r.click(function(e){e.preventDefault(),e.stopPropagation(),D({purchaseCode:a.val(),host:o.val()})});var D=function(a){var o=arguments.length>1&&void 0!==arguments[1]&&arguments[1];return e.ajax({url:x,dataType:"jsonp",data:{action:"purchase_code",slug:c+"-cc",host:a.host,purchase_code:a.purchaseCode,version:_,force_update:o}}).done(function(o){if(o.verification){y=o.verification;var r=!!o.verification.valid;n.val(r),s.val(o.verification.supported_until||0),y.valid?(i.removeClass("elfsight-admin-activation-invalid").addClass("elfsight-admin-activation-activated"),w.text("").hide(),C.hide(),h.show(),e(".elfsight-admin-page-support-ticket-form-purchase-code").val(a.purchaseCode),k(a.purchaseCode,r,y.supported_until)):(i.removeClass("elfsight-admin-activation-activated").toggleClass("elfsight-admin-activation-invalid",!!a.purchaseCode),h.hide(),C.hide(),w.text(y.error).show()),y.exception&&"PC_REGISTERED_TO_ANOTHER"===y.exception&&(w.text("").hide(),d.find(".elfsight-admin-page-activation-form-activation-confirm-caption-message").html(y.error),t.addClass("elfsight-admin-page-activation-form-activation-confirm-visible"))}}).fail(function(){i.removeClass("elfsight-admin-activation-activated").addClass("elfsight-admin-activation-invalid"),n.val(!1),h.hide(),w.hide(),C.show(),k(a.purchaseCode,!1)})};l.click(function(e){e.preventDefault(),e.stopPropagation(),t.addClass("elfsight-admin-page-activation-form-deactivation-confirm-visible")}),f.click(function(e){e.preventDefault(),e.stopPropagation(),t.removeClass("elfsight-admin-page-activation-form-deactivation-confirm-visible")}),u.click(function(r){r.preventDefault(),r.stopPropagation(),t.removeClass("elfsight-admin-page-activation-form-deactivation-confirm-visible");var d=a.val(),p=o.val();e.ajax({url:x,dataType:"jsonp",data:{action:"deactivate",slug:c+"-cc",host:p,purchase_code:d,version:_}}).done(function(e){a.val(""),n.val("false"),s.val(0),i.removeClass("elfsight-admin-activation-activated"),h.hide(),C.hide(),w.hide(),k("",!1)})}),p.click(function(e){e.preventDefault(),e.stopPropagation(),t.removeClass("elfsight-admin-page-activation-form-activation-confirm-visible")}),g.click(function(e){e.preventDefault(),e.stopPropagation(),D({purchaseCode:a.val(),host:o.val()},!0)}),S.find(".elfsight-admin-page-activation-faq-list-item-question").click(function(){var t=e(this).closest(".elfsight-admin-page-activation-faq-list-item");b.not(t).removeClass("elfsight-admin-page-activation-faq-list-item-active"),t.toggleClass("elfsight-admin-page-activation-faq-list-item-active")})}),C.add("error",function(){var t=e(".elfsight-admin-page-error");return{init:function(i){i&&i.message&&e(".elfsight-admin-page-error-message",t).text(i.message)}}}),b.add("rating",function(){var t=e(".elfsight-admin-header-rating"),i=t.find("input[name=rating-header]"),a=e(".elfsight-admin-popup-rating"),n=a.find("form"),s=a.find("input[name=rating-popup]"),o=a.find(".elfsight-admin-popup-textarea"),r=a.find(".elfsight-admin-popup-text"),d=a.find(".elfsight-admin-popup-footer-button-ok"),p=a.find(".elfsight-admin-popup-footer-button-close"),g=localStorage.getItem("popupRatingShowed")?localStorage.getItem("popupRatingShowed"):Math.floor(Date.now()/1e3),c=parseInt(g)+86400<Math.floor(Date.now()/1e3),l=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:1e3;setTimeout(function(){if(a.length&&!a.hasClass("elfsight-admin-popup-sent")){var t=!~hasher.getHash().indexOf("edit-widget")&&!~hasher.getHash().indexOf("add-widget");(!e||e&&c&&t&&h)&&b.show("rating"),localStorage.setItem("popupRatingShowed",Math.floor(Date.now()/1e3))}},t)};setTimeout(function(){h&&t&&(l(!0,3e4),t.slideDown())},5e3),i.on("change",function(){var t=parseInt(e(this).val());l(!1,0),setTimeout(function(){s.filter('[value="'+t+'"]').prop("checked",!0),f(t)},400),e(this).prop("checked",!1)});var f=function(e){d.removeClass("elfsight-admin-popup-footer-button-hide"),o.toggleClass("elfsight-admin-popup-textarea-hide",5===e),r.toggleClass("elfsight-admin-popup-text-hide",e<5)};return s.on("change",function(){f(parseInt(e(this).val()))}),d.on("click",function(i){i.preventDefault();var s=parseInt(n.find('input[name="rating-popup"]:checked').val()),r=n.find("textarea").val();5===s&&w(e(i.target).attr("href")),s<5&&""===r?o.toggleClass("elfsight-admin-popup-textarea-error",!0):(o.toggleClass("elfsight-admin-popup-textarea-error",!1),e.ajax({type:"POST",url:m+"/rating-send/",data:{value:s,comment:r},beforeSend:function(e){e.setRequestHeader("X-WP-Nonce",v)}}).then(function(){a.addClass("elfsight-admin-popup-sent"),p.text("OK"),d.addClass("elfsight-admin-popup-footer-button-hide"),t.slideUp(),localStorage.removeItem("popupRatingShowed")}))}),p.on("click",function(){b.hide("rating"),d.addClass("elfsight-admin-popup-footer-button-hide"),o.addClass("elfsight-admin-popup-textarea-hide"),r.addClass("elfsight-admin-popup-text-hide"),s.prop("checked",!1)}),{init:function(e,t){return!0},open:l}});var w=function(e,i){var a=940,n=700,s=["width="+a,"height="+n,"menubar=no","toolbar=no","resizable=yes","scrollbars=yes","left="+(t.screen.availLeft+t.screen.availWidth/2-a/2),"top="+(t.screen.availTop+t.screen.availHeight/2-n/2)];t.open(e,i,s.join(","))},S=function(t,i,n,s){return i="post"===(n="post"===n?"post":"get")?JSON.stringify(i):i,e.ajax({url:m+"/"+t+"/",dataType:"json",data:i,contentType:"application/json",type:n,beforeSend:function(e){e.setRequestHeader("X-WP-Nonce",v),e.setRequestHeader("X-HTTP-Method-Override",n),s||a.addClass("elfsight-admin-loading")}}).always(function(){s||a.removeClass("elfsight-admin-loading")}).then(function(t){return t.status?t:(C.show("error",{message:"An error occurred during your request process. Please, try again."}),e.Deferred().reject(t).promise())},function(e){return C.show("error",{message:"An error occurred during your request process. Please, try again."}),e})};if(t.crossroads&&t.hasher){crossroads.addRoute("/add-widget/",function(){C.show("edit-widget")}),crossroads.addRoute("/edit-widget/{id}/",function(e){C.show("edit-widget",{id:e})}),crossroads.addRoute("/edit-widget/{id}/duplicate/",function(e){C.show("edit-widget",{id:e,duplicate:!0})}),crossroads.addRoute("/{page}/",function(e){e&&-1===e.indexOf("!")&&(C.show(e)||C.show("error",{message:"The requested page was not found."}))});var y=function(e,t){crossroads.parse(e)};hasher.initialized.add(y),hasher.changed.add(y),hasher.init(),hasher.getHash()||hasher.setHash("widgets/")}})}(jQuery,window);