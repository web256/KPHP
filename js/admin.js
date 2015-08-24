/**
 * Alltosun - admin.js 后台通用JS代码
 * Copyright (c) 2009-2011 Alltosun.INC - http://www.alltosun.com
 * Date: 2011/01/06
 * @author gaojj@alltosun.com
 * @requires jQuery v1.4.4+
 * @requires jQuery-ui v1.8.7+
 */
// 删除提示信息 为了兼容原有后台程序移植过来的 将来将要被废除
var prompt = {
  'prompt': "确定要删除记录吗?",
  'nochange': "您没有要删除的记录",
  'errors': "删除失败"
};
var predefineParam;

var resType = resType || '';
var resName = resName || '';
res_name = '';
$(function(){

  //added by ninghx 2012-07-31 将页面中的时间插件转移到公用的js中
  $('#start_time, #end_time').datepicker({
      dateFormat: 'yy-mm-dd',
      showButtonPanel: true
  });

  $('#vip_start_time, #vip_end_time').datepicker({
      dateFormat: 'yy-mm-dd',
      showButtonPanel: true
  });

  //点击列表选中checkbox
  $(".dataBox table tbody  tr").click(function(e){
    var clickTarget = $(e.target);
    // 当直接点击checkbox时，不做checked的切换
    if (clickTarget.is("input.listSelect")) {
      return;
    }
    var listCheckbox = $("input.listSelect", $(this));
    if (listCheckbox.is(":disabled")) {
      return;
    }
    if (listCheckbox.attr("checked")) {
      listCheckbox.removeAttr("checked");
    } else {
      listCheckbox.attr("checked", "checked");
    }
  });

  // 用于去除mozilla中radio和checkbox的bug问题
  if($.browser.mozilla) $("form").attr("autocomplete", "off");

  // 全选
  $("input.selectAll").click(function(){
    console.log($(this).attr("checked"));
	
    if ($(this).attr("checked")) {
      $("input.selectAll, input.listSelect").not(":disabled").attr("checked", "checked");
    } else {
      $("input.selectAll, input.listSelect").not(":disabled").removeAttr("checked");
    }
  });

  //操作警告
  $(".warningAction").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    if (!confirm("确定要执行该操作吗？")) {
      return false;
    }

    var clickObj = $(this);
    var url = clickObj.attr("href");
    $.post(url, {}, function(json){
      if (json.info != 'ok') {
        alert(json.info);
      } else {
        clickObj.closest("tr").fadeOut(function(){
          $(this).remove();
          interLineColor();
        });
      }
    }, 'json');
    return false;
  });

  // 单个删除
  $(".deleteOne").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    if (!confirm("您确定要删除该条记录吗")) {
      return false;
    }

    var clickObj = $(this);
    var url = clickObj.attr("href");
    $.post(url, {}, function(json){
      if (json.info != 'ok') {
        alert(json.info);
      } else {
        clickObj.closest("tr").fadeOut(function(){
          $(this).remove();
          interLineColor();
        });
      }
    }, 'json');
    return false;
  });
  
  //评论管理中的加入头条
  $(".addHead").click(function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    if (!confirm("确定要加入头条吗？")) {
	      return false;
	    }

	    var clickObj = $(this);
	    var url = clickObj.attr("href");
	    $.post(url, {}, function(json){
	      if (json.info != 'ok') {
	        alert(json.info);
	      } else {
	        clickObj.closest("tr").fadeOut(function(){
	          $(this).remove();
	          interLineColor();
	        });
	      }
	    }, 'json');
	    return false;
	  });
  //头条管理中的撤销头条
  $(".reverH").click(function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    if (!confirm("确定要撤销头条吗？")) {
	      return false;
	    }

	    var clickObj = $(this);
	    var url = clickObj.attr("href");
	    $.post(url, {}, function(json){
	      if (json.info != 'ok') {
	        alert(json.info);
	      } else {
	        clickObj.closest("tr").fadeOut(function(){
	          $(this).remove();
	          interLineColor();
	        });
	      }
	    }, 'json');
	    return false;
	  });
  //单个还原
  $(".recoverOne").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    if (!confirm("确定要还原该条记录吗？")) {
      return false;
    }

    var clickObj = $(this);
    var url = clickObj.attr("href");
    $.post(url, {}, function(json){
      if (json.info != 'ok') {
        alert(json.info);
      } else {
        clickObj.closest("tr").fadeOut(function(){
          $(this).remove();
          interLineColor();
        });
      }
    }, 'json');
    return false;
  });
  
  // 槽点列表将帖子恢复到普通帖子
  $(".revertOne").click(function(e){
      e.preventDefault();
      e.stopPropagation();
      if (!confirm("确定要将帖子恢复到普通帖子吗？")) {
        return false;
      }

      var clickObj = $(this);
      var url = clickObj.attr("href");
      $.post(url, {}, function(json){
        if (json.info != 'ok') {
          alert(json.info);
        } else {
          clickObj.closest("tr").fadeOut(function(){
            $(this).remove();
            interLineColor();
          });
        }
      }, 'json');
      return false;
    });
  
  // 槽点列表将普通帖子置顶
  $(".topOne").click(function(e){
      e.preventDefault();
      e.stopPropagation();
      if (!confirm("确定要将帖子置顶吗？")) {
        return false;
      }

      var clickObj = $(this);
      var url = clickObj.attr("href");
      $.post(url, {}, function(json){
        if (json.info != 'ok') {
          alert(json.info);
        } else {
          clickObj.closest("tr").fadeOut(function(){
            $(this).remove();
            interLineColor();
          });
        }
      }, 'json');
      return false;
    });
  //前台显示
  $(".front_view").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    if (!confirm("确定让本区域显示在前台吗？")) {
      return false;
    }

    var clickObj = $(this);
    var url = clickObj.attr("href");
    $.post(url, {}, function(json){
      if (json.info != 'ok') {
        alert(json.info);
      } else {
        clickObj.closest("tr").fadeOut(function(){
          $(this).remove();
          interLineColor();
        });
      }
    }, 'json');
    return false;
  });

  // 批量删除
  $(".deleteAll").click(function(e){
  	//aler
    e.preventDefault();
    var url = $(this).attr("href");
    var ids = getCheckedIds();
	console.log(ids);
    deleteAll(url, ids);
    $("input[name=selectAll]").not(":disabled").removeAttr("checked");
    return false;
  });

  // nba 帖子审核
  $('.checkOne').click(function(e){
      e.preventDefault();
      e.stopPropagation();
      if (!confirm("确定要通过审核吗?")) {
        return false;
      }

      var clickObj = $(this);
      var url = clickObj.attr("href");
      $.post(url, {}, function(json){
        if (json.info != 'ok') {
          alert(json.info);
        } else {
          clickObj.closest("tr").fadeOut(function(){
            $(this).remove();
            interLineColor();
          });
        }
      }, 'json');
      return false;
  })
  
  // nba批量审核
  $(".checkAll").click(function(e){
    e.preventDefault();
    
    if (!confirm("确定要全部通过审核吗?")) {
        return false;
      }
    
    var ids = getCheckedIds();
    console.log(ids);
    var clickObj = $(this);
    var url = clickObj.attr("href");
    
    $.post(url, {'id':ids.join(',')}, function(json){
      if (json.info != 'ok') {
        alert(json.info);
      } else {
          $.each(ids, function(k, v){
              $("#dataList"+v).fadeOut(function(){
                $(this).remove();
              });
            });
            interLineColor();
      }
    }, 'json');
    return false;
    
    $("input[name=selectAll]").not(":disabled").removeAttr("checked");
    
    return false;
  });
  
  
  // 点击列表选中checkbox
  $("tbody > tr", $("#AnTable")).click(function(e){
    var clickTarget = $(e.target);
    // 当直接点击checkbox时，不做checked的切换
    if (clickTarget.is("input[name=listSelect]")) {
      return;
    }
    var listCheckbox = $("input[name=listSelect]", $(this));
    if (listCheckbox.is(":disabled")) {
      return;
    }
    if (listCheckbox.attr("checked")) {
      listCheckbox.removeAttr("checked");
    } else {
      listCheckbox.attr("checked", "checked");
    }
  });

  // 批量转移分类
  $('#moveCategory').click(function(){
    $.getJSON("admin/category/get_list&res_name="+resName, function(data){
      var input='&nbsp;&nbsp;&nbsp;';
      input += '<select name="move_category" id="moveCategorySelect">';
      input += '<option>请选择目标分类</option>';
      $.each(data, function(i,item){
        input += '<option value="'+item['id']+'">'+item['name']+'</option>';
      });
      input += '</select>';
      $('#moveCategory').after(input);
      $('#moveCategory').unbind();
      $("#moveCategorySelect").change(function(){
        var categoryId = $(this).val();
        moveCategory(categoryId);
        $(this).children("option:first").attr('selected', 'selected');
        return false;
      });
    });
  });
});

