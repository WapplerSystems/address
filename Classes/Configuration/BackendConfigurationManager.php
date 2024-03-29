<?php
declare(strict_types = 1);

namespace WapplerSystems\Address\Configuration;


use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateRepository;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateTreeBuilder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Traverser\ConditionVerdictAwareIncludeTreeTraverser;
use TYPO3\CMS\Core\TypoScript\Tokenizer\LossyTokenizer;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;

class BackendConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager
{

    public function __construct(
        private readonly TypoScriptService $typoScriptService,
        private readonly PhpFrontend $typoScriptCache,
        private readonly FrontendInterface $runtimeCache,
        private readonly SysTemplateRepository $sysTemplateRepository,
        private readonly SysTemplateTreeBuilder $treeBuilder,
        private readonly LossyTokenizer $lossyTokenizer,
        private readonly ConditionVerdictAwareIncludeTreeTraverser $includeTreeTraverserConditionVerdictAware,
        private readonly SiteFinder $siteFinder,
    ) {

        // extract page id from returnUrl GET parameter
        if (isset($_GET['returnUrl'])) {
            $url = parse_url($_GET['returnUrl']);
            parse_str($url['query'] ?? '',$params);
            $pageId = $params['id'] ?? -1;
            if ($pageId !== -1) $this->currentPageId = (int)$pageId;
        }

        parent::__construct($typoScriptService, $typoScriptCache, $runtimeCache, $sysTemplateRepository, $treeBuilder, $lossyTokenizer, $includeTreeTraverserConditionVerdictAware, $siteFinder);

    }


}
