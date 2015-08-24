/**
 * Alltosun - common.js 公用JS函数库
 * Copyright (c) 2009-2011 Alltosun.INC - http://www.alltosun.com
 * Date: 2011/01/06
 * @author gaojj@alltosun.com
 * @requires jQuery v1.4.4+
 * @requires jQuery-ui v1.8.7+
 * $Id: common.js 53045M 2013-08-14 01:34:36Z (local) $
 */

/**
 * 防止被其他页面作为iframe包含
 */
/*if (window != top) top.location.href = location.href;*/

/**
 * 按钮禁用
 */
function setDisabled(obj, html) {
  if (html) {
    obj.data('disabled', 1).addClass('disabled').html(html);
  } else {
    obj.data('disabled', 1).addClass('disabled');
  }
}

/**
 * 解除按钮
 * @param obj
 * @param html
 */
function unsetDisabled(obj, html) {
  if (html) {
    obj.removeData('disabled').removeClass('disabled').html(html);  
  } else {
    obj.removeData('disabled').removeClass('disabled');
  }
  
}

/**
 * 加入到收藏夹，支持IE、Firefox、Opera
 * @param clickObj 当前点击的对象
 * @return
 */
function addToBookmark(clickObj)
{
  var bookmarkUrl = window.location.href;
  var bookmarkTitle = document.title;

	if (window.sidebar) {
	  // Firefox书签
		window.sidebar.addPanel(bookmarkTitle, bookmarkUrl,"");
	} else if( window.external || document.all) {
	  // IE收藏夹
		window.external.AddFavorite(bookmarkUrl, bookmarkTitle);
	} else if(window.opera) {
	  // Opera
	  if (!clickObj instanceof jQuery) {
	    clickObj = $(clickObj);
	  }
	  clickObj.attr("href", bookmarkUrl);
	  clickObj.attr("title", bookmarkTitle);
	  clickObj.attr("rel", "sidebar");
	  clickObj.click();
	} else {
		alert('您的浏览器不支持该功能，请手动将本页面加入收藏夹。');
	}
}

/**
 * 复制到剪贴板
 */
function copyToClipboard(txt)
{
  if(window.clipboardData) {
    window.clipboardData.clearData();
    window.clipboardData.setData("Text", txt);
  } else if(navigator.userAgent.indexOf("Opera") != -1) {
    window.location = txt;
  } else if (window.netscape) {
    try {
      netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
    } catch (e) {
      alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");
    }
    var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
    if (!clip) {
      return;
    }
    var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
    if (!trans) {
      return;
    }
    trans.addDataFlavor('text/unicode');
    var str = new Object();
    var len = new Object();
    var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
    var copytext = txt;
    str.data = copytext;
    trans.setTransferData("text/unicode",str,copytext.length*2);
    var clipid = Components.interfaces.nsIClipboard;
    if (!clip) {
      return false;
    }
    clip.setData(trans,null,clipid.kGlobalClipboard);
    alert("复制成功！");
  }
}

/**
 * 图片垂直居中
 * @param obj 图片的jQuery对象
 * @param maxHeight 最大高度
 * @param maxWidth 最大宽度
 * @param border 补的边框，可以传none，则不设置border
 * @param backgroundColor 补的背景色
 * @param loadingImg 是否开启loading动画，默认开启
 * @return
 */
