<?php
declare(strict_types=1);

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
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use WapplerSystems\Address\Domain\Model\Dto\EmConfiguration;

class AddressBaseController extends ActionController
{
    /**
     * HTTP redirect status codes accepted by `redirect,<url>,<status>`. Anything
     * else falls back to 301 — keeps editors from accidentally returning
     * non-redirect codes (200, 404, …) with a Location header, which most
     * browsers ignore but proxies sometimes mishandle.
     *
     * @var list<int>
     */
    private const ALLOWED_REDIRECT_STATUS = [301, 302, 303, 307, 308];

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
     * Error handling for the "address not found" case. `$configuration` comes
     * from TypoScript / FlexForm (`settings.detail.errorHandling`) and has
     * three forms:
     *  - `redirect,<url>[,<statusCode>]` — Location-header redirect
     *  - `pageNotFoundHandler`           — delegate to TYPO3's 404 handler
     *  - `<EXT:…/path/to/template.html>` — render a Fluid template with the
     *    optional leading integer interpreted as the HTTP status code
     *
     * @throws \InvalidArgumentException
     */
    protected function handleNoAddressFoundError(string $configuration): ?ResponseInterface
    {
        $configuration = trim($configuration);
        if ($configuration === '') {
            return null;
        }

        if (str_starts_with($configuration, 'redirect')) {
            return $this->buildRedirectResponse($configuration);
        }

        if (str_starts_with($configuration, 'pageNotFoundHandler')) {
            $errorController = GeneralUtility::makeInstance(ErrorController::class);
            $response = $errorController->pageNotFoundAction(
                $this->request,
                'Address not found'
            );
            throw new ImmediateResponseException($response);
        }

        return $this->buildTemplateResponse($configuration);
    }

    /**
     * Builds the redirect response for `redirect,<url>[,<status>]`.
     *
     * Hardening over the historical implementation:
     *  - strips CR/LF/NUL from the URL → blocks header-injection via a
     *    crafted FlexForm value
     *  - rejects `javascript:` / `data:` / `vbscript:` / `file:` schemes —
     *    some legacy browsers execute the URL of a Location header during
     *    history navigation
     *  - passes relative URLs through `sanitizeLocalUrl()`, which returns
     *    "" for protocol-relative tricks like `//evil.example`
     *  - clamps the status code to the standard redirect set; anything else
     *    falls back to 301
     */
    private function buildRedirectResponse(string $configuration): ?ResponseInterface
    {
        $parts = explode(',', $configuration);
        $url = $this->sanitizeRedirectUrl((string)($parts[1] ?? ''));
        $statusCode = $this->clampRedirectStatus((int)($parts[2] ?? 301));
        if ($url === '') {
            // Invalid / unsafe URL — let the caller render its default empty
            // template instead of emitting a broken Location header.
            return null;
        }
        return $this->responseFactory->createResponse($statusCode)
            ->withHeader('Location', $url);
    }

    private function sanitizeRedirectUrl(string $url): string
    {
        // CR/LF/NUL break out of the header. PHP's response object would
        // throw on these too, but stripping early keeps the failure mode
        // predictable (empty URL → no redirect, vs. ImmediateResponseException).
        $url = preg_replace('/[\r\n\0]+/', '', $url) ?? '';
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        if (preg_match('/^\s*(?:javascript|data|vbscript|file)\s*:/i', $url) === 1) {
            return '';
        }
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme === null || $scheme === '' || $scheme === false) {
            // Relative or root-relative — TYPO3 vets it; sanitizeLocalUrl
            // returns "" for protocol-relative (//evil) and absolute external
            // links that an editor pasted by accident.
            return GeneralUtility::sanitizeLocalUrl($url);
        }
        if (!in_array(strtolower((string)$scheme), ['http', 'https'], true)) {
            return '';
        }
        return $url;
    }

    private function clampRedirectStatus(int $status): int
    {
        return in_array($status, self::ALLOWED_REDIRECT_STATUS, true) ? $status : 301;
    }

    /**
     * Renders a Fluid template referenced by an EXT:/fileadmin path. If
     * `$configuration` parses as a positive integer instead, treat it as a
     * standalone HTTP status code with no body — this preserves the historic
     * shorthand `errorHandling = 410`.
     */
    private function buildTemplateResponse(string $configuration): ?ResponseInterface
    {
        $statusCode = (int)$configuration;
        if ($statusCode > 0 && (string)$statusCode === $configuration) {
            return $this->responseFactory->createResponse($statusCode);
        }
        $absoluteFile = GeneralUtility::getFileAbsFileName($configuration);
        if ($absoluteFile === '' || !is_file($absoluteFile)) {
            return null;
        }
        $standaloneTemplate = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneTemplate->setTemplatePathAndFilename($absoluteFile);
        return $this->responseFactory->createResponse($statusCode > 0 ? $statusCode : 404)
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withBody($this->streamFactory->createStream($standaloneTemplate->render()));
    }
}
