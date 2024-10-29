<?php
/**
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Dylan Ramos <dylan@drsoft_fr.fr>
 * @copyright  2024 drsoft_fr.fr
 * @license    README.md
 * @site    drsoft_fr.fr
 */

declare(strict_types=1);

namespace DrSoftFr\Module\ValidateCustomerPro\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use DrSoftFr\Module\ValidateCustomerPro\Entity\AdapterCustomer;
use DrSoftFr\Module\ValidateCustomerPro\Exception\AdapterCustomer\AdapterCustomerNotFoundException;
use DrSoftFr\Module\ValidateCustomerPro\Repository\AdapterCustomerRepository;
use DrSoftFr\Module\ValidateCustomerPro\Search\Filters\AdapterCustomerFilters;
use DrSoftFr\PrestaShopModuleHelper\Domain\Asset\Package;
use DrSoftFr\PrestaShopModuleHelper\Domain\Asset\VersionStrategy\JsonManifestVersionStrategy;
use DrSoftFr\PrestaShopModuleHelper\Traits\JsPropsTrait;
use drsoftfrvalidatecustomerpro;
use Media;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class AdapterCustomerController.
 *
 * @ModuleActivated(moduleName="drsoftfrvalidatecustomerpro", redirectRoute="admin_module_manage")
 */
final class AdapterCustomerController extends FrameworkBundleAdminController
{
    use JsPropsTrait;

    const PAGE_INDEX_ROUTE = 'admin_drsoft_fr_validate_customer_pro_adapter_customer_index';
    const TAB_CLASS_NAME = 'AdminDrSoftFrValidateCustomerProAdapterCustomer';
    const TEMPLATE_FOLDER = '@Modules/drsoftfrvalidatecustomerpro/views/templates/admin/adapter_customer/';

    /**
     * @var Package
     */
    private $manifest;

    public function __construct()
    {
        parent::__construct();

        $this->manifest = new Package(
            new JsonManifestVersionStrategy(
                _PS_MODULE_DIR_ . '/drsoftfrvalidatecustomerpro/views/.vite/manifest.json'
            )
        );
    }

    /**
     * Enable bulk AdapterCustomer
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_adapter_customer_index",
     *     message="You do not have permission to enable this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkEnableAction(Request $request): Response
    {
        $objs = $this->getBulkObjs($request);

        if (empty($objs)) {
            $this->cannotFindCustomerRedirect();
        }

        $em = $this->getEntityManager();

        foreach ($objs as $obj) {
            $obj->setActive(true);
        }

        $em->flush();
        $this->addFlash(
            'success',
            $this->trans(
                'The selection has been successfully enabled.',
                'Modules.Drsoftfrvalidatecustomerpro.Success'
            )
        );

        return $this->redirectToRoute(self::PAGE_INDEX_ROUTE);
    }

    /**
     * Delete bulk AdapterCustomer
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_adapter_customer_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $objs = $this->getBulkObjs($request);

        if (empty($objs)) {
            return $this->cannotFindCustomerRedirect();
        }

        $em = $this->getEntityManager();

        foreach ($objs as $obj) {
            $em->remove($obj);
        }

        $em->flush();
        $this->addFlash(
            'success',
            $this->trans(
                'The selection has been successfully deleted.',
                'Admin.Notifications.Success'
            )
        );

        return $this->redirectToRoute(self::PAGE_INDEX_ROUTE);
    }

    /**
     * Disable bulk AdapterCustomer
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_adapter_customer_index",
     *     message="You do not have permission to disable this."
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkDisableAction(Request $request): Response
    {
        $objs = $this->getBulkObjs($request);

        if (empty($objs)) {
            return $this->cannotFindCustomerRedirect();
        }

        $em = $this->getEntityManager();

        foreach ($objs as $obj) {
            $obj->setActive(false);
        }

        $em->flush();
        $this->addFlash(
            'success',
            $this->trans(
                'The selection has been successfully disabled.',
                'Modules.Drsoftfrvalidatecustomerpro.Success'
            )
        );

        return $this->redirectToRoute(self::PAGE_INDEX_ROUTE);
    }

    /**
     * Display error message when customer cannot be found and redirect to index page.
     *
     * @return Response
     */
    private function cannotFindCustomerRedirect(): Response
    {
        $this->addFlash(
            'error',
            $this->trans(
                'Cannot find customer',
                'Modules.Drsoftfrvalidatecustomerpro.Error'
            )
        );

        return $this->redirectToRoute(self::PAGE_INDEX_ROUTE);
    }

