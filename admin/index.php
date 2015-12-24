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

$iaDb->setTable('testimonials');

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	$iaGrid = $iaCore->factory('grid', iaCore::ADMIN);

	switch ($pageAction)
	{
		case iaCore::ACTION_READ:

			switch ($_GET['get'])
			{
				case 'alias':
					iaUtil::loadUTF8Functions('ascii', 'utf8_to_ascii');

					$title = $_GET['title'];
					utf8_is_ascii($title) || $title = utf8_to_ascii($title);

					$output['url'] = IA_PLUGIN_URL . $iaDb->getNextId() . '-' . iaSanitize::alias($title) . '.html';

					break;

				default:

					$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
					$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
					$order = isset($_GET['sort']) ? " ORDER BY `{$_GET['sort']}` {$_GET['dir']}" : '';
					$where = $values = array();

					if (isset($_GET['status']) && in_array($_GET['status'], array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE)))
					{
						$where[] = '`status` = :status';
						$values['status'] = $_GET['status'];
					}

					if (isset($_GET['text']) && $_GET['text'])
					{
						$where[] = '(`name` LIKE :text OR `email` LIKE :text OR `url` LIKE :text OR `body` LIKE :text)';
						$values['text'] = '%' . $_GET['text'] . '%';
					}

					$where || $where[] = iaDb::EMPTY_CONDITION;

					$where = implode(' OR ', $where);
					$iaDb->bind($where, $values);

					$output = array(
						'total' => $iaDb->one(iaDb::STMT_COUNT_ROWS, $where),
						'data' => $iaDb->all("`id`, `name`, `email`, `url`, `body`, IF(`reply` IS NULL, 0, 1)`replied`, `date`, `status`, `lang`, 1 `update`, 1 `delete`", $where . $order, $start, $limit)
					);
			}

			break;

		case iaCore::ACTION_DELETE:

			$output = $iaGrid->gridDelete($_POST, 'deleted');

			break;

		case iaCore::ACTION_EDIT:
		
			$output = $iaGrid->gridUpdate($_POST);
	}

	if (isset($output))
	{
		$iaView->assign($output);
	}
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (iaCore::ACTION_ADD == $pageAction || iaCore::ACTION_EDIT == $pageAction)
	{
		// iaBreadcrumb::add(iaLanguage::get($pageAction . '_testimonial'), IA_ADMIN_URL . 'testimonials/' . $pageAction);

		$id = 0;
		$testimonials = array(
			'date' => date(iaDb::DATE_FORMAT),
			'lang' => IA_LANGUAGE,
			'status' => iaCore::STATUS_ACTIVE
		);

		if (iaCore::ACTION_EDIT == $pageAction)
		{
			$id = (int)$iaCore->requestPath[0];
			$testimonials = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, "`id` = '{$id}'");
		}

		$iaCore->util();

		$testimonials = array(
			'id' => isset($id) ? $id : 0,
			'name' => iaUtil::checkPostParam('name', $testimonials),
			'lang' => iaUtil::checkPostParam('lang', $testimonials),
			'email' => iaUtil::checkPostParam('email', $testimonials),
			'url' => iaUtil::checkPostParam('url', $testimonials),
			'body' => iaUtil::checkPostParam('body', $testimonials),
			'reply' => iaUtil::checkPostParam('reply', $testimonials) ? iaUtil::checkPostParam('reply', $testimonials) : NULL,
			'avatar' => iaUtil::checkPostParam('avatar', $testimonials),
			'date' => iaUtil::checkPostParam('date', $testimonials),
			'status' => iaUtil::checkPostParam('status', $testimonials)
		);

		if (isset($_POST['save']))
		{
			iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

			$error = false;
			$messages = array();

			$testimonials['status'] = in_array($testimonials['status'], array('active', 'approval')) ? $testimonials['status'] : 'approval';
			$testimonials['lang'] = array_key_exists($testimonials['lang'], $iaCore->languages) ? $testimonials['lang'] : IA_LANGUAGE;
			$testimonials['url'] = !empty($testimonials['url']) && 'http://' != substr($testimonials['url'], 0, 7) ? 'http://' . $testimonials['url'] : $testimonials['url'];
			$testimonials['body'] = iaUtil::safeHTML($testimonials['body']);
			$testimonials['reply'] = iaUtil::safeHTML($testimonials['reply']) ? iaUtil::safeHTML($testimonials['reply']) : NULL;
			$testimonials['avatar'] = iaSanitize::html($testimonials['avatar']);
			$testimonials['name'] = iaSanitize::html($testimonials['name']);
			$testimonials['email'] = iaSanitize::html($testimonials['email']);
			$testimonials['url'] = iaSanitize::html($testimonials['url']);
			$testimonials['date'] = iaSanitize::html($testimonials['date']);
			$len = array('min' => $iaCore->get('testimonials_min_len'), 'max' => $iaCore->get('testimonials_max_len'));
			$body_len = utf8_strlen(trim(strip_tags($testimonials['body'])));

			if (empty($testimonials['name']))
			{
				$error = true;
				$messages[] = iaLanguage::get('incorrect_fullname');
			}

			if ($body_len < $len['min'] || $body_len > $len['max'])
			{
				$error = true;
				$messages[] = iaLanguage::getf('testimon_body_len', array('num' => $len['min'] . '-' . $len['max']));
			}

			if (!empty($testimonials['url']) && !iaValidate::isUrl($testimonials['url']))
			{
				$error = true;
				$messages[] = iaLanguage::get('error_url');
			}

			if (!$error)
			{
				if (isset($_FILES['photo']) && !$testimonials['avatar'])
				{
					$photo = $_FILES['photo'];

					if (!empty($photo['name']) && !in_array($photo['type'], array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png')))
					{
						$error = true;
						$messages[] = iaLanguage::get('unsupported_image_type');
					}

					$iaPicture = $iaCore->factory('picture');
					$tok = 'photo_' . iaUtil::generateToken();

					$imageInfo = array(
						'image_width' => 500,
						'image_height' => 500,
						'resize_mode' => iaPicture::CROP
					);

					$name = $iaPicture->processImage($photo, 'testimonials/', $tok, $imageInfo);
					$testimonials['avatar'] = $name;
				}

				if (iaCore::ACTION_EDIT == $pageAction)
				{

					$testimonials['id'] = $id;
					$iaDb->update($testimonials);
					$messages[] = iaLanguage::get('saved');
				}
				else
				{
					$id = $iaDb->insert($testimonials);
					$messages[] = iaLanguage::get('testimonials_added');
				}

				$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);

				if (isset($_POST['goto']))
				{
					$url = IA_ADMIN_URL . 'testimonials/';
					iaUtil::post_goto(array(
						'add' => $url . 'add/',
						'list' => $url,
						'stay' => $url . 'edit/' . $id . '/',
					));
				}
				else
				{
					iaUtil::go_to(IA_ADMIN_URL . 'testimonials/');
				}
			}

			$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
		}

		$options = array('list' => 'go_to_list', 'add' => 'add_another_one', 'stay' => 'stay_here');
		$iaView->assign('goto', $options);

		$iaView->assign('testimonials', $testimonials);
		$iaView->display('index');
	}
	else
	{
		$iaView->grid('_IA_URL_plugins/testimonials/js/admin/index');
	}
}

$iaDb->resetTable();