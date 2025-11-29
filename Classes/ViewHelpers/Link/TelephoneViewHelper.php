<?php

namespace WapplerSystems\Address\ViewHelpers\Link;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;


class TelephoneViewHelper extends AbstractTagBasedViewHelper
{

    protected $tagName = 'a';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('number', 'string', 'Telephone number', true);
    }

    public function render(): string
    {
        $number = $this->arguments['number'] ?? '';
        if ($number === '' || !str_starts_with($number, '+')) {
            return (string)$this->renderChildren();
        }

        $number = str_replace('(0)', '', $number);
        // Remove any non-numeric characters from the number
        $number = preg_replace('/[^\d+]/', '', $number);

        $this->tag->addAttribute('href', 'tel:' . $number);
        $this->tag->setContent((string)$this->renderChildren());
        $this->tag->forceClosingTag(true);
        return $this->tag->render();
    }

}
