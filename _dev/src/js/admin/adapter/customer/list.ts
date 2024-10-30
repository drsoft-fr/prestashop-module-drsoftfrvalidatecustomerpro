/**
 * Represents a CustomerController class.
 */
export default class CustomerController {
  /**
   * This method initializes and configures a grid component with various extensions.
   *
   * @return {Promise<void>} Resolves when all extensions are added to the grid.
   */
  async run() {
    const [
      Grid,
      ReloadListActionExtension,
      ExportToSqlManagerExtension,
      FiltersResetExtension,
      SortingExtension,
      LinkRowActionExtension,
      SubmitGridExtension,
      SubmitBulkExtension,
      BulkActionCheckboxExtension,
      SubmitRowActionExtension,
      AsyncToggleColumnExtension,
    ] = await Promise.all([
      import('@components/grid/grid'),
      import('@components/grid/extension/reload-list-extension'),
      import('@components/grid/extension/export-to-sql-manager-extension'),
      import('@components/grid/extension/filters-reset-extension'),
      import('@components/grid/extension/sorting-extension'),
      import('@components/grid/extension/link-row-action-extension'),
      import('@components/grid/extension/submit-grid-action-extension'),
      import('@components/grid/extension/submit-bulk-action-extension'),
      import('@components/grid/extension/bulk-action-checkbox-extension'),
      import(
        '@components/grid/extension/action/row/submit-row-action-extension'
      ),
      import(
        '@components/grid/extension/column/common/async-toggle-column-extension'
      ),
    ])

    const grid = new Grid.default(
      'drsoft_fr_validate_customer_pro_adapter_customer',
    )

    grid.addExtension(new ReloadListActionExtension.default())
    grid.addExtension(new ExportToSqlManagerExtension.default())
    grid.addExtension(new FiltersResetExtension.default())
    grid.addExtension(new SortingExtension.default())
    grid.addExtension(new LinkRowActionExtension.default())
    grid.addExtension(new SubmitGridExtension.default())
    grid.addExtension(new SubmitBulkExtension.default())
    grid.addExtension(new BulkActionCheckboxExtension.default())
    grid.addExtension(new SubmitRowActionExtension.default())
    grid.addExtension(new AsyncToggleColumnExtension.default())
  }
}
