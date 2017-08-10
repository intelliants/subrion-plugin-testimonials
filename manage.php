<?php
if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaTestimonial = $iaCore->factoryModule('testimonial', 'testimonials');

    $iaField = $iaCore->factory('field');

    $iaDb->setTable($iaTestimonial::getTable());

    $listing = [];

    $sections = $iaField->getTabs($iaTestimonial->getItemName(), $listing);

    if (isset($_POST['save'])) {

        $error = false;
        $messages = [];

        list($item, $error, $messages) = $iaField->parsePost($iaTestimonial->getItemName(), $listing);

        if (!iaUsers::hasIdentity() && !iaValidate::isCaptchaValid()) {
            $error = true;
            $messages[] = iaLanguage::get('confirmation_code_incorrect');
        }


        $body_len = utf8_strlen($item['body_' . $iaCore->language['iso']]);

        $len = array(
            'min' => $iaCore->get('testimonials_min_len'),
            'max' => $iaCore->get('testimonials_max_len')
        ); // min and max message length

        if ($len['min'] > $body_len || $len['max'] < $body_len) {
            $errors[] = iaLanguage::getf('testimon_body_len', array('num' => $len['min'] . '-' . $len['max']));
        }

        if ($error) {
            $listing = $item;
            $listing['status'] = $_POST['status'];

            $iaView->setMessages($messages);
        } else {
            $item['date'] = date(iaDb::DATE_FORMAT);
            $item['status'] = $iaCore->get('testimonials_approve') ? iaCore::STATUS_ACTIVE : iaCore::STATUS_INACTIVE;

            $item['id'] = $iaDb->insert($item);

            $iaView->setMessages(iaLanguage::get('testimonials_added'), iaView::SUCCESS);

            iaUtil::go_to(IA_URL . 'testimonials/');
        }
        $iaView->setMessages($errors);
    }

    iaBreadcrumb::replaceEnd(iaLanguage::get('add_testimonial'), IA_URL . 'testimonials/add/');

    $iaView->title(iaLanguage::get('add_testimonial'));
    $iaView->assign('sections', $sections);

    $iaView->display('manage');
}

$iaDb->resetTable();