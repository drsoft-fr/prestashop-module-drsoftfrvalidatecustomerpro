<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Controller\Hook;

use DrSoftFr\Module\ValidateCustomerPro\Config;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\AbstractHookController;
use DrSoftFr\PrestaShopModuleHelper\Controller\Hook\HookControllerInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;
use Throwable;

/**
 * Class ActionListMailThemesController
 *
 * Handles the action of listing mail themes.
 */
final class ActionListMailThemesController extends AbstractHookController implements HookControllerInterface
{
    /**
     * This function is used to add/remove layout to the theme's collection.
     *
     * @param ThemeCollectionInterface $themes
     */
    private function addLayoutToCollection(ThemeCollectionInterface $themes)
    {
        $layouts = [
            'admin_pending_account',
            'pending_account',
            'validate_account',
        ];

        /** @var ThemeInterface $theme */
        foreach ($themes as $theme) {
            if (!in_array($theme->getName(), ['classic', 'modern'])) {
                continue;
            }

            $htmlPath = '@Modules' . DIRECTORY_SEPARATOR;
            $htmlPath .= $this->module->name . DIRECTORY_SEPARATOR;
            $htmlPath .= 'mails' . DIRECTORY_SEPARATOR;
            $htmlPath .= $theme->getName() === 'classic' ? 'classic' : 'modern';
            $htmlPath .= DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR;
            $extension = '.html.twig';

            foreach ($layouts as $layout) {
                $theme
                    ->getLayouts()
                    ->add(
                        new Layout(
                            $layout,
                            $htmlPath . $layout . $extension,
                            '',
                            $this->module->name
                        )
                    );
            }
        }
    }

    /**
     * Checks if mail themes property is empty and throws an exception if it is.
     *
     * @return bool Returns true if mail themes property is not empty.
     */
    private function checkData(): bool
    {
        if (empty($this->props['mailThemes'])) {
            throw new Exception('Mail themes is empty.');
        }

        return true;
    }

    /**
     * Handles an exception by logging an error message.
     *
     * @param Throwable $t The exception to handle.
     *
     * @return void
     */
    private function handleException(Throwable $t): void
    {
        $errorMessage = Config::createErrorMessage(__METHOD__, __LINE__, $t);

        $this->logger->error($errorMessage, [
            'error_code' => $t->getCode(),
            'object_type' => null,
            'object_id' => null,
            'allow_duplicate' => false,
        ]);
    }

    /**
     * Runs the method.
     *
     * This method checks the data and adds a layout to the theme collection,
     * if the data is valid.
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $this->checkData();

            /** @var ThemeCollectionInterface $themes */
            $themes = $this->props['mailThemes'];

            $this->addLayoutToCollection($themes);
        } catch (Throwable $t) {
            $this->handleException($t);
        }
    }
}
