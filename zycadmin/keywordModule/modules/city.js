/**

 @Name：layuiAdmin 主页示例
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：GPL-2

 */


layui.define(function (exports) {
    var $ = layui.$;
    layui.use('table', function () {
        var table = layui.table;

        table.render({
            elem: '#city_table'
            //, width: 725
            , url: 'city_list.php?dopost=ajax'
            , cols: [[
                {field: 'id', title: 'ID'}
                , {field: 'name', title: '城市名'}
                , {field: 'pinyin', title: '拼音'}
                , {field: 'banner_id', title: '轮播栏目id'}
                , {field: 'news_id', title: '新闻栏目id'}
                , {field: 'product_id', title: '产品栏目id'}
                , {
                    field: 'is_master', title: '是否主站', templet: function (res) {
                        return res.is_master == 1 ? "<span style='color: red;'>是</span>" : '否';
                    }
                }
                , {
                    field: 'engines', width: 180, title: '操作', templet: function (res) {
                        return '<a href="city_do.php?dopost=info&id='+res.id+'" style="color: #3366FF">编辑</a>';
                    }
                }
            ]]
            , page: true
            , limit : 30
        });
        $('#sub_btn').click(function(){
            if($("input[name='name']").val() == ''){
                layer.alert('请输入城市名称',{icon:2});
            }
            if($("input[name='pinyin']").val() == ''){
                layer.alert('请输入城市拼音',{icon:2});
            }
            if($("input[name='banner_id']").val() == ''){
                layer.alert('请输入轮播栏目id',{icon:2});
            }
            if($("input[name='product']").val() == ''){
                layer.alert('请输入产品栏目id',{icon:2});
            }
            if($("input[name='news_id']").val() == ''){
                layer.alert('请输入新闻栏目id',{icon:2});
            }
            $('#theForm').submit();
        })
    });

});