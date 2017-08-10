Ext.onReady(function () {
    if (Ext.get('js-grid-placeholder')) {
        var grid = new IntelliGrid(
            {
                columns: [
                    'selection',
                    'expander',
                    {name: 'name', title: _t('name'), width: 2},
                    {name: 'email', title: _t('email'), width: 250},
                    'status',
                    {name: 'date', title: _t('date'), width: 120, editor: 'date'},
                    'update',
                    'delete'
                ],
                sorters: [{property: 'date', direction: 'DESC'}],
                expanderTemplate: '{body}',
                fields: ['body']
            }, false);

        grid.toolbar = Ext.create('Ext.Toolbar', {
            items: [
                {
                    emptyText: _t('text'),
                    name: 'text',
                    listeners: intelli.gridHelper.listener.specialKey,
                    width: 275,
                    xtype: 'textfield'
                }, {
                    displayField: 'title',
                    editable: false,
                    emptyText: _t('status'),
                    id: 'fltStatus',
                    name: 'status',
                    store: grid.stores.statuses,
                    typeAhead: true,
                    valueField: 'value',
                    xtype: 'combo'
                }, {
                    handler: function () {
                        intelli.gridHelper.search(grid);
                    },
                    id: 'fltBtn',
                    text: '<i class="i-search"></i> ' + _t('search')
                }, {
                    handler: function () {
                        intelli.gridHelper.search(grid, true);
                    },
                    text: '<i class="i-close"></i> ' + _t('reset')
                }]
        });

        grid.init();
    }
});
