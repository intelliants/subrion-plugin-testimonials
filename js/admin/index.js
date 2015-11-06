Ext.onReady(function()
{
	var pageUrl = intelli.config.admin_url + '/testimonials/';

	if (Ext.get('js-grid-placeholder'))
	{
		var urlParam = intelli.urlVal('status');

		intelli.testimonials =
		{
			columns: [
				'selection',
				'expander',
				{name: 'name', title: _t('name'), width: 2},
				{name: 'email', title: _t('email'), width: 250},
				{name: 'replied', title: _t('replied'), width: 76, align: intelli.gridHelper.constants.ALIGN_CENTER, renderer: intelli.gridHelper.renderer.check, editor: Ext.create('Ext.form.ComboBox',
					{
						typeAhead: false,
						editable: false,
						lazyRender: true,
						displayField: 'title',
						valueField: 'value'
					})},
				'status',
				{name: 'date', title: _t('date'), width: 120, editor: 'date'},
				'update',
				'delete'
			],
			sorters: [{property: 'date', direction: 'DESC'}],
			expanderTemplate: '{body}',
			fields: ['body'],
			storeParams: urlParam ? {status: urlParam} : null,
			url: pageUrl
		};
		intelli.testimonials = new IntelliGrid(intelli.testimonials, false);
		intelli.testimonials.toolbar = Ext.create('Ext.Toolbar', {items:[
		{
			emptyText: _t('text'),
			name: 'text',
			listeners: intelli.gridHelper.listener.specialKey,
			width: 275,
			xtype: 'textfield'
		},{
			displayField: 'title',
			editable: false,
			emptyText: _t('status'),
			id: 'fltStatus',
			name: 'status',
			store: intelli.testimonials.stores.statuses,
			typeAhead: true,
			valueField: 'value',
			xtype: 'combo'
		},{
			handler: function(){intelli.gridHelper.search(intelli.testimonials);},
			id: 'fltBtn',
			text: '<i class="i-search"></i> ' + _t('search')
		},{
			handler: function(){intelli.gridHelper.search(intelli.testimonials, true);},
			text: '<i class="i-close"></i> ' + _t('reset')
		}]});

		if (urlParam)
		{
			Ext.getCmp('fltStatus').setValue(urlParam);
		}

		intelli.testimonials.init();
	}
});
