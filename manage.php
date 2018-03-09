<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaTestimonial = $iaCore->factoryModule('testimonial', IA_CURRENT_MODULE);

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

        $body_len = utf8_strlen(trim(strip_tags($item['body_' . $iaCore->language['iso']])));

        $len = array(
            'min' => $iaCore->get('testimonials_min_len'),
            'max' => $iaCore->get('testimonials_max_len')
        ); // min and max message length

        if ($len['min'] > $body_len || $len['max'] < $body_len) {
            $error = true;
            $messages[] = iaLanguage::getf('testimon_body_len', array('num' => $len['min'] . '-' . $len['max']));
        }

        if ($error) {
            $listing = $item;

            $iaView->setMessages($messages);
        } else {
            $item['date'] = date(iaDb::DATE_FORMAT);
            $item['status'] = $iaCore->get('testimonials_approve') ? iaCore::STATUS_ACTIVE : iaCore::STATUS_INACTIVE;

            $item['id'] = $iaDb->insert($item);

            $iaView->setMessages(iaLanguage::get('testimonials_added'), iaView::SUCCESS);

            iaUtil::go_to(IA_URL . 'testimonials/');
        }
        $iaView->setMessages($messages);
    }

    iaBreadcrumb::replaceEnd(iaLanguage::get('add_testimonial'), IA_URL . 'testimonials/add/');

    $iaView->title(iaLanguage::get('add_testimonial'));
    $iaView->assign('sections', $sections);

    $iaView->display('manage');
}

$iaDb->resetTable();
