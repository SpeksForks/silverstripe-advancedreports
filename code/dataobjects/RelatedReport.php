<?php

/**
 * 
 *
 * @author <marcus@silverstripe.com.au>
 * @license BSD License http://www.silverstripe.org/bsd-license
 */
class RelatedReport extends DataObject  {
	public static $db = array(
		'Title'			=> 'Varchar',
		'Parameters'	=> 'MultiValueField',
		'Sort'			=> 'Int',
	);
	
	public static $has_one = array(
		'CombinedReport'		=> 'CombinedReport',
		'Report'				=> 'AdvancedReport',
	);
	
	public static $summary_fields = array(
		'Report.Title'
	);
	
	public function getCMSFields($params = null) {
		$fields = new FieldSet();
		
		// tabbed or untabbed
		$fields->push(new TabSet("Root", $mainTab = new Tab("Main")));
		$mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));
		
		$reports = array();
		$reportObjs = DataObject::get('AdvancedReport', '"ReportID" = 0');
		if ($reportObjs && $reportObjs->count()) {
			foreach ($reportObjs as $obj) {
				if ($obj instanceof CombinedReport) {
					continue;
				}
				$reports[$obj->ID] = $obj->Title . '(' . $obj->ClassName .')';
			}
		}
		
		$fields->addFieldsToTab('Root.Main', array(
			new DropdownField('ReportID', 'Related report', $reports),
			new KeyValueField('Parameters', 'Parameters to pass to the report'),
		));

		return $fields;
	}
	
		/**
	 * @todo Should canCreate be a static method?
	 *
	 * @param Member $member
	 * @return boolean
	 */
	public function canCreate($member = null) {
		if (!$member) {
			$member = Member::currentUser();
		}
		return Permission::check('ADMIN', 'any', $member) || Permission::check('CMS_ACCESS_AdvancedReportsAdmin', 'any', $member);
	}
	
	public function canView($member = null) {
		return $this->CombinedReport()->canView($member);
	}
	
	public function canEdit($member = null) {
		return $this->CombinedReport()->canEdit($member);
	}
	
	public function canDelete($member = null) {
		return $this->CombinedReport()->canDelete($member);
	}
}
