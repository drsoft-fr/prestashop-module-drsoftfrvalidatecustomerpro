<?php

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Controller\Admin;

use drsoftfrvalidatecustomerpro;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ValidateCustomerProController.
 *
 * @ModuleActivated(moduleName="drsoftfrvalidatecustomerpro", redirectRoute="admin_module_manage")
 */
final class ValidateCustomerProController extends FrameworkBundleAdminController
{
    const TAB_CLASS_NAME = 'AdminDrSoftFrValidateCustomerPro';

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

        return $this->render('@Modules/drsoftfrvalidatecustomerpro/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'form' => $form->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'module' => $this->getModule(),
        ]);
    }

    /**
     * @return drsoftfrvalidatecustomerpro|object
     */
    protected function getModule()
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.module');
    }

    /**
     * Get ValidateCustomerPro form handler.
     *
     * @return FormHandlerInterface
     */
    protected function getValidateCustomerProFormHandler(): FormHandlerInterface
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.form.handler.validate_customer_pro_form_handler');
    }
}