function vhCenter(obj, maxHeight, maxWidth, border, backgroundColor, loadingImg){
	if (obj == undefined || maxHeight == undefined || maxWidth == undefined) {
		return;
	}
	var backgroundColor = backgroundColor || "#FFFFFF";
	var border = border || "1px solid #CCCCCC";
	// FIXME 永远为true
	var loadingImg = loadingImg || true;
	// 图片定位
	var imgPad = function(imgObj){
		var cssAttr = {"background":backgroundColor};
		var paddingV = paddingH = 0;
		var imgHeight = imgObj.height();
		var imgWidth  = imgObj.width();
		// fix img in display:none
		if (imgHeight == 0) {
			$("body").append('<div id="tmpImg" style="position:absolute;width:0px;visibility:hidden;overflow:hidden;"><img src="'+imgObj.attr('src')+'" /></div>');
			imgHeight = $("#tmpImg > img").height();
			imgWidth = $("#tmpImg > img").width();
			$("#tmpImg").remove();
		}
		if (imgHeight < maxHeight) {
			paddingV = (maxHeight - imgHeight)/2;
			$.extend(cssAttr, {"padding-top":paddingV, "padding-bottom":paddingV});
		}
		if (imgWidth < maxWidth) {
			paddingH = (maxWidth - imgWidth)/2;
			$.extend(cssAttr, {"padding-left":paddingH, "padding-right":paddingH});
		}
		if (border != 'none') {
			$.extend(cssAttr, {"border":border});
		}
		imgObj.css(cssAttr);
	};
	$.each(obj, function(k, v){
		var img = $(v);
		if (loadingImg) {
			img.hide();
			var divWidth = maxWidth+2, divHeight = maxHeight+2;
			if (border == 'none') {
				divWidth = maxWidth;
				divHeight = maxHeight;
			}
			img.before('<div class="loadingImg" style="width:'+divWidth+'px; height:'+divHeight+'px;"></div>');
		}
		// 当document.ready完了后img不一定ready，尤其是在强制刷新时易出现取得的img宽高与实际不同
		img.load(function(){
			imgPad(img);
			if (loadingImg) {
				img.prev("div.loadingImg").hide();
				img.show();
			}
		});
		$(window).load(function(){
			imgPad(img);
			if (loadingImg) {
				img.prev("div.loadingImg").hide();
				img.show();
			}
		});
	});
}

/**
 * 类Facebook图片居中
 * @param {jQueryobject} container 装载图片的外框，内部图片可相对其相对定位（relative）
 * @param number width 框的宽度
 * @param number height 框的高度
 * @author liw
 */
function imageCenter(containers, maxWidth, maxHeight, isLoading)
{
  if (!containers || !containers.length || !maxWidth || !maxHeight) {
    return false;
  }
  
  // 添加固定样式
  containers.css({
    overflow: 'hidden'
    //width: maxWidth+'px',
    //height: maxHeight+'px'
    //position: 'relative'
  });
  
  // 遍历
  $.each(containers, function(){
    var container = $(this);
    var imgObj = container.children('img').eq(0);
    var src    = imgObj.attr('src');
    if (!src) return true;
    imgObj.removeAttr("width").removeAttr("height");
    // loading
    if (isLoading) {
      imgObj.attr('src', siteUrl+'/images/loading.gif').css({
        'width': '16px',
        'height': '16px',
        'top': (maxHeight/2 - 8) + 'px',
        'left': (maxWidth/2 - 8) + 'px',
        'position': 'relative'
      });
    }
    // 加载图片
    loadImage(src, function(img){
      var width  = img.width;
      var height = img.height;
      
      if ( (width / height) >= (maxWidth/maxHeight) ) {
        var realW = (width / height) * maxHeight;
        var left = (realW -maxWidth) /2;
        imgObj.attr('src', src).attr('style', '').css({
          'height': maxHeight+'px',
          'left': '-' + left + 'px',
          'position': 'relative'
        });
      } else {
        var realH = (height / width) * maxWidth;
        var top = (realH-maxHeight) /2;
        imgObj.attr('src', src).attr('style', '').css({
          'width': maxWidth+'px',
          'top': '-' + top + 'px',
          'position': 'relative'
        });
      }
    });
  });
}

/**
 * 加载图片
 */
/**
 * 加载图片并返回图片js dom对象给callback
 */
