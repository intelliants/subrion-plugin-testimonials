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
class iaBackendController extends iaAbstractControllerModuleBackend
{
    protected $_name = 'testimonials';

    protected $_itemName = 'testimonials';

//	protected $_gridColumns = '`id`, `name`, `email`, `url`, `body`, IF(`reply` IS NULL, 0, 1) `replied`, `date`, `status`, `lang`, 1 `update`, 1 `delete`';
    protected $_gridColumns = ['id', 'name', 'email', 'body', 'status', 'date'];
    protected $_gridFilters = array('status' => self::EQUAL);

    protected $_phraseAddSuccess = 'testimonials_added';


    public function init()
    {
        $this->_template = 'index';
    }

    protected function _indexPage(&$iaView)
    {
        $iaView->grid('_IA_URL_modules/' . $this->getModuleName() . '/js/admin/index');
    }

    protected function _modifyGridParams(&$conditions, &$values, array $params)
    {
        if (!empty($params['text'])) {
            $conditions[] = '(`name` LIKE :text OR `email` LIKE :text OR `url` LIKE :text OR `body` LIKE :text)';
            $values['text'] = '%' . iaSanitize::sql($params['text']) . '%';
        }
    }

    protected function _setDefaultValues(array &$entry)
    {
        $entry['date'] = date(iaDb::DATE_FORMAT);
        $entry['status'] = iaCore::STATUS_ACTIVE;
    }

    protected function _entryAdd(array $entryData)
    {
        $entryData['date'] = date(iaDb::DATE_FORMAT);

        return parent::_entryAdd($entryData);
    }

    protected function _entryDelete($id)
    {
        $row = $this->getById($id);
        $result = parent::_entryDelete($id);

        if ($result && $row) {
            empty($row['avatar'])
            || $this->_iaCore->factory('field')->deleteUploadedFile('avatar', $this->getTable(), $id, $row['avatar']);
        }

        return $result;
    }

    protected function _preSaveEntry(array &$entry, array $data, $action)
    {
        parent::_preSaveEntry($entry, $data, $action);

        iaUtil::loadUTF8Functions();

        if (!empty($entry['url']) && !iaValidate::isUrl($entry['url'])) {
            if (iaValidate::isUrl($entry['url'], false)) {
                $entry['url'] = 'http://' . $entry['url'];

            } else {
                $this->addMessage('error_url');
            }
        }

        $len = array(
            'min' => $this->_iaCore->get('testimonials_min_len'),
            'max' => $this->_iaCore->get('testimonials_max_len')
        );

        // TODO: check for multilingual
        $body_len = utf8_strlen(trim(strip_tags($entry['body_' . $this->_iaCore->language['iso']])));

        if ($body_len < $len['min'] || $body_len > $len['max']) {
            $this->addMessage(iaLanguage::getf('testimon_body_len', array('num' => $len['min'] . '-' . $len['max'])),
                false);
        }

        if (!empty($entry['url']) && !iaValidate::isUrl($entry['url'])) {
            $this->addMessage('error_url');
        }

//		if (!$this->getMessages() && isset($_FILES['avatar']['error']) && !$_FILES['avatar']['error'])
//		{
//			try
//			{
//				$iaField = $this->_iaCore->factory('field');
//
//				$path = $iaField->uploadImage($_FILES['avatar'], null, null, 100, 100, 'crop', 'testimonials', 'photo_');
//
//				empty($entry['avatar']) || $iaField->deleteUploadedFile('avatar', $this->getTable(), $this->getEntryId(), $entry['avatar']);
//				$entry['avatar'] = $path;
//			}
//			catch (Exception $e)
//			{
//				$this->addMessage($e->getMessage(), false);
//			}
//		}
        return !$this->getMessages();
    }

    protected function _postSaveEntry(array &$entry, array $data, $action)
    {
        if (iaCore::ACTION_DELETE == $action) {
            $this->_iaCore->factory('log')->write(iaLog::ACTION_DELETE,
                array('item' => 'testimonial', 'name' => $entry['name'], 'id' => (int)$this->getEntryId()));
        }
    }
}