    /**
     * Create AdapterCustomer
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_adapter_customer_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $form = $this
            ->getFormBuilder()
            ->getForm();
        $form->handleRequest($request);

        try {
            $result = $this
                ->getFormHandler()
                ->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash(
                    'success',
                    $this->trans(
                        'Successful creation.',
                        'Admin.Notifications.Success'
                    )
                );

                return $this->redirectToRoute(self::PAGE_INDEX_ROUTE);
            }
        } catch (Throwable $t) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot save the data. Throwable: #%code% - %message%',
                    'Modules.Drsoftfrvalidatecustomerpro.Error',
                    [
                        '%code%' => $t->getCode(),
                        '%message%' => $t->getMessage(),
                    ]
                )
            );
        }

        return $this->render(self::TEMPLATE_FOLDER . 'create.html.twig', [
            'drsoft_fr_validate_customer_pro_adapter_customer_form' => $form->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'module' => $this->getModule(),
        ]);
    }

    /**
     * Delete AdapterCustomer
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_adapter_customer_index",
     *     message="You do not have permission to delete this."
     * )
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function deleteAction(int $id): Response
    {
        try {
            $repository = $this->getRepository();

            /** @var AdapterCustomer $obj */
            $obj = $repository->find($id);

            if (null === $obj) {
                throw new AdapterCustomerNotFoundException(
                    sprintf(
                        'AdapterCustomer with id "%d" was not found',
                        $id
                    )
                );
            }

            $em = $this->getEntityManager();

