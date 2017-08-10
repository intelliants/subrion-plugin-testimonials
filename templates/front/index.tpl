{ia_add_media files='css: _IA_URL_modules/testimonials/templates/front/css/style'}

{if isset($testimonial)}
    <div class="testimonial-view">
        <div class="testimonial-view__user text-center m-b">
            {ia_image file=$testimonial.avatar width=100 height=100 class='img-circle img-responsive' alt=$testimonial.name gravatar=true}
            <p><b>{$testimonial.name|escape:'html'}</b> {lang key='on'} {$testimonial.date|date_format:$core.config.date_format}</p>
        </div>

        <div class="testimonial-view__text m-b">
            {$testimonial.body|html_entity_decode:2:"UTF-8"}
        </div>

        {if isset($testimonial.reply)}
            <div class="testimonial-view__reply m-b m-l p-l">
                <h4>{lang key='admin_reply'}</h4>
                {$testimonial.reply}
            </div>
        {/if}
    </div>
{elseif $testimonials}
    <div class="slogan m-b-md">{lang key='testimonial_slogan'}</div>
    <div class="ia-items testimonials-list">
        {foreach $testimonials as $testimonial}
            <div class="media ia-item ia-item--border">
                <div class="pull-left testimonials-list__avatar">
                    {ia_image file=$testimonial.avatar type="thumbnail" width=100 class='media-object' gravatar=true}
                </div>

                <div class="media-body">
                    <div class="ia-item-body">{$testimonial.body|html_entity_decode:2:"UTF-8"|truncate:$core.config.testimonials_max_page:"... <a href='{$smarty.const.IA_URL}testimonials/{$testimonial.id}/' class='testimonials-list__more'>read more<span class='fa fa-long-arrow-right'></span></a>":true}</div>
                </div>
                <div class="ia-item-panel clearfix">
                    <span class="panel-item panel-item--name pull-left">{$testimonial.name|escape:'html'}</span>
                    <span class="panel-item pull-right">
                        <span class="fa fa-calendar"></span> {$testimonial.date|date_format:$core.config.date_format}
                    </span>
                </div>
            </div>
        {/foreach}
    </div>
    {navigation aTotal=$pagination.total aTemplate=$pagination.template aItemsPerPage=$pagination.limit aNumPageItems=5}
{else}
    <div class="alert alert-info">{lang key='no_testimonials_yet'}</div>
{/if}