function loadImage(url, successCB, errorCB)
{
  //创建一个Image对象，实现图片的预下载
  var img = new Image();
  img.onload = function(){
    // 防止gif图多次onload
    img.onload = null;
    if ( typeof successCB == 'function' ) successCB(img);
    }
  
    img.onerror = function() {
    if ( typeof errorCB == 'function' ) errorCB(img);
  }
  img.src = url;
}

/**
 * 给图片加上上一页/下一页的鼠标指针
 * @param img 图片的jQuery对象，也支持div等的jQuery对象
 * @param callback 点击鼠标时触发的回调函数
 * @return
 * @notice 考虑到多种主题的指针样式不同，改为添加class：cursorPrev和cursorNext
 */
function imgCursor(img, callback){
	var imgWidth = img[0].offsetWidth, imgLeft = img.offset().left;
	img.mousemove(function(e){
		if (e.pageX >= imgLeft && e.pageX <= imgWidth/2+imgLeft) {
			// prev
			$(this).removeClass('cursorNext').addClass('cursorPrev');
			$(this).attr('alt', '点击查看上一张').attr('title', '点击查看上一张');
			if (callback != undefined) {
				$(this).unbind('click');
				$(this).click(function(){
					callback('prev');
					return false;
				});
			}
		}
		if (e.pageX >= imgWidth/2+imgLeft && e.pageX <= imgWidth+imgLeft) {
			// next
			$(this).removeClass('cursorPrev').addClass('cursorNext');
			$(this).attr('alt', '点击查看下一张').attr('title', '点击查看下一张');
			if (callback != undefined) {
				$(this).unbind('click');
				$(this).click(function(){
					callback('next');
					return false;
				});
			}
		}
	});
}

/**
 * html实体化
 * @param str
 * @return
 */
function htmlSpecialChars(str){
	str = str.replace(/</g, '&lt;');
	str = str.replace(/>/g, '&gt;');
	return str;
}

function css(el, prop) {
    return parseInt($.css(el[0], prop)) || 0;
}

function width(el) {
    return  el[0].offsetWidth + css(el, 'marginLeft') + css(el, 'marginRight');
}

function height(el) {
    return el[0].offsetHeight + css(el, 'marginTop') + css(el, 'marginBottom');
}

/**
 * 验证字符串
 */
function checkStr(str, type)
{
  if (type == 'name') {
     // 用户名只能包括中文，英文，下划线(_)，连接线(-)
    if (str.match(/([\u4E00-\u9FBF]|[\u0041-\u005A]|[\u0061-\u007A]|\u005F|\u002D|\d)/g)) {
      return true;
    }
    return false;
  } else if (type == 'mail') {
    // Email验证
    if (str.match(/^([a-zA-Z0-9]+[\_|\.]?)*[a-zA-Z0-9]*@([a-zA-Z0-9]+\.)([a-zA-Z])+(|\.[a-zA-Z]+)$/g )) {
      return true;
    }
    return false;
  }
}

/**
 * 是否全是中文
 */
function isChinese(str)
{
  return new RegExp("^[\\u4e00-\\u9fa5]+$", "").test(str);
}

/**
 * 是否是汉字加数字
 */
function isChineseNum(str)
{
  return new RegExp("^[\\u4e00-\\u9fa5]+[0-9]*$", "").test(str);
}

/**
 * 精度加法
 */
function accuratePlus(arg1, arg2)
{
    var r1,r2,m;
    try{r1=arg1.toString().split(".")[1].length;}catch(e){r1=0;}
    try{r2=arg2.toString().split(".")[1].length;}catch(e){r2=0;}
    m=Math.pow(10,Math.max(r1,r2));
    return (arg1*m+arg2*m)/m;
}

/**
 * 精度减法
 */
function accurateMinus(arg1, arg2)
{
  var r1,r2,m,n;
  try{r1=arg1.toString().split(".")[1].length;}catch(e){r1=0;}
  try{r2=arg2.toString().split(".")[1].length;}catch(e){r2=0;}
  m=Math.pow(10,Math.max(r1,r2));
  //动态控制精度长度
  n=(r1>=r2)?r1:r2;
  return ((arg1*m-arg2*m)/m).toFixed(n);
}

