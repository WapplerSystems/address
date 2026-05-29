<?php
declare(strict_types=1);

namespace WapplerSystems\Address\ViewHelpers;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileType;
use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use WapplerSystems\Address\Domain\Model\Address;
use WapplerSystems\Address\Domain\Model\FileReference as AddressFileReference;

/**
 * Replaces `[media]` tokens in rich-text content with the attached media
 * files of an Address record, one file per token in document order.
 *
 * Output is unescaped on purpose (escapeOutput = false) because the body
 * itself is rendered HTML, but every dynamic attribute value goes through
 * TagBuilder, which html-encodes attribute values via htmlspecialchars.
 * Inline text contents are explicitly htmlspecialchars'd before being
 * passed to setContent.
 */
class RenderMediaViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;
    protected $escapingInterceptorEnabled = false;

    private string $mediaTag = '/\\[media\\]/';
    private string $replaceMediaTag = '/(?:<p>\s*)?\\[media\\](?:\s*<\/p>)?/';

    private string $imgClass = '';
    private string $videoClass = '';
    private string $audioClass = '';

    public function initializeArguments(): void
    {
        $this->registerArgument('address', 'object', 'the address post', true);
        $this->registerArgument('imgClass', 'string', 'add css class to images', false, '');
        $this->registerArgument('videoClass', 'string', 'wrap videos in a div with this class', false, '');
        $this->registerArgument('audioClass', 'string', 'wrap audio files in a div with this class', false, '');
    }

    public function render(): string
    {
        $address = $this->arguments['address'];
        if (!$address instanceof Address) {
            return (string)$this->renderChildren();
        }

        $this->imgClass = (string)$this->arguments['imgClass'];
        $this->videoClass = (string)$this->arguments['videoClass'];
        $this->audioClass = (string)$this->arguments['audioClass'];

        $mediaFiles = (array)$address->getMediaNonPreviews();
        $content = (string)$this->renderChildren();
        return $this->renderMedia($content, $mediaFiles);
    }

    /**
     * Walks the [media] tokens in $content and replaces each with the next
     * attached file's renderer output. When more tokens than files exist,
     * the surplus tokens are left as literal text rather than swallowed —
     * matches the historical behaviour and keeps editors aware that they
     * referenced a file that isn't there.
     *
     * @param array<int,AddressFileReference|null> $files
     */
    private function renderMedia(string $content, array $files): string
    {
        $fileIndex = 0;
        preg_match_all($this->mediaTag, $content, $matches);
        foreach ($matches[0] as $_) {
            if (!isset($files[$fileIndex])) {
                break;
            }
            $file = $files[$fileIndex++];
            if ($file === null) {
                break;
            }

            $media = $file->getOriginalResource();
            $renderer = GeneralUtility::makeInstance(RendererRegistry::class)->getRenderer($media);

            if ($renderer !== null) {
                $mediaTag = $renderer->render($media, 0, 0);
                $wrapClass = match ($media->getType()) {
                    FileType::VIDEO => $this->videoClass,
                    FileType::AUDIO => $this->audioClass,
                    default => '',
                };
                if ($wrapClass !== '') {
                    $wrap = new TagBuilder('div');
                    $wrap->addAttribute('class', $wrapClass);
                    // setContent passes through without re-encoding — the renderer
                    // output is trusted HTML (TYPO3 core renderers).
                    $wrap->setContent($mediaTag);
                    $wrap->forceClosingTag(true);
                    $mediaTag = $wrap->render();
                }
            } else {
                $mediaTag = $this->renderImage($media);
            }

            $content = preg_replace($this->replaceMediaTag, $mediaTag, $content, 1) ?? $content;
        }
        return $content;
    }

    /**
     * Fallback renderer for images that have no registered MediaRenderer.
     * Builds a `<figure><img …/><figcaption>…</figcaption></figure>` with
     * all attribute values quoted through TagBuilder.
     */
    private function renderImage(FileInterface $image): string
    {
        $imageService = GeneralUtility::makeInstance(ImageService::class);

        // `getProperty()` may return null on freshly imported files; cast
        // upfront so trim() doesn't get a TypeError on PHP 8.1+.
        $crop = $image->getProperty('crop');
        $processedImage = $imageService->applyProcessingInstructions($image, [
            'width' => null,
            'height' => null,
            'crop' => $crop,
        ]);
        $imageUri = $imageService->getImageUri($processedImage);

        $alt = trim((string)$image->getProperty('alternative'));
        $title = trim((string)$image->getProperty('title'));
        $description = trim((string)$image->getProperty('description'));

        $tag = new TagBuilder('img');
        $tag->addAttribute('src', $imageUri);
        $tag->addAttribute('alt', $alt !== '' ? $alt : $title);
        if ($title !== '') {
            $tag->addAttribute('title', $title);
        }
        if ($this->imgClass !== '') {
            $tag->addAttribute('class', $this->imgClass);
        }

        $figcaption = '';
        if ($description !== '') {
            $caption = new TagBuilder('figcaption');
            // setContent does not encode — encode here so a description
            // containing `<script>…` is rendered as text, not executed.
            $caption->setContent(htmlspecialchars($description, ENT_QUOTES | ENT_HTML5));
            $caption->forceClosingTag(true);
            $figcaption = $caption->render();
        }

        return '<figure>' . $tag->render() . $figcaption . '</figure>';
    }
}
