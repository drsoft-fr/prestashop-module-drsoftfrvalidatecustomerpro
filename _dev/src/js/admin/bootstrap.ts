import type { ConstructorPropsInterface, PageInterface } from '@src/types/app'

void (async (props: ConstructorPropsInterface): Promise<void> => {
  /**
   * Prepares the page object based on the provided constructor props.
   *
   * @param {ConstructorPropsInterface} props - The constructor props object.
   *
   * @returns {PageInterface} - The prepared page object.
   */
  const preparePage = (props: ConstructorPropsInterface): PageInterface => {
    const page: PageInterface = { name: '', type: '' }

    props.jsObject = props.jsObject ?? { page }
    props.jsObject.page = props.jsObject.page ?? page

    page.name = props.jsObject.page.name ?? page.name
    page.type = props.jsObject.page.type ?? page.type

    delete props.jsObject.page

    return page
  }

  /**
   * Initialize the component with the given properties.
   *
   * @async
   * @param {ConstructorPropsInterface} props - The properties needed to set up the component.
   *
   * @returns {Promise<void>} - A promise that resolves when the initialization is complete.
   */
  const init = async (props: ConstructorPropsInterface): Promise<void> => {
    const page = preparePage(props)

    try {
      let m

      switch (page.name) {
        case 'adapterCustomer':
          switch (page.type) {
            case 'list':
              m = await import('@src/js/admin/adapter/customer/list')

              break
            default:
              return
          }

          break
        default:
          return
      }

      const c = new m.default()

      await c.run()
    } catch (e) {
      console.error(e)
    }
  }

  await init(props)
})(window.props || {})