/**
 * 精度乘法
 */
function accurateMultiply(arg1, arg2)
{
    var m=0,s1=arg1.toString(),s2=arg2.toString();
    try{m+=s1.split(".")[1].length;}catch(e){}
    try{m+=s2.split(".")[1].length;}catch(e){}
    return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
}

/**
 * 精度除法
 */
function accurateDivide(arg1, arg2)
{
    var t1=0,t2=0,r1,r2,n;
    try{t1=arg1.toString().split(".")[1].length;}catch(e){}
    try{t2=arg2.toString().split(".")[1].length;}catch(e){}
    with(Math){
        r1=Number(arg1.toString().replace(".",""));
        r2=Number(arg2.toString().replace(".",""));
    // 动态控制精度长度
    n=(t1>=t2)?t1:t2;
    var result = (r1/r2)*pow(10,t2-t1);
        return result.toFixed(n);
    }
}

/**
* 获取图片的缩略图
*/
function pathInfo(path, prefix)
{
    if (!path) {
    	return '';
    }
    var file_path = '';
    var path_arr = path.split('/');
    var path_arr_length = path_arr.length;
    // FIXME 改为读取对应资源的缩略图配置，但是需要函数传入资源类型，待考虑
    if (prefix) {
    	path_arr[path_arr_length-1] = prefix+'_'+path_arr[path_arr_length-1];
    }

    path = path_arr.join('/');
    //如果传入的路径没有标明上传文件夹的话，则补全路径
    path = uploadUrl+path;
    return path;
}

/**
 * 验证身份证号是否合法
 */
function checkIdentityCard(v_card)
{
	var reg = /^\d{15}(\d{2}[0-9X])?$/i;
	if (!reg.test(v_card)) {
		return false;
	}
	if (v_card.length==15) {
		var n = new Date();
        var y = n.getFullYear();
	    if(parseInt("19" + v_card.substr(6,2)) < 1900 || parseInt("19" + v_card.substr(6,2)) > y){
			return false;
		}
		var birth = "19" + v_card.substr(6,2) + "-" + v_card.substr(8,2) + "-" + v_card.substr(10,2);
		if(!isDate(birth)){
			return false;
		}
	}
	if (v_card.length==18) {
		var n = new Date();
		var y = n.getFullYear();
		if(parseInt(v_card.substr(6,4)) < 1900 || parseInt(v_card.substr(6,4)) > y){
			return false;
		}
		var birth = v_card.substr(6,4) + "-" + v_card.substr(10,2) + "-" + v_card.substr(12,2);
		if(!isDate(birth)){
			return false;
		}
		iW = new Array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2,1);
		iSum = 0;
		for ( i=0;i<17;i++){
			iC = v_card.charAt(i);
			iVal = parseInt(iC);
			iSum += iVal * iW[i];
		}
		iJYM = iSum % 11;
		if(iJYM == 0) sJYM = "1";
		else if(iJYM == 1) sJYM = "0";
        else if(iJYM == 2) sJYM = "x";
        else if(iJYM == 3) sJYM = "9";
        else if(iJYM == 4) sJYM = "8";
        else if(iJYM == 5) sJYM = "7";
        else if(iJYM == 6) sJYM = "6";
        else if(iJYM == 7) sJYM = "5";
        else if(iJYM == 8) sJYM = "4";
        else if(iJYM == 9) sJYM = "3";
        else if(iJYM == 10) sJYM = "2";
		var cCheck = v_card.charAt(17).toLowerCase();
		if( cCheck != sJYM ){
			return false;
		}
	}
	try {
	  var lvAreaId=v_card.substr(0,2);
	  if( lvAreaId=="11" || lvAreaId=="12" || lvAreaId=="13" || lvAreaId=="14" || lvAreaId=="15" ||
	    lvAreaId=="21" || lvAreaId=="22" || lvAreaId=="23" ||
	    lvAreaId=="31" || lvAreaId=="32" || lvAreaId=="33" || lvAreaId=="34" || lvAreaId=="35" || lvAreaId=="36" || lvAreaId=="37" ||
	    lvAreaId=="41" || lvAreaId=="42" || lvAreaId=="43" || lvAreaId=="44" || lvAreaId=="45" || lvAreaId=="46" ||
	    lvAreaId=="50" || lvAreaId=="51" || lvAreaId=="52" || lvAreaId=="53" || lvAreaId=="54" ||
	    lvAreaId=="61" || lvAreaId=="62" || lvAreaId=="63" || lvAreaId=="64" || lvAreaId=="65" ||
	    lvAreaId=="71" || lvAreaId=="82" || lvAreaId=="82" ) {
		return true;
	  } else {
		return false;
	  }
	} catch(ex) {
	}
	return true;
}