            $em->remove($obj);
            $em->flush();
            $this->addFlash(
                'success',
                $this->trans(
                    'Successful deletion.',
                    'Admin.Notifications.Success'
                )
            );
        } catch (Throwable $t) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot delete the data. Throwable: #%code% - %message%',
                    'Modules.Drsoftfrvalidatecustomerpro.Error',
                    [
                        '%code%' => $t->getCode(),
                        '%message%' => $t->getMessage(),
                    ]
                )
            );
        }

        return $this->redirectToRoute(self::PAGE_INDEX_ROUTE);
    }

    /**
     * Edit AdapterCustomer
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_adapter_customer_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function editAction(Request $request, int $id): Response
    {
        $form = $this
            ->getFormBuilder()
            ->getFormFor($id);
        $form->handleRequest($request);

        try {
            $result = $this
                ->getFormHandler()
                ->handleFor($id, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash(
                    'success',
                    $this->trans(
                        'Successful update.',
                        'Admin.Notifications.Success'
                    )
                );

                return $this->redirectToRoute(self::PAGE_INDEX_ROUTE);
            }
        } catch (Throwable $t) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Cannot save the data. Throwable: #%code% - %message%',
                    'Modules.Drsoftfrvalidatecustomerpro.Error',
                    [
                        '%code%' => $t->getCode(),
                        '%message%' => $t->getMessage(),
                    ]
                )
            );
        }

        return $this->render(self::TEMPLATE_FOLDER . 'edit.html.twig', [
            'drsoft_fr_validate_customer_pro_adapter_customer_form' => $form->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'module' => $this->getModule(),
        ]);
    }

    /**
     * Export filtered AdapterCustomer.
     *
     * @AdminSecurity(
     *     "is_granted(['read', 'update', 'create', 'delete'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_adapter_customer_index",
     *     message="You do not have permission to view this."
     * )
     *
     * @param AdapterCustomerFilters $filters
     *
     * @return CsvResponse
     */
    public function exportAction(AdapterCustomerFilters $filters): CsvResponse
    {
        $filters = new AdapterCustomerFilters(['limit' => null] + $filters->all());

        /** @var GridInterface $grid */
        $grid = $this
            ->getGridFactory()
            ->getGrid($filters);

        $headers = [
            'id' => $this->trans('ID', 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
            'id_customer' => $this->trans('Customer ID', 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
            'active' => $this->trans('Is Enabled', 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
            'date_add' => $this->trans('Date add', 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
            'date_upd' => $this->trans('Date upd', 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
        ];

        $data = [];

        foreach ($grid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id' => $record['id'],
                'id_customer' => $record['id_customer'],
                'active' => $record['active'],
                'date_add' => $record['date_add'],
                'date_upd' => $record['date_upd'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('adapter_customer_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * Get an array of AdapterCustomer objects based on request IDs.
     *
     * @param Request $request The request object containing the IDs.
     *
     * @return array|null An array of AdapterCustomer objects if found, or null if no objects were found.
     */
    private function getBulkObjs(Request $request): ?array
    {
        $ids = $request->request->get('drsoft_fr_validate_customer_pro_adapter_customer_bulk');
        $repository = $this->getRepository();

        try {
            /** @var AdapterCustomer[] $objs */
            return $repository->findById($ids);
        } catch (EntityNotFoundException $e) {
            return null;
        }
    }

    /**
     * @AdminSecurity(
     *     "is_granted(['read'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_index",
     *     message="Access denied."
     * )
     *
     * @param Request $request
     * @param AdapterCustomerFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, AdapterCustomerFilters $filters): Response
    {
        $this->createJsProps();
        $this->addJsObject(['name' => 'adapterCustomer', 'type' => 'list'], 'page');
        Media::addJsDef($this->getJsProps());

        /** @var GridInterface $grid */
        $grid = $this
            ->getGridFactory()
            ->getGrid($filters);

        return $this->render(self::TEMPLATE_FOLDER . 'index.html.twig', [
            'drsoft_fr_validate_customer_pro_adapter_customer_grid' => $this->presentGrid($grid),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'manifest' => $this->manifest,
            'module' => $this->getModule(),
        ]);
    }

    /**
     * Toggle AdapterCustomer active.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_drsoft_fr_validate_customer_pro_adapter_customer_index",
     *     message="You do not have permission to update this."
     * )
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function toggleActiveAction(int $id): JsonResponse
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json([
                'status' => false,
                'message' => $this->getDemoModeErrorMessage(),
            ]);
        }

        try {
            /** @var AdapterCustomer $obj */
            $obj = $this
                ->getRepository()
                ->find($id);

            if (null === $obj) {
                throw new AdapterCustomerNotFoundException(
                    sprintf(
                        'AdapterCustomer with id "%d" was not found',
                        $id
                    )
                );
            }

            $em = $this->getEntityManager();

            $obj->setActive(!$obj->isActive());
            $em->flush();

            $response = [
                'status' => true,
                'message' => $this->trans(
                    'The status has been successfully updated.',
                    'Admin.Notifications.Success'
                ),
            ];
        } catch (Throwable $t) {
            $response = [
                'status' => false,
                'message' =>
                    $this->trans(
                        'Cannot save the data. Throwable: #%code% - %message%',
                        'Modules.Drsoftfrvalidatecustomerpro.Error',
                        [
                            '%code%' => $t->getCode(),
                            '%message%' => $t->getMessage(),
                        ]
                    ),
            ];
        }

        return $this->json($response);
    }

    /**
     * Get AdapterCustomer repository.
     *
     * @return AdapterCustomerRepository
     */
    private function getRepository(): AdapterCustomerRepository
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.repository.adapter_customer_repository');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager(): EntityManagerInterface
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * Get AdapterCustomer form builder.
     *
     * @return FormBuilder
     */
    protected function getFormBuilder(): FormBuilder
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.form.identifiable_object.builder.adapter_customer_form_builder');
    }

    /**
     * Get AdapterCustomer form handler.
     *
     * @return FormHandler
     */
    protected function getFormHandler(): FormHandler
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.form.identifiable_object.handler.adapter_customer_form_handler');
    }

    /**
     * @return GridFactory
     */
    protected function getGridFactory(): GridFactory
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.grid.factory.adapter_customers');
    }

    /**
     * @return drsoftfrvalidatecustomerpro|object
     */
    protected function getModule(): drsoftfrvalidatecustomerpro
    {
        return $this->get('drsoft_fr.module.validate_customer_pro.module');
    }

    /**
     * @return array[]
     */
    private function getToolbarButtons(): array
    {
        return [
            'add' => [
                'desc' => $this->trans('Add new AdapterCustomer', 'Modules.Drsoftfrvalidatecustomerpro.Admin'),
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('admin_drsoft_fr_validate_customer_pro_adapter_customer_create'),
            ],
        ];
    }
}