/**
 * 批量删除
 * @param url
 * @return
 */
function deleteAll(url, ids){
  var idstr = ids.join(',');
  if (!idstr) {
    alert("请选择要删除的记录");
    return false;
  }
  if (!confirm("确定要删除这些记录吗？")) {
    return false;
  }
  
	var postData = { 'id': idstr };
	$.post(url, postData, function(json){
	  if (json.info != 'ok') {
	    alert(json.info);
	  } else {
	    $.each(ids, function(k, v){
	      $("#dataList"+v).fadeOut(function(){
	        $(this).remove();
	      });
	    });
	    interLineColor();
	  }
	}, 'json');
}

/**
 * 为了兼容原有后台程序移植过来的 将来将要被废除
 * 单个删除
 * @param url
 * @param id
 * @return
 */
function del_one(url,id){
  if(!confirm(prompt.prompt)) return false;
  var sendata = {id:id, 'res_name':res_name};
  if (typeof(predefineParam) != 'undefined') {
    $.extend(sendata, predefineParam);
  }
  $.getJSON(url, sendata,function(data){
    if(data.info == 'ok'){
      var text = 'list_'+id;
      if (typeof(predefineFun) == 'undefined') {
        $("#"+text).fadeOut();
      } else {
        $.extend({ybo:predefineFun});
        $.ybo(text);
      }
    }else{
      alert(data.info);
      return false;
    }
  });
}
/**
 * 为了兼容原有后台程序移植过来的 将来将要被废除
 * 批量删除
 * @param url
 * @return
 */
