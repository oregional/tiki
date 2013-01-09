<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Tracker_Controller
{
	private $utilities;

	function setUp()
	{
		global $prefs;
		$this->utilities = new Services_Tracker_Utilities;

		Services_Exception_Disabled::check('feature_trackers');
	}

	function action_add_field($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$trklib = TikiLib::lib('trk');
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$name = $input->name->text();
		$permName = $input->permName->word();
		$type = $input->type->text();
		$description = $input->description->text();
		$wikiparse = $input->description_parse->int();
		$adminOnly = $input->adminOnly->int();
		$fieldId = 0;

		$types = $this->utilities->getFieldTypes();

		if (empty($type)) {
			$type = 't';
		}

		if (! isset($types[$type])) {
			throw new Services_Exception(tr('Type does not exist'), 400);
		}

		if ($input->type->word()) {
			if (empty($name)) {
				throw new Services_Exception_MissingValue('name');
			}

			if ($definition->getFieldFromName($name)) {
				throw new Services_Exception_DuplicateValue('name', $name);
			}

			if ($definition->getFieldFromPermName($permName)) {
				throw new Services_Exception_DuplicateValue('permName', $permName);
			}

			$fieldId = $this->utilities->createField(
				array(
					'trackerId' => $trackerId,
					'name' => $name,
					'permName' => $permName,
					'type' => $type,
					'description' => $description,
					'descriptionIsParsed' => $wikiparse,
					'isHidden' => $adminOnly ? 'y' : 'n',
				)
			);

			if ($input->submit_and_edit->none() || $input->next->word() === 'edit') {
				return array(
					'FORWARD' => array(
						'action' => 'edit_field',
						'fieldId' => $fieldId,
						'trackerId' => $trackerId,
					),
				);
			}
		}

		return array(
			'trackerId' => $trackerId,
			'fieldId' => $fieldId,
			'name' => $name,
			'permName' => $permName,
			'type' => $type,
			'types' => $types,
			'description' => $description,
			'descriptionIsParsed' => $wikiparse,
		);
	}

	function action_list_fields($input)
	{
		$trackerId = $input->trackerId->int();
		$perms = Perms::get('tracker', $trackerId);

		if (! $perms->view_trackers) {
			throw new Services_Exception_Denied(tr('Not allowed to view the tracker'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$fields = $definition->getFields();
		$types = $this->utilities->getFieldTypes();
		$typesDisabled = array();

		if ($perms->admin_trackers) {
			$typesDisabled = $this->utilities->getFieldTypesDisabled();
		}

		$missing = array();

		foreach ($fields as $field) {
			if (! array_key_exists($field['type'], $types) && ! in_array($field['type'], $missing)) {
				$missing[] = $field['type'];
			}
		}
		if (!empty($missing)) {
			TikiLib::lib('errorreport')->report(tr('Warning: Required field types not enabled: %0', implode(', ', $missing)));
		}

		return array(
			'fields' => $fields,
			'types' => $types,
			'typesDisabled' => $typesDisabled
		);
	}

	function action_save_fields($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$hasList = false;
		$hasLink = false;

		$fields = array();
		foreach ($input->field as $key => $value) {
			$fieldId = (int) $key;
			$isMain = $value->isMain->int();
			$isTblVisible = $value->isTblVisible->int();

			$fields[$fieldId] = array(
				'position' => $value->position->int(),
				'isTblVisible' => $isTblVisible ? 'y' : 'n',
				'isMain' => $isMain ? 'y' : 'n',
				'isSearchable' => $value->isSearchable->int() ? 'y' : 'n',
				'isPublic' => $value->isPublic->int() ? 'y' : 'n',
				'isMandatory' => $value->isMandatory->int() ? 'y' : 'n',
			);

			$this->utilities->updateField($trackerId, $fieldId, $fields[$fieldId]);

			$hasList = $hasList || $isTblVisible;
			$hasLink = $hasLink || $isMain;
		}

		$errorreport = TikiLib::lib('errorreport');
		if (! $hasList) {
			$errorreport->report(tr('Tracker contains no listed field, no meaningful information will be provided in the default list.'));
		}

		if (! $hasLink) {
			$errorreport->report(tr('Tracker contains no field in the title, no link will be generated.'));
		}

		$errorreport->send_headers();

		return array(
			'fields' => $fields,
		);
	}

	function action_edit_field($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$fieldId = $input->fieldId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$field = $definition->getField($fieldId);
		if (! $field) {
			throw new Services_Exception_NotFound;
		}

		$types = $this->utilities->getFieldTypes();
		$typeInfo = $types[$field['type']];

		$permName = $input->permName->word();
		if ($field['permName'] != $permName) {
			if ($definition->getFieldFromPermName($permName)) {
				throw new Services_Exception_DuplicateValue('permName', $permName);
			}
		}

		if ($input->name->text()) {
			$input->replaceFilters(
				array(
					'visible_by' => 'groupname',
					'editable_by' => 'groupname',
				)
			);
			$visibleBy = $input->asArray('visible_by', ',');
			$editableBy = $input->asArray('editable_by', ',');
			$this->utilities->updateField(
				$trackerId,
				$fieldId,
				array(
					'name' => $input->name->text(),
					'description' => $input->description->text(),
					'descriptionIsParsed' => $input->description_parse->int() ? 'y' : 'n',
					'options' => $this->utilities->buildOptions($input->option, $typeInfo),
					'validation' => $input->validation_type->word(),
					'validationParam' => $input->validation_parameter->none(),
					'validationMessage' => $input->validation_message->text(),
					'isMultilingual' => $input->multilingual->int() ? 'y' : 'n',
					'visibleBy' => array_filter(array_map('trim', $visibleBy)),
					'editableBy' => array_filter(array_map('trim', $editableBy)),
					'isHidden' => $input->visibility->alpha(),
					'errorMsg' => $input->error_message->text(),
					'permName' => $permName,
				)
			);
		}

		return array(
			'field' => $field,
			'info' => $typeInfo,
			'options' => $this->utilities->parseOptions($field['options_array'], $typeInfo),
			'validation_types' => array(
				'' => tr('None'),
				'captcha' => tr('Captcha'),
				'distinct' => tr('Distinct'),
				'pagename' => tr('Page Name'),
				'password' => tr('Password'),
				'regex' => tr('Regular Expression (Pattern)'),
				'username' => tr('User Name'),
			),
		);
	}

	function action_remove_fields($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$fields = $input->fields->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		foreach ($fields as $fieldId) {
			if (! $definition->getField($fieldId)) {
				throw new Services_Exception_NotFound;
			}
		}

		if ($input->confirm->int()) {
			$trklib = TikiLib::lib('trk');
			foreach ($fields as $fieldId) {
				$trklib->remove_tracker_field($fieldId, $trackerId);
			}

			return array(
				'status' => 'DONE',
				'trackerId' => $trackerId,
				'fields' => $fields,
			);
		} else {
			return array(
				'trackerId' => $trackerId,
				'fields' => $fields,
			);
		}
	}

	function action_export_fields($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$fields = $input->fields->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if ($fields) {
			$fields = $this->utilities->getFieldsFromIds($definition, $fields);
		} else {
			$fields = $definition->getFields();
		}

		$data = "";
		foreach ($fields as $field) {
			$data .= $this->utilities->exportField($field);
		}

		return array(
			'trackerId' => $trackerId,
			'fields' => $fields,
			'export' => $data,
		);
	}

	function action_import_fields($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$raw = $input->raw->none();
		$preserve = $input->preserve_ids->int();

		$data = TikiLib::lib('tiki')->read_raw($raw);

		if (! $data) {
			throw new Services_Exception(tr('Invalid data provided'), 400);
		}

		foreach ($data as $info) {
			$this->utilities->importField($trackerId, new JitFilter($info), $preserve);
		}

		return array(
			'trackerId' => $trackerId,
		);
	}

	function action_list_trackers($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$trklib = TikiLib::lib('trk');
		return $trklib->list_trackers();
	}

	function action_list_items($input)
	{
		// TODO : Eventually, this method should filter according to the actual permissions, but because
		//        it is only to be used for tracker sync at this time, admin privileges are just fine.

		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$trackerId = $input->trackerId->int();
		$offset = $input->offset->int();
		$maxRecords = $input->maxRecords->int();
		$status = $input->status->word();
		$format = $input->format->word();
		$modifiedSince = $input->modifiedSince->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$items = $this->utilities->getItems(
			array(
				'trackerId' => $trackerId,
				'status' => $status,
				'modifiedSince' => $modifiedSince,
			),
			$maxRecords,
			$offset
		);

		if ($format !== 'raw') {
			foreach ($items as & $item) {
				$item = $this->utilities->processValues($definition, $item);
			}
		}

		return array(
			'trackerId' => $trackerId,
			'offset' => $offset,
			'maxRecords' => $maxRecords,
			'result' => $items,
		);
	}

	function action_get_item_inputs($input)
	{
		$trackerId = $input->trackerId->int();
		$trackerName = $input->trackerName->string();
		$itemId = $input->itemId->int();
		$byName = $input->byName->bool();
		$defaults = $input->defaults->array();

		$this->trackerNameAndId($trackerId, $trackerName);

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::newItem($trackerId);

		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		$query = Tracker_Query::tracker($byName ? $trackerName : $trackerId)
			->itemId($itemId);

		if ($input > 0) $query->byName();
		if (!empty($defaults)) $query->inputDefaults($defaults);

		$inputs = $query
			->queryInput();

		return $inputs;
	}

	function action_clone_item($input)
	{
		global $prefs;

		Services_Exception_Disabled::check('tracker_clone_item');

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$itemId = $input->itemId->int();
		if (! $itemId ) {
			throw new Services_Exception_Denied(tr('No item to clone'));
		}

		$itemObject = Tracker_Item::fromId($itemId);

		if (! $itemObject->canView()) {
			throw new Services_Exception_Denied(tr('Item to clone not visible'));
		}

		$newItem = Tracker_Item::newItem($trackerId);

		if (! $newItem->canModify()) {
			throw new Services_Exception_Denied(tr('Not allowed to create new items'));
		}

		$itemObject->asNew();
		$itemData = $itemObject->getData($input);

		$id = 0;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$transaction = TikiLib::lib('tiki')->begin();

			$id = $this->utilities->insertItem($definition, $itemData);

			$itemObject = Tracker_Item::fromId($id);

			$trklib = TikiLib::lib('trk');
			foreach ($trklib->get_child_items($itemId) as $info) {
				$childItem = Tracker_Item::fromId($info['itemId']);

				if ($childItem->canView()) {
					$childItem->asNew();
					$data = $childItem->getData();
					$data['fields'][$info['field']] = $id;

					$new = $this->utilities->insertItem($childItem->getDefinition(), $data);
				}
			}

			$transaction->commit();
		}

		return array(
			'trackerId' => $trackerId,
			'itemId' => $itemId,
			'created' => $id,
			'data' => $itemData['fields'],
			'fields' => $itemObject->prepareInput(new JitFilter(array())),
		);
	}

	function action_insert_item($input)
	{
		$processedFields = array();

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::newItem($trackerId);

		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		$fields = $input->fields->none();
		$forced = $input->forced->none();

		if (empty($fields)) {
			$toRemove = array();
			$processedFields = $itemObject->prepareInput($input);

			$fields = array();
			foreach ($processedFields as $k => $f) {
				$permName = $f['permName'];
				$fields[$permName] = $f['value'];

				if (isset($forced[$permName])) {
					$toRemove[$permName] = $k;
				}
			}

			foreach ($toRemove as $permName => $key) {
				unset($fields[$permName]);
				unset($processedFields[$key]);
			}
		} else {
			$out = array();
			foreach ($fields as $key => $value) {
				if ($itemObject->canModifyField($key)) {
					$out[$key] = $value;
				}
			}
			$fields = $out;
		}

		$itemId = 0;
		if (! empty($fields) && $_SERVER['REQUEST_METHOD'] == 'POST') {
			foreach ($forced as $key => $value) {
				if ($itemObject->canModifyField($key)) {
					$fields[$key] = $value;
				}
			}

			$itemId = $this->utilities->insertItem(
				$definition,
				array(
					'status' => $input->status->word(),
					'fields' => $fields,
				)
			);

			if ($itemId) {
				TikiLib::lib('unifiedsearch')->processUpdateQueue();

				return $this->utilities->getItem($trackerId, $itemId);
			} else {
				throw new Services_Exception(tr('Item could not be created.'), 400);
			}
		}

		return array(
			'trackerId' => $trackerId,
			'itemId' => $itemId,
			'fields' => $processedFields,
			'forced' => $forced,
		);
	}

	function action_update_item($input)
	{
		$processedFields = array();

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$itemInfo = TikiLib::lib('trk')->get_tracker_item($itemId);
		if (! $itemInfo || $itemInfo['trackerId'] != $trackerId) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		$processedFields = $itemObject->prepareInput($input);

		$fields = array();
		foreach ($processedFields as $k => $f) {
			$permName = $f['permName'];
			$fields[$permName] = $f['value'];
		}

		$userInput = $input->fields->none();
		if (! empty($userInput)) {
			foreach ($userInput as $key => $value) {
				if ($itemObject->canModifyField($key)) {
					$fields[$key] = $value;
				}
			}
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$result = $this->utilities->updateItem(
				$definition,
				array(
					'itemId' => $itemId,
					'status' => $input->status->word(),
					'fields' => $fields,
				)
			);

			if (false === $result) {
				throw new Services_Exception(tr('Validation error'), 406);
			}

			TikiLib::lib('unifiedsearch')->processUpdateQueue();
		}

		return array(
			'trackerId' => $trackerId,
			'itemId' => $itemId,
			'fields' => $processedFields,
		);
	}

	function action_fetch_item_field($input)
	{
		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (! $field = $definition->getField($input->fieldId->int())) {
			throw new Services_Exception_NotFound;
		}

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$itemInfo = TikiLib::lib('trk')->get_tracker_item($itemId);
		if (! $itemInfo || $itemInfo['trackerId'] != $trackerId) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $processed = $itemObject->prepareFieldInput($field, $input->none())) {
			throw new Services_Exception_Denied;
		}

		return array(
			'field' => $processed,
		);
	}

	function action_set_location($input)
	{
		$location = $input->location->text();

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$itemInfo = TikiLib::lib('trk')->get_tracker_item($itemId);
		if (! $itemInfo) {
			throw new Services_Exception_NotFound;
		}

		$trackerId = $itemInfo['trackerId'];
		$definition = Tracker_Definition::get($trackerId);
		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $itemObject->canModify()) {
			throw new Services_Exception_Denied;
		}

		$field = $definition->getGeolocationField();
		if (! $field) {
			throw new Services_Exception_NotFound;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$field = $definition->getField($field);

			$this->utilities->updateItem(
				$definition,
				array(
					'itemId' => $itemId,
					'status' => $itemInfo['status'],
					'fields' => array(
						$field['permName'] => $location,
					),
				)
			);
			TikiLib::lib('unifiedsearch')->processUpdateQueue();
		}

		return array(
			'trackerId' => $trackerId,
			'itemId' => $itemId,
			'location' => $location,
		);
	}

	function action_remove_item($input)
	{
		$processedFields = array();

		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (! $itemId = $input->itemId->int()) {
			throw new Services_Exception_MissingValue('itemId');
		}

		$itemInfo = TikiLib::lib('trk')->get_tracker_item($itemId);
		if (! $itemInfo || $itemInfo['trackerId'] != $trackerId) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);
		if (! $itemObject->canRemove()) {
			throw new Services_Exception_Denied;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->utilities->removeItem($itemId);
			TikiLib::lib('unifiedsearch')->processUpdateQueue();
		}

		return array(
			'trackerId' => $trackerId,
			'itemId' => $itemId,
		);
	}

	function action_remove($input)
	{
		$trackerId = $input->trackerId->int();
		$confirm = $input->confirm->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if ($confirm) {
			$this->utilities->removeTracker($trackerId);

			return array(
				'trackerId' => 0,
			);
		}

		return array(
			'trackerId' => $trackerId,
			'name' => $definition->getConfiguration('name'),
		);
	}

	function action_clear($input)
	{
		$trackerId = $input->trackerId->int();
		$confirm = $input->confirm->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if ($confirm) {
			$this->utilities->clearTracker($trackerId);

			return array(
				'trackerId' => 0,
			);
		}

		return array(
			'trackerId' => $trackerId,
			'name' => $definition->getConfiguration('name'),
		);
	}

	function action_replace($input)
	{
		$trackerId = $input->trackerId->int();
		$confirm = $input->confirm->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		if ($trackerId) {
			$definition = Tracker_Definition::get($trackerId);

			if (! $definition) {
				throw new Services_Exception_NotFound;
			}
		} else {
			$definition = Tracker_Definition::getDefault();
		}

		$cat_type = 'tracker';
		$cat_objid = $trackerId;

		if ($confirm) {
			$name = $input->name->text();

			if (! $name) {
				throw new Services_Exception_MissingValue('name');
			}

			$data = array(
				'name' => $name,
				'description' => $input->description->text(),
				'descriptionIsParsed' => $input->descriptionIsParsed->int() ? 'y' : 'n',
				'showStatus' => $input->showStatus->int() ? 'y' : 'n',
				'showStatusAdminOnly' => $input->showStatusAdminOnly->int() ? 'y' : 'n',
				'showCreated' => $input->showCreated->int() ? 'y' : 'n',
				'showCreatedView' => $input->showCreatedView->int() ? 'y' : 'n',
				'showCreatedBy' => $input->showCreatedBy->int() ? 'y' : 'n',
				'showCreatedFormat' => $input->showCreatedFormat->text(),
				'showLastModif' => $input->showLastModif->int() ? 'y' : 'n',
				'showLastModifView' => $input->showLastModifView->int() ? 'y' : 'n',
				'showLastModifBy' => $input->showLastModifBy->int() ? 'y' : 'n',
				'showLastModifFormat' => $input->showLastModifFormat->text(),
				'defaultOrderKey' => $input->defaultOrderKey->int(),
				'defaultOrderDir' => $input->defaultOrderDir->word(),
				'doNotShowEmptyField' => $input->doNotShowEmptyField->int() ? 'y' : 'n',
				'showPopup' => $input->showPopup->text(),
				'defaultStatus' => implode('', (array) $input->defaultStatus->word()),
				'newItemStatus' => $input->newItemStatus->word(),
				'modItemStatus' => $input->modItemStatus->word(),
				'outboundEmail' => $input->outboundEmail->email(),
				'simpleEmail' => $input->simpleEmail->int() ? 'y' : 'n',
				'writerCanModify' => $input->writerCanModify->int() ? 'y' : 'n',
				'writerCanRemove' => $input->writerCanRemove->int() ? 'y' : 'n',
				'userCanTakeOwnership' => $input->userCanTakeOwnership->int() ? 'y' : 'n',
				'oneUserItem' => $input->oneUserItem->int() ? 'y' : 'n',
				'writerGroupCanModify' => $input->writerGroupCanModify->int() ? 'y' : 'n',
				'writerGroupCanRemove' => $input->writerGroupCanRemove->int() ? 'y' : 'n',
				'useRatings' => $input->useRatings->int() ? 'y' : 'n',
				'showRatings' => $input->showRatings->int() ? 'y' : 'n',
				'ratingOptions' => $input->ratingOptions->text(),
				'useComments' => $input->useComments->int() ? 'y' : 'n',
				'showComments' => $input->showComments->int() ? 'y' : 'n',
				'showLastComment' => $input->showLastComment->int() ? 'y' : 'n',
				'useAttachments' => $input->useAttachments->int() ? 'y' : 'n',
				'showAttachments' => $input->showAttachments->int() ? 'y' : 'n',
				'orderAttachments' => implode(',', $input->orderAttachments->word()),
				'start' => $input->start->int() ? $this->readDate($input, 'start') : 0,
				'end' => $input->end->int() ? $this->readDate($input, 'end') : 0,
				'autoCreateGroup' => $input->autoCreateGroup->int() ? 'y' : 'n',
				'autoCreateGroupInc' => $input->autoCreateGroupInc->groupname(),
				'autoAssignCreatorGroup' => $input->autoAssignCreatorGroup->int() ? 'y' : 'n',
				'autoAssignCreatorGroupDefault' => $input->autoAssignCreatorGroupDefault->int() ? 'y' : 'n',
				'autoAssignGroupItem' => $input->autoAssignGroupItem->int() ? 'y' : 'n',
				'autoCopyGroup' => $input->autoCopyGroup->int() ? 'y' : 'n',
				'viewItemPretty' => $input->viewItemPretty->text(),
				'editItemPretty' => $input->editItemPretty->text(),
				'autoCreateCategories' => $input->autoCreateCategories->int() ? 'y' : 'n',
				'publishRSS' => $input->publishRSS->int() ? 'y' : 'n',
				'sectionFormat' => $input->sectionFormat->word(),
				'adminOnlyViewEditItem' => $input->adminOnlyViewEditItem->int() ? 'y' : 'n',
			);

			$trackerId = $this->utilities->updateTracker($trackerId, $data);

			$cat_desc = $data['description'];
			$cat_name = $data['name'];
			$cat_href = "tiki-view_tracker.php?trackerId=" . $trackerId;
			$cat_objid = $trackerId;
			include "categorize.php";

			$groupforAlert = $input->groupforAlert->groupname();

			if ($groupforAlert) {
				$groupalertlib = TikiLib::lib('groupalert');
				$showeachuser = $input->showeachuser->int() ? 'y' : 'n';
				$groupalertlib->AddGroup('tracker', $trackerId, $groupforAlert, $showeachuser);
			}

			$definition = Tracker_Definition::get($trackerId);
		}

		include_once ("categorize_list.php");
		$groupalertlib = TikiLib::lib('groupalert');
		$groupforAlert = $groupalertlib->GetGroup('tracker', 'trackerId');
		return array(
			'trackerId' => $trackerId,
			'info' => $definition->getInformation(),
			'statusTypes' => TikiLib::lib('trk')->status_types(),
			'statusList' => preg_split('//', $definition->getConfiguration('defaultStatus', 'o'), -1, PREG_SPLIT_NO_EMPTY),
			'sortFields' => $this->getSortFields($definition),
			'attachmentAttributes' => $this->getAttachmentAttributes($definition->getConfiguration('orderAttachments', 'created,filesize,hits')),
			'startDate' => $this->format($definition->getConfiguration('start'), '%Y-%m-%d'),
			'startTime' => $this->format($definition->getConfiguration('start'), '%H:%M'),
			'endDate' => $this->format($definition->getConfiguration('end'), '%Y-%m-%d'),
			'endTime' => $this->format($definition->getConfiguration('end'), '%H:%M'),
			'groupList' => $this->getGroupList(),
			'groupforAlert' => $groupforAlert,
			'showeachuser' => $groupalertlib->GetShowEachUser('tracker', 'trackerId', $groupforAlert),
		);
	}

	function action_duplicate($input)
	{
		$trackerId = $input->trackerId->int();
		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers || ! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);
		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$name = $input->name->text();

		if (! $name) {
			throw new Services_Exception_MissingValue('name');
		}

		$newId = $this->utilities->duplicateTracker($trackerId, $name, $input->dupCateg->int(), $input->dupPerms->int());

		return array(
			'trackerId' => $newId,
			'name' => $name,
		);
	}

	function action_export($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->export_tracker) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if ($perms->admin_trackers) {
			$info = $definition->getInformation();

			$out = "[TRACKER]\n";

			foreach ($info as $key => $value) {
				if ($key && $value) {
					$out .= "$key = $value\n";
				}
			}
		} else {
			$out = null;
		}

		return array(
			'trackerId' => $trackerId,
			'export' => $out,
			'fields' => $definition->getFields(),
			'recordsMax' => $definition->getConfiguration('items'),
		);
	}

	function action_export_items($input)
	{
		TikiLib::lib('tiki')->allocate_extra(
			'tracker_export_items',
			function () use ($input) {
				$trackerId = $input->trackerId->int();

				$definition = Tracker_Definition::get($trackerId);

				if (! $definition) {
					throw new Services_Exception_NotFound;
				}

				$perms = Perms::get('tracker', $trackerId);
				if (! $perms->export_tracker) {
					throw new Services_Exception_Denied(tr('Not allowed to export'));
				}

				$fields = array();
				foreach ((array) $input->listfields->int() as $fieldId) {
					if ($f = $definition->getField($fieldId)) {
						$fields[$fieldId] = $f;
					}
				}

				if (0 === count($fields)) {
					throw new Services_Exception(tr('No valid field selected for export'), 400);
				}

				$showItemId = $input->showItemId->int();
				$showStatus = $input->showStatus->int();
				$showCreated = $input->showCreated->int();
				$showLastModif = $input->showLastModif->int();
				$keepItemlinkId = $input->keepItemlinkId->int();
				$dateFormatUnixTimestamp = $input->dateFormatUnixTimestamp->int();

				$encoding = $input->encoding->text();
				if (! in_array($encoding, array('UTF-8', 'ISO-8859-1'))) {
					$encoding = 'UTF-8';
				}
				$separator = $input->separator->none();
				$delimitorR = $input->delimitorR->none();
				$delimitorL = $input->delimitorL->none();

				$cr = $input->CR->none();

				$recordsMax = $input->recordsMax->int();
				$recordsOffset = $input->recordsOffset->int() - 1;

				$writeCsv = function ($fields) use($separator, $delimitorL, $delimitorR, $encoding) {
					$values = array();
					foreach ($fields as $v) {
						$values[] = "$delimitorL$v$delimitorR";
					}

					$line = implode($separator, $values);
					$line = str_replace(array("\r\n", "\n", "<br/>", "<br />"), $cr, $line);

					if ($encoding === 'ISO-8859-1') {
						echo utf8_decode($line) . "\n";
					} else {
						echo $line . "\n";
					}
				};

			 	session_write_close();

				$trklib = TikiLib::lib('trk');
				$trklib->write_export_header($encoding, $trackerId);

				$header = array();
				if ($showItemId) {
					$header[] = 'itemId';
				}
				if ($showStatus) {
					$header[] = 'status';
				}
				if ($showCreated) {
					$header[] = 'created';
				}
				if ($showLastModif) {
					$header[] = 'lastModif';
				}
				foreach ($fields as $f) {
					$header[] = $f['name'] . ' -- ' . $f['fieldId'];
				}

				$writeCsv($header);

				$items = $trklib->list_items($trackerId, $recordsOffset, $recordsMax, 'itemId_asc', $fields);

				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_modifier_tiki_short_datetime');
				foreach ($items['data'] as $row) {
					$toDisplay = array();
					if ($showItemId) {
						$toDisplay[] = $row['itemId'];
					}
					if ($showStatus) {
						$toDisplay[] = $row['status'];
					}
					if ($showCreated) {
						if ($dateFormatUnixTimestamp) {
							$toDisplay[] = $row['created'];
						} else {
							$toDisplay[] = smarty_modifier_tiki_short_datetime($row['created'], '', 'n');
						}
					}
					if ($showLastModif) {
						if ($dateFormatUnixTimestamp) {
							$toDisplay[] = $row['lastModif'];
						} else {
							$toDisplay[] = smarty_modifier_tiki_short_datetime($row['lastModif'], '', 'n');
						}
					}
					foreach ($row['field_values'] as $val) {
						if ( ($keepItemlinkId) && ($val['type'] == 'r') ) {
							$toDisplay[] = $val['value'];
						} elseif ( ($dateFormatUnixTimestamp) && ($val['type'] == 'f') ) {
							$toDisplay[] = $val['value'];
						} elseif ( ($dateFormatUnixTimestamp) && ($val['type'] == 'j') ) {
							$toDisplay[] = $val['value'];
						} else {
							$toDisplay[] = $trklib->get_field_handler($val)->renderOutput(array('list_mode' => 'csv'));
						}
					}

					$writeCsv($toDisplay);
				}
			}
		);

		exit;
	}

	function action_dump_items($input)
	{
		$trackerId = $input->trackerId->int();

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->export_tracker) {
			throw new Services_Exception_Denied(tr('Not allowed to export'));
		}

		$trklib = TikiLib::lib('trk');
		$trklib->dump_tracker_csv($trackerId);
		exit;
	}

	function action_export_profile($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$trackerId = $input->trackerId->int();

		$profile = Tiki_Profile::fromString('dummy', '');
		$data = array();
		$profileObject = new Tiki_Profile_Object($data, $profile);
		$profileTrackerInstallHandler = new Tiki_Profile_InstallHandler_Tracker($profileObject, array());

		$export_yaml = $profileTrackerInstallHandler->_export($trackerId, $profileObject);

		include_once 'lib/wiki-plugins/wikiplugin_code.php';
		$export_yaml = wikiplugin_code($export_yaml, array('caption' => 'YAML', 'colors' => 'yaml'));
		$export_yaml = preg_replace('/~[\/]?np~/', '', $export_yaml);

		return array(
			'trackerId' => $trackerId,
			'yaml' => $export_yaml,
		);
	}

	private function trackerName($trackerId)
	{
		return TikiLib::lib('tiki')->table('tiki_trackers')->fetchOne('name', array('trackerId' => $trackerId));
	}

	private function trackerId($trackerName)
	{
		return TikiLib::lib('tiki')->table('tiki_trackers')->fetchOne('trackerId', array('name' => $trackerName));
	}

	private function trackerNameAndId(&$trackerId, &$trackerName)
	{
		if ($trackerId > 0 && empty($trackerName)) {
			$trackerName = $this->trackerName($trackerId);
		} elseif ($trackerId < 1 && !empty($trackerName)) {
			$trackerId = $this->trackerId($trackerName);
		}
	}

	function action_import($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$raw = $input->raw->none();
		$preserve = $input->preserve->int();

		$data = TikiLib::lib('tiki')->read_raw($raw);

		if (! $data || ! isset($data['tracker'])) {
			throw new Services_Exception(tr('Invalid data provided'), 400);
		}

		$data = $data['tracker'];

		$trackerId = 0;
		if ($preserve) {
			$trackerId = (int) $data['trackerId'];
		}

		unset($data['trackerId']);
		$trackerId = $this->utilities->updateTracker($trackerId, $data);

		return array(
			'trackerId' => $trackerId,
			'name' => $data['name'],
		);
	}

	function action_import_items($input)
	{
		$trackerId = $input->trackerId->int();

		$perms = Perms::get('tracker', $trackerId);
		if (! $perms->admin_trackers) {
			throw new Services_Exception_Denied(tr('Reserved to tracker administrators'));
		}

		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception_NotFound;
		}

		if (isset($_FILES['importfile'])) {
			if (! is_uploaded_file($_FILES['importfile']['tmp_name'])) {
				throw new Services_Exception(tr('File upload failed.'), 400);
			}

			if (! $fp = @ fopen($_FILES['importfile']['tmp_name'], "rb")) {
				throw new Services_Exception(tr('Uploaded file could not be read.'), 500);
			}

			$trklib = TikiLib::lib('trk');
			$count = $trklib->import_csv(
				$trackerId,
				$fp,
				($input->add_items->int() !== 1), // checkbox is "Create as new items" - param is replace_rows
				$input->dateFormat->text(),
				$input->encoding->text(),
				$input->separator->text(),
				$input->updateLastModif->int(),
				$input->convertItemLinkValues->int()
			);

			fclose($fp);

			return array(
				'trackerId' => $trackerId,
				'return' => $count,
				'importfile' => $_FILES['importfile']['name'],
			);
		}

		return array(
			'trackerId' => $trackerId,
			'return' => '',
		);
	}

	function action_vote($input)
	{
		$requestData = array();
		$requestData['itemId'] = $input->i->int();
		$requestData['fieldId'] = $input->f->int();
		$requestData['vote'] = 'y';

		$v = $input->v->text();
		if ($v !== 'NULL') {
			$v = $input->v->int();
		}
		$requestData['ins_' . $requestData['fieldId']] = $v;

		$trklib = TikiLib::lib('trk');
		$field = $trklib->get_tracker_field($requestData['fieldId']);

		$handler = $trklib->get_field_handler($field);

		$result = $handler->getFieldData($requestData);

		return array($result);
	}

	public function action_import_profile($input)
	{
		global $tikilib, $access;
		$access->check_permission('tiki_p_admin');
		$transaction = $tikilib->begin();
		$installer = new Tiki_Profile_Installer;

		$yaml = $input->yaml->string();
		$name = "tracker_import:" . md5($yaml);
		$profile = Tiki_Profile::fromString('{CODE(caption="yaml")}' . "\n" . $yaml . "\n" . '{CODE}', $name);

		if ($installer->isInstallable($profile) == true) {
			if ($installer->isInstalled($profile) == true) {
				$installer->forget($profile);
			}

			$installer->install($profile);
			$feedback = $installer->getFeedback();
			$transaction->commit();
			return $feedback;
		} else {
			return false;
		}
	}

	private function getSortFields($definition)
	{
		$sorts = array();

		foreach ($definition->getFields() as $field) {
			$sorts[$field['fieldId']] = $field['name'];
		}

		$sorts[-1] = tr('Last Modification');
		$sorts[-2] = tr('Creation Date');
		$sorts[-3] = tr('Item ID');

		return $sorts;
	}

	private function getAttachmentAttributes($active)
	{
		$active = explode(',', $active);

		$available = array(
			'filename' => tr('Filename'),
			'created' => tr('Creation date'),
			'hits' => tr('Views'),
			'comment' => tr('Comment'),
			'filesize' => tr('File size'),
			'version' => tr('Version'),
			'filetype' => tr('File type'),
			'longdesc' => tr('Long description'),
			'user' => tr('User'),
		);

		$active = array_intersect(array_keys($available), $active);

		$attributes = array_fill_keys($active, null);
		foreach ($available as $key => $label) {
			$attributes[$key] = array('label' => $label, 'selected' => in_array($key, $active));
		}

		return $attributes;
	}

	private function readDate($input, $prefix)
	{
		$date = $input->{$prefix . 'Date'}->text();
		$time = $input->{$prefix . 'Time'}->text();

		if (! $time) {
			$time = '00:00';
		}

		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute) = explode(':', $time);
		$second = 0;

		$tikilib = TikiLib::lib('tiki');
		$tikidate = TikiLib::lib('tikidate');
		$display_tz = $tikilib->get_display_timezone();
		if ( $display_tz == '' ) $display_tz = 'UTC';
		$tikidate->setTZbyID($display_tz);
		$tikidate->setLocalTime($day, $month, $year, $hour, $minute, $second, 0);
		return $tikidate->getTime();
	}

	private function format($date, $format)
	{
		if ($date) {
			return TikiLib::date_format($format, $date);
		}
	}

	private function getGroupList()
	{
		$userlib = TikiLib::lib('user');
		$groups = $userlib->list_all_groupIds();
		$out = array();

		foreach ($groups as $g) {
			$out[] = $g['groupName'];
		}

		return $out;
	}
}

