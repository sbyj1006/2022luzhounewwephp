define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'userdk/userdk/index',
                    add_url: '',
                    edit_url: 'userdk/userdk/edit',
                    del_url: 'userdk/userdk/del',
                    import_url: 'userdk/userdk/import',
                    multi_url: 'userdk/userdk/multi',
                    table: 'userdk',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                //禁用默认搜索
                search: true,
                //启用普通表单搜索
                commonSearch: true,
                //可以控制是否默认显示搜索单表,false则隐藏,默认为false
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true},

                        {field: 'nickname', title: __('nickname')},
                        {field: 'jobnumber', title: __('jobnumber'), operate: 'LIKE'},
                        {field: 'daikuanOrderNumber', title: __('daikuanOrderNumber'), operate: 'LIKE'},
                        {field: 'all_daikuanOrderNumber', title: __('all_daikuanOrderNumber'), operate: 'LIKE'},
                        {field: 'amount', title: __('amount'), operate: 'LIKE'},
                        {field: 'all_amount', title: __('all_amount'), operate: 'LIKE'},
                       {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});