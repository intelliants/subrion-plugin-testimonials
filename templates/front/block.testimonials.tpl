{if !empty($block_testimonials)}
	<div class="ia-items block-block_testimonials">
		{foreach $block_testimonials as $one_testimonials}
		<div class="media ia-item ia-item-bordered-bottom">
			<div class="media-body">
				<p class="ia-item-body">{$one_testimonials.body|truncate:$core.config.testimonials_max:"..."}</p>
				<p class="ia-item-date"><i class="icon-user"></i> {$one_testimonials.name}</p>
			</div>
		</div>
		{/foreach}

		<div class="ia-items-panel">
			<a class="btn btn-info btn-mini" href="{$smarty.const.IA_URL}testimonials/">{lang key='read_more'}</a>
			<a class="btn btn-info btn-mini" href="{$smarty.const.IA_URL}testimonials/add/">{lang key='add_yours'}</a>
		</div>
	</div>
{/if}