/**
 * 验证日期是否正确
 * @param strDate 格式1985-07-13
 * @returns {Boolean}
 */
function isDate(strDate)
{
  var strSeparator = "-"; //日期分隔符
  var strDateArray;
  var intYear;
  var intMonth;
  var intDay;
  var boolLeapYear;
  strDateArray = strDate.split(strSeparator);
  if(strDateArray.length!=3) return false;
  intYear = parseInt(strDateArray[0],10);
  intMonth = parseInt(strDateArray[1],10);
  intDay = parseInt(strDateArray[2],10);
  if(isNaN(intYear)||isNaN(intMonth)||isNaN(intDay)) return false;
  if (intMonth>12||intMonth<1) return false;
  if ((intMonth==1||intMonth==3||intMonth==5||intMonth==7||intMonth==8||intMonth==10||intMonth==12)&&(intDay>31||intDay<1)) return false;
  if ((intMonth==4||intMonth==6||intMonth==9||intMonth==11)&&(intDay>30||intDay<1)) return false;
  if(intMonth==2){
    if(intDay<1) return false;
		boolLeapYear = false;
    if ((intYear%100)==0) {
      if((intYear%400)==0) boolLeapYear = true;
    } else {
      if((intYear%4)==0) boolLeapYear = true;
    }
    if (boolLeapYear) {
      if(intDay>29) return false;
    } else {
      if(intDay>28) return false;
    }
  }
  return true;
}

/**
 * 验证是否是正整数
 * @param str strInt
 * @return bool
 */
function isPosInt(strInt)
{
  return (strInt.match(/^[1-9]{1}[0-9]*$/)!=null);
}

function loadJS(url,callback,charset)
{
    var script = document.createElement('script');
    script.onload = script.onreadystatechange = function ()
    {
        if (script && script.readyState && /^(?!(?:loaded|complete)$)/.test(script.readyState)) {
            return;
        }
        script.onload = script.onreadystatechange = null;
        script.src = '';
        script.parentNode.removeChild(script);
        script = null;
        if(callback){ callback(); }
    };
    script.charset=charset || document.charset || document.characterSet;
    script.src = url;
    try {document.getElementsByTagName("head")[0].appendChild(script);} catch (e) {}
}

/**
 * @param obj 对象
 * @param imgHeight 真实容器高度
 * @param speed 滚动速度
 */
function moveIt(obj, imgHeight, speed){
  obj.each(function(){
    $(this).hover(function(){
      var self  = $(this).find("img");
      var realH = self.outerHeight();
      var moveH = realH-imgHeight;
      if(moveH){
        self.animate({ "margin-top":"-"+moveH+"px" },speed);
      }
    },function(){
      var self  = $(this).find("img");
      self.animate({ "margin-top":"0" },speed);
    }); 
  });           
}

