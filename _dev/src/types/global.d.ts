export declare type PropsType = Record<string, unknown>

export {}

interface JQueryStatic {
  tableDnD: TableDnD;
  tokenfield: any;
  clickableDropdown: () => void;
  datetimepicker: any;
  select2: any;
  sortable: any;
  fancybox: any;
  growl: any;
  pstooltip: any;
  serializeJSON: any;
}

interface TableDnD {
  serialize(): string;
  jsonize(): string;
}

interface PrestashopWindow {
  customRoutes: unknown;
  component: any;
  instance: any;
}

declare global {
  interface Window {
    props: PropsType
    $: JQueryStatic;
    prestashop: PrestashopWindow;
  }
}
