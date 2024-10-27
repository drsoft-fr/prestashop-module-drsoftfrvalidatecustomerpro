<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Controller\Admin;

use DrSoftFr\Module\ValidateCustomerPro\Data\Configuration\ValidateCustomerProConfiguration;
use drsoftfrvalidatecustomerpro;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class SettingController.
 *
 * @ModuleActivated(moduleName="drsoftfrvalidatecustomerpro", redirectRoute="admin_module_manage")
 */
final class SettingController extends FrameworkBundleAdminController
{
    const TAB_CLASS_NAME = 'AdminDrSoftFrValidateCustomerProSetting';

    /**
     * Renders the index page of the drSoft.fr ValidateCustomerPro settings.
     *
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_module_manage",
     *     message="Access denied."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $form = $this
            ->getValidateCustomerProFormHandler()
            ->getForm();

        return $this->render('@Modules/drsoftfrvalidatecustomerpro/views/templates/admin/setting/index.html.twig', [
            'enableSidebar' => true,
            'form' => $form->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'module' => $this->getModule(),
        ]);
    }

    /**
     * Reset setting
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_setting_index",
     *     message="You do not have permission to reset this."
     * )
     *
     * @return RedirectResponse
     */
    public function resetAction(): RedirectResponse
    {
        try {
            $this
                ->getValidateCustomerProConfiguration()
                ->initConfiguration();

            $this->addFlash(
                'success',
                $this->trans(
                    'The default setting are reset.',
                    'Modules.Drsoftfrvalidatecustomerpro.Admin'
                )
            );
        } catch (Throwable $t) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot reset the setting. Exception: #%code% - %message%',
                    'Modules.Drsoftfrvalidatecustomerpro.Error',
                    [
                        '%code%' => $t->getCode(),
                        '%message%' => $t->getMessage(),
                    ]
                )
            );
        }

        return $this->redirectToRoute('admin_drsoft_fr_validate_customer_pro_setting_index');
    }

    /**
     * Edit setting
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_setting_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function saveAction(Request $request): Response
    {
        try {
            $handler = $this->getValidateCustomerProFormHandler();

            $form = $handler->getForm();
            $form->handleRequest($request);

            if (!$form->isSubmitted()) {
                return $this->redirectToRoute('admin_drsoft_fr_validate_customer_pro_setting_index');
            }

            if (!$form->isValid()) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'The form is invalid.',
                        'Modules.Drsoftfrvalidatecustomerpro.Error'
                    )
                );

                return $this->redirectToRoute('admin_drsoft_fr_validate_customer_pro_setting_index');
            }

            $errors = $handler->save($form->getData());

            if (!empty($errors)) {
                $this->flashErrors($errors);
            } else {
                $this->addFlash(
                    'success',
                    $this->trans(
                        'Your setting are saved.',
                        'Modules.Drsoftfrvalidatecustomerpro.Success'
                    )
                );
            }

        } catch (Throwable $t) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot save the setting. Throwable: #%code% - %message%',
                    'Modules.Drsoftfrvalidatecustomerpro.Error',
                    [
                        '%code%' => $t->getCode(),
                        '%message%' => $t->getMessage(),
                    ]
                )
            );
        }

        return $this->redirectToRoute('admin_drsoft_fr_validate_customer_pro_setting_index');
    }

    /**
     * @return drsoftfrvalidatecustomerpro|object
     */
    protected function getModule()
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.module');
    }

    /**
     * Get ValidateCustomerPro configuration.
     *
     * @return ValidateCustomerProConfiguration
     */
    protected function getValidateCustomerProConfiguration(): ValidateCustomerProConfiguration
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.data.configuration.setting_configuration');
    }

    /**
     * Get ValidateCustomerPro form handler.
     *
     * @return FormHandlerInterface
     */
    protected function getValidateCustomerProFormHandler(): FormHandlerInterface
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.form.handler.setting_form_handler');
    }
}
