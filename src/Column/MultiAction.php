<?php declare(strict_types = 1);

namespace Ublaboo\DataGrid\Column;

use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;
use Ublaboo\DataGrid\Row;
use Ublaboo\DataGrid\Traits\TButtonCaret;
use Ublaboo\DataGrid\Traits\TButtonClass;
use Ublaboo\DataGrid\Traits\TButtonIcon;
use Ublaboo\DataGrid\Traits\TButtonText;
use Ublaboo\DataGrid\Traits\TButtonTitle;
use Ublaboo\DataGrid\Traits\TButtonTryAddIcon;
use Ublaboo\DataGrid\Traits\TLink;

class MultiAction extends Column
{

	use TButtonTryAddIcon;
	use TButtonIcon;
	use TButtonClass;
	use TButtonTitle;
	use TButtonText;
	use TButtonCaret;
	use TLink;

	/** @var DataGrid */
	protected $grid;

	/** @var string */
	protected $name;

	/** @var array */
	protected $actions = [];

	/** @var callable[] */
	private $rowConditions = [];

	public function __construct(DataGrid $grid, string $name)
	{
		$this->grid = $grid;
		$this->name = $name;

		$this->setTemplate(__DIR__ . '/../templates/column_multi_action.latte');
	}


	public function renderButton(): Html
	{
		$button = Html::el('button')
			->type('button')
			->data('toggle', 'dropdown');

		$this->tryAddIcon($button, $this->getIcon(), $this->getName());

		if (!empty($this->attributes)) {
			$button->addAttributes($this->attributes);
		}

		$button->addText($this->grid->getTranslator()->translate($this->name));

		if ($this->hasCaret()) {
			$button->addHtml('&nbsp;');
			$button->addHtml('<i class="caret"></i>');
		}

		if ($this->getTitle()) {
			$button->title($this->grid->getTranslator()->translate($this->getTitle()));
		}

		if ($this->getClass()) {
			$button->class($this->getClass() . ' dropdown-toggle');
		}

		return $button;
	}


	public function addAction(
		string $key,
		string $name,
		?string $href = null,
		?array $params = null
	): self
	{
		if (isset($this->actions[$key])) {
			throw new DataGridException(
				"There is already action at key [$key] defined for MultiAction."
			);
		}

		$href = $href ?: $key;

		if ($params === null) {
			$params = [$this->grid->getPrimaryKey()];
		}

		$action = new Action($this->grid, $href, $name, $params);

		$action->setClass('');

		$this->actions[$key] = $action;

		return $this;
	}


	/**
	 * @return Action[]
	 */
	public function getActions(): array
	{
		return $this->actions;
	}


	public function getAction(string $key): Action
	{
		if (!isset($this->actions[$key])) {
			throw new DataGridException(
				"There is no action at key [$key] defined for MultiAction."
			);
		}

		return $this->actions[$key];
	}


	/**
	 * Column can have variables that will be passed to custom template scope
	 */
	public function getTemplateVariables(): array
	{
		return array_merge($this->template_variables, [
			'multi_action' => $this,
		]);
	}


	public function setRowCondition(
		string $actionKey,
		callable $rowCondition
	): void
	{
		$this->rowConditions[$actionKey] = $rowCondition;
	}


	public function testRowCondition(string $actionKey, Row $row): bool
	{
		if (!isset($this->rowConditions[$actionKey])) {
			return true;
		}

		return (bool) call_user_func($this->rowConditions[$actionKey], $row->getItem());
	}

}
