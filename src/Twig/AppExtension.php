<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('preg_replace', [$this, '_preg_replace']),
        ];
    }

    public function _preg_replace($subject, $pattern, $replacement='', $limit=-1)
	{
		if (!isset($subject)) {
			return null;
		}
		else {
			return preg_replace($pattern, $replacement, $subject, $limit);
		}
	}
}