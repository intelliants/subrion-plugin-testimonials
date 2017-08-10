<form method="post" enctype="multipart/form-data" id="testimonials" class="ia-form add-testimonial">
    {preventCsrf}

    {include 'item-view-tabs.tpl'}

    {include 'captcha.tpl'}

    <div>
        <input type="hidden" name="save" value="save">
        <button type="submit" class="btn btn-primary" name="save">{lang key='send'}</button>
    </div>

</form>