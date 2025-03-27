declare module 'laravel-permission-to-vuejs' {
  export interface JsPermissions {
    roles: string[];
    permissions: string[];
  }

  declare global {
    interface Window {
      Laravel: {
        jsPermissions: JsPermissions | 0;
      };
    }
  }

  export declare class LaravelPermissionToVue {
    is(value: string): boolean;
    can(value: string): boolean;
    reloadRolesAndPermissions(route?: string): Promise<void>;
  }

  export declare const is: (value: string) => boolean;
  export declare const can: (value: string) => boolean;
  export declare const reloadRolesAndPermissions: (route?: string) => Promise<void>;

  export declare const _default: {
    install: (app: any, options?: any) => void;
  };

  export default _default;
}
