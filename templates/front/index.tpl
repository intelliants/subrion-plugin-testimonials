{ia_add_media files='css: _IA_URL_plugins/testimonials/templates/front/css/style'}

{if iaCore::ACTION_ADD == $pageAction}
	<form method="post" enctype="multipart/form-data" action="{$smarty.const.IA_SELF}" id="testimonials" class="ia-form">
		{preventCsrf}

		<div class="fieldset-wrapper">
			<div class="control-group">
				<label class="control-label" for="name">{lang key='fullname'}:</label>
				<input type="text" name="name" id="name" class="text" value="{if isset($smarty.post.name)}{$smarty.post.name|escape:'html'}{/if}" >
			</div>

			<label class="control-label" for="photo">{lang key='avatar'}</label>
			<div class="upload-wrap">
				<div class="input-append">
					<span class="span2 uneditable-input">{lang key='upload_avatar'}</span>
					<span class="add-on">{lang key='browse'}</span>
				</div>
				<input type="file" name="photo" id="photo" class="upload-hidden">
			</div>

			<div class="control-group">
				<label class="control-label" for="url">{lang key='url'}:</label>
				<input type="text" name="url" id="url" class="text" value="{if isset($smarty.post.url)}{$smarty.post.url|escape:'html'}{/if}" placeholder="http://">
			</div>

			<div class="control-group">
				<label class="control-label" for="email">{lang key='email'}:</label>
				<input type="text" name="email" id="email" class="text" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'html'}{/if}">
			</div>

			<div class="control-group">
				<label class="control-label" for="body">{lang key='testimonial_body'}:</label>
				<textarea name="body" id="body" class="input-block-level" rows="5">{if isset($smarty.post.body)}{$smarty.post.body|escape:'html'}{/if}</textarea>
				{ia_add_js}
					jQuery(function($)
					{
						$('#body').dodosTextCounter({$core.config.testimonials_max_len}, {
						counterDisplayElement: 'span',
						counterDisplayClass: 'textcounter_body'
					});

					$('.textcounter_body').addClass('textcounter').wrap('<p class="help-block text-right"></p>').before('{lang key='chars_left'} ');
					});
				{/ia_add_js}

				{ia_print_js files='jquery/plugins/jquery.textcounter'}
			</div>

			{include file='captcha.tpl'}
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">{lang key='send'}</button>
			</div>
		</div>
	</form>
{else}
	{if isset($testimonial)}
		<div class="testimonial-view">
			<div class="testimonial-view__user">
				{if $testimonial.avatar}
					{printImage imgfile=$testimonial.avatar width=100 height=100 class='img-circle'}
				{else}
					<img class="img-circle" src="{$img}no-avatar.png" alt="{$testimonial.name}">
				{/if}

				<p><b>{$testimonial.name}</b> {lang key='on'} {$testimonial.date|date_format:$core.config.date_format}</p>
			</div>

			<div class="testimonial-view__text">
				{$testimonial.body}
			</div>

			{if isset($testimonial.reply)}
				<div class="testimonial-view__reply">
					<h4>{lang key='admin_reply'}</h4>
					{$testimonial.reply}
				</div>
			{/if}
		</div>
	{elseif $testimonials}
		<div class="slogan">{lang key='testimonial_slogan'}</div>
		<div class="ia-items">
			{foreach $testimonials as $testimonial}
				<div class="media ia-item ia-item-bordered">
					<div class="pull-left">
						{if $testimonial.avatar}
							{printImage imgfile=$testimonial.avatar width=100 height=100 class='media-object'}
						{else}
							<img src="{$img}no-avatar.png" alt="{$testimonial.name}">
						{/if}
					</div>

					<div class="media-body">
						<a href='{$smarty.const.IA_URL}testimonials/{$testimonial.id}/'>{$testimonial.name}</a>
						<div class="ia-item-body">{$testimonial.body|truncate:200:"... <a href='{$smarty.const.IA_URL}testimonials/{$testimonial.id}/'>read more</a>":true}</div>
					</div>
					<div class="ia-item-panel">
						<span class="panel-item pull-right">
							<i class="icon-calendar"></i> {$testimonial.date|date_format:$core.config.date_format}
						</span>
					</div>
				</div>
			{/foreach}
		</div>

		{navigation aTotal=$total_testimonials aTemplate=$aTemplate aItemsPerPage=$core.config.testimonials_num_on_page aNumPageItems=5}
	{else}
		<div class="alert alert-info">{lang key='no_testimonials_yet'}</div>
	{/if}
{/if}