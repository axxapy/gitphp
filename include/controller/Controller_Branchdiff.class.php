<?php
/**
 * Controller for displaying a branchdiff
 *
 * @author Alexey Manukhin <axxapy@gmail.com>
 * @copyright Copyright (c) 2010 Christopher Han
 * @package GitPHP
 * @subpackage Controller
 */
class GitPHP_Controller_Branchdiff extends GitPHP_Controller_DiffBase
{
	/**
	 * Gets the template for this controller
	 *
	 * @return string template filename
	 */
	protected function GetTemplate() {
		if ($this->Plain()) {
			return 'branchdiffplain.tpl';
		}
		return 'branchdiff.tpl';
	}

	/**
	 * Gets the cache key for this controller
	 *
	 * @return string cache key
	 */
	protected function GetCacheKey() {
		$key = (isset($this->params['branch']) ? $this->params['branch'] : '')
			. '|' . (isset($this->params['branchparent']) ? $this->params['branchparent'] : '')
			. '|' . (isset($this->params['sidebyside']) && ($this->params['sidebyside'] === true) ? '1' : '');

		return $key;
	}

	/**
	 * Gets the name of this controller's action
	 *
	 * @param boolean $local true if caller wants the localized action name
	 * @return string action name
	 */
	public function GetName($local = false) {
		if ($local && $this->resource) {
			return $this->resource->translate('branchdiff');
		}
		return 'branchdiff';
	}

	/**
	 * Loads data for this template
	 */
	protected function LoadData() {
		$Commit = $this->getProject()->GetHeadList()->GetHead($this->params['branch'])->GetCommit();
		$this->tpl->assign('commit', $Commit);
		$this->tpl->assign('branch', $this->params['branch']);
		$refs = $this->GetProject()->GetHeadList()->GetRefsList();
		array_map(function($key) use (&$refs) {
			$refs[$key] = $key;
		}, array_keys($refs));
		ksort($refs);
		$this->tpl->assign('branch_list', $refs);

		if (isset($this->params['sidebyside']) && ($this->params['sidebyside'] === true)) {
			$this->tpl->assign('sidebyside', true);
		}

		$from_branch = isset($this->params['branchparent']) ? $this->params['branchparent'] : 'master';
		$this->tpl->assign("branchparent", $from_branch);

		$head = $Commit->GetHash();
		#$from_hash = $this->GetProject()->GetHeadList()->GetHead($from_branch)->GetHash();

		$treediff = new GitPHP_TreeDiff($this->GetProject(), $this->exe, $head, $from_branch);
		$this->tpl->assign('treediff', $treediff);
	}
}