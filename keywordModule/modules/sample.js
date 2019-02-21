/**

 @Name：layuiAdmin 主页示例
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：GPL-2

 */


layui.define(function (exports) {
    var admin = layui.admin;
    var $ = layui.$

    //组合关键词选项代码
    function make_html(word) {
        return '<div class="layui-unselect layui-form-checkbox" lay-skin="primary"><span>'+word+'</span><i class="layui-icon layui-icon-ok" layadmin-event="checkWord"></i></div>';
    }
    function insert_words(word) {
        $('#select_words div').each(function(idx,item){
            if($(item).find('p').text() == word){
                $(item).remove();
            }
        })
        var html_str = '<div style="display: none;"><p>'+word+'<input type="hidden" name="keywords[]" value="'+word+'"></p><i class="layui-icon">&#x1006;</i></div>';
        $('#select_words').prepend(html_str);
        $('#select_words div').eq(0).fadeIn();
    }
    $('body').on('click','#select_words div i',function(){
        $(this).parent('div').remove();
    })
    //组合关键词全选
    admin.events.selectAll = function (obj) {
        $('#word-box').find('.layui-form-checkbox').addClass('layui-form-checked');
        $('#word-box').find('.layui-form-checkbox').fadeOut();
        setTimeout(function(){
            $('#word-box .layui-form-checkbox').each(function(idx,item){
                insert_words($(item).find('span').text());
                $(item).remove();
            })
        },200);
    };
    //组合关键词单选
    admin.events.checkWord = function (obj) {
        $(obj).parent().addClass('layui-form-checked');
        $(obj).parent().fadeOut();
        insert_words($(obj).prev().text());
        setTimeout(function(){
            $(obj).prev().parent().remove();
        },200);
    };
    admin.events.buildWord = function (obj) {
        var mian_word = $('#my-layer #main-word').val().trim();
        if (mian_word == '') {
            layer.msg('关键词不能为空', {icon: 5});
            return false;
        }
        var prefix_word = $('#my-layer #prefix-word').val().trim();
        var suffix_word = $('#my-layer #suffix-word').val().trim();
        if (prefix_word == '' && suffix_word == '') {
            layer.msg('词头跟词尾不能都为空', {icon: 5});
            return false;
        }
        var mian_arr = mian_word.split('，');
        var prefix_arr = prefix_word.split('，');
        var suffix_arr = suffix_word.split('，');
        var html_str = '';
        if (prefix_arr != '') {
            $.each(mian_arr, function (idx, item) {
                $.each(prefix_arr, function (i, v) {
                    html_str += make_html(v + item);
                })
            })
        }
        if (suffix_arr != '') {
            $.each(mian_arr, function (idx, item) {
                $.each(suffix_arr, function (i, v) {
                    html_str += make_html(item + v);
                })
            })
        }
        if (suffix_arr != '' && prefix_arr != '') {
            $.each(prefix_arr, function (idx, item) {
                $.each(mian_arr, function (i, val) {
                    $.each(suffix_arr, function (x, v) {
                        html_str += make_html(item + val + v);
                    })
                })
            })
        }
        $('#word-box').html(html_str);
        $('#first_box').animate({left:'-100%'});
        $('#second_box').animate({left:'1%'});
        layer.closeAll();
    }
    //组合关键词返回到直接输入关键词
    admin.events.back = function (obj) {
        $('#first_box').animate({left:'1%'});
        $('#second_box').animate({left:'100%'});
    };
    //文本框键入关键词
    admin.events.addWord = function (obj) {
        var words_arr = $('#text_words').val().split('，');
        var html_str = '';
        $.each(words_arr,function(idx,item){
            html_str += insert_words(item);
        })
    };
    //自定义关键词弹框
    admin.events.diyWord = function (obj) {
        layer.open({
            type: 1,
            title: '自定义组合词',
            area: ['440px', '470px'], //宽高
            content: $('#my-box').html(),
        });
    };
});