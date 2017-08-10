<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2015 Intelliants, LLC <http://www.intelliants.com>
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
 * @link http://www.subrion.org/
 *
 ******************************************************************************/
if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaDb->setTable('testimonials');

    $iaTestimonial = $iaCore->factoryModule('testimonial', 'testimonials');

    if (isset($iaCore->requestPath[0])) {
        $id = (int)$iaCore->requestPath[0];

        if (!$id) {
            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
        }
        $testimonialEntry = $iaTestimonial->getById($id);

        if (empty($testimonialEntry)) {
            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
        }

        $openGraph = array(
            'title' => 'Testimonial: ' . $testimonialEntry['name'],
            'url' => IA_SELF,
            'description' => $testimonialEntry['body']
        );
        if ($image = $testimonialEntry['avatar']) {
            $openGraph['image'] = IA_CLEAR_URL . 'uploads/' . $image['path'] . 'original/' . $image['file'];
        }
        $iaView->set('og', $openGraph);

        iaBreadcrumb::toEnd($testimonialEntry['name'], IA_SELF);
        $iaView->assign('testimonial', $testimonialEntry);
    } else {
        iaLanguage::set('no_testimonials_yet', iaLanguage::get('no_testimonials_yet', array('url' => IA_URL)));

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
        $page = ($page < 1) ? 1 : $page;

        $limit = (int)$iaCore->get('testimonials_num_on_page');

        $pagination = array(
            'start' => ($page - 1) * $limit,
            'limit' => $limit,
            'template' => IA_URL . 'testimonials/?page={page}'
        );

        $entries = $iaTestimonial->get("`date` <= NOW()", $pagination['start'], $pagination['limit']);

        $pagination['total'] = $iaTestimonial->getFoundRows();

        $iaView->assign('testimonials', $entries);
        $iaView->assign('pagination', $pagination);
    }

    $iaDb->resetTable();

    $iaView->display('index');
}