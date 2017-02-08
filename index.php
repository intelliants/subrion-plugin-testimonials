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

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaDb->setTable('testimonials');

	if (iaCore::ACTION_ADD == $pageAction)
	{
		$errors = array();

		if (isset($_POST['body']))
		{
			iaUtil::loadUTF8Functions();

			$data = array(
				'url' => $_POST['url'],
				'name' => $_POST['name'],
				'email' => $_POST['email'],
				'status' => $iaCore->get('testimonials_approve') ? iaCore::STATUS_ACTIVE : iaCore::STATUS_INACTIVE,
				'lang' => $iaView->language,
				'date' => date(iaDb::DATE_FORMAT),
				'body' => $_POST['body']
			);

			$body_len = utf8_strlen($data['body']);

			if (empty($data['name']))
			{
				$errors[] = iaLanguage::get('incorrect_fullname');
			}

			$len = array('min' => $iaCore->get('testimonials_min_len'), 'max' => $iaCore->get('testimonials_max_len')); // min and max message length
			if ($len['min'] > $body_len || $len['max'] < $body_len)
			{
				$errors[] = iaLanguage::getf('testimon_body_len', array('num' => $len['min'] . '-' . $len['max']));
			}

			if (!iaUsers::hasIdentity() && !iaValidate::isCaptchaValid())
			{
				$errors[] = iaLanguage::get('confirmation_code_incorrect');
			}

			if ($data['url'] != '' && !iaValidate::isUrl($data['url']))
			{
				$errors[] = iaLanguage::get('error_url');
			}

			if (!iaValidate::isEmail($data['email']))
			{
				$errors[] = iaLanguage::get('error_email_incorrect');
			}

			if (!$errors && isset($_FILES['avatar']['error']) && !$_FILES['avatar']['error'])
			{
				try
				{
					$data['avatar'] = $iaCore->factory('field')->uploadImage($_FILES['avatar'], null, null,
						100, 100, 'crop', 'testimonials', 'photo_');
				}
				catch (Exception $e)
				{
					$errors[] = $e->getMessage();
				}
			}

			if (!$errors)
			{
				$iaDb->insert($data);
				$iaView->setMessages(iaLanguage::get('testimonials_added'), iaView::SUCCESS);

				iaUtil::go_to(IA_URL . 'testimonials/');
			}

			$iaView->setMessages($errors);
		}

		iaBreadcrumb::replaceEnd(iaLanguage::get('add_testimonial'), IA_URL . 'testimonials/add/');

		$iaView->title(iaLanguage::get('add_testimonial'));
	}
	else
	{
		if (isset($iaCore->requestPath[0]))
		{
			$id = (int)$iaCore->requestPath[0];

			if (!$id)
			{
				return iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}

			$testimonialEntry = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($id));

			if (empty($testimonialEntry))
			{
				return iaView::errorPage(iaView::ERROR_NOT_FOUND);
			}

			$openGraph = array(
				'title' => 'Testimonial: ' . $testimonialEntry['name'],
				'url' => IA_SELF,
				'description' => $testimonialEntry['body']
			);
			if ($testimonialEntry['avatar'])
			{
				$openGraph['image'] = IA_CLEAR_URL . 'uploads/' . $testimonialEntry['avatar'];
			}
			$iaView->set('og', $openGraph);

			iaBreadcrumb::toEnd($testimonialEntry['name'], IA_SELF);
			$iaView->assign('testimonial', $testimonialEntry);
		}
		else
		{
			iaLanguage::set('no_testimonials_yet', iaLanguage::get('no_testimonials_yet', array('url' => IA_URL)));

			$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
			$page = ($page < 1) ? 1 : $page;

			$limit = (int)$iaCore->get('testimonials_num_on_page');

			$pagination = array(
				'start' => ($page - 1) * $limit,
				'limit' => $limit,
				'template' => IA_URL . 'testimonials/?page={page}'
			);

			$entries = $iaDb->all('SQL_CALC_FOUND_ROWS *', "`status`='active' AND `lang`='" . IA_LANGUAGE . "' AND `date` <= NOW() ORDER BY `date` DESC",
				$pagination['start'], $pagination['limit']);
			$pagination['total'] = $iaDb->foundRows();

			$iaView->assign('testimonials', $entries);
			$iaView->assign('pagination', $pagination);
		}
	}

	$iaDb->resetTable();

	$iaView->display('index');
}