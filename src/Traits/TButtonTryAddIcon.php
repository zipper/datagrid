<?php declare(strict_types = 1);

namespace Ublaboo\DataGrid\Traits;

use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;

trait TButtonTryAddIcon
{

	public function tryAddIcon(Html $el, ?string $icon, string $name): void
	{
		if ($icon) {
			$el->addHtml(Html::el('span')->class(DataGrid::$iconPrefix . $icon));

			if (mb_strlen($name) > 1) {
				$el->addHtml('&nbsp;');
			}
		}
	}

}