//检查用户授权情况
function checkAuth()
{
  var authHmtl = '<iframe src="'+authUrl+'" id="authFloat" style="position: absolute; left: 75px; height: 460px; width: 600px; border: 0; z-index: 10000;"></iframe>';
  
  if (needAuth == 1) {
    $('#authbox').css('height', '470px').html(authHmtl);
    var docH = parseInt($('body').height());
    // reset bg height
    $('#authbox_bg').css('height', docH);
    if (window.scrollTop > 150) {
        $('#authbox').css('top', window.scrollTop);
    }
    $('#authbox_bg').show();
    $('#authbox').show(function(){
      var thisH = 460,
      posH  = parseInt($('#authbox').position().top);
      if ( (docH - posH < thisH) && (posH - thisH > 10)) {
          $('#authbox').css('top', posH - thisH );
      }
    });
    
    return false;
  }

  if (needToken == 1) {
    $('#authbox').html(authHmtl);
    $('#authbox, #authbox_bg').hide();
    return false;
  }

  if (needToken == 0 && needAuth == 0) {
    $('#authbox').html('');
    $('#authbox, #authbox_bg').hide();
    return true;
  }
}

// 授权成功后的回调函数
var authSuccessCallback = function ()
{
  window.location.reload();
}
// 授权成功后需要执行的任务
// authSuccessTasks[0] = { 'obj':$(this), 'event':'click' } ...
var authSuccessTasks = authSuccessSubTasks = [];



/* 
 * 限制微博发布框字数
 */
String.byteLength = function(str) {
  if (typeof str == "undefined") {
    return 0;
  }
  var aMatch = str.match(/[^\x00-\x80]/g);
  return (str.length + (!aMatch ? 0 : aMatch.length));
};
String.trimHead = function(str) {
  return str.replace(/^(\u3000|\s|\t)*/gi, "");
};
String.trimTail = function(str) {
  return str.replace(/(\u3000|\s|\t)*$/gi, "");
};
String.trim = function(str) {
  return String.trimHead(String.trimTail(str));
};

var ua = navigator.userAgent.toLowerCase();
$IE = /msie/.test(ua);
$OPERA = /opera/.test(ua);
$MOZ = /gecko/.test(ua);
$IE5 = /msie 5 /.test(ua);
$IE55 = /msie 5.5/.test(ua);
$IE6 = /msie 6/.test(ua);
$IE7 = /msie 7/.test(ua);
$SAFARI = /safari/.test(ua);
$winXP = /windows nt 5.1/.test(ua);
$winVista = /windows nt 6.0/.test(ua);
$FF2 = /Firefox\/2/i.test(ua);
$IOS = /\((iPhone|iPad|iPod)/i.test(ua);

function $E(oID) {
  var node = typeof oID == "string" ? document.getElementById(oID) : oID;
  if (node != null) {
    return node;
  } else {}
  return null;
}

var $CLTMSG = {
    CD0033: "还可以输入<span>${num}</span>字",
    CD0034: "已经超出<span>${num}</span>个汉字"
};
/*
 * 检查字数
 */
function checkchar(inputId, textId){
  var maxlen = 280;
  var mdforwardtextarea = $E(inputId);
  var tipStringOK = $CLTMSG.CD0033;
  var tipStringErr = $CLTMSG.CD0034;
  var forwardInputLimit = function() {
    var num = Math.ceil(String.byteLength(String.trim(mdforwardtextarea.value)) / 2);
    if ($E(textId)) {
      if (num > 140) {
        $('#'+textId).html( tipStringErr.replace(/\$\{num\}/, (maxlen / 2 - num) * ( - 1)) );
        return false;
      } else {
        $('#'+textId).html( tipStringOK.replace(/\$\{num\}/, (maxlen / 2 - num)) );
        return true;
      }
    }
  };

  try {
    if($IE) {
      $E(inputId).focus();  
      return forwardInputLimit();
    } else {
        $E(inputId).focus();        
      return forwardInputLimit();
    }
  } catch(e) {  
    
  }
}