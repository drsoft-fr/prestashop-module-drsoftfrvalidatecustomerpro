interface ConstructorPropsInterface {
  jsAjaxUrl?: JsAjaxUrlType
  jsObject?: JsObjectInterface
  jsText?: JsTextType
  [propName: string]: unknown
}

type JsAjaxUrlType = Record<string, string>

interface JsObjectInterface {
  page?: PageInterface
  [propName: string]: object
}

type JsTextType = Record<string, string>

interface JsonResponseInterface {
  success: boolean
  message: string
}

interface PageInterface {
  name: string
  type: string
}

export {
  ConstructorPropsInterface,
  JsAjaxUrlType,
  JsObjectInterface,
  JsTextType,
  JsonResponseInterface,
  PageInterface,
}