function del(url){
  if(!confirm(prompt.prompt)) return false;
  var ids = '';
  $(".select_s input:checked").each(function(){
    var id = $(this).closest("tr").attr("id").substring(5);
    ids += id+',' ;
  });
  if(!ids){
    alert(prompt.nochange);
    return false;
  }
  var sendata = {id:ids};
  if (typeof(predefineParam) != 'undefined') {
    $.extend(sendata, predefineParam);
  }
  $.getJSON(url, sendata, function(json){
    if (json.info == 'ok'){
        var $obj = $(".select_s input:checked").parents("tr");
        if (typeof(predefineFun) == 'undefined') {
          $obj.fadeOut();
        } else {
          $obj.each(function(i, n){
            var jobj = $(n);
            $.extend({ybo:predefineFun});
            $.ybo(jobj.attr("id"));
          });
        }
      $(".selectAll").attr("checked",false);
    } else {
      alert(prompt.errors);
    }
  });
}

/**
 * 批量转移分类
 * @param categoryId 分类id
 * @require 更新对应class="category"的分类td的内容
 * @TODO 与user_list中的审核功能合并抽取
 * @author gaojj@alltosun.com
 */
function moveCategory(categoryId)
{
	if(!confirm('确定要批量转移吗?')) return false;

	var id = getCheckedId();
    if (!id) {
        alert("你没有选择要操作的记录");
        return false;
    }

	var url = site_url + '/admin/category/move';
    var data = {'category_id': categoryId, 'id[]': id};
    $.getJSON(url, data, function(json){
        if (json.info != 'ok') {
            alert(json.info);
            return;
        }

        var categoryName = json.category_name;

        var articleCategorySelectors = [];

        $.each(id, function(k, v){
        	// 更新对应class="category"的分类td的内容
            articleCategorySelectors.push('#list_'+v+' .category');
        });

        var newCategoryhtml = '<a href="admin/article&cat_id='+categoryId+'" target="_blank">'+categoryName+'</a>';
        var articleCategorySelector = articleCategorySelectors.join(',');

        $(articleCategorySelector).html(categoryName).effect("highlight", {}, 300).effect("highlight", {}, 300);
    });
}

/**
 * 获取页面中选中的checkbox对应的ids
 * @requires checkbox上统一加name="listSelect"
 * @requires tr的class="dataList1"
 * @return Array 所有选中的id数组
 */
