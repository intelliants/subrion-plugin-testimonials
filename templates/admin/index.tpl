<form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}

	<div class="wrap-list">
		<div class="wrap-group">
			<div class="wrap-group-heading">
				<h4>{lang key='options'}</h4>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-name">{lang key='name'}</label>
				<div class="col col-lg-4">
					<input type="text" id="input-name" name="name" size="32" value="{$item.name|escape:'html'}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-email">{lang key='email'}</label>
				<div class="col col-lg-4">
					<input type="text" id="input-email" name="email" size="32" value="{$item.email|escape:'html'}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-url">{lang key='url'}</label>
				<div class="col col-lg-4">
					<input type="text" id="input-url" name="url" size="32" value="{$item.url|escape:'html'}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="body">{lang key='body'}</label>
				<div class="col col-lg-8">
					{ia_wysiwyg name='body' value={$item.body|html_entity_decode:2:"UTF-8"}}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="reply">{lang key='reply'}</label>
				<div class="col col-lg-8">
					{ia_wysiwyg name='reply' value=$item.reply}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-avatar">{lang key='avatar'}</label>
				<div class="col col-lg-4">
					{if !empty($item.avatar)}
						<div class="input-group thumbnail thumbnail-single with-actions">
							<a href="{ia_image file=$item.avatar large=true url=true}" rel="ia_lightbox">
								{ia_image file=$item.avatar}
							</a>

							<div class="caption">
								<a class="btn btn-small btn-danger js-cmd-delete-file" href="#" title="{lang key='delete'}" data-file="{$item.avatar}" data-item="testimonials" data-field="avatar" data-id="{$id}"><i class=" i-remove-sign"></i></a>
							</div>
						</div>
					{/if}

					{ia_html_file name='avatar' id='input-avatar'}
				</div>
			</div>
		</div>

		{capture name='systems' append='fieldset_before'}
		<div class="row">
			<label class="col col-lg-2 control-label" for="input-language">{lang key='language'}</label>
			<div class="col col-lg-4">
				<select name="lang" id="input-language"{if count($core.languages) == 1} disabled{/if}>
					{foreach $core.languages as $code => $language}
						<option value="{$code}"{if $item.lang == $code} selected{/if}>{$language.title|escape:'html'}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="row">
			<label class="col col-lg-2 control-label" for="input-date">{lang key='date'}</label>
			<div class="col col-lg-4">
				{assign var='default_date' value=($item.date && !in_array($item.date, array('0000-00-00', '0000-00-00 00:00:00'))) ? {$item.date|escape:'html'} : ''}

				<div class="input-group">
					<input type="text" class="js-datepicker" name="date" id="input-date" value="{$default_date}" data-date-format="YYYY-MM-DD">
					<span class="input-group-addon js-datepicker-toggle"><i class="i-calendar"></i></span>
				</div>
			</div>
		</div>
		{/capture}

		{include 'fields-system.tpl'}
	</div>
</form>
{ia_add_media files='moment, datepicker'}