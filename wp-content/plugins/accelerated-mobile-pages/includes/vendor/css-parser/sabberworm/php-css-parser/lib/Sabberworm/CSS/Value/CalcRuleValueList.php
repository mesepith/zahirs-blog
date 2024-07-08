<?php

namespace Sabberworm\CSS\Value;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class CalcRuleValueList extends RuleValueList {
	public function __construct($iLineNo = 0) {
		parent::__construct(array(), ',', $iLineNo);
	}

	public function render(\Sabberworm\CSS\OutputFormat $oOutputFormat) {
		return $oOutputFormat->implode(' ', $this->aComponents);
	}

}