function getCheckedIds()
{
  var ids = [];
  $("input.listSelect:checked").not(":disabled").each(function(){
    var selectId = $(this).closest("tr").attr("id").substring(8);
    ids.push(selectId);
  });
  return ids;
}

/**
 * 为了兼容原有后台程序移植过来的 将来将要被废除
 * 获取页面中选中的checkbox的值
 * 本方法中获取页面选中的checkbox必须在checkbox上统一加class="listCheck"，并且tr的class="list_1"
 * @return Array 所有选中的id数组
 * @author gaojj@alltosun.com
 */
function getCheckedId()
{
  var id = [];
  // checkbox上统一加class="listSelect"
    $("input.listSelect:checked").each(function(){
      // tr的class="list_1"
        var selectId = $(this).closest("tr").attr("id").substring(8);        
        id.push(selectId);
    });
    return id;
}

/**
 * 表格隔行换色
 * @return
 */
function interLineColor()
{
	$("tr:odd").removeClass("even").addClass("odd");
	$("tr:even").removeClass("odd").addClass("even");
}

/**
 * 是否是中文
 */
function isChinese(str)
{
  return new RegExp("[\\u4e00-\\u9fa5]", "").test(str);
}

// 获取排序的view_order
function getViewOrder()
{
  var viewOrderArr = { };
  var list = $(".dataBox tbody tr");
  var total = list.length;
  $.each(list ,function(viewOrder, v){
    var key = $(this).attr('id').substring(8);
    if (!key) {
      return true;
    }
    viewOrderArr[key] = viewOrder + 1;
  });
  return viewOrderArr;
}

// 判断一个对象是否是同一个对象 (只判断了第一层)
function isSameObj(obj1, obj2)
{
  for(var i in obj1) {
    if (obj1[i] !== obj2[i]) {
      return false;
    }
  }
  for(var j in obj2) {
    if (obj2[j] !== obj1[j]) {
      return false;
    }
  }
  return true;
}

$(function () {
//编辑器采用上传模式 *require resType
  var xheditorUploadUrl =  siteUrl + "/news/handler/file_uploader&source=xheditor&immediate=1&file_field=filedata";
  var myUploadUrl = siteUrl + "/news/handler/file_uploader/my_file_upload";
  var xheditorSettings = {
      height        : 400,
      wordDeepClean : false,
      inlineScript  : true,
      internalScript: true,
      linkTag       : true,
      upImgUrl      : xheditorUploadUrl,
      upImgExt      : "jpg,jpeg,gif,png",
      upFlashUrl    : xheditorUploadUrl,
      upFlashExt    : "swf",
      upMediaUrl    : xheditorUploadUrl,
      upMediaExt    : "flv,avi,mp4",
      upLinkUrl     : myUploadUrl,
      upLinkExt     : "rar,pdf,txt,zip,doc,docx,jpg,jpeg,gif,png"
  };

  $.each($('.xheditor-upload'), function(){
    // 如果定义了id，证明这个xh实例需要被用到，可在xheditorObjs对象中通过下标访问到
    // 如 xheditorObjs['newsContent']
    var id = $(this).attr('id');
    if ( id ) {
      xheditorObjs[id] = $(this).xheditor(xheditorSettings);
    } else {
      $(this).xheditor(xheditorSettings);
    }
  });
  
  /**
   * 编码URL
   */
  $('#searchForm').submit(function(e){
    e.preventDefault();
    var params = $(this).serialize();
    var href = $(this).attr('action');
    href += '&' + params;
    window.location.href = href;
  });
  // 单个删除
  $(".deleteOnes").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    if (!confirm("确定要删除该条记录吗？")) {
      return false;
    }

    var clickObj = $(this);
    var url = clickObj.attr("href");
    var lo_url = $(this).attr("lo_url");
    $.post(url, {}, function(json){
      if (json.info != 'ok') {
        alert(json.info);
      } else {
          window.location.assign(lo_url);
      }
    }, 'json');
    return false;
  });
});

function autoassign(id,url){
    var cache = {};
    $( "#"+id ).autocomplete({
        minLength: 1,
        source: function( request, response ) {
            var term = request.term;
            if ( term in cache ) {
                response( cache[ term ] );
                return;
          }

        $.getJSON(url, request, function( data, status, xhr ) {
            cache[ term ] = data;
            response( data );
        });
     }
    });
}

