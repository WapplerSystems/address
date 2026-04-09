<?php

namespace WapplerSystems\Address\Controller;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use WapplerSystems\Address\Domain\Model\Dto\EmConfiguration;

/**
 *
 */
class AddressBaseController extends ActionController
{

    protected function initializeView($view)
    {
        $view->assign('contentObjectData', $this->request->getAttribute('currentContentObject')->data);
        $view->assign('emConfiguration', GeneralUtility::makeInstance(EmConfiguration::class));
        $pageInformation = $this->request->getAttribute('frontend.page.information');
        if ($pageInformation !== null) {
            $view->assign('pageData', $pageInformation->getPageRecord());
        }
    }


    /**
     * Error handling if no address entry is found
     *
     * @param string $configuration configuration what will be done
     * @throws \InvalidArgumentException
     */
    protected function handleNoAddressFoundError(string $configuration): ?ResponseInterface
    {
        if (empty($configuration)) {
            return null;
        }

        $configuration = trim($configuration);
        if (str_starts_with($configuration, 'redirect')) {
            $parts = explode(',', $configuration);
            $url = $parts[1] ?? '';
            $statusCode = (int)($parts[2] ?? 301);
            if (!empty($url)) {
                return $this->responseFactory->createResponse($statusCode)
                    ->withHeader('Location', $url);
            }
        } elseif (str_starts_with($configuration, 'pageNotFoundHandler')) {
            $errorController = GeneralUtility::makeInstance(ErrorController::class);
            $response = $errorController->pageNotFoundAction(
                $this->request,
                'Address not found'
            );
            throw new ImmediateResponseException($response);
        } else {
                $statusCode = (int)$configuration;
                if ($statusCode === 0) {
                    $statusCode = 404;
                }
                $standaloneTemplate = GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
                $standaloneTemplate->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($configuration));
                return $this->responseFactory->createResponse($statusCode)
                    ->withHeader('Content-Type', 'text/html; charset=utf-8')
                    ->withBody($this->streamFactory->createStream($standaloneTemplate->render()));
        }
        return null;
    }

}