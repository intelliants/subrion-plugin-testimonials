<form action="" method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}

	<div class="wrap-list">
		<div class="wrap-group">
			<div class="wrap-group-heading">
				<h4>{lang key='options'}</h4>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-language">{lang key='language'}</label>
				<div class="col col-lg-4">
					<select name="lang" id="input-language"{if count($core.languages) == 1} disabled="disabled"{/if}>
						{foreach $core.languages as $code => $language}
							<option value="{$code}"{if $testimonials.lang == $code} selected="selected"{/if}>{$language.title}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-name">{lang key='name'}</label>
				<div class="col col-lg-4">
					<input type="text" id="input-name" name="name" size="32" value="{$testimonials.name}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-email">{lang key='email'}</label>
				<div class="col col-lg-4">
					<input type="text" id="input-email" name="email" size="32" value="{$testimonials.email}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-url">{lang key='url'}</label>
				<div class="col col-lg-4">
					<input type="text" id="input-url" name="url" size="32" value="{$testimonials.url}">
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="body">{lang key='body'}</label>
				<div class="col col-lg-8">
					{ia_wysiwyg name="body" value={$testimonials.body|html_entity_decode:2:"UTF-8"}}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="reply">{lang key='reply'}</label>
				<div class="col col-lg-8">
					{ia_wysiwyg name="reply" value=$testimonials.reply}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-image">{lang key='avatar'}</label>
				<div class="col col-lg-4">
					{if isset($testimonials.avatar) && $testimonials.avatar}
						<div class="input-group thumbnail thumbnail-single with-actions">
							<a href="{printImage imgfile=$testimonials.avatar fullimage=true url=true}" rel="ia_lightbox">
								{printImage imgfile=$testimonials.avatar}
							</a>

							<div class="caption">
								<a class="btn btn-small btn-danger" href="javascript:void(0);" title="{lang key='delete'}" onclick="return intelli.admin.removeFile('{$testimonials.avatar}', this, 'testimonials', 'avatar', '{$testimonials.id}')"><i class=" i-remove-sign"></i></a>
							</div>
						</div>
					{/if}

					{ia_html_file name='photo' id='input-image'}
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-date">{lang key='date'}</label>
				<div class="col col-lg-4">
					{assign var='default_date' value=($testimonials.date && !in_array($testimonials.date, array('0000-00-00', '0000-00-00 00:00:00'))) ? {$testimonials.date|escape:'html'} : ''}

					<div class="input-group">
						<input type="text" class="js-datepicker" name="date" id="input-date" value="{$default_date}" data-date-format="YYYY-MM-DD">
						<span class="input-group-addon js-datepicker-toggle"><i class="i-calendar"></i></span>
					</div>
				</div>
			</div>

			<div class="row">
				<label class="col col-lg-2 control-label" for="input-status">{lang key='status'}</label>
				<div class="col col-lg-4">
					<select name="status" id="input-status">
						<option value="active"{if $testimonials.status == 'active'} selected="selected"{/if}>{lang key='active'}</option>
						<option value="inactive"{if $testimonials.status == 'inactive'} selected="selected"{/if}>{lang key='inactive'}</option>
					</select>
				</div>
			</div>
		</div>
		<div class="form-actions inline">
			<input type="submit" name="save" class="btn btn-primary" value="{if iaCore::ACTION_EDIT == $pageAction}{lang key='save_changes'}{else}{lang key='add'}{/if}">
			{include file='goto.tpl'}
		</div>
	</div>
</form>

{ia_add_media files='moment, datepicker